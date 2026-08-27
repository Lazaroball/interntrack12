<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\PartnerSchool;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class DeploymentController extends Controller
{
    /**
     * Deployment Dashboard
     */
    public function index(Request $request)
    {
        $deploymentQuery = function () use ($request) {

            $query = Deployment::with([
                'student',
                'partnerSchool',
                'supervisor',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            if ($request->filled('search')) {

                $search = $request->search;

                $query->where(function ($q) use ($search) {

                    $q->whereHas('student', function ($student) use ($search) {

                        $student->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");

                    })->orWhereHas('partnerSchool', function ($school) use ($search) {

                        $school->where('school_name', 'like', "%{$search}%");

                    });

                });
            }

            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            if ($request->filled('partner_school_id')) {
                $query->where('partner_school_id', $request->partner_school_id);
            }

            if ($request->filled('school_year')) {
                $query->where('school_year', $request->school_year);
            }

            if ($request->filled('program')) {
                $query->where('program', $request->program);
            }

            return $query;
        };

        /*
        |--------------------------------------------------------------------------
        | Waiting For Approval
        |--------------------------------------------------------------------------
        */

        $waitingDeployments = $deploymentQuery()
            ->waiting()
            ->latest()
            ->paginate(10, ['*'], 'waiting_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Current Deployments
        |--------------------------------------------------------------------------
        */

        $currentDeployments = $deploymentQuery()
            ->deployed()
            ->whereNull('completed_at')
            ->latest('deployment_date')
            ->paginate(10, ['*'], 'current_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Completed Deployments
        |--------------------------------------------------------------------------
        */

        $completedDeployments = $deploymentQuery()
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->paginate(10, ['*'], 'completed_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $schoolYears = Deployment::select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        $programs = [
            'Field Study',
            'Internship',
        ];

        return view('coordinator.deployments.index', compact(
            'waitingDeployments',
            'currentDeployments',
            'completedDeployments',
            'partnerSchools',
            'supervisors',
            'schoolYears',
            'programs'
        ));
    }

    /**
     * Show Manual Deployment Form
     */
    public function create(Request $request)
    {
        // Fetch selected student if passed via query parameter (e.g. ?student_id=1)
        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        // Get students who don't have an active deployment record
        $students = Student::whereDoesntHave('deployment')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $programs = [
            'Field Study',
            'Internship',
        ];

        return view('coordinator.deployments.create', compact(
            'students',
            'selectedStudent',
            'partnerSchools',
            'supervisors',
            'programs'
        ));
    }

    /**
     * Store a Manually Deployed Student
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'student_id' => [
            'required',
            'exists:students,id',
            'unique:deployments,student_id',
        ],
        'partner_school_id' => [
            'required',
            'exists:partner_schools,id',
        ],
        'supervisor_id' => [
            'nullable',
            'exists:supervisors,id',
        ],
        'program' => [
            'required',
            'in:Field Study,Internship',
        ],
        'school_year' => [
            'required',
            'string',
            'max:20',
        ],
        'semester' => [
            'required',
            'string',
            'in:1st Semester,2nd Semester,Summer', // Adjust options to match your school structure
        ],
        'remarks' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ]);

    $deployment = Deployment::create([
        'student_id'        => $validated['student_id'],
        'partner_school_id' => $validated['partner_school_id'],
        'supervisor_id'     => $validated['supervisor_id'] ?? null,
        'program'           => $validated['program'],
        'school_year'       => $validated['school_year'],
        'semester'          => $validated['semester'],
        'status'            => 'deployed', // Set default status if needed by your model scopes
        'remarks'           => $validated['remarks'] ?? null,
        'coordinator_id'    => optional(auth()->user()->coordinator)->id,
        'deployment_date'   => now(),
    ]);

    return redirect()
        ->route('coordinator.deployments.show', $deployment->id)
        ->with('success', 'Student manually deployed successfully.');
}

    /**
     * View Deployment
     */
    public function show(Deployment $deployment)
    {
        $deployment->load([
            'student',
            'partnerSchool',
            'supervisor',
        ]);

        return view('coordinator.deployments.show', compact('deployment'));
    }

    /**
     * Edit Deployment
     */
    public function edit(Deployment $deployment)
    {
        $deployment->load([
            'student',
            'partnerSchool',
            'supervisor',
        ]);

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('coordinator.deployments.edit', compact(
            'deployment',
            'partnerSchools',
            'supervisors'
        ));
    }

    /**
     * Update Deployment
     */
    public function update(Request $request, Deployment $deployment)
    {
        $validated = $request->validate([

            'partner_school_id' => [
                'required',
                'exists:partner_schools,id',
            ],

            'supervisor_id' => [
                'nullable',
                'exists:supervisors,id',
            ],

            'program' => [
                'required',
                'in:Field Study,Internship',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);

        $deployment->update([

            'partner_school_id' => $validated['partner_school_id'],
            'supervisor_id'     => $validated['supervisor_id'],
            'program'           => $validated['program'],
            'remarks'           => $validated['remarks'] ?? null,

            // Coordinator approving this deployment
            'coordinator_id' => optional(auth()->user()->coordinator)->id,

            // Only set once
            'deployment_date' => $deployment->deployment_date ?? now(),

        ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Deployment updated successfully.');
    }

    /**
     * Approve selected deployments.
     */
    public function approveSelected(Request $request)
    {
        $validated = $request->validate([
            'deployment_ids'   => ['required', 'array', 'min:1'],
            'deployment_ids.*' => ['exists:deployments,id'],
            'supervisor_id'    => ['required', 'exists:supervisors,id'],
        ]);

        Deployment::whereIn('id', $validated['deployment_ids'])
            ->waiting()
            ->update([
                'supervisor_id'   => $validated['supervisor_id'],
                'coordinator_id'  => optional(auth()->user()->coordinator)->id,
                'deployment_date' => now(),
            ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Selected deployments approved successfully.');
    }

    /**
     * Approve all waiting deployments (uses current filters).
     */
    public function approveAll(Request $request)
    {
        $validated = $request->validate([
            'supervisor_id' => ['required', 'exists:supervisors,id'],
        ]);

        $query = Deployment::waiting();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('student', function ($student) use ($search) {

                    $student->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");

                })->orWhereHas('partnerSchool', function ($school) use ($search) {

                    $school->where('school_name', 'like', "%{$search}%");

                });

            });
        }

        if ($request->filled('partner_school_id')) {
            $query->where('partner_school_id', $request->partner_school_id);
        }

        if ($request->filled('school_year')) {
            $query->where('school_year', $request->school_year);
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        $query->update([
            'supervisor_id'   => $validated['supervisor_id'],
            'coordinator_id'  => optional(auth()->user()->coordinator)->id,
            'deployment_date' => now(),
        ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'All waiting deployments approved successfully.');
    }

    /**
     * Mark deployment as completed.
     */
    public function complete(Deployment $deployment)
    {
        $deployment->update([
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Deployment marked as completed.');
    }

    /**
     * Delete deployment.
     */
    public function destroy(Deployment $deployment)
    {
        $deployment->delete();

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Deployment deleted successfully.');
    }
}