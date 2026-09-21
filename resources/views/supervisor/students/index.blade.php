{{-- resources/views/supervisor/students/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Students – InternTrack</title>
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

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Students</h1>
            <p class="text-sm text-slate-400 mt-0.5">View and monitor students assigned to you.</p>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 self-start sm:self-auto">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
            {{ $deployments->count() }} Assigned Students
        </span>
    </div>

    {{-- ── Client-side Search ── --}}
    {{-- No backend search/filter exists on this controller, so this is a pure JS filter over the already-scoped list. --}}
    @if ($deployments->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-5">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="searchInput"
                       placeholder="Search by student name, student number, or partner school…"
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm
                              text-slate-700 placeholder-slate-400 outline-none transition-all duration-200
                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
            </div>
        </div>
    @endif

    {{-- ── Student List ── --}}
    @if ($deployments->isNotEmpty())

        {{-- Desktop table --}}
        <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Program</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Partner School</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Deployment Date</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Academic Year</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Semester</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Status</th>
                            <th class="text-right px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="studentTableBody">
                        @foreach ($deployments as $deployment)
                            @php $student = $deployment->student; @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100"
                                data-search="{{ strtolower(($student->first_name ?? '').' '.($student->last_name ?? '').' '.($student->student_number ?? '').' '.optional($deployment->partnerSchool)->school_name) }}">

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
                                            @if ($student && $student->student_number)
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
                                    {{ optional($deployment->partnerSchool)->school_name ?? 'No partner school assigned' }}
                                </td>

                                {{-- Deployment Date --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->deployment_date ? $deployment->deployment_date->format('M d, Y') : '—' }}
                                </td>

                                {{-- Academic Year --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->school_year ?? '—' }}
                                </td>

                                {{-- Semester --}}
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $deployment->semester ?? '—' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php $status = $deployment->status; @endphp
                                    @if ($status === 'deployed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-teal-50 text-teal-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>Deployed
                                        </span>
                                    @elseif ($status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending
                                        </span>
                                    @elseif ($status === 'completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Completed
                                        </span>
                                    @elseif ($status === 'cancelled')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>Cancelled
                                        </span>
                                    @elseif ($status)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ ucfirst($status) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Unknown
                                        </span>
                                    @endif
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
                                        {{-- TODO: point this to the Supervisor's Student Profile route once it exists (e.g. supervisor.students.show) --}}
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
        </div>

        {{-- Mobile stacked cards --}}
        <div class="md:hidden space-y-3" id="studentCardList">
            @foreach ($deployments as $deployment)
                @php $student = $deployment->student; $status = $deployment->status; @endphp
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-4"
                     data-search="{{ strtolower(($student->first_name ?? '').' '.($student->last_name ?? '').' '.($student->student_number ?? '').' '.optional($deployment->partnerSchool)->school_name) }}">

                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center
                                    text-blue-700 text-sm font-bold flex-shrink-0">
                            {{ $student ? strtoupper(substr($student->first_name, 0, 1)) : '?' }}
                        </div>
                        <div class="leading-tight">
                            <p class="font-semibold text-slate-800">
                                {{ $student ? trim($student->first_name . ' ' . $student->last_name) : 'Unknown Student' }}
                            </p>
                            @if ($student && $student->student_number)
                                <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                    {{ $student->student_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-y-2 gap-x-3 text-xs mb-3">
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Program</dt>
                            <dd class="text-slate-700 font-medium">{{ $deployment->program ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Partner School</dt>
                            <dd class="text-slate-700 font-medium">{{ optional($deployment->partnerSchool)->school_name ?? 'No partner school assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Deployment Date</dt>
                            <dd class="text-slate-700 font-medium">{{ $deployment->deployment_date ? $deployment->deployment_date->format('M d, Y') : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Academic Year</dt>
                            <dd class="text-slate-700 font-medium">{{ $deployment->school_year ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Semester</dt>
                            <dd class="text-slate-700 font-medium">{{ $deployment->semester ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-semibold uppercase tracking-wide text-[10px]">Status</dt>
                            <dd class="mt-0.5">
                                @if ($status === 'deployed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>Deployed
                                    </span>
                                @elseif ($status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending
                                    </span>
                                @elseif ($status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Completed
                                    </span>
                                @elseif ($status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>Cancelled
                                    </span>
                                @elseif ($status)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ ucfirst($status) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Unknown
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if (Route::has('supervisor.students.show'))
                        <a href="{{ route('supervisor.students.show', $student->id ?? $deployment->student_id) }}"
                           class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold
                                  bg-blue-50 text-blue-700 border border-blue-200/60
                                  hover:bg-blue-600 hover:text-white hover:border-blue-600
                                  transition-all duration-150">
                            View
                        </a>
                    @else
                        {{-- TODO: point this to the Supervisor's Student Profile route once it exists (e.g. supervisor.students.show) --}}
                        <span class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold
                                     bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                            View
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

    @else
        {{-- ── Empty State ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 text-center py-16 px-6">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-blue-400">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">No Students Assigned</p>
            <p class="text-sm text-slate-400 mt-1">Students assigned to you will appear here.</p>
        </div>
    @endif

</main>

{{-- ── Vanilla Javascript Client-side Search ── --}}
<script>
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        const tableRows = Array.from(document.querySelectorAll('#studentTableBody tr'));
        const cardRows = Array.from(document.querySelectorAll('#studentCardList > div'));
        const allRows = tableRows.concat(cardRows);

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();

            allRows.forEach((row) => {
                const match = row.dataset.search.includes(term);
                row.style.display = match ? '' : 'none';
            });
        });
    }
</script>

</body>
</html>