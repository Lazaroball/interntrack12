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

@include('coordinator.partials.navbar')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Requirement Review</h1>
            <p class="text-sm text-slate-400 mt-0.5">Review student {{ $stage }} requirement submissions and manage eligibility.</p>
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

    {{-- ── Live Search (client-side, current page) + Filters (apply automatically) ── --}}
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
                    block: @js($student->block),
                    progress: @js($student->submission_progress),
                    toReview: @js($student->to_review_count),
                    resubmitted: @js($student->resubmitted_count),
                    statusLabel: @js($stage === 'Internship' ? $student->internship_status_label : $student->field_study_status_label),
                    reviewUrl: @js(route('coordinator.requirements.review.show', ['student' => $student, 'stage' => $stage])),
                },
                @endforeach
            ],
            get filtered() {
                const q = this.search.trim().toLowerCase();
                if (!q) return this.students;
                return this.students.filter(s => {
                    return [s.name, s.firstName, s.middleName, s.lastName, s.studentNumber, s.program, s.block]
                        .filter(Boolean)
                        .some(field => String(field).toLowerCase().includes(q));
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
                           placeholder="Name, student number, program, or block..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                  transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Filters instantly among the students currently loaded on this page.</p>
            </div>

            {{-- Stage, Program, Block and Status apply automatically when changed --}}
            <form method="GET" action="{{ route('coordinator.requirements.review.index') }}" class="flex flex-wrap items-end gap-3">

                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Stage</label>
                    <select name="stage"
                            onchange="this.form.requestSubmit();"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="Field Study" @selected(request('stage', 'Field Study') === 'Field Study')>Field Study</option>
                        <option value="Internship" @selected(request('stage') === 'Internship')>Internship</option>
                    </select>
                </div>

                <div class="min-w-[170px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Program</label>
                    <select name="program"
                            onchange="this.form.elements['block'].value = ''; this.form.requestSubmit();"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="">All Programs</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program }}" @selected(request('program') === $program)>{{ $program }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Block</label>
                    <select name="block"
                            onchange="this.form.requestSubmit();"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="">All Blocks</option>
                        @foreach ($blocks as $block)
                            <option value="{{ $block }}" @selected((string) request('block') === (string) $block)>{{ $block }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[170px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                    <select name="submission"
                            onchange="this.form.requestSubmit();"
                            class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm text-slate-700 outline-none
                                   transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="">All</option>
                        <option value="not_submitted" @selected(request('submission') === 'not_submitted')>Not Submitted</option>
                        <option value="incomplete" @selected(request('submission') === 'incomplete')>Incomplete</option>
                        <option value="all_submitted" @selected(request('submission') === 'all_submitted')>All Submitted</option>
                    </select>
                </div>

                @if (request()->filled('program') || request()->filled('block') || request()->filled('submission') || (request()->filled('stage') && request('stage') !== 'Field Study'))
                    <div class="flex items-center gap-2">
                        <a href="{{ route('coordinator.requirements.review.index') }}"
                           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150">
                            Clear
                        </a>
                    </div>
                @endif
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
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Block</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $stage }}</th>
                            <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Submission</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @if ($students->isEmpty())
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 italic">No students found matching your filters.</td>
                            </tr>
                        @else
                            <template x-for="student in filtered" :key="student.id">
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-800" x-text="student.name"></td>
                                    <td class="px-4 py-3 font-mono text-slate-600" x-text="student.studentNumber || '—'"></td>
                                    <td class="px-4 py-3 text-slate-600" x-text="student.program || '—'"></td>
                                    <td class="px-4 py-3 text-slate-600" x-text="student.block || '—'"></td>
                                    <td class="px-4 py-3 text-slate-600" x-text="student.statusLabel"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <template x-if="student.progress === 'grey'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Not Submitted</span>
                                            </template>
                                            <template x-if="student.progress === 'orange'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Incomplete</span>
                                            </template>
                                            <template x-if="student.progress === 'green'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">All Submitted</span>
                                            </template>
                                            <template x-if="student.progress === 'none'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">—</span>
                                            </template>

                                            <template x-if="student.resubmitted > 0">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-700"
                                                      x-text="student.resubmitted + ' Resubmitted'"></span>
                                            </template>
                                            <template x-if="student.toReview > 0">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700"
                                                      x-text="student.toReview + ' to review'"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="text-blue-600 hover:text-blue-700 font-semibold text-xs" :href="student.reviewUrl">Review</a>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filtered.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 italic">No students found.</td>
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