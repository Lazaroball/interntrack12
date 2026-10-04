{{-- resources/views/student/teaching-hours/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Teaching Hours – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

   @php
       $programLabel = $stage;
   @endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

   @include('student.partials.nav')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
       @include('student.partials.stage-tabs', ['section' => 'hours', 'activeStage' => $stage])
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6">
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Teaching Hours</h1>
        <p class="text-sm text-slate-400 mt-1">{{ $programLabel }} attendance and rendered-hours tracking.</p>
    </div>

    {{-- ── Alerts ── --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <p class="text-sm text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if (!$deployment)
        {{-- ── Empty deployment state ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-10 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-blue-600">
                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1.5">No Active Deployment Yet</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
                You don't have an approved deployment yet. Once your
                deployment has been set up and approved, you'll be able to record
                your teaching hours here.
            </p>
            <a href="{{ route('student.deployment.select') }}"
               class="inline-flex items-center gap-1.5 mt-5 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow shadow-blue-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                Go to Deployment Setup
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    @else

        {{-- ── Progress + Time In/Out ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Hours Progress --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">{{ $programLabel }} Hours</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                        {{ $progressPercent }}%
                    </span>
                </div>

                <div class="flex items-end justify-between mb-2">
                    <span class="text-2xl font-extrabold text-slate-800">{{ $totalHours }}</span>
                    <span class="text-sm text-slate-400">of {{ $requiredHours }} hrs</span>
                </div>

                <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-600 transition-all duration-500"
                         style="width: {{ min(100, max(0, (float) $progressPercent)) }}%"></div>
                </div>

                <p class="text-xs text-slate-400 mt-3">
                    Keep logging your teaching hours until you complete the required {{ $requiredHours }} hours.
                </p>
            </div>

            {{-- Time In / Time Out --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 flex flex-col" x-data="{ submitting: false }">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Time Clock</h2>
                    <span class="text-xs font-semibold text-slate-500">
                        Today's Total:
                        <span class="text-slate-800 font-bold">{{ $todayTotalHours }} hrs</span>
                    </span>
                </div>

                <div class="flex-1 flex flex-col justify-center">
                    @if (!$canLogHours)
                        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div>
                                <p class="text-sm font-bold text-amber-700">Teaching hours are unavailable</p>
                                  <p class="text-xs text-amber-600 mt-0.5">
       @if ($stageCompleted)
           Your {{ $programLabel }} deployment is completed, so no more hours can be logged for this stage.
       @else
           Your {{ $programLabel }} deployment needs to be approved before you can log teaching hours.
       @endif
   </p>
                                <a href="{{ route('student.deployment.select') }}"
                                   class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-amber-700 hover:text-amber-800">
                                    View deployment setup &rarr;
                                </a>
                            </div>
                        </div>
                    @elseif ($activeTodayLog)
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 space-y-4">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-red-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-red-700">Currently Timed In</p>
                                    <p class="text-xs text-red-600">
                                        Session started at
                                        <strong>{{ \Carbon\Carbon::parse($activeTodayLog->time_in)->format('g:i A') }}</strong>.
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('student.teaching-hours.time-out') }}" @submit="submitting = true">
                                @csrf
                                <button type="submit" :disabled="submitting"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow shadow-red-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                                    <span x-show="!submitting">Time Out</span>
                                    <span x-show="submitting" x-cloak>Recording...</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 space-y-4">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600 flex-shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-blue-700">Ready to start a session?</p>
                                    <p class="text-xs text-blue-600">
                                        Record your Time In when you begin your {{ $programLabel }} activities.
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('student.teaching-hours.time-in') }}" @submit="submitting = true">
                                @csrf
                                <button type="submit" :disabled="submitting"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow shadow-blue-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span x-show="!submitting">Time In</span>
                                    <span x-show="submitting" x-cloak>Recording...</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Deployment Information ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-800">Deployment Information</h2>
                    @if ($deployment->program)
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 border border-slate-200/50">
                            {{ $deployment->program }}
                        </span>
                    @endif
                </div>
                @if ($canLogHours)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Approved
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        {{ ucfirst($deployment->status ?? 'Pending') }}
                    </span>
                @endif
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">Partner School</p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $deployment->partnerSchool->school_name ?? 'Not yet assigned' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">Supervisor</p>
                    <p class="text-sm text-slate-700">
                        {{ $deployment->supervisor->full_name ?? 'Not yet assigned' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">School Year</p>
                    <p class="text-sm text-slate-700">{{ $deployment->school_year ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">Semester</p>
                    <p class="text-sm text-slate-700">{{ $deployment->semester ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">Deployment Date</p>
                    <p class="text-sm text-slate-700">
                        {{ optional($deployment->deployment_date)->format('M j, Y') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Today's Sessions ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Today's Sessions</h2>
                <span class="text-xs font-semibold text-slate-500">
                    Today's Total:
                    <span class="text-slate-800 font-bold">{{ $todayTotalHours }} hrs</span>
                </span>
            </div>

            <div class="p-6">
                @if ($todayLogs->isNotEmpty())
                    <ul class="space-y-2.5">
                        @foreach ($todayLogs as $index => $session)
                            <li class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-400">Session {{ $index + 1 }}</p>
                                        <p class="text-sm font-semibold text-slate-800 truncate">
                                            {{ \Carbon\Carbon::parse($session->time_in)->format('g:i A') }}
                                            &rarr;
                                            {{ $session->time_out ? \Carbon\Carbon::parse($session->time_out)->format('g:i A') : '—' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-sm font-semibold text-slate-600 hidden sm:inline">
                                        {{ $session->hours_rendered ?? '—' }} hrs
                                    </span>
                                    @if ($session->is_completed)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            In Progress
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 italic">No sessions recorded today.</p>
                @endif
            </div>
        </div>

        {{-- ── Log History ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Log History</h2>
            </div>

            @if ($history->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-slate-400"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <p class="text-sm text-slate-400 italic">No logs yet. Your recorded sessions will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-400 text-xs uppercase tracking-wide border-b border-slate-100">
                                <th class="py-3 px-6 font-semibold">Date</th>
                                <th class="py-3 px-4 font-semibold">Time In</th>
                                <th class="py-3 px-4 font-semibold">Time Out</th>
                                <th class="py-3 px-4 font-semibold">Hours</th>
                                <th class="py-3 px-6 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($history as $log)
                                <tr class="hover:bg-blue-50/40 transition-colors duration-100">
                                    <td class="py-3 px-6 font-medium text-slate-700 whitespace-nowrap">{{ $log->date->format('M j, Y') }}</td>
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->time_in)->format('g:i A') }}</td>
                                    <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('g:i A') : '—' }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-700 whitespace-nowrap">{{ $log->hours_rendered ?? '—' }}</td>
                                    <td class="py-3 px-6">
                                        @if ($log->is_completed)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    @endif

</main>

</body>
</html>