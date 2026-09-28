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
     * Required Field Study hours.
     */
    const REQUIRED_HOURS = 600;

    /**
     * Resolve the authenticated student's profile.
     */
    protected function student()
    {
        return Auth::user()->student;
    }

    /**
     * Get the student's current Field Study deployment.
     *
     * Only an active Field Study deployment is considered.
     * The deployment must not already be completed.
     *
     * Unchanged from before, per your instructions.
     */
    protected function currentDeployment($student)
    {
        if (!$student) {
            return null;
        }

        return Deployment::with(['partnerSchool', 'supervisor'])
            ->where('student_id', $student->id)
            ->where('program', 'Field Study')
            ->whereNull('completed_at')
            ->latest('deployment_date')
            ->first();
    }

    /**
     * Display the Teaching Hours page.
     */
    public function index()
    {
        $student = $this->student();

        abort_unless(
            $student,
            403,
            'No student profile is linked to this account.'
        );

        $deployment = $this->currentDeployment($student);

        /*
         * Logging is only allowed when the student has
         * an approved Field Study deployment.
         */
        $canLogHours = (bool) (
            $deployment &&
            $deployment->is_approved
        );

        $todayLogs = collect();
        $activeTodayLog = null;
        $todayTotalHours = 0;
        $totalHours = 0;
        $history = collect();

        if ($deployment) {

            // Every log for today, oldest first — a student can have
            // several sessions (completed) plus at most one open
            // (in_progress) session in the same day.
            $todayLogs = DailyLog::where('student_id', $student->id)
                ->where('deployment_id', $deployment->id)
                ->whereDate('date', Carbon::today())
                ->orderBy('time_in')
                ->get();

            // The single open session, if any. There should never be
            // more than one at a time — timeIn() enforces that.
            $activeTodayLog = $todayLogs->firstWhere('status', 'in_progress');

            // Today's total is only from today's completed sessions.
            $todayTotalHours = round(
                (float) $todayLogs->where('status', 'completed')->sum('hours_rendered'),
                2
            );

            /*
             * Only completed logs count toward the
             * official 600-hour Field Study total — across all days.
             */
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
            round(
                ($totalHours / self::REQUIRED_HOURS) * 100,
                1
            )
        );

        return view('student.teaching-hours.index', [
            'deployment' => $deployment,
            'canLogHours' => $canLogHours,
            'todayLogs' => $todayLogs,
            'activeTodayLog' => $activeTodayLog,
            'todayTotalHours' => $todayTotalHours,
            'totalHours' => $totalHours,
            'requiredHours' => self::REQUIRED_HOURS,
            'progressPercent' => $progressPercent,
            'history' => $history,
        ]);
    }

    /**
     * Record Time In for a new session.
     *
     * Only blocked if the student already has an OPEN (in_progress)
     * session today. Prior completed sessions do not block a new one.
     */
    public function timeIn(Request $request)
    {
        $student = $this->student();

        abort_unless($student, 403);

        $deployment = $this->currentDeployment($student);

        /*
         * Student must have an approved Field Study deployment.
         */
        abort_unless(
            $deployment && $deployment->is_approved,
            403,
            'You need an approved Field Study deployment before you can log hours.'
        );

        /*
         * Only block Time In if there is currently an OPEN session
         * today. Completed sessions from earlier today are fine —
         * this is what allows multiple sessions per day.
         */
        $activeLog = DailyLog::where('student_id', $student->id)
            ->where('deployment_id', $deployment->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'in_progress')
            ->first();

        if ($activeLog) {
            return back()->with(
                'error',
                'You are already timed in.'
            );
        }

        // Always a new row — never updateOrCreate. Each session is
        // its own DailyLog record.
        DailyLog::create([
            'student_id' => $student->id,
            'deployment_id' => $deployment->id,
            'date' => Carbon::today()->toDateString(),
            'time_in' => Carbon::now()->format('H:i:s'),
            'gps_location' => null,
            'status' => 'in_progress',
        ]);

        return back()->with(
            'success',
            'Time in recorded.'
        );
    }

    /**
     * Record Time Out for the currently open session and
     * automatically calculate hours rendered.
     */
    public function timeOut(Request $request)
    {
        $student = $this->student();

        abort_unless($student, 403);

        $deployment = $this->currentDeployment($student);

        /*
         * Find the one open session for today belonging to the
         * authenticated student's current deployment. This updates
         * that specific row only — no other DailyLog is touched.
         */
        $log = DailyLog::where('student_id', $student->id)
            ->when($deployment, fn ($query) => $query->where('deployment_id', $deployment->id))
            ->whereDate('date', Carbon::today())
            ->where('status', 'in_progress')
            ->first();

        if (!$log) {
            return back()->with(
                'error',
                'No open time-in found for today.'
            );
        }

        /*
         * Combine the stored date and time_in because
         * MySQL TIME contains no date information.
         */
        $timeIn = Carbon::parse(
            $log->date->toDateString() . ' ' . $log->time_in
        );

        $timeOut = Carbon::now();

        /*
         * Prevent invalid negative durations.
         */
        if ($timeOut->lessThan($timeIn)) {
            return back()->with(
                'error',
                'Invalid time-out. The time-out cannot be earlier than the time-in.'
            );
        }

        /*
         * Calculate rendered hours on the server.
         * The student never submits this value.
         */
        $minutes = $timeIn->diffInMinutes($timeOut);

        $hours = round($minutes / 60, 2);

        $log->update([
            'time_out' => $timeOut->format('H:i:s'),
            'hours_rendered' => $hours,
            'status' => 'completed',
        ]);

        return back()->with(
            'success',
            "Time out recorded — {$hours} hours logged."
        );
    }
}