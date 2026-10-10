{{-- resources/views/supervisor/observations/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Observation Schedules – InternTrack</title>
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Observation Schedules</h1>
            <p class="text-sm text-slate-400 mt-0.5">Manage observation schedules for your assigned students</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('supervisor.observations.create') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                      bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                      shadow-sm shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Schedule Observation
            </a>
        </div>
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

    {{-- ── Observation Table ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

        @if ($observations->isNotEmpty())
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <p class="text-xs text-slate-400">
                    Showing
                    <span class="font-semibold text-slate-700">{{ $observations->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $observations->lastItem() }}</span>
                    of <span class="font-semibold text-slate-700">{{ $observations->total() }}</span> observations
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Date</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Time</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Venue</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Status</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Remarks</th>
                            <th class="text-right px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($observations as $observation)
                            @php
                                $canModify = ! in_array($observation->status, ['completed', 'cancelled'], true);
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100">

                                {{-- Student --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                    text-blue-700 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($observation->student->first_name ?? $observation->student->full_name, 0, 1)) }}
                                        </div>
                                        <div class="leading-tight">
                                            <p class="font-semibold text-slate-800 whitespace-nowrap">
                                                {{ $observation->student->full_name }}
                                            </p>
                                            <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                                {{ $observation->student->student_number }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-4 whitespace-nowrap text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($observation->observation_date)->format('M d, Y') }}
                                </td>

                                {{-- Time --}}
                                <td class="px-4 py-4 whitespace-nowrap text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($observation->observation_time)->format('h:i A') }}
                                </td>

                                {{-- Venue --}}
                                <td class="px-4 py-4 text-slate-700">
                                    {{ $observation->venue }}
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($observation->status === 'completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $observation->status_label }}
                                        </span>
                                    @elseif ($observation->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $observation->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $observation->status_label ?? ucfirst($observation->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Remarks --}}
                                <td class="px-4 py-4 text-slate-500 max-w-xs truncate">
                                    {{ $observation->remarks ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">

                                        <a href="{{ route('supervisor.observations.show', $observation) }}"
                                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                  bg-blue-50 text-blue-700 border border-blue-200/60
                                                  hover:bg-blue-600 hover:text-white hover:border-blue-600
                                                  transition-all duration-150 shadow-sm shadow-blue-100/50">
                                            View
                                        </a>

                                        @if ($canModify)

                                            <a href="{{ route('supervisor.observations.edit', $observation) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                      bg-indigo-50 text-indigo-700 border border-indigo-200/60
                                                      hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                                                      transition-all duration-150 shadow-sm shadow-indigo-100/50">
                                                Edit
                                            </a>

                                            <form action="{{ route('supervisor.observations.complete', $observation) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Mark this observation as completed?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                               bg-emerald-50 text-emerald-700 border border-emerald-200/60
                                                               hover:bg-emerald-600 hover:text-white hover:border-emerald-600
                                                               transition-all duration-150 shadow-sm shadow-emerald-100/50">
                                                    Complete
                                                </button>
                                            </form>

                                            <form action="{{ route('supervisor.observations.cancel', $observation) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Cancel this observation?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                               bg-rose-50 text-rose-600 border border-rose-200/60
                                                               hover:bg-rose-600 hover:text-white hover:border-rose-600
                                                               transition-all duration-150 shadow-sm shadow-rose-100/50">
                                                    Cancel
                                                </button>
                                            </form>

                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            @if ($observations->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $observations->links() }}
                </div>
            @endif

        @else
            {{-- ── Empty State ── --}}
            <div class="text-center py-16 px-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-blue-400">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-medium mb-4">No observations scheduled yet.</p>
                <a href="{{ route('supervisor.observations.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                          bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                          shadow-sm shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Schedule Observation
                </a>
            </div>
        @endif

    </div>
</main>

</body>
</html>