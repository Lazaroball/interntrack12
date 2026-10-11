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
@include('student.partials.nav')

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