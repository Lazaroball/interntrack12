<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use App\Models\Supervisor;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupervisorStudentController extends Controller
{
    private function supervisor(Request $request): Supervisor
    {
        return Supervisor::where('user_id', $request->user()->id)->firstOrFail();
    }

    /**
     * This supervisor's open Internship deployment for a student, if any.
     */
    private function internshipDeploymentFor(Supervisor $supervisor, int $studentId)
    {
        return $supervisor->deployments()
            ->where('student_id', $studentId)
            ->where('program', 'Internship')
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->first();
    }

    /**
     * Display students assigned to the logged-in supervisor.
     */
    public function index(Request $request)
    {
        $supervisor = $this->supervisor($request);

        $deployments = $supervisor->deployments()
            ->with(['student', 'partnerSchool'])
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->latest('deployment_date')
            ->get();

        return view(
            'supervisor.students.index',
            compact('supervisor', 'deployments')
        );
    }

    /**
     * Display a single student's profile, scoped to this supervisor's
     * own deployment record for that student.
     */
    public function show(Request $request, $studentId)
    {
        $supervisor = $this->supervisor($request);

        // Scoped to $supervisor->deployments() so a supervisor can never
        // view a student who isn't actually assigned to them.
        $deployment = $supervisor->deployments()
            ->with(['student', 'partnerSchool'])
            ->where('student_id', $studentId)
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->firstOrFail();

        $student = $deployment->student;

        // TODO: placeholders. Keep in sync with the coordinator controller.
        $fieldStudyTarget = 600;
        $internshipTarget = 600;

        $fieldStudyPercent = $fieldStudyTarget > 0
            ? min(100, round((($student->field_study_hours ?? 0) / $fieldStudyTarget) * 100))
            : 0;

        $internshipPercent = $internshipTarget > 0
            ? min(100, round((($student->internship_hours ?? 0) / $internshipTarget) * 100))
            : 0;

        // ── Internship documents (READ-ONLY for the supervisor) ──
        $internshipDefinitions = RequirementDefinition::active()
            ->forStage('Internship')
            ->orderByRaw("FIELD(phase, 'initial', 'ongoing')")
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        $submissions = Requirement::where('student_id', $student->id)
            ->whereNotNull('requirement_definition_id')
            ->orderBy('id')
            ->get()
            ->keyBy('requirement_definition_id');

        $internshipDeployment = $this->internshipDeploymentFor($supervisor, $student->id);

        $allRequiredApproved = $student->hasAllRequiredApproved('Internship');

        // Shown to the supervisor so they know why the Pass button is disabled.
        $canPassInternship = $internshipDeployment !== null
            && $internshipDeployment->is_approved
            && $student->internship_status === 'accepted'
            && $student->internship_completed_at === null
            && $student->internship_supervisor_passed_at === null
            && $allRequiredApproved;

        return view('supervisor.students.show', compact(
            'student',
            'deployment',
            'fieldStudyTarget',
            'internshipTarget',
            'fieldStudyPercent',
            'internshipPercent',
            'internshipDefinitions',
            'submissions',
            'internshipDeployment',
            'allRequiredApproved',
            'canPassInternship'
        ));
    }

    /**
     * Supervisor "Pass" for the Internship.
     *
     * This alone does NOT make the student valid. The student only becomes
     * valid once the coordinator has also passed.
     */
    public function passInternship(Request $request, Student $student)
    {
        $supervisor = $this->supervisor($request);

        abort_unless(
            $this->internshipDeploymentFor($supervisor, $student->id),
            403,
            'This student is not assigned to you for Internship.'
        );

        if ($student->internship_supervisor_passed_at) {
            return back()->with('error', 'You have already passed this student for the Internship.');
        }

        try {
            $student->passInternshipAsSupervisor($supervisor->id);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $student->refresh();

        return back()->with(
            'success',
            $student->is_internship_valid
                ? 'Both the coordinator and supervisor have passed. The student is now VALID for the Internship.'
                : 'Your Pass was recorded. The student becomes valid once the coordinator also passes.'
        );
    }

    /**
     * Stream an Internship document, read-only.
     * Only for students assigned to this supervisor.
     */
    public function file(Request $request, Student $student, Requirement $requirement)
    {
        $supervisor = $this->supervisor($request);

        abort_unless(
            $supervisor->deployments()->where('student_id', $student->id)->exists(),
            403,
            'This student is not assigned to you.'
        );

        abort_unless($requirement->student_id === $student->id, 404);

        abort_unless(
            optional($requirement->requirementDefinition)->stage === 'Internship',
            403,
            'You can only view Internship documents.'
        );

        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        return Storage::disk('local')->response($requirement->file_path);
    }
}