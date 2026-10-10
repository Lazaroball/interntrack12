{{-- resources/views/admin/dashboard.blade.php --}}
<x-admin-layout title="Dashboard">
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Admin</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">System Dashboard</h1>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ now()->format('l, F j, Y') }} &mdash; Academic Year {{ now()->year }}–{{ now()->year + 1 }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700
                      active:bg-blue-800 text-white text-sm font-semibold transition-colors duration-150
                      shadow shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create User
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border-2 border-slate-200
                      hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-sm
                      font-semibold transition-all duration-150 focus:outline-none focus:ring-2
                      focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Manage Users
            </a>
        </div>
    </div>

    {{-- Statistics cards --}}
    <section aria-label="Statistics overview">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

            @php
                $cards = [
                    ['key' => 'total_students',        'label' => 'Student Interns',    'bg' => 'bg-blue-50',    'stroke' => '#2563eb',
                     'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                    ['key' => 'total_supervisors',     'label' => 'Supervisors',        'bg' => 'bg-sky-50',     'stroke' => '#0284c7',
                     'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>'],
                    ['key' => 'total_coordinators',    'label' => 'Coordinators',       'bg' => 'bg-indigo-50',  'stroke' => '#6366f1',
                     'icon' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],

                    ['key' => 'pending_requests',      'label' => 'Pending Requests',   'bg' => 'bg-amber-50',   'stroke' => '#d97706',
                     'icon' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
                    ['key' => 'completed_internships', 'label' => 'Completed',          'bg' => 'bg-blue-50',    'stroke' => '#2563eb',
                     'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-5
                            flex flex-col gap-3 hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                    <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="{{ $card['stroke'] }}" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-5 h-5">{!! $card['icon'] !!}</svg>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $stats[$card['key']] ?? 0 }}</p>
                        <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach

        </div>
    </section>

    {{-- Quick actions + overview --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6" aria-label="Quick actions and system info">

        {{-- Quick actions --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Quick Actions</h2>
                <span class="text-[10px] font-bold tracking-widest uppercase text-blue-500">Admin</span>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.users.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                          text-white transition-colors duration-150 focus:outline-none focus:ring-2
                          focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Create User</p>
                        <p class="text-[11px] text-blue-200">Add coordinator or supervisor</p>
                    </div>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 text-slate-700
                          transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0284c7"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Manage Users</p>
                        <p class="text-[11px] text-slate-400">View all accounts</p>
                    </div>
                </a>

                <a href="{{ route('admin.student-records.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 text-slate-700
                          transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/>
                            <line x1="10" y1="12" x2="14" y2="12"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Student Records</p>
                        <p class="text-[11px] text-slate-400">Archive, restore or delete students</p>
                    </div>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 text-slate-700
                          transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="9 11 12 14 22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Review Requests</p>
                        <p class="text-[11px] text-slate-400">{{ $stats['pending_requests'] ?? 0 }} pending approval</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Overview + session --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Deployment Overview</h2>
                    <span class="text-xs font-semibold text-slate-400">A.Y. {{ now()->year }}–{{ now()->year + 1 }}</span>
                </div>

                @php
                    $bars = [
                        ['label' => 'Field Study',        'done' => $stats['field_study_deployed'] ?? 0, 'total' => $stats['field_study_total'] ?? 0, 'color' => 'bg-blue-500',    'note' => 'placement rate'],
                        ['label' => 'Internship',         'done' => $stats['internship_deployed'] ?? 0,  'total' => $stats['internship_total'] ?? 0,  'color' => 'bg-sky-400',     'note' => 'placement rate'],
                        ['label' => 'Overall Completion', 'done' => $stats['completed_internships'] ?? 0,'total' => $stats['total_students'] ?? 0,    'color' => 'bg-emerald-500', 'note' => 'completion rate'],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($bars as $bar)
                        @php $pct = $bar['total'] > 0 ? round(($bar['done'] / $bar['total']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-slate-600">{{ $bar['label'] }}</span>
                                <span class="text-xs font-bold text-slate-800">{{ $bar['done'] }} / {{ $bar['total'] }}</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $bar['color'] }} rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $pct }}% {{ $bar['note'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4">Session Information</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Logged in as</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? '' }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Role</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-semibold text-blue-700">Admin</span>
                        </span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Last Login</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()?->last_login_at ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Login Time</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()?->last_login_at ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('g:i A') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Recent Activity (admin only) --}}
    <section aria-label="Recent admin activity">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Recent Activity</h2>
                <p class="text-xs text-slate-400 mt-0.5">Latest actions performed by administrators</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            @foreach(['#', 'Admin', 'Module', 'Action', 'Details', 'Date'] as $col)
                                <th class="text-left px-4 first:px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentActivity as $index => $activity)
                            @php $adminName = $activity->user?->name ?? 'Unknown'; @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors duration-100">
                                <td class="px-6 py-3.5 text-xs font-semibold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center
                                                    justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($adminName, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-800">{{ $adminName }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-xs font-medium text-slate-500">{{ $activity->module }}</td>
                                <td class="px-4 py-3.5 text-sm text-slate-600">{{ $activity->action }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $activity->description ?: '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-400 whitespace-nowrap">
                                    {{ $activity->created_at?->format('M d, Y · g:i A') ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-400">No recent activity found</p>
                                    <p class="text-xs text-slate-300 mt-1">Admin actions will appear here once they are performed.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <p class="text-xs text-slate-400">
                    Showing <span class="font-semibold text-slate-600">{{ $recentActivity->count() }}</span>
                    recent {{ \Illuminate\Support\Str::plural('entry', $recentActivity->count()) }}
                </p>
            </div>
        </div>
    </section>

</div>
</x-admin-layout>