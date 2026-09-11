<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\Requirement;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class FieldStudyController extends Controller
{
    /**
     * Field Study requirement, in hours.
     * Kept consistent with StudentDashboardController's value, which is
     * the same authoritative source (students.field_study_hours).
     */
    protected int $fieldStudyRequiredHours = 600;

    /**
     * Display the authenticated student's Field Study overview.
     *
     * GET /student/field-study
     * Route name: student.field-study
     */
    public function index(): View
    {
        $student = Student::with([
            'currentDeployment.partnerSchool',
            'currentDeployment.supervisor',
        ])->where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        // ── Field Study hour progress ──
        // students.field_study_hours is treated as the official accumulated
        // value (same source already used by StudentDashboardController),
        // so this page does not introduce a second/competing calculation.
        $completedHours = $student->field_study_hours ?? 0;
        $requiredHours  = $this->fieldStudyRequiredHours;
        $remainingHours = max(0, $requiredHours - $completedHours);
        $progressPercent = $requiredHours > 0
            ? min(100, (int) round(($completedHours / $requiredHours) * 100))
            : 0;

        // ── Deployment / Supervisor ──
        $currentDeployment = $student->currentDeployment;

        $deploymentInfo = $currentDeployment ? [
            'partner_school'  => $currentDeployment->partnerSchool->school_name ?? null,
            'program'         => $currentDeployment->program,
            'school_year'     => $currentDeployment->school_year,
            'semester'        => $currentDeployment->semester,
            'deployment_date' => $currentDeployment->deployment_date,
            'status'          => $currentDeployment->status,
            'remarks'         => $currentDeployment->remarks,
            'supervisor_name' => $currentDeployment->supervisor->full_name ?? null,
        ] : null;

        // ── Requirements summary (queried directly by student_id — no
        //    Requirement relationship assumed on the Student model) ──
        $requirements = Requirement::where('student_id', $student->id)
            ->orderByDesc('submitted_at')
            ->get();

        $requirementsSummary = [
            'total'     => $requirements->count(),
            'submitted' => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'submitted')->count(),
            'pending'   => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'pending')->count(),
            'rejected'  => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'rejected')->count(),
        ];

        $recentRequirements = $requirements->take(5);

        // ── Daily log / teaching-hour summary (queried directly by
        //    student_id — no DailyLog relationship assumed on Student) ──
        $dailyLogs = DailyLog::where('student_id', $student->id)
            ->orderByDesc('date')
            ->get();

        $dailyLogSummary = [
            'total_logged_hours' => $dailyLogs->sum('hours_rendered'),
            'total_logs'         => $dailyLogs->count(),
            'latest_log_date'    => $dailyLogs->first()->date ?? null,
        ];

        $recentDailyLogs = $dailyLogs->take(5);

        return view('student.field-study.index', [
            'student' => $student,

            'fieldStudyProgress' => [
                'completed_hours'  => $completedHours,
                'required_hours'   => $requiredHours,
                'remaining_hours'  => $remainingHours,
                'progress_percent' => $progressPercent,
            ],

            'deploymentInfo' => $deploymentInfo,

            'requirementsSummary' => $requirementsSummary,
            'recentRequirements'  => $recentRequirements,

            'dailyLogSummary'  => $dailyLogSummary,
            'recentDailyLogs'  => $recentDailyLogs,
        ]);
    }
}