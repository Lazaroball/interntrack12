{{--
    TARGET PATH: resources/views/student/teaching-hours/index.blade.php
    Uses <x-app-layout> to match your project's Blade component layout.
    DESIGN UPDATE ONLY — all variables, routes, and Time In/Time Out
    logic are unchanged from the previous version.
--}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Teaching Hours
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Field Study attendance and rendered-hours tracking
            </p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 space-y-6">

        {{-- ── Alerts ── --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
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
            <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-blue-600">
                        <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1.5">No Field Study Deployment Yet</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                    You don't have an approved Field Study deployment yet. Once your
                    deployment has been set up and approved, you'll be able to record
                    your teaching hours here.
                </p>
                <a href="{{ route('student.deployment.select') }}"
                   class="inline-flex items-center gap-1.5 mt-5 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow shadow-blue-200 transition-colors duration-150">
                    Go to Deployment Setup
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        @else

            {{-- ── 1. Deployment Information Card ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Deployment Information</h2>
                    @if ($canLogHours)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[11px] font-bold border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Approved
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ ucfirst($deployment->status ?? 'Pending') }}
                        </span>
                    @endif
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Partner School</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $deployment->partnerSchool->school_name ?? 'Not yet assigned' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Supervisor</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $deployment->supervisor->full_name ?? 'Not yet assigned' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">School Year</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $deployment->school_year ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Semester</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $deployment->semester ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Deployment Date</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ optional($deployment->deployment_date)->format('M j, Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── 2. 600-Hour Progress Card (main visual focus) ── --}}
            <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-600 to-blue-700 p-6 sm:p-8 shadow-lg shadow-blue-200/50">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-100">Field Study Progress</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-white/15 text-white">
                        {{ $progressPercent }}%
                    </span>
                </div>

                <div class="flex items-end gap-2 mt-2 mb-4">
                    <span class="text-3xl sm:text-4xl font-extrabold text-white leading-none">{{ $totalHours }}</span>
                    <span class="text-sm sm:text-base text-blue-100 pb-1">/ {{ $requiredHours }} hours</span>
                </div>

                <div class="w-full h-3.5 rounded-full bg-white/20 overflow-hidden">
                    <div class="h-full rounded-full bg-white transition-all duration-500"
                         style="width: {{ min(100, max(0, (float) $progressPercent)) }}%"></div>
                </div>

                <p class="text-xs sm:text-sm text-blue-100 mt-4">
                    Keep logging your teaching hours until you complete the required {{ $requiredHours }} hours.
                </p>
            </div>

            {{-- ── 3 & 4. Today's Sessions + Time In / Time Out ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden" x-data="{ submitting: false }">

                <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Today's Sessions</h2>
                    <span class="text-xs font-semibold text-gray-500">
                        Today's Total:
                        <span class="text-gray-800 font-bold">{{ $todayTotalHours }} hrs</span>
                    </span>
                </div>

                <div class="p-6">
                    @if ($todayLogs->isNotEmpty())
                        <ul class="space-y-2.5 mb-6">
                            @foreach ($todayLogs as $index => $session)
                                <li class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 flex-shrink-0">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-400">Session {{ $index + 1 }}</p>
                                            <p class="text-sm font-semibold text-gray-800 truncate">
                                                {{ \Carbon\Carbon::parse($session->time_in)->format('g:i A') }}
                                                &rarr;
                                                {{ $session->time_out ? \Carbon\Carbon::parse($session->time_out)->format('g:i A') : '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        <span class="text-sm font-semibold text-gray-600 hidden sm:inline">
                                            {{ $session->hours_rendered ?? '—' }} hrs
                                        </span>
                                        @if ($session->is_completed)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                In Progress
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Time In / Time Out action area --}}
                    @if (!$canLogHours)
                        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div>
                                <p class="text-sm font-bold text-amber-800">Teaching hours are unavailable</p>
                                <p class="text-sm text-amber-700 mt-0.5">
                                    Your Field Study deployment needs to be approved before you can log teaching hours.
                                </p>
                                <a href="{{ route('student.deployment.select') }}"
                                   class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-amber-800 hover:text-amber-900">
                                    View deployment setup &rarr;
                                </a>
                            </div>
                        </div>
                    @elseif ($activeTodayLog)
                        <div class="rounded-xl border border-red-100 bg-red-50/60 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-800">Currently Timed In</p>
                                <p class="text-sm text-gray-600 mt-0.5">
                                    Your current session started at
                                    <strong>{{ \Carbon\Carbon::parse($activeTodayLog->time_in)->format('g:i A') }}</strong>.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('student.teaching-hours.time-out') }}"
                                  @submit="submitting = true" class="flex-shrink-0">
                                @csrf
                                <button type="submit" :disabled="submitting"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold px-6 py-3 rounded-xl text-sm shadow shadow-red-200 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                                    <span x-show="!submitting">Time Out</span>
                                    <span x-show="submitting">Recording...</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-800">Ready to start a session?</p>
                                <p class="text-sm text-gray-600 mt-0.5">
                                    Record your Time In when you begin your Field Study activities.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('student.teaching-hours.time-in') }}"
                                  @submit="submitting = true" class="flex-shrink-0">
                                @csrf
                                <button type="submit" :disabled="submitting"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold px-6 py-3 rounded-xl text-sm shadow shadow-blue-200 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span x-show="!submitting">Time In</span>
                                    <span x-show="submitting">Recording...</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── 5. Log History ── --}}
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Log History</h2>
                </div>

                @if ($history->isEmpty())
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 mx-auto rounded-xl bg-gray-100 flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-400"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">No logs yet. Your recorded sessions will appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                                    <th class="py-3 px-6 font-semibold">Date</th>
                                    <th class="py-3 px-4 font-semibold">Time In</th>
                                    <th class="py-3 px-4 font-semibold">Time Out</th>
                                    <th class="py-3 px-4 font-semibold">Hours</th>
                                    <th class="py-3 px-6 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($history as $log)
                                    <tr class="hover:bg-gray-50 transition-colors duration-100 odd:bg-white even:bg-gray-50/40">
                                        <td class="py-3 px-6 font-medium text-gray-700 whitespace-nowrap">{{ $log->date->format('M j, Y') }}</td>
                                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->time_in)->format('g:i A') }}</td>
                                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('g:i A') : '—' }}
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-gray-700 whitespace-nowrap">{{ $log->hours_rendered ?? '—' }}</td>
                                        <td class="py-3 px-6">
                                            @if ($log->is_completed)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100">
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
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

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>