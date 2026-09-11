{{-- resources/views/student/deployment/select-school.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Partner School Deployment – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@php
    $isLocked = $existingDeployment && !is_null($existingDeployment->supervisor_id);
@endphp

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

            <nav class="hidden md:flex items-center gap-1" aria-label="Student navigation">
                <a href="{{ route('student.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Dashboard
                </a>
                <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Requirements</span>
                <a href="{{ route('student.field-study') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Field Study</a>
                <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Internship</span>
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
                    <div x-show="open" @click.outside="open = false"
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
            <a href="{{ route('student.dashboard') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-100">Dashboard</a>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Requirements</span>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Field Study</span>
            <span class="block px-3.5 py-2 rounded-lg text-sm font-medium text-slate-300">Internship</span>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Partner School Deployment</h1>
            <p class="text-sm text-slate-400 mt-1">Select your preferred partner school for your Field Study or Internship deployment.</p>
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
        {{ $partnerSchools->firstWhere('id', $existingDeployment->partner_school_id)?->school_name ?? '—' }}
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
            <p class="text-xs text-slate-400 mt-1">There are currently no partner schools accepting students. Please check again later or contact your coordinator.</p>
        </div>

    @else

        <form method="POST" action="{{ route('student.deployment.store') }}" class="space-y-6">
            @csrf

            <fieldset @if ($isLocked) disabled @endif>

                {{-- ── Available Partner Schools ── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Available Partner Schools</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($partnerSchools as $school)
                            @php
                                $selectedSchoolId = old('partner_school_id', $existingDeployment?->partner_school_id);
                                $isChecked = (string) $selectedSchoolId === (string) $school->id;
                            @endphp
                            <label class="relative flex items-center gap-3 px-4 py-3.5 rounded-xl border-2 cursor-pointer transition-colors duration-150
                                          {{ $isChecked ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-blue-300' }}
                                          {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                                <input
                                    type="radio"
                                    name="partner_school_id"
                                    value="{{ $school->id }}"
                                    class="w-4 h-4 text-blue-600 flex-shrink-0"
                                    @checked($isChecked)
                                    @if ($isLocked) disabled @endif
                                    required
                                >
                                <span class="text-sm font-semibold text-slate-800">{{ $school->school_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── Program Selection ── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 mt-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Program</h2>

                    @php
                        $selectedProgram = old('program', $existingDeployment?->program);
                        $programOptions = ['Field Study', 'Internship'];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md">
                        @foreach ($programOptions as $option)
                            @php $isProgramChecked = $selectedProgram === $option; @endphp
                            <label class="relative flex items-center gap-3 px-4 py-3.5 rounded-xl border-2 cursor-pointer transition-colors duration-150
                                          {{ $isProgramChecked ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-blue-300' }}
                                          {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                                <input
                                    type="radio"
                                    name="program"
                                    value="{{ $option }}"
                                    class="w-4 h-4 text-blue-600 flex-shrink-0"
                                    @checked($isProgramChecked)
                                    @if ($isLocked) disabled @endif
                                    required
                                >
                                <span class="text-sm font-semibold text-slate-800">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </fieldset>

            {{-- ── Submit ── --}}
            @unless ($isLocked)
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