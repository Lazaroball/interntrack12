{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200
                      bg-white text-slate-500 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600
                      transition-all duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                     stroke-linejoin="round" class="w-4 h-4">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Edit Account</h2>
                <p class="text-sm text-slate-500">{{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    {{-- ── Account summary strip ── --}}
                    <div class="flex items-center gap-4 bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4">
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center
                                    flex-shrink-0 text-white font-bold text-sm">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($user->status)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700
                                             bg-green-50 border border-green-200 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600
                                             bg-red-50 border border-red-200 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Inactive
                                </span>
                            @endif
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5
                                         text-xs font-semibold
                                         {{ $user->role === 'coordinator' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>

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
                                       value="{{ old('first_name', $user->first_name) }}" required
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              outline-none transition-all duration-150
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
                                       value="{{ old('last_name', $user->last_name) }}" required
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              outline-none transition-all duration-150
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
                                       value="{{ old('middle_name', $user->middle_name) }}"
                                       class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5
                                              text-sm text-slate-800 outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300 transition-all duration-150">
                            </div>

                        </div>
                    </div>

                    {{-- ── Contact, Role & Status ── --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="#3b82f6" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/>
                                </svg>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-600">Contact, Role & Status</h3>
                        </div>
                        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Email Address <span class="text-blue-500">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800
                                              outline-none transition-all duration-150
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
                                       value="{{ old('mobile_number', $user->mobile_number) }}"
                                       placeholder="e.g. 09XXXXXXXXX"
                                       class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5
                                              text-sm text-slate-800 placeholder:text-slate-400 outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                              hover:border-blue-300 transition-all duration-150">
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Role <span class="text-blue-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="role" name="role" required
                                            class="w-full appearance-none rounded-lg border px-4 py-2.5 text-sm
                                                   text-slate-700 outline-none bg-white transition-all duration-150
                                                   focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                                   hover:border-blue-300
                                                   {{ $errors->has('role') ? 'border-red-400' : 'border-slate-200' }}">
                                        <option value="coordinator" @selected(old('role', $user->role) === 'coordinator')>Coordinator</option>
                                        <option value="supervisor"  @selected(old('role', $user->role) === 'supervisor')>Supervisor</option>
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

                            {{-- Status --}}
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Account Status <span class="text-blue-500">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="status" value="1"
                                               @checked(old('status', $user->status ? '1' : '0') === '1')
                                               class="w-4 h-4 text-blue-600 border-slate-300
                                                      focus:ring-blue-300 cursor-pointer">
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-green-700
                                                     transition-colors duration-150">
                                            🟢 Active
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="status" value="0"
                                               @checked(old('status', $user->status ? '1' : '0') === '0')
                                               class="w-4 h-4 text-blue-600 border-slate-300
                                                      focus:ring-blue-300 cursor-pointer">
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-red-600
                                                     transition-colors duration-150">
                                            🔴 Inactive
                                        </span>
                                    </label>
                                </div>
                                @error('status')
                                    <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- ── Actions ── --}}
                    <div class="flex items-center justify-between gap-3">
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
                            <span x-text="loading ? 'Saving…' : 'Save Changes'">Save Changes</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
