{{-- resources/views/supervisor/observations/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Observation Details – InternTrack</title>
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<x-supervisor-nav />

@php
    $canModify = ! in_array($observation->status, ['completed', 'cancelled'], true);
@endphp

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Observation Details</h1>
        <p class="text-sm text-slate-400 mt-0.5">Full details of this scheduled observation</p>
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
                @if ($observation->status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $observation->status_label }}
                    </span>
                @elseif ($observation->status === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $observation->status_label }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $observation->status_label ?? ucfirst($observation->status) }}
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

            @if ($canModify)
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