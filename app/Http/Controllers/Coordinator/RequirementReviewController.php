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
    /**
     * List students awaiting Field Study requirement review, along with
     * a computed submission-completion summary (grey/orange/green) for
     * the current stage — split by phase (initial/ongoing).
     */
    public function index(Request $request)
    {
        $stage = $request->get('stage', 'Field Study');

        $students = Student::query()
            ->when($stage === 'Field Study', fn ($q) => $q->whereIn('field_study_status', [
                'pending_review', 'requirements_incomplete', 'requirements_approved', 'rejected',
            ]))
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
            ->when($request->filled('field_study_status'), fn ($q) => $q->where('field_study_status', $request->get('field_study_status')))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $programs = Student::query()
            ->whereNotNull('program')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        // ── Required, active definitions for the current stage, split by phase ──
        // (a definition with no phase set falls under 'initial' by default,
        // matching the original Field Study requirements which predate the
        // phase column)
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

        // ── One query for every student on this page: which required
        // definitions have a submission (any status counts as "submitted") ──
        $studentIds = $students->pluck('id');

        $submittedByStudent = Requirement::whereIn('student_id', $studentIds)
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

            // Overall submission-progress badge for the currently active
            // phase view (defaults to 'initial' — the phase filter, when
            // added client-side, decides which counts to actually show).
            $student->submission_progress = $this->progressState(
                $initialDefinitionIds->count(),
                $initialSubmitted
            );

            $student->ongoing_submission_progress = $this->progressState(
                $ongoingDefinitionIds->count(),
                $ongoingSubmitted
            );
        }

        return view('coordinator.requirements.review.index', compact('students', 'stage', 'programs'));
    }

    /**
     * Grey / Orange / Green submission-progress state for a required-count
     * vs submitted-count pair. "Submitted" only — never conflated with
     * approval status.
     */
    private function progressState(int $requiredCount, int $submittedCount): string
    {
        if ($requiredCount === 0) {
            return 'none'; // no required definitions configured for this phase — nothing to show
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
     */
    public function show(Student $student)
    {
        $definitions = RequirementDefinition::active()
            ->forStage('Field Study')
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        $submissions = Requirement::where('student_id', $student->id)
            ->whereNotNull('requirement_definition_id')
            ->get()
            ->keyBy('requirement_definition_id');

        return view('coordinator.requirements.review.show', compact('student', 'definitions', 'submissions'));
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

    /**
     * Manually accept a student for Field Study.
     */
   /**
 * Manually accept a student for Field Study.
 *
 * Only required initial requirements are needed for initial acceptance.
 * Ongoing requirements become available after the student is accepted.
 */
public function acceptFieldStudy(Student $student)
{
    $requiredInitialDefinitionIds = RequirementDefinition::active()
        ->forStage('Field Study')
        ->where('is_required', true)
        ->where(function ($query) {
            $query->where('phase', 'initial')
                ->orWhereNull('phase');
        })
        ->pluck('id');

    $approvedCount = Requirement::where('student_id', $student->id)
        ->whereIn('requirement_definition_id', $requiredInitialDefinitionIds)
        ->where('status', 'approved')
        ->distinct('requirement_definition_id')
        ->count('requirement_definition_id');

    abort_unless(
        $approvedCount >= $requiredInitialDefinitionIds->count(),
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
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $student->update(['field_study_status' => 'rejected']);

        return redirect()
            ->route('coordinator.requirements.review.show', $student)
            ->with('success', "{$student->full_name}'s Field Study status set to Rejected.");
    }
}