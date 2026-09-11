<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Field Study requirement, in hours, per the current program guidelines.
     * (Internship requirement is intentionally NOT hardcoded here — see index().)
     */
    protected int $fieldStudyRequiredHours = 600;

    /**
     * Display the authenticated student's dashboard.
     *
     * GET /student/dashboard
     * Route name: student.dashboard
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

        // No confirmed internship-hour requirement exists in the current
        // project, so we only pass the raw hours logged and leave the
        // percentage/requirement for a later step once that value is defined.
        $internshipHoursLogged = $student->internship_hours;

        $currentDeployment = $student->currentDeployment;

        $deploymentInfo = $currentDeployment ? [
            'id'               => $currentDeployment->id,
            'status'           => $currentDeployment->status,
            'program'          => $currentDeployment->program,
            'school_year'      => $currentDeployment->school_year,
            'semester'         => $currentDeployment->semester,
            'deployment_date'  => $currentDeployment->deployment_date,
            'completed_at'     => $currentDeployment->completed_at,
            'remarks'          => $currentDeployment->remarks,
            'is_approved'      => $currentDeployment->is_approved,
            'partner_school'   => $currentDeployment->partnerSchool->school_name ?? null,
            'supervisor_name'  => $currentDeployment->supervisor->full_name ?? null,
        ] : null;

        $preferredPartnerSchool = $student->preferredPartnerSchool;

        return view('student.dashboard', [
            'student' => $student,

            'fieldStudy' => [
                'hours_completed'  => $student->field_study_hours,
                'hours_required'   => $this->fieldStudyRequiredHours,
                'progress_percent' => $fieldStudyProgress,
            ],

            'internship' => [
                'hours_completed'  => $internshipHoursLogged,
                'hours_required'   => null, // not yet defined in the system
                'progress_percent' => null, // cannot be calculated without a requirement
            ],

            'isEligible' => (bool) $student->is_eligible,

            'isDeployed' => $student->is_deployed,

            'currentDeployment' => $deploymentInfo,

            'preferredPartnerSchool' => $preferredPartnerSchool,
        ]);
    }

    /**
     * Calculate a progress percentage, capped at 100.
     */
    protected function calculateProgressPercentage(?int $completed, int $required): int
    {
        if ($required <= 0) {
            return 0;
        }

        $completed = $completed ?? 0;

        $percentage = (int) round(($completed / $required) * 100);

        return min($percentage, 100);
    }
}