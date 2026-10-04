<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\PartnerSchool;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDeploymentController extends Controller
{
    // TODO: move these to a settings table / config when you add term management.
    private const SCHOOL_YEAR = '2025-2026';
    private const SEMESTER    = '1st Semester';

    /**
     * Which deployment is this student choosing a school for?
     * Anyone whose Internship is unlocked is choosing for Internship;
     * everyone else is choosing for Field Study.
     */
    private function resolveProgram(Student $student): string
    {
        return ($student->internship_status ?? 'locked') !== 'locked'
            ? 'Internship'
            : 'Field Study';
    }

    /**
     * Why (if at all) can this student NOT choose a school right now?
     * Returns null when allowed. Enforced on the server, not just the UI.
     */
    private function blockReason(Student $student, string $program): ?string
    {
        if ($program === 'Internship') {
            if ($student->internship_completed_at) {
                return 'Your Internship is already completed.';
            }

            if (! $student->can_select_internship_school) {
                return 'Your initial Internship requirements must be accepted by the coordinator before you can choose a school.';
            }

            return null;
        }

        if ($student->field_study_status !== 'accepted') {
            return 'Your Field Study requirements must be accepted by the coordinator before you can choose a school.';
        }

        return null;
    }

    private function existingDeployment(Student $student, string $program): ?Deployment
    {
        return Deployment::where('student_id', $student->id)
            ->where('program', $program)
            ->where('school_year', self::SCHOOL_YEAR)
            ->where('semester', self::SEMESTER)
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->first();
    }

    /**
     * Display the partner school selection page.
     */
    public function index()
    {
        $student = Auth::user()?->student;

        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $program            = $this->resolveProgram($student);
        $blockReason        = $this->blockReason($student, $program);
        $existingDeployment = $this->existingDeployment($student, $program);

        $partnerSchools = PartnerSchool::where('accepting_interns', true)
            ->orderBy('school_name')
            ->get();

        return view(
            'student.deployment.select-school',
            compact(
                'student',
                'partnerSchools',
                'existingDeployment',
                'program',
                'blockReason'
            )
        );
    }

    /**
     * Save or update the student's partner school request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_school_id' => ['required', 'exists:partner_schools,id'],
        ]);

        $student = Auth::user()?->student;

        if (! $student) {
            return back()->with('error', 'Student profile not found.');
        }

        $program = $this->resolveProgram($student);

        if ($reason = $this->blockReason($student, $program)) {
            return back()->with('error', $reason);
        }

        $existing = $this->existingDeployment($student, $program);

        if ($existing && ! is_null($existing->supervisor_id)) {
            return back()->with(
                'error',
                'Your deployment request has already been processed by the coordinator and cannot be modified.'
            );
        }

        $school = PartnerSchool::findOrFail($validated['partner_school_id']);

        if (! $school->canAcceptNewInterns()) {
            return back()->with(
                'error',
                'That partner school is not currently available. Please choose another school.'
            );
        }

        if ($existing) {
            $existing->update(['partner_school_id' => $school->id]);
        } else {
            Deployment::create([
                'student_id'        => $student->id,
                'partner_school_id' => $school->id,
                'program'           => $program,
                'school_year'       => self::SCHOOL_YEAR,
                'semester'          => self::SEMESTER,
                'status'            => 'pending',
            ]);
        }

        return back()->with(
            'success',
            'Your preferred partner school has been submitted successfully.'
        );
    }
}