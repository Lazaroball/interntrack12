{{-- resources/views/coordinator/students/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Management – InternTrack</title>
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<x-coordinator-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Student Management</h1>
            <p class="text-sm text-slate-400 mt-0.5">Monitor and manage all student interns</p>
        </div>

        {{-- Import Action Button --}}
        <div class="flex items-center gap-2.5">
            <a href="{{ route('coordinator.students.import') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                      bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                      shadow-sm shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="17 8 12 3 7 8" />
                    <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
                Import Students
            </a>
        </div>
    </div>

    {{-- ── Summary Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Total Students',      'value' => $counts['total'],       'dot' => 'bg-blue-500',    'pill' => 'bg-blue-50 text-blue-700'],
            ['label' => 'Field Study',         'value' => $counts['field_study'], 'dot' => 'bg-indigo-500',  'pill' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'Internships',         'value' => $counts['internship'],  'dot' => 'bg-sky-500',     'pill' => 'bg-sky-50 text-sky-700'],
            ['label' => 'Deployed Students',   'value' => $counts['deployed'],    'dot' => 'bg-teal-500',    'pill' => 'bg-teal-50 text-teal-700'],
        ] as $pill)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-4 py-4 flex flex-col justify-between
                        hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $pill['pill'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $pill['dot'] }}"></span>
                        {{ $pill['value'] }}
                    </span>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $pill['value'] }}</p>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5 truncate">{{ $pill['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Auto-Filtering Search Form ── --}}
    <form id="filterForm" method="GET" action="{{ route('coordinator.students.index') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-5">

        {{-- Search row --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" id="searchInput"
                       placeholder="Search by student name or student number…"
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm
                              text-slate-700 placeholder-slate-400 outline-none transition-all duration-200
                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
            </div>

            {{-- Sort --}}
            <select name="sort" onchange="autoSubmit()"
                    class="px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                           outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 min-w-[160px]">
                <option value="newest"         {{ request('sort','newest') === 'newest'         ? 'selected' : '' }}>Newest First</option>
                <option value="oldest"         {{ request('sort') === 'oldest'                  ? 'selected' : '' }}>Oldest First</option>
                <option value="alphabetical"   {{ request('sort') === 'alphabetical'            ? 'selected' : '' }}>Alphabetical</option>
                <option value="student_number" {{ request('sort') === 'student_number'          ? 'selected' : '' }}>Student Number</option>
            </select>
        </div>

        {{-- Filter row --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- Program Filter --}}
            <select name="program" onchange="autoSubmit()"
                    class="px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                           outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                <option value="">All Programs</option>
                @foreach ($programs as $p)
                    <option value="{{ $p }}" {{ request('program') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>

            {{-- Program Type Filter --}}
            <select name="program_type" onchange="autoSubmit()"
                    class="px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                           outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                <option value="">All Types</option>
                @foreach ($programTypes as $pt)
                    <option value="{{ $pt }}" {{ request('program_type') === $pt ? 'selected' : '' }}>{{ $pt }}</option>
                @endforeach
            </select>

            {{-- Block Filter --}}
            <select name="block" onchange="autoSubmit()"
                    class="px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                           outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                <option value="">All Blocks</option>
                @foreach ($blocks as $b)
                    <option value="{{ $b }}" {{ request('block') == $b ? 'selected' : '' }}>Block {{ $b }}</option>
                @endforeach
            </select>

            {{-- Deployment Status --}}
            <select name="deployment_status" onchange="autoSubmit()"
                    class="px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                           outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                <option value="">All Deployment</option>
                <option value="not_deployed" {{ request('deployment_status') === 'not_deployed' ? 'selected' : '' }}>Not Deployed</option>
                <option value="pending"      {{ request('deployment_status') === 'pending'      ? 'selected' : '' }}>Pending Deployment</option>
                <option value="deployed"     {{ request('deployment_status') === 'deployed'     ? 'selected' : '' }}>Deployed</option>
                <option value="completed"    {{ request('deployment_status') === 'completed'    ? 'selected' : '' }}>Completed</option>
            </select>

            @if (request()->hasAny(['search','sort','program','program_type','block','deployment_status']))
                <a href="{{ route('coordinator.students.index') }}"
                   class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100
                          text-slate-600 text-sm font-semibold transition-all duration-150 ml-auto">
                    Clear Filters
                </a>
            @endif
        </div>
    </form>

    {{-- ── Student Table ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

        {{-- Table meta --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <p class="text-xs text-slate-400">
                Showing
                <span class="font-semibold text-slate-700">{{ $students->firstItem() ?? 0 }}</span>–<span class="font-semibold text-slate-700">{{ $students->lastItem() ?? 0 }}</span>
                of <span class="font-semibold text-slate-700">{{ $students->total() }}</span> students
            </p>
        </div>

        @if ($students->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Program</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Type</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Hours</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Deployment</th>
                            <th class="text-right px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($students as $student)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100">

                                {{-- Student Details --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                    text-blue-700 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        </div>
                                        <div class="leading-tight">
                                            <p class="font-semibold text-slate-800 whitespace-nowrap">
                                                {{ $student->first_name }}
                                                {{ $student->middle_name ? strtoupper(substr($student->middle_name,0,1)).'.' : '' }}
                                                {{ $student->last_name }}
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">
                                                    {{ $student->student_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Program --}}
                                <td class="px-4 py-4 text-slate-700 font-medium">
                                    {{ $student->program }}
                                    @if ($student->year_level || $student->block)
                                        <span class="text-xs text-slate-400 block font-normal">
                                            Year {{ $student->year_level ?? '—' }} · Block {{ $student->block ?? '—' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Type Badge --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if (strtolower($student->program_type) === 'field study' || strtolower($student->program_type) === 'field_study')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>Field Study
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>Internship
                                        </span>
                                    @endif
                                </td>

                                {{-- Hours Progress --}}
                                <td class="px-4 py-4 whitespace-nowrap leading-snug">
                                    @if (strtolower($student->program_type) === 'field study' || strtolower($student->program_type) === 'field_study')
                                        <span class="text-sm font-bold text-slate-800">{{ $student->field_study_hours ?? 0 }}</span>
                                        <span class="text-xs text-slate-400 font-semibold">/ {{ $fieldStudyTarget }} hrs</span>
                                        <span class="block text-[10px] text-slate-400 font-medium">Field Study Target</span>
                                    @else
                                        <span class="text-sm font-bold text-slate-800">{{ $student->internship_hours ?? 0 }}</span>
                                        <span class="text-xs text-slate-400 font-semibold">/ {{ $internshipTarget }} hrs</span>
                                        <span class="block text-[10px] text-slate-400 font-medium">Internship Target</span>
                                    @endif
                                </td>

                                {{-- Deployment Badge --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($student->deployment)
                                        @php $ds = $student->deployment->status; @endphp
                                        @if ($ds === 'deployed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-teal-50 text-teal-700 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>Deployed
                                            </span>
                                        @elseif ($ds === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending Deployment
                                            </span>
                                        @elseif ($ds === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Completed
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Not Deployed
                                        </span>
                                    @endif
                                </td>

                                {{-- Action Buttons --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('coordinator.students.show', $student->id) }}"
                                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                  bg-blue-50 text-blue-700 border border-blue-200/60
                                                  hover:bg-blue-600 hover:text-white hover:border-blue-600
                                                  transition-all duration-150 shadow-sm shadow-blue-100/50">
                                            View
                                        </a>

                                        @if ($student->deployment)
                                            <a href="{{ route('coordinator.deployments.show', $student->deployment->id) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                      bg-indigo-50 text-indigo-700 border border-indigo-200/60
                                                      hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                                                      transition-all duration-150 shadow-sm shadow-indigo-100/50">
                                                Deployment
                                            </a>
                                        @else
                                            <a href="{{ route('coordinator.deployments.index', ['search' => $student->student_number]) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold
                                                      bg-indigo-50 text-indigo-700 border border-indigo-200/60
                                                      hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                                                      transition-all duration-150 shadow-sm shadow-indigo-100/50">
                                                Deploy
                                            </a>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-slate-400 text-sm">No students match the configured filter criteria.</p>
            </div>
        @endif

        {{-- Pagination footer --}}
        @if ($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $students->links() }}
            </div>
        @endif

    </div>
</main>

{{-- ── Auto-Submit Behavior ── --}}
<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('filterForm');

    // Debounced search trigger (avoids continuous calls while typing)
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            autoSubmit();
        }, 400);
    });

    function autoSubmit() {
        form.submit();
    }
</script>

</body>
</html>