{{-- resources/views/coordinator/requirements/definitions/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Requirement Definitions – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

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

            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Students</a>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Partner Schools</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Deployments</a>
                <a href="{{ route('coordinator.requirements.definitions.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Requirements</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('coordinator.dashboard') }}" class="hover:text-blue-600 font-medium">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-500 font-medium">Requirement Definitions</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Requirement Definitions</h1>
            <p class="text-sm text-slate-400 mt-0.5">Manage the Field Study and Internship requirement checklist shown to students.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('coordinator.requirements.review.index') }}"
               class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150">
                Review Submissions
            </a>
            <a href="{{ route('coordinator.requirements.definitions.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors duration-150">
                + Add Requirement
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Stage</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Phase</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Semester</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Required</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Active</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($definitions as $definition)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $definition->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $definition->stage }}</td>
                            <td class="px-4 py-3">
                                @if ($definition->phase === 'initial')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-200">Initial</span>
                                @elseif ($definition->phase === 'ongoing')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 ring-1 ring-purple-200">Ongoing</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $definition->semester }}</td>
                            <td class="px-4 py-3">
                                @if ($definition->is_required)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-600 ring-1 ring-red-200">Required</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500 ring-1 ring-slate-200">Optional</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('coordinator.requirements.definitions.toggle-active', $definition) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 transition-colors
                                                   {{ $definition->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200 hover:bg-slate-200' }}">
                                        {{ $definition->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('coordinator.requirements.definitions.edit', $definition) }}"
                                   class="text-blue-600 hover:text-blue-700 font-semibold text-xs mr-3">Edit</a>
                                <form method="POST" action="{{ route('coordinator.requirements.definitions.destroy', $definition) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this requirement definition? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 font-semibold text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 italic">No requirement definitions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>