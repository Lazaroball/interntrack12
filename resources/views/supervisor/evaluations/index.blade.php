{{-- resources/views/supervisor/evaluations/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Supervisor Evaluations – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Supervisor Evaluations</h1>
            <p class="text-sm text-slate-400 mt-0.5">Evaluate students assigned to you once their observation is completed</p>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            {{ session('info') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Evaluations Table ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

        @if ($rows->isNotEmpty())
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <p class="text-xs text-slate-400">
                    <span class="font-semibold text-slate-700">{{ $rows->count() }}</span>
                    student{{ $rows->count() === 1 ? '' : 's' }} assigned to you
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Program</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Partner School</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Observation Date</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Observation Status</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Evaluation Status</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Supervisor Total</th>
                            <th class="text-right px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($rows as $row)
                           @php
                            $student = $row['student'];
                            $deployment = $row['deployment'];
                            $observationSchedule = $row['observationSchedule'];
                            $evaluation = $row['evaluation'];
                            $isEvaluable = $row['isEvaluable'];
                        @endphp
                            
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100">

                                {{-- Student --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                    text-blue-700 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                        </div>
                                        <div class="leading-tight">
                                            <p class="font-semibold text-slate-800 whitespace-nowrap">
                                                {{ $student->full_name }}
                                            </p>
                                            <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                                {{ $student->student_number }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                               {{-- Program --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->program ?? '—' }}
                                </td>

                                {{-- Partner School --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->partnerSchool->school_name ?? '—' }}
                                </td>

                                {{-- Observation Date --}}
                                <td class="px-4 py-4 whitespace-nowrap text-slate-700 font-medium">
                                    {{ $observationSchedule ? \Carbon\Carbon::parse($observationSchedule->observation_date)->format('M d, Y') : '—' }}
                                </td>

                                {{-- Observation Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if (! $observationSchedule)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Not Scheduled
                                        </span>
                                    @elseif ($observationSchedule->status === 'completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold capitalize">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ str_replace('_', ' ', $observationSchedule->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Evaluation Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($evaluation)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Evaluated
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending Evaluation
                                        </span>
                                    @endif
                                </td>

                                {{-- Supervisor Total --}}
                                <td class="px-4 py-4 whitespace-nowrap text-slate-700 font-semibold">
                                    {{ $evaluation->supervisor_total_score ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if (! $evaluation)
                                            @if ($isEvaluable)
                                                <a href="{{ route('supervisor.evaluations.create', [
                                                        'observationSchedule' => $observationSchedule->id,
                                                        'student' => $student->id,
                                                    ]) }}"
                                                   class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                          bg-blue-50 text-blue-700 border border-blue-200/60
                                                          hover:bg-blue-600 hover:text-white hover:border-blue-600
                                                          transition-all duration-150 shadow-sm shadow-blue-100/50">
                                                    Start Evaluation
                                                </a>
                                            @elseif (! $observationSchedule)
                                                <span title="No observation has been scheduled with this student yet."
                                                      class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 bg-slate-50 border border-slate-100 cursor-help">
                                                    No Observation Yet
                                                </span>
                                            @else
                                                <span title="This observation must be marked 'completed' before it can be evaluated. Current status: {{ str_replace('_', ' ', $observationSchedule->status) }}."
                                                      class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-100 cursor-help">
                                                    Awaiting Completed Observation
                                                </span>
                                            @endif
                                        @else
                                            <a href="{{ route('supervisor.evaluations.show', $evaluation->id) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                      bg-blue-50 text-blue-700 border border-blue-200/60
                                                      hover:bg-blue-600 hover:text-white hover:border-blue-600
                                                      transition-all duration-150 shadow-sm shadow-blue-100/50">
                                                View
                                            </a>
                                            <a href="{{ route('supervisor.evaluations.edit', $evaluation->id) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                      bg-indigo-50 text-indigo-700 border border-indigo-200/60
                                                      hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                                                      transition-all duration-150 shadow-sm shadow-indigo-100/50">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            {{-- ── Empty State ── --}}
            <div class="text-center py-16 px-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-blue-400">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">There are currently no students ready for supervisor evaluation.</p>
            </div>
        @endif

    </div>
</main>

</body>
</html>