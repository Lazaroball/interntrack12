{{-- resources/views/admin/users/create.blade.php --}}
<x-admin-layout title="Add Account">
    <x-slot name="header">
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Super Admin</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Add New Account</h1>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.store') }}" x-data="{ loading: false }"
                  @submit="loading = true">
                @csrf

                <div class="space-y-6">

                    {{-- ── Personal Information ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="#3b82f6" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-600">Personal Information</h3>
                        </div>
                        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- First Name --}}
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    First Name <span class="text-blue-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name"
                                       value="{{ old('first_name') }}" required autofocus
                                       placeholder="e.g. Juan"
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              placeholder:text-slate-400 outline-none transition-all duration-150
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300
                                              {{ $errors->has('first_name') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                                @error('first_name')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Last Name --}}
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Last Name <span class="text-blue-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name"
                                       value="{{ old('last_name') }}" required
                                       placeholder="e.g. Dela Cruz"
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              placeholder:text-slate-400 outline-none transition-all duration-150
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300
                                              {{ $errors->has('last_name') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                                @error('last_name')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div class="sm:col-span-2">
                                <label for="middle_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Middle Name <span class="text-slate-400 font-normal text-xs">(optional)</span>
                                </label>
                                <input type="text" id="middle_name" name="middle_name"
                                       value="{{ old('middle_name') }}"
                                       placeholder="e.g. Santos"
                                       class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5
                                              text-sm text-slate-800 placeholder:text-slate-400 outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300 transition-all duration-150">
                            </div>

                        </div>
                    </div>

                    {{-- ── Contact & Role ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="#3b82f6" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/>
                                </svg>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-600">Contact & Role</h3>
                        </div>
                        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Email Address <span class="text-blue-500">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       value="{{ old('email') }}" required
                                       placeholder="user@example.com"
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              placeholder:text-slate-400 outline-none transition-all duration-150
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300
                                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Mobile Number --}}
                            <div>
                                <label for="mobile_number" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Mobile Number <span class="text-slate-400 font-normal text-xs">(optional)</span>
                                </label>
                                <input type="text" id="mobile_number" name="mobile_number"
                                       value="{{ old('mobile_number') }}"
                                       placeholder="e.g. 09XXXXXXXXX"
                                       class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5
                                              text-sm text-slate-800 placeholder:text-slate-400 outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300 transition-all duration-150">
                                @error('mobile_number')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Role <span class="text-blue-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="role" name="role" required
                                            class="w-full appearance-none rounded-lg border px-4 py-2.5 text-sm
                                                   outline-none transition-all duration-150 bg-white
                                                   focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                                   hover:border-blue-300
                                                   {{ $errors->has('role') ? 'border-red-400 bg-red-50 text-slate-800' : 'border-slate-200 text-slate-700' }}">
                                        <option value="" disabled @selected(!old('role'))>Select role…</option>
                                        <option value="coordinator" @selected(old('role') === 'coordinator')>Coordinator</option>
                                        <option value="supervisor"  @selected(old('role') === 'supervisor')>Supervisor</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                             stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('role')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- ── Password ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="#3b82f6" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-600">Account Security</h3>
                        </div>
                        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5"
                             x-data="{ show1: false, show2: false }">

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Password <span class="text-blue-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="show1 ? 'text' : 'password'" id="password" name="password"
                                           required placeholder="Min. 8 characters"
                                           class="w-full rounded-lg border pr-10 px-4 py-2.5 text-sm text-slate-800
                                                  placeholder:text-slate-400 outline-none transition-all duration-150
                                                  focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                                  hover:border-blue-300
                                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                                    <button type="button" @click="show1 = !show1"
                                            class="absolute inset-y-0 right-0 flex items-center px-3
                                                   text-slate-400 hover:text-blue-500 transition-colors">
                                        <svg x-show="!show1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg x-show="show1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Confirm Password <span class="text-blue-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="show2 ? 'text' : 'password'" id="password_confirmation"
                                           name="password_confirmation" required placeholder="Re-enter password"
                                           class="w-full rounded-lg border border-slate-200 bg-white pr-10
                                                  px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400
                                                  outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                                  hover:border-blue-300 transition-all duration-150">
                                    <button type="button" @click="show2 = !show2"
                                            class="absolute inset-y-0 right-0 flex items-center px-3
                                                   text-slate-400 hover:text-blue-500 transition-colors">
                                        <svg x-show="!show2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg x-show="show2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ── Actions ── --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold
                                  text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                            Cancel
                        </a>
                        <button type="submit" :disabled="loading"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5
                                       text-sm font-semibold text-white shadow-sm hover:bg-blue-700
                                       active:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed
                                       transition-all duration-150">
                            <svg x-show="loading" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                            </svg>
                            <span x-text="loading ? 'Creating…' : 'Create Account'">Create Account</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
