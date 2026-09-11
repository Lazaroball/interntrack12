{{-- resources/views/student/profile.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

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
                        <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50">
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

    {{-- ── Breadcrumb / Back ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Student</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">My Profile</h1>
        </div>

        <a href="{{ route('student.dashboard') }}"
           class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- ── Profile Header Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6 flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-2xl font-extrabold select-none flex-shrink-0">
            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
        </div>
        <div>
            <h2 class="text-lg font-extrabold text-slate-800">{{ $student->full_name }}</h2>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $student->program ?? '—' }}
                @if (!empty($student->program_type))
                    &middot; {{ $student->program_type }}
                @endif
            </p>
            <p class="font-mono text-xs text-slate-400 mt-1">{{ $student->student_number ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Personal Information ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Personal Information</h2>
            </div>
            <div class="divide-y divide-slate-50">
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Full Name</span>
                    <span class="font-semibold text-slate-800">{{ $student->full_name ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">First Name</span>
                    <span class="text-slate-700">{{ $student->first_name ?? '—' }}</span>
                </div>
                @if (!empty($student->middle_name))
                    <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                        <span class="font-semibold text-slate-500">Middle Name</span>
                        <span class="text-slate-700">{{ $student->middle_name }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Last Name</span>
                    <span class="text-slate-700">{{ $student->last_name ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Email</span>
                    <span class="text-slate-700">{{ $student->email ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Mobile Number</span>
                    <span class="text-slate-700">{{ $student->mobile_number ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ── Academic Information ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Academic Information</h2>
            </div>
            <div class="divide-y divide-slate-50">
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Student Number</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $student->student_number ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Program</span>
                    <span class="text-slate-700">{{ $student->program ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Program Type</span>
                    <span class="text-slate-700">{{ $student->program_type ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Year Level</span>
                    <span class="text-slate-700">{{ $student->year_level ? 'Year ' . $student->year_level : '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                    <span class="font-semibold text-slate-500">Block</span>
                    <span class="text-slate-700">{{ $student->block ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Field Study / Internship Status ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Field Study / Internship Status</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Field Study Hours</p>
                <p class="text-lg font-extrabold text-slate-800">{{ $student->field_study_hours ?? 0 }} hrs</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Internship Hours</p>
                <p class="text-lg font-extrabold text-slate-800">{{ $student->internship_hours ?? 0 }} hrs</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Eligibility</p>
                @if ($student->is_eligible)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Eligible</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-200">Not Eligible</span>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Registration Status</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-200 capitalize">
                    {{ $student->registration_status ?? '—' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Student Status</p>
                @if (strtolower((string) $student->status) === 'active')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Active</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 ring-1 ring-slate-200">Inactive</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Account / Enrollment Information ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Account / Enrollment Information</h2>
        </div>
        <div class="divide-y divide-slate-50">
            <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                <span class="font-semibold text-slate-500">Imported Student</span>
                <span class="text-slate-700">{{ $student->is_imported ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                <span class="font-semibold text-slate-500">Late Enrollee</span>
                <span class="text-slate-700">{{ $student->is_late_enrollee ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex items-center justify-between px-6 py-3.5 text-sm">
                <span class="font-semibold text-slate-500">Enrollment Form</span>
                <span class="text-slate-700">
                    {{ !empty($student->enrollment_form_path) ? 'Uploaded' : 'Not Uploaded' }}
                </span>
            </div>
        </div>
    </div>

</main>

</body>
</html>