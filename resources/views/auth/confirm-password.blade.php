{{-- resources/views/auth/confirm-password.blade.php --}}
<x-auth-layout title="Confirm Password" subtitle="This is a secure area of the application.">

    <div class="flex items-start gap-2.5 rounded-lg border border-blue-100 bg-blue-50/60 px-3.5 py-3 mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="mt-0.5 w-4 h-4 flex-shrink-0" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="text-xs text-slate-500 leading-relaxed">
            Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <div class="flex flex-col gap-5">

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input id="password" name="password" type="password"
                       placeholder="••••••••" autocomplete="current-password" autofocus required
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

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                           active:bg-blue-800 text-white text-sm font-semibold
                           transition-colors duration-200 focus:outline-none
                           focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Confirm
            </button>

        </div>
    </form>

</x-auth-layout>