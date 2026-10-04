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
    protected int $fieldStudyRequiredHours = 600;

    /**
     * GET /student/field-study
     */
    public function index(): View
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        // ── Hour progress ──
        $completedHours  = $student->field_study_hours ?? 0;
        $requiredHours   = $this->fieldStudyRequiredHours;
        $remainingHours  = max(0, $requiredHours - $completedHours);
        $progressPercent = $requiredHours > 0
            ? min(100, (int) round(($completedHours / $requiredHours) * 100))
            : 0;

        // ── Latest Field Study deployment (open or completed, not cancelled) ──
        $currentDeployment = $student->deployments()
            ->with(['partnerSchool', 'supervisor'])
            ->where('program', 'Field Study')
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->first();

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

        // ── Field Study requirements only ──
        // Legacy rows (no definition) are treated as Field Study.
        $requirements = Requirement::where('student_id', $student->id)
            ->where(function ($q) {
                $q->whereNull('requirement_definition_id')
                  ->orWhereHas('requirementDefinition', fn ($d) => $d->where('stage', 'Field Study'));
            })
            ->orderByDesc('submitted_at')
            ->get();

        $requirementsSummary = [
            'total'     => $requirements->count(),
            'submitted' => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'submitted')->count(),
            'pending'   => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'pending')->count(),
            'approved'  => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'approved')->count(),
            'rejected'  => $requirements->filter(fn ($r) => strtolower((string) $r->status) === 'rejected')->count(),
        ];

        $recentRequirements = $requirements->take(5);

        // ── Daily logs for Field Study deployments only ──
        $fieldStudyDeploymentIds = $student->deployments()
            ->where('program', 'Field Study')
            ->pluck('id');

        $dailyLogs = DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $fieldStudyDeploymentIds)
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

            'dailyLogSummary' => $dailyLogSummary,
            'recentDailyLogs' => $recentDailyLogs,
        ]);
    }
}