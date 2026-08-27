{{-- resources/views/coordinator/partner-schools/show.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $partnerSchool->school_name }} – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
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
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Partner Schools</a>
                <a href="#" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Deployments</a>
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Coordinator</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Coordinator' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'C', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Coordinator' }}</span>
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


<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Flash Message ── --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Breadcrumb / Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-2">
                <a href="{{ route('coordinator.partner-schools.index') }}" class="hover:text-blue-600 font-medium">Partner Schools</a>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="text-slate-500 font-medium">Profile</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-700 text-lg font-bold flex-shrink-0">
                    {{ strtoupper(substr($partnerSchool->school_name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">{{ $partnerSchool->school_name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @if ($partnerSchool->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('coordinator.partner-schools.edit', $partnerSchool) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
            </a>

            @if ($partnerSchool->status === 'active')
                <form method="POST" action="{{ route('coordinator.partner-schools.deactivate', $partnerSchool) }}"
                      onsubmit="return confirm('Deactivate {{ addslashes($partnerSchool->school_name) }}?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold transition-all duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                        </svg>
                        Deactivate
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('coordinator.partner-schools.activate', $partnerSchool) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-semibold transition-all duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── Summary Stat Pills ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        @foreach ([
            ['label' => 'Students Assigned',     'value' => $assignedStudents->count(),    'dot' => 'bg-blue-500',    'pill' => 'bg-blue-50 text-blue-700'],
            ['label' => 'Supervisors Assigned',  'value' => $assignedSupervisors->count(), 'dot' => 'bg-indigo-500',  'pill' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'Active Deployments',    'value' => $partnerSchool->total_active_deployments ?? 0, 'dot' => 'bg-emerald-500', 'pill' => 'bg-emerald-50 text-emerald-700'],
        ] as $pill)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-5 py-4 flex items-center justify-between
                        hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-200">
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $pill['value'] }}</p>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">{{ $pill['label'] }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $pill['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pill['dot'] }}"></span>
                    {{ $pill['value'] }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── School Information ── --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6 space-y-5 h-fit">
            <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400">School Information</h2>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Address</p>
                <p class="text-sm text-slate-700 leading-snug">{{ $partnerSchool->address ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Contact Person</p>
                <p class="text-sm text-slate-700">{{ $partnerSchool->contact_person ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Contact Number</p>
                <p class="text-sm text-slate-700">{{ $partnerSchool->contact_number ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Email</p>
                <p class="text-sm text-slate-700 break-words">{{ $partnerSchool->email ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Registered On</p>
                <p class="text-sm text-slate-700">{{ optional($partnerSchool->created_at)->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- ── Assigned Students & Supervisors + Deployment History ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Assigned Students --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400">Assigned Students</h2>
                </div>
                @if ($assignedStudents->isNotEmpty())
                    <ul class="divide-y divide-slate-50">
                        @foreach ($assignedStudents as $student)
                            <li class="flex items-center gap-3 px-6 py-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="leading-tight">
                                    <p class="text-sm font-semibold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $student->program ?? 'N/A' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 px-6 py-6">No students assigned yet.</p>
                @endif
            </div>

            {{-- Assigned Supervisors --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400">Assigned Supervisors</h2>
                </div>
                @if ($assignedSupervisors->isNotEmpty())
                    <ul class="divide-y divide-slate-50">
                        @foreach ($assignedSupervisors as $supervisor)
                            <li class="flex items-center gap-3 px-6 py-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($supervisor->first_name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="leading-tight">
                                    <p class="text-sm font-semibold text-slate-800">{{ $supervisor->first_name }} {{ $supervisor->last_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $supervisor->department ?? 'N/A' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 px-6 py-6">No supervisors assigned yet.</p>
                @endif
            </div>

            {{-- Recent Deployments / History --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400">Recent Deployments</h2>
                </div>
                @if ($recentDeployments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                                    <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Supervisor</th>
                                    <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Deployment Date</th>
                                    <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($recentDeployments as $deployment)
                                    <tr class="hover:bg-blue-50/40 transition-colors duration-100">
                                        <td class="px-6 py-3 text-sm text-slate-700 whitespace-nowrap">
                                            {{ $deployment->student?->first_name }} {{ $deployment->student?->last_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">
                                            {{ $deployment->supervisor?->first_name }} {{ $deployment->supervisor?->last_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-500 whitespace-nowrap">
                                            {{ optional($deployment->deployment_date)->format('M d, Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($deployment->status === 'deployed')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Deployed
                                                </span>
                                            @elseif ($deployment->status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-400 px-6 py-6">No deployment history for this school yet.</p>
                @endif
            </div>

        </div>
    </div>

</main>

<footer class="mt-8 border-t border-slate-100 bg-white">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.</p>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-slate-400">InternTrack v1.0 — System Online</span>
        </div>
    </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>