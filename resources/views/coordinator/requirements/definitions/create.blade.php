{{-- resources/views/coordinator/requirements/definitions/create.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Requirement – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Add Requirement</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <form method="POST" action="{{ route('coordinator.requirements.definitions.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Requirement Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description / Instructions (optional)</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Stage</label>
                    <select name="stage" required class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
                        <option value="Field Study" @selected(old('stage') === 'Field Study')>Field Study</option>
                        <option value="Internship" @selected(old('stage') === 'Internship')>Internship</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Semester</label>
                    <select name="semester" required class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
                        <option value="1st Semester" @selected(old('semester') === '1st Semester')>1st Semester</option>
                        <option value="2nd Semester" @selected(old('semester') === '2nd Semester')>2nd Semester</option>
                        <option value="Summer" @selected(old('semester') === 'Summer')>Summer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phase</label>
                <select name="phase" required
                        class="w-full px-3.5 py-2 rounded-xl border-2 border-slate-200 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('phase') border-red-300 @enderror">
                    <option value="">Select Phase</option>
                    <option value="initial" @selected(old('phase') === 'initial')>Initial</option>
                    <option value="ongoing" @selected(old('phase') === 'ongoing')>Ongoing</option>
                </select>
                <p class="text-xs text-slate-400 mt-1.5">
                    <span class="font-semibold text-slate-500">Initial</span> — required before Field Study acceptance.
                    <span class="font-semibold text-slate-500">Ongoing</span> — submitted during Field Study.
                </p>
                @error('phase')
                    <p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                    Required
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                    Active
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('coordinator.requirements.definitions.index') }}"
                   class="px-4 py-2 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Save Requirement</button>
            </div>
        </form>
    </div>

</main>

</body>
</html> 