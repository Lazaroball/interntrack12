{{-- resources/views/coordinator/students/import.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Import Student Master List – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

@include('coordinator.partials.navbar')


<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ── Breadcrumb ── --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('coordinator.dashboard') }}" class="hover:text-blue-600 font-medium">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('coordinator.students.index') }}" class="hover:text-blue-600 font-medium">Student Management</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-500 font-medium">Import Student Master List</span>
    </nav>

    {{-- ── Page Header ── --}}
    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Upload Student Master List</h1>
        <p class="text-sm text-slate-400 mt-0.5">Upload an Excel or CSV file — InternTrack will automatically detect the columns for you.</p>
    </div>

    {{-- ── Flash Messages ── --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            <p class="font-semibold mb-1">Please correct the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Import Summary Alert (Shows only after a successful upload) ── --}}
    @if (session('importSummary'))
        @php $summary = session('importSummary'); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm shadow-blue-50 p-5">
                <p class="text-2xl font-extrabold text-slate-800">{{ $summary['total'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1">Total Records</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl shadow-sm p-5">
                <p class="text-2xl font-extrabold text-emerald-700">{{ $summary['success'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-emerald-600 mt-1">Imported Successfully</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl shadow-sm p-5">
                <p class="text-2xl font-extrabold text-red-600">{{ $summary['failed'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-red-500 mt-1">Failed Records</p>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-2xl shadow-sm p-5">
                <p class="text-2xl font-extrabold text-amber-600">{{ $summary['duplicate'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-amber-600 mt-1">Duplicate Records</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Step 1: Upload Card ── --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 space-y-5">
            <div>
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Upload Student Master List</h2>
            </div>

            <form action="{{ route('coordinator.students.import.upload') }}" method="POST" enctype="multipart/form-data"
                  x-data="{ dragging: false, fileName: null }" class="space-y-5">
                @csrf

                <label for="file"
                       @dragover.prevent="dragging = true"
                       @dragleave.prevent="dragging = false"
                       @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name ?? null;"
                       :class="dragging ? 'border-blue-400 bg-blue-50/60' : 'border-slate-200 bg-slate-50'"
                       class="flex flex-col items-center justify-center gap-3 border-2 border-dashed rounded-2xl
                              px-6 py-12 text-center cursor-pointer transition-colors duration-150 hover:border-blue-300 hover:bg-blue-50/40">

                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                            <path d="M12 12v9"/>
                            <path d="m16 16-4-4-4 4"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">
                            <span x-show="!fileName">Drag &amp; Drop Excel or CSV<br class="hidden sm:block"> or Upload File</span>
                            <span x-show="fileName" x-text="fileName" class="text-blue-700 font-bold"></span>
                        </p>
                        <p class="text-xs text-slate-400 mt-1.5">Accepted: .xlsx, .xls, .csv &middot; Maximum: 5MB</p>
                    </div>

                    <input type="file" name="file" id="file" x-ref="fileInput" accept=".csv,.xlsx,.xls"
                           class="hidden" @change="fileName = $event.target.files[0]?.name ?? null" required>
                </label>
                @error('file')
                    <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
                @enderror

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                                   transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        Upload File
                    </button>
                    <a href="{{ route('coordinator.students.import.template') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold
                              transition-colors duration-150">
                        Download Template
                    </a>
                </div>
            </form>
        </div>

        {{-- ── Import Guidelines Card ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4">Import Guidelines</h2>
            <ul class="space-y-2.5 text-sm text-slate-600">
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    File must be Excel (.xlsx, .xls) or CSV.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Student Number must be unique.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Email Address is required.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Mobile Number is required.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Duplicate student numbers will be skipped.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Columns are detected automatically — you'll only be asked about anything unclear.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                    Login credentials will be sent through email.
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Recent Import History ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800 tracking-tight">Recent Import History</h2>
        </div>

        @if (isset($imports) && $imports->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">File Name</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Imported By</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Total</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Successful</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Failed</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Duplicate</th>
                            <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Date Imported</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($imports as $import)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100">
                                {{-- Cleans up the randomized hashed storage directory and shows original user-facing name --}}
                                <td class="px-6 py-3 text-slate-700 font-medium">{{ basename($import->file_name) }}</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                    {{ $import->importedBy?->first_name }} {{ $import->importedBy?->last_name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $import->total_records }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                        {{ $import->successful_records }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-[11px] font-semibold">
                                        {{ $import->failed_records }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[11px] font-semibold">
                                        {{ $import->duplicate_records }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $import->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($imports->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $imports->links() }}
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center gap-3 py-16 text-slate-400">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-slate-300">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M12 18v-6"/>
                        <path d="m9 15 3-3 3 3"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500">No import history yet.</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">
                        Upload a student master list to begin creating student accounts.
                    </p>
                </div>
            </div>
        @endif
    </div>

</main>

<footer class="mt-8 border-t border-slate-100 bg-white">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} UCU · College of Teacher Education. All rights reserved.</p>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-slate-400">InternTrack v1.0 — System Online</span>
        </div>
    </div>
</footer>

</body>
</html>