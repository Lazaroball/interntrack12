{{-- resources/views/coordinator/deployments/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deployment Details – InternTrack</title>
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

    $student = $deployment->student;
    $school  = $deployment->partnerSchool;

    $isCancelled  = $deployment->status === 'cancelled';
    $isOpen       = ! $deployment->completed_at && ! $isCancelled;
    $isInternship = $deployment->program === 'Internship';

    // Workflow state: Pending -> Deployed -> Completed (or Cancelled)
    if ($isCancelled) {
        $stateLabel = 'Cancelled';
        $stateBadge = 'bg-rose-50 text-rose-600 border border-rose-100';
        $stateIcon  = 'bg-rose-50 text-rose-600 border-rose-100';
        $stateNote  = 'Cancelled on ' . $deployment->updated_at->format('M d, Y');
    } elseif ($deployment->completed_at) {
        $stateLabel = 'Completed';
        $stateBadge = 'bg-slate-100 text-slate-500 border border-slate-200';
        $stateIcon  = 'bg-slate-100 text-slate-500 border-slate-200';
        $stateNote  = 'Completed on ' . $deployment->completed_at->format('M d, Y');
    } elseif ($deployment->supervisor_id) {
        $stateLabel = 'Student Deployed';
        $stateBadge = 'bg-blue-50 text-blue-600 border border-blue-100';
        $stateIcon  = 'bg-blue-50 text-blue-600 border-blue-100';
        $stateNote  = $deployment->deployment_date
            ? 'Deployed on ' . $deployment->deployment_date->format('M d, Y')
            : 'Approved by the coordinator';
    } else {
        $stateLabel = 'Pending Deployment';
        $stateBadge = 'bg-amber-50 text-amber-600 border border-amber-100';
        $stateIcon  = 'bg-amber-50 text-amber-600 border-amber-100';
        $stateNote  = 'Requested on ' . $deployment->created_at->format('M d, Y');
    }
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

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back to Deployments
    </a>

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

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase {{ $stateBadge }}">
                {{ $stateLabel }}
            </span>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                {{ $student->first_name }} {{ $student->last_name }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Student No. {{ $student->student_number }}
                &middot; {{ $student->program ?: 'No program' }}
                &middot; {{ $student->block ? 'Block ' . $student->block : 'No block' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($isOpen)
                <a href="{{ route('coordinator.deployments.edit', $deployment) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white transition border border-slate-200/50 hover:border-blue-600">
                    Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Current state summary --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm shadow-blue-50/50 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center border {{ $stateIcon }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800">{{ $stateLabel }}</p>
            <p class="text-xs text-slate-400">{{ $stateNote }}</p>
        </div>
    </div>

    {{-- Internship pass status (Internship deployments only) --}}
    @if ($isInternship)
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Internship Pass Status</h2>

            <dl class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm font-semibold text-slate-600">Coordinator</dt>
                    <dd class="text-sm font-semibold {{ $student->internship_coordinator_passed_at ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $student->internship_coordinator_passed_at
                            ? 'Passed on ' . $student->internship_coordinator_passed_at->format('M d, Y')
                            : 'Not yet passed' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm font-semibold text-slate-600">Supervisor</dt>
                    <dd class="text-sm font-semibold {{ $student->internship_supervisor_passed_at ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $student->internship_supervisor_passed_at
                            ? 'Passed on ' . $student->internship_supervisor_passed_at->format('M d, Y')
                            : 'Not yet passed' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
                    <dt class="text-sm font-bold text-slate-700">Result</dt>
                    <dd>
                        @if ($student->is_internship_valid)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">VALID</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200">Not valid yet</span>
                        @endif
                    </dd>
                </div>
            </dl>

            <p class="mt-4 text-xs text-slate-400">
                Internship is completed only when BOTH the coordinator and the supervisor have passed.
            </p>
            <a href="{{ route('coordinator.students.show', $student) }}" class="mt-2 inline-block text-xs font-bold text-blue-600 hover:underline">
                Open student page
            </a>
        </div>
    @endif

    {{-- Deployment details --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Deployment Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Partner School</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $school?->school_name ?? '—' }}</dd>
                @if ($school?->school_type)
                    <dd class="text-xs text-slate-400">{{ $school->school_type }}</dd>
                @endif
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-800">
                    @if ($deployment->supervisor)
                        {{ $deployment->supervisor->last_name }}, {{ $deployment->supervisor->first_name }}
                    @else
                        <span class="font-medium text-slate-400">Not yet assigned</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Type</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-500 border border-slate-200/50">{{ $deployment->program ?? '—' }}</span>
                </dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $stateLabel }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">School Year</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $deployment->school_year ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Semester</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $deployment->semester ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Requested On</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $deployment->created_at->format('M d, Y') }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Deployment Date</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ optional($deployment->deployment_date)->format('M d, Y') ?? '—' }}</dd>
            </div>

            @if ($deployment->completed_at)
                <div>
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Completed On</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $deployment->completed_at->format('M d, Y') }}</dd>
                </div>
            @endif

            <div class="sm:col-span-2">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Remarks</dt>
                <dd class="mt-1 text-sm text-slate-600 whitespace-pre-line">
                    @if (!empty($deployment->remarks))
                        {{ $deployment->remarks }}
                    @else
                        <span class="italic text-slate-400">No remarks.</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- Student information --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Student Information</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Name</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Student Number</dt>
                <dd class="mt-1 text-sm font-mono font-semibold text-slate-800">{{ $student->student_number ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $student->program ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Year Level</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $student->year_level ? 'Year ' . $student->year_level : '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Block</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if (!empty($student->block))
                        Block {{ $student->block }}
                    @else
                        <span class="font-semibold text-amber-500">No block assigned</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Student Type</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $student->program_type ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Field Study Status</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $student->field_study_status_label }}</dd>
            </div>

            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Internship Status</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $student->internship_status_label }}</dd>
            </div>
        </dl>
    </div>

</main>
</body>
</html>