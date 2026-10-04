{{-- resources/views/student/deployment/select-school.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Partner School Deployment – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@php
    $isLocked = $existingDeployment && !is_null($existingDeployment->supervisor_id);

    // Student cannot choose a school yet (reason supplied by the controller)
    $isBlocked = filled($blockReason ?? null);

    // Inputs are disabled / submit hidden in either situation
    $isInputDisabled = $isLocked || $isBlocked;

    $selectedSchool = $existingDeployment
        ? $partnerSchools->firstWhere('id', $existingDeployment->partner_school_id)
        : null;

    // Only relevant while the deployment can still be changed. Once locked,
    // whatever the coordinator already processed stands, so there is nothing
    // for the student to act on.
    $selectionNoLongerEligible = $existingDeployment
        && !$isLocked
        && $selectedSchool
        && !$selectedSchool->canAcceptNewInterns();
@endphp

@include('student.partials.nav')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student · {{ $program }}</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Partner School Deployment</h1>
            <p class="text-sm text-slate-400 mt-1">Select your preferred partner school for your {{ $program }} deployment.</p>
        </div>

        <a href="{{ route('student.dashboard') }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150 flex items-center gap-1.5 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>
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

    {{-- ── Blocked banner (cannot choose a school yet) ── --}}
    @if ($isBlocked)
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-amber-50 border border-amber-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div class="flex-1">
                <p class="text-sm font-bold text-amber-700">School selection not available yet</p>
                <p class="text-xs text-amber-600 mt-0.5">{{ $blockReason }}</p>
                <a href="{{ route($program === 'Internship' ? 'student.internship.requirements' : 'student.field-study.requirements') }}"
                   class="mt-3 inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors duration-150">
                    Go to Requirements
                </a>
            </div>
        </div>
    @endif

    {{-- ── Student Information ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-5">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">Student Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Name</p>
                <p class="font-semibold text-slate-800">{{ $student->full_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Student Number</p>
                <p class="font-mono font-semibold text-slate-800">{{ $student->student_number ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Program</p>
                <p class="text-slate-700">{{ $student->program ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Year Level</p>
                <p class="text-slate-700">{{ $student->year_level ? 'Year ' . $student->year_level : '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5">Block</p>
                <p class="text-slate-700">{{ $student->block ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Current Selection ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Current Selection</h2>
        </div>

        <div class="p-6">
            @if ($existingDeployment)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Partner School</p>
                        <p class="font-semibold text-slate-800">
                            {{ $selectedSchool?->school_name ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Program</p>
                        <p class="text-slate-700">{{ $existingDeployment->program ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">School Year</p>
                        <p class="text-slate-700">{{ $existingDeployment->school_year ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Semester</p>
                        <p class="text-slate-700">{{ $existingDeployment->semester ?? '—' }}</p>
                    </div>
                    @if (!empty($existingDeployment->status))
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-200 capitalize">
                                {{ $existingDeployment->status }}
                            </span>
                        </div>
                    @endif
                </div>

                @if ($isLocked)
                    <div class="mt-5 flex items-start gap-3 px-4 py-3.5 rounded-xl bg-amber-50 border border-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>
                            <p class="text-sm font-bold text-amber-700">Deployment Processed</p>
                            <p class="text-xs text-amber-600 mt-0.5">
                                Your deployment request has already been processed by the coordinator. You can no longer modify your partner school preference.
                            </p>
                        </div>
                    </div>
                @elseif ($selectionNoLongerEligible)
                    <div class="mt-5 flex items-start gap-3 px-4 py-3.5 rounded-xl bg-rose-50 border border-rose-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>
                            <p class="text-sm font-bold text-rose-700">Your Selected School Is No Longer Available</p>
                            <p class="text-xs text-rose-600 mt-0.5">
                                {{ $selectedSchool?->school_name }} can no longer accept new students right now. Your preference is still saved, but you'll need to choose a different eligible school below and resubmit before the coordinator processes your deployment.
                            </p>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-sm text-slate-400 italic">You have not submitted a partner school preference for this term yet.</p>
            @endif
        </div>
    </div>

    {{-- ── Selection Form ── --}}
    @if ($partnerSchools->isEmpty())

        {{-- ── Empty State ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-slate-400"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
            </div>
            <p class="text-sm font-bold text-slate-700">No Partner Schools Available</p>
            <p class="text-xs text-slate-400 mt-1">There are currently no partner schools registered. Please check again later or contact your coordinator.</p>
        </div>

    @else

        <form method="POST" action="{{ route('student.deployment.store') }}" class="space-y-6">
            @csrf

            <fieldset @if ($isInputDisabled) disabled @endif>

                {{-- ── Available Partner Schools ── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Available Partner Schools</h2>

                    @php
                        $selectedSchoolId = old('partner_school_id', $existingDeployment?->partner_school_id);
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach ($partnerSchools as $school)
                            @php
                                $canSelect = $school->canAcceptNewInterns();
                                $isChecked = (string) $selectedSchoolId === (string) $school->id;

                                // Never strand a student on their existing pick: only disable
                                // schools that are unavailable AND are not the one already
                                // selected. The backend re-checks eligibility on submit
                                // regardless of what the radio state allows here.
                                $disableRadio = $isInputDisabled || (!$canSelect && !$isChecked);

                                $showStaleSelectionNote = $isChecked && !$canSelect && !$isInputDisabled;

                                if (!$school->hasValidMoa()) {
                                    $badgeLabel   = 'Invalid MOA';
                                    $badgeClasses = 'bg-amber-50 text-amber-700 ring-amber-200';
                                } elseif ($school->isFull()) {
                                    $badgeLabel   = 'Full';
                                    $badgeClasses = 'bg-rose-50 text-rose-700 ring-rose-200';
                                } elseif (!$school->accepting_interns) {
                                    $badgeLabel   = 'Closed';
                                    $badgeClasses = 'bg-slate-100 text-slate-600 ring-slate-200';
                                } else {
                                    $badgeLabel   = 'Accepting';
                                    $badgeClasses = 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                                }

                                $moaLabels = [
                                    'active'         => 'Active',
                                    'expired'        => 'Expired',
                                    'renewal_needed' => 'Renewal Needed',
                                ];
                                $moaLabel = $moaLabels[$school->moa_status_display] ?? 'Unknown';
                            @endphp

                            <label class="relative flex gap-3 px-4 py-4 rounded-xl border-2 transition-colors duration-150
                                          {{ $isChecked ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white' }}
                                          {{ $disableRadio ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:border-blue-300' }}">

                                <input
                                    type="radio"
                                    name="partner_school_id"
                                    value="{{ $school->id }}"
                                    class="w-4 h-4 mt-1 text-blue-600 flex-shrink-0"
                                    @checked($isChecked)
                                    @if ($disableRadio) disabled @endif
                                    required
                                >

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ $school->school_name }}</p>
                                            <p class="text-xs font-semibold text-slate-400 mt-0.5">{{ $school->school_type }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold ring-1 whitespace-nowrap {{ $badgeClasses }}">
                                            {{ $badgeLabel }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-500 mt-2 flex items-start gap-1">
                                        <span>📍</span>
                                        <span>{{ $school->address ?? 'Address not provided' }}</span>
                                    </p>

                                    <div class="mt-2 text-xs text-slate-500 space-y-0.5">
                                        @if ($school->contact_person)
                                            <p>Contact: <span class="font-semibold text-slate-700">{{ $school->contact_person }}</span></p>
                                        @endif
                                        @if ($school->contact_number)
                                            <p>Contact No.: <span class="font-semibold text-slate-700">{{ $school->contact_number }}</span></p>
                                        @endif
                                        @if (!$school->contact_person && !$school->contact_number)
                                            <p class="italic text-slate-400">No contact information provided</p>
                                        @endif
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-500">
                                            Slots: <span class="font-semibold text-slate-700">{{ $school->occupied_slots }} occupied</span> / {{ $school->available_slots }} capacity
                                        </span>
                                        <span class="font-semibold {{ $school->remaining_slots > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $school->remaining_slots }} slot{{ $school->remaining_slots === 1 ? '' : 's' }} remaining
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-500 mt-2">
                                        MOA: <span class="font-semibold text-slate-700">{{ $moaLabel }}</span>
                                    </p>

                                    @if ($showStaleSelectionNote)
                                        <p class="text-xs font-semibold text-rose-600 mt-2 pt-2 border-t border-rose-100">
                                            This is your current preference, but it can no longer accept students. Choose another eligible school below.
                                        </p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

            </fieldset>

            {{-- ── Submit ── --}}
            @unless ($isInputDisabled)
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                                   transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        {{ $existingDeployment ? 'Update School Preference' : 'Submit School Preference' }}
                    </button>
                </div>
            @endunless
        </form>

    @endif

</main>

</body>
</html>