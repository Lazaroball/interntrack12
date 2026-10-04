<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\FieldStudyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoordinatorFieldStudyRequestController extends Controller
{
    /**
     * Field Study completion requests awaiting Coordinator approval.
     * Only requests that are still pending AND already approved by the supervisor.
     */
    public function index()
    {
        $requests = FieldStudyRequest::with('student')
            ->where('status', 'pending')
            ->where('supervisor_approval', true)
            ->latest()
            ->get();

        return view('coordinator.field-study-requests.index', compact('requests'));
    }

    /**
     * Coordinator approval, the final approval step.
     * Completion goes through Student::markFieldStudyCompleted(), the same
     * method the manual "complete" button uses.
     */
    public function approve(Request $request, FieldStudyRequest $fieldStudyRequest)
    {
        abort_unless(
            $fieldStudyRequest->status === 'pending',
            422,
            'This Field Study request is no longer pending.'
        );

        abort_unless(
            $fieldStudyRequest->supervisor_approval === true,
            422,
            'This request cannot be approved until the supervisor has approved it first.'
        );

        DB::transaction(function () use ($fieldStudyRequest) {
            $fieldStudyRequest->update([
                'coordinator_approval' => true,
                'status'               => 'approved',
            ]);

            $fieldStudyRequest->student->markFieldStudyCompleted();
        });

        return back()->with('success', 'Field Study completion approved. The student can now submit initial Internship requirements.');
    }

    /**
     * Coordinator rejection.
     */
    public function reject(Request $request, FieldStudyRequest $fieldStudyRequest)
    {
        abort_unless(
            $fieldStudyRequest->status === 'pending',
            422,
            'This Field Study request is no longer pending.'
        );

        $fieldStudyRequest->update([
            'coordinator_approval' => false,
            'status'               => 'rejected',
        ]);

        return back()->with('success', 'Field Study completion request rejected.');
    }
}