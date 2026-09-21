<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\FieldStudyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoordinatorFieldStudyRequestController extends Controller
{
    /**
     * Display Field Study completion requests awaiting Coordinator approval.
     *
     * Only requests that are still pending AND have already been
     * approved by the supervisor are shown here.
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
     * Coordinator approval — the final approval step.
     *
     * Only allowed when the request is still pending and the supervisor
     * has already approved it. Marks the request approved and stamps
     * the student's official Field Study completion timestamp.
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

            $fieldStudyRequest->student->update([
                'field_study_completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Field Study completion request approved. The student has been marked as completed.');
    }

    /**
     * Coordinator rejection.
     *
     * Does not touch the student's field_study_completed_at, and does
     * not require prior supervisor approval — a coordinator can reject
     * a request regardless of supervisor status, as long as it's pending.
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