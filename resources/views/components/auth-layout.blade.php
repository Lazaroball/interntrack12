{{--
    resources/views/components/auth-layout.blade.php
    ─────────────────────────────────────────────────
    Props (passed via @props or component attributes):
      $title      string        – Page heading  (e.g. "Sign In")
      $subtitle   string        – Supporting text below the title
      $footer     string|null   – Optional footer slot
      $showBadge  bool          – Show UCU badge strip (default: true)

    Usage:
      <x-auth-layout title="Sign In" subtitle="InternTrack System">
          ... form content ...
          <x-slot name="footer">Already have an account? ...</x-slot>
      </x-auth-layout>
--}}

@props([
    'title'     => '',
    'subtitle'  => '',
    'showBadge' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ? $title . ' – ' : '' }}InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{--
    Full-viewport background: subtle blue-tinted off-white
    with a fine dot-grid watermark.
--}}
<body class="min-h-screen w-full flex flex-col items-center justify-center
             bg-slate-50 px-4 py-10"
      style="background-image: radial-gradient(circle, #bfdbfe 1px, transparent 1px);
             background-size: 28px 28px;">

    {{-- ── Card ── --}}
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg shadow-blue-100/60
                border border-slate-100 overflow-hidden">

        {{-- Blue accent bar at the top --}}
        <div class="h-1.5 w-full bg-gradient-to-r from-blue-500 via-blue-400 to-sky-400"></div>

        {{-- Card body --}}
        <div class="px-8 pt-8 pb-7 flex flex-col gap-6">

            {{-- Logo + System Identity --}}
            <header class="flex flex-col items-center gap-3 text-center">

                {{-- Logo mark --}}
                <div class="w-16 h-16 rounded-2xl bg-blue-600 shadow-md shadow-blue-200
                            flex items-center justify-center flex-shrink-0"
                     aria-hidden="true">
                    {{-- Graduation cap icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="white" class="w-9 h-9">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>

                {{-- System name --}}
                <div>
                    <p class="text-[10px] font-bold tracking-[0.18em] uppercase text-blue-500 mb-0.5">
                        UCU · College of Teacher Education
                    </p>
                    <h1 class="text-xl font-extrabold text-slate-800 leading-tight">
                        InternTrack
                    </h1>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5 leading-snug">
                        Field Study &amp; Internship Management System
                    </p>
                </div>

            </header>

            {{-- Divider --}}
            <div class="flex items-center gap-3" aria-hidden="true">
                <div class="flex-1 h-px bg-slate-100"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-blue-300"></div>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>

            {{-- Page title & subtitle --}}
            @if ($title || $subtitle)
                <div class="text-center -mt-2">
                    @if ($title)
                        <h2 class="text-lg font-bold text-slate-800 leading-tight">
                            {{ $title }}
                        </h2>
                    @endif
                    @if ($subtitle)
                        <p class="text-sm text-slate-500 mt-1 leading-snug">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
            @endif

            {{-- Form content slot --}}
            <main>
                {{ $slot }}
            </main>

        </div>

        {{-- Optional footer slot --}}
        @isset($footer)
            <div class="px-8 pb-6 text-center text-xs text-slate-400">
                {{ $footer }}
            </div>
        @endisset

        {{-- UCU badge strip --}}
        @if ($showBadge)
            <div class="border-t border-slate-100 bg-slate-50 px-8 py-3
                        flex items-center justify-center gap-2">
                <div class="w-4 h-4 rounded-full bg-blue-600 flex items-center
                            justify-center flex-shrink-0" aria-hidden="true">
                    <div class="w-2 h-2 rounded-full bg-white"></div>
                </div>
                <span class="text-[10px] font-semibold tracking-widest uppercase text-slate-400">
                    URDANETA CITY UNIVERSITY
                </span>
            </div>
        @endif

    </div>

    {{-- Below-card fine print --}}
    <p class="mt-5 text-[10px] text-slate-400 text-center select-none">
        &copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.
    </p>

</body>
</html>
