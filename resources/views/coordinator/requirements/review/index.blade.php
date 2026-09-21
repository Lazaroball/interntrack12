{{-- resources/views/coordinator/requirements/review/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Requirement Review – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

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
            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Students</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Deployments</a>
                <a href="{{ route('coordinator.requirements.review.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Requirements</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Requirement Review</h1>
            <p class="text-sm text-slate-400 mt-0.5">Review student Field Study requirement submissions and manage eligibility.</p>
        </div>
        <a href="{{ route('coordinator.requirements.definitions.index') }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold">
            Manage Definitions
        </a>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Live Search (client-side, current page) + Server-side Filters ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-4"
         x-data="{
            search: '',
            students: [
                @foreach ($students as $student)
                {
                    id: {{ $student->id }},
                    name: @js($student->full_name),
                    firstName: @js($student->first_name),
                    middleName: @js($student->middle_name),
                    lastName: @js($student->last_name),
                    studentNumber: @js($student->student_number),
                    program: @js($student->program),
                    progress: @js($student->submission_progress),
                    reviewUrl: @js(route('coordinator.requirements.review.show', $student)),
                },
                @endforeach
            ],
            get filtered() {
                const q = this.search.trim().toLowerCase();
                if (!q) return this.students;
                return this.students.filter(s => {
                    return [s.name, s.firstName, s.middleName, s.lastName, s.studentNumber, s.program]
                        .filter(Boolean)
                        .some(field => field.toLowerCase().includes(q));
                });
            }
         }">

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Search (this page)</label>
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="search"
                           placeholder="Name, student number, or program..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                  transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Filters instantly among the students currently loaded on this page.</p>
            </div>

            <form method="GET" action="{{ route('coordinator.requirements.review.index') }}" class="flex flex-wrap items-end gap-3">

                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Stage</label>
                    <select name="stage"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="Field Study" @selected(request('stage', 'Field Study') === 'Field Study')>Field Study</option>
                        <option value="Internship" @selected(request('stage') === 'Internship')>Internship</option>
                    </select>
                </div>

                <div class="min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Program</label>
                    <select name="program"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="">All Programs</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program }}" @selected(request('program') === $program)>{{ $program }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[190px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Field Study Status</label>
                    <select name="field_study_status"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="">All Statuses</option>
                        <option value="pending_review" @selected(request('field_study_status') === 'pending_review')>Pending Review</option>
                        <option value="requirements_incomplete" @selected(request('field_study_status') === 'requirements_incomplete')>Requirements Incomplete</option>
                        <option value="requirements_approved" @selected(request('field_study_status') === 'requirements_approved')>Requirements Approved</option>
                        <option value="accepted" @selected(request('field_study_status') === 'accepted')>Accepted for Field Study</option>
                        <option value="rejected" @selected(request('field_study_status') === 'rejected')>Rejected / Needs Correction</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors duration-150">
                        Filter
                    </button>
                    @if (request()->filled('program') || request()->filled('field_study_status') || (request()->filled('stage') && request('stage') !== 'Field Study'))
                        <a href="{{ route('coordinator.requirements.review.index') }}"
                           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student No.</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Program</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $stage }}</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @if ($students->isEmpty())
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400 italic">No students found matching your search/filters.</td>
                            </tr>
                        @else
                            <template x-for="student in filtered" :key="student.id">
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-800" x-text="student.name"></td>
                                    <td class="px-4 py-3 font-mono text-slate-600" x-text="student.studentNumber || '—'"></td>
                                    <td class="px-4 py-3 text-slate-600" x-text="student.program || '—'"></td>
                                    <td class="px-4 py-3 text-slate-400" x-text="'—'"></td>
                                    <td class="px-4 py-3">
                                        <template x-if="student.progress === 'grey'">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Pending Review</span>
                                        </template>
                                        <template x-if="student.progress === 'orange'">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Partial</span>
                                        </template>
                                        <template x-if="student.progress === 'green'">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Complete</span>
                                        </template>
                                        <template x-if="student.progress === 'none'">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">—</span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="text-blue-600 hover:text-blue-700 font-semibold text-xs" :href="student.reviewUrl">Review</a>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filtered.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400 italic">No students found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $students->links() }}

</main>

</body>
</html>