<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FieldStudyRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentFieldStudyRequestController extends Controller
{
    /**
     * Submit a request to officially complete Field Study.
     */
    public function store(Request $request)
    {
        $student = Student::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        // Field Study must reach the required 600 hours first.
        abort_unless(
            ($student->field_study_hours ?? 0) >= 600,
            422,
            'You must complete 600 Field Study hours before requesting completion.'
        );

        // Do not allow another request after Field Study is officially completed.
        abort_if(
            $student->field_study_completed_at !== null,
            422,
            'Your Field Study has already been officially completed.'
        );

        // Prevent duplicate pending requests.
        $pendingRequestExists = FieldStudyRequest::where(
            'student_id',
            $student->id
        )
            ->where('status', 'pending')
            ->exists();

        abort_if(
            $pendingRequestExists,
            422,
            'You already have a pending Field Study completion request.'
        );

        // Create a new request.
        FieldStudyRequest::create([
            'student_id' => $student->id,
            'requested_hours' => 600,
            'status' => 'pending',
            'supervisor_approval' => false,
            'coordinator_approval' => false,
        ]);

        return back()->with(
            'success',
            'Your Field Study completion request has been submitted for approval.'
        );
    }
}