{{-- resources/views/supervisor/observations/create.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Schedule Observation – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- ══ NAV ══ --}}
<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow shadow-blue-200 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">UCU · CTE</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-1" aria-label="Supervisor navigation">
                <a href="{{ route('supervisor.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Dashboard
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Students
                </a>
                @if (Route::has('supervisor.observations.index'))
                    <a href="{{ route('supervisor.observations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">
                        Observation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Observation</span>
                @endif
                @if (Route::has('supervisor.evaluations.index'))
                    <a href="{{ route('supervisor.evaluations.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Evaluation</a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Evaluation</span>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supervisor</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 shadow-lg shadow-slate-200/60 py-1 z-50">
                        <a href="#" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            My Profile
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Schedule Observation</h1>
            <p class="text-sm text-slate-400 mt-0.5">Set an observation schedule for one of your assigned students</p>
        </div>

        <a href="{{ route('supervisor.observations.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-slate-200
                  hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Back to Observations
        </a>
    </div>

    {{-- ── Validation Errors ── --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Form Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 px-6 py-6 max-w-2xl">

        <form method="POST" action="{{ route('supervisor.observations.store') }}" class="space-y-5">
            @csrf

            {{-- Student --}}
            <div>
                <label for="student_id" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Student
                </label>

                @if ($students->isEmpty())
                    <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm">
                        You have no active assigned students to schedule an observation for.
                    </div>
                @else
                    <select name="student_id" id="student_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                   outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="" disabled {{ old('student_id') ? '' : 'selected' }}>Select a student…</option>
                        @foreach ($students as $student)
                            <option value="{{ $student['id'] }}" {{ (string) old('student_id') === (string) $student['id'] ? 'selected' : '' }}>
                                {{ $student['name'] }} — {{ $student['student_number'] }}
                                @if ($student['school'])
                                    ({{ $student['school'] }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Date / Time --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="observation_date" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Observation Date
                    </label>
                    <input type="date" name="observation_date" id="observation_date"
                           value="{{ old('observation_date') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                  outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                </div>

                <div>
                    <label for="observation_time" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Observation Time
                    </label>
                    <input type="time" name="observation_time" id="observation_time"
                           value="{{ old('observation_time') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                  outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                </div>
            </div>

            {{-- Venue --}}
            <div>
                <label for="venue" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Venue
                </label>
                <input type="text" name="venue" id="venue"
                       value="{{ old('venue') }}" required maxlength="255"
                       placeholder="e.g. Room 204, ABC Elementary School"
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                              placeholder-slate-400 outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
            </div>

            {{-- Remarks --}}
            <div>
                <label for="remarks" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Remarks <span class="normal-case font-medium text-slate-400">(optional)</span>
                </label>
                <textarea name="remarks" id="remarks" rows="4" maxlength="2000"
                          placeholder="Any notes for this observation…"
                          class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                 placeholder-slate-400 outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none">{{ old('remarks') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" @if ($students->isEmpty()) disabled @endif
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                               shadow-sm shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600">
                    Schedule Observation
                </button>

                <a href="{{ route('supervisor.observations.index') }}"
                   class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100
                          text-slate-600 text-sm font-semibold transition-all duration-150">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</main>

</body>
</html>