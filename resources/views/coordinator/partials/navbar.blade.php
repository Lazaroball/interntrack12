{{-- resources/views/coordinator/partials/navbar.blade.php --}}
{{-- Shared coordinator navigation (same as the dashboard). Usage: @include('coordinator.partials.navbar') --}}

@php
    $user = $user ?? auth()->user();

    $coordinatorNav = [
        [
            'label'  => 'Dashboard',
            'url'    => route('coordinator.dashboard'),
            'active' => request()->routeIs('coordinator.dashboard'),
        ],
        [
            'label'  => 'Students',
            'url'    => route('coordinator.students.index'),
            'active' => request()->routeIs('coordinator.students.*'),
        ],
        [
            'label'  => 'Requirements',
            'url'    => route('coordinator.requirements.review.index'),
            'active' => request()->routeIs('coordinator.requirements.*'),
        ],
        [
            'label'  => 'Partner Schools',
            'url'    => route('coordinator.partner-schools.index'),
            'active' => request()->routeIs('coordinator.partner-schools.*'),
        ],
        [
            'label'  => 'Deployments',
            'url'    => route('coordinator.deployments.index'),
            'active' => request()->routeIs('coordinator.deployments.*'),
        ],
    ];
@endphp

<style>[x-cloak] { display: none !important; }</style>

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50" x-data="{ mobileOpen: false }">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

           {{-- Brand --}}
<a href="{{ route('coordinator.dashboard') }}" class="flex items-center gap-3">
    <img src="{{ asset('images/CTE.jpg') }}" alt="CTE logo"
         class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
    <div class="leading-tight">
        <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
        <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">UCU · CTE</span>
    </div>
</a>

            {{-- Center Nav --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                @foreach ($coordinatorNav as $item)
                    <a href="{{ $item['url'] }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all duration-150
                       {{ $item['active']
                            ? 'bg-blue-600 text-white font-semibold shadow-sm'
                            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right Controls --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last Login</span>
                    <span class="text-xs font-medium text-slate-600">
                        {{ $user?->last_login_at
                            ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y · g:i A')
                            : now()->format('M d, Y · g:i A') }}
                    </span>
                </div>

                {{-- Mobile Menu Trigger --}}
                <button type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center
                               text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150"
                        aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                {{-- Avatar + Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1"
                            :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center
                                    text-white text-xs font-bold select-none flex-shrink-0">
                            {{ strtoupper(substr($user->first_name ?? 'C', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">
                            {{ $user->first_name ?? 'Coordinator' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                             class="w-3.5 h-3.5 text-slate-400">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100
                                shadow-lg shadow-slate-200/60 py-1 z-50" role="menu">
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            My Profile
                        </a>
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Settings
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors text-left">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        {{-- Mobile nav panel --}}
        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden pb-4 space-y-1">
            @foreach ($coordinatorNav as $item)
                <a href="{{ $item['url'] }}"
                   @if ($item['active']) aria-current="page" @endif
                   class="block px-3.5 py-2 rounded-lg text-sm
                          {{ $item['active']
                                ? 'font-semibold text-white bg-blue-600'
                                : 'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>