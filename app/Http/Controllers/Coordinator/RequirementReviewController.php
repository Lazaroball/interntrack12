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
     * List students awaiting Field Study requirement review.
     */
    public function index(Request $request)
    {
        $stage = $request->get('stage', 'Field Study');

        $students = Student::query()
            ->when($stage === 'Field Study', fn ($q) => $q->whereIn('field_study_status', [
                'pending_review', 'requirements_incomplete', 'requirements_approved', 'rejected',
            ]))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('coordinator.requirements.review.index', compact('students', 'stage'));
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
     * No ownership check against the authenticated user's own student
     * record (coordinators aren't students) — gated purely by
     * role:coordinator middleware on the route.
     */
    public function file(Requirement $requirement)
    {
        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        return Storage::disk('local')->response($requirement->file_path);
    }

    /**
     * Manually accept a student for Field Study.
     * Only allowed once all required Field Study definitions are approved.
     */
    public function acceptFieldStudy(Student $student)
    {
        $requiredDefinitionIds = RequirementDefinition::active()
            ->forStage('Field Study')
            ->where('is_required', true)
            ->pluck('id');

        $approvedCount = Requirement::where('student_id', $student->id)
            ->whereIn('requirement_definition_id', $requiredDefinitionIds)
            ->where('status', 'approved')
            ->count();

        abort_unless(
            $approvedCount >= $requiredDefinitionIds->count(),
            422,
            'All required Field Study documents must be approved before accepting this student.'
        );

        $student->update(['field_study_status' => 'accepted']);

        return redirect()
            ->route('coordinator.requirements.review.show', $student)
            ->with('success', "{$student->full_name} has been accepted for Field Study.");
    }

    /**
     * Reject a student's overall Field Study eligibility (not an
     * individual submission) — e.g. after review, sends them back to
     * correct something.
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