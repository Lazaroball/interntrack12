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
<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Schedule Observation</h1>
            <p class="text-sm text-slate-400 mt-0.5">Set an observation schedule for one of your assigned students</p>
        </div>

       
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