<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    protected int $fieldStudyRequiredHours = 600;
    protected int $internshipRequiredHours = 600;

    /**
     * GET /student/dashboard
     */
    public function index(): View
    {
        $student = Student::with([
            'user',
            'preferredPartnerSchool',
            'currentDeployment.partnerSchool',
            'currentDeployment.supervisor',
        ])->where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        $fieldStudyProgress = $this->calculateProgressPercentage(
            $student->field_study_hours,
            $this->fieldStudyRequiredHours
        );

        $internshipProgress = $this->calculateProgressPercentage(
            $student->internship_hours,
            $this->internshipRequiredHours
        );

        $currentDeployment = $student->currentDeployment;

        $deploymentInfo = $currentDeployment ? [
            'id'              => $currentDeployment->id,
            'status'          => $currentDeployment->status,
            'program'         => $currentDeployment->program,
            'school_year'     => $currentDeployment->school_year,
            'semester'        => $currentDeployment->semester,
            'deployment_date' => $currentDeployment->deployment_date,
            'completed_at'    => $currentDeployment->completed_at,
            'remarks'         => $currentDeployment->remarks,
            'is_approved'     => $currentDeployment->is_approved,
            'partner_school'  => $currentDeployment->partnerSchool->school_name ?? null,
            'supervisor_name' => $currentDeployment->supervisor->full_name ?? null,
        ] : null;

        $preferredPartnerSchool = $student->preferredPartnerSchool;

        // Field Study eligibility follows the coordinator acceptance workflow.
        $isFieldStudyEligible = $student->field_study_status === 'accepted';

        // Latest non-cancelled request. "Pending" until a supervisor is assigned.
        $latestDeploymentRequest = Deployment::where('student_id', $student->id)
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->first();

        $isDeploymentPending = $latestDeploymentRequest
            && is_null($latestDeploymentRequest->supervisor_id);

        return view('student.dashboard', [
            'student' => $student,

            'fieldStudy' => [
                'hours_completed'  => $student->field_study_hours,
                'hours_required'   => $this->fieldStudyRequiredHours,
                'progress_percent' => $fieldStudyProgress,
            ],

            'internship' => [
                'hours_completed'  => $student->internship_hours,
                'hours_required'   => $this->internshipRequiredHours,
                'progress_percent' => $internshipProgress,
            ],

            // Internship stage state
            'internshipStatus'          => $student->internship_status,
            'internshipStatusLabel'     => $student->internship_status_label,
            'canSelectInternshipSchool' => $student->can_select_internship_school,
            'hasCompletedFieldStudy'    => $student->has_completed_field_study,
            'coordinatorPassed'         => $student->internship_coordinator_passed_at !== null,
            'supervisorPassed'          => $student->internship_supervisor_passed_at !== null,
            'isInternshipValid'         => $student->is_internship_valid,

            'isEligible'             => $isFieldStudyEligible,
            'isDeploymentPending'    => $isDeploymentPending,
            'isDeployed'             => $student->is_deployed,
            'currentDeployment'      => $deploymentInfo,
            'preferredPartnerSchool' => $preferredPartnerSchool,
        ]);
    }

    protected function calculateProgressPercentage(?int $completed, int $required): int
    {
        if ($required <= 0) {
            return 0;
        }

        $percentage = (int) round((($completed ?? 0) / $required) * 100);

        return min($percentage, 100);
    }
}