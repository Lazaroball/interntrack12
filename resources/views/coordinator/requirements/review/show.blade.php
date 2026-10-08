{{-- resources/views/coordinator/requirements/review/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Review {{ $student->full_name }} – InternTrack</title>
    {{-- Alpine comes from app.js (the list page relies on it too), so it is not loaded again here. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        /* Two-column layout: checklist + viewer (does not depend on the Tailwind build) */
        .review-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1.5rem; align-items: start; }

        /* File viewer: hidden on phone until opened, full-screen when open */
        .file-viewer { display: none; flex-direction: column; background: #fff; overflow: hidden; }
        .file-viewer.is-open { display: flex; position: fixed; inset: 0; z-index: 50; }

        @media (min-width: 1024px) {
            .review-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            /* Desktop: always visible, sticky beside the checklist */
            .file-viewer,
            .file-viewer.is-open {
                display: flex;
                position: sticky;
                inset: auto;
                top: 1rem;
                z-index: 10;
                height: calc(100vh - 2rem);
                border: 1px solid #f1f5f9;
                border-radius: 1rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            .file-viewer-close { display: none; }
        }
    </style>
    @include('partials.file-viewer-script')
</head>

@php
    $stageStatus      = $stage === 'Internship' ? $student->internship_status : $student->field_study_status;
    $stageStatusLabel = $stage === 'Internship'
        ? $student->internship_status_label
        : $student->field_study_status_label;

    // Accept is only allowed when every required INITIAL document is approved.
    $initialRequiredApproved = $definitions
        ->filter(fn ($d) => in_array($d->phase, [null, '', 'initial'], true) && $d->is_required)
        ->every(fn ($d) => optional($submissions->get($d->id))->status === 'approved');

    // Group definitions by phase: initial first, then ongoing (null/legacy phase = initial).
    $groupedDefinitions = $definitions
        ->groupBy(fn ($d) => $d->phase === 'ongoing' ? 'ongoing' : 'initial')
        ->sortKeys();

    $internshipLocked = $student->internship_status === 'locked';

    // Documents waiting for the coordinator (pending + resubmitted) in this stage
    $needsReviewCount = $definitions
        ->filter(fn ($d) => in_array(optional($submissions->get($d->id))->status, ['pending', 'resubmitted'], true))
        ->count();

    $statusMeta = [
        'pending'     => ['label' => 'Pending Review', 'class' => 'bg-blue-50 text-blue-700 ring-blue-200'],
        'resubmitted' => ['label' => 'Resubmitted',    'class' => 'bg-violet-50 text-violet-700 ring-violet-200'],
        'approved'    => ['label' => 'Approved',       'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'rejected'    => ['label' => 'Rejected',       'class' => 'bg-red-50 text-red-600 ring-red-200'],
    ];
    $notSubmittedMeta = ['label' => 'Not Submitted', 'class' => 'bg-slate-50 text-slate-400 ring-slate-200'];

    $stageDone = $stage === 'Internship' && $student->internship_completed_at;
@endphp

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">{{ $student->full_name }}</h1>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $student->student_number ?? '—' }} &middot; {{ $student->program ?? '—' }}
                @if ($student->year_level) &middot; Year {{ $student->year_level }} @endif
                @if ($student->block) &middot; Block {{ $student->block }} @endif
            </p>
        </div>
        <a href="{{ route('coordinator.requirements.review.index', ['stage' => $stage]) }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold">
            Back to List
        </a>
    </div>

    {{-- ── Stage switcher ── --}}
    <div class="flex items-center gap-2">
        <a href="{{ route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Field Study']) }}"
           class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors duration-150
                  {{ $stage === 'Field Study' ? 'bg-blue-600 text-white' : 'border-2 border-slate-200 hover:bg-slate-100 text-slate-600' }}">
            Field Study
        </a>

        @if ($internshipLocked)
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold border-2 border-slate-100 text-slate-300 cursor-not-allowed">
                Internship
            </span>
        @else
            <a href="{{ route('coordinator.requirements.review.show', ['student' => $student, 'stage' => 'Internship']) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors duration-150
                      {{ $stage === 'Internship' ? 'bg-blue-600 text-white' : 'border-2 border-slate-200 hover:bg-slate-100 text-slate-600' }}">
                Internship
            </a>
        @endif
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

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Current Status + Accept/Reject ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Current {{ $stage }} Status</p>
                <p class="text-lg font-extrabold text-slate-800">{{ $stageStatusLabel }}</p>
                @if ($needsReviewCount > 0)
                    <p class="text-xs font-semibold text-blue-600 mt-1">{{ $needsReviewCount }} document{{ $needsReviewCount === 1 ? '' : 's' }} waiting for your review</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if ($stage === 'Internship')

                    @if ($student->internship_status !== 'accepted' && ! $student->internship_completed_at)
                        <form method="POST" action="{{ route('coordinator.requirements.review.accept-internship', $student) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    @disabled(! $initialRequiredApproved)
                                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors duration-150
                                           {{ $initialRequiredApproved
                                                ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                                : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                                Accept for Internship
                            </button>
                        </form>
                    @endif

                    @if ($student->internship_status !== 'rejected' && ! $student->internship_completed_at)
                        <form method="POST" action="{{ route('coordinator.requirements.review.reject-internship', $student) }}"
                              onsubmit="return confirm('Reject this student\'s Internship eligibility?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl border-2 border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold transition-colors duration-150">
                                Reject
                            </button>
                        </form>
                    @endif

                @else

                    @if ($student->field_study_status !== 'accepted')
                        <form method="POST" action="{{ route('coordinator.requirements.review.accept', $student) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    @disabled(! $initialRequiredApproved)
                                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors duration-150
                                           {{ $initialRequiredApproved
                                                ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                                : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                                Accept for Field Study
                            </button>
                        </form>
                    @endif

                    @if ($student->field_study_status !== 'rejected')
                        <form method="POST" action="{{ route('coordinator.requirements.review.reject', $student) }}"
                              onsubmit="return confirm('Reject this student\'s Field Study eligibility?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl border-2 border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold transition-colors duration-150">
                                Reject
                            </button>
                        </form>
                    @endif

                @endif
            </div>
        </div>

        @if ($stageStatus !== 'accepted' && ! $stageDone && ! $initialRequiredApproved)
            <p class="mt-3 text-xs font-semibold text-amber-600">
                All required initial documents must be approved first.
            </p>
        @endif
    </div>

    {{-- ── Checklist (left) + File viewer (right on desktop, full-screen on phone) ── --}}
    <div class="review-grid">

        {{-- Requirement Checklist --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">{{ $stage }} Requirements</h2>
            </div>

            <div>
                @forelse ($groupedDefinitions as $phase => $group)
                    <div class="px-6 pt-5 pb-1 {{ ! $loop->first ? 'border-t border-slate-100' : '' }}">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            {{ $phase === 'ongoing' ? 'Ongoing Requirements' : 'Initial Requirements' }}
                        </p>
                    </div>

                    <div class="divide-y divide-slate-50">
                        @foreach ($group as $definition)
                            @php
                                $submission = $submissions->get($definition->id);
                                $meta       = $submission ? ($statusMeta[$submission->status] ?? $statusMeta['pending']) : $notSubmittedMeta;
                                $status     = $submission?->status;
                                $hasNotes   = filled($submission?->remarks);

                                $viewerPayload = $submission && $submission->file_path ? [
                                    'title'       => $definition->name,
                                    'name'        => $submission->display_name,
                                    'ext'         => $submission->file_extension,
                                    'url'         => route('coordinator.requirements.review.file', $submission),
                                    'downloadUrl' => route('coordinator.requirements.review.file', ['requirement' => $submission, 'download' => 1]),
                                    'updateUrl'   => route('coordinator.requirements.review.update-submission', $submission),
                                    'canReview'   => in_array($status, ['pending', 'resubmitted'], true),
                                    'status'      => $meta['label'],
                                ] : null;
                            @endphp

                            <div class="p-6" x-data="{ reopen: false, notes: '' }">
                                <div class="flex items-start justify-between gap-4 flex-wrap">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $definition->name }}</p>
                                        <p class="text-xs font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }} mt-0.5">
                                            {{ $definition->is_required ? 'Required' : 'Optional' }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $meta['class'] }}">
                                            {{ $meta['label'] }}
                                        </span>
                                        @if ($submission)
                                            <p class="text-xs text-slate-400 mt-1.5">
                                                {{ $status === 'resubmitted' ? 'Resubmitted' : 'Submitted' }}
                                                {{ $submission->submitted_at?->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if ($submission)
                                    {{-- File name the student uploaded --}}
                                    <p class="mt-2 text-xs text-slate-500 break-all">{{ $submission->display_name }}</p>

                                    {{-- Notes already sent to the student --}}
                                    @if ($hasNotes && in_array($status, ['rejected', 'resubmitted'], true))
                                        <div class="mt-3 px-4 py-3 rounded-xl border text-xs
                                                    {{ $status === 'rejected' ? 'bg-red-50 border-red-200 text-red-600' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                                            <p class="font-bold">
                                                {{ $status === 'rejected' ? 'Notes sent to the student' : 'Your earlier notes (student has resubmitted)' }}
                                            </p>
                                            <p class="mt-1 whitespace-pre-line">{{ $submission->remarks }}</p>
                                        </div>
                                    @endif

                                    <div class="mt-4 flex items-center gap-3 flex-wrap">
                                        @if ($viewerPayload)
                                            <button type="button"
                                                    @click="$dispatch('open-file', @js($viewerPayload))"
                                                    class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                                                Show
                                            </button>
                                            <a href="{{ $viewerPayload['downloadUrl'] }}"
                                               class="px-3 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition-colors duration-150">
                                                Download
                                            </a>
                                        @endif

                                        @if ($status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                Approved. Locked for the student.
                                            </span>
                                            <button type="button" @click="reopen = !reopen"
                                                    class="text-xs font-semibold text-red-600 hover:underline">
                                                Reopen for correction
                                            </button>

                                        @elseif ($status === 'rejected')
                                            <span class="text-xs font-semibold text-slate-400">Waiting for the student to resubmit.</span>

                                        @else
                                            <span class="text-xs text-slate-400">Open the file to approve it or request a resubmission.</span>
                                        @endif
                                    </div>

                                    {{-- Reopen form: approved --}}
                                    @if ($status === 'approved')
                                        <form method="POST"
                                              action="{{ route('coordinator.requirements.review.update-submission', $submission) }}"
                                              x-show="reopen" x-cloak
                                              onsubmit="return confirm('Reopen this approved document? The student will be able to replace the file.');"
                                              class="mt-4 space-y-3">
                                            @csrf
                                            @method('PATCH')

                                            <textarea name="remarks" x-model="notes" rows="2" maxlength="1000"
                                                      placeholder="Tell the student what needs to be corrected"
                                                      class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 placeholder-slate-400
                                                             focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none"></textarea>

                                            <div class="flex items-center gap-2">
                                                <button type="submit" name="status" value="rejected"
                                                        class="px-3 py-1.5 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition-colors duration-150">
                                                    Reopen and Request Resubmission
                                                </button>
                                                <button type="button" @click="reopen = false"
                                                        class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 text-xs font-semibold">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-400 italic">No {{ $stage }} requirements have been configured yet.</p>
                @endforelse
            </div>
        </div>

        {{-- File viewer: sticky side panel on desktop, full-screen on phone --}}
        <aside x-data="{
                    open: false,
                    f: null,
                    notes: '',
                    show(file) {
                        this.f = file;
                        this.notes = '';
                        this.open = true;
                        this.$nextTick(() => window.renderFilePreview(this.$refs.box, file));
                    },
                    close() { this.open = false; }
               }"
               @open-file.window="show($event.detail)"
               @keydown.escape.window="close()"
               x-effect="document.body.classList.toggle('overflow-hidden', open && window.innerWidth < 1024)"
               class="file-viewer"
               :class="{ 'is-open': open }">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border-b border-slate-100">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate" x-text="f ? f.title : 'File viewer'"></p>
                    <p class="text-xs text-slate-400 truncate" x-show="f" x-text="f ? f.name : ''"></p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a x-show="f" :href="f ? f.downloadUrl : '#'"
                       class="px-3 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-semibold">
                        Download
                    </a>
                    <button type="button" @click="close()"
                            class="file-viewer-close px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-semibold">
                        Close
                    </button>
                </div>
            </div>

            {{-- Preview --}}
            <div class="flex-1 overflow-auto bg-slate-100">
                <p x-show="!f" class="p-8 text-center text-sm text-slate-400 italic">
                    Select Show on a requirement to view the file here.
                </p>
                <div x-ref="box" class="min-h-full"></div>
            </div>

            {{-- Approve / Request Resubmission --}}
            <form x-show="f && f.canReview" x-cloak method="POST" :action="f ? f.updateUrl : '#'"
                  class="p-4 border-t border-slate-100 bg-white space-y-3">
                @csrf
                @method('PATCH')

                <textarea name="remarks" x-model="notes" rows="2" maxlength="1000"
                          placeholder="Notes for the student"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 placeholder-slate-400
                                 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none"></textarea>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="submit" name="status" value="approved"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors duration-150">
                        Approve
                    </button>
                    <button type="submit" name="status" value="rejected"
                            class="px-4 py-2 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition-colors duration-150">
                        Request Resubmission
                    </button>
                </div>
            </form>
        </aside>
    </div>

</main>

</body>
</html>