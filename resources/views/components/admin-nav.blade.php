{{-- resources/views/components/admin-nav.blade.php --}}
@php
    $links = [
        ['label' => 'Dashboard',       'route' => 'admin.dashboard',             'active' => 'admin.dashboard'],
        ['label' => 'Student Records', 'route' => 'admin.student-records.index', 'active' => 'admin.student-records.*'],
        ['label' => 'Manage Users',    'route' => 'admin.users.index',           'active' => 'admin.users.*'],
    ];
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50"
        x-data="{ mobile: false }">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
               <img src="{{ asset('images/CTE.jpg') }}" alt="CTE logo"
     class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">
                        UCU · CTE
                    </span>
                </div>
            </a>

            {{-- Desktop links --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       @class([
                           'px-3.5 py-1.5 rounded-lg text-sm transition-colors duration-150',
                           'focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1',
                           'font-semibold text-white bg-blue-600' => request()->routeIs($link['active']),
                           'font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100' => ! request()->routeIs($link['active']),
                       ])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right side --}}
            <div class="flex items-center gap-3">

                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Last Login</span>
                    <span class="text-xs font-medium text-slate-600">
                        {{ $user?->last_login_at
                            ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y · g:i A')
                            : 'Never Logged In' }}
                    </span>
                </div>

                {{-- Profile dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1"
                            aria-haspopup="true" :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center
                                    text-white text-xs font-bold select-none flex-shrink-0">
                            {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">
                            {{ $user->first_name ?? 'Admin' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100
                                shadow-lg shadow-slate-200/60 py-1 z-50" role="menu">
                                <a href="{{ route('profile.edit') }}" role="menuitem"
   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600
          hover:bg-blue-50 hover:text-blue-700 transition-colors duration-100">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round"
         stroke-linejoin="round" class="w-4 h-4">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
    </svg>
    Profile
</a>
<div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm
                                           text-red-500 hover:bg-red-50 transition-colors duration-100">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-4 h-4">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile menu button --}}
                <button type="button" @click="mobile = !mobile" aria-label="Toggle menu"
                        class="md:hidden w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center
                               justify-center text-slate-500 hover:bg-blue-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile links --}}
    <nav x-show="mobile" x-transition style="display:none"
         class="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1" aria-label="Mobile navigation">
        @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               @class([
                   'block px-3.5 py-2 rounded-lg text-sm',
                   'font-semibold text-white bg-blue-600' => request()->routeIs($link['active']),
                   'font-medium text-slate-600 hover:bg-slate-100' => ! request()->routeIs($link['active']),
               ])>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>