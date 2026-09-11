{{-- resources/views/coordinator/requirements/review/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Review {{ $student->full_name }} – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

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
        <a href="{{ route('coordinator.requirements.review.index') }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold">
            Back to List
        </a>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            {{ session('success') }}
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
                <p class="text-xs font-semibold text-slate-500 mb-1">Current Field Study Status</p>
                <p class="text-lg font-extrabold text-slate-800">{{ $student->field_study_status_label }}</p>
            </div>

            <div class="flex items-center gap-3">
                @if ($student->field_study_status !== 'accepted')
                    <form method="POST" action="{{ route('coordinator.requirements.review.accept', $student) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors duration-150">
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
            </div>
        </div>
    </div>

    {{-- ── Requirement Checklist ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Field Study Requirements</h2>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse ($definitions as $definition)
                @php $submission = $submissions->get($definition->id); @endphp
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $definition->name }}</p>
                            <p class="text-xs font-semibold {{ $definition->is_required ? 'text-red-500' : 'text-slate-400' }} mt-0.5">
                                {{ $definition->is_required ? 'Required' : 'Optional' }}
                            </p>
                        </div>

                        @if ($submission)
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 capitalize
                                    {{ $submission->status === 'approved' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                       : ($submission->status === 'rejected' ? 'bg-red-50 text-red-600 ring-red-200'
                                       : 'bg-slate-100 text-slate-600 ring-slate-200') }}">
                                    {{ $submission->status === 'pending' ? 'Pending Review' : $submission->status }}
                                </span>
                                <p class="text-xs text-slate-400 mt-1.5">Submitted {{ $submission->submitted_at?->format('M d, Y') }}</p>
                            </div>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-400 ring-1 ring-slate-200">
                                Not Submitted
                            </span>
                        @endif
                    </div>

                    @if ($submission)
                        <div class="mt-4 flex items-center gap-4 flex-wrap">
                            <a href="{{ route('coordinator.requirements.review.file', $submission) }}"
                               target="_blank"
                               class="text-blue-600 hover:text-blue-700 font-semibold text-xs">
                                View File
                            </a>

                            @if ($submission->status === 'pending')
                                <form method="POST" action="{{ route('coordinator.requirements.review.update-submission', $submission) }}"
                                      class="flex items-center gap-2 flex-wrap">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="remarks" placeholder="Remarks (optional for approval, recommended for rejection)"
                                           class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs w-64 focus:ring-2 focus:ring-blue-300 outline-none">
                                    <button type="submit" name="status" value="approved"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                                        Approve
                                    </button>
                                    <button type="submit" name="status" value="rejected"
                                            class="px-3 py-1.5 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold">
                                        Reject
                                    </button>
                                </form>
                            @elseif ($submission->remarks)
                                <p class="text-xs text-slate-500"><span class="font-bold">Remarks:</span> {{ $submission->remarks }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <p class="p-6 text-sm text-slate-400 italic">No Field Study requirements have been configured yet.</p>
            @endforelse
        </div>
    </div>

</main>

</body>
</html>