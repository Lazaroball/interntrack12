<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use DomainException;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    /**
     * Hour targets (single place to change them).
     */
    private const FIELD_STUDY_TARGET = 600;
    private const INTERNSHIP_TARGET  = 600;

    /**
     * Display student management page.
     */
    public function index(Request $request)
    {
        $query = Student::with([
            'user',
            'deployment',
            'deployment.partnerSchool',
            'deployment.supervisor',
            'deployment.coordinator',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        | Search by: Student Number, Reference Number, First Name, Last Name, Full Name
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name,' ',last_name) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        if ($request->filled('program_type')) {
            $query->where('program_type', $request->program_type);
        }

        if ($request->filled('block')) {
            $query->where('block', $request->block);
        }

        if ($request->filled('internship_status')) {
            $query->where('internship_status', $request->internship_status);
        }

        if (
            $request->filled('is_eligible')
            && in_array($request->is_eligible, ['1', '0'])
        ) {
            $query->where('is_eligible', $request->is_eligible);
        }

        /*
        |--------------------------------------------------------------------------
        | Deployment Filter
        |--------------------------------------------------------------------------
        | The deployment state is derived from supervisor_id / completed_at
        | (the same rules the Deployments page uses), NOT from deployments.status,
        | because the approve actions never set that column.
        |--------------------------------------------------------------------------
        */

        if ($request->filled('deployment_status')) {
            match ($request->deployment_status) {
                'not_deployed'
                    => $query->whereDoesntHave('deployments'),

                'pending'
                    => $query->whereHas('deployments', fn ($d) => $d->whereNull('supervisor_id')),

                'deployed'
                    => $query->whereHas('deployments', fn ($d) => $d
                        ->whereNotNull('supervisor_id')
                        ->whereNull('completed_at')),

                'completed'
                    => $query->whereHas('deployments', fn ($d) => $d->whereNotNull('completed_at')),

                default => null,
            };
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        match ($request->get('sort', 'newest')) {
            'oldest'
                => $query->oldest(),

            'alphabetical'
                => $query
                    ->orderBy('last_name')
                    ->orderBy('first_name'),

            'student_number'
                => $query->orderBy('student_number'),

            default
                => $query->latest(),
        };

        $students = $query
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

        $programs = Student::whereNotNull('program')
            ->where('program', '!=', '')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        $programTypes = [
            'Field Study',
            'Internship',
        ];

        $internshipStatuses = [
            'locked',
            'pending_review',
            'requirements_incomplete',
            'accepted',
            'rejected',
        ];

        $blocks = Student::query()
            ->select('block')
            ->whereNotNull('block')
            ->where('block', '!=', '')
            ->distinct()
            ->orderBy('block')
            ->pluck('block');

        $deploymentStatuses = [
            'not_deployed',
            'pending',
            'deployed',
            'completed',
        ];

        /*
        |--------------------------------------------------------------------------
        | Hour Targets
        |--------------------------------------------------------------------------
        */

        $fieldStudyTarget = self::FIELD_STUDY_TARGET;
        $internshipTarget = self::INTERNSHIP_TARGET;

        /*
        |--------------------------------------------------------------------------
        | Dashboard Cards
        |--------------------------------------------------------------------------
        */

        $counts = [
            'total'
                => Student::count(),

            'field_study'
                => Student::where('program_type', 'Field Study')->count(),

            'internship'
                => Student::where('program_type', 'Internship')->count(),

            'internship_valid'
                => Student::whereNotNull('internship_completed_at')->count(),

            'eligible'
                => Student::where('is_eligible', true)->count(),

            'pending_requirements'
                => Student::where('is_eligible', false)->count(),

            'deployed'
                => Student::whereHas('deployments', fn ($d) => $d
                    ->whereNotNull('supervisor_id')
                    ->whereNull('completed_at')
                )->count(),
        ];

        return view(
            'coordinator.students.index',
            compact(
                'students',
                'programs',
                'programTypes',
                'internshipStatuses',
                'blocks',
                'deploymentStatuses',
                'counts',
                'fieldStudyTarget',
                'internshipTarget'
            )
        );
    }

    /**
     * Display a single student's profile.
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'deployments.partnerSchool',
            'deployments.supervisor',
            'deployments.coordinator',
            'internshipDeployment.partnerSchool',
            'internshipDeployment.supervisor',
        ]);

        $fieldStudyTarget = self::FIELD_STUDY_TARGET;
        $internshipTarget = self::INTERNSHIP_TARGET;

        $fieldStudyPercent = $fieldStudyTarget > 0
            ? min(round((($student->field_study_hours ?? 0) / $fieldStudyTarget) * 100), 100)
            : 0;

        $internshipPercent = $internshipTarget > 0
            ? min(round((($student->internship_hours ?? 0) / $internshipTarget) * 100), 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Requirement definitions (per stage)
        |--------------------------------------------------------------------------
        | "initial" phase first, then "ongoing".
        |--------------------------------------------------------------------------
        */

        $definitions = RequirementDefinition::active()
            ->forStage('Field Study')
            ->orderByRaw("FIELD(phase, 'initial', 'ongoing')")
            ->orderBy('id')
            ->get();

        $internshipDefinitions = RequirementDefinition::active()
            ->forStage('Internship')
            ->orderByRaw("FIELD(phase, 'initial', 'ongoing')")
            ->orderBy('id')
            ->get();

        // Ordered by id so that if a student re-uploads a document,
        // keyBy keeps the latest submission. Keyed by definition id, so this
        // one collection covers BOTH Field Study and Internship definitions.
        $submissions = Requirement::where('student_id', $student->id)
            ->orderBy('id')
            ->get()
            ->keyBy('requirement_definition_id');

        return view(
            'coordinator.students.show',
            compact(
                'student',
                'fieldStudyTarget',
                'internshipTarget',
                'fieldStudyPercent',
                'internshipPercent',
                'definitions',
                'internshipDefinitions',
                'submissions'
            )
        );
    }

    /**
     * Mark a student's Field Study as passed.
     *
     * The actual work (closing the Field Study deployment, stamping
     * field_study_completed_at, unlocking Internship) lives in
     * Student::markFieldStudyCompleted() so the FieldStudyRequest
     * approval path ends in exactly the same state.
     */
    public function completeFieldStudy(Student $student)
    {
        if ($student->field_study_completed_at) {
            return back()->with('error', 'Field Study is already marked as passed.');
        }

        $hasActiveDeployment = $student->deployments()
            ->where('program', 'Field Study')
            ->whereNotNull('supervisor_id')
            ->whereNull('completed_at')
            ->exists();

        if (! $hasActiveDeployment) {
            return back()->with('error', 'This student has no active Field Study deployment to complete.');
        }

        $student->markFieldStudyCompleted();

        return redirect()
            ->route('coordinator.students.show', $student)
            ->with('success', 'Field Study marked as passed. The student can now submit initial Internship requirements.');
    }

    /**
     * Coordinator "Pass" for the Internship.
     *
     * This alone does NOT make the student valid. The student only becomes
     * valid once the supervisor has also passed (see
     * Student::finalizeInternshipIfBothPassed()).
     */
    public function passInternship(Student $student)
    {
        $coordinator = auth()->user()->coordinator;

        abort_unless($coordinator, 403, 'No coordinator profile found for this account.');

        if ($student->internship_coordinator_passed_at) {
            return back()->with('error', 'You have already passed this student for the Internship.');
        }

        try {
            $student->passInternshipAsCoordinator($coordinator->id);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $student->refresh();

        return redirect()
            ->route('coordinator.students.show', $student)
            ->with(
                'success',
                $student->is_internship_valid
                    ? 'Both the coordinator and supervisor have passed. The student is now VALID for the Internship.'
                    : 'Your Pass was recorded. The student becomes valid once the supervisor also passes.'
            );
    }
}