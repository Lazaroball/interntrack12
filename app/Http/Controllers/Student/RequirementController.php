<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    private const STAGES = ['Field Study', 'Internship'];

    /**
     * Resolve the stage for index(). The Internship routes pass it as a
     * route default; anything else falls back to Field Study so the
     * existing Field Study URLs keep working.
     */
    private function resolveStage(Request $request): string
    {
        $stage = $request->route('stage') ?? 'Field Study';

        return in_array($stage, self::STAGES, true) ? $stage : 'Field Study';
    }

    private function currentStudent(): Student
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        return $student;
    }

    /**
     * Has the student been accepted for this stage's ongoing phase?
     */
    private function isAcceptedFor(Student $student, string $stage): bool
    {
        return $stage === 'Internship'
            ? $student->internship_status === 'accepted'
            : $student->field_study_status === 'accepted';
    }

    /**
     * Why (if at all) may this student NOT submit this definition right now?
     * Returns null when submission is allowed.
     *
     * This is the server-side gate. It is checked independently of what
     * the UI shows, because a definition ID can be posted directly.
     */
    private function submissionBlockReason(Student $student, RequirementDefinition $definition): ?string
    {
        $stage = $definition->stage;
        $phase = $definition->phase ?? 'initial';

        if ($stage === 'Internship') {
            if ($student->internship_status === 'locked') {
                return 'Internship requirements unlock after you complete Field Study.';
            }

            if ($student->internship_completed_at !== null) {
                return 'Your Internship is already completed.';
            }
        }

        if ($phase === 'ongoing' && ! $this->isAcceptedFor($student, $stage)) {
            return 'You are not currently allowed to submit this requirement.';
        }

        return null;
    }

    /**
     * Display the student's requirement checklist for a stage.
     *
     * GET /student/field-study/requirements
     * GET /student/internship/requirements
     *
     * Initial-phase definitions are always shown. Ongoing-phase definitions
     * only appear once the student has been accepted for that stage.
     */
    public function index(Request $request)
    {
        $student = $this->currentStudent();
        $stage   = $this->resolveStage($request);

        if ($stage === 'Internship' && $student->internship_status === 'locked') {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Internship requirements unlock after you complete Field Study.');
        }

        $definitions = RequirementDefinition::active()
            ->forStage($stage)
            ->when(! $this->isAcceptedFor($student, $stage), function ($query) {
                // Not yet accepted: only initial-phase requirements are
                // visible. A null/legacy phase counts as 'initial' so older
                // definitions don't silently disappear from the checklist.
                $query->where(function ($q) {
                    $q->where('phase', 'initial')->orWhereNull('phase');
                });
            })
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        $submissions = Requirement::where('student_id', $student->id)
            ->whereNotNull('requirement_definition_id')
            ->orderBy('id')
            ->get()
            ->keyBy('requirement_definition_id');

        // Same Blade view for both stages. It receives $stage so it can
        // adjust headings and links.
        return view('student.field-study.requirements.index', [
            'student'     => $student,
            'stage'       => $stage,
            'definitions' => $definitions,
            'submissions' => $submissions,
        ]);
    }

    /**
     * Store a submission against a specific requirement definition.
     *
     * The stage comes from the DEFINITION itself, never from user input,
     * so a student can't submit an Internship document by pretending
     * it's Field Study (or the other way round).
     */
    public function store(Request $request)
    {
        $student = $this->currentStudent();

        $validated = $request->validate([
            'requirement_definition_id' => ['required', 'exists:requirement_definitions,id'],
            'file'                       => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ]);

        $definition = RequirementDefinition::active()
            ->find($validated['requirement_definition_id']);

        abort_if(
            $definition === null || ! in_array($definition->stage, self::STAGES, true),
            403,
            'You are not currently allowed to submit this requirement.'
        );

        $blockReason = $this->submissionBlockReason($student, $definition);

        abort_if($blockReason !== null, 403, $blockReason);

        // An approved document is final. Re-uploading would silently reset
        // it to "pending" and undo the coordinator's approval.
        $existing = Requirement::where('student_id', $student->id)
            ->where('requirement_definition_id', $definition->id)
            ->first();

        abort_if(
            $existing && $existing->status === 'approved',
            422,
            'This requirement has already been approved and cannot be replaced.'
        );

        $storedPath = $request->file('file')->store(
            "requirements/{$student->id}",
            'local'
        );

        // Resubmission: update in place (e.g. a rejected one being corrected)
        // rather than creating a duplicate row.
        Requirement::updateOrCreate(
            [
                'student_id'                => $student->id,
                'requirement_definition_id' => $definition->id,
            ],
            [
                'file_path'    => $storedPath,
                'status'       => 'pending',
                'remarks'      => null,
                'submitted_at' => now(),
                'reviewed_at'  => null,
                'reviewed_by'  => null,
            ]
        );

        // Internship only: a student who was sent back for correction is
        // put back in the coordinator's review queue after resubmitting an
        // initial document.
        if (
            $definition->stage === 'Internship'
            && ($definition->phase ?? 'initial') === 'initial'
            && in_array($student->internship_status, ['requirements_incomplete', 'rejected'], true)
        ) {
            $student->update(['internship_status' => 'pending_review']);
        }

        $routeName = $definition->stage === 'Internship'
            ? 'student.internship.requirements'
            : 'student.field-study.requirements';

        return redirect()
            ->route($routeName)
            ->with('success', 'Requirement submitted successfully.');
    }

    /**
     * Stream a requirement file, verifying ownership first.
     */
    public function show(Requirement $requirement)
    {
        $student = $this->currentStudent();

        abort_unless($requirement->student_id === $student->id, 403, 'You do not have access to this file.');
        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        return Storage::disk('local')->response($requirement->file_path);
    }
}