{{-- resources/views/student/dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

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

            <nav class="hidden md:flex items-center gap-1" aria-label="Student navigation">
                <a href="{{ route('student.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">
                    Dashboard
                </a>
                <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Requirements</span>
                <a href="{{ route('student.field-study') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Field Study</a>
                <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Internship</span>
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
                    <div x-show="open" @click.outside="open = false"
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
            <a href="{{ route('student.dashboard') }}" class="block px-3.5 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600">Dashboard</a>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Requirements</span>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Field Study</span>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Internship</span>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Welcome Section ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6">
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">
            Welcome back, {{ $student->first_name ?? 'Student' }}
        </h1>
        <p class="text-sm text-slate-400 mt-1">Track your Field Study and Internship progress.</p>

        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm">
            <div class="flex items-center gap-1.5 text-slate-600">
                <span class="text-slate-400 font-semibold">Student No.:</span>
                <span class="font-mono font-semibold">{{ $student->student_number ?? '—' }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-slate-600">
                <span class="text-slate-400 font-semibold">Program:</span>
                <span class="font-semibold">{{ $student->program ?? '—' }}</span>
                @if (!empty($student->program_type))
                    <span class="text-slate-400">({{ $student->program_type }})</span>
                @endif
            </div>
            <div class="flex items-center gap-1.5 text-slate-600">
                <span class="text-slate-400 font-semibold">Year &amp; Block:</span>
                <span class="font-semibold">
                    {{ $student->year_level ? 'Year ' . $student->year_level : '—' }}
                    @if (!empty($student->block))
                        &middot; Block {{ $student->block }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- ── Progress Cards ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Field Study Progress --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Field Study Hours</h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                    {{ $fieldStudy['progress_percent'] }}%
                </span>
            </div>

            <div class="flex items-end justify-between mb-2">
                <span class="text-2xl font-extrabold text-slate-800">{{ $fieldStudy['hours_completed'] ?? 0 }}</span>
                <span class="text-sm text-slate-400">of {{ $fieldStudy['hours_required'] }} hrs</span>
            </div>

            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-blue-600 transition-all duration-500"
                     style="width: {{ min(100, max(0, (int) $fieldStudy['progress_percent'])) }}%"></div>
            </div>
        </div>

        {{-- Internship Progress --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Internship Hours</h2>
                @if (is_null($internship['hours_required']))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 ring-1 ring-slate-200">
                        Not configured
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                        {{ $internship['progress_percent'] }}%
                    </span>
                @endif
            </div>

            <div class="flex items-end justify-between mb-2">
                <span class="text-2xl font-extrabold text-slate-800">{{ $internship['hours_completed'] ?? 0 }}</span>
                <span class="text-sm text-slate-400">
                    @if (is_null($internship['hours_required']))
                        hrs logged
                    @else
                        of {{ $internship['hours_required'] }} hrs
                    @endif
                </span>
            </div>

            @if (is_null($internship['hours_required']))
                <p class="text-xs text-slate-400 italic mt-1">
                    Internship hour requirement has not been configured yet. Your logged hours are shown above.
                </p>
            @else
                <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-600 transition-all duration-500"
                         style="width: {{ min(100, max(0, (int) $internship['progress_percent'])) }}%"></div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Eligibility & Preferred School Cards ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Eligibility --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Deployment Eligibility</h2>

            @if ($isDeployed && $currentDeployment)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Deployed</p>
                        <p class="text-xs text-emerald-600">You have been deployed for Field Study. See details below.</p>
                    </div>
                </div>
            @elseif ($isDeploymentPending)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>
                        <p class="text-sm font-bold text-blue-700">Pending</p>
                        <p class="text-xs text-blue-600">Your partner school preference has been submitted and is awaiting coordinator processing.</p>
                    </div>
                </div>
            @elseif ($isEligible)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Eligible</p>
                        <p class="text-xs text-emerald-600">You've been accepted for Field Study. You may now select a partner school.</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-amber-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <p class="text-sm font-bold text-amber-700">Not Yet Eligible</p>
                        <p class="text-xs text-amber-600">Some requirements are still pending.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Preferred Partner School --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Preferred Partner School</h2>

            @if ($preferredPartnerSchool)
                <p class="text-sm font-semibold text-slate-800">{{ $preferredPartnerSchool->school_name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $preferredPartnerSchool->school_type ?? '—' }}</p>

                <div class="mt-3 space-y-1.5 text-sm text-slate-600">
                    @if (!empty($preferredPartnerSchool->address))
                        <p class="flex items-start gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0 mt-0.5 text-slate-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $preferredPartnerSchool->address }}
                        </p>
                    @endif
                    @if (!empty($preferredPartnerSchool->contact_person))
                        <p class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-semibold">Contact:</span>
                            {{ $preferredPartnerSchool->contact_person }}
                        </p>
                    @endif
                    @if (!empty($preferredPartnerSchool->contact_number))
                        <p class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-semibold">Phone:</span>
                            {{ $preferredPartnerSchool->contact_number }}
                        </p>
                    @endif
                </div>
            @else
                <p class="text-sm text-slate-400 italic">No preferred partner school selected.</p>
            @endif
        </div>
    </div>

    {{-- ── Deployment Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Current Deployment</h2>
            @if ($isDeployed && $currentDeployment)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Deployed
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-bold border border-slate-200">
                    Not Deployed
                </span>
            @endif
        </div>

        <div class="p-6">
            @if ($isDeployed && $currentDeployment)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Partner School</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $currentDeployment['partner_school'] ?? 'Not assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Supervisor</p>
                        <p class="text-sm text-slate-700">
                            {{ $currentDeployment['supervisor_name'] ?? 'Not yet assigned' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Program</p>
                        <p class="text-sm text-slate-700">{{ $currentDeployment['program'] ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">School Year</p>
                        <p class="text-sm text-slate-700">{{ $currentDeployment['school_year'] ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Semester</p>
                        <p class="text-sm text-slate-700">{{ $currentDeployment['semester'] ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Deployment Date</p>
                        <p class="text-sm text-slate-700">
                            {{ $currentDeployment['deployment_date']
                                ? \Illuminate\Support\Carbon::parse($currentDeployment['deployment_date'])->format('M d, Y')
                                : '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Status</p>
                        <p class="text-sm text-slate-700 capitalize">{{ $currentDeployment['status'] ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Approval Status</p>
                        @if (!empty($currentDeployment['is_approved']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                Awaiting Approval
                            </span>
                        @endif
                    </div>

                    @if (!empty($currentDeployment['remarks']))
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-slate-500 mb-1">Remarks</p>
                            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $currentDeployment['remarks'] }}</p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-slate-400 italic">Not currently deployed.</p>
            @endif
        </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Quick Actions</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">

            @php
                $quickActions = [
                    ['label' => 'Requirements', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z'],
                    ['label' => 'Field Study', 'icon' => 'M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'],
                    ['label' => 'Attendance', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9l2 2 4-4'],
                    ['label' => 'Lesson Plans', 'icon' => 'M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z'],
                    ['label' => 'Internship', 'icon' => 'M20 7h-9m9 5H8m12 5H5M4 7h.01M4 12h.01M4 17h.01'],
                    ['label' => 'Evaluations', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2'],
                ];
            @endphp

            @foreach ($quickActions as $action)
                <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-4 flex flex-col items-center justify-center gap-2.5 text-center opacity-60 cursor-not-allowed">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-slate-400">
                            <path d="{{ $action['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $action['label'] }}</span>
                    <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-400 text-[9px] font-bold uppercase tracking-wide">
                        Soon
                    </span>
                </div>
            @endforeach

        </div>
    </div>

</main>

</body>
</html>