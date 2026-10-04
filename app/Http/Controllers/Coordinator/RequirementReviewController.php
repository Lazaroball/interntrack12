<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementReviewController extends Controller
{
    private const STAGES = ['Field Study', 'Internship'];

    /**
     * Resolve the stage from the request. Anything unknown falls back
     * to Field Study so old links keep working.
     */
    private function resolveStage(Request $request): string
    {
        $stage = $request->get('stage', 'Field Study');

        return in_array($stage, self::STAGES, true) ? $stage : 'Field Study';
    }

    /**
     * The students column that holds the review status for a stage.
     */
    private function statusColumn(string $stage): string
    {
        return $stage === 'Internship' ? 'internship_status' : 'field_study_status';
    }

    /**
     * List students awaiting requirement review for the chosen stage,
     * with a submission-completion summary (grey/orange/green),
     * split by phase (initial/ongoing).
     *
     * GET /coordinator/requirements/review?stage=Field Study|Internship
     */
    public function index(Request $request)
    {
        $stage        = $this->resolveStage($request);
        $statusColumn = $this->statusColumn($stage);

        // Accept both the new ?status= and the old ?field_study_status= filter.
        $statusFilter = $request->get('status', $request->get('field_study_status'));

        $reviewableStatuses = $stage === 'Internship'
            ? ['pending_review', 'requirements_incomplete', 'accepted', 'rejected']
            : ['pending_review', 'requirements_incomplete', 'requirements_approved', 'accepted', 'rejected'];

        $students = Student::query()
            // Internship: "locked" students haven't finished Field Study yet,
            // so they never appear here.
            ->whereIn($statusColumn, $reviewableStatuses)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->get('search');
                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%")
                        ->orWhere('program', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('program'), fn ($q) => $q->where('program', $request->get('program')))
            ->when(
                filled($statusFilter),
                fn ($q) => $q->where($statusColumn, $statusFilter)
            )
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $programs = Student::query()
            ->whereNotNull('program')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        // ── Required, active definitions for the stage, split by phase ──
        // (a definition with no phase set falls under 'initial' by default,
        // matching the original requirements which predate the phase column)
        $requiredDefinitions = RequirementDefinition::active()
            ->forStage($stage)
            ->where('is_required', true)
            ->get(['id', 'phase']);

        $initialDefinitionIds = $requiredDefinitions
            ->filter(fn ($d) => ($d->phase ?? 'initial') === 'initial')
            ->pluck('id');

        $ongoingDefinitionIds = $requiredDefinitions
            ->filter(fn ($d) => $d->phase === 'ongoing')
            ->pluck('id');

        $allRequiredDefinitionIds = $requiredDefinitions->pluck('id');

        // One query for every student on this page: which required
        // definitions have a submission (any status counts as "submitted").
        $submittedByStudent = Requirement::whereIn('student_id', $students->pluck('id'))
            ->whereIn('requirement_definition_id', $allRequiredDefinitionIds)
            ->get(['student_id', 'requirement_definition_id'])
            ->groupBy('student_id');

        foreach ($students as $student) {
            $submittedIds = $submittedByStudent->get($student->id, collect())
                ->pluck('requirement_definition_id');

            $initialSubmitted = $submittedIds->intersect($initialDefinitionIds)->unique()->count();
            $ongoingSubmitted = $submittedIds->intersect($ongoingDefinitionIds)->unique()->count();

            $student->initial_required  = $initialDefinitionIds->count();
            $student->initial_submitted = $initialSubmitted;
            $student->ongoing_required  = $ongoingDefinitionIds->count();
            $student->ongoing_submitted = $ongoingSubmitted;

            $student->submission_progress = $this->progressState(
                $initialDefinitionIds->count(),
                $initialSubmitted
            );

            $student->ongoing_submission_progress = $this->progressState(
                $ongoingDefinitionIds->count(),
                $ongoingSubmitted
            );
        }

        return view('coordinator.requirements.review.index', compact(
            'students',
            'stage',
            'programs',
            'statusColumn'
        ));
    }

    /**
     * Grey / Orange / Green submission-progress state for a required-count
     * vs submitted-count pair. "Submitted" only, never conflated with
     * approval status.
     */
    private function progressState(int $requiredCount, int $submittedCount): string
    {
        if ($requiredCount === 0) {
            return 'none';
        }

        if ($submittedCount === 0) {
            return 'grey';
        }

        if ($submittedCount < $requiredCount) {
            return 'orange';
        }

        return 'green';
    }

    /**
     * Show a single student's requirement checklist for review.
     *
     * GET /coordinator/requirements/review/{student}?stage=Internship
     */
    public function show(Request $request, Student $student)
    {
        $stage = $this->resolveStage($request);

        // An Internship checklist is meaningless until Field Study is done.
        if ($stage === 'Internship' && $student->internship_status === 'locked') {
            return redirect()
                ->route('coordinator.requirements.review.show', $student)
                ->with('error', 'This student has not completed Field Study yet, so Internship requirements are locked.');
        }

        $definitions = RequirementDefinition::active()
            ->forStage($stage)
            ->orderByRaw("FIELD(phase, 'initial', 'ongoing')")
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        $submissions = Requirement::where('student_id', $student->id)
            ->whereNotNull('requirement_definition_id')
            ->orderBy('id')
            ->get()
            ->keyBy('requirement_definition_id');

        return view('coordinator.requirements.review.show', compact(
            'student',
            'stage',
            'definitions',
            'submissions'
        ));
    }

    /**
     * Approve or reject an individual submission.
     */
    public function updateSubmission(Request $request, Requirement $requirement)
    {
        $validated = $request->validate([
            'status'  => ['required', 'in:approved,rejected'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $requirement->update([
            'status'      => $validated['status'],
            'remarks'     => $validated['remarks'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => optional(auth()->user()->coordinator)->id,
        ]);

        return back()->with('success', 'Submission updated.');
    }

    /**
     * Securely stream a submitted file for coordinator review.
     */
    public function file(Requirement $requirement)
    {
        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        return Storage::disk('local')->response($requirement->file_path);
    }

    /*
    |--------------------------------------------------------------------------
    | Field Study acceptance
    |--------------------------------------------------------------------------
    */

    /**
     * Manually accept a student for Field Study.
     *
     * Only required initial requirements are needed for initial acceptance.
     * Ongoing requirements become available after the student is accepted.
     */
    public function acceptFieldStudy(Student $student)
    {
        abort_unless(
            $student->hasAllRequiredApproved('Field Study', 'initial'),
            422,
            'All required initial Field Study documents must be approved before accepting this student.'
        );

        $student->update([
            'field_study_status' => 'accepted',
        ]);

        return redirect()
            ->route('coordinator.requirements.review.show', $student)
            ->with('success', "{$student->full_name} has been accepted for Field Study.");
    }

    /**
     * Reject a student's overall Field Study eligibility.
     */
    public function rejectFieldStudy(Request $request, Student $student)
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $student->update(['field_study_status' => 'rejected']);

        return redirect()
            ->route('coordinator.requirements.review.show', $student)
            ->with('success', "{$student->full_name}'s Field Study status set to Rejected.");
    }

    /*
    |--------------------------------------------------------------------------
    | Internship acceptance
    |--------------------------------------------------------------------------
    */

    /**
     * Accept a student's INITIAL Internship requirements.
     *
     * After this the student may choose an Internship school
     * (Student::can_select_internship_school).
     */
    public function acceptInternship(Student $student)
    {
        abort_if(
            $student->internship_status === 'locked',
            422,
            'This student has not completed Field Study yet.'
        );

        abort_if(
            $student->internship_completed_at !== null,
            422,
            'This student has already completed the Internship.'
        );

        abort_unless(
            $student->hasAllRequiredApproved('Internship', 'initial'),
            422,
            'All required initial Internship documents must be approved before accepting this student.'
        );

        $student->update([
            'internship_status' => 'accepted',
        ]);

        return redirect()
            ->route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Internship'])
            ->with('success', "{$student->full_name} has been accepted for Internship. They can now choose an Internship school.");
    }

    /**
     * Reject a student's Internship eligibility (needs correction).
     */
    public function rejectInternship(Request $request, Student $student)
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if(
            $student->internship_status === 'locked',
            422,
            'This student has not completed Field Study yet.'
        );

        abort_if(
            $student->internship_completed_at !== null,
            422,
            'This student has already completed the Internship.'
        );

        $student->update(['internship_status' => 'rejected']);

        return redirect()
            ->route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Internship'])
            ->with('success', "{$student->full_name}'s Internship status set to Rejected.");
    }
}