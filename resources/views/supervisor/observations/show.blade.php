{{-- resources/views/supervisor/observations/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Observation Details – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Dashboard
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Students
                </a>
                @if (Route::has('supervisor.observations.index'))
                    <a href="{{ route('supervisor.observations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">
                        Observation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Observation</span>
                @endif
                @if (Route::has('supervisor.evaluations.index'))
                    <a href="{{ route('supervisor.evaluations.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Evaluation</a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Evaluation</span>
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

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Observation Details</h1>
            <p class="text-sm text-slate-400 mt-0.5">Full details of this scheduled observation</p>
        </div>

        <a href="{{ route('supervisor.observations.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-slate-200
                  hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Back to Observations
        </a>
    </div>

    {{-- ── Flash Messages ── --}}
    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Details Card ── --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

            {{-- Status banner --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Status</span>
                @if ($observation->status === 'scheduled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $observation->status_label }}
                    </span>
                @elseif ($observation->status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $observation->status_label }}
                    </span>
                @elseif ($observation->status === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $observation->status_label }}
                    </span>
                @endif
            </div>

            <div class="px-6 py-6 space-y-6">

                {{-- Student --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Student</p>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($observation->student->full_name, 0, 1)) }}
                        </div>
                        <div class="leading-tight">
                            <p class="font-semibold text-slate-800">{{ $observation->student->full_name }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                    {{ $observation->student->student_number }}
                                </span>
                                @if ($observation->student->currentDeployment?->partnerSchool?->school_name)
                                    <span class="text-xs text-slate-400">
                                        {{ $observation->student->currentDeployment->partnerSchool->school_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                {{-- Date / Time / Venue --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">Date</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($observation->observation_date)->format('M d, Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">Time</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($observation->observation_time)->format('h:i A') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">Venue</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $observation->venue }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                {{-- Remarks --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">Remarks</p>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $observation->remarks ?? 'No remarks added.' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- ── Side Card: Meta + Actions ── --}}
        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Record Info</p>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Supervisor</span>
                        <span class="font-semibold text-slate-700">
                            {{ $observation->supervisor->user->first_name ?? 'You' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Scheduled On</span>
                        <span class="font-semibold text-slate-700">
                            {{ $observation->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Last Updated</span>
                        <span class="font-semibold text-slate-700">
                            {{ $observation->updated_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            @if ($observation->status === 'scheduled')
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Actions</p>

                    <a href="{{ route('supervisor.observations.edit', $observation) }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                              bg-indigo-50 text-indigo-700 border border-indigo-200/60 hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                              text-sm font-semibold transition-all duration-150">
                        Edit Observation
                    </a>

                    <form action="{{ route('supervisor.observations.complete', $observation) }}"
                          method="POST"
                          onsubmit="return confirm('Mark this observation as completed?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                       bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-600 hover:text-white hover:border-emerald-600
                                       text-sm font-semibold transition-all duration-150">
                            Mark as Completed
                        </button>
                    </form>

                    <form action="{{ route('supervisor.observations.cancel', $observation) }}"
                          method="POST"
                          onsubmit="return confirm('Cancel this observation?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                       bg-rose-50 text-rose-600 border border-rose-200/60 hover:bg-rose-600 hover:text-white hover:border-rose-600
                                       text-sm font-semibold transition-all duration-150">
                            Cancel Observation
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</main>

</body>
</html>