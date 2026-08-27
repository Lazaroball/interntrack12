{{-- resources/views/coordinator/partner-schools/edit.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Partner School – InternTrack</title>

    {{-- MapLibre GL JS & Turf.js --}}
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" />
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm shadow-blue-50">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-slate-800 tracking-tight">InternTrack</span>
                    <span class="hidden sm:block text-[10px] font-semibold text-blue-500 tracking-widest uppercase -mt-0.5">UCU · CTE</span>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('coordinator.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">Dashboard</a>
                <a href="{{ route('coordinator.students.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">Students</a>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Partner Schools</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">Deployments</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-2">
            <a href="{{ route('coordinator.partner-schools.index') }}" class="hover:text-blue-600 font-medium">Partner Schools</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-slate-500 font-medium">Edit School</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Partner School</h1>
        <p class="text-sm text-slate-400">Update institutional deployment details, geofenced location, and MOA documents.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 max-w-4xl">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl mb-5">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('coordinator.partner-schools.update', $partnerSchool->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. Institutional Selection --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">1. School Information</h2>

                <div>
                    <label for="school_select" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Select Institutional Partner <span class="text-red-500">*</span></label>
                    <select id="school_select" class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="" disabled>Loading schools...</option>
                    </select>
                </div>

                {{-- Hidden Input for School Name --}}
                <input type="hidden" name="school_name" id="school_name" value="{{ old('school_name', $partnerSchool->school_name) }}" />

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="sm:col-span-2">
                        <label for="school_type" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">School Type <span class="text-red-500">*</span></label>
                        <select name="school_type" id="school_type" required
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('school_type') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                            <option value="" disabled>Select type...</option>
                            <option value="School" {{ old('school_type', $partnerSchool->school_type) == 'School' ? 'selected' : '' }}>Basic Education (K-12)</option>
                            <option value="University" {{ old('school_type', $partnerSchool->school_type) == 'University' ? 'selected' : '' }}>Higher Education Institution (HEI)</option>
                            <option value="Training Center" {{ old('school_type', $partnerSchool->school_type) == 'Training Center' ? 'selected' : '' }}>Training Center</option>
                            <option value="LGU" {{ old('school_type', $partnerSchool->school_type) == 'LGU' ? 'selected' : '' }}>Government Agency / LGU</option>
                            <option value="Other" {{ old('school_type', $partnerSchool->school_type) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="available_slots" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Available Slots <span class="text-red-500">*</span></label>
                        <input type="number" name="available_slots" id="available_slots" min="0" value="{{ old('available_slots', $partnerSchool->available_slots) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('available_slots') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                    </div>
                </div>

                {{-- Other School Type (shown only when "Other" is selected) --}}
                <div id="other-school-type-wrapper" class="{{ old('school_type', $partnerSchool->school_type) == 'Other' ? '' : 'hidden' }}">
                    <label for="other_school_type" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Specify School Type <span class="text-red-500">*</span></label>
                    <input type="text" name="other_school_type" id="other_school_type" value="{{ old('other_school_type') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('other_school_type') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" placeholder="e.g. Non-Government Organization" />
                </div>
            </div>

            <hr class="border-slate-100" />

            {{-- 2. Interactive Map & Geofenced Location --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">2. Interactive Location & Geofence</h2>
                        <p class="text-xs text-slate-500">Search for an address or drag the pin to position the school accurately on the map.</p>
                    </div>
                </div>

                {{-- Address Search Bar --}}
                <div class="relative">
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">School Address <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="address" id="address" value="{{ old('address', $partnerSchool->address) }}" placeholder="e.g. San Vicente, Urdaneta City, Pangasinan" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('address') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                        <button type="button" id="btn-search-address" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl shrink-0 transition-colors">
                            Locate
                        </button>
                    </div>
                    <div id="search-results" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-30 max-h-48 overflow-y-auto divide-y divide-slate-100"></div>
                </div>

                {{-- Maplibre Container --}}
                <div class="relative w-full h-80 rounded-2xl overflow-hidden border-2 border-slate-200">
                    <div id="map" class="w-full h-full"></div>
                    <div class="absolute bottom-2 left-2 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg shadow border border-slate-200 text-[11px] font-medium text-slate-600 z-10">
                        💡 Drag pin or click anywhere on the map to set coordinates
                    </div>
                </div>

                {{-- Latitude, Longitude & Geofence Radius Inputs --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <div>
                        <label for="latitude" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $partnerSchool->latitude ?? 15.9758) }}" required readonly
                               class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono text-slate-700 cursor-not-allowed" />
                    </div>
                    <div>
                        <label for="longitude" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $partnerSchool->longitude ?? 120.5707) }}" required readonly
                               class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono text-slate-700 cursor-not-allowed" />
                    </div>
                    <div>
                       <label for="radius" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
    Geofence Radius (meters)
</label>
<input type="number" min="10" max="2000" name="radius_meters" id="radius"
       value="{{ old('radius_meters', $partnerSchool->radius_meters ?? $partnerSchool->radius ?? 100) }}"
       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-700 focus:ring-2 focus:ring-blue-300" />
                    </div>
                </div>
            </div>

            <hr class="border-slate-100" />

            {{-- 3. Contact Details --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">3. Primary Contact</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="contact_person" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $partnerSchool->contact_person) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                    </div>
                    <div>
                        <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $partnerSchool->contact_number) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $partnerSchool->email) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('email') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                    </div>
                </div>
            </div>

            <hr class="border-slate-100" />

            {{-- 4. MOA & Documents --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">4. Memorandum of Agreement (MOA)</h2>

                {{-- Current MOA Status (read from the database, not recalculated) --}}
                @if($partnerSchool->moa_started_at || $partnerSchool->moa_expires_at)
                    <div class="p-3 rounded-xl border text-xs font-medium flex flex-wrap items-center gap-x-4 gap-y-1
                        {{ $partnerSchool->hasValidMoa() ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                        <span class="font-bold uppercase tracking-wider text-[10px]">
                            {{ $partnerSchool->hasValidMoa() ? 'Active MOA' : 'Inactive / Expired MOA' }}
                        </span>
                        @if($partnerSchool->moa_started_at)
                            <span>Started: {{ $partnerSchool->moa_started_at->format('M d, Y') }}</span>
                        @endif
                        @if($partnerSchool->moa_expires_at)
                            <span>Expires: {{ $partnerSchool->moa_expires_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- MOA Start Date --}}
                    <div>
                        <label for="moa_started_at" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            MOA Start Date <span class="text-slate-400 lowercase font-normal">(optional)</span>
                        </label>
                        <input type="date" name="moa_started_at" id="moa_started_at"
                               value="{{ old('moa_started_at', optional($partnerSchool->moa_started_at)->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('moa_started_at') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                    </div>

                    {{-- MOA Expiration Date --}}
                    <div>
                        <label for="moa_expires_at" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            MOA Expiration Date <span class="text-slate-400 lowercase font-normal">(optional)</span>
                        </label>
                        <input type="date" name="moa_expires_at" id="moa_expires_at"
                               value="{{ old('moa_expires_at', optional($partnerSchool->moa_expires_at)->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('moa_expires_at') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                        <p class="text-[11px] text-slate-400 mt-1">Must be on or after the start date.</p>
                    </div>
                </div>

                {{-- MOA PDF Upload --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                        MOA Document <span class="text-slate-400 lowercase font-normal">(optional - PDF max 5MB)</span>
                    </label>

                    @if(!empty($partnerSchool->moa_file))
                        <div id="existing-file-badge" class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="p-1.5 bg-red-100 text-red-600 rounded-lg shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.5L14.5 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-700 truncate">Current Stored MOA Document</p>
                                    <p class="text-[10px] text-slate-400">Saved on server</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($partnerSchool->moa_file) }}" target="_blank" class="px-2.5 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors shrink-0">
                                View PDF
                            </a>
                        </div>
                    @endif

                    <div id="drop-zone" class="relative group border-2 border-dashed border-slate-200 hover:border-blue-400 bg-slate-50/50 hover:bg-blue-50/30 rounded-2xl p-4 text-center transition-all cursor-pointer">
                        <input type="file" name="moa_file" id="moa_file" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewSelectedFile(event)" />

                        <div id="upload-prompt" class="space-y-1">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-700">
                                <span class="text-blue-600 underline">Click to upload new file</span> or drag and drop
                            </p>
                            <p class="text-[11px] text-slate-400">PDF up to 5MB</p>
                        </div>

                        <div id="new-file-preview" class="hidden relative z-20 flex items-center justify-between bg-white border border-blue-200 rounded-xl p-3 shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2 bg-red-50 text-red-500 rounded-lg shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-800 truncate" id="preview-filename">filename.pdf</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Ready to Upload</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400" id="preview-filesize">0 KB</span>
                                </div>
                            </div>

                            <button type="button" onclick="removeSelectedFile(event)" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Remove selected file">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="remarks" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="2" placeholder="Special tracks available, key restrictions or notes..."
                              class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300">{{ old('remarks', $partnerSchool->remarks) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Update Partner School
                </button>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ensure MapLibre GL JS is loaded
        if (typeof maplibregl === 'undefined') {
            console.error('MapLibre GL JS script is missing or failed to load.');
            return;
        }

        // --- 1. Institutional Selection Dropdown ---
        const selectElement = document.getElementById('school_select');
        const hiddenInput = document.getElementById('school_name');
        const addressInput = document.getElementById('address');
        const currentSchoolName = @json(old('school_name', $partnerSchool->school_name));

        async function loadSchoolList() {
            try {
                const response = await fetch('https://raw.githubusercontent.com/faeldon/philippines-json-data/master/geojson/regions/master-list/schools.geojson');
                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);

                const data = await response.json();
                selectElement.innerHTML = '<option value="" disabled>Select a school from list...</option>';

                let matched = false;
                const features = data.features || [];

                features.slice(0, 500).forEach(item => {
                    const name = item.properties.school_name || item.properties.name;
                    if (!name) return;

                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;

                    if (name === currentSchoolName) {
                        option.selected = true;
                        matched = true;
                    }
                    selectElement.appendChild(option);
                });

                if (!matched && currentSchoolName) {
                    const customOption = document.createElement('option');
                    customOption.value = currentSchoolName;
                    customOption.textContent = `${currentSchoolName} (Current Saved Value)`;
                    customOption.selected = true;
                    selectElement.insertBefore(customOption, selectElement.firstChild);
                }

            } catch (error) {
                console.warn('External schools dataset unreadable:', error);
                selectElement.innerHTML = '<option value="" disabled>Preloaded list unavailable - enter school manually below</option>';
                if (currentSchoolName) {
                    const customOption = document.createElement('option');
                    customOption.value = currentSchoolName;
                    customOption.textContent = currentSchoolName;
                    customOption.selected = true;
                    selectElement.appendChild(customOption);
                }
            }
        }

        loadSchoolList();

        selectElement.addEventListener('change', (e) => {
            hiddenInput.value = e.target.value;
        });

        // --- 2. MapLibre Map Setup ---
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');

        let initialLat = parseFloat(latInput.value) || 15.9758;
        let initialLng = parseFloat(lngInput.value) || 120.5707;
        let initialRadius = parseInt(radiusInput.value) || 100;

        const map = new maplibregl.Map({
            container: 'map',
            style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            center: [initialLng, initialLat],
            zoom: 15
        });

        map.addControl(new maplibregl.NavigationControl());

        const marker = new maplibregl.Marker({ draggable: true, color: '#2563eb' })
            .setLngLat([initialLng, initialLat])
            .addTo(map);

        function updateGeofenceCircle(lng, lat, radiusMeters) {
            if (typeof turf === 'undefined') return;
            const center = [lng, lat];
            const circleFeature = turf.circle(center, radiusMeters / 1000, { units: 'kilometers' });

            if (map.getSource('geofence-circle')) {
                map.getSource('geofence-circle').setData(circleFeature);
            } else {
                map.addSource('geofence-circle', {
                    type: 'geojson',
                    data: circleFeature
                });

                map.addLayer({
                    id: 'geofence-circle-fill',
                    type: 'fill',
                    source: 'geofence-circle',
                    paint: { 'fill-color': '#3b82f6', 'fill-opacity': 0.2 }
                });

                map.addLayer({
                    id: 'geofence-circle-stroke',
                    type: 'line',
                    source: 'geofence-circle',
                    paint: { 'line-color': '#2563eb', 'line-width': 2 }
                });
            }
        }

        map.on('load', () => {
            setTimeout(() => map.resize(), 150);
            updateGeofenceCircle(initialLng, initialLat, initialRadius);
        });

        function updateCoordinates(lng, lat) {
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
            const currentRadius = parseInt(radiusInput.value) || 100;
            updateGeofenceCircle(lng, lat, currentRadius);
        }

        marker.on('dragend', () => {
            const lngLat = marker.getLngLat();
            updateCoordinates(lngLat.lng, lngLat.lat);
        });

        map.on('click', (e) => {
            marker.setLngLat(e.lngLat);
            updateCoordinates(e.lngLat.lng, e.lngLat.lat);
        });

        radiusInput.addEventListener('input', () => {
            const lngLat = marker.getLngLat();
            const radius = parseInt(radiusInput.value) || 100;
            updateGeofenceCircle(lngLat.lng, lngLat.lat, radius);
        });

        // --- 3. Photon Geocoding Search ---
        const btnSearch = document.getElementById('btn-search-address');
        const searchResults = document.getElementById('search-results');

        async function geocodeAddress(query) {
            if (!query.trim()) return;
            try {
                const response = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(query)}&limit=5`);
                const data = await response.json();

                searchResults.innerHTML = '';
                if (!data.features || data.features.length === 0) {
                    searchResults.classList.add('hidden');
                    return;
                }

                data.features.forEach(feature => {
                    const coords = feature.geometry.coordinates;
                    const name = feature.properties.name || '';
                    const city = feature.properties.city || feature.properties.county || '';
                    const state = feature.properties.state || '';
                    const formatted = [name, city, state].filter(Boolean).join(', ');

                    const item = document.createElement('div');
                    item.className = 'px-3.5 py-2 hover:bg-blue-50 text-xs font-medium cursor-pointer text-slate-700';
                    item.textContent = formatted || query;
                    item.addEventListener('click', () => {
                        const [lng, lat] = coords;
                        addressInput.value = formatted || query;
                        marker.setLngLat([lng, lat]);
                        map.flyTo({ center: [lng, lat], zoom: 16 });
                        updateCoordinates(lng, lat);
                        searchResults.classList.add('hidden');
                    });
                    searchResults.appendChild(item);
                });

                searchResults.classList.remove('hidden');
            } catch (err) {
                console.error('Geocoding search error:', err);
            }
        }

        if (btnSearch) {
            btnSearch.addEventListener('click', () => geocodeAddress(addressInput.value));
        }

        if (addressInput) {
            addressInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    geocodeAddress(addressInput.value);
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!searchResults.contains(e.target) && e.target !== addressInput && e.target !== btnSearch) {
                searchResults.classList.add('hidden');
            }
        });

        // --- 4. Toggle "Other" school type input ---
        const schoolTypeSelect = document.getElementById('school_type');
        const otherTypeWrapper = document.getElementById('other-school-type-wrapper');
        const otherTypeInput = document.getElementById('other_school_type');

        function toggleOtherType() {
            if (schoolTypeSelect.value === 'Other') {
                otherTypeWrapper.classList.remove('hidden');
            } else {
                otherTypeWrapper.classList.add('hidden');
                otherTypeInput.value = '';
            }
        }

        schoolTypeSelect.addEventListener('change', toggleOtherType);
    });

    // --- File Upload Helpers ---
    function previewSelectedFile(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        if (file) {
            if (file.type !== 'application/pdf') {
                alert('Please select a PDF document.');
                fileInput.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size exceeds the 5MB limit.');
                fileInput.value = '';
                return;
            }

            document.getElementById('preview-filename').textContent = file.name;
            document.getElementById('preview-filesize').textContent = formatBytes(file.size);

            document.getElementById('upload-prompt').classList.add('hidden');
            document.getElementById('new-file-preview').classList.remove('hidden');
        }
    }

    function removeSelectedFile(event) {
        event.stopPropagation();
        const fileInput = document.getElementById('moa_file');
        fileInput.value = '';

        document.getElementById('new-file-preview').classList.add('hidden');
        document.getElementById('upload-prompt').classList.remove('hidden');
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>

</body>
</html>