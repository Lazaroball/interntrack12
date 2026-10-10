{{-- resources/views/admin/profile/edit.blade.php --}}
@php
    $inputCls = 'w-full rounded-lg border px-4 py-2.5 text-sm text-slate-800 outline-none transition-all duration-150
                 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 hover:border-blue-300';
    $card     = 'bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden';
@endphp

<x-admin-layout title="Profile">
    <x-slot name="header">
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Admin</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Profile Settings</h1>
        <p class="text-sm text-slate-400 mt-0.5">Update your account details and password.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Account summary --}}
        <div class="flex items-center gap-4 bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0 text-white font-bold">
                {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-blue-200 px-2.5 py-1 text-[11px] font-semibold text-blue-700">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Admin
            </span>
        </div>

        {{-- Profile information --}}
        <form method="POST" action="{{ route('profile.update') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PATCH')

            <div class="{{ $card }}">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-blue-600">Profile Information</h2>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Name <span class="text-blue-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                               required autocomplete="name"
                               class="{{ $inputCls }} {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Email Address <span class="text-blue-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               required autocomplete="username"
                               class="{{ $inputCls }} {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                        @error('email')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    @if(session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                           class="text-sm font-medium text-emerald-600">Saved.</p>
                    @endif
                    <button type="submit" :disabled="loading"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                                   hover:bg-blue-700 active:bg-blue-800 disabled:opacity-50 transition-all duration-150">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>

        {{-- Password --}}
        <form method="POST" action="{{ route('password.update') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')

            <div class="{{ $card }}">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-blue-600">Update Password</h2>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Current Password</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                               class="{{ $inputCls }} {{ $errors->updatePassword->has('current_password') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                        @if($errors->updatePassword->has('current_password'))
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">New Password</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                   class="{{ $inputCls }} {{ $errors->updatePassword->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}">
                            @if($errors->updatePassword->has('password'))
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $errors->updatePassword->first('password') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                   class="{{ $inputCls }} border-slate-200 bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    @if(session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                           class="text-sm font-medium text-emerald-600">Saved.</p>
                    @endif
                    <button type="submit" :disabled="loading"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                                   hover:bg-blue-700 active:bg-blue-800 disabled:opacity-50 transition-all duration-150">
                        Update Password
                    </button>
                </div>
            </div>
        </form>

    </div>
</x-admin-layout>