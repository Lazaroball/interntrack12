{{-- resources/views/auth/verify-email.blade.php --}}
<x-auth-layout title="Verify Your Email" subtitle="One more step before you get started.">

    @if (session('status') == 'verification-link-sent')
        <div role="alert"
             class="flex items-start gap-2.5 rounded-lg border border-green-200 bg-green-50
                    px-4 py-3 text-sm text-green-700 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" class="mt-0.5 w-4 h-4 flex-shrink-0 text-green-500" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span class="font-medium leading-snug">
                A new verification link has been sent to the email address you provided during registration.
            </span>
        </div>
    @endif

    <div class="flex items-start gap-2.5 rounded-lg border border-blue-100 bg-blue-50/60 px-3.5 py-3 mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="mt-0.5 w-4 h-4 flex-shrink-0" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="text-xs text-slate-500 leading-relaxed">
            Thanks for signing up! Please verify your email address by clicking the link we just emailed you.
            If you didn't receive it, we'll gladly send another.
        </p>
    </div>

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                           active:bg-blue-800 text-white text-sm font-semibold
                           transition-colors duration-200 focus:outline-none
                           focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl border-2 border-slate-200
                           hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-blue-600
                           text-sm font-semibold text-center transition-all duration-200
                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                Log Out
            </button>
        </form>
    </div>

</x-auth-layout>