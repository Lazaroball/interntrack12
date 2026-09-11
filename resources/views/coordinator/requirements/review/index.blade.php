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

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50">
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
            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Students</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Deployments</a>
                <a href="{{ route('coordinator.requirements.review.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Requirements</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Requirement Review</h1>
            <p class="text-sm text-slate-400 mt-0.5">Review student Field Study requirement submissions and manage eligibility.</p>
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

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student No.</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Program</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Field Study Status</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $statusStyles = [
                            'pending_review'          => 'bg-slate-100 text-slate-600 ring-slate-200',
                            'requirements_incomplete' => 'bg-amber-50 text-amber-700 ring-amber-200',
                            'requirements_approved'   => 'bg-blue-50 text-blue-700 ring-blue-200',
                            'accepted'                => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                            'rejected'                => 'bg-red-50 text-red-600 ring-red-200',
                        ];
                    @endphp
                    @forelse ($students as $student)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $student->full_name }}</td>
                            <td class="px-4 py-3 font-mono text-slate-600">{{ $student->student_number ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $student->program ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $statusStyles[$student->field_study_status] ?? $statusStyles['pending_review'] }}">
                                    {{ $student->field_study_status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('coordinator.requirements.review.show', $student) }}"
                                   class="text-blue-600 hover:text-blue-700 font-semibold text-xs">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400 italic">No students awaiting review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $students->links() }}

</main>

</body>
</html>