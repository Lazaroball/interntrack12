<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\FieldStudyRequest;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldStudyRequestController extends Controller
{
    /**
     * Display pending Field Study completion requests belonging to
     * students currently assigned to the logged-in supervisor.
     */
    public function index(Request $request)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)->firstOrFail();

        $requests = FieldStudyRequest::with('student')
            ->where('status', 'pending')
            ->whereHas('student.deployments', function ($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id)
                    ->whereNull('completed_at');
            })
            ->latest()
            ->get();

        return view('supervisor.field-study-requests.index', compact('requests'));
    }

    /**
     * Approve a Field Study completion request.
     */
    public function approve(Request $request, FieldStudyRequest $fieldStudyRequest)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)->firstOrFail();

        $this->authorizeRequest($fieldStudyRequest, $supervisor);

        abort_unless(
            $fieldStudyRequest->status === 'pending',
            422,
            'This Field Study request is no longer pending.'
        );

        DB::transaction(function () use ($fieldStudyRequest) {
            $fieldStudyRequest->supervisor_approval = true;

            if ($fieldStudyRequest->supervisor_approval && $fieldStudyRequest->coordinator_approval) {
                $fieldStudyRequest->status = 'approved';
                $fieldStudyRequest->student->update([
                    'field_study_completed_at' => now(),
                ]);
            }

            $fieldStudyRequest->save();
        });

        return back()->with('success', 'Field Study completion request approved.');
    }

    /**
     * Reject a Field Study completion request.
     */
    public function reject(Request $request, FieldStudyRequest $fieldStudyRequest)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)->firstOrFail();

        $this->authorizeRequest($fieldStudyRequest, $supervisor);

        abort_unless(
            $fieldStudyRequest->status === 'pending',
            422,
            'This Field Study request is no longer pending.'
        );

        $fieldStudyRequest->update([
            'status'               => 'rejected',
            'supervisor_approval'  => false,
        ]);

        return back()->with('success', 'Field Study completion request rejected.');
    }

    /**
     * Make sure the request belongs to a student currently assigned
     * (active deployment) to the logged-in supervisor.
     */
    private function authorizeRequest(FieldStudyRequest $fieldStudyRequest, Supervisor $supervisor): void
    {
        $belongsToSupervisor = $supervisor->deployments()
            ->where('student_id', $fieldStudyRequest->student_id)
            ->whereNull('completed_at')
            ->exists();

        abort_unless($belongsToSupervisor, 403, 'You are not authorized to manage this request.');
    }
}