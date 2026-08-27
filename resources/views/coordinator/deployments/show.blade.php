<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deployment Details – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50/50 text-slate-900 antialiased selection:bg-blue-500 selection:text-white">

{{-- Navigation --}}
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5.5 h-5.5">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-base font-black text-slate-800 tracking-tight block">InternTrack</span>
                    <span class="text-[9px] font-bold text-blue-600 tracking-widest uppercase block -mt-1">UCU · CTE</span>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">Students</a>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">Partner Schools</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-blue-600 bg-blue-50/80">Deployments</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back to Deployments
    </a>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-3.5 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase
                @if ($deployment->completed_at) bg-slate-100 text-slate-500 border border-slate-200
                @elseif ($deployment->supervisor_id) bg-blue-50 text-blue-600 border border-blue-100
                @else bg-amber-50 text-amber-600 border border-amber-100 @endif">
                @if ($deployment->completed_at) Completed
                @elseif ($deployment->supervisor_id) Current Deployment
                @else Waiting for Approval @endif
            </span>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">Student No. {{ $deployment->student->student_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('coordinator.deployments.edit', $deployment) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white transition border border-slate-200/50 hover:border-blue-600">
                Edit
            </a>

            @if ($deployment->supervisor_id && ! $deployment->completed_at)
                <form
                    method="POST"
                    action="{{ route('coordinator.deployments.complete', $deployment) }}"
                    onsubmit="return confirm('Mark this deployment as completed?');"
                >
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition">
                        Mark Completed
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Deployment details --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Deployment Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Partner School</dt>
                <dd