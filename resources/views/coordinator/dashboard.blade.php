{{-- resources/views/coordinator/dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coordinator Dashboard – InternTrack</title>
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<x-coordinator-nav />

{{-- ════════════════════════════════════════════════════════════
     MAIN LAYOUT WRAPPER
════════════════════════════════════════════════════════════ --}}
<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Welcome Banner Card ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="h-1.5 w-full bg-gradient-to-r from-blue-500 via-blue-400 to-sky-400"></div>

        <div class="px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 shadow shadow-blue-200
                            flex items-center justify-center text-white text-xl font-extrabold flex-shrink-0">
                    {{ strtoupper(substr($coordinator->first_name ?? $user->first_name ?? 'C', 0, 1)) }}
                </div>

                <div>
                    <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">
                        @php
                            $hour = now()->hour;
                            $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                        @endphp
                        {{ $greeting }}, Coordinator
                    </p>
                    <h1 class="text-xl font-extrabold text-slate-800 leading-tight">
                        {{ $coordinator->first_name ?? $user->first_name ?? 'Coordinator' }}
                        {{ $coordinator->middle_name ? strtoupper(substr($coordinator->middle_name, 0, 1)) . '.' : '' }}
                        {{ $coordinator->last_name ?? $user->last_name ?? '' }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if ($coordinator?->department)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-blue-400">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                                {{ $coordinator->department }}
                            </span>
                        @endif
                        @if ($coordinator?->position)
                            <span class="text-slate-300" aria-hidden="true">·</span>
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $coordinator->position }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-start sm:items-end gap-1">
                <p class="text-sm font-semibold text-slate-700">
                    {{ now()->format('l, F j, Y') }}
                </p>
                <p class="text-xs text-slate-400">
                    Academic Year {{ now()->year }}–{{ now()->year + 1 }}
                </p>
                <span class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                             bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Active Session
                </span>
            </div>
        </div>
    </div>

    {{-- ── Statistics Cards (static, not clickable) ──────────────── --}}
    @php
        $cards = [
            [
                'key' => 'total_students', 'label' => 'Student Interns',
                'bg' => 'bg-blue-50', 'stroke' => '#2563eb',
                'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            ],
            [
                'key' => 'partner_schools', 'label' => 'Partner Schools',
                'bg' => 'bg-sky-50', 'stroke' => '#0284c7',
                'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
            ],
            [
                'key' => 'pending_deployments', 'label' => 'Pending Deployments',
                'bg' => 'bg-amber-50', 'stroke' => '#d97706',
                'icon' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
            ],
            [
                'key' => 'active_deployments', 'label' => 'Student Deployed',
                'bg' => 'bg-emerald-50', 'stroke' => '#059669',
                'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            ],
            [
                'key' => 'completed_deployments', 'label' => 'Completed',
                'bg' => 'bg-blue-50', 'stroke' => '#2563eb',
                'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            ],
        ];
    @endphp

    <section aria-label="Statistics overview">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach ($cards as $card)
                <div class="col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50
                            p-5 flex flex-col gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="{{ $card['stroke'] }}" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-5 h-5">{!! $card['icon'] !!}</svg>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-slate-800 leading-none">
                            {{ $stats[$card['key']] ?? 0 }}
                        </p>
                        <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                            {{ $card['label'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ── Quick Actions + Deployment Overview ───────────────────── --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6" aria-label="Quick actions and deployment overview">

        {{-- Quick Actions --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 flex flex-col justify-between gap-4">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Quick Actions</h2>
                    <span class="text-[10px] font-bold tracking-widest uppercase text-blue-500">Coordinator</span>
                </div>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('coordinator.students.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700
                              active:bg-blue-800 text-white transition-colors duration-150
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="leading-tight text-left">
                            <p class="text-sm font-semibold">View Students</p>
                            <p class="text-[11px] text-blue-200">Browse all interns</p>
                        </div>
                    </a>

                    <a href="{{ route('coordinator.partner-schools.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                              hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                              text-slate-700 transition-all duration-150
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <div class="leading-tight text-left">
                            <p class="text-sm font-semibold">Partner Schools</p>
                            <p class="text-[11px] text-slate-400">Manage placements</p>
                        </div>
                    </a>

                    <a href="{{ route('coordinator.deployments.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                              hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                              text-slate-700 transition-all duration-150
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </div>
                        <div class="leading-tight text-left">
                            <p class="text-sm font-semibold">Deploy Students</p>
                            <p class="text-[11px] text-slate-400">
                                {{ $stats['pending_deployments'] }} pending approval
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Deployment Overview --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-sm font-bold text-slate-800 tracking-tight">Deployment Overview</h2>
                        <span class="text-xs font-semibold text-slate-400">
                            A.Y. {{ now()->year }}–{{ now()->year + 1 }}
                        </span>
                    </div>

                    @php
                        $total = max($stats['active_deployments'] + $stats['pending_deployments'] + $stats['completed_deployments'], 1);
                        $bars = [
                            ['label' => 'Pending Deployments', 'value' => $stats['pending_deployments'],   'color' => 'bg-amber-500'],
                            ['label' => 'Student Deployed',    'value' => $stats['active_deployments'],    'color' => 'bg-emerald-500'],
                            ['label' => 'Completed',           'value' => $stats['completed_deployments'], 'color' => 'bg-blue-500'],
                        ];
                    @endphp

                    <div class="space-y-5">
                        @foreach ($bars as $bar)
                            @php $pct = round(($bar['value'] / $total) * 100); @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-semibold text-slate-600">{{ $bar['label'] }}</span>
                                    <span class="text-xs font-bold text-slate-800">{{ $bar['value'] }}</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $bar['color'] }} rounded-full transition-all duration-500"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $pct }}% of total deployments</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Recent Activity ───────────────────────────────────────── --}}
    <section aria-label="Recent activity">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Recent Activity</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Latest actions taken across the system</p>
                </div>
                <span class="text-[10px] font-bold tracking-widest uppercase text-blue-500">Last 10</span>
            </div>

            @if ($recentActivity->isEmpty())
                <p class="text-sm text-slate-400 py-6 text-center">No activity recorded yet.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentActivity as $log)
                        @php
                            $iconStyle = match ($log->action) {
                                'Created'  => ['bg-emerald-50', '#059669', 'M12 5v14M5 12h14'],
                                'Updated'  => ['bg-amber-50', '#d97706', 'M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7 M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z'],
                                'Deleted'  => ['bg-red-50', '#dc2626', 'M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14z'],
                                'Restored' => ['bg-blue-50', '#2563eb', 'M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8'],
                                default    => ['bg-slate-50', '#64748b', 'M12 8v4l3 3'],
                            };
                        @endphp
                        <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="w-8 h-8 rounded-lg {{ $iconStyle[0] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="{{ $iconStyle[1] }}"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                    <path d="{{ $iconStyle[2] }}"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ $log->user->first_name ?? 'System' }} {{ $log->user->last_name ?? '' }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ strtolower($log->action) }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">
                                        {{ $log->module }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $log->description }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                            </div>

                            @if ($log->is_restorable ?? false)
                                <form method="POST" action="{{ route('coordinator.partner-schools.restore', $log->subject_id) }}" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-blue-200 bg-blue-50
                                                   text-blue-600 text-xs font-semibold hover:bg-blue-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                            <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                                            <path d="M3 3v5h5"/>
                                        </svg>
                                        Undo
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</main>

</body>
</html>