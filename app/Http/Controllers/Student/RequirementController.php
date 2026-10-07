<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\RequirementDefinition;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequirementController extends Controller
{
    private const STAGES = ['Field Study', 'Internship'];

    /** Images, Word and Excel only. */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

    private const INTERNSHIP_LOCKED_MESSAGE = 'Internship unlocks after the coordinator clears your Field Study.';

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

    private function isAcceptedFor(Student $student, string $stage): bool
    {
        return $stage === 'Internship'
            ? $student->internship_status === 'accepted'
            : $student->field_study_status === 'accepted';
    }

    /**
     * Deployed = the coordinator approved a placement (supervisor + date set)
     * for THIS stage's program. Completed placements still count.
     */
    private function isDeployedFor(Student $student, string $stage): bool
    {
        return $student->deployments()
            ->where('program', $stage)
            ->deployed()
            ->exists();
    }

    /**
     * Ongoing requirements are visible/submittable only after the student is
     * accepted for the stage AND deployed in it.
     */
    private function canSeeOngoing(Student $student, string $stage): bool
    {
        return $this->isAcceptedFor($student, $stage)
            && $this->isDeployedFor($student, $stage);
    }

    private function stageClosed(Student $student, string $stage): bool
    {
        return $stage === 'Internship'
            ? $student->internship_completed_at !== null
            : $student->field_study_completed_at !== null;
    }

    private function submissionBlockReason(Student $student, RequirementDefinition $definition): ?string
    {
        $stage = $definition->stage;
        $phase = $definition->phase ?? 'initial';

        if ($stage === 'Internship' && ! $student->is_internship_unlocked) {
            return self::INTERNSHIP_LOCKED_MESSAGE;
        }

        if ($this->stageClosed($student, $stage)) {
            return $stage === 'Internship'
                ? 'Your Internship is already completed.'
                : 'Your Field Study is already completed.';
        }

        if ($phase === 'ongoing' && ! $this->canSeeOngoing($student, $stage)) {
            return 'You are not currently allowed to submit this requirement.';
        }

        return null;
    }

    public function index(Request $request)
    {
        $student = $this->currentStudent();
        $stage   = $this->resolveStage($request);

        // Gate enforced here, not only by hiding the tab.
        if ($stage === 'Internship' && ! $student->is_internship_unlocked) {
            return redirect()
                ->route('student.field-study')
                ->with('error', self::INTERNSHIP_LOCKED_MESSAGE);
        }

        $showOngoing = $this->canSeeOngoing($student, $stage);

        $definitions = RequirementDefinition::active()
            ->forStage($stage)
            ->when(! $showOngoing, function ($query) {
                $query->where(function ($q) {
                    $q->where('phase', 'initial')->orWhereNull('phase');
                });
            })
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        // Only this stage's submissions, so Field Study and Internship never mix.
        $submissions = Requirement::where('student_id', $student->id)
            ->whereHas('requirementDefinition', fn ($d) => $d->where('stage', $stage))
            ->orderBy('id')
            ->get()
            ->keyBy('requirement_definition_id');

        $hasInternshipDeployment = $student->deployments()
            ->where('program', 'Internship')
            ->where('status', '!=', 'cancelled')
            ->exists();

        return view('student.field-study.requirements.index', [
            'student'          => $student,
            'stage'            => $stage,
            'definitions'      => $definitions,
            'submissions'      => $submissions,
            'stageClosed'      => $this->stageClosed($student, $stage),
            'showOngoing'      => $showOngoing,
            'canChooseSchool'  => $stage === 'Internship'
                                    && $student->can_select_internship_school
                                    && ! $hasInternshipDeployment,
        ]);
    }

    public function store(Request $request)
    {
        $student = $this->currentStudent();

        $validated = $request->validate([
            'requirement_definition_id' => ['required', 'exists:requirement_definitions,id'],
            'file'                       => ['required', 'file', 'mimes:' . implode(',', self::ALLOWED_EXTENSIONS), 'max:5120'],
        ], [
            'file.mimes' => 'Only images (JPG, PNG), Word (DOC, DOCX) and Excel (XLS, XLSX) files are allowed.',
            'file.max'   => 'The file must not be larger than 5 MB.',
        ]);

        $definition = RequirementDefinition::active()
            ->find($validated['requirement_definition_id']);

        abort_if(
            $definition === null || ! in_array($definition->stage, self::STAGES, true),
            403,
            'You are not currently allowed to submit this requirement.'
        );

        $routeName = $definition->stage === 'Internship'
            ? 'student.internship.requirements'
            : 'student.field-study.requirements';

        $blockReason = $this->submissionBlockReason($student, $definition);

        if ($blockReason !== null) {
            $target = ($definition->stage === 'Internship' && ! $student->is_internship_unlocked)
                ? 'student.field-study'
                : $routeName;

            return redirect()->route($target)->with('error', $blockReason);
        }

        $existing = Requirement::where('student_id', $student->id)
            ->where('requirement_definition_id', $definition->id)
            ->first();

        if ($existing && $existing->is_locked) {
            return back()->with('error', 'This requirement was already approved by the coordinator and can no longer be changed.');
        }

        $newStatus = match (true) {
            $existing === null
                => Requirement::STATUS_PENDING,

            in_array($existing->status, [Requirement::STATUS_REJECTED, Requirement::STATUS_RESUBMITTED], true)
                => Requirement::STATUS_RESUBMITTED,

            default
                => Requirement::STATUS_PENDING,
        };

        $file = $request->file('file');

        // The random name stays on disk (safe, no collisions).
        // The student's real file name is saved in the database.
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $extension = $file->guessExtension() ?: 'bin';
        }

        $oldPath      = $existing?->file_path;
        $originalName = Str::limit($file->getClientOriginalName(), 250, '');
        $storedPath   = $file->storeAs(
            "requirements/{$student->id}",
            Str::random(40) . '.' . $extension,
            'local'
        );

        Requirement::updateOrCreate(
            [
                'student_id'                => $student->id,
                'requirement_definition_id' => $definition->id,
            ],
            [
                'file_path'     => $storedPath,
                'original_name' => $originalName,
                'status'        => $newStatus,
                'submitted_at'  => now(),
                'reviewed_at'   => null,
                'reviewed_by'   => null,
            ]
        );

        if ($oldPath && $oldPath !== $storedPath) {
            Storage::disk('local')->delete($oldPath);
        }

        if (($definition->phase ?? 'initial') === 'initial') {
            $statusColumn = $definition->stage === 'Internship' ? 'internship_status' : 'field_study_status';

            if (in_array($student->{$statusColumn}, ['requirements_incomplete', 'rejected'], true)) {
                $student->update([$statusColumn => 'pending_review']);
            }
        }

        $message = match ($newStatus) {
            Requirement::STATUS_RESUBMITTED => 'Requirement resubmitted. Your coordinator will review it again.',
            default                         => $existing
                                                ? 'File updated. Your coordinator will review the new file.'
                                                : 'Requirement submitted successfully.',
        };

        return redirect()
            ->route($routeName)
            ->with('success', $message);
    }

    /**
     * Stream the student's own file. Inline by default (so the page can show it),
     * or as a download with ?download=1. Both use the original file name.
     */
    public function show(Request $request, Requirement $requirement)
    {
        $student = $this->currentStudent();

        abort_unless($requirement->student_id === $student->id, 403, 'You do not have access to this file.');
        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        $name = $requirement->display_name;

        if ($request->boolean('download')) {
            return Storage::disk('local')->download($requirement->file_path, $name);
        }

        return Storage::disk('local')->response($requirement->file_path, $name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}