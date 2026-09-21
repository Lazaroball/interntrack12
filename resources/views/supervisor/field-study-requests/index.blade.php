{{-- resources/views/supervisor/field-study-requests/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Field Study Completion Requests – InternTrack</title>
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

                       <nav class="hidden md:flex items-center gap-1" aria-label="Supervisor navigation">
                <a href="{{ route('supervisor.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                   {{ request()->routeIs('supervisor.dashboard')
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                   {{ request()->routeIs('supervisor.students.*')
                        ? 'bg-blue-600 text-white font-semibold'
                        : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    Students
                </a>
                @if (Route::has('supervisor.observations.index'))
                    <a href="{{ route('supervisor.observations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.observations.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Observation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Observation</span>
                @endif
                @if (Route::has('supervisor.evaluations.index'))
                    <a href="{{ route('supervisor.evaluations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.evaluations.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Evaluation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Evaluation</span>
                @endif
                @if (Route::has('supervisor.field-study-requests.index'))
                    <a href="{{ route('supervisor.field-study-requests.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150
                       {{ request()->routeIs('supervisor.field-study-requests.*')
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        Field Study Requests
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Field Study Requests</span>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supervisor</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 shadow-lg shadow-slate-200/60 py-1 z-50">
                        <a href="#" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
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
            </div>
        </div>
    </div>

</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Field Study Completion Requests</h1>
        <p class="text-sm text-slate-400 mt-0.5">Review and act on completion requests submitted by your assigned students.</p>
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

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student No.</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Requested Hours</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Request Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($requests as $fieldStudyRequest)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $fieldStudyRequest->student->full_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                {{ $fieldStudyRequest->student->student_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $fieldStudyRequest->requested_hours }} hrs
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                {{ $fieldStudyRequest->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusStyles = [
                                        'pending'  => 'bg-slate-100 text-slate-600 ring-slate-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-600 ring-red-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 capitalize {{ $statusStyles[$fieldStudyRequest->status] ?? $statusStyles['pending'] }}">
                                    {{ $fieldStudyRequest->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($fieldStudyRequest->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('supervisor.field-study-requests.approve', $fieldStudyRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors duration-150">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('supervisor.field-study-requests.reject', $fieldStudyRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition-colors duration-150">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No action available</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400 italic">
                                No pending Field Study completion requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>