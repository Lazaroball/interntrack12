<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\PartnerSchool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDeploymentController extends Controller
{
    /**
     * Display the partner school selection page for the student.
     */
    public function index()
    {
        $student = Auth::user()?->student;

        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        $currentSchoolYear = '2025-2026';
        $currentSemester   = '1st Semester';

        // Student's existing request for the current term
        $existingDeployment = Deployment::where('student_id', $student->id)
            ->where('school_year', $currentSchoolYear)
            ->where('semester', $currentSemester)
            ->first();

        // Partner schools currently accepting students
        $partnerSchools = PartnerSchool::where('accepting_interns', true)
            ->orderBy('school_name')
            ->get();

        return view(
            'student.deployment.select-school',
            compact(
                'student',
                'partnerSchools',
                'existingDeployment'
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

    if (!$student) {
        return back()->with('error', 'Student profile not found.');
    }

    $currentSchoolYear = '2025-2026';
    $currentSemester   = '1st Semester';

    $existingDeployment = Deployment::where('student_id', $student->id)
        ->where('school_year', $currentSchoolYear)
        ->where('semester', $currentSemester)
        ->first();

    if ($existingDeployment && !is_null($existingDeployment->supervisor_id)) {
        return back()->with(
            'error',
            'Your deployment request has already been processed by the coordinator and cannot be modified.'
        );
    }

    // Backend eligibility check (the Blade comment assumes this exists)
    $school = PartnerSchool::findOrFail($validated['partner_school_id']);

    if (!$school->canAcceptNewInterns()) {
        return back()->with(
            'error',
            'That partner school is not currently available. Please choose another school.'
        );
    }

    Deployment::updateOrCreate(
        [
            'student_id'  => $student->id,
            'school_year' => $currentSchoolYear,
            'semester'    => $currentSemester,
        ],
        [
            'partner_school_id' => $validated['partner_school_id'],
            'program'           => 'Field Study', // this page is Field Study only
        ]
    );

    return back()->with(
        'success',
        'Your preferred partner school has been submitted successfully.'
    );
}
}