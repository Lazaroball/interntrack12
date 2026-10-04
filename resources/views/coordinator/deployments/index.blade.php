{{-- resources/views/coordinator/deployments/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deployments – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

@php
    $user = auth()->user();

    $coordinatorNav = [
        [
            'label'  => 'Dashboard',
            'url'    => route('coordinator.dashboard'),
            'active' => request()->routeIs('coordinator.dashboard'),
        ],
        [
            'label'  => 'Students',
            'url'    => route('coordinator.students.index'),
            'active' => request()->routeIs('coordinator.students.*'),
        ],
        [
            'label'  => 'Requirements',
            'url'    => route('coordinator.requirements.review.index'),
            'active' => request()->routeIs('coordinator.requirements.*'),
        ],
        [
            'label'  => 'Partner Schools',
            'url'    => route('coordinator.partner-schools.index'),
            'active' => request()->routeIs('coordinator.partner-schools.*'),
        ],
        [
            'label'  => 'Deployments',
            'url'    => route('coordinator.deployments.index'),
            'active' => request()->routeIs('coordinator.deployments.*'),
        ],
    ];

    // Safe defaults so the page still renders before the controller passes these lists
    $studentPrograms = $studentPrograms ?? [];
    $blocks          = $blocks ?? [];
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ════════════════════════════════════════════════════════════
     TOP NAVIGATION BAR
════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50" x-data="{ mobileOpen: false }">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 shadow shadow-blue-200 flex-shrink-0 overflow-hidden flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="InternTrack Logo" class="w-full h-full object-contain p-1" />
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">UCU · CTE</span>
                </div>
            </div>

            {{-- Center Nav --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                @foreach ($coordinatorNav as $item)
                    <a href="{{ $item['url'] }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all duration-150
                       {{ $item['active']
                            ? 'bg-blue-600 text-white font-semibold shadow-sm'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right Controls --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last Login</span>
                    <span class="text-xs font-medium text-slate-600">
                        {{ $user->last_login_at
                            ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y · g:i A')
                            : now()->format('M d, Y · g:i A') }}
                    </span>
                </div>

                {{-- Mobile Menu Trigger --}}
                <button type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center
                               text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150"
                        aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                {{-- Avatar + Logout Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1"
                            :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center
                                    text-white text-xs font-bold select-none flex-shrink-0">
                            {{ strtoupper(substr($user->first_name ?? 'C', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">
                            {{ $user->first_name ?? 'Coordinator' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                             class="w-3.5 h-3.5 text-slate-400">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100
                                shadow-lg shadow-slate-200/60 py-1 z-50" role="menu">
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            My Profile
                        </a>
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Settings
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors text-left">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        {{-- Mobile nav panel --}}
        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden pb-4 space-y-1">
            @foreach ($coordinatorNav as $item)
                <a href="{{ $item['url'] }}"
                   @if ($item['active']) aria-current="page" @endif
                   class="block px-3.5 py-2 rounded-lg text-sm
                          {{ $item['active']
                                ? 'font-semibold text-white bg-blue-600'
                                : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-100">Portal Zone</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-1">Deployment Management</h1>
            <p class="text-sm text-slate-500 mt-1">Review school requests, manually deploy students, assign supervisors, and update records.</p>
        </div>

        {{-- Manual Action --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('coordinator.deployments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Manual Deployment
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-3.5 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-3.5 text-sm font-semibold text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Workflow rail --}}
    <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
        <span class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            Pending Deployments
        </span>
        <span class="h-px w-6 bg-slate-200"></span>
        <span class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
            Student Deployed
        </span>
        <span class="h-px w-6 bg-slate-200"></span>
        <span class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
            Completed
        </span>
        <span class="h-px w-6 bg-slate-200"></span>
        <span class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-rose-400"></span>
            Cancelled
        </span>
    </div>

    {{-- ================= FILTERS ================= --}}
    <form
        id="filter-form"
        method="GET"
        action="{{ route('coordinator.deployments.index') }}"
        class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm shadow-blue-50/50"
    >
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Search (debounced auto-submit) --}}
            <div class="lg:col-span-2 relative">
                <label for="search" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pt-5 pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
                </span>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Student name, number, or school"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200"
                    x-data
                    x-on:input.debounce.400ms="$el.form.requestSubmit()"
                >
            </div>

            {{-- Partner School --}}
            <div>
                <label for="partner_school_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Partner School</label>
                <select
                    id="partner_school_id"
                    name="partner_school_id"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                >
                    <option value="">All schools</option>
                    @foreach ($partnerSchools as $school)
                        <option value="{{ $school->id }}" @selected(request('partner_school_id') == $school->id)>
                            {{ $school->school_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- School Year --}}
            <div>
                <label for="school_year" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">School Year</label>
                <select
                    id="school_year"
                    name="school_year"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                >
                    <option value="">All years</option>
                    @foreach ($schoolYears as $year)
                        <option value="{{ $year }}" @selected(request('school_year') == $year)>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">

            {{-- Program (the student's program: BEED / BSED / BPED) --}}
            <div>
                <label for="student_program" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Program</label>
                <select
                    id="student_program"
                    name="student_program"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                >
                    <option value="">All programs</option>
                    @foreach ($studentPrograms as $studentProgram)
                        <option value="{{ $studentProgram }}" @selected(request('student_program') == $studentProgram)>
                            {{ $studentProgram }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Block --}}
            <div>
                <label for="block" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Block</label>
                <select
                    id="block"
                    name="block"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                >
                    <option value="">All blocks</option>
                    @foreach ($blocks as $blockOption)
                        <option value="{{ $blockOption }}" @selected(request('block') == $blockOption)>
                            Block {{ $blockOption }}
                        </option>
                    @endforeach
                    <option value="none" @selected(request('block') === 'none')>No block assigned</option>
                </select>
            </div>

            {{-- Type (Field Study / Internship, stored in deployments.program) --}}
            <div>
                <label for="program" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Type</label>
                <select
                    id="program"
                    name="program"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                >
                    <option value="">All types</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program }}" @selected(request('program') == $program)>
                            {{ $program }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex lg:justify-end">
                <a
                    href="{{ route('coordinator.deployments.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Reset Filters
                </a>
            </div>
        </div>
    </form>

    {{-- ================= PENDING DEPLOYMENTS ================= --}}
    <section
        x-data="{ selected: [], get hasSelection() { return this.selected.length > 0 } }"
        class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm shadow-blue-50/50"
    >
        <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-amber-50 text-amber-600 border-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight">Pending Deployments</h2>
                    <p class="text-xs font-semibold text-slate-400">Students who submitted a preferred partner school and are waiting for coordinator approval.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-bind:disabled="!hasSelection"
                    x-on:click="$dispatch('open-approve-modal', { mode: 'selected', ids: selected })"
                    class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none"
                >
                    Approve Selected
                </button>

                @if ($waitingDeployments->total() > 0)
                    <button
                        type="button"
                        x-on:click="$dispatch('open-approve-modal', { mode: 'all', ids: [] })"
                        class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100 transition"
                    >
                        Approve All
                    </button>
                @endif
            </div>
        </div>

        @if ($waitingDeployments->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" /></svg>
                </div>
                <p class="font-bold text-slate-800 text-sm">No pending deployments</p>
                <p class="text-xs text-slate-400 mt-1">No students are waiting for approval. Adjust your filters or use manual deployment to place students immediately.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="w-10 px-6 py-4">
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    x-on:change="selected = $event.target.checked ? [{{ $waitingDeployments->pluck('id')->implode(',') }}] : []"
                                >
                            </th>
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Program &amp; Block</th>
                            <th class="px-6 py-4">Preferred School</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">School Year</th>
                            <th class="px-6 py-4">Requested</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach ($waitingDeployments as $deployment)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        value="{{ $deployment->id }}"
                                        x-model="selected"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-800 leading-tight">
                                        {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $deployment->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-extrabold uppercase text-slate-700">{{ $deployment->student->program ?: '—' }}</div>
                                    @if ($deployment->student->block)
                                        <div class="text-xs text-slate-400">Block {{ $deployment->student->block }}</div>
                                    @else
                                        <div class="text-xs font-semibold text-amber-500">No block</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $deployment->partnerSchool->school_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-500 border border-slate-200/50">{{ $deployment->program }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $deployment->school_year }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $deployment->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('coordinator.deployments.show', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">View</a>
                                        <a href="{{ route('coordinator.deployments.edit', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white transition border border-slate-200/50 hover:border-blue-600">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $waitingDeployments->onEachSide(1)->links() }}
            </div>
        @endif

        {{-- Approve modal — shared for "Approve Selected" and "Approve All" --}}
        <div
            x-data="{ open: false, mode: 'selected', ids: [] }"
            x-on:open-approve-modal.window="open = true; mode = $event.detail.mode; ids = $event.detail.ids"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4"
        >
            <div
                x-show="open"
                x-on:click.outside="open = false"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <h3 class="text-base font-black text-slate-800 tracking-tight" x-text="mode === 'all' ? 'Approve all pending deployments' : 'Approve selected deployments'"></h3>
                <p class="mt-1 text-sm text-slate-500">
                    Choose the supervisor who will approve
                    <span x-text="mode === 'all' ? 'every student currently pending.' : ids.length + ' selected student(s).'"></span>
                </p>

                <form
                    method="POST"
                    x-bind:action="mode === 'all' ? '{{ route('coordinator.deployments.approve-all') }}' : '{{ route('coordinator.deployments.approve-selected') }}'"
                    class="mt-4 space-y-4"
                >
                    @csrf

                    {{-- Carry selected ids when approving a specific batch --}}
                    <template x-if="mode === 'selected'">
                        <template x-for="id in ids" :key="id">
                            <input type="hidden" name="deployment_ids[]" :value="id">
                        </template>
                    </template>

                    {{-- Carry active filters when approving all, so the batch matches what's visible --}}
                    <template x-if="mode === 'all'">
                        <div>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="partner_school_id" value="{{ request('partner_school_id') }}">
                            <input type="hidden" name="school_year" value="{{ request('school_year') }}">
                            <input type="hidden" name="program" value="{{ request('program') }}">
                            <input type="hidden" name="student_program" value="{{ request('student_program') }}">
                            <input type="hidden" name="block" value="{{ request('block') }}">
                        </div>
                    </template>

                    <div>
                        <label for="modal_supervisor_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor</label>
                        <select
                            id="modal_supervisor_id"
                            name="supervisor_id"
                            required
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        >
                            <option value="" disabled selected>Select a supervisor</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->last_name }}, {{ $supervisor->first_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            x-on:click="open = false"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition"
                        >
                            Confirm Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ================= STUDENT DEPLOYED ================= --}}
    <section
        x-data="{ selectedCurrent: [], get hasCurrentSelection() { return this.selectedCurrent.length > 0 } }"
        class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm shadow-blue-50/50"
    >
        <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-blue-50 text-blue-600 border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 1-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight">Student Deployed</h2>
                    <p class="text-xs font-semibold text-slate-400">Students approved by the coordinator and currently deployed with an assigned supervisor.</p>
                </div>
            </div>

            {{-- Bulk update actions --}}
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-bind:disabled="!hasCurrentSelection"
                    x-on:click="$dispatch('open-update-supervisor-modal', { ids: selectedCurrent })"
                    class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-md transition disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none"
                >
                    Reassign Selected Supervisor
                </button>
            </div>
        </div>

        @if ($currentDeployments->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <p class="font-bold text-slate-800 text-sm">No deployed students match your filters</p>
                <p class="text-xs text-slate-400 mt-1">Approve a pending student or create a manual deployment to see them appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="w-10 px-6 py-4">
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    x-on:change="selectedCurrent = $event.target.checked ? [{{ $currentDeployments->pluck('id')->implode(',') }}] : []"
                                >
                            </th>
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Program &amp; Block</th>
                            <th class="px-6 py-4">Partner School</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">School Year</th>
                            <th class="px-6 py-4">Deployed On</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach ($currentDeployments as $deployment)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        value="{{ $deployment->id }}"
                                        x-model="selectedCurrent"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-800 leading-tight">
                                        {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $deployment->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-extrabold uppercase text-slate-700">{{ $deployment->student->program ?: '—' }}</div>
                                    @if ($deployment->student->block)
                                        <div class="text-xs text-slate-400">Block {{ $deployment->student->block }}</div>
                                    @else
                                        <div class="text-xs font-semibold text-amber-500">No block</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $deployment->partnerSchool->school_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-500 border border-slate-200/50">{{ $deployment->program }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $deployment->school_year }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ optional($deployment->deployment_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('coordinator.deployments.show', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">View</a>
                                        <a href="{{ route('coordinator.deployments.edit', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white transition border border-slate-200/50 hover:border-blue-600">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $currentDeployments->onEachSide(1)->links() }}
            </div>
        @endif

        {{-- Batch Reassign Supervisor Modal --}}
        <div
            x-data="{ open: false, ids: [] }"
            x-on:open-update-supervisor-modal.window="open = true; ids = $event.detail.ids"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4"
        >
            <div
                x-show="open"
                x-on:click.outside="open = false"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <h3 class="text-base font-black text-slate-800 tracking-tight">Batch Update Supervisor</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Reassign a new supervisor for <span x-text="ids.length"></span> selected deployed student(s).
                </p>

                <form
                    method="POST"
                    action="{{ route('coordinator.deployments.bulk-update-supervisor') }}"
                    class="mt-4 space-y-4"
                >
                    @csrf
                    @method('PATCH')

                    <template x-for="id in ids" :key="id">
                        <input type="hidden" name="deployment_ids[]" :value="id">
                    </template>

                    <div>
                        <label for="bulk_supervisor_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">New Supervisor</label>
                        <select
                            id="bulk_supervisor_id"
                            name="supervisor_id"
                            required
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        >
                            <option value="" disabled selected>Select a new supervisor</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->last_name }}, {{ $supervisor->first_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            x-on:click="open = false"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-md transition"
                        >
                            Update Supervisor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ================= COMPLETED ================= --}}
    <section class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm shadow-blue-50/50">
        <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-slate-100 text-slate-500 border-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div>
                <h2 class="text-base font-black text-slate-800 tracking-tight">Completed</h2>
                <p class="text-xs font-semibold text-slate-400">Read-only history of deployments that have been fully completed.</p>
            </div>
        </div>

        @if ($completedDeployments->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <p class="font-bold text-slate-800 text-sm">No completed deployments match your filters</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Program &amp; Block</th>
                            <th class="px-6 py-4">Partner School</th>
                            <th class="px-6 py-4">Supervisor</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">School Year</th>
                            <th class="px-6 py-4">Completed On</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-400">
                        @foreach ($completedDeployments as $deployment)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-600 leading-tight">
                                        {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $deployment->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-extrabold uppercase text-slate-500">{{ $deployment->student->program ?: '—' }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ $deployment->student->block ? 'Block ' . $deployment->student->block : 'No block' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $deployment->partnerSchool->school_name }}</td>
                                <td class="px-6 py-4">
                                    @if ($deployment->supervisor)
                                        {{ $deployment->supervisor->last_name }}, {{ $deployment->supervisor->first_name }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-400 border border-slate-200/50">{{ $deployment->program }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $deployment->school_year }}</td>
                                <td class="px-6 py-4">{{ $deployment->completed_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('coordinator.deployments.show', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $completedDeployments->onEachSide(1)->links() }}
            </div>
        @endif
    </section>

    {{-- ================= CANCELLED ================= --}}
    @if ($cancelledDeployments->total() > 0)
        <section class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm shadow-blue-50/50">
            <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-rose-50 text-rose-500 border-rose-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight">Cancelled</h2>
                    <p class="text-xs font-semibold text-slate-400">Read-only history of cancelled deployments.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Program &amp; Block</th>
                            <th class="px-6 py-4">Partner School</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">School Year</th>
                            <th class="px-6 py-4">Cancelled On</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-400">
                        @foreach ($cancelledDeployments as $deployment)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-600 leading-tight">
                                        {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $deployment->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-extrabold uppercase text-slate-500">{{ $deployment->student->program ?: '—' }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ $deployment->student->block ? 'Block ' . $deployment->student->block : 'No block' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $deployment->partnerSchool->school_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-400 border border-slate-200/50">{{ $deployment->program }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $deployment->school_year }}</td>
                                <td class="px-6 py-4">{{ $deployment->updated_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('coordinator.deployments.show', $deployment) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $cancelledDeployments->onEachSide(1)->links() }}
            </div>
        </section>
    @endif

</main>
</body>
</html>