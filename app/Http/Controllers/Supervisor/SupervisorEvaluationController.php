<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\ObservationSchedule;
use App\Models\Deployment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorEvaluationController extends Controller
{
    /**
     * Per-area criterion rang  es and max score per criterion,
     * matching the official Supervisor Evaluation Form.
     *
     * Area I   (25 pts / 5 criteria)  -> 5 pts each
     * Area II  (20 pts / 4 criteria)  -> 5 pts each
     * Area III (30 pts / 10 criteria) -> 3 pts each
     * Area IV  (10 pts / 5 criteria)  -> 2 pts each
     * Area V   (15 pts / 3 criteria)  -> 5 pts each
     */
    protected array $areaCriterionCaps = [
        ['start' => 1,  'end' => 5,  'max' => 5],
        ['start' => 6,  'end' => 9,  'max' => 5],
        ['start' => 10, 'end' => 19, 'max' => 3],
        ['start' => 20, 'end' => 24, 'max' => 2],
        ['start' => 25, 'end' => 27, 'max' => 5],
    ];

    /**
     * Resolve the currently authenticated Supervisor.
     * Supervisor::user_id -> users.id
     */
   protected function currentSupervisor(): Supervisor
{
    $supervisor = Supervisor::where('user_id', Auth::id())->first();

    abort_unless($supervisor !== null, 403, 'No supervisor profile found for this account.');

    return $supervisor;
}
    /**
     * Confirm the given student is assigned to the supervisor
     * through an active Deployment record.
     */
    protected function assertStudentAssignedToSupervisor(Supervisor $supervisor, int $studentId): void
    {
        $isAssigned = Deployment::where('supervisor_id', $supervisor->id)
            ->where('student_id', $studentId)
            ->exists();

        abort_unless($isAssigned, 403, 'This student is not assigned to you.');
    }

    /**
     * Boolean version of the eligibility check, reused by both
     * assertObservationScheduleIsEvaluable() (which aborts) and
     * index() (which just needs a true/false to decide what to show).
     */
    protected function isObservationScheduleEvaluable(
        ObservationSchedule $observationSchedule,
        Supervisor $supervisor,
        int $studentId
    ): bool {
        return $observationSchedule->supervisor_id === $supervisor->id
            && $observationSchedule->student_id === $studentId
            && $observationSchedule->status === 'completed';
    }

    /**
     * Confirm the observation schedule:
     * - belongs to this student and this supervisor
     * - has status 'completed'
     */
    protected function assertObservationScheduleIsEvaluable(
        ObservationSchedule $observationSchedule,
        Supervisor $supervisor,
        int $studentId
    ): void {
        abort_unless(
            $observationSchedule->supervisor_id === $supervisor->id
                && $observationSchedule->student_id === $studentId,
            403,
            'This observation schedule does not belong to you and this student.'
        );

        abort_unless(
            $observationSchedule->status === 'completed',
            422,
            'This observation is not marked as completed yet. Evaluations can only be submitted for completed observations.'
        );
    }

    /**
     * Build validation rules for the 27 criterion scores,
     * honoring each area's maximum.
     */
    protected function criterionValidationRules(): array
    {
        $rules = [];

        foreach ($this->areaCriterionCaps as $area) {
            for ($i = $area['start']; $i <= $area['end']; $i++) {
                $rules["criterion_{$i}_score"] = [
                    'required',
                    'numeric',
                    'min:0',
                    'max:' . $area['max'],
                ];
            }
        }

        return $rules;
    }

    /**
     * Shared validation rules for create/update.
     */
    protected function baseValidationRules(): array
    {
        return array_merge([
            'observation_schedule_id' => ['required', 'exists:observation_schedules,id'],
            'student_id'              => ['required', 'exists:students,id'],
            'evaluation_date'         => ['required', 'date'],
            'rating'                  => ['nullable', 'numeric', 'min:0'],
            'comments'                => ['nullable', 'string'],
            'overall_score'           => ['nullable', 'numeric', 'min:0'],
        ], $this->criterionValidationRules());
    }

    /**
     * Sum validated criterion scores into the 5 official areas
     * and return the supervisor total (max 100), via the model's
     * own calculation so this never drifts from Evaluation::calculateSupervisorTotal().
     */
    protected function computeSupervisorTotal(array $validated): float
    {
        $evaluation = new Evaluation();

        foreach ($this->areaCriterionCaps as $area) {
            for ($i = $area['start']; $i <= $area['end']; $i++) {
                $key = "criterion_{$i}_score";
                $evaluation->{$key} = $validated[$key];
            }
        }

        return $evaluation->calculateSupervisorTotal();
    }

    /**
     * List students assigned to the current supervisor (via Deployment),
     * their most recent observation schedule with this supervisor, and
     * the evaluation status for that schedule (if any).
     *
     * GET /supervisor/evaluations
     * Route name: supervisor.evaluations.index
     */
    public function index()
{
    $supervisor = $this->currentSupervisor();

    $deployments = Deployment::where('supervisor_id', $supervisor->id)
        ->with(['student', 'partnerSchool'])
        ->get();

    $rows = $deployments->map(function ($deployment) use ($supervisor) {
        $student = $deployment->student;

        $observationSchedule = ObservationSchedule::where('student_id', $student->id)
            ->where('supervisor_id', $supervisor->id)
            ->orderByDesc('observation_date')
            ->first();

        $evaluation = null;
        $isEvaluable = false;

        if ($observationSchedule) {
            $evaluation = Evaluation::where('observation_schedule_id', $observationSchedule->id)
                ->where('student_id', $student->id)
                ->where('supervisor_id', $supervisor->id)
                ->first();

            $isEvaluable = $this->isObservationScheduleEvaluable(
                $observationSchedule,
                $supervisor,
                $student->id
            );
        }

        return [
            'student'             => $student,
            'deployment'          => $deployment,
            'observationSchedule' => $observationSchedule,
            'evaluation'          => $evaluation,
            'isEvaluable'         => $isEvaluable,
        ];
    });

    return view('supervisor.evaluations.index', [
        'rows' => $rows,
    ]);
}

    /**
     * Display the form for creating a new evaluation.
     *
     * GET /supervisor/observation-schedules/{observationSchedule}/students/{student}/evaluate
     * Route name: supervisor.evaluations.create
     */
    public function create(ObservationSchedule $observationSchedule, Student $student)
    {
        $supervisor = $this->currentSupervisor();

        $this->assertStudentAssignedToSupervisor($supervisor, $student->id);
        $this->assertObservationScheduleIsEvaluable($observationSchedule, $supervisor, $student->id);

        $existing = Evaluation::where('observation_schedule_id', $observationSchedule->id)
            ->where('student_id', $student->id)
            ->where('supervisor_id', $supervisor->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('supervisor.evaluations.edit', $existing->id)
                ->with('info', 'An evaluation for this observation already exists. You can edit it here.');
        }

        return view('supervisor.evaluations.create', [
            'observationSchedule' => $observationSchedule,
            'student'             => $student,
            'supervisor'          => $supervisor,
            'areaCriterionCaps'   => $this->areaCriterionCaps,
        ]);
    }

    /**
     * Store a newly created evaluation.
     *
     * POST /supervisor/evaluations
     * Route name: supervisor.evaluations.store
     */
    public function store(Request $request)
    {
        $supervisor = $this->currentSupervisor();

        $validated = $request->validate($this->baseValidationRules());

        $this->assertStudentAssignedToSupervisor($supervisor, $validated['student_id']);

        $observationSchedule = ObservationSchedule::findOrFail($validated['observation_schedule_id']);

        $this->assertObservationScheduleIsEvaluable(
            $observationSchedule,
            $supervisor,
            $validated['student_id']
        );

        $duplicate = Evaluation::where('observation_schedule_id', $validated['observation_schedule_id'])
            ->where('student_id', $validated['student_id'])
            ->where('supervisor_id', $supervisor->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['observation_schedule_id' => 'An evaluation for this student and observation schedule already exists. Please edit the existing evaluation instead.']);
        }

        $supervisorTotal = $this->computeSupervisorTotal($validated);

        $evaluation = DB::transaction(function () use ($validated, $supervisor, $supervisorTotal) {
    return Evaluation::create(array_merge($validated, [
        'supervisor_id'          => $supervisor->id,
        'supervisor_total_score' => $supervisorTotal,
        'rating'                 => $validated['rating'] ?? null,
        'overall_score'          => $validated['overall_score'] ?? null,
    ]));
});
        return redirect()
            ->route('supervisor.evaluations.show', $evaluation->id)
            ->with('success', 'Evaluation submitted successfully.');
    }

    /**
     * View an existing evaluation.
     *
     * GET /supervisor/evaluations/{evaluation}
     * Route name: supervisor.evaluations.show
     */
   public function show(Evaluation $evaluation)
{
    $supervisor = $this->currentSupervisor();

    abort_unless($evaluation->supervisor_id === $supervisor->id, 403);

    return view('supervisor.evaluations.show', [
        'evaluation'        => $evaluation->load(['student', 'supervisor', 'observationSchedule', 'otherEvaluatorResults']),
        'areaCriterionCaps' => $this->areaCriterionCaps,
    ]);
}

    /**
     * Display the form for editing an existing evaluation.
     *
     * GET /supervisor/evaluations/{evaluation}/edit
     * Route name: supervisor.evaluations.edit
     */
    public function edit(Evaluation $evaluation)
    {
        $supervisor = $this->currentSupervisor();

        abort_unless($evaluation->supervisor_id === $supervisor->id, 403);

        return view('supervisor.evaluations.edit', [
            'evaluation'        => $evaluation->load(['student', 'observationSchedule']),
            'supervisor'        => $supervisor,
            'areaCriterionCaps' => $this->areaCriterionCaps,
        ]);
    }

    /**
     * Update an existing evaluation.
     *
     * PUT/PATCH /supervisor/evaluations/{evaluation}
     * Route name: supervisor.evaluations.update
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $supervisor = $this->currentSupervisor();

        abort_unless($evaluation->supervisor_id === $supervisor->id, 403);

        $validated = $request->validate($this->baseValidationRules());

        $this->assertStudentAssignedToSupervisor($supervisor, $validated['student_id']);

        $observationSchedule = ObservationSchedule::findOrFail($validated['observation_schedule_id']);

        $this->assertObservationScheduleIsEvaluable(
            $observationSchedule,
            $supervisor,
            $validated['student_id']
        );

        $conflict = Evaluation::where('observation_schedule_id', $validated['observation_schedule_id'])
            ->where('student_id', $validated['student_id'])
            ->where('supervisor_id', $supervisor->id)
            ->where('id', '!=', $evaluation->id)
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['observation_schedule_id' => 'Another evaluation already exists for this student and observation schedule.']);
        }

        $supervisorTotal = $this->computeSupervisorTotal($validated);

        DB::transaction(function () use ($evaluation, $validated, $supervisorTotal) {
            $evaluation->update(array_merge($validated, [
                'supervisor_total_score' => $supervisorTotal,
            ]));
        });

        return redirect()
            ->route('supervisor.evaluations.show', $evaluation->id)
            ->with('success', 'Evaluation updated successfully.');
    }
}

