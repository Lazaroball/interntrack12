<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\Deployment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    /**
     * Required hours (Field Study and Internship share the same target for now).
     */
    const REQUIRED_HOURS = 600;

    const STAGES = ['Field Study', 'Internship'];

    protected function student()
    {
        return Auth::user()->student;
    }

    /**
     * The student's current open deployment, Field Study OR Internship.
     * Cancelled and completed deployments are ignored. An approved
     * deployment is preferred over a pending one.
     */
    protected function currentDeployment($student)
    {
        if (! $student) {
            return null;
        }

        return Deployment::with(['partnerSchool', 'supervisor'])
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->orderByRaw('supervisor_id is null')
            ->latest('id')
            ->first();
    }

    /**
     * Keep students.field_study_hours / internship_hours equal to the total
     * of COMPLETED logs for that program. Floored, so a student can't reach
     * 600 on a rounded-up 599.6.
     */
    protected function syncStudentHours($student, ?string $program): void
    {
        if (! in_array($program, self::STAGES, true)) {
            return;
        }

        $deploymentIds = Deployment::where('student_id', $student->id)
            ->where('program', $program)
            ->pluck('id');

        $total = DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $deploymentIds)
            ->where('status', 'completed')
            ->sum('hours_rendered');

        $column = $program === 'Internship' ? 'internship_hours' : 'field_study_hours';

        $student->forceFill([$column => (int) floor((float) $total)])->save();
    }

    /**
     * Display the Teaching Hours page for one stage.
     *
     * GET /student/teaching-hours?stage=Field Study|Internship
     *
     * With no ?stage= the page opens on the stage of the student's current
     * open deployment. The Internship tab stays closed until the coordinator
     * clears Field Study.
     */
    public function index(Request $request)
    {
        $student = $this->student();

        abort_unless($student, 403, 'No student profile is linked to this account.');

        $openDeployment = $this->currentDeployment($student);

        $stage = $request->query('stage');

        if (! in_array($stage, self::STAGES, true)) {
            $stage = $openDeployment->program
                ?? ($student->is_internship_unlocked ? 'Internship' : 'Field Study');
        }

        if ($stage === 'Internship' && ! $student->is_internship_unlocked) {
            $stage = 'Field Study';
        }

        // Every deployment of this stage, so the totals match students.*_hours
        $stageDeployments = Deployment::with(['partnerSchool', 'supervisor'])
            ->where('student_id', $student->id)
            ->where('program', $stage)
            ->latest('id')
            ->get();

        $deploymentIds = $stageDeployments->pluck('id');

        // The deployment shown on the page: latest one that was not cancelled
        $deployment = $stageDeployments->first(fn ($d) => $d->status !== 'cancelled');

        $stageCompleted = (bool) optional($deployment)->completed_at;

        // The clock only works for the stage that has the open, approved deployment
        $canLogHours = (bool) (
            $openDeployment
            && $openDeployment->is_approved
            && $openDeployment->program === $stage
        );

        $todayLogs = DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $deploymentIds)
            ->whereDate('date', Carbon::today())
            ->orderBy('time_in')
            ->get();

        $activeTodayLog = $todayLogs->firstWhere('status', 'in_progress');

        $todayTotalHours = round(
            (float) $todayLogs->where('status', 'completed')->sum('hours_rendered'),
            2
        );

        // Only completed logs count toward the official total.
        $totalHours = round((float) DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $deploymentIds)
            ->where('status', 'completed')
            ->sum('hours_rendered'), 2);

        $history = DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $deploymentIds)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->paginate(15)
            ->withQueryString();

        $progressPercent = min(
            100,
            round(($totalHours / self::REQUIRED_HOURS) * 100, 1)
        );

        return view('student.teaching-hours.index', [
            'student'         => $student,
            'stage'           => $stage,
            'deployment'      => $deployment,
            'program'         => $stage,
            'canLogHours'     => $canLogHours,
            'stageCompleted'  => $stageCompleted,
            'todayLogs'       => $todayLogs,
            'activeTodayLog'  => $activeTodayLog,
            'todayTotalHours' => $todayTotalHours,
            'totalHours'      => $totalHours,
            'requiredHours'   => self::REQUIRED_HOURS,
            'progressPercent' => $progressPercent,
            'history'         => $history,
        ]);
    }

    /**
     * Record Time In for a new session.
     */
    public function timeIn(Request $request)
    {
        $student = $this->student();

        abort_unless($student, 403);

        $deployment = $this->currentDeployment($student);

        abort_unless(
            $deployment && $deployment->is_approved,
            403,
            'You need an approved deployment before you can log hours.'
        );

        // Only block if there is currently an OPEN session today.
        $activeLog = DailyLog::where('student_id', $student->id)
            ->where('deployment_id', $deployment->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'in_progress')
            ->first();

        if ($activeLog) {
            return back()->with('error', 'You are already timed in.');
        }

        DailyLog::create([
            'student_id'    => $student->id,
            'deployment_id' => $deployment->id,
            'date'          => Carbon::today()->toDateString(),
            'time_in'       => Carbon::now()->format('H:i:s'),
            'gps_location'  => null,
            'status'        => 'in_progress',
        ]);

        return back()->with('success', 'Time in recorded.');
    }

    /**
     * Record Time Out for the open session and calculate hours rendered.
     */
    public function timeOut(Request $request)
    {
        $student = $this->student();

        abort_unless($student, 403);

        $deployment = $this->currentDeployment($student);

        $log = DailyLog::where('student_id', $student->id)
            ->when($deployment, fn ($query) => $query->where('deployment_id', $deployment->id))
            ->whereDate('date', Carbon::today())
            ->where('status', 'in_progress')
            ->first();

        if (! $log) {
            return back()->with('error', 'No open time-in found for today.');
        }

        // MySQL TIME has no date, so combine the stored date and time_in.
        $timeIn  = Carbon::parse($log->date->toDateString() . ' ' . $log->time_in);
        $timeOut = Carbon::now();

        if ($timeOut->lessThan($timeIn)) {
            return back()->with(
                'error',
                'Invalid time-out. The time-out cannot be earlier than the time-in.'
            );
        }

        // Calculated on the server. The student never submits this value.
        $hours = round($timeIn->diffInMinutes($timeOut) / 60, 2);

        $log->update([
            'time_out'       => $timeOut->format('H:i:s'),
            'hours_rendered' => $hours,
            'status'         => 'completed',
        ]);

        $program = optional(Deployment::find($log->deployment_id))->program;
        $this->syncStudentHours($student, $program);

        return back()->with('success', "Time out recorded: {$hours} hours logged.");
    }
}