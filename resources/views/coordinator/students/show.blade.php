{{-- resources/views/coordinator/students/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $student->first_name }} {{ $student->last_name }} – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Derived state for this page
    |--------------------------------------------------------------------------
    */
    $deployments   = $student->deployments->sortBy('id')->values();
    $fieldStudyDep = $deployments->where('program', 'Field Study')->where('status', '!=', 'cancelled')->last();
    $internshipDep = $deployments->where('program', 'Internship')->where('status', '!=', 'cancelled')->last();

    $fieldStudyPassed = (bool) $student->field_study_completed_at;

    // A Field Study deployment that is approved (supervisor assigned) and not yet completed
    $activeFieldStudy = $fieldStudyDep && $fieldStudyDep->supervisor_id && ! $fieldStudyDep->completed_at;

    // Any deployment still open (pending or deployed) - ignores cancelled and completed
    $hasOpenDeployment = $deployments->contains(
        fn ($d) => ! $d->completed_at && $d->status !== 'cancelled'
    );

    $hoursMet = $fieldStudyPercent >= 100;

    // Requirements (passed in by the controller; safe defaults if missing)
    $definitions = $definitions ?? collect();
    $submissions = $submissions ?? collect();
    $internshipDefinitions = $internshipDefinitions ?? collect();

    // Only ONGOING required documents decide whether the student can pass Field Study.
    // Initial requirements were already cleared before deployment.
    $ongoingRequired = $definitions
        ->where('phase', 'ongoing')
        ->where('is_required', true);

    $approvedRequired = $ongoingRequired
        ->filter(fn ($d) => optional($submissions->get($d->id))->status === 'approved')
        ->count();

    $requirementsMet = $ongoingRequired->count() === 0
        || $approvedRequired === $ongoingRequired->count();

    $confirmMessage = ($hoursMet && $requirementsMet)
        ? 'Mark Field Study as passed? The Field Study deployment will be completed and the student will unlock initial Internship requirements.'
        : 'Hours or required documents are not complete yet. Mark Field Study as passed anyway? The Field Study deployment will be completed and the student will unlock initial Internship requirements.';

    /*
    |--------------------------------------------------------------------------
    | Internship state
    |--------------------------------------------------------------------------
    */
    $internshipUnlocked = $student->internship_status !== 'locked';

    $requiredInternship = $internshipDefinitions->where('is_required', true);

    $approvedInternship = $requiredInternship
        ->filter(fn ($d) => optional($submissions->get($d->id))->status === 'approved')
        ->count();

    $allInternshipApproved = $requiredInternship
        ->every(fn ($d) => optional($submissions->get($d->id))->status === 'approved');

    $internshipDeploymentApproved = (bool) ($student->internshipDeployment && $student->internshipDeployment->supervisor_id);

    $coordinatorPassed = (bool) $student->internship_coordinator_passed_at;
    $supervisorPassed  = (bool) $student->internship_supervisor_passed_at;

    $canCoordinatorPass = $student->internship_status === 'accepted'
        && $internshipDeploymentApproved
        && $allInternshipApproved
        && ! $coordinatorPassed
        && ! $student->internship_completed_at;
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50">
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
            <nav class="hidden md:flex items-center gap-1">

                <a href="{{ route('coordinator.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm {{ request()->routeIs('coordinator.dashboard') ? 'font-semibold text-white bg-blue-600' : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Dashboard
                </a>

                <a href="{{ route('coordinator.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm {{ request()->routeIs('coordinator.students.*') ? 'font-semibold text-white bg-blue-600' : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Students
                </a>

                <a href="{{ route('coordinator.partner-schools.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm {{ request()->routeIs('coordinator.partner-schools.*') ? 'font-semibold text-white bg-blue-600' : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Partner Schools
                </a>

                <a href="{{ route('coordinator.deployments.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm {{ request()->routeIs('coordinator.deployments.*') ? 'font-semibold text-white bg-blue-600' : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Deployments
                </a>

                <a href="#"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">
                    Reports
                </a>

            </nav>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign Out"
                            class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center
                                   text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>


<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400" aria-label="Breadcrumb">
        <a href="{{ route('coordinator.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('coordinator.students.index') }}" class="hover:text-blue-600 transition-colors">Students</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-600 font-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>
    </nav>

    {{-- Flash messages --}}
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

    {{-- ── Profile Hero Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="h-1.5 w-full bg-gradient-to-r from-blue-500 via-blue-400 to-sky-400"></div>

        <div class="px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div class="flex items-center gap-5">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-blue-600 shadow shadow-blue-200
                            flex items-center justify-center text-white text-2xl font-extrabold flex-shrink-0">
                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                </div>

                <div>
                    <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student Profile</p>
                    <h1 class="text-xl font-extrabold text-slate-800 leading-tight">
                        {{ $student->first_name }}
                        {{ $student->middle_name ? strtoupper(substr($student->middle_name,0,1)).'.' : '' }}
                        {{ $student->last_name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5">
                        <code class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                            {{ $student->student_number }}
                        </code>

                        <span class="text-xs font-semibold text-slate-500">
                            {{ $student->program ?: 'No program' }}
                            &middot;
                            {{ $student->block ? 'Block ' . $student->block : 'No block' }}
                        </span>

                        {{-- Eligibility badge --}}
                        @if ($student->is_eligible)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Eligible
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-[11px] font-bold border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Not Eligible
                            </span>
                        @endif

                        {{-- Field Study result --}}
                        @if ($fieldStudyPassed)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                Field Study Passed
                            </span>
                        @endif

                        {{-- Internship result (both coordinator and supervisor passed) --}}
                        @if ($student->is_internship_valid)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                Internship Valid
                            </span>
                        @endif

                        {{-- Account status --}}
                        @if ($student->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ ucfirst($student->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick action button --}}
            @if ($deployments->isNotEmpty())
                <a href="{{ route('coordinator.deployments.show', $deployments->last()->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border-2 border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    View Latest Deployment
                </a>
            @else
                <a href="{{ route('coordinator.deployments.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-slate-400">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Awaiting Student Request
                </a>
            @endif
        </div>
    </div>

    {{-- ── Two-column layout ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Personal + Account Info --}}
        <div class="lg:col-span-1 flex flex-col gap-6">

            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-4">Personal Information</h2>
                <dl class="space-y-3">
                    @foreach ([
                        ['label' => 'Full Name',    'value' => trim("{$student->first_name} " . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name)],
                        ['label' => 'Student No.',  'value' => $student->student_number],
                        ['label' => 'Reference No.','value' => $student->reference_number ?? '—'],
                        ['label' => 'Program',      'value' => $student->program ?: '—'],
                        ['label' => 'Program Type', 'value' => $student->program_type ? ucfirst(str_replace('_', ' ', $student->program_type)) : '—'],
                        ['label' => 'Year Level',   'value' => $student->year_level ?: '—'],
                        ['label' => 'Block',        'value' => $student->block ? 'Block ' . $student->block : '—'],
                    ] as $row)
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $row['label'] }}</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Contact & Account --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-4">Account & Contact</h2>
                <dl class="space-y-3">
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</dt>
                        <dd class="text-sm font-semibold text-slate-800 break-all">{{ $student->user?->email ?? '—' }}</dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Mobile Number</dt>
                        <dd class="text-sm font-semibold text-slate-800">{{ $student->user?->mobile_number ?? '—' }}</dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Account Status</dt>
                        <dd>
                            @if ($student->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ ucfirst($student->status) }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Registered</dt>
                        <dd class="text-sm font-semibold text-slate-800">
                            {{ $student->created_at ? $student->created_at->format('M d, Y') : '—' }}
                        </dd>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last Updated</dt>
                        <dd class="text-sm text-slate-500">
                            {{ $student->updated_at ? $student->updated_at->format('M d, Y · g:i A') : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

        {{-- RIGHT: Hours + Requirements + Clearance + Deployments --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- ── Hours Progress ── --}}
            <div id="hours" class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-5">Hours Progress</h2>

                <div class="space-y-6">

                    {{-- Field Study Hours --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Field Study Hours</p>
                                <p class="text-xs text-slate-400 mt-0.5">Target: {{ $fieldStudyTarget }} hours</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-extrabold text-slate-800">{{ $student->field_study_hours ?? 0 }}</p>
                                <p class="text-xs text-slate-400">of {{ $fieldStudyTarget }} hrs</p>
                            </div>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                        {{ $fieldStudyPercent >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                                 style="width: {{ $fieldStudyPercent }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-[11px] font-semibold {{ $fieldStudyPercent >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">
                                {{ $fieldStudyPercent }}% completed
                            </p>
                            @if ($fieldStudyPercent >= 100)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                    Complete
                                </span>
                            @else
                                <span class="text-[11px] text-slate-400">
                                    {{ $fieldStudyTarget - ($student->field_study_hours ?? 0) }} hrs remaining
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Internship Hours --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Internship Hours</p>
                                <p class="text-xs text-slate-400 mt-0.5">Target: {{ $internshipTarget }} hours</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-extrabold text-slate-800">{{ $student->internship_hours ?? 0 }}</p>
                                <p class="text-xs text-slate-400">of {{ $internshipTarget }} hrs</p>
                            </div>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                        {{ $internshipPercent >= 100 ? 'bg-emerald-500' : 'bg-sky-500' }}"
                                 style="width: {{ $internshipPercent }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-[11px] font-semibold {{ $internshipPercent >= 100 ? 'text-emerald-600' : 'text-sky-600' }}">
                                {{ $internshipPercent }}% completed
                            </p>
                            @if ($internshipPercent >= 100)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                    Complete
                                </span>
                            @else
                                <span class="text-[11px] text-slate-400">
                                    {{ $internshipTarget - ($student->internship_hours ?? 0) }} hrs remaining
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Overall summary --}}
                    <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-4">
                        <div class="bg-blue-50/50 rounded-xl px-4 py-3 text-center">
                            <p class="text-xl font-extrabold text-blue-700">{{ $fieldStudyPercent }}%</p>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Field Study Progress</p>
                        </div>
                        <div class="bg-sky-50/50 rounded-xl px-4 py-3 text-center">
                            <p class="text-xl font-extrabold text-sky-700">{{ $internshipPercent }}%</p>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Internship Progress</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Uploaded Requirements (Field Study) ── --}}
            <div id="requirements" class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500">Field Study Requirements</h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Review status: <span class="font-semibold text-slate-600">{{ $student->field_study_status_label }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($ongoingRequired->count() > 0)
                            <span class="text-xs font-semibold {{ $requirementsMet ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $approvedRequired }}/{{ $ongoingRequired->count() }} ongoing required approved
                            </span>
                        @endif

                        @if (\Illuminate\Support\Facades\Route::has('coordinator.requirements.review.show'))
                            <a href="{{ route('coordinator.requirements.review.show', $student) }}"
                               class="px-3 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition-colors">
                                Open Full Review
                            </a>
                        @endif
                    </div>
                </div>

                @forelse ($definitions->groupBy('phase') as $phase => $group)
                    <div class="{{ ! $loop->first ? 'mt-5' : '' }}">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">
                            {{ $phase === 'ongoing' ? 'Ongoing Requirements' : 'Initial Requirements' }}
                        </p>

                        <div class="divide-y divide-slate-50 rounded-xl border border-slate-100 overflow-hidden">
                            @foreach ($group as $definition)
                                @php $submission = $submissions->get($definition->id); @endphp
                                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $definition->name }}</p>
                                        <p class="text-[11px] font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }}">
                                            {{ $definition->is_required ? 'Required' : 'Optional' }}
                                            @if ($submission?->submitted_at)
                                                <span class="text-slate-400 font-medium">&middot; Submitted {{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y') }}</span>
                                            @endif
                                        </p>
                                        @if ($submission?->remarks)
                                            <p class="text-[11px] text-slate-500 mt-0.5"><span class="font-bold">Remarks:</span> {{ $submission->remarks }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3">
                                        @if ($submission)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1
                                                {{ $submission->status === 'approved' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                                   : ($submission->status === 'rejected' ? 'bg-red-50 text-red-600 ring-red-200'
                                                   : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                                {{ $submission->status === 'pending' ? 'Pending Review' : ucfirst($submission->status) }}
                                            </span>
                                            <a href="{{ route('coordinator.requirements.review.file', $submission) }}" target="_blank"
                                               class="text-blue-600 hover:text-blue-700 font-semibold text-xs">View File</a>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-50 text-slate-400 ring-1 ring-slate-200">
                                                Not Submitted
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-400 italic">No Field Study requirements have been configured yet.</p>
                @endforelse
            </div>

            {{-- ── Internship Requirements ── --}}
            <div id="internship-requirements" class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500">Internship Requirements</h2>
                        @if ($internshipUnlocked)
                            <p class="text-xs text-slate-400 mt-1">
                                Review status: <span class="font-semibold text-slate-600">{{ $student->internship_status_label }}</span>
                            </p>
                        @endif
                    </div>

                    @if ($internshipUnlocked)
                        <div class="flex items-center gap-3">
                            @if ($requiredInternship->count() > 0)
                                <span class="text-xs font-semibold {{ $allInternshipApproved ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $approvedInternship }}/{{ $requiredInternship->count() }} required approved
                                </span>
                            @endif

                            @if (\Illuminate\Support\Facades\Route::has('coordinator.requirements.review.show'))
                                <a href="{{ route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Internship']) }}"
                                   class="px-3 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition-colors">
                                    Open Full Review
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                @if (! $internshipUnlocked)
                    <p class="px-4 py-6 text-sm text-slate-400 italic">Unlocks after Field Study is passed.</p>
                @else
                    @forelse ($internshipDefinitions->groupBy('phase') as $phase => $group)
                        <div class="{{ ! $loop->first ? 'mt-5' : '' }}">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">
                                {{ $phase === 'ongoing' ? 'Ongoing Requirements' : 'Initial Requirements' }}
                            </p>

                            <div class="divide-y divide-slate-50 rounded-xl border border-slate-100 overflow-hidden">
                                @foreach ($group as $definition)
                                    @php $submission = $submissions->get($definition->id); @endphp
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $definition->name }}</p>
                                            <p class="text-[11px] font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }}">
                                                {{ $definition->is_required ? 'Required' : 'Optional' }}
                                                @if ($submission?->submitted_at)
                                                    <span class="text-slate-400 font-medium">&middot; Submitted {{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y') }}</span>
                                                @endif
                                            </p>
                                            @if ($submission?->remarks)
                                                <p class="text-[11px] text-slate-500 mt-0.5"><span class="font-bold">Remarks:</span> {{ $submission->remarks }}</p>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3">
                                            @if ($submission)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1
                                                    {{ $submission->status === 'approved' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                                       : ($submission->status === 'rejected' ? 'bg-red-50 text-red-600 ring-red-200'
                                                       : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                                    {{ $submission->status === 'pending' ? 'Pending Review' : ucfirst($submission->status) }}
                                                </span>
                                                <a href="{{ route('coordinator.requirements.review.file', $submission) }}" target="_blank"
                                                   class="text-blue-600 hover:text-blue-700 font-semibold text-xs">View File</a>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-50 text-slate-400 ring-1 ring-slate-200">
                                                    Not Submitted
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-sm text-slate-400 italic">No Internship requirements have been configured yet.</p>
                    @endforelse
                @endif
            </div>

            {{-- ── Field Study Clearance (replaces "Mark Completed" on the Deployments page) ── --}}
            <div id="clearance" class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-5">Field Study Clearance</h2>

                @if ($fieldStudyPassed)
                    {{-- Already passed --}}
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-emerald-700">Field Study passed</p>
                            <p class="text-xs text-emerald-600 mt-0.5">
                                Cleared on {{ \Carbon\Carbon::parse($student->field_study_completed_at)->format('F j, Y') }}.
                                The student can now submit initial Internship requirements.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-slate-100 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Internship Placement</p>

                        @if ($internshipDep)
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $internshipDep->partnerSchool?->school_name ?? '—' }}
                                    <span class="text-xs font-semibold text-slate-400">
                                        &middot;
                                        {{ $internshipDep->completed_at ? 'Completed' : ($internshipDep->supervisor_id ? 'Deployed' : 'Pending approval') }}
                                    </span>
                                </p>
                                <a href="{{ route('coordinator.deployments.show', $internshipDep->id) }}"
                                   class="text-xs font-bold text-blue-600 hover:underline">View Deployment</a>
                            </div>
                        @elseif ($student->internship_status === 'accepted')
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm text-slate-500">
                                    No Internship placement yet. The student can request a school, or you can deploy them manually.
                                </p>
                                <a href="{{ route('coordinator.deployments.create', ['student_id' => $student->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow shadow-blue-200 transition-colors">
                                    Deploy for Internship
                                </a>
                            </div>
                        @else
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm text-slate-500">
                                    @if (in_array($student->internship_status, ['pending_review', 'requirements_incomplete', 'rejected'], true))
                                        Waiting for initial Internship requirements to be accepted
                                    @else
                                        Internship placement is not available yet.
                                    @endif
                                </p>
                                @if (in_array($student->internship_status, ['pending_review', 'requirements_incomplete', 'rejected'], true))
                                    <a href="{{ route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Internship']) }}"
                                       class="text-xs font-bold text-blue-600 hover:underline">Review Requirements</a>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Checklist --}}
                    <ul class="space-y-2.5 mb-5">
                        @php
                            $checks = [
                                [
                                    'ok'    => $hoursMet,
                                    'label' => 'Field Study hours',
                                    'note'  => ($student->field_study_hours ?? 0) . ' of ' . $fieldStudyTarget . ' hrs',
                                ],
                                [
                                    'ok'    => $requirementsMet,
                                    'label' => 'Ongoing required documents approved',
                                    'note'  => $ongoingRequired->count() > 0
                                                ? $approvedRequired . ' of ' . $ongoingRequired->count()
                                                : 'None required',
                                ],
                                [
                                    'ok'    => (bool) $activeFieldStudy,
                                    'label' => 'Active Field Study deployment',
                                    'note'  => $activeFieldStudy
                                                ? ($fieldStudyDep->partnerSchool?->school_name ?? 'Deployed')
                                                : 'Not deployed yet',
                                ],
                            ];
                        @endphp

                        @foreach ($checks as $check)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="flex items-center gap-2.5 text-slate-700 font-semibold">
                                    @if ($check['ok'])
                                        <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    @else
                                        <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        </span>
                                    @endif
                                    {{ $check['label'] }}
                                </span>
                                <span class="text-xs {{ $check['ok'] ? 'text-emerald-600' : 'text-slate-400' }} font-semibold">{{ $check['note'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST"
                          action="{{ route('coordinator.students.complete-field-study', $student) }}"
                          onsubmit="return confirm(@js($confirmMessage));"
                          class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                        @csrf
                        @method('PATCH')

                        <p class="text-xs text-slate-400 max-w-md">
                            Passing Field Study completes the student's Field Study deployment and unlocks initial Internship requirements for this student.
                        </p>

                        <button type="submit"
                                @disabled(! $activeFieldStudy)
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150
                                       {{ $activeFieldStudy
                                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow shadow-emerald-200'
                                            : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                            Mark Field Study as Passed
                        </button>
                    </form>

                    @unless ($activeFieldStudy)
                        <p class="mt-3 text-xs font-semibold text-amber-600">
                            The student needs an approved Field Study deployment before this can be marked as passed.
                        </p>
                    @endunless
                @endif
            </div>

            {{-- ── Internship Clearance ── --}}
            <div id="internship-clearance" class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-5">Internship Clearance</h2>

                @if (! $internshipUnlocked)
                    <p class="text-sm text-slate-400 italic">Locked. Unlocks after Field Study is passed.</p>
                @else
                    {{-- Checklist --}}
                    <ul class="space-y-2.5 mb-5">
                        @php
                            $internshipChecks = [
                                [
                                    'ok'    => $student->internship_status === 'accepted',
                                    'label' => 'Initial Internship requirements accepted',
                                    'note'  => $student->internship_status_label,
                                ],
                                [
                                    'ok'    => $internshipDeploymentApproved,
                                    'label' => 'Approved Internship deployment',
                                    'note'  => $internshipDeploymentApproved
                                                ? ($student->internshipDeployment->partnerSchool?->school_name ?? 'Deployed')
                                                : 'Not deployed yet',
                                ],
                                [
                                    'ok'    => $allInternshipApproved,
                                    'label' => 'All required Internship documents approved',
                                    'note'  => $requiredInternship->count() > 0
                                                ? $approvedInternship . ' of ' . $requiredInternship->count()
                                                : 'None required',
                                ],
                                [
                                    // Informational only - does NOT block the Pass button
                                    'ok'    => $internshipPercent >= 100,
                                    'label' => 'Internship hours',
                                    'note'  => ($student->internship_hours ?? 0) . ' of ' . $internshipTarget . ' hrs',
                                ],
                            ];
                        @endphp

                        @foreach ($internshipChecks as $check)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="flex items-center gap-2.5 text-slate-700 font-semibold">
                                    @if ($check['ok'])
                                        <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    @else
                                        <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        </span>
                                    @endif
                                    {{ $check['label'] }}
                                </span>
                                <span class="text-xs {{ $check['ok'] ? 'text-emerald-600' : 'text-slate-400' }} font-semibold">{{ $check['note'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Pass status --}}
                    <div class="rounded-xl border border-slate-100 overflow-hidden">
                        <div class="divide-y divide-slate-50">
                            <div class="flex items-center justify-between gap-3 px-4 py-3">
                                <span class="text-sm font-semibold text-slate-700">Coordinator</span>
                                @if ($coordinatorPassed)
                                    <span class="text-xs font-semibold text-emerald-600">
                                        Passed on {{ \Carbon\Carbon::parse($student->internship_coordinator_passed_at)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">Not yet passed</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-3 px-4 py-3">
                                <span class="text-sm font-semibold text-slate-700">Supervisor</span>
                                @if ($supervisorPassed)
                                    <span class="text-xs font-semibold text-emerald-600">
                                        Passed on {{ \Carbon\Carbon::parse($student->internship_supervisor_passed_at)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">Not yet passed</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50/60">
                                <span class="text-sm font-bold text-slate-800">Result</span>
                                @if ($student->is_internship_valid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>VALID
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Not valid yet
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        The student is only valid when BOTH the coordinator and the supervisor have passed.
                    </p>

                    {{-- Pass action --}}
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        @if ($coordinatorPassed)
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-700">You have passed this student</p>
                                    <p class="text-xs text-emerald-600 mt-0.5">
                                        {{ $supervisorPassed
                                            ? 'Both passes are recorded. The student is valid.'
                                            : 'Waiting for the supervisor to pass this student.' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <form method="POST"
                                  action="{{ route('coordinator.students.pass-internship', $student) }}"
                                  onsubmit="return confirm('Pass this student for the Internship? The student only becomes valid once the supervisor also passes.');"
                                  class="flex flex-wrap items-center justify-between gap-3">
                                @csrf
                                @method('PATCH')

                                <p class="text-xs text-slate-400 max-w-md">
                                    Your pass is recorded separately from the supervisor's pass.
                                </p>

                                <button type="submit"
                                        @disabled(! $canCoordinatorPass)
                                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150
                                               {{ $canCoordinatorPass
                                                    ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow shadow-emerald-200'
                                                    : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                                    Pass Student (Coordinator)
                                </button>
                            </form>

                            @unless ($canCoordinatorPass)
                                <p class="mt-3 text-xs font-semibold text-amber-600">
                                    @if ($student->internship_completed_at)
                                        This Internship has already been completed.
                                    @elseif ($student->internship_status !== 'accepted')
                                        The student's initial Internship requirements must be accepted first.
                                    @elseif (! $internshipDeploymentApproved)
                                        The student needs an approved Internship deployment before this can be passed.
                                    @elseif (! $allInternshipApproved)
                                        All required Internship documents must be approved first.
                                    @endif
                                </p>
                            @endunless
                        @endif
                    </div>
                @endif
            </div>

            {{-- ── Deployment History ── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-5">Deployment History</h2>

                @forelse ($deployments as $dep)
                    @php
                        if ($dep->status === 'cancelled') {
                            $depLabel = 'Cancelled';
                            $depBadge = 'bg-slate-100 text-slate-500';
                            $depDot   = 'bg-slate-400';
                        } elseif ($dep->completed_at) {
                            $depLabel = 'Completed';
                            $depBadge = 'bg-emerald-50 text-emerald-700';
                            $depDot   = 'bg-emerald-500';
                        } elseif ($dep->supervisor_id) {
                            $depLabel = 'Deployed';
                            $depBadge = 'bg-blue-50 text-blue-700';
                            $depDot   = 'bg-blue-500';
                        } else {
                            $depLabel = 'Pending Approval';
                            $depBadge = 'bg-amber-50 text-amber-700';
                            $depDot   = 'bg-amber-500';
                        }
                    @endphp

                    <div class="rounded-xl border border-slate-100 p-4 {{ ! $loop->first ? 'mt-4' : '' }}">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 border border-slate-200/50">
                                    {{ $dep->program }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $depBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $depDot }}"></span>{{ $depLabel }}
                                </span>
                            </div>
                            <a href="{{ route('coordinator.deployments.show', $dep->id) }}"
                               class="text-xs font-bold text-blue-600 hover:underline">View</a>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Partner School</dt>
                                <dd class="text-sm font-semibold text-slate-800">{{ $dep->partnerSchool?->school_name ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supervisor</dt>
                                <dd class="text-sm font-semibold text-slate-800">
                                    @if ($dep->supervisor)
                                        {{ $dep->supervisor->first_name }} {{ $dep->supervisor->last_name }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">School Year / Semester</dt>
                                <dd class="text-sm font-semibold text-slate-800">
                                    {{ $dep->school_year ?? '—' }} &middot; {{ $dep->semester ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                    {{ $dep->completed_at ? 'Completed On' : 'Deployed On' }}
                                </dt>
                                <dd class="text-sm font-semibold text-slate-800">
                                    @if ($dep->completed_at)
                                        {{ $dep->completed_at->format('F j, Y') }}
                                    @elseif ($dep->deployment_date)
                                        {{ $dep->deployment_date->format('F j, Y') }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                @empty
                    {{-- Not deployed --}}
                    <div class="flex flex-col items-center justify-center gap-4 py-10 text-slate-400">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-slate-300">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-500">Not Yet Deployed</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                This student has not been assigned to a partner school yet.
                            </p>
                        </div>
                    </div>
                @endforelse

                {{-- Manual deploy shortcut when nothing is open and the student isn't finished --}}
                @if (! $hasOpenDeployment && ! optional($internshipDep)->completed_at)
                    <div class="mt-5 pt-4 border-t border-slate-100 flex justify-end">
                        <a href="{{ route('coordinator.deployments.create', ['student_id' => $student->id]) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700
                                  text-white text-sm font-semibold transition-colors duration-150
                                  shadow shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Deploy This Student
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

</main>

<footer class="mt-8 border-t border-slate-100 bg-white">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.</p>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-slate-400">InternTrack v1.0 — System Online</span>
        </div>
    </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>