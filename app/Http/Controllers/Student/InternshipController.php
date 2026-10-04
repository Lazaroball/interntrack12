<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\Deployment;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class InternshipController extends Controller
{
    protected int $internshipRequiredHours = 600;

    /**
     * GET /student/internship
     */
    public function index()
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        // Internship stays closed until the coordinator clears Field Study.
        if (! $student->is_internship_unlocked) {
            return redirect()
                ->route('student.field-study')
                ->with('error', 'Internship unlocks after the coordinator clears your Field Study.');
        }

        // ── Hour progress ──
        $completedHours  = $student->internship_hours ?? 0;
        $requiredHours   = $this->internshipRequiredHours;
        $remainingHours  = max(0, $requiredHours - $completedHours);
        $progressPercent = $requiredHours > 0
            ? min(100, (int) round(($completedHours / $requiredHours) * 100))
            : 0;

        // ── Latest Internship deployment (open or completed, not cancelled) ──
        $deployment = $student->deployments()
            ->with(['partnerSchool', 'supervisor'])
            ->where('program', 'Internship')
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->first();

        $deploymentInfo = $deployment ? [
            'partner_school'  => $deployment->partnerSchool->school_name ?? null,
            'school_year'     => $deployment->school_year,
            'semester'        => $deployment->semester,
            'deployment_date' => $deployment->deployment_date,
            'supervisor_name' => $deployment->supervisor->full_name ?? null,
            'remarks'         => $deployment->remarks,
            'status_label'    => $deployment->completed_at
                                    ? 'Completed'
                                    : ($deployment->supervisor_id ? 'Deployed' : 'Pending approval'),
            'status_key'      => $deployment->completed_at
                                    ? 'completed'
                                    : ($deployment->supervisor_id ? 'deployed' : 'pending'),
        ] : null;

        // ── Internship requirements only ──
        $requirements = Requirement::with('requirementDefinition')
            ->where('student_id', $student->id)
            ->whereHas('requirementDefinition', fn ($d) => $d->where('stage', 'Internship'))
            ->orderByDesc('submitted_at')
            ->get();

        $requiredIds = RequirementDefinition::active()
            ->forStage('Internship')
            ->where('is_required', true)
            ->pluck('id');

        $requirementsSummary = [
            'total'             => $requirements->count(),
            'approved'          => $requirements->where('status', Requirement::STATUS_APPROVED)->count(),
            'pending'           => $requirements->whereIn('status', Requirement::NEEDS_REVIEW)->count(),
            'rejected'          => $requirements->where('status', Requirement::STATUS_REJECTED)->count(),
            'required_total'    => $requiredIds->count(),
            'required_approved' => $requirements
                ->where('status', Requirement::STATUS_APPROVED)
                ->whereIn('requirement_definition_id', $requiredIds)
                ->unique('requirement_definition_id')
                ->count(),
        ];

        $recentRequirements = $requirements->take(5);

        // ── Daily logs for Internship deployments only ──
        $internshipDeploymentIds = $student->deployments()
            ->where('program', 'Internship')
            ->pluck('id');

        $dailyLogs = DailyLog::where('student_id', $student->id)
            ->whereIn('deployment_id', $internshipDeploymentIds)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();

        $dailyLogSummary = [
            'total_logged_hours' => $dailyLogs->where('status', 'completed')->sum('hours_rendered'),
            'total_logs'         => $dailyLogs->count(),
            'latest_log_date'    => $dailyLogs->first()->date ?? null,
        ];

        $recentDailyLogs = $dailyLogs->take(5);

        // ── Final decision (both coordinator AND supervisor must pass) ──
        $finalDecision = [
            'coordinator' => $student->internship_coordinator_passed_at,
            'supervisor'  => $student->internship_supervisor_passed_at,
            'completed'   => $student->internship_completed_at,
        ];

        return view('student.internship.index', [
            'student' => $student,

            'internshipProgress' => [
                'completed_hours'  => $completedHours,
                'required_hours'   => $requiredHours,
                'remaining_hours'  => $remainingHours,
                'progress_percent' => $progressPercent,
            ],

            'deploymentInfo'      => $deploymentInfo,
            'requirementsSummary' => $requirementsSummary,
            'recentRequirements'  => $recentRequirements,
            'dailyLogSummary'     => $dailyLogSummary,
            'recentDailyLogs'     => $recentDailyLogs,
            'finalDecision'       => $finalDecision,
            'nextStep'            => $this->nextStep($student, $deployment),
        ]);
    }

    /**
     * What should the student do next? Drives the highlighted card on the page.
     *
     * @return array{tone:string,title:string,body:string,url:?string,label:?string}
     */
    private function nextStep(Student $student, ?Deployment $deployment): array
    {
        if ($student->internship_completed_at) {
            return [
                'tone'  => 'success',
                'title' => 'Internship completed',
                'body'  => 'Your coordinator and supervisor have both passed your Internship.',
                'url'   => null,
                'label' => null,
            ];
        }

        // 1. Initial requirements not accepted yet, and no school chosen
        if (! $deployment && ! $student->can_select_internship_school) {
            return [
                'tone'  => 'action',
                'title' => 'Submit your initial Internship requirements',
                'body'  => $student->internship_status === 'rejected'
                            ? 'Some documents need correction. Resubmit them and wait for your coordinator.'
                            : 'Submit every initial requirement, then wait for your coordinator to accept them. You can choose a school after that.',
                'url'   => route('student.internship.requirements'),
                'label' => 'Go to Internship Requirements',
            ];
        }

        // 2. Accepted, but no school chosen yet
        if (! $deployment) {
            return [
                'tone'  => 'action',
                'title' => 'Choose your Internship school',
                'body'  => 'Your initial requirements were accepted. Pick the school where you want to do your Internship.',
                'url'   => route('student.deployment.select'),
                'label' => 'Choose Internship School',
            ];
        }

        // 3. School chosen, waiting for the coordinator to approve the placement
        if (! $deployment->supervisor_id) {
            return [
                'tone'  => 'waiting',
                'title' => 'Waiting for your deployment approval',
                'body'  => 'Your coordinator will assign a supervisor and approve your placement. You can still change your school until then.',
                'url'   => route('student.deployment.select'),
                'label' => 'View School Selection',
            ];
        }

        // 4. Only one of the two decisions is in
        $coordinatorPassed = (bool) $student->internship_coordinator_passed_at;
        $supervisorPassed  = (bool) $student->internship_supervisor_passed_at;

        if ($coordinatorPassed xor $supervisorPassed) {
            return [
                'tone'  => 'waiting',
                'title' => 'Waiting for the final decision',
                'body'  => $coordinatorPassed
                            ? 'Your coordinator passed you. Your supervisor still has to decide.'
                            : 'Your supervisor passed you. Your coordinator still has to decide.',
                'url'   => null,
                'label' => null,
            ];
        }

        // 5. Deployed and working
        return [
            'tone'  => 'action',
            'title' => 'Log your hours and submit ongoing requirements',
            'body'  => 'You are deployed. Record your attendance every day and submit your ongoing requirements.',
            'url'   => route('student.teaching-hours', ['stage' => 'Internship']),
            'label' => 'Open Teaching Hours',
        ];
    }
}