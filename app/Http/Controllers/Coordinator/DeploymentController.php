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
        $deploymentQuery = fn () => $this->applyFilters(
            Deployment::with(['student', 'partnerSchool', 'supervisor']),
            $request
        );

        $waitingDeployments = $deploymentQuery()
            ->waiting()
            ->latest()
            ->paginate(10, ['*'], 'waiting_page')
            ->withQueryString();

        $currentDeployments = $deploymentQuery()
            ->deployed()
            ->whereNull('completed_at')
            ->latest('deployment_date')
            ->paginate(10, ['*'], 'current_page')
            ->withQueryString();

        $completedDeployments = $deploymentQuery()
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->paginate(10, ['*'], 'completed_page')
            ->withQueryString();

        $cancelledDeployments = $deploymentQuery()
            ->where('status', 'cancelled')
            ->latest('updated_at')
            ->paginate(10, ['*'], 'cancelled_page')
            ->withQueryString();

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $schoolYears = Deployment::select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        // Deployment TYPE
        $programs = ['Field Study', 'Internship'];

        // Student PROGRAM and BLOCK
        [$studentPrograms, $blocks] = $this->studentFilterOptions();

        return view('coordinator.deployments.index', compact(
            'waitingDeployments',
            'currentDeployments',
            'completedDeployments',
            'cancelledDeployments',
            'partnerSchools',
            'supervisors',
            'schoolYears',
            'programs',
            'studentPrograms',
            'blocks'
        ));
    }

    /**
     * Show Manual Deployment Form.
     *
     * Only students who are eligible for a deployment are listed:
     *  - Field Study: accepted, not yet completed
     *  - Internship:  initial requirements accepted, not yet completed
     * ...and who don't already have an open deployment.
     */
    public function create(Request $request)
    {
        $selectedStudent = null;

        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        $students = Student::query()
            ->whereDoesntHave('deployments', fn ($q) => $q->open())
            ->where(function ($q) {
                $q->where(fn ($fs) => $fs
                        ->where('field_study_status', 'accepted')
                        ->whereNull('field_study_completed_at')
                        ->where('internship_status', 'locked'))
                  ->orWhere(fn ($in) => $in
                        ->where('internship_status', 'accepted')
                        ->whereNull('internship_completed_at'));
            })
            ->when(
                $request->filled('student_program'),
                fn ($q) => $q->where('program', $request->student_program)
            )
            ->when(
                $request->filled('block'),
                function ($q) use ($request) {
                    $request->block === 'none'
                        ? $q->where(fn ($b) => $b->whereNull('block')->orWhere('block', ''))
                        : $q->where('block', $request->block);
                }
            )
            ->orderBy('program')
            ->orderBy('block')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Which deployment type each listed student is eligible for.
        $eligibleProgram = $students->mapWithKeys(fn ($s) => [
            $s->id => $s->internship_status === 'accepted' ? 'Internship' : 'Field Study',
        ]);

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $programs = ['Field Study', 'Internship'];

        [$studentPrograms, $blocks] = $this->studentFilterOptions();

        return view('coordinator.deployments.create', compact(
            'students',
            'selectedStudent',
            'eligibleProgram',
            'partnerSchools',
            'supervisors',
            'programs',
            'studentPrograms',
            'blocks'
        ));
    }

    /**
     * Store a Manually Deployed Student.
     *
     * A student may have one Field Study AND one Internship deployment.
     * Duplicates of the same program are blocked by eligibilityError().
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'        => ['required', 'exists:students,id'],
            'partner_school_id' => ['required', 'exists:partner_schools,id'],
            'supervisor_id'     => ['nullable', 'exists:supervisors,id'],
            'program'           => ['required', 'in:Field Study,Internship'],
            'school_year'       => ['required', 'string', 'max:20'],
            'semester'          => ['required', 'string', 'in:1st Semester,2nd Semester,Summer'],
            'remarks'           => ['nullable', 'string', 'max:1000'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($error = $this->eligibilityError($student, $validated['program'])) {
            return back()->withInput()->withErrors(['student_id' => $error]);
        }

        $supervisorId = $validated['supervisor_id'] ?? null;

        $deployment = Deployment::create([
            'student_id'        => $student->id,
            'partner_school_id' => $validated['partner_school_id'],
            'supervisor_id'     => $supervisorId,
            'program'           => $validated['program'],
            'school_year'       => $validated['school_year'],
            'semester'          => $validated['semester'],
            'status'            => $supervisorId ? 'deployed' : 'pending',
            'remarks'           => $validated['remarks'] ?? null,
            'coordinator_id'    => optional(auth()->user()->coordinator)->id,
            'deployment_date'   => $supervisorId ? now() : null,
        ]);

        return redirect()
            ->route('coordinator.deployments.show', $deployment->id)
            ->with('success', 'Student manually deployed successfully.');
    }

    public function show(Deployment $deployment)
    {
        $deployment->load(['student', 'partnerSchool', 'supervisor']);

        return view('coordinator.deployments.show', compact('deployment'));
    }

    public function edit(Deployment $deployment)
    {
        $deployment->load(['student', 'partnerSchool', 'supervisor']);

        $partnerSchools = PartnerSchool::orderBy('school_name')->get();

        $supervisors = Supervisor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'coordinator.deployments.edit',
            compact('deployment', 'partnerSchools', 'supervisors')
        );
    }

    /**
     * Update Deployment.
     */
    public function update(Request $request, Deployment $deployment)
    {
        $validated = $request->validate([
            'partner_school_id' => ['required', 'exists:partner_schools,id'],
            'supervisor_id'     => ['nullable', 'exists:supervisors,id'],
            'program'           => ['required', 'in:Field Study,Internship'],
            'remarks'           => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['program'] !== $deployment->program) {
            return back()->withInput()->withErrors([
                'program' => 'The deployment type cannot be changed. Cancel this deployment and create a new one instead.',
            ]);
        }

        abort_if(
            $deployment->completed_at || $deployment->status === 'cancelled',
            422,
            'A completed or cancelled deployment cannot be edited.'
        );

        $supervisorId = $validated['supervisor_id'] ?? null;

        $deployment->update([
            'partner_school_id' => $validated['partner_school_id'],
            'supervisor_id'     => $supervisorId,
            'remarks'           => $validated['remarks'] ?? null,
            'coordinator_id'    => optional(auth()->user()->coordinator)->id,
            'deployment_date'   => $deployment->deployment_date
                ?? ($supervisorId ? now() : null),
            'status'            => $supervisorId ? 'deployed' : 'pending',
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
                'status'          => 'deployed',
            ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Selected deployments approved successfully.');
    }

    /**
     * Approve all waiting deployments.
     */
    public function approveAll(Request $request)
    {
        $validated = $request->validate([
            'supervisor_id' => ['required', 'exists:supervisors,id'],
        ]);

        $this->applyFilters(Deployment::waiting(), $request)->update([
            'supervisor_id'   => $validated['supervisor_id'],
            'coordinator_id'  => optional(auth()->user()->coordinator)->id,
            'deployment_date' => now(),
            'status'          => 'deployed',
        ]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'All waiting deployments approved successfully.');
    }

    /**
     * Reassign supervisor for several already-deployed students.
     */
    public function bulkUpdateSupervisor(Request $request)
    {
        $validated = $request->validate([
            'deployment_ids'   => ['required', 'array', 'min:1'],
            'deployment_ids.*' => ['exists:deployments,id'],
            'supervisor_id'    => ['required', 'exists:supervisors,id'],
        ]);

        Deployment::whereIn('id', $validated['deployment_ids'])
            ->deployed()
            ->whereNull('completed_at')
            ->update(['supervisor_id' => $validated['supervisor_id']]);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Supervisor updated for the selected students.');
    }

    /**
     * Assign / change the supervisor of ONE deployment.
     * (Route: deployments.update-supervisor)
     */
    public function updateSupervisor(Request $request, Deployment $deployment)
    {
        $validated = $request->validate([
            'supervisor_id' => ['required', 'exists:supervisors,id'],
        ]);

        if ($deployment->completed_at || $deployment->status === 'cancelled') {
            return back()->with('error', 'A completed or cancelled deployment cannot be changed.');
        }

        $deployment->update([
            'supervisor_id'   => $validated['supervisor_id'],
            'coordinator_id'  => optional(auth()->user()->coordinator)->id,
            'deployment_date' => $deployment->deployment_date ?? now(),
            'status'          => 'deployed',
        ]);

        return back()->with('success', 'Supervisor updated.');
    }

    /**
     * Cancel a deployment so the student can be deployed again.
     * (Route: deployments.cancel)
     */
    public function cancel(Deployment $deployment)
    {
        if ($deployment->completed_at) {
            return back()->with('error', 'A completed deployment cannot be cancelled.');
        }

        if ($deployment->status === 'cancelled') {
            return back()->with('error', 'This deployment is already cancelled.');
        }

        $deployment->update(['status' => 'cancelled']);

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Deployment cancelled.');
    }

    public function destroy(Deployment $deployment)
    {
        $deployment->delete();

        return redirect()
            ->route('coordinator.deployments.index')
            ->with('success', 'Deployment deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Why (if at all) can this student NOT get a deployment of this type?
     * Returns null when allowed.
     */
    private function eligibilityError(Student $student, string $program): ?string
    {
        if ($program === 'Field Study') {
            if ($student->field_study_completed_at) {
                return 'This student has already completed Field Study.';
            }

            if ($student->field_study_status !== 'accepted') {
                return 'This student has not been accepted for Field Study yet.';
            }
        } else {
            if ($student->internship_completed_at) {
                return 'This student has already completed the Internship.';
            }

            if ($student->internship_status !== 'accepted') {
                return "This student's initial Internship requirements have not been accepted yet.";
            }
        }

        $hasOpen = $student->deployments()
            ->where('program', $program)
            ->open()
            ->exists();

        if ($hasOpen) {
            return "This student already has an open {$program} deployment.";
        }

        return null;
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($student) use ($search) {
                    $student
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })
                ->orWhereHas('partnerSchool', function ($school) use ($search) {
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
            $query->program($request->program);
        }

        if ($request->filled('student_program')) {
            $query->studentProgram($request->student_program);
        }

        if ($request->filled('block')) {
            $query->inBlock($request->block);
        }

        return $query;
    }

    private function studentFilterOptions(): array
    {
        $studentPrograms = Student::whereNotNull('program')
            ->where('program', '!=', '')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        $blocks = Student::whereNotNull('block')
            ->where('block', '!=', '')
            ->distinct()
            ->orderBy('block')
            ->pluck('block');

        return [$studentPrograms, $blocks];
    }
}