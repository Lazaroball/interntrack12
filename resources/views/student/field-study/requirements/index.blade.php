{{-- resources/views/student/field-study/requirements/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $stage }} Requirements – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Student navigation (single source of truth for desktop + mobile)
    |--------------------------------------------------------------------------
    | 'url'    => null means the feature has no route yet (rendered disabled).
    | 'active' => evaluated against the current request.
    */
    $onRequirements = $stage === 'Field Study' && request()->routeIs('student.field-study.requirements*');

    $studentNav = [
        [
            'label'  => 'Dashboard',
            'url'    => route('student.dashboard'),
            'active' => request()->routeIs('student.dashboard'),
        ],
        [
            'label'  => 'Requirements',
            'url'    => route('student.field-study.requirements'),
            'active' => $onRequirements,
        ],
        [
            'label'  => 'Field Study',
            'url'    => route('student.field-study'),
            // Requirements lives under the field-study prefix, so exclude it here
            'active' => request()->routeIs('student.field-study*') && ! request()->routeIs('student.field-study.requirements*'),
        ],
        [
            'label'  => 'Select School',
            'url'    => route('student.deployment.select'),
            'active' => request()->routeIs('student.deployment.*') || request()->is('student/deployment/*'),
        ],
        [
            'label'  => 'Teaching Hours',
            'url'    => url('/student/teaching-hours'),
            'active' => request()->routeIs('student.teaching-hours*') || request()->is('student/teaching-hours*'),
        ],
        [
            'label'  => 'Internship',
            'url'    => $student->internship_status !== 'locked' ? route('student.internship.requirements') : null,
            'active' => request()->routeIs('student.internship.*'),
        ],
    ];

    // Stage-specific status + upload target
    $stageStatus      = $stage === 'Internship' ? $student->internship_status : $student->field_study_status;
    $stageStatusLabel = $stage === 'Internship' ? $student->internship_status_label : $student->field_study_status_label;
    $storeRoute       = $stage === 'Internship'
        ? route('student.internship.requirements.store')
        : route('student.field-study.requirements.store');
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50" x-data="{ mobileOpen: false }">
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

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Student navigation">
                @foreach ($studentNav as $item)
                    @if ($item['url'])
                        <a href="{{ $item['url'] }}"
                           @if ($item['active']) aria-current="page" @endif
                           class="px-3.5 py-1.5 rounded-lg text-sm transition-colors duration-150
                                  {{ $item['active']
                                        ? 'font-semibold text-white bg-blue-600'
                                        : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">{{ $item['label'] }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</span>
                    <span class="text-xs font-semibold text-slate-700">{{ $student->full_name }}</span>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ $student->first_name ?? 'Student' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 shadow-lg shadow-slate-200/60 py-1 z-50">
                        <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
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

                {{-- Mobile menu toggle --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-500 hover:text-slate-800 p-1.5">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile nav panel --}}
        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden pb-4 space-y-1">
            @foreach ($studentNav as $item)
                @if ($item['url'])
                    <a href="{{ $item['url'] }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="block px-3.5 py-2 rounded-lg text-sm
                              {{ $item['active']
                                    ? 'font-semibold text-white bg-blue-600'
                                    : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">{{ $item['label'] }}</span>
                @endif
            @endforeach
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student · {{ $stage }}</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">{{ $stage }} Requirements</h1>
            <p class="text-sm text-slate-400 mt-1">Submit and monitor your {{ $stage }} requirements here.</p>
        </div>

        @if ($stage === 'Internship')
            <a href="{{ route('student.dashboard') }}"
               class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150 flex items-center gap-1.5 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Dashboard
            </a>
        @else
            <a href="{{ route('student.field-study') }}"
               class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150 flex items-center gap-1.5 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Field Study
            </a>
        @endif
    </div>

    {{-- ── Session Messages ── --}}
    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            <p class="font-semibold mb-1">Please correct the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Status Banner ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-2">{{ $stage }} Eligibility Status</h2>
        @php
            $statusStyles = [
                'pending_review'          => 'bg-slate-100 text-slate-600 ring-slate-200',
                'requirements_incomplete' => 'bg-amber-50 text-amber-700 ring-amber-200',
                'requirements_approved'   => 'bg-blue-50 text-blue-700 ring-blue-200',
                'accepted'                => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'rejected'                => 'bg-red-50 text-red-600 ring-red-200',
            ];
            $statusClass = $statusStyles[$stageStatus] ?? $statusStyles['pending_review'];
        @endphp
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold ring-1 {{ $statusClass }}">
            {{ $stageStatusLabel }}
        </span>

        @if ($stage === 'Internship')
            @if ($stageStatus === 'accepted')
                <p class="text-sm text-emerald-600 font-semibold mt-3">Internship access granted. You may now choose an Internship school.</p>
                <a href="{{ route('student.deployment.select') }}"
                   class="mt-3 inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors duration-150">
                    Choose Internship School
                </a>
            @elseif ($stageStatus === 'rejected')
                <p class="text-sm text-slate-500 mt-3">Your Internship requirements need correction. Resubmit the rejected documents.</p>
            @else
                <p class="text-sm text-slate-500 mt-3">Submit all initial Internship requirements below and wait for coordinator approval.</p>
            @endif
        @else
            @if ($stageStatus === 'accepted')
                <p class="text-sm text-emerald-600 font-semibold mt-3">Field Study access granted.</p>
            @elseif ($stageStatus === 'requirements_approved')
                <p class="text-sm text-slate-500 mt-3">All required documents are approved. Waiting for the coordinator to formally accept you into Field Study.</p>
            @else
                <p class="text-sm text-slate-500 mt-3">Field Study is currently locked. Complete the required requirements below and wait for coordinator approval.</p>
            @endif
        @endif
    </div>

    @php
        // Group definitions by phase. Treat null/empty/legacy phase as 'initial'.
        $initialDefinitions = $definitions->filter(function ($definition) {
            return in_array($definition->phase, [null, '', 'initial'], true);
        })->values();

        $ongoingDefinitions = $definitions->filter(function ($definition) {
            return $definition->phase === 'ongoing';
        })->values();

        $isAccepted = $stageStatus === 'accepted';
    @endphp

    {{-- ── Initial Requirements ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Initial Requirements</h2>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse ($initialDefinitions as $definition)
                @php $submission = $submissions->get($definition->id); @endphp
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $definition->name }}</p>
                            <p class="text-xs font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }} mt-0.5">
                                {{ $definition->is_required ? 'Required' : 'Optional' }}
                            </p>
                            @if ($definition->description)
                                <p class="text-xs text-slate-400 mt-1">{{ $definition->description }}</p>
                            @endif
                        </div>

                        <div class="text-right">
                            @if ($submission)
                                @php
                                    $subStyles = [
                                        'pending'  => 'bg-slate-100 text-slate-600 ring-slate-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-600 ring-red-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 capitalize {{ $subStyles[$submission->status] ?? $subStyles['pending'] }}">
                                    {{ $submission->status === 'pending' ? 'Pending Review' : $submission->status }}
                                </span>
                                <p class="text-xs text-slate-400 mt-1.5">
                                    Submitted {{ $submission->submitted_at?->format('M d, Y') }}
                                </p>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-400 ring-1 ring-slate-200">
                                    Not Submitted
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($submission?->status === 'rejected' && $submission?->remarks)
                        <div class="mt-3 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600">
                            <span class="font-bold">Coordinator remarks:</span> {{ $submission->remarks }}
                        </div>
                    @endif

                    <div class="mt-4 flex items-center gap-3 flex-wrap">
                        @if ($submission?->file_path)
                            <a href="{{ route('student.field-study.requirements.file', $submission) }}"
                               target="_blank"
                               class="text-blue-600 hover:text-blue-700 font-semibold text-xs">
                                View Submitted File
                            </a>
                        @endif

                        @if (!$submission || $submission->status === 'rejected')
                            <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="requirement_definition_id" value="{{ $definition->id }}">
                                <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                       class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                              border border-slate-200 rounded-lg">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                                    {{ $submission ? 'Resubmit' : 'Submit' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="p-6 text-sm text-slate-400 italic">No initial requirements are currently available.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Ongoing Requirements (only after acceptance) ── --}}
    @if ($isAccepted)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Ongoing Requirements</h2>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse ($ongoingDefinitions as $definition)
                    @php $submission = $submissions->get($definition->id); @endphp
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $definition->name }}</p>
                                <p class="text-xs font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }} mt-0.5">
                                    {{ $definition->is_required ? 'Required' : 'Optional' }}
                                </p>
                                @if ($definition->description)
                                    <p class="text-xs text-slate-400 mt-1">{{ $definition->description }}</p>
                                @endif
                            </div>

                            <div class="text-right">
                                @if ($submission)
                                    @php
                                        $subStyles = [
                                            'pending'  => 'bg-slate-100 text-slate-600 ring-slate-200',
                                            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                            'rejected' => 'bg-red-50 text-red-600 ring-red-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 capitalize {{ $subStyles[$submission->status] ?? $subStyles['pending'] }}">
                                        {{ $submission->status === 'pending' ? 'Pending Review' : $submission->status }}
                                    </span>
                                    <p class="text-xs text-slate-400 mt-1.5">
                                        Submitted {{ $submission->submitted_at?->format('M d, Y') }}
                                    </p>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-400 ring-1 ring-slate-200">
                                        Not Submitted
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($submission?->status === 'rejected' && $submission?->remarks)
                            <div class="mt-3 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600">
                                <span class="font-bold">Coordinator remarks:</span> {{ $submission->remarks }}
                            </div>
                        @endif

                        <div class="mt-4 flex items-center gap-3 flex-wrap">
                            @if ($submission?->file_path)
                                <a href="{{ route('student.field-study.requirements.file', $submission) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-700 font-semibold text-xs">
                                    View Submitted File
                                </a>
                            @endif

                            @if (!$submission || $submission->status === 'rejected')
                                <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="requirement_definition_id" value="{{ $definition->id }}">
                                    <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                           class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                  file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  border border-slate-200 rounded-lg">
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                                        {{ $submission ? 'Resubmit' : 'Submit' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-400 italic">No ongoing requirements are currently available.</p>
                @endforelse
            </div>
        </div>
    @endif

</main>

</body>
</html>