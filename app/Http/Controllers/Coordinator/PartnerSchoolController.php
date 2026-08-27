<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\PartnerSchool;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PartnerSchoolController extends Controller
{
    /**
     * Display a listing of partner schools.
     */
    public function index(Request $request)
    {
        // Include both pending and deployed statuses
        // to reserve slots for student applications.
        $query = PartnerSchool::withCount([
            'deployments as occupied_slots' => function ($q) {
                $q->whereIn('status', ['pending', 'deployed']);
            }
        ]);

        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'alphabetical':
                $query->orderBy('school_name', 'asc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $partnerSchools = $query->get();

        $totalPartnerSchools = $partnerSchools->count();

        $fullSlotsCount = $partnerSchools
            ->filter(function ($school) {
                return $school->isFull();
            })
            ->count();

        return view('coordinator.partner-schools.index', compact(
            'partnerSchools',
            'totalPartnerSchools',
            'fullSlotsCount'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('coordinator.partner-schools.create');
    }

    /**
     * Store partner school.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name' => [
                'required',
                'string',
                'max:255',
            ],

            'school_type' => [
                'required',
                'string',
                'max:255',
            ],

            'other_school_type' => [
                'nullable',
                'required_if:school_type,Other',
                'string',
                'max:255',
            ],

            'address' => [
                'required',
                'string',
                'max:500',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:partner_schools,email',
            ],

            'available_slots' => [
                'required',
                'integer',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Geolocation
            |--------------------------------------------------------------------------
            */

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'radius_meters' => [
                'required',
                'integer',
                'min:10',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | MOA
            |--------------------------------------------------------------------------
            */

            'moa_started_at' => [
                'nullable',
                'date',
            ],

            'moa_expires_at' => [
                'nullable',
                'date',
                'after_or_equal:moa_started_at',
            ],

            'moa_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | School Type
        |--------------------------------------------------------------------------
        */

        if ($validated['school_type'] === 'Other') {
            $validated['school_type'] = $validated['other_school_type'];
        }

        unset($validated['other_school_type']);

        /*
        |--------------------------------------------------------------------------
        | Contact Defaults
        |--------------------------------------------------------------------------
        */

        $validated['contact_person'] = $validated['contact_person'] ?? '';
        $validated['contact_number'] = $validated['contact_number'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | MOA Dates
        |--------------------------------------------------------------------------
        |
        | The coordinator enters the actual dates stated on the MOA.
        | The system does not automatically calculate the expiration date.
        |
        */

        if (!empty($validated['moa_started_at'])) {
            $validated['moa_started_at'] = Carbon::parse(
                $validated['moa_started_at']
            )->toDateString();

            $validated['moa_expires_at'] = !empty($validated['moa_expires_at'])
                ? Carbon::parse($validated['moa_expires_at'])->toDateString()
                : null;

            $validated['moa_status'] = 'active';
        } else {
            $validated['moa_started_at'] = null;
            $validated['moa_expires_at'] = null;
            $validated['moa_status'] = 'inactive';
        }

        /*
        |--------------------------------------------------------------------------
        | Upload MOA PDF
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('moa_file')) {
            $validated['moa_file'] = $request
                ->file('moa_file')
                ->store('moa_files', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Partner School
        |--------------------------------------------------------------------------
        */

        $school = PartnerSchool::create($validated);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        ActivityLogger::log(
            'Created',
            'Partner Schools',
            'Registered partner school: ' . $school->school_name
        );

        return redirect()
            ->route('coordinator.partner-schools.index')
            ->with(
                'success',
                'Partner school registered successfully.'
            );
    }

    /**
     * Show edit form.
     */
    public function edit(PartnerSchool $partnerSchool)
    {
        return view(
            'coordinator.partner-schools.edit',
            compact('partnerSchool')
        );
    }

    /**
     * Update partner school.
     */
    public function update(
        Request $request,
        PartnerSchool $partnerSchool
    ) {
        $validated = $request->validate([
            'school_name' => [
                'required',
                'string',
                'max:255',
            ],

            'school_type' => [
                'required',
                'string',
                'max:255',
            ],

            'other_school_type' => [
                'nullable',
                'required_if:school_type,Other',
                'string',
                'max:255',
            ],

            'address' => [
                'required',
                'string',
                'max:500',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:partner_schools,email,' . $partnerSchool->id,
            ],

            'available_slots' => [
                'required',
                'integer',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Geolocation
            |--------------------------------------------------------------------------
            */

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'radius_meters' => [
                'required',
                'integer',
                'min:10',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | MOA
            |--------------------------------------------------------------------------
            */

            'moa_started_at' => [
                'nullable',
                'date',
            ],

            'moa_expires_at' => [
                'nullable',
                'date',
                'after_or_equal:moa_started_at',
            ],

            'moa_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | School Type
        |--------------------------------------------------------------------------
        */

        if ($validated['school_type'] === 'Other') {
            $validated['school_type'] = $validated['other_school_type'];
        }

        unset($validated['other_school_type']);

        /*
        |--------------------------------------------------------------------------
        | Contact Defaults
        |--------------------------------------------------------------------------
        */

        $validated['contact_person'] = $validated['contact_person'] ?? '';
        $validated['contact_number'] = $validated['contact_number'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | MOA Dates
        |--------------------------------------------------------------------------
        |
        | The coordinator enters the actual dates stated on the MOA.
        | The system does not automatically calculate the expiration date.
        |
        */

        if (!empty($validated['moa_started_at'])) {
            $validated['moa_started_at'] = Carbon::parse(
                $validated['moa_started_at']
            )->toDateString();

            $validated['moa_expires_at'] = !empty($validated['moa_expires_at'])
                ? Carbon::parse($validated['moa_expires_at'])->toDateString()
                : null;

            $validated['moa_status'] = 'active';
        } else {
            $validated['moa_started_at'] = null;
            $validated['moa_expires_at'] = null;
            $validated['moa_status'] = 'inactive';
        }

        /*
        |--------------------------------------------------------------------------
        | Upload New MOA
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('moa_file')) {
            $validated['moa_file'] = $request
                ->file('moa_file')
                ->store('moa_files', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update Partner School
        |--------------------------------------------------------------------------
        */

        $partnerSchool->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        ActivityLogger::log(
            'Updated',
            'Partner Schools',
            'Updated partner school: ' . $partnerSchool->school_name
        );

        return redirect()
            ->route('coordinator.partner-schools.index')
            ->with(
                'success',
                'Partner school updated successfully.'
            );
    }

    /**
     * Delete partner school.
     */
    public function destroy(PartnerSchool $partnerSchool)
    {
        ActivityLogger::log(
            'Deleted',
            'Partner Schools',
            'Deleted partner school: ' . $partnerSchool->school_name
        );

        $partnerSchool->delete();

        return redirect()
            ->route('coordinator.partner-schools.index')
            ->with(
                'success',
                'Partner school deleted successfully.'
            );
    }
}