{{-- resources/views/components/admin-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} – InternTrack</title>
    <link rel="icon" href="{{ asset('images/CTE.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900 antialiased">

    <x-admin-nav />

    {{-- Optional page heading --}}
    @isset($header)
        <div class="max-w-screen-xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-8">
            {{ $header }}
        </div>
    @endisset

    <main class="flex-1 w-full">
        {{ $slot }}
    </main>

    <footer class="mt-8 border-t border-slate-100 bg-white">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4
                    flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-[11px] text-slate-400">
                &copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.
            </p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[11px] font-semibold text-slate-400">InternTrack v1.0 &mdash; System Online</span>
            </div>
        </div>
    </footer>

</body>
</html>