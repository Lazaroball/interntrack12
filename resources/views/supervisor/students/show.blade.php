{{-- resources/views/supervisor/students/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $student->first_name }} {{ $student->last_name }} – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

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

                       <nav class="hidden md:flex items-center gap-1" aria-label="Supervisor navigation">
                <a href="{{ route('supervisor.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                   {{ request()->routeIs('supervisor.dashboard')
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                   {{ request()->routeIs('supervisor.students.*')
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Students
                </a>
                @if (Route::has('supervisor.observations.index'))
                    <a href="{{ route('supervisor.observations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.observations.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Observation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Observation</span>
                @endif
                @if (Route::has('supervisor.evaluations.index'))
                    <a href="{{ route('supervisor.evaluations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.evaluations.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Evaluation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Evaluation</span>
                @endif
                @if (Route::has('supervisor.field-study-requests.index'))
                    <a href="{{ route('supervisor.field-study-requests.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.field-study-requests.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Field Study Requests
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Field Study Requests</span>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supervisor</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 shadow-lg shadow-slate-200/60 py-1 z-50">
                        <a href="#" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
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
            </div>
        </div>
    </div>

</header>


<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400" aria-label="Breadcrumb">
        <a href="{{ route('supervisor.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('supervisor.students.index') }}" class="hover:text-blue-600 transition-colors">Students</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-600 font-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>
    </nav>

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

            {{--
                No quick-action button here (no "View Deployment" / "Deploy This Student").
                A supervisor is monitoring an assignment they already have — the full
                deployment detail is already shown below, so there's nothing to link out to.
            --}}
        </div>
    </div>

    {{-- ── Two-column layout ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Personal + Account Info ── --}}
        <div class="lg:col-span-1 flex flex-col gap-6">

            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-4">Personal Information</h2>
                <dl class="space-y-3">
                    @foreach ([
                        ['label' => 'Full Name',    'value' => trim("{$student->first_name} " . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name)],
                        ['label' => 'Student No.',  'value' => $student->student_number],
                        ['label' => 'Reference No.','value' => $student->reference_number ?? '—'],
                        ['label' => 'Program',      'value' => $student->program],
                        ['label' => 'Program Type', 'value' => ucfirst(str_replace('_',' ',$student->program_type))],
                        ['label' => 'Year Level',   'value' => $student->year_level],
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
                </dl>
            </div>

        </div>

        {{-- RIGHT: Hours + Deployment ── --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Hours Progress ──────────────────────────────── --}}
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

            {{-- Deployment Summary ──────────────────────────── --}}
            {{--
                Uses $deployment — the specific Deployment record where
                supervisor_id belongs to the logged-in supervisor.
                Do NOT use $student->deployment here; that accessor doesn't
                guarantee it's *this* supervisor's assignment.
            --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-500 mb-5">Deployment Information</h2>

                @if ($deployment)

                    {{-- Status banner --}}
                    <div class="mb-5 flex items-center justify-between p-3.5 rounded-xl
                                {{ $deployment->status === 'deployed' ? 'bg-blue-50 border border-blue-200' : ($deployment->status === 'completed' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200') }}">
                        <div class="flex items-center gap-2">
                            @if ($deployment->status === 'deployed')
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <span class="text-sm font-bold text-blue-700">Currently Deployed</span>
                            @elseif ($deployment->status === 'completed')
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-sm font-bold text-emerald-700">Deployment Completed</span>
                            @elseif ($deployment->status === 'pending')
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-sm font-bold text-amber-700">Deployment Pending</span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                <span class="text-sm font-bold text-slate-600">{{ ucfirst($deployment->status ?? 'Unknown') }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-500 font-medium">
                            Deployed on: {{ $deployment->deployment_date ? \Carbon\Carbon::parse($deployment->deployment_date)->format('M d, Y') : '—' }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Partner School</dt>
                            <dd class="text-sm font-semibold text-slate-800">
                                {{ $deployment->partnerSchool?->school_name ?? 'No partner school assigned' }}
                            </dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Academic Year</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $deployment->school_year ?? '—' }}</dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Semester</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $deployment->semester ?? '—' }}</dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Deployment Date</dt>
                            <dd class="text-sm font-semibold text-slate-800">
                                {{ $deployment->deployment_date ? \Carbon\Carbon::parse($deployment->deployment_date)->format('F j, Y') : '—' }}
                            </dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Deployment Status</dt>
                            <dd>
                                @if ($deployment->status === 'deployed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Deployed
                                    </span>
                                @elseif ($deployment->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed
                                    </span>
                                @elseif ($deployment->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                        {{ ucfirst($deployment->status ?? 'Unknown') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Remarks</dt>
                            <dd class="text-sm text-slate-600">{{ $deployment->remarks ?? '—' }}</dd>
                        </div>
                    </dl>

                @else
                    {{-- Should not normally happen if this page is only reached via a supervisor's own deployment list --}}
                    <div class="flex flex-col items-center justify-center gap-4 py-10 text-slate-400">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-slate-300">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-500">No Deployment Record Found</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                This student is not currently linked to a deployment under your supervision.
                            </p>
                        </div>
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