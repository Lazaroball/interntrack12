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

    protected function student()
    {
        return Auth::user()->student;
    }

    /**
     * The student's current open deployment, Field Study OR Internship.
     * Cancelled and completed deployments are ignored.
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
        if (! in_array($program, ['Field Study', 'Internship'], true)) {
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
     * Display the Teaching Hours page.
     */
    public function index()
    {
        $student = $this->student();

        abort_unless($student, 403, 'No student profile is linked to this account.');

        $deployment = $this->currentDeployment($student);

        // Logging is only allowed with an approved deployment.
        $canLogHours = (bool) ($deployment && $deployment->is_approved);

        $todayLogs       = collect();
        $activeTodayLog  = null;
        $todayTotalHours = 0;
        $totalHours      = 0;
        $history         = collect();

        if ($deployment) {
            $todayLogs = DailyLog::where('student_id', $student->id)
                ->where('deployment_id', $deployment->id)
                ->whereDate('date', Carbon::today())
                ->orderBy('time_in')
                ->get();

            $activeTodayLog = $todayLogs->firstWhere('status', 'in_progress');

            $todayTotalHours = round(
                (float) $todayLogs->where('status', 'completed')->sum('hours_rendered'),
                2
            );

            // Only completed logs count toward the official total.
            $totalHours = DailyLog::where('student_id', $student->id)
                ->where('deployment_id', $deployment->id)
                ->where('status', 'completed')
                ->sum('hours_rendered');

            $history = DailyLog::where('student_id', $student->id)
                ->where('deployment_id', $deployment->id)
                ->orderByDesc('date')
                ->orderByDesc('time_in')
                ->paginate(15);
        }

        $totalHours = round((float) $totalHours, 2);

        $progressPercent = min(
            100,
            round(($totalHours / self::REQUIRED_HOURS) * 100, 1)
        );

        return view('student.teaching-hours.index', [
            'deployment'      => $deployment,
            'program'         => $deployment->program ?? null,
            'canLogHours'     => $canLogHours,
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