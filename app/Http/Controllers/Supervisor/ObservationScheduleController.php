<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ObservationSchedule;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class ObservationScheduleController extends Controller
{
    /**
     * Display the supervisor's observations.
     */
    public function index(Request $request)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        $observations = $supervisor->observationSchedules()
            ->with(['student', 'student.currentDeployment.partnerSchool'])
            ->latest('observation_date')
            ->latest('observation_time')
            ->paginate(10);

        return view(
            'supervisor.observations.index',
            compact('observations')
        );
    }

    /**
     * Show the schedule observation form.
     */
    public function create(Request $request)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Only students assigned to this supervisor
        |--------------------------------------------------------------------------
        */

        $students = $supervisor->deployments()
            ->with(['student', 'partnerSchool'])
            ->whereNull('completed_at')
            ->get()
            ->map(function ($deployment) {
                return [
                    'id' => $deployment->student_id,
                    'deployment_id' => $deployment->id,
                    'name' => $deployment->student->full_name,
                    'student_number' => $deployment->student->student_number,
                    'school' => $deployment->partnerSchool?->school_name,
                ];
            });

        return view(
            'supervisor.observations.create',
            compact('students')
        );
    }

    /**
     * Store a new observation schedule.
     */
    public function store(Request $request)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
            ],

            'observation_date' => [
                'required',
                'date',
            ],

            'observation_time' => [
                'required',
                'date_format:H:i',
            ],

            'venue' => [
                'required',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Security check
        |--------------------------------------------------------------------------
        | Make sure the selected student is actually assigned to this
        | supervisor.
        */

        $deployment = $supervisor->deployments()
            ->where('student_id', $validated['student_id'])
            ->whereNull('completed_at')
            ->first();

        if (!$deployment) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'The selected student is not assigned to you.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate active observation
        |--------------------------------------------------------------------------
        */

        $existingObservation = $supervisor->observationSchedules()
            ->where('student_id', $validated['student_id'])
            ->whereIn('status', ['scheduled'])
            ->exists();

        if ($existingObservation) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'This student already has a scheduled observation.',
                ]);
        }

        $validated['supervisor_id'] = $supervisor->id;
        $validated['status'] = 'scheduled';

        ObservationSchedule::create($validated);

        return redirect()
            ->route('supervisor.observations.index')
            ->with(
                'success',
                'Observation scheduled successfully.'
            );
    }

    /**
     * Display a specific observation.
     */
    public function show(Request $request, ObservationSchedule $observation)
    {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        */

        if ($observation->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        $observation->load([
            'student',
            'student.currentDeployment.partnerSchool',
            'supervisor',
        ]);

        return view(
            'supervisor.observations.show',
            compact('observation')
        );
    }

    /**
     * Show the edit form.
     */
    public function edit(
        Request $request,
        ObservationSchedule $observation
    ) {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($observation->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Only allow editing scheduled observations
        |--------------------------------------------------------------------------
        */

        if ($observation->status !== 'scheduled') {
            return redirect()
                ->route('supervisor.observations.show', $observation)
                ->with(
                    'error',
                    'Only scheduled observations can be edited.'
                );
        }

        $students = $supervisor->deployments()
            ->with(['student', 'partnerSchool'])
            ->whereNull('completed_at')
            ->get()
            ->map(function ($deployment) {
                return [
                    'id' => $deployment->student_id,
                    'deployment_id' => $deployment->id,
                    'name' => $deployment->student->full_name,
                    'student_number' => $deployment->student->student_number,
                    'school' => $deployment->partnerSchool?->school_name,
                ];
            });

        return view(
            'supervisor.observations.edit',
            compact('observation', 'students')
        );
    }

    /**
     * Update an observation schedule.
     */
    public function update(
        Request $request,
        ObservationSchedule $observation
    ) {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($observation->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        if ($observation->status !== 'scheduled') {
            return redirect()
                ->route('supervisor.observations.show', $observation)
                ->with(
                    'error',
                    'Only scheduled observations can be edited.'
                );
        }

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
            ],

            'observation_date' => [
                'required',
                'date',
            ],

            'observation_time' => [
                'required',
                'date_format:H:i',
            ],

            'venue' => [
                'required',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $assigned = $supervisor->deployments()
            ->where('student_id', $validated['student_id'])
            ->whereNull('completed_at')
            ->exists();

        if (!$assigned) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'The selected student is not assigned to you.',
                ]);
        }

        $observation->update($validated);

        return redirect()
            ->route('supervisor.observations.show', $observation)
            ->with(
                'success',
                'Observation schedule updated successfully.'
            );
    }

    /**
     * Mark an observation as completed.
     */
    public function complete(
        Request $request,
        ObservationSchedule $observation
    ) {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($observation->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        if ($observation->status !== 'scheduled') {
            return back()->with(
                'error',
                'This observation is no longer scheduled.'
            );
        }

        $observation->update([
            'status' => 'completed',
        ]);

        return back()->with(
            'success',
            'Observation marked as completed.'
        );
    }

    /**
     * Cancel an observation.
     */
    public function cancel(
        Request $request,
        ObservationSchedule $observation
    ) {
        $supervisor = Supervisor::where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($observation->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        if ($observation->status !== 'scheduled') {
            return back()->with(
                'error',
                'This observation can no longer be cancelled.'
            );
        }

        $observation->update([
            'status' => 'cancelled',
        ]);

        return back()->with(
            'success',
            'Observation cancelled successfully.'
        );
    }
}