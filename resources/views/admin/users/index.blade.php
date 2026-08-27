{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800 leading-tight">
                User Management
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm
                      font-semibold text-white shadow-sm hover:bg-blue-700 active:bg-blue-800
                      transition-colors duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                     stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Account
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ── Success alert ── --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="flex items-center justify-between gap-3 rounded-xl border border-green-200
                            bg-green-50 px-4 py-3 text-sm text-green-700">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4 flex-shrink-0 text-green-500">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- ── Password Reset alert ── --}}
            @if(session('password_reset'))
                @php $pr = session('password_reset'); @endphp
                <div x-data="{ show: true }" x-show="show"
                     class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800 space-y-1">
                    <p class="font-bold text-blue-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4 text-blue-500">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Password Reset — {{ $pr['name'] }}
                    </p>
                    <p>Temporary password:
                        <code class="font-mono font-bold text-blue-900 bg-blue-100 px-2 py-0.5 rounded">
                            {{ $pr['password'] }}
                        </code>
                    </p>
                    <p class="text-xs text-blue-500">Share this securely. The user will be prompted to change it on next login.</p>
                    <button @click="show = false"
                            class="mt-1 text-xs font-semibold text-blue-600 hover:underline">Dismiss</button>
                </div>
            @endif

            {{-- ── Filters ── --}}
            <form method="GET" action="{{ route('admin.users.index') }}"
                  class="flex flex-wrap gap-3 items-end bg-white rounded-xl border border-slate-100
                         shadow-sm px-5 py-4">

                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-4 h-4 text-slate-400">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Name or email…"
                               class="w-full rounded-lg border border-slate-200 pl-9 pr-4 py-2.5 text-sm
                                      text-slate-800 placeholder:text-slate-400 outline-none
                                      focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                      hover:border-blue-300 transition-all duration-150">
                    </div>
                </div>

                {{-- Role filter --}}
                <div class="w-44">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Role</label>
                    <select name="role"
                            class="w-full appearance-none rounded-lg border border-slate-200 px-3 py-2.5
                                   text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300
                                   focus:border-blue-400 hover:border-blue-300 transition-all duration-150 bg-white">
                        <option value="">All Roles</option>
                        <option value="coordinator" @selected(request('role') === 'coordinator')>Coordinator</option>
                        <option value="supervisor"  @selected(request('role') === 'supervisor')>Supervisor</option>
                    </select>
                </div>

                {{-- Status filter --}}
                <div class="w-44">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Status</label>
                    <select name="status"
                            class="w-full appearance-none rounded-lg border border-slate-200 px-3 py-2.5
                                   text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300
                                   focus:border-blue-400 hover:border-blue-300 transition-all duration-150 bg-white">
                        <option value="">All Status</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2.5
                                   text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150">
                        Filter
                    </button>
                    @if(request()->hasAny(['search','role','status']))
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200
                                  bg-white px-4 py-2.5 text-sm font-semibold text-slate-600
                                  hover:bg-slate-50 transition-colors duration-150">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- ── Table card ── --}}
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">

                @if($users->isEmpty())
                    {{-- Empty state --}}
                    <div class="flex flex-col items-center justify-center py-20 gap-4 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100
                                    flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-8 h-8 text-slate-300">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-600">No accounts found</p>
                            <p class="text-xs text-slate-400 mt-1">
                                @if(request()->hasAny(['search','role','status']))
                                    Try adjusting your filters.
                                @else
                                    Add a Coordinator or Supervisor to get started.
                                @endif
                            </p>
                        </div>
                        @if(!request()->hasAny(['search','role','status']))
                            <a href="{{ route('admin.users.create') }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2
                                      text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                                Add First Account
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    @foreach(['#','Full Name','Email','Role','Status','Last Login','Actions'] as $col)
                                        <th class="px-5 py-3.5 text-left text-xs font-bold uppercase
                                                   tracking-wider text-slate-500">
                                            {{ $col }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($users as $user)
                                    <tr class="hover:bg-blue-50/40 transition-colors duration-100 group">

                                        {{-- ID --}}
                                        <td class="px-5 py-4 text-sm text-slate-400 font-mono">
                                            {{ $user->id }}
                                        </td>

                                        {{-- Full Name --}}
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center
                                                            justify-center flex-shrink-0 text-xs font-bold text-blue-600">
                                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                                                    @if($user->mobile_number)
                                                        <p class="text-xs text-slate-400">{{ $user->mobile_number }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Email --}}
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ $user->email }}</td>

                                        {{-- Role --}}
                                        <td class="px-5 py-4">
                                            @php
                                                $roleStyle = match($user->role) {
                                                    'coordinator' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'supervisor'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    default       => 'bg-slate-50 text-slate-600 border-slate-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5
                                                         text-xs font-semibold {{ $roleStyle }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-5 py-4">
                                            @if($user->status)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700">
                                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600">
                                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>Inactive
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Last Login --}}
                                        <td class="px-5 py-4 text-xs text-slate-400">
                                            {{ $user->last_login_at
                                                ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans()
                                                : 'Never' }}
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-5 py-4">
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" @click.outside="open = false"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200
                                                               bg-white px-3 py-1.5 text-xs font-semibold text-slate-600
                                                               hover:bg-slate-50 hover:border-blue-300 transition-all duration-150">
                                                    Actions
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                         stroke-linejoin="round" class="w-3 h-3">
                                                        <polyline points="6 9 12 15 18 9"/>
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-transition
                                                     class="absolute right-0 z-20 mt-1.5 w-48 rounded-xl border border-slate-100
                                                            bg-white shadow-lg shadow-slate-200/60 overflow-hidden">
                                                    {{-- Edit --}}
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700
                                                              hover:bg-blue-50 hover:text-blue-700 transition-colors duration-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                             stroke-linejoin="round" class="w-4 h-4">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                        </svg>
                                                        Edit Account
                                                    </a>

                                                    <div class="border-t border-slate-100"></div>

                                                    {{-- Activate / Deactivate --}}
                                                    @if($user->status)
                                                        <button @click="open=false; $dispatch('open-modal', 'deactivate-{{ $user->id }}')"
                                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm
                                                                       text-orange-600 hover:bg-orange-50 transition-colors duration-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                 stroke-linejoin="round" class="w-4 h-4">
                                                                <circle cx="12" cy="12" r="10"/>
                                                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                            </svg>
                                                            Deactivate
                                                        </button>
                                                    @else
                                                        <button @click="open=false; $dispatch('open-modal', 'activate-{{ $user->id }}')"
                                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm
                                                                       text-green-700 hover:bg-green-50 transition-colors duration-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                 stroke-linejoin="round" class="w-4 h-4">
                                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                                            </svg>
                                                            Activate
                                                        </button>
                                                    @endif

                                                    {{-- Reset Password --}}
                                                    <button @click="open=false; $dispatch('open-modal', 'reset-{{ $user->id }}')"
                                                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm
                                                                   text-blue-700 hover:bg-blue-50 transition-colors duration-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                             stroke-linejoin="round" class="w-4 h-4">
                                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                        </svg>
                                                        Reset Password
                                                    </button>

                                                    <div class="border-t border-slate-100"></div>

                                                    {{-- Delete --}}
                                                    <button @click="open=false; $dispatch('open-modal', 'delete-{{ $user->id }}')"
                                                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm
                                                                   text-red-600 hover:bg-red-50 transition-colors duration-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                             stroke-linejoin="round" class="w-4 h-4">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                        </svg>
                                                        Delete Account
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- ── Modals ── --}}
                                    @include('admin.users._modals', ['user' => $user])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="border-t border-slate-100 px-5 py-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- Row count --}}
            @if($users->isNotEmpty())
                <p class="text-xs text-slate-400 text-right">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} accounts
                </p>
            @endif

        </div>
    </div>
</x-app-layout>
