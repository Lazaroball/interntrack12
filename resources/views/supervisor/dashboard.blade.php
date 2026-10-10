{{-- resources/views/supervisor/dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    <title>Supervisor Dashboard – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Portal Zone</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Supervisor Dashboard</h1>
        <p class="text-sm text-slate-400 mt-0.5">Overview of your assigned students and observation progress.</p>
    </div>

    {{-- ── Statistics ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-5 flex flex-col justify-between
                    hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    {{ $assignedStudentsCount }}
                </span>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $assignedStudentsCount }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Assigned Students</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-5 flex flex-col justify-between
                    hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    {{ $pendingObservationsCount ?? 0 }}
                </span>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $pendingObservationsCount ?? 0 }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Pending Observations</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-5 flex flex-col justify-between
                    hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $completedObservationsCount ?? 0 }}
                </span>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $completedObservationsCount ?? 0 }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Completed Observations</p>
            </div>
        </div>

    </div>

    {{-- ── Assigned Students Table ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-800">Assigned Students</h2>
            <p class="text-xs text-slate-400 mt-0.5">Students currently assigned under your supervision.</p>
        </div>

        @if ($assignedStudents->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Program</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Partner School</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Deployment Date</th>
                            <th class="text-right px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($assignedStudents as $deployment)
                            @php $student = $deployment->student; @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100">

                                {{-- Student --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                    text-blue-700 text-xs font-bold flex-shrink-0">
                                            {{ $student ? strtoupper(substr($student->first_name, 0, 1)) : '?' }}
                                        </div>
                                        <div class="leading-tight">
                                            <p class="font-semibold text-slate-800 whitespace-nowrap">
                                                {{ $student ? trim($student->first_name . ' ' . $student->last_name) : 'Unknown Student' }}
                                            </p>
                                            @if ($student)
                                                <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                                    {{ $student->student_number }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Program --}}
                                <td class="px-4 py-4 text-slate-700 font-medium whitespace-nowrap">
                                    {{ $deployment->program ?? '—' }}
                                </td>

                                {{-- Partner School --}}
                                <td class="px-4 py-4 text-slate-700 font-medium whitespace-nowrap">
                                    {{ optional($deployment->partnerSchool)->school_name ?? 'No Partner School' }}
                                </td>

                                {{-- Deployment Date --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->deployment_date ? $deployment->deployment_date->format('M d, Y') : '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if (Route::has('supervisor.students.show'))
                                        <a href="{{ route('supervisor.students.show', $student->id ?? $deployment->student_id) }}"
                                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                  bg-blue-50 text-blue-700 border border-blue-200/60
                                                  hover:bg-blue-600 hover:text-white hover:border-blue-600
                                                  transition-all duration-150 shadow-sm shadow-blue-100/50">
                                            View
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                     bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                            View
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-slate-400 text-sm">No students are currently assigned to you.</p>
            </div>
        @endif
    </div>

    {{-- ── Recent Activity (below the table) ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-800">Recent Activity</h2>
            <p class="text-xs text-slate-400 mt-0.5">Latest updates on your assigned deployments.</p>
        </div>

        @if ($recentActivity->isNotEmpty())
            <ul class="divide-y divide-slate-50">
                @foreach ($recentActivity as $activity)
                    <li class="px-6 py-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-blue-600">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <p class="text-sm font-medium text-slate-700">{{ $activity['text'] }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $activity['time'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-12">
                <p class="text-slate-400 text-sm">No recent activity.</p>
            </div>
        @endif
    </div>

</main>

</body>
</html>