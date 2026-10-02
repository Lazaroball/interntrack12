{{-- resources/views/student/field-study/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Field Study Overview – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Student navigation (single source of truth for desktop + mobile)
    |--------------------------------------------------------------------------
    | 'url'    => null means the feature has no route yet (rendered disabled).
    | 'active' => evaluated against the current request.
    */
    $onRequirements = request()->routeIs('student.field-study.requirements*');

    $studentNav = [
        [
            'label'  => 'Dashboard',
            'url'    => route('student.dashboard'),
            'active' => request()->routeIs('student.dashboard'),
        ],
        [
            'label'  => 'Requirements',
            'url'    => route('student.field-study.requirements'),
            'active' => $onRequirements,
        ],
        [
            'label'  => 'Field Study',
            'url'    => route('student.field-study'),
            // Requirements lives under the field-study prefix, so exclude it here
            'active' => request()->routeIs('student.field-study*') && ! $onRequirements,
        ],
        [
            'label'  => 'Select School',
            'url'    => route('student.deployment.select'),
            'active' => request()->routeIs('student.deployment.*') || request()->is('student/deployment/*'),
        ],
        [
            'label'  => 'Teaching Hours',
            'url'    => url('/student/teaching-hours'),
            'active' => request()->routeIs('student.teaching-hours*') || request()->is('student/teaching-hours*'),
        ],
        [
            'label'  => 'Internship',
            'url'    => null,
            'active' => false,
        ],
    ];
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50" x-data="{ mobileOpen: false }">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow shadow-blue-200 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">UCU · CTE</span>
                </div>
            </div>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Student navigation">
                @foreach ($studentNav as $item)
                    @if ($item['url'])
                        <a href="{{ $item['url'] }}"
                           @if ($item['active']) aria-current="page" @endif
                           class="px-3.5 py-1.5 rounded-lg text-sm transition-colors duration-150
                                  {{ $item['active']
                                        ? 'font-semibold text-white bg-blue-600'
                                        : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">{{ $item['label'] }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</span>
                    <span class="text-xs font-semibold text-slate-700">{{ $student->full_name }}</span>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ $student->first_name ?? 'Student' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 shadow-lg shadow-slate-200/60 py-1 z-50">
                        <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            My Profile
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile menu toggle --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-500 hover:text-slate-800 p-1.5">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile nav panel --}}
        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden pb-4 space-y-1">
            @foreach ($studentNav as $item)
                @if ($item['url'])
                    <a href="{{ $item['url'] }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="block px-3.5 py-2 rounded-lg text-sm
                              {{ $item['active']
                                    ? 'font-semibold text-white bg-blue-600'
                                    : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">{{ $item['label'] }}</span>
                @endif
            @endforeach
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Field Study Overview</h1>
            <p class="text-sm text-slate-400 mt-1">Track your Field Study requirements, deployment, and progress.</p>
        </div>

        <a href="{{ route('student.dashboard') }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150 flex items-center gap-1.5 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>
    </div>
    @if ($student->field_study_status !== 'accepted')
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-amber-50 border border-amber-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                <p class="text-sm font-bold text-amber-700">Field Study is currently locked.</p>
                <p class="text-xs text-amber-600 mt-0.5">
                    Complete the required requirements and wait for coordinator approval.
                    <a href="{{ route('student.field-study.requirements') }}" class="underline font-semibold">View Requirements</a>
                </p>
            </div>
        </div>
    @endif
    {{-- ── Field Study Progress ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Field Study Progress</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                {{ $fieldStudyProgress['progress_percent'] }}%
            </span>
        </div>

        <div class="flex items-end justify-between mb-2">
            <span class="text-2xl font-extrabold text-slate-800">
                {{ $fieldStudyProgress['completed_hours'] }} / {{ $fieldStudyProgress['required_hours'] }} hrs
            </span>
            <span class="text-sm text-slate-400">
                {{ $fieldStudyProgress['remaining_hours'] }} hrs remaining
            </span>
        </div>

        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full rounded-full bg-blue-600 transition-all duration-500"
                 style="width: {{ $fieldStudyProgress['progress_percent'] }}%"></div>
        </div>

        @if ($fieldStudyProgress['progress_percent'] >= 100)
            <p class="text-xs font-semibold text-emerald-600 mt-3">Requirement reached.</p>
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
                            <span class="font-semibold text-slate-500">Program</span>
                            <span class="text-slate-700">{{ $deploymentInfo['program'] ?? '—' }}</span>
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
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-200 capitalize">
                                {{ $deploymentInfo['status'] ?? '—' }}
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
                    <p class="text-sm text-slate-400 italic">No deployment has been assigned yet.</p>
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
                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Total</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-blue-700">{{ $requirementsSummary['submitted'] }}</p>
                            <p class="text-[11px] font-semibold text-blue-600 mt-0.5">Submitted</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-amber-700">{{ $requirementsSummary['pending'] }}</p>
                            <p class="text-[11px] font-semibold text-amber-600 mt-0.5">Pending</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-extrabold text-red-600">{{ $requirementsSummary['rejected'] }}</p>
                            <p class="text-[11px] font-semibold text-red-500 mt-0.5">Rejected</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-50">
                        @foreach ($recentRequirements as $requirement)
                            <div class="flex items-center justify-between py-2.5 text-sm">
                                <span class="font-medium text-slate-700">{{ $requirement->requirement_name }}</span>
                                <span class="text-xs font-semibold text-slate-500 capitalize">{{ $requirement->status }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">No requirements submitted yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Teaching Hours / Attendance ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Teaching Hours / Attendance</h2>
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
                                    <td class="px-3 py-2 text-slate-600 whitespace-nowrap capitalize">
                                        {{ $log->status }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-400 italic">No daily logs recorded yet.</p>
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