{{-- resources/views/auth/login.blade.php --}}

<x-auth-layout title="Sign In" subtitle="Enter your credentials to continue.">

    {{-- Session error (e.g. invalid credentials from controller) --}}
    @if (session('error'))
        <div role="alert"
             class="flex items-start gap-2.5 rounded-lg border border-red-200 bg-red-50
                    px-4 py-3 text-sm text-red-600 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" class="mt-0.5 w-4 h-4 flex-shrink-0 text-red-500" aria-hidden="true">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span class="font-medium leading-snug">{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="flex flex-col gap-5">

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Email <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="you@ucu.edu.ph"
                    autocomplete="email"
                    autofocus
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('email') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
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

            {{-- Password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('password') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
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

            {{-- Remember Me --}}
            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-blue-500
                           focus:ring-blue-400 focus:ring-2 cursor-pointer"
                />
                <label for="remember" class="text-sm text-slate-500 cursor-pointer select-none">
                    Remember me
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                       active:bg-blue-800 text-white text-sm font-semibold
                       transition-colors duration-200 focus:outline-none
                       focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Sign In
            </button>

            {{-- Divider --}}
            <div class="flex items-center gap-3" aria-hidden="true">
                <div class="h-px flex-1 bg-slate-100"></div>
                <span class="text-xs text-slate-400 font-medium">or</span>
                <div class="h-px flex-1 bg-slate-100"></div>
            </div>

            {{-- Create Account --}}
            <a href="{{ route('register') }}"
               class="w-full py-2.5 px-4 rounded-xl border-2 border-slate-200
                      hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-blue-600
                      text-sm font-semibold text-center transition-all duration-200
                      focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                Create Account
            </a>

            {{-- Forgot Password --}}
            <p class="text-center text-xs text-slate-400">
                <a href="{{ route('password.request') }}"
                   class="text-blue-500 font-semibold hover:text-blue-600
                          hover:underline transition-colors duration-150">
                    Forgot Password?
                </a>
            </p>

        </div>
    </form>

</x-auth-layout>
