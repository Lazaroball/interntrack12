{{-- resources/views/auth/reset-password.blade.php --}}
<x-auth-layout title="Reset Password" subtitle="Choose a new password for your account.">

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        {{-- Password reset token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="flex flex-col gap-5">

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Email <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $request->email) }}"
                       autocomplete="username" autofocus required
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                              placeholder-slate-300 outline-none transition-all duration-200
                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                              @error('email') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror" />
                @error('email')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- New password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-semibold text-slate-700 tracking-wide">
                    New Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input id="password" name="password" type="password"
                       placeholder="Min. 8 characters" autocomplete="new-password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                              placeholder-slate-300 outline-none transition-all duration-200
                              focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                              @error('password') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror" />
                @error('password')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Confirm Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       placeholder="Re-enter your password" autocomplete="new-password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white
                              text-sm text-slate-800 placeholder-slate-300 outline-none
                              transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                @error('password_confirmation')
                    <p role="alert" class="text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                           active:bg-blue-800 text-white text-sm font-semibold
                           transition-colors duration-200 focus:outline-none
                           focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Reset Password
            </button>

        </div>
    </form>

</x-auth-layout>