{{-- resources/views/coordinator/students/mapping.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirm Student Import – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <nav class="hidden md:flex items-center gap-1" aria-label="Coordinator navigation">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Students</a>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Partner Schools</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Deployments</a>
                <a href="#" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">Reports</a>
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Coordinator</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Coordinator' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'C', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Coordinator' }}</span>
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


@php
    // ── Auto-Detection (server-side, so there's no flash of the wrong UI state) ──
    // Priority order: student_number, email, mobile, block, type, year, program, name.
    // "block" is checked before "type" and "program" so a header like "Block"
    // or "Section" is never mistaken for another field.
    // "type" is checked before "program" so a header like "Program Type"
    // maps to program_type, not program.

    // Required fields: the import cannot start until all of these are mapped.
    $requiredFields = [
        'student_number' => 'Student Number',
        'full_name'      => 'Full Name',
        'email'          => 'Email',
        'mobile_number'  => 'Mobile Number',
        'program'        => 'Program',
        'year_level'     => 'Year Level',
        'program_type'   => 'Program Type',
    ];

    $detectField = function (string $header) {
        $h = strtolower($header);

        if (str_contains($h, 'student number') || str_contains($h, 'student id') || str_contains($h, 'id number')) return 'student_number';
        if (str_contains($h, 'email')) return 'email';
        if (str_contains($h, 'mobile') || str_contains($h, 'contact') || str_contains($h, 'phone')) return 'mobile_number';
        if (str_contains($h, 'block') || str_contains($h, 'section')) return 'block';
        if (str_contains($h, 'type')) return 'program_type';
        if (str_contains($h, 'year') || str_contains($h, 'level')) return 'year_level';
        if (str_contains($h, 'program') || str_contains($h, 'course')) return 'program';
        if (str_contains($h, 'name')) return 'full_name';

        return null;
    };

    $mapping = []; // field_key => header index
    $usedIndexes = [];

    foreach ($headers as $index => $header) {
        $fieldKey = $detectField((string) $header);
        if (!$fieldKey) continue;
        if (isset($mapping[$fieldKey])) continue; // already filled by an earlier column
        if (in_array($index, $usedIndexes)) continue;

        $mapping[$fieldKey] = $index;
        $usedIndexes[] = $index;
    }

    $missingFields = array_diff_key($requiredFields, $mapping);
    $extraColumns  = array_values(array_diff_key($headers, array_flip($usedIndexes)));
    $allDetected   = empty($missingFields);

    // Block is optional, so it is not counted in the required total.
    $blockDetected  = isset($mapping['block']);
    $requiredFound  = count(array_intersect_key($mapping, $requiredFields));
@endphp

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ── Breadcrumb ── --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('coordinator.dashboard') }}" class="hover:text-blue-600 font-medium">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('coordinator.students.import') }}" class="hover:text-blue-600 font-medium">Import Student Master List</a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-500 font-medium">Confirm Import</span>
    </nav>

    {{-- ── Step 2: Analyze File ── --}}
    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Coordinator</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Confirm Import</h1>
        <p class="text-sm text-slate-400 mt-0.5">{{ $originalFileName ?? basename($filePath) }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
        <div class="space-y-2.5">
            <div class="flex items-center gap-2.5 text-sm text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span class="font-semibold">File uploaded successfully</span>
            </div>
            <div class="flex items-center gap-2.5 text-sm text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span class="font-semibold">{{ $totalRows }} records detected</span>
            </div>
            <div class="flex items-center gap-2.5 text-sm text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span class="font-semibold">{{ count($headers) }} columns detected</span>
            </div>
        </div>
    </div>

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

    <form method="POST" action="{{ route('coordinator.students.import.process') }}" id="mapping-form" class="space-y-6">
        @csrf

        <input type="hidden" name="file_path" value="{{ $filePath }}">
        <input type="hidden" name="original_file_name" value="{{ $originalFileName }}">

        {{-- Hidden inputs for every field that was auto-detected (not shown to the coordinator) --}}
        @foreach ($mapping as $fieldKey => $index)
            <input type="hidden" name="mapping[{{ $fieldKey }}]" value="{{ $index }}">
        @endforeach

        @if ($allDetected)
            {{-- ── Everything looks good ── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-700">Everything looks good.</p>
                        <p class="text-xs text-slate-400 mt-0.5">All required fields were detected automatically.</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-100 overflow-hidden">
                    <div class="divide-y divide-slate-50">
                        @foreach ($requiredFields as $fieldKey => $label)
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="font-bold text-slate-800">{{ $label }}</span>
                                <span class="flex items-center gap-2 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                        <polyline points="12 5 19 12 12 19"/>
                                    </svg>
                                    {{ $headers[$mapping[$fieldKey]] }}
                                </span>
                            </div>
                        @endforeach

                        @if ($blockDetected)
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="font-bold text-slate-800">
                                    Block <span class="text-xs font-normal text-slate-400">(optional)</span>
                                </span>
                                <span class="flex items-center gap-2 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                        <polyline points="12 5 19 12 12 19"/>
                                    </svg>
                                    {{ $headers[$mapping['block']] }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- ── Manual Fallback: only unresolved fields shown ── --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-700">We couldn't identify:</p>
                        <ul class="mt-1.5 space-y-0.5">
                            @foreach ($missingFields as $fieldKey => $label)
                                <li class="text-xs font-semibold text-amber-600">• {{ $label }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-1">Confirm the Remaining Fields</h2>
                <p class="text-xs text-slate-400 mb-4">
                    {{ $requiredFound }} of {{ count($requiredFields) }} required fields were detected automatically. Please confirm the rest below.
                </p>

                <div class="space-y-3">
                    @foreach ($missingFields as $fieldKey => $label)
                        <div class="flex items-center gap-3">
                            <span class="w-36 flex-shrink-0 text-sm font-bold text-slate-800">{{ $label }}</span>
                            <select name="mapping[{{ $fieldKey }}]" id="mapping_{{ $fieldKey }}"
                                    class="fallback-select flex-1 px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none
                                           transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                                <option value="">— Select column —</option>
                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}" @if (in_array($index, $usedIndexes)) disabled @endif>
                                        {{ $header }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <p id="mapping-warning" class="text-xs font-semibold text-amber-600 mt-4" style="display:none;">
                    Please select a column for every field above before starting the import.
                </p>
            </div>
        @endif

        {{-- ── Block (optional) ── --}}
        @if ($blockDetected)
            @unless ($allDetected)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-sm">
                    <span class="font-bold text-emerald-700">Block detected:</span>
                    <span class="text-emerald-600">{{ $headers[$mapping['block']] }}</span>
                </div>
            @endunless
        @else
            <div class="bg-white rounded-2xl border border-amber-200 shadow-sm shadow-blue-50 p-6">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-amber-700">No Block column detected</p>
                        <p class="text-xs text-slate-500 mt-1">
                            Students are deployed by Program and Block. Without a block, these students can't be grouped by block in Deployments.
                            If your file uses a different header for block, pick it below. Otherwise leave it as skipped.
                        </p>

                        <select name="mapping[block]" id="mapping_block"
                                class="optional-select mt-3 w-full sm:w-72 px-3.5 py-2 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none
                                       transition-colors duration-150 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                            <option value="">— Skip (no block) —</option>
                            @foreach ($headers as $index => $header)
                                <option value="{{ $index }}" @if (in_array($index, $usedIndexes)) disabled @endif>
                                    {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Extra Columns ── --}}
        @if (!empty($extraColumns))
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0 mt-0.5">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-slate-600">Extra columns detected:</p>
                        <ul class="mt-1.5 space-y-0.5">
                            @foreach ($extraColumns as $column)
                                <li class="text-xs font-semibold text-slate-500">• {{ $column }}</li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-slate-400 mt-2">These columns will be ignored.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Compact Stats ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-2xl font-extrabold text-blue-700">{{ $totalRows }}</p>
                    <p class="text-xs font-semibold text-blue-600 mt-1">Records Detected</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-2xl font-extrabold text-slate-700">{{ count($headers) }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Columns Detected</p>
                </div>
                <div class="rounded-xl p-4 {{ $allDetected ? 'bg-emerald-50' : 'bg-amber-50' }}">
                    <p class="text-2xl font-extrabold {{ $allDetected ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $requiredFound }}/{{ count($requiredFields) }}
                    </p>
                    <p class="text-xs font-semibold mt-1 {{ $allDetected ? 'text-emerald-600' : 'text-amber-600' }}">Required Columns Found</p>
                </div>
            </div>

            {{-- Optional first 3 rows --}}
            @if (!empty($previewRows))
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">First 3 Rows</p>
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    @foreach ($headers as $header)
                                        <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach (array_slice($previewRows, 0, 3) as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td class="px-3 py-2 text-slate-500 whitespace-nowrap">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Buttons ── --}}
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('coordinator.students.import') }}"
               class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-colors duration-150">
                Back to Upload
            </a>

            @if ($allDetected)
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                               transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Import Students
                </button>
            @else
                <button type="submit" id="start-import-btn" disabled
                        class="px-5 py-2.5 rounded-xl bg-slate-300 text-white text-sm font-semibold cursor-not-allowed transition-colors duration-150">
                    Start Import
                </button>
            @endif
        </div>
    </form>

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

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@if (!$allDetected)
<script>
    (function () {
        // Only the REQUIRED selects gate the import button.
        // The optional Block select uses a different class and is never required.
        var selects = Array.prototype.slice.call(document.querySelectorAll('.fallback-select'));

        function updateDuplicateOptions() {
            var chosen = {};
            selects.forEach(function (select) {
                if (select.value !== '') chosen[select.value] = select;
            });

            selects.forEach(function (select) {
                Array.prototype.forEach.call(select.options, function (option) {
                    if (option.value === '') return;
                    // Skip options already disabled from server-side (auto-detected columns)
                    if (option.dataset.serverDisabled) return;

                    var takenBy = chosen[option.value];
                    var isOwnSelection = (select.value === option.value);
                    option.disabled = !!(takenBy && takenBy !== select && !isOwnSelection);
                });
            });
        }

        function checkFormComplete() {
            var allFilled = selects.every(function (select) {
                return select.value !== '';
            });

            var btn = document.getElementById('start-import-btn');
            var warning = document.getElementById('mapping-warning');

            if (allFilled) {
                btn.disabled = false;
                btn.classList.remove('bg-slate-300', 'cursor-not-allowed');
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                warning.style.display = 'none';
            } else {
                btn.disabled = true;
                btn.classList.add('bg-slate-300', 'cursor-not-allowed');
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                warning.style.display = 'block';
            }
        }

        // Mark options that were disabled server-side (already used by auto-detected fields)
        // so the duplicate-check logic above doesn't try to re-enable them.
        selects.forEach(function (select) {
            Array.prototype.forEach.call(select.options, function (option) {
                if (option.disabled) option.dataset.serverDisabled = 'true';
            });
            select.addEventListener('change', function () {
                updateDuplicateOptions();
                checkFormComplete();
            });
        });

        updateDuplicateOptions();
        checkFormComplete();
    })();
</script>
@endif

</body>
</html>