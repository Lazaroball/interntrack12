{{-- resources/views/student/internship/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Internship Overview – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

@php
    $toneClasses = [
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
        'action'  => 'bg-blue-50 border-blue-200 text-blue-700',
        'waiting' => 'bg-amber-50 border-amber-200 text-amber-700',
    ];
    $tone = $toneClasses[$nextStep['tone']] ?? $toneClasses['action'];

    $deploymentBadge = [
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'deployed'  => 'bg-blue-50 text-blue-700 ring-blue-200',
        'pending'   => 'bg-amber-50 text-amber-700 ring-amber-200',
    ];
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@include('student.partials.nav')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Internship Overview</h1>
            <p class="text-sm text-slate-400 mt-1">Track your Internship requirements, deployment, attendance and final decision.</p>
        </div>

        @include('student.partials.stage-tabs', ['section' => 'overview', 'activeStage' => 'Internship'])
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Next step ── --}}
    <div class="rounded-2xl border p-5 {{ $tone }}">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70 mb-1">Next step</p>
                <p class="text-base font-extrabold">{{ $nextStep['title'] }}</p>
                <p class="text-sm mt-1 opacity-90 max-w-2xl">{{ $nextStep['body'] }}</p>
            </div>

            @if ($nextStep['url'])
                <a href="{{ $nextStep['url'] }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl bg-white/80 hover:bg-white text-sm font-semibold shadow-sm transition-colors duration-150 flex-shrink-0">
                    {{ $nextStep['label'] }}
                </a>
            @endif
        </div>
    </div>

    {{-- ── Internship Progress ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Internship Progress</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                {{ $internshipProgress['progress_percent'] }}%
            </span>
        </div>

        <div class="flex items-end justify-between mb-2">
            <span class="text-2xl font-extrabold text-slate-800">
                {{ $internshipProgress['completed_hours'] }} / {{ $internshipProgress['required_hours'] }} hrs
            </span>
            <span class="text-sm text-slate-400">
                {{ $internshipProgress['remaining_hours'] }} hrs remaining
            </span>
        </div>

        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full rounded-full bg-blue-600 transition-all duration-500"
                 style="width: {{ $internshipProgress['progress_percent'] }}%"></div>
        </div>

        @if ($internshipProgress['progress_percent'] >= 100)
            <p class="text-xs font-semibold text-emerald-600 mt-3">Required hours reached.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Deployment Information ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Deployment Information</h2>
            </div>

            <div class="p-6">
                @if ($deploymentInfo)
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">Partner School</span>
                            <span class="font-semibold text-slate-800">{{ $deploymentInfo['partner_school'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">School Year</span>
                            <span class="text-slate-700">{{ $deploymentInfo['school_year'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">Semester</span>
                            <span class="text-slate-700">{{ $deploymentInfo['semester'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">Deployment Date</span>
                            <span class="text-slate-700">
                                {{ $deploymentInfo['deployment_date']
                                    ? \Illuminate\Support\Carbon::parse($deploymentInfo['deployment_date'])->format('M d, Y')
                                    : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">Supervisor</span>
                            <span class="text-slate-700">{{ $deploymentInfo['supervisor_name'] ?? 'Supervisor not assigned yet.' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-500">Status</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1
                                {{ $deploymentBadge[$deploymentInfo['status_key']] ?? $deploymentBadge['pending'] }}">
                                {{ $deploymentInfo['status_label'] }}
                            </span>
                        </div>
                        @if (!empty($deploymentInfo['remarks']))
                            <div>
                                <p class="font-semibold text-slate-500 mb-1">Remarks</p>
                                <p class="text-slate-600 whitespace-pre-line">{{ $deploymentInfo['remarks'] }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">No Internship school has been chosen yet.</p>
                @endif
            </div>
        </div>

        {{-- ── Requirements Summary ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Requirements</h2>
            </div>

            <div class="p-6">
                @if ($requirementsSummary['total'] > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        <div class="bg-slate-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-slate-700">{{ $requirementsSummary['total'] }}</p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Submitted</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-emerald-700">{{ $requirementsSummary['approved'] }}</p>
                            <p class="text-[11px] font-semibold text-emerald-600 mt-0.5">Approved</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-blue-700">{{ $requirementsSummary['pending'] }}</p>
                            <p class="text-[11px] font-semibold text-blue-600 mt-0.5">In Review</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-red-600">{{ $requirementsSummary['rejected'] }}</p>
                            <p class="text-[11px] font-semibold text-red-500 mt-0.5">Rejected</p>
                        </div>
                    </div>

                    @if ($requirementsSummary['required_total'] > 0)
                        <p class="text-xs font-semibold text-slate-500 mb-3">
                            Required documents approved:
                            <span class="text-slate-800">{{ $requirementsSummary['required_approved'] }} of {{ $requirementsSummary['required_total'] }}</span>
                        </p>
                    @endif

                    <div class="divide-y divide-slate-50">
                        @foreach ($recentRequirements as $requirement)
                            <div class="flex items-center justify-between py-2.5 text-sm">
                                <span class="font-medium text-slate-700">{{ $requirement->requirementDefinition->name ?? $requirement->requirement_name ?? 'Requirement' }}</span>
                                <span class="text-xs font-semibold text-slate-500">{{ $requirement->status_label }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">No Internship requirements submitted yet.</p>
                @endif

                <a href="{{ route('student.internship.requirements') }}"
                   class="inline-block mt-3 text-blue-600 hover:text-blue-700 font-semibold text-xs">
                    Open Internship requirements
                </a>
            </div>
        </div>
    </div>

    {{-- ── Final Decision ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Final Decision</h2>
            <p class="text-xs text-slate-400 mt-0.5">Your Internship is complete only when both your coordinator and your supervisor have passed you.</p>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ([
                'Coordinator' => $finalDecision['coordinator'],
                'Supervisor'  => $finalDecision['supervisor'],
            ] as $who => $passedAt)
                <div class="flex items-center justify-between rounded-xl border px-4 py-3
                            {{ $passedAt ? 'border-emerald-200 bg-emerald-50' : 'border-slate-100 bg-slate-50' }}">
                    <span class="text-sm font-semibold {{ $passedAt ? 'text-emerald-700' : 'text-slate-600' }}">{{ $who }}</span>
                    @if ($passedAt)
                        <span class="text-xs font-semibold text-emerald-700">Passed &middot; {{ $passedAt->format('M d, Y') }}</span>
                    @else
                        <span class="text-xs font-semibold text-slate-400">Pending</span>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($finalDecision['completed'])
            <div class="px-6 pb-6">
                <p class="text-sm font-bold text-emerald-700">Internship completed on {{ $finalDecision['completed']->format('F j, Y') }}.</p>
            </div>
        @endif
    </div>

    {{-- ── Teaching Hours / Attendance ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Teaching Hours / Attendance</h2>
            <a href="{{ route('student.teaching-hours', ['stage' => 'Internship']) }}"
               class="text-xs font-semibold text-blue-600 hover:text-blue-700">Open Teaching Hours</a>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xl font-extrabold text-slate-700">{{ number_format($dailyLogSummary['total_logged_hours'], 2) }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Total Logged Hours</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xl font-extrabold text-slate-700">{{ $dailyLogSummary['total_logs'] }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Number of Logs</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xl font-extrabold text-slate-700">
                        {{ $dailyLogSummary['latest_log_date']
                            ? \Illuminate\Support\Carbon::parse($dailyLogSummary['latest_log_date'])->format('M d, Y')
                            : '—' }}
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Latest Log Date</p>
                </div>
            </div>

            @if ($recentDailyLogs->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Date</th>
                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Time In</th>
                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Time Out</th>
                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Hours Rendered</th>
                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($recentDailyLogs as $log)
                                <tr>
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($log->date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap">
                                        {{ $log->time_in ? \Illuminate\Support\Carbon::parse($log->time_in)->format('h:i A') : '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap">
                                        {{ $log->time_out ? \Illuminate\Support\Carbon::parse($log->time_out)->format('h:i A') : '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap">
                                        {{ number_format($log->hours_rendered, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap">
                                        {{ $log->status === 'in_progress' ? 'In Progress' : 'Completed' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-400 italic">No Internship attendance recorded yet.</p>
            @endif
        </div>
    </div>

    {{-- ── Quick Information ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">Quick Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Student Number</p>
                <p class="font-mono font-semibold text-slate-800">{{ $student->student_number ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Name</p>
                <p class="font-semibold text-slate-800">{{ $student->full_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Program</p>
                <p class="text-slate-700">{{ $student->program ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Year Level</p>
                <p class="text-slate-700">{{ $student->year_level ? 'Year ' . $student->year_level : '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Block</p>
                <p class="text-slate-700">{{ $student->block ?? '—' }}</p>
            </div>
        </div>
    </div>

</main>

</body>
</html>