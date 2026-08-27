<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Partner Schools – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50/50 text-slate-900 antialiased selection:bg-blue-500 selection:text-white" x-data="schoolFilter()">

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
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-blue-600 bg-blue-50/80">Partner Schools</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-100">Portal Zone</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-1">Partner School Networks</h1>
            <p class="text-sm text-slate-500 mt-1">Manage affiliate academic institutions and available student placement counts.</p>
        </div>
        <a href="{{ route('coordinator.partner-schools.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/15 hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all self-start sm:self-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Register School
        </a>
    </div>

    {{-- Interactive Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card: Total Schools -->
        <div @click="resetFilters()" 
             class="group bg-white rounded-2xl border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md hover:border-blue-300 hover:shadow-blue-100/50 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             :class="{ 'border-blue-500 ring-2 ring-blue-100 bg-blue-50/10': !search && !filterType && !filterFull && !filterAvailable }">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition duration-200"
                     :class="{ 'bg-blue-600 text-white': !search && !filterType && !filterFull && !filterAvailable }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33l-7.5-5-7.5 5V21M3 21h18" /></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 group-hover:text-blue-500 transition">Reset View</span>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-800 tracking-tight leading-none">{{ $totalPartnerSchools }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1.5">Total Registered Partner Schools</p>
            </div>
        </div>

        <!-- Card: Open Slots -->
        <div @click="toggleAvailable()" 
             class="group bg-white rounded-2xl border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md hover:border-emerald-300 hover:shadow-emerald-100/50 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             :class="{ 'border-emerald-500 ring-2 ring-emerald-100 bg-emerald-50/10': filterAvailable }">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition duration-200"
                     :class="{ 'bg-emerald-600 text-white': filterAvailable }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 group-hover:text-emerald-500 transition" x-text="filterAvailable ? 'Active' : 'Filter'">Filter</span>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-800 tracking-tight leading-none">{{ $totalPartnerSchools - $fullSlotsCount }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1.5">Institutions with Vacant Slots</p>
            </div>
        </div>

        <!-- Card: Completely Occupied -->
        <div @click="toggleFull()" 
             class="group bg-white rounded-2xl border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md hover:border-amber-300 hover:shadow-amber-100/50 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
             :class="{ 'border-amber-500 ring-2 ring-amber-100 bg-amber-50/10': filterFull }">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center border bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition duration-200"
                     :class="{ 'bg-amber-600 text-white': filterFull }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 group-hover:text-amber-500 transition" x-text="filterFull ? 'Active' : 'Filter'">Filter</span>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-black text-slate-800 tracking-tight leading-none">{{ $fullSlotsCount }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-1.5">Completely Occupied Schools</p>
            </div>
        </div>
    </div>

    {{-- Search & Filters Panel --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col lg:flex-row gap-4 items-center justify-between shadow-sm">
        <div class="w-full lg:w-1/3 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
            </span>
            <input type="text" x-model="search" placeholder="Search school name, location, or contact details..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200">
        </div>

        <div class="w-full lg:w-auto flex flex-wrap gap-2.5 items-center">
            <!-- Filter: Type -->
            <select x-model="filterType" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                <option value="">All Institutional Types</option>
                <option value="University">University</option>
                <option value="College">College</option>
                <option value="Training Center">Training Center</option>
                <option value="Government Agency">Government Agency</option>
            </select>

            <div class="w-px h-6 bg-slate-200 hidden lg:block"></div>

            <!-- Sort Selection -->
            <select x-model="sort" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                <option value="newest">Newest Registered</option>
                <option value="oldest">Oldest Registered</option>
                <option value="alphabetical">Alphabetical (A-Z)</option>
            </select>
        </div>
    </div>

    {{-- Main Results Data Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm shadow-blue-50/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4.5">School Details</th>
                        <th class="px-6 py-4.5">Location</th>
                        <th class="px-6 py-4.5">Primary Contact</th>
                        <th class="px-6 py-4.5 text-center">Slots Structure</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <template x-for="school in filteredSchools" :key="school.id">
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="font-extrabold text-slate-800 leading-tight" x-text="school.school_name"></span>
                                    <div>
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-500 border border-slate-200/50" x-text="school.school_type"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="block max-w-[240px] truncate text-slate-500" x-text="school.address"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700" x-text="school.contact_person || 'N/A'"></span>
                                    <span class="text-xs text-slate-400" x-text="school.contact_number || 'No Contact Phone'"></span>
                                    <span class="text-xs text-slate-400 select-all font-mono" x-text="school.email || ''"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-4 text-center">
                                    <div>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total</span>
                                        <span class="block font-black text-slate-700" x-text="school.available_slots"></span>
                                    </div>
                                    <div class="h-6 w-px bg-slate-100"></div>
                                    <div>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Occ</span>
                                        <span class="block font-black text-slate-700" x-text="school.occupied_slots || 0"></span>
                                    </div>
                                    <div class="h-6 w-px bg-slate-100"></div>
                                    <div>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Remaining</span>
                                        <span class="inline-block px-2 py-0.5 rounded font-black text-xs mt-0.5"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-800': school.remaining_slots > 5,
                                                  'bg-amber-100 text-amber-800': school.remaining_slots <= 5 && school.remaining_slots > 0,
                                                  'bg-rose-100 text-rose-800': school.remaining_slots <= 0
                                              }" x-text="school.remaining_slots <= 0 ? 'Full' : school.remaining_slots"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/coordinator/partner-schools/${school.id}/edit`" 
                                       class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white transition duration-150 border border-slate-200/50 hover:border-blue-600">
                                        Manage
                                    </a>
                                    <form :action="`/coordinator/partner-schools/${school.id}`" method="POST" onsubmit="return confirm('Are you sure you want to delete this partner school? This action cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredSchools.length === 0">
                        <td colspan="5" class="text-center py-16 text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m16.5 0a1.5 1.5 0 0 0-1.5-1.5H18.512a2.25 2.25 0 0 0-1.802-.872c-.475 0-.945.074-1.392.218A2.245 2.245 0 0 0 13.5 3h-3a2.245 2.245 0 0 0-1.818 1.146 2.25 2.25 0 0 0-1.392-.218c-.474 0-.943.074-1.391.218C5.011 4.5 4.5 5.25 4.5 6h-.75a1.5 1.5 0 0 0-1.5 1.5m16.5 0v2.25m-16.5 0v2.25m13.5-3v2.25m-10.5-2.25v2.25M10.5 13.5h3" /></svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">No partner schools configured</p>
                            <p class="text-xs text-slate-400 mt-1">Adjust your search parameters or register a new campus network location.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function schoolFilter() {
    return {
        search: '',
        filterType: '',
        filterFull: false,
        filterAvailable: false,
        sort: 'newest',
        schools: @json($partnerSchools),

        get filteredSchools() {
            let result = this.schools.filter(school => {
                const searchString = `${school.school_name} ${school.address} ${school.contact_person}`.toLowerCase();
                const matchesSearch = searchString.includes(this.search.toLowerCase());
                const matchesType = !this.filterType || school.school_type === this.filterType;
                const matchesFull = !this.filterFull || school.remaining_slots <= 0;
                const matchesAvailable = !this.filterAvailable || school.remaining_slots > 0;

                return matchesSearch && matchesType && matchesFull && matchesAvailable;
            });

            if (this.sort === 'alphabetical') {
                result.sort((a, b) => a.school_name.localeCompare(b.school_name));
            } else if (this.sort === 'oldest') {
                result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            } else {
                result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            }

            return result;
        },

        resetFilters() {
            this.search = '';
            this.filterType = '';
            this.filterFull = false;
            this.filterAvailable = false;
            this.sort = 'newest';
        },

        toggleAvailable() {
            this.filterAvailable = !this.filterAvailable;
            if (this.filterAvailable) this.filterFull = false;
        },

        toggleFull() {
            this.filterFull = !this.filterFull;
            if (this.filterFull) this.filterAvailable = false;
        }
    }
}
</script>
</body>
</html>