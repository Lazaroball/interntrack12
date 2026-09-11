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
    /**
     * Display the authenticated student's Field Study requirement checklist.
     */
    public function index()
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        $definitions = RequirementDefinition::active()
            ->forStage('Field Study')
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        $submissions = Requirement::where('student_id', $student->id)
            ->whereNotNull('requirement_definition_id')
            ->get()
            ->keyBy('requirement_definition_id');

        return view('student.field-study.requirements.index', [
            'student'     => $student,
            'definitions' => $definitions,
            'submissions' => $submissions,
        ]);
    }

    /**
     * Store a submission against a specific requirement definition.
     */
    public function store(Request $request)
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        $validated = $request->validate([
            'requirement_definition_id' => ['required', 'exists:requirement_definitions,id'],
            'file'                       => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ]);

        $definition = RequirementDefinition::findOrFail($validated['requirement_definition_id']);

        $storedPath = $request->file('file')->store(
            "requirements/{$student->id}",
            'local'
        );

        // Resubmission: if a submission already exists for this definition
        // (e.g. a rejected one being corrected), update it in place rather
        // than creating a duplicate row.
        Requirement::updateOrCreate(
            [
                'student_id'                 => $student->id,
                'requirement_definition_id'  => $definition->id,
            ],
            [
                'file_path'     => $storedPath,
                'status'        => 'pending',
                'remarks'       => null,
                'submitted_at'  => now(),
                'reviewed_at'   => null,
                'reviewed_by'   => null,
            ]
        );

        return redirect()
            ->route('student.field-study.requirements')
            ->with('success', 'Requirement submitted successfully.');
    }

    /**
     * Stream a requirement file, verifying ownership first.
     */
    public function show(Requirement $requirement)
    {
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');
        abort_unless($requirement->student_id === $student->id, 403, 'You do not have access to this file.');
        abort_if(empty($requirement->file_path), 404, 'No file submitted for this requirement.');
        abort_unless(Storage::disk('local')->exists($requirement->file_path), 404, 'File not found.');

        return Storage::disk('local')->response($requirement->file_path);
    }
}