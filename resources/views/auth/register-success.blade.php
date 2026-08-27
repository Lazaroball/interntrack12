<x-auth-layout title="Account Created Successfully"
               subtitle="Your InternTrack account is ready.">

    <div class="flex flex-col gap-6 text-center">

        <div class="text-6xl">
            ✅
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Account created completely!
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Your account has been successfully registered.
            </p>
        </div>

        <a href="{{ route('login') }}"
           class="w-full py-3 rounded-xl bg-blue-600 text-white font-semibold">
            Go to Login
        </a>

    </div>

</x-auth-layout>