{{--
    resources/views/admin/dashboard.blade.php
    Super Admin Dashboard — InternTrack
    Laravel 12 · Blade · Tailwind CSS
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Admin Dashboard – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ═══════════════════════════════════════════════════════════
     TOP NAVIGATION BAR
═══════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow shadow-blue-200 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">
                        UCU · CTE
                    </span>
                </div>
            </div>

            {{-- Center nav links --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Dashboard
                </a>

                {{-- Student Records (archive / restore / permanent delete) --}}
                <a href="{{ route('admin.student-records.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500
                          hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Student Records
                </a>

                {{-- Students (hidden for now)
                <a href="#"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500
                          hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Students
                </a> --}}

                <a href="#"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500
                          hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Supervisors
                </a>
                <a href="#"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500
                          hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Coordinators
                </a>

                {{-- Reports (hidden for now)
                <a href="#"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500
                          hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Reports
                </a> --}}
            </nav>

            {{-- Right: last login + avatar --}}
            <div class="flex items-center gap-3">

                {{-- Last login badge (hidden on very small screens) --}}
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        Last Login
                    </span>
                    <span class="text-xs font-medium text-slate-600">
                        {{ auth()->user()?->last_login_at
                            ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('M d, Y · g:i A')
                            : 'Never Logged In' }}
                    </span>
                </div>

                {{-- Notification bell --}}
                <button type="button" aria-label="Notifications"
                        class="relative w-9 h-9 rounded-xl border border-slate-200 bg-white
                               flex items-center justify-center text-slate-500
                               hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200
                               transition-colors duration-150 focus:outline-none focus:ring-2
                               focus:ring-blue-400 focus:ring-offset-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-4.5 h-4.5">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    {{-- Notification dot --}}
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"
                          aria-label="Unread notifications"></span>
                </button>

                {{-- Avatar / profile dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200
                                   transition-colors duration-150 focus:outline-none
                                   focus:ring-2 focus:ring-blue-400 focus:ring-offset-1"
                            aria-haspopup="true" :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center
                                    text-white text-xs font-bold select-none flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">
                            {{ auth()->user()->first_name ?? 'Admin' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100
                                shadow-lg shadow-slate-200/60 py-1 z-50"
                         role="menu">
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600
                                  hover:bg-blue-50 hover:text-blue-700 transition-colors duration-100">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-4 h-4">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            My Profile
                        </a>
                        <a href="#" role="menuitem"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600
                                  hover:bg-blue-50 hover:text-blue-700 transition-colors duration-100">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-4 h-4">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06
                                         a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09
                                         A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83
                                         l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                                         A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83
                                         l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09
                                         a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83
                                         l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09
                                         a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            Settings
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm
                                           text-red-500 hover:bg-red-50 transition-colors duration-100">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="w-4 h-4">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>


{{-- ═══════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════ --}}
<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ── Page Header ───────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">
                Super Admin
            </p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">
                System Dashboard
            </h1>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ now()->format('l, F j, Y') }} &mdash; Academic Year {{ now()->year }}–{{ now()->year + 1 }}
            </p>
        </div>

        {{-- ── Quick Actions ── --}}
        {{-- "Add Student" removed: students self-register via public registration page --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Primary: Create User --}}
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600
                      hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold
                      transition-colors duration-150 shadow shadow-blue-200
                      focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                     stroke-linejoin="round" class="w-4 h-4">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create User
            </a>

            {{-- Secondary: Manage Users --}}
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border-2 border-slate-200
                      hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700
                      text-slate-600 text-sm font-semibold transition-all duration-150
                      focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                     stroke-linejoin="round" class="w-4 h-4">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Manage Users
            </a>

        </div>
    </div>


    {{-- ── Statistics Cards ──────────────────────────────────── --}}
    <section aria-label="Statistics overview">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

            {{-- 1. Total Student Interns --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['total_students'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Student Interns
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" class="w-3 h-3">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    +12 this month
                </div>
            </div>

            {{-- 2. Total Supervisors --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#0284c7" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['total_supervisors'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Supervisors
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" class="w-3 h-3">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    +3 this month
                </div>
            </div>

            {{-- 3. Total Coordinators --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#6366f1" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['total_coordinators'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Coordinators
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" class="w-3 h-3">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    No change
                </div>
            </div>

            {{-- 4. Active Deployments --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#059669" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['active_deployments'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Active Deployments
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                    Live now
                </div>
            </div>

            {{-- 5. Pending Requests --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#d97706" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['pending_requests'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Pending Requests
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" class="w-3 h-3">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    Needs review
                </div>
            </div>

            {{-- 6. Completed Internships --}}
            <div class="col-span-1 bg-white rounded-2xl border border-slate-100
                        shadow-sm shadow-blue-50 p-5 flex flex-col gap-3
                        hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-5 h-5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                        {{ $stats['completed_internships'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1 leading-snug">
                        Completed
                    </p>
                </div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-blue-600">
                    All time total
                </div>
            </div>

        </div>
    </section>


    {{-- ── Middle Row: Quick Actions + System Info ───────────── --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6" aria-label="Quick actions and system info">

        {{-- Quick Action Buttons --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100
                    shadow-sm shadow-blue-50 p-6 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Quick Actions</h2>
                <span class="text-[10px] font-bold tracking-widest uppercase text-blue-500">Admin</span>
            </div>

            <div class="flex flex-col gap-2">

                {{-- Create User --}}
                <a href="{{ route('admin.users.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700
                          active:bg-blue-800 text-white transition-colors duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Create User</p>
                        <p class="text-[11px] text-blue-200">Add coordinator or supervisor</p>
                    </div>
                </a>

                {{-- Manage Users --}}
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                          text-slate-700 transition-all duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="#0284c7" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Manage Users</p>
                        <p class="text-[11px] text-slate-400">View all accounts</p>
                    </div>
                </a>

                {{-- Student Records --}}
                <a href="{{ route('admin.student-records.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                          text-slate-700 transition-all duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="#059669" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="21 8 21 21 3 21 3 8"/>
                            <rect x="1" y="3" width="22" height="5"/>
                            <line x1="10" y1="12" x2="14" y2="12"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Student Records</p>
                        <p class="text-[11px] text-slate-400">Archive, restore or delete students</p>
                    </div>
                </a>

                {{-- Review Requests --}}
                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                          text-slate-700 transition-all duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="#d97706" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="9 11 12 14 22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Review Requests</p>
                        <p class="text-[11px] text-slate-400">
                            {{ $stats['pending_requests'] ?? 0 }} pending approval
                        </p>
                    </div>
                </a>

                {{-- Generate Report (hidden for now)
                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                          text-slate-700 transition-all duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">Generate Report</p>
                        <p class="text-[11px] text-slate-400">Export internship summary</p>
                    </div>
                </a> --}}

                {{-- System Settings (hidden for now)
                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200
                          hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                          text-slate-700 transition-all duration-150 group
                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="#6366f1" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-4 h-4">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06
                                     a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09
                                     A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83
                                     l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                                     A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83
                                     l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09
                                     a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83
                                     l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09
                                     a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold">System Settings</p>
                        <p class="text-[11px] text-slate-400">Configure preferences</p>
                    </div>
                </a> --}}

            </div>
        </div>

        {{-- System Overview / Info Panel --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Deployment progress --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Deployment Overview</h2>
                    <span class="text-xs font-semibold text-slate-400">A.Y. {{ now()->year }}–{{ now()->year + 1 }}</span>
                </div>

                <div class="space-y-4">

                    {{-- Field Study --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-600">Field Study</span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $stats['field_study_deployed'] ?? 0 }} / {{ $stats['field_study_total'] ?? 0 }}
                            </span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            @php
                                $fsTotal = $stats['field_study_total'] ?? 0;
                                $fsPct   = $fsTotal > 0
                                    ? round((($stats['field_study_deployed'] ?? 0) / $fsTotal) * 100)
                                    : 0;
                            @endphp
                            <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                                 style="width: {{ $fsPct }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $fsPct }}% placement rate</p>
                    </div>

                    {{-- Internship --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-600">Internship</span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $stats['internship_deployed'] ?? 0 }} / {{ $stats['internship_total'] ?? 0 }}
                            </span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            @php
                                $intTotal = $stats['internship_total'] ?? 0;
                                $intPct   = $intTotal > 0
                                    ? round((($stats['internship_deployed'] ?? 0) / $intTotal) * 100)
                                    : 0;
                            @endphp
                            <div class="h-full bg-sky-400 rounded-full transition-all duration-500"
                                 style="width: {{ $intPct }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $intPct }}% placement rate</p>
                    </div>

                    {{-- Overall Completion --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-600">Overall Completion</span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $stats['completed_internships'] ?? 0 }} / {{ $stats['total_students'] ?? 0 }}
                            </span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            @php
                                $compTotal = $stats['total_students'] ?? 0;
                                $compPct   = $compTotal > 0
                                    ? round((($stats['completed_internships'] ?? 0) / $compTotal) * 100)
                                    : 0;
                            @endphp
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                                 style="width: {{ $compPct }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $compPct }}% completion rate</p>
                    </div>

                </div>
            </div>

            {{-- Session Info --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4">Session Information</h2>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Logged in as</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()->first_name ?? 'Admin' }} {{ auth()->user()->last_name ?? '' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Role</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-semibold text-blue-700">Super Admin</span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Last Login</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()?->last_login_at
                                ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('M d, Y')
                                : 'N/A' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Login Time</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ auth()->user()?->last_login_at
                                ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('g:i A')
                                : 'N/A' }}
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </section>


    {{-- ── Recent Activity Table ─────────────────────────────── --}}
    <section aria-label="Recent activity">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">

            {{-- Table header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between
                        gap-3 px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Recent Activity</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Latest actions across the system</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" placeholder="Search activity…"
                               class="pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-xs
                                      text-slate-700 placeholder-slate-400 bg-slate-50 outline-none
                                      focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                      transition-all duration-200 w-40 sm:w-52" />
                    </div>
                    <a href="#"
                       class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50
                              hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700
                              text-slate-600 text-xs font-semibold transition-all duration-150">
                        View All
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm" role="table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">#</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Program</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">

                        @forelse($recentActivity as $index => $activity)
                            <tr class="hover:bg-slate-50/60 transition-colors duration-100">

                                {{-- Row number --}}
                                <td class="px-6 py-3.5 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Student --}}
                                <td class="px-4 py-3.5">
                                    <div class="leading-tight">
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $activity->student_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ $activity->student_number ?? 'N/A' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Action --}}
                                <td class="px-4 py-3.5 text-sm text-slate-600">
                                    {{ $activity->action ?? 'No Action' }}
                                </td>

                                {{-- Program --}}
                                <td class="px-4 py-3.5 text-xs font-medium text-slate-500">
                                    {{ $activity->program_type ?? 'N/A' }}
                                </td>

                                {{-- Status badge --}}
                                <td class="px-4 py-3.5">
                                    @if(($activity->status ?? '') === 'Approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Approved
                                        </span>
                                    @elseif(($activity->status ?? '') === 'Pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-amber-50 text-amber-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    @elseif(($activity->status ?? '') === 'Completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-red-50 text-red-700 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            {{ $activity->status ?? 'Unknown' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-3.5 text-xs text-slate-400">
                                    {{ $activity->created_at
                                        ? \Carbon\Carbon::parse($activity->created_at)->format('M d, Y')
                                        : 'N/A' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                 stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round" class="w-5 h-5">
                                                <circle cx="11" cy="11" r="8"/>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-400">No recent activity found</p>
                                        <p class="text-xs text-slate-300">Activity will appear here once students begin logging actions.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Table footer / pagination --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between
                        gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <p class="text-xs text-slate-400">
                    Showing
                    <span class="font-semibold text-slate-600">{{ $recentActivity->count() }}</span>
                    of
                    <span class="font-semibold text-slate-600">{{ $stats['total_students'] ?? 0 }}</span>
                    records
                </p>
                <div class="flex items-center gap-1.5">
                    <button type="button" disabled
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold
                                   text-slate-400 bg-white cursor-not-allowed">
                        Previous
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold
                                   hover:bg-blue-700 transition-colors duration-150">
                        1
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold
                                   text-slate-600 bg-white hover:bg-blue-50 hover:border-blue-200
                                   transition-all duration-150">
                        2
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold
                                   text-slate-600 bg-white hover:bg-blue-50 hover:border-blue-200
                                   transition-all duration-150">
                        Next
                    </button>
                </div>
            </div>

        </div>
    </section>

</main>


{{-- ═══════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════ --}}
<footer class="mt-8 border-t border-slate-100 bg-white">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4
                flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-[11px] text-slate-400">
            &copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.
        </p>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-slate-400">
                InternTrack v1.0 &mdash; System Online
            </span>
        </div>
    </div>
</footer>

{{-- Alpine.js for dropdown --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>