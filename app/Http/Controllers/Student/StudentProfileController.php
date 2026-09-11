<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    /**
     * Display the authenticated student's profile.
     *
     * GET /student/profile
     * Route name: student.profile
     */
    public function index(): View
    {
        // No relationships are eager-loaded here because every field
        // displayed on this page (personal, academic, and status info)
        // already lives directly on the Student model.
        $student = Student::where('user_id', Auth::id())->first();

        abort_unless($student !== null, 403, 'No student profile found for this account.');

        return view('student.profile', [
            'student' => $student,
        ]);
    }
}