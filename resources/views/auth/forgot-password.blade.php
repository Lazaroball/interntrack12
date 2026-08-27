{{-- resources/views/auth/forgot-password.blade.php --}}

<x-auth-layout title="Forgot Password" subtitle="Enter your registered email to receive a reset link.">

    {{-- Success status — sent by Laravel's password broker after dispatching the link --}}
    @if (session('status'))
        <div role="alert"
             class="flex items-start gap-2.5 rounded-lg border border-green-200 bg-green-50
                    px-4 py-3 text-sm text-green-700 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" class="mt-0.5 w-4 h-4 flex-shrink-0 text-green-500" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span class="font-medium leading-snug">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Info box --}}
    <div class="flex items-start gap-2.5 rounded-lg border border-blue-100 bg-blue-50/60 px-3.5 py-3 mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="mt-0.5 w-4 h-4 flex-shrink-0" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="text-xs text-slate-500 leading-relaxed">
            Use the email address registered to your InternTrack student account.
            The reset link expires in
            <span class="font-semibold text-blue-600">60 minutes</span>.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="flex flex-col gap-5">

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Email Address <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
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

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                       active:bg-blue-800 text-white text-sm font-semibold
                       transition-colors duration-200 focus:outline-none
                       focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Send Reset Link
            </button>

            {{-- Back to login --}}
            <a href="{{ route('login') }}"
               class="w-full py-2.5 px-4 rounded-xl border-2 border-slate-200
                      hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-blue-600
                      text-sm font-semibold text-center transition-all duration-200
                      focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                Back to Sign In
            </a>

        </div>
    </form>

    {{-- Footer slot --}}
    <x-slot name="footer">
        Remember your password?
        <a href="{{ route('login') }}"
           class="text-blue-500 font-semibold hover:text-blue-600 hover:underline transition-colors duration-150">
            Sign In
        </a>
    </x-slot>

</x-auth-layout>
