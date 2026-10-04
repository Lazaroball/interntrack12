{{-- resources/views/student/field-study/requirements/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $stage }} Requirements – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

@php
    // Stage-specific status + upload target
    $stageStatus      = $stage === 'Internship' ? $student->internship_status : $student->field_study_status;
    $stageStatusLabel = $stage === 'Internship' ? $student->internship_status_label : $student->field_study_status_label;
    $storeRoute       = $stage === 'Internship'
        ? route('student.internship.requirements.store')
        : route('student.field-study.requirements.store');

    // Per-document status badges (Not Submitted is "no row yet", so it is not stored)
    $statusMeta = [
        'pending'     => ['label' => 'Pending Review', 'class' => 'bg-blue-50 text-blue-700 ring-blue-200'],
        'resubmitted' => ['label' => 'Resubmitted',    'class' => 'bg-violet-50 text-violet-700 ring-violet-200'],
        'approved'    => ['label' => 'Approved',       'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'rejected'    => ['label' => 'Rejected',       'class' => 'bg-red-50 text-red-600 ring-red-200'],
    ];
    $notSubmittedMeta = ['label' => 'Not Submitted', 'class' => 'bg-slate-50 text-slate-400 ring-slate-200'];

    // Group definitions by phase. Treat null/empty/legacy phase as 'initial'.
    $initialDefinitions = $definitions->filter(function ($definition) {
        return in_array($definition->phase, [null, '', 'initial'], true);
    })->values();

    $ongoingDefinitions = $definitions->filter(function ($definition) {
        return $definition->phase === 'ongoing';
    })->values();

    $stageClosed     = $stageClosed ?? false;
    $showOngoing     = $showOngoing ?? false;
    $canChooseSchool = $canChooseSchool ?? false;

    $phaseSections = [
        [
            'title'       => 'Initial Requirements',
            'definitions' => $initialDefinitions,
            'visible'     => true,
            'empty'       => 'No initial requirements are currently available.',
        ],
        [
            'title'       => 'Ongoing Requirements',
            'definitions' => $ongoingDefinitions,
            // Only after the student is accepted AND deployed in this stage
            'visible'     => $showOngoing,
            'empty'       => 'No ongoing requirements are currently available.',
        ],
    ];
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@include('student.partials.nav')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student · {{ $stage }}</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">{{ $stage }} Requirements</h1>
            <p class="text-sm text-slate-400 mt-1">Submit and monitor your {{ $stage }} requirements here.</p>
        </div>

        @include('student.partials.stage-tabs', ['section' => 'requirements', 'activeStage' => $stage])
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
                @if ($canChooseSchool)
                    <p class="text-sm text-emerald-600 font-semibold mt-3">Internship access granted. You may now choose an Internship school.</p>
                    <a href="{{ route('student.deployment.select') }}"
                       class="mt-3 inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors duration-150">
                        Choose Internship School
                    </a>
                @else
                    <p class="text-sm text-emerald-600 font-semibold mt-3">Internship access granted.</p>
                @endif
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

    {{-- ── Requirement sections (Initial, then Ongoing once accepted and deployed) ── --}}
    @foreach ($phaseSections as $section)
        @if ($section['visible'])
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800">{{ $section['title'] }}</h2>
                </div>

                <div class="divide-y divide-slate-50">
                    @forelse ($section['definitions'] as $definition)
                        @php
                            $submission = $submissions->get($definition->id);
                            $meta       = $submission ? ($statusMeta[$submission->status] ?? $statusMeta['pending']) : $notSubmittedMeta;
                            $isLocked   = $submission?->status === 'approved';
                            $isRejected = $submission?->status === 'rejected';
                            $hasNotes   = filled($submission?->remarks);
                        @endphp

                        <div class="p-6" x-data="{ editing: @js($isRejected) }">
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $meta['class'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                    @if ($submission)
                                        <p class="text-xs text-slate-400 mt-1.5">
                                            {{ $submission->status === 'resubmitted' ? 'Resubmitted' : 'Submitted' }}
                                            {{ $submission->submitted_at?->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Rejected: notes from the coordinator --}}
                            @if ($isRejected)
                                <div class="mt-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600">
                                    <p class="font-bold">Coordinator notes. Please fix this and resubmit.</p>
                                    @if ($hasNotes)
                                        <p class="mt-1 whitespace-pre-line">{{ $submission->remarks }}</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Resubmitted: keep the earlier notes visible for reference --}}
                            @if ($submission?->status === 'resubmitted' && $hasNotes)
                                <div class="mt-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500">
                                    <p class="font-bold text-slate-600">Earlier notes from your coordinator</p>
                                    <p class="mt-1 whitespace-pre-line">{{ $submission->remarks }}</p>
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

                                @if ($isLocked)
                                    {{-- Approved: locked --}}
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        Approved. This file is locked and can no longer be changed.
                                    </span>

                                @elseif ($stageClosed)
                                    {{-- Stage finished: nothing to edit --}}

                                @elseif (! $submission)
                                    {{-- First submission --}}
                                    <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data" class="flex items-center gap-2 flex-wrap">
                                        @csrf
                                        <input type="hidden" name="requirement_definition_id" value="{{ $definition->id }}">
                                        <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                               class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                      file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                      border border-slate-200 rounded-lg">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                                            Submit
                                        </button>
                                    </form>

                                @else
                                    {{-- Pending / Rejected / Resubmitted: the student can change the file --}}
                                    <button type="button" x-show="!editing" @click="editing = true"
                                            class="px-3 py-1.5 rounded-lg border-2 {{ $isRejected ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-slate-200 text-slate-600 hover:bg-slate-100' }} text-xs font-semibold transition-colors duration-150">
                                        {{ $isRejected ? 'Resubmit File' : 'Change File' }}
                                    </button>

                                    <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data"
                                          x-show="editing" x-cloak
                                          @if (! $isRejected)
                                              onsubmit="return confirm('Replace your current file? The old file will be removed.');"
                                          @endif
                                          class="flex items-center gap-2 flex-wrap">
                                        @csrf
                                        <input type="hidden" name="requirement_definition_id" value="{{ $definition->id }}">
                                        <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                               class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                      file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                      border border-slate-200 rounded-lg">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                                            {{ $isRejected ? 'Resubmit' : 'Replace File' }}
                                        </button>
                                        <button type="button" @click="editing = false"
                                                class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 text-xs font-semibold">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if (! $isLocked && ! $stageClosed)
                                <p class="mt-2 text-[11px] text-slate-400">Accepted files: PDF, DOC, DOCX, JPG, PNG (max 5 MB).</p>
                            @endif
                        </div>
                    @empty
                        <p class="p-6 text-sm text-slate-400 italic">{{ $section['empty'] }}</p>
                    @endforelse
                </div>
            </div>
        @endif
    @endforeach

</main>

</body>
</html>