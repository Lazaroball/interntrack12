{{-- resources/views/supervisor/evaluations/edit.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Evaluation – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
      x-data="evaluationEditForm({
          areaCaps: {{ \Illuminate\Support\Js::from($areaCriterionCaps) }},
          initialScores: {{ \Illuminate\Support\Js::from([
              1 => $evaluation->criterion_1_score, 2 => $evaluation->criterion_2_score, 3 => $evaluation->criterion_3_score,
              4 => $evaluation->criterion_4_score, 5 => $evaluation->criterion_5_score, 6 => $evaluation->criterion_6_score,
              7 => $evaluation->criterion_7_score, 8 => $evaluation->criterion_8_score, 9 => $evaluation->criterion_9_score,
              10 => $evaluation->criterion_10_score, 11 => $evaluation->criterion_11_score, 12 => $evaluation->criterion_12_score,
              13 => $evaluation->criterion_13_score, 14 => $evaluation->criterion_14_score, 15 => $evaluation->criterion_15_score,
              16 => $evaluation->criterion_16_score, 17 => $evaluation->criterion_17_score, 18 => $evaluation->criterion_18_score,
              19 => $evaluation->criterion_19_score, 20 => $evaluation->criterion_20_score, 21 => $evaluation->criterion_21_score,
              22 => $evaluation->criterion_22_score, 23 => $evaluation->criterion_23_score, 24 => $evaluation->criterion_24_score,
              25 => $evaluation->criterion_25_score, 26 => $evaluation->criterion_26_score, 27 => $evaluation->criterion_27_score,
          ]) }}
      })">

    {{-- ── Breadcrumb / Back ── --}}
    <div class="mb-4">
        <a href="{{ route('supervisor.evaluations.show', $evaluation->id) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-blue-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Evaluation
        </a>
    </div>

    {{-- ── Page Header ── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor · Editing Evaluation</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Final Teaching Demonstration</h1>
            <p class="text-sm text-slate-400 mt-0.5">Teaching Skills and Competencies of a Student Teacher</p>
        </div>
        <span class="inline-flex items-center gap-1.5 self-start px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Editing a saved evaluation
        </span>
    </div>

    {{-- ── Incomplete Warning (client-side) ── --}}
    <div x-show="showIncompleteWarning" x-cloak
         class="mb-6 flex items-start gap-2.5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p><span class="font-semibold">Incomplete form.</span> Please score every criterion (all 27) before submitting.</p>
    </div>

    {{-- ── Validation Errors ── --}}
    @if ($errors->any())
        <div class="mb-6 flex items-start gap-2.5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <p class="font-semibold mb-1">Please fix the following before submitting:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('supervisor.evaluations.update', $evaluation->id) }}" @submit="onSubmit">
        @csrf
        @method('PUT')
        <input type="hidden" name="observation_schedule_id" value="{{ $evaluation->observation_schedule_id }}">
        <input type="hidden" name="student_id" value="{{ $evaluation->student_id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ══ LEFT / MAIN COLUMN ══ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ── Read-Only Context Card ── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Evaluation Details</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Date of Evaluation</label>
                            <input type="date" name="evaluation_date"
                                   value="{{ old('evaluation_date', optional($evaluation->evaluation_date)->format('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Student Teacher</label>
                            <input type="text"
                                   value="{{ $evaluation->student->full_name ?? '—' }} ({{ $evaluation->student->student_number ?? '—' }})"
                                   disabled
                                   class="w-full px-3 py-2 rounded-lg border border-slate-100 bg-slate-50 text-sm text-slate-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Observation Date</label>
                            <input type="text"
                                   value="{{ $evaluation->observationSchedule && $evaluation->observationSchedule->observation_date ? \Carbon\Carbon::parse($evaluation->observationSchedule->observation_date)->format('M d, Y') : '—' }}"
                                   disabled
                                   class="w-full px-3 py-2 rounded-lg border border-slate-100 bg-slate-50 text-sm text-slate-500">
                        </div>

                        @if (!empty($evaluation->observationSchedule->observation_time))
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Observation Time</label>
                                <input type="text" value="{{ $evaluation->observationSchedule->observation_time }}" disabled
                                       class="w-full px-3 py-2 rounded-lg border border-slate-100 bg-slate-50 text-sm text-slate-500">
                            </div>
                        @endif

                        @if (!empty($evaluation->observationSchedule->venue))
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Observation Venue</label>
                                <input type="text" value="{{ $evaluation->observationSchedule->venue }}" disabled
                                       class="w-full px-3 py-2 rounded-lg border border-slate-100 bg-slate-50 text-sm text-slate-500">
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Name of Evaluator</label>
                            <input type="text"
                                   value="{{ $evaluation->supervisor->first_name ?? auth()->user()->first_name ?? '' }} {{ $evaluation->supervisor->last_name ?? auth()->user()->last_name ?? '' }}"
                                   disabled
                                   class="w-full px-3 py-2 rounded-lg border border-slate-100 bg-slate-50 text-sm text-slate-500">
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] text-slate-400">
                        Student, supervisor, and observation schedule cannot be changed on an existing evaluation.
                    </p>
                </div>

                {{-- ── Direction ── --}}
                <div class="flex items-start gap-2.5 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <p><span class="font-semibold">Direction:</span> Kindly rate the student teacher using the given points in each area for the maximum score.</p>
                </div>

                {{-- ═══ AREA I: Communication Skills (25 points) ═══ --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800">Area I: Communication Skills</h3>
                        <span class="text-xs font-bold text-slate-400">
                            <span x-text="areaTotal(1,5)"></span> / 25 pts
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @php
                            $areaOneCriteria = [
                                1 => "Has command of the language of instruction and is able to express his/her ideas clearly.",
                                2 => "Speaks audibly in a well-modulated voice.",
                                3 => "Ability to use appropriate language for questioning.",
                                4 => "Writes legibly and structures materials.",
                                5 => "Uses words and expressions within the students' level of understanding.",
                            ];
                        @endphp
                        @foreach ($areaOneCriteria as $num => $label)
                            @include('supervisor.evaluations.partials.criterion-row', [
                                'num' => $num,
                                'label' => $label,
                                'max' => 5,
                                'value' => $evaluation->{'criterion_' . $num . '_score'},
                            ])
                        @endforeach
                    </div>
                </div>

                {{-- ═══ AREA II: Knowledge of the Subject Matter (20 points) ═══ --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800">Area II: Knowledge of the Subject Matter</h3>
                        <span class="text-xs font-bold text-slate-400">
                            <span x-text="areaTotal(6,9)"></span> / 20 pts
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @php
                            $areaTwoCriteria = [
                                6 => "Demonstrates thorough knowledge of the subject matter.",
                                7 => "Relates subject matter to other fields, current issues, and real life situations.",
                                8 => "Elaborates subject matter rather than resorting to mere textbook teaching.",
                                9 => "Provides examples that will bring the grasping of the learners.",
                            ];
                        @endphp
                        @foreach ($areaTwoCriteria as $num => $label)
                            @include('supervisor.evaluations.partials.criterion-row', [
                                'num' => $num,
                                'label' => $label,
                                'max' => 5,
                                'value' => $evaluation->{'criterion_' . $num . '_score'},
                            ])
                        @endforeach
                    </div>
                </div>

                {{-- ═══ AREA III: Teaching Methods and Classroom Management (30 points) ═══ --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800">Area III: Teaching Methods and Classroom Management</h3>
                        <span class="text-xs font-bold text-slate-400">
                            <span x-text="areaTotal(10,19)"></span> / 30 pts
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @php
                            $areaThreeCriteria = [
                                10 => "Organizes and presents subject matter clearly and systematically.",
                                11 => "Uses appropriate teaching techniques and strategies.",
                                12 => "Arouses students' interest and encourages students to think and form ideas.",
                                13 => "Encourages students' participation in class discussion.",
                                14 => "Utilizes appropriate and challenging instructional materials (e.g. to give convenience to abstract ideas).",
                                15 => "Elicits responses from students through skillful questioning.",
                                16 => "Maintains the learners' interest and attention.",
                                17 => "Maintains permissive but disciplined classroom atmosphere.",
                                18 => "Creativity in adapting methods to the learners' ability.",
                                19 => "Promotes a classroom atmosphere conducive to learning.",
                            ];
                        @endphp
                        @foreach ($areaThreeCriteria as $num => $label)
                            @include('supervisor.evaluations.partials.criterion-row', [
                                'num' => $num,
                                'label' => $label,
                                'max' => 3,
                                'value' => $evaluation->{'criterion_' . $num . '_score'},
                            ])
                        @endforeach
                    </div>
                </div>

                {{-- ═══ AREA IV: Teacher's Personality and Poise (10 points) ═══ --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800">Area IV: Teacher's Personality and Poise</h3>
                        <span class="text-xs font-bold text-slate-400">
                            <span x-text="areaTotal(20,24)"></span> / 10 pts
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @php
                            $areaFourCriteria = [
                                20 => "Shows poise and composure.",
                                21 => "Invites respect through behavior and general appearance.",
                                22 => "Is well-groomed, dresses neatly and appropriately.",
                                23 => "Presents professionally through the overall performance.",
                                24 => "Attracts attention and respect.",
                            ];
                        @endphp
                        @foreach ($areaFourCriteria as $num => $label)
                            @include('supervisor.evaluations.partials.criterion-row', [
                                'num' => $num,
                                'label' => $label,
                                'max' => 2,
                                'value' => $evaluation->{'criterion_' . $num . '_score'},
                            ])
                        @endforeach
                    </div>
                </div>

                {{-- ═══ AREA V: Lesson Planning (15 points) ═══ --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800">Area V: Lesson Planning</h3>
                        <span class="text-xs font-bold text-slate-400">
                            <span x-text="areaTotal(25,27)"></span> / 15 pts
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @php
                            $areaFiveCriteria = [
                                25 => "Lesson plan is well-prepared, achievable and attainable.",
                                26 => "The lesson plan is neat and readable.",
                                27 => "There is congruence between objectives, subject matter, teaching procedures, and methods of evaluation used.",
                            ];
                        @endphp
                        @foreach ($areaFiveCriteria as $num => $label)
                            @include('supervisor.evaluations.partials.criterion-row', [
                                'num' => $num,
                                'label' => $label,
                                'max' => 5,
                                'value' => $evaluation->{'criterion_' . $num . '_score'},
                            ])
                        @endforeach
                    </div>
                </div>

                {{-- ── Comments / Suggestions / Recommendations ── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">
                        Comments / Suggestions / Recommendations
                    </label>
                    <textarea name="comments" rows="5"
                              placeholder="Write your comments, suggestions, or recommendations here..."
                              class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">{{ old('comments', $evaluation->comments) }}</textarea>
                </div>

            </div>

            {{-- ══ RIGHT / SUMMARY COLUMN ══ --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">

                    {{-- ── Saved Total (read-only, from DB) ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-800">Supervisor Total Score</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-400">Currently saved</span>
                                <span class="text-xl font-extrabold text-blue-600">
                                    {{ $evaluation->supervisor_total_score ?? '—' }} / 100
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2">
                                This is calculated automatically on save — it will update to reflect any changes once you submit.
                            </p>
                        </div>
                    </div>

                    {{-- ── Live Preview (client-side only) ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-800">Live Preview</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">I. Communication Skills</span>
                                <span class="font-semibold text-slate-800" x-text="areaTotal(1,5) + ' / 25'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">II. Knowledge of Subject Matter</span>
                                <span class="font-semibold text-slate-800" x-text="areaTotal(6,9) + ' / 20'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">III. Teaching Methods &amp; Classroom Mgmt.</span>
                                <span class="font-semibold text-slate-800" x-text="areaTotal(10,19) + ' / 30'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">IV. Personality and Poise</span>
                                <span class="font-semibold text-slate-800" x-text="areaTotal(20,24) + ' / 10'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">V. Lesson Planning</span>
                                <span class="font-semibold text-slate-800" x-text="areaTotal(25,27) + ' / 15'"></span>
                            </div>

                            <div class="pt-3 mt-1 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-700">Preview Total</span>
                                <span class="text-lg font-extrabold text-indigo-600" x-text="grandTotal() + ' / 100'"></span>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs text-slate-400">Descriptive Equivalent</span>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold"
                                      :class="descriptiveClass()"
                                      x-text="descriptiveEquivalent()"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ── Actions ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 space-y-3">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow shadow-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            Update Evaluation
                        </button>
                        <a href="{{ route('supervisor.evaluations.show', $evaluation->id) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 border border-slate-200 hover:bg-slate-50 transition-colors">
                            Back to Evaluation
                        </a>
                        <p class="text-[11px] text-slate-400 text-center pt-1">
                            All 27 criteria are required. Scores are validated against each area's maximum on submission.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </form>
</main>

<script>
    function evaluationEditForm({ areaCaps, initialScores }) {
        return {
            scores: {},
            areaCaps: areaCaps,
            showIncompleteWarning: false,

            init() {
                for (let i = 1; i <= 27; i++) {
                    const v = initialScores[i];
                    this.scores[i] = (v === null || v === undefined) ? '' : v;
                }
            },

            areaTotal(start, end) {
                let total = 0;
                for (let i = start; i <= end; i++) {
                    total += Number(this.scores[i]) || 0;
                }
                return Math.round(total * 100) / 100;
            },

            grandTotal() {
                let total = 0;
                for (let i = 1; i <= 27; i++) {
                    total += Number(this.scores[i]) || 0;
                }
                return Math.round(total * 100) / 100;
            },

            incompleteCriteria() {
                const missing = [];
                for (let i = 1; i <= 27; i++) {
                    if (this.scores[i] === '' || this.scores[i] === null || Number.isNaN(Number(this.scores[i]))) {
                        missing.push(i);
                    }
                }
                return missing;
            },

            descriptiveEquivalent() {
                const t = this.grandTotal();
                if (t >= 96) return 'Excellent';
                if (t >= 90) return 'Very Satisfactory';
                if (t >= 82) return 'Satisfactory';
                if (t >= 75) return 'Needs Improvement';
                return 'Poor';
            },

            descriptiveClass() {
                const t = this.grandTotal();
                if (t >= 96) return 'bg-emerald-50 text-emerald-700';
                if (t >= 90) return 'bg-blue-50 text-blue-700';
                if (t >= 82) return 'bg-indigo-50 text-indigo-700';
                if (t >= 75) return 'bg-amber-50 text-amber-700';
                return 'bg-rose-50 text-rose-700';
            },

            onSubmit(e) {
                const missing = this.incompleteCriteria();
                if (missing.length > 0) {
                    e.preventDefault();
                    this.showIncompleteWarning = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    this.showIncompleteWarning = false;
                }
            }
        }
    }
</script>

</body>
</html>