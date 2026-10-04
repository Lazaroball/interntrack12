{{-- resources/views/coordinator/deployments/create.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manual Deployment – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Coordinator navigation (single source of truth for desktop + mobile)
    |--------------------------------------------------------------------------
    */
    $coordinatorNav = [
        [
            'label'  => 'Dashboard',
            'url'    => route('coordinator.dashboard'),
            'active' => request()->routeIs('coordinator.dashboard'),
        ],
        [
            'label'  => 'Students',
            'url'    => route('coordinator.students.index'),
            'active' => request()->routeIs('coordinator.students.*'),
        ],
        [
            'label'  => 'Requirements',
            'url'    => route('coordinator.requirements.review.index'),
            'active' => request()->routeIs('coordinator.requirements.*'),
        ],
        [
            'label'  => 'Partner Schools',
            'url'    => route('coordinator.partner-schools.index'),
            'active' => request()->routeIs('coordinator.partner-schools.*'),
        ],
        [
            'label'  => 'Deployments',
            'url'    => route('coordinator.deployments.index'),
            'active' => request()->routeIs('coordinator.deployments.*'),
        ],
    ];

    // Safe defaults in case the controller doesn't pass these lists
    $studentPrograms = $studentPrograms ?? collect();
    $blocks          = $blocks ?? collect();

    // Default school year: the school year starts in June (e.g. Oct 2026 -> 2026-2027)
    $startYear         = now()->month >= 6 ? now()->year : now()->year - 1;
    $defaultSchoolYear = $startYear . '-' . ($startYear + 1);

    $hasFilter = request()->filled('student_program') || request()->filled('block');
@endphp

<body class="min-h-screen bg-slate-50/50 text-slate-900 antialiased selection:bg-blue-500 selection:text-white">


@include('coordinator.partials.navbar')

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back to Deployments
    </a>

    {{-- Header --}}
    <div class="border-b border-slate-100 pb-5">
        <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-100">Portal Zone</span>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-1">Manual Student Deployment</h1>
        <p class="text-sm text-slate-500 mt-1">Place a student directly with a partner school. Filter by Program and Block to find the right students.</p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <p class="font-bold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ================= STUDENT FILTER ================= --}}
    {{-- Separate GET form (HTML does not allow nesting it inside the deployment form) --}}
    @if (! isset($selectedStudent))
        <form
            method="GET"
            action="{{ route('coordinator.deployments.create') }}"
            class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm shadow-blue-50/50"
        >
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">

                <div>
                    <label for="filter_student_program" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter by Program</label>
                    <select
                        id="filter_student_program"
                        name="student_program"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        x-data
                        x-on:change="$el.form.requestSubmit()"
                    >
                        <option value="">All programs</option>
                        @foreach ($studentPrograms as $studentProgram)
                            <option value="{{ $studentProgram }}" @selected(request('student_program') == $studentProgram)>
                                {{ $studentProgram }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_block" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter by Block</label>
                    <select
                        id="filter_block"
                        name="block"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        x-data
                        x-on:change="$el.form.requestSubmit()"
                    >
                        <option value="">All blocks</option>
                        @foreach ($blocks as $blockOption)
                            <option value="{{ $blockOption }}" @selected(request('block') == $blockOption)>
                                Block {{ $blockOption }}
                            </option>
                        @endforeach
                        <option value="none" @selected(request('block') === 'none')>No block assigned</option>
                    </select>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-3">
                    <span class="text-xs font-semibold text-slate-400">
                        {{ $students->count() }} undeployed student{{ $students->count() === 1 ? '' : 's' }}
                    </span>
                    @if ($hasFilter)
                        <a
                            href="{{ route('coordinator.deployments.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    @endif

    {{-- ================= DEPLOYMENT FORM ================= --}}
    <form
        action="{{ route('coordinator.deployments.store') }}"
        method="POST"
        class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50 space-y-5"
    >
        @csrf

        {{-- Student --}}
        <div>
            <label for="student_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Student <span class="text-rose-500">*</span>
            </label>

            @if (isset($selectedStudent))
                {{-- Pre-selected student (e.g. from the Students page) --}}
                <div class="flex items-center justify-between gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3">
                    <div>
                        <p class="text-sm font-extrabold text-slate-800 leading-tight">
                            {{ $selectedStudent->last_name }}, {{ $selectedStudent->first_name }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $selectedStudent->student_number ?? 'No ID' }}
                            &middot; {{ $selectedStudent->program ?: 'No program' }}
                            &middot;
                            @if ($selectedStudent->block)
                                Block {{ $selectedStudent->block }}
                            @else
                                <span class="font-semibold text-amber-500">No block</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('coordinator.deployments.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">Change</a>
                </div>
                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
            @else
                {{-- Dropdown, grouped by the student's program --}}
                <select
                    name="student_id"
                    id="student_id"
                    required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="">Choose an undeployed student</option>
                    @foreach ($students->groupBy(fn ($s) => $s->program ?: 'No program') as $programName => $group)
                        <optgroup label="{{ $programName }}">
                            @foreach ($group as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                    {{ $student->last_name }}, {{ $student->first_name }}
                                    ({{ $student->student_number ?? 'No ID' }})
                                    &mdash; {{ $student->block ? 'Block ' . $student->block : 'No block' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                @if ($students->isEmpty())
                    <p class="mt-1.5 text-xs font-semibold text-amber-600">No undeployed students match this filter.</p>
                @endif
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Partner School --}}
            <div>
                <label for="partner_school_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Partner School <span class="text-rose-500">*</span>
                </label>
                <select
                    name="partner_school_id"
                    id="partner_school_id"
                    required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="">Select partner school</option>
                    @foreach ($partnerSchools as $school)
                        <option value="{{ $school->id }}" @selected(old('partner_school_id') == $school->id)>
                            {{ $school->school_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Supervisor --}}
            <div>
                <label for="supervisor_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Supervisor <span class="normal-case font-semibold text-slate-300">(optional)</span>
                </label>
                <select
                    name="supervisor_id"
                    id="supervisor_id"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="">Not yet assigned</option>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @selected(old('supervisor_id') == $supervisor->id)>
                            {{ $supervisor->last_name }}, {{ $supervisor->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Deployment Type (field name stays "program" so store() validation keeps working) --}}
            <div>
                <label for="program" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Deployment Type <span class="text-rose-500">*</span>
                </label>
                <select
                    name="program"
                    id="program"
                    required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="">Select type</option>
                    @foreach ($programs as $prog)
                        <option value="{{ $prog }}" @selected(old('program') == $prog)>
                            {{ $prog }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Field Study or Internship. The student's program and block come from their record.</p>
            </div>

            {{-- School Year --}}
            <div>
                <label for="school_year" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    School Year <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="school_year"
                    id="school_year"
                    value="{{ old('school_year', $defaultSchoolYear) }}"
                    required
                    maxlength="20"
                    placeholder="e.g. {{ $defaultSchoolYear }}"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
            </div>

            {{-- Semester --}}
            <div class="sm:col-span-2">
                <label for="semester" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Semester <span class="text-rose-500">*</span>
                </label>
                <select
                    name="semester"
                    id="semester"
                    required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="1st Semester" @selected(old('semester') == '1st Semester')>1st Semester</option>
                    <option value="2nd Semester" @selected(old('semester') == '2nd Semester')>2nd Semester</option>
                    <option value="Summer" @selected(old('semester') == 'Summer')>Summer</option>
                </select>
            </div>
        </div>

        {{-- Remarks --}}
        <div>
            <label for="remarks" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Remarks <span class="normal-case font-semibold text-slate-300">(optional)</span>
            </label>
            <textarea
                name="remarks"
                id="remarks"
                rows="3"
                maxlength="1000"
                placeholder="Optional notes about this deployment"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
            >{{ old('remarks') }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
            <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition">
                Deploy Student Now
            </button>
        </div>
    </form>

</main>
</body>
</html>