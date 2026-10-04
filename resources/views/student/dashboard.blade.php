{{-- resources/views/student/dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Internship lock override
    |--------------------------------------------------------------------------
    | Internship counts as unlocked only after the coordinator cleared Field Study,
    | no matter what the internship_status column currently holds.
    */
    if (! $student->is_internship_unlocked) {
        $internshipStatus          = 'locked';
        $internshipStatusLabel     = 'Locked (Field Study not completed)';
        $canSelectInternshipSchool = false;
    }

    // Used by the Quick Actions grid only (the top nav no longer has Internship)
    $internshipUrl = $internshipStatus !== 'locked' ? route('student.internship.requirements') : null;

    /*
    |--------------------------------------------------------------------------
    | Quick Actions
    |--------------------------------------------------------------------------
    | Requirements is the single entry point for requirements and lesson plans,
    | so there is no separate Lesson Plans action.
    | Attendance is the entry point for Teaching Hours (time in / time out),
    | so there is no separate Teaching Hours action.
    */
    $quickActions = [
        ['label' => 'Requirements',   'url' => route('student.field-study.requirements'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z'],
        ['label' => 'Field Study',    'url' => route('student.field-study'), 'icon' => 'M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'],
        ['label' => 'Select School',  'url' => route('student.deployment.select'), 'icon' => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0zM12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z'],
        ['label' => 'Attendance',     'url' => url('/student/teaching-hours'), 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9l2 2 4-4'],
        ['label' => 'Internship',     'url' => $internshipUrl, 'icon' => 'M20 7h-9m9 5H8m12 5H5M4 7h.01M4 12h.01M4 17h.01'],
        ['label' => 'Evaluations',    'url' => null, 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Internship status badge styles
    |--------------------------------------------------------------------------
    */
    $internshipBadgeStyles = [
        'locked'                  => 'bg-slate-100 text-slate-500 ring-slate-200',
        'pending_review'          => 'bg-slate-100 text-slate-600 ring-slate-200',
        'requirements_incomplete' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'accepted'                => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'rejected'                => 'bg-red-50 text-red-600 ring-red-200',
    ];
    $internshipBadgeClass = $internshipBadgeStyles[$internshipStatus] ?? $internshipBadgeStyles['pending_review'];

    /*
    |--------------------------------------------------------------------------
    | Internship step list (Internship Status card)
    |--------------------------------------------------------------------------
    */
    $internshipDeployed = ($isDeployed && $currentDeployment && ($currentDeployment['program'] ?? null) === 'Internship')
        || $coordinatorPassed
        || $supervisorPassed
        || $isInternshipValid;

    $internshipSteps = [
        ['ok' => (bool) $hasCompletedFieldStudy,         'label' => 'Field Study completed'],
        ['ok' => $internshipStatus === 'accepted',       'label' => 'Initial Internship requirements accepted'],
        ['ok' => (bool) $internshipDeployed,             'label' => 'Internship school deployed'],
        ['ok' => (bool) $coordinatorPassed,              'label' => 'Coordinator pass'],
        ['ok' => (bool) $supervisorPassed,               'label' => 'Supervisor pass'],
    ];
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@include('student.partials.nav')

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

    {{-- ── Quick Actions ── --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Quick Actions</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($quickActions as $action)
                @if ($action['url'])
                    <a href="{{ $action['url'] }}"
                       class="group bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-4 flex flex-col items-center justify-center gap-2.5 text-center
                              hover:border-blue-200 hover:bg-blue-50/40 transition-colors duration-150
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600">
                                <path d="{{ $action['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-700">{{ $action['label'] }}</span>
                    </a>
                @else
                    <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-4 flex flex-col items-center justify-center gap-2.5 text-center opacity-60 cursor-not-allowed">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-slate-400">
                                <path d="{{ $action['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $action['label'] }}</span>
                        <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-400 text-[9px] font-bold uppercase tracking-wide">
                            {{ $action['label'] === 'Internship' ? 'Locked' : 'Soon' }}
                        </span>
                    </div>
                @endif
            @endforeach
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

    {{-- ── Internship Status ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Internship Status</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $internshipBadgeClass }}">
                {{ $internshipStatusLabel }}
            </span>
        </div>

        @if ($internshipStatus === 'locked')
            <p class="text-sm text-slate-400 italic">Unlocks after you complete Field Study.</p>
        @else
            <ul class="space-y-2.5">
                @foreach ($internshipSteps as $step)
                    <li class="flex items-center gap-2.5 text-sm text-slate-700 font-semibold">
                        @if ($step['ok'])
                            <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                        @else
                            <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            </span>
                        @endif
                        {{ $step['label'] }}
                    </li>
                @endforeach

                <li class="flex items-center justify-between gap-2.5 text-sm pt-3 mt-1 border-t border-slate-100">
                    <span class="font-bold text-slate-800">Result</span>
                    @if ($isInternshipValid)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>VALID
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Both passes required
                        </span>
                    @endif
                </li>
            </ul>
        @endif
    </div>

    {{-- ── Eligibility & Preferred School Cards ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Eligibility --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Deployment Eligibility</h2>

            @if ($isInternshipValid)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Internship Passed</p>
                        <p class="text-xs text-emerald-600">Your coordinator and supervisor have both passed you.</p>
                    </div>
                </div>
            @elseif ($isDeployed && $currentDeployment)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Deployed</p>
                        <p class="text-xs text-emerald-600">You have been deployed for {{ $currentDeployment['program'] ?? 'your program' }}. See details below.</p>
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
            @elseif ($canSelectInternshipSchool)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Eligible for Internship</p>
                        <p class="text-xs text-emerald-600">Your initial Internship requirements are accepted. You may now choose an Internship school.</p>
                        <a href="{{ route('student.deployment.select') }}" class="inline-block mt-1.5 text-xs font-bold text-emerald-700 hover:underline">Choose a school</a>
                    </div>
                </div>
            @elseif ($hasCompletedFieldStudy && in_array($internshipStatus, ['pending_review', 'requirements_incomplete', 'rejected'], true))
                @if ($internshipStatus === 'rejected')
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-amber-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <p class="text-sm font-bold text-amber-700">Internship requirements pending</p>
                            <p class="text-xs text-amber-600">Some Internship documents need correction.</p>
                            <a href="{{ route('student.internship.requirements') }}" class="inline-block mt-1.5 text-xs font-bold text-amber-700 hover:underline">Open Internship requirements</a>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <div>
                            <p class="text-sm font-bold text-blue-700">Internship requirements pending</p>
                            <p class="text-xs text-blue-600">Submit your initial Internship requirements and wait for coordinator approval.</p>
                            <a href="{{ route('student.internship.requirements') }}" class="inline-block mt-1.5 text-xs font-bold text-blue-700 hover:underline">Open Internship requirements</a>
                        </div>
                    </div>
                @endif
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

</main>

</body>
</html>