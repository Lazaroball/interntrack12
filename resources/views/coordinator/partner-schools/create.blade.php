{{-- resources/views/coordinator/partner-schools/create.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Partner School – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- MapLibre GL JS CSS -->
    <link href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" rel="stylesheet" />

    <!-- MapLibre Geocoder Control CSS (Search Bar) -->
    <link rel="stylesheet" href="https://unpkg.com/@maplibre/maplibre-gl-geocoder@1.5.0/dist/maplibre-gl-geocoder.css" />

    <!-- Turf.js (Required for drawing geofence circle in MapLibre) -->
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

    <style>
        /* Fix map geocoder control stacking context */
        .maplibregl-ctrl-top-left {
            z-index: 10;
        }
        .maplibregl-ctrl-geocoder {
            min-width: 280px;
            font-family: inherit;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

{{-- Shared coordinator navigation (same as dashboard) --}}
@include('coordinator.partials.navbar')

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-2">
            <a href="{{ route('coordinator.partner-schools.index') }}" class="hover:text-blue-600 font-medium">Partner Schools</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-slate-500 font-medium">Add New</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Register Partner School</h1>
        <p class="text-sm text-slate-400">Add an institutional deployment site optimized for CTE placements.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 p-6 max-w-3xl">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-xl mb-5">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('coordinator.partner-schools.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- School Name --}}
            <div>
                <label for="school_name" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">School Name <span class="text-red-500">*</span></label>
                <input type="text" name="school_name" id="school_name" value="{{ old('school_name') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('school_name') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" placeholder="e.g. Pangasinan National High School" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                {{-- School Type --}}
                <div class="sm:col-span-2">
                    <label for="school_type" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">School Type <span class="text-red-500">*</span></label>
                    <select name="school_type" id="school_type" required
                            class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('school_type') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <option value="" disabled {{ old('school_type') ? '' : 'selected' }}>Select type...</option>
                        <option value="School" {{ old('school_type') == 'School' ? 'selected' : '' }}>Basic Education (K-12)</option>
                        <option value="University" {{ old('school_type') == 'University' ? 'selected' : '' }}>Higher Education Institution (HEI)</option>
                        <option value="Training Center" {{ old('school_type') == 'Training Center' ? 'selected' : '' }}>Training Center</option>
                        <option value="LGU" {{ old('school_type') == 'LGU' ? 'selected' : '' }}>Government Agency / LGU</option>
                        <option value="Other" {{ old('school_type') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Available Allocation Slots --}}
                <div>
                    <label for="available_slots" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Available Slots <span class="text-red-500">*</span></label>
                    <input type="number" name="available_slots" id="available_slots" min="0" value="{{ old('available_slots', 0) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('available_slots') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                </div>
            </div>

            {{-- Other School Type (shown only when "Other" is selected) --}}
            <div id="other-school-type-wrapper" class="{{ old('school_type') == 'Other' ? '' : 'hidden' }}">
                <label for="other_school_type" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Specify School Type <span class="text-red-500">*</span></label>
                <input type="text" name="other_school_type" id="other_school_type" value="{{ old('other_school_type') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('other_school_type') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" placeholder="e.g. Non-Government Organization" />
            </div>

            {{-- Geofence Location Picker (MapLibre Map) — sole source of address/lat/lng --}}
            <div class="border-2 border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Location & Address <span class="text-red-500">*</span></h3>
                        <p class="text-xs text-slate-500">Search for a location using the map search bar, or click/drag the marker pin. The address below fills in automatically.</p>
                    </div>

                    {{-- Radius Input --}}
                    <div class="w-full sm:w-48">
                        <label for="radius_meters" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Radius (Meters) <span class="text-red-500">*</span></label>
                        <input type="number" name="radius_meters" id="radius_meters" min="10" max="1000" value="{{ old('radius_meters', 100) }}" required
                               class="w-full px-3 py-1.5 rounded-lg border-2 border-slate-200 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-blue-300" />
                    </div>
                </div>

                {{-- Hidden Address/Coordinates Inputs (populated by the map, still submitted as before) --}}
                <input type="hidden" name="address" id="address" value="{{ old('address') }}">
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                {{-- MapLibre Container --}}
                <div id="school-map" class="h-80 w-full rounded-xl border border-slate-300 relative z-0"></div>

                {{-- Selected Address & Coordinates Display (read-only, reflects the hidden inputs) --}}
                <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 space-y-1 @error('address') ring-2 ring-red-300 @enderror">
                    <p class="text-xs text-slate-500">
                        Selected Address: <strong id="address-display" class="text-slate-700">{{ old('address', 'Not set — pick a location on the map') }}</strong>
                    </p>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Latitude: <strong id="lat-display" class="text-slate-700">{{ old('latitude', 'Not set') }}</strong></span>
                        <span>Longitude: <strong id="lng-display" class="text-slate-700">{{ old('longitude', 'Not set') }}</strong></span>
                    </div>
                </div>
                @error('address')
                    <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Information --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="contact_person" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                </div>
                <div>
                    <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('email') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300" placeholder="Optional" />
                </div>
            </div>

            {{-- Memorandum of Agreement (MOA) --}}
            <div class="border-2 border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Memorandum of Agreement (MOA)</h3>
                    <p class="text-xs text-slate-500">Optional at registration. Set the start and expiration dates directly.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- MOA Start Date --}}
                    <div>
                        <label for="moa_started_at" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">MOA Start Date <span class="text-slate-400 lowercase font-normal">(optional)</span></label>
                        <input type="date" name="moa_started_at" id="moa_started_at" value="{{ old('moa_started_at') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('moa_started_at') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                    </div>

                    {{-- MOA Expiration Date --}}
                    <div>
                        <label for="moa_expires_at" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">MOA Expiration Date <span class="text-slate-400 lowercase font-normal">(optional)</span></label>
                        <input type="date" name="moa_expires_at" id="moa_expires_at" value="{{ old('moa_expires_at') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 @error('moa_expires_at') border-red-300 @else border-slate-200 @enderror bg-white text-sm text-slate-700 outline-none transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                        <p class="text-[11px] text-slate-400 mt-1">Must be on or after the start date.</p>
                    </div>
                </div>

                {{-- MOA PDF Upload --}}
                <div>
                    <label for="moa_file" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">MOA Document <span class="text-slate-400 lowercase font-normal">(optional - PDF max 5MB)</span></label>
                    <input type="file" name="moa_file" id="moa_file" accept="application/pdf"
                           class="w-full px-3 py-2 rounded-xl border-2 border-dashed @error('moa_file') border-red-300 @else border-slate-200 @enderror text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            </div>

            {{-- Remarks --}}
            <div>
                <label for="remarks" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2" placeholder="Special tracks available, key restrictions or notes..."
                          class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-300">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                    Save Partner School
                </button>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<!-- MapLibre JS -->
<script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>

<!-- MapLibre Geocoder JS -->
<script src="https://unpkg.com/@maplibre/maplibre-gl-geocoder@1.5.0/dist/maplibre-gl-geocoder.min.js"></script>

<!-- MapLibre Setup Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultLat = 15.9281;
    const defaultLng = 120.3481;

    const addressInput = document.getElementById('address');
    const addressDisplay = document.getElementById('address-display');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const latDisplay = document.getElementById('lat-display');
    const lngDisplay = document.getElementById('lng-display');
    const radiusInput = document.getElementById('radius_meters');

    const initialLat = parseFloat(latInput.value) || defaultLat;
    const initialLng = parseFloat(lngInput.value) || defaultLng;
    let currentRadius = parseInt(radiusInput.value) || 100;

    // 1. Initialize MapLibre GL Map
    const map = new maplibregl.Map({
        container: 'school-map',
        style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
        center: [initialLng, initialLat],
        zoom: latInput.value ? 16 : 14
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-right');

    let marker = null;

    // Helper: Draw or Update Geofence Circle Layer using Turf.js
    function updateCircleLayer(lng, lat, radiusMeters) {
        if (!map.isStyleLoaded()) return;

        const circleGeoJson = turf.circle([lng, lat], radiusMeters / 1000, {
            steps: 64,
            units: 'kilometers'
        });

        if (map.getSource('geofence-source')) {
            map.getSource('geofence-source').setData(circleGeoJson);
        } else {
            map.addSource('geofence-source', {
                type: 'geojson',
                data: circleGeoJson
            });

            map.addLayer({
                id: 'geofence-fill',
                type: 'fill',
                source: 'geofence-source',
                paint: {
                    'fill-color': '#3b82f6',
                    'fill-opacity': 0.25
                }
            });

            map.addLayer({
                id: 'geofence-stroke',
                type: 'line',
                source: 'geofence-source',
                paint: {
                    'line-color': '#2563eb',
                    'line-width': 2
                }
            });
        }
    }

    // Helper: Reverse-geocode a point into a readable address (Photon by Komoot)
    async function reverseGeocode(lat, lng) {
        try {
            const request = `https://photon.komoot.io/reverse?lon=${lng}&lat=${lat}`;
            const response = await fetch(request);
            const geojson = await response.json();

            const feature = geojson.features && geojson.features[0];
            if (!feature) return null;

            const props = feature.properties;
            const labelParts = [props.name, props.street, props.city || props.town, props.state, props.country].filter(Boolean);
            return labelParts.join(', ') || null;
        } catch (e) {
            console.error(`Reverse geocoding failed: ${e}`);
            return null;
        }
    }

    // Helper: Set Marker, Sync Coordinates & Resolve Address
    async function setLocation(lat, lng, knownAddress = null) {
        const formattedLat = parseFloat(lat).toFixed(7);
        const formattedLng = parseFloat(lng).toFixed(7);

        latInput.value = formattedLat;
        lngInput.value = formattedLng;
        latDisplay.innerText = formattedLat;
        lngDisplay.innerText = formattedLng;

        if (!marker) {
            marker = new maplibregl.Marker({ draggable: true, color: '#2563eb' })
                .setLngLat([lng, lat])
                .addTo(map);

            marker.on('dragend', function () {
                const lngLat = marker.getLngLat();
                setLocation(lngLat.lat, lngLat.lng);
            });
        } else {
            marker.setLngLat([lng, lat]);
        }

        updateCircleLayer(lng, lat, currentRadius);

        // Resolve the address: use the known label from search results,
        // otherwise reverse-geocode the clicked/dragged coordinates.
        addressDisplay.innerText = 'Resolving address...';
        const resolvedAddress = knownAddress || await reverseGeocode(lat, lng);

        addressInput.value = resolvedAddress || '';
        addressDisplay.innerText = resolvedAddress || 'Address not found — try a nearby point';
    }

    // 2. Free, Zero-Key Geocoder API Engine (Photon by Komoot)
    const geocoderApi = {
        forwardGeocode: async (config) => {
            const features = [];
            try {
                const request = `https://photon.komoot.io/api/?q=${encodeURIComponent(config.query)}&limit=5`;
                const response = await fetch(request);
                const geojson = await response.json();

                for (let feature of geojson.features) {
                    const center = feature.geometry.coordinates;
                    const props = feature.properties;
                    const labelParts = [props.name, props.street, props.city || props.town, props.state, props.country].filter(Boolean);
                    const label = labelParts.join(', ');

                    features.push({
                        type: 'Feature',
                        geometry: feature.geometry,
                        place_name: label || props.name || 'Unknown location',
                        text: props.name || label,
                        place_type: ['place'],
                        center: center
                    });
                }
            } catch (e) {
                console.error(`Geocoding search failed: ${e}`);
            }
            return { features: features };
        }
    };

    // Add Geocoder Search Control to Map
    const geocoder = new MaplibreGeocoder(geocoderApi, {
        maplibregl: maplibregl,
        placeholder: 'Search location or school...',
        showResultsWhileTyping: true
    });

    map.addControl(geocoder, 'top-left');

    // Handle Search Selection — use the search result's own label as the address
    geocoder.on('result', function (e) {
        const coords = e.result.geometry.coordinates;
        const lng = coords[0];
        const lat = coords[1];

        map.flyTo({ center: [lng, lat], zoom: 16 });
        setLocation(lat, lng, e.result.place_name);
    });

    // Handle Map Initialization & Click Events
    map.on('load', function () {
        if (latInput.value && lngInput.value) {
            setLocation(initialLat, initialLng, addressInput.value || null);
        }

        map.on('click', function (e) {
            setLocation(e.lngLat.lat, e.lngLat.lng);
        });
    });

    // Dynamic Geofence Radius Adjustment
    radiusInput.addEventListener('input', function () {
        currentRadius = parseInt(this.value) || 100;
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (lat && lng) {
            updateCircleLayer(lng, lat, currentRadius);
        }
    });

    // Toggle "Other" school type input
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
</script>
</body>
</html>