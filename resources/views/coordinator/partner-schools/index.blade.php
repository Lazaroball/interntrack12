{{-- resources/views/coordinator/partner-schools/index.blade.php --}}

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

{{-- Shared coordinator navigation (same as dashboard) --}}
@include('coordinator.partials.navbar')

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
   {{-- Search & Filters Panel --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col lg:flex-row gap-4 items-center justify-between shadow-sm">
        <div class="w-full lg:w-1/3 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
            </span>
            <input type="text" x-model="search" placeholder="Search school name, address, or contact person..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200">
        </div>

        <div class="w-full lg:w-auto flex flex-wrap gap-2.5 items-center">
            <!-- Filter: Institutional Type (dynamic) -->
            <select x-model="filterType" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                <option value="">All Institutional Types</option>
                @foreach ($schoolTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>

            <!-- Filter: MOA Status -->
            <select x-model="filterMoa" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                <option value="">All MOA Statuses</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="renewal_needed">Renewal Needed</option>
            </select>

            <!-- Filter: Acceptance -->
            <select x-model="filterAcceptance" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                <option value="">All Acceptance</option>
                <option value="accepting">Accepting</option>
                <option value="full">Full</option>
                <option value="closed">Closed</option>
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
                        <th class="px-6 py-4.5">Geographic Location</th>
                        <th class="px-6 py-4.5">Primary Contact</th>
                        <th class="px-6 py-4.5">MOA Status</th>
                        <th class="px-6 py-4.5 text-center">Slot Capacity</th>
                        <th class="px-6 py-4.5">Acceptance</th>
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
                                <div class="flex flex-col gap-0.5">
                                    <template x-if="school.contact_person || school.contact_number">
                                        <div class="flex flex-col">
                                            <span x-show="school.contact_person" class="font-bold text-slate-700" x-text="school.contact_person"></span>
                                            <span x-show="school.contact_number" class="text-xs text-slate-400" x-text="school.contact_number"></span>
                                        </div>
                                    </template>
                                    <span x-show="!school.contact_person && !school.contact_number" class="text-xs italic text-slate-400">Not Provided</span>
                                    <span x-show="school.email" class="text-xs text-slate-400 select-all font-mono" x-text="school.email"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide"
                                      :class="{
                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': school.moa_status_display === 'active',
                                          'bg-rose-50 text-rose-700 border border-rose-200': school.moa_status_display === 'expired',
                                          'bg-amber-50 text-amber-700 border border-amber-200': school.moa_status_display === 'renewal_needed'
                                      }"
                                      x-text="moaLabel(school.moa_status_display)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-4 text-center">
                                    <div>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total</span>
                                        <span class="block font-black text-slate-700" x-text="school.available_slots"></span>
                                    </div>
                                    <div class="h-6 w-px bg-slate-100"></div>
                                    <div>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Occupied</span>
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
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide"
                                      :class="{
                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': school.acceptance_status === 'accepting',
                                          'bg-rose-50 text-rose-700 border border-rose-200': school.acceptance_status === 'full',
                                          'bg-slate-100 text-slate-600 border border-slate-200': school.acceptance_status === 'closed'
                                      }"
                                      x-text="acceptanceLabel(school.acceptance_status)"></span>
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
                        <td colspan="7" class="text-center py-16 text-slate-400">
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
        filterMoa: '',
        filterAcceptance: '',
        sort: 'newest',
        schools: @json($partnerSchools),

        get filteredSchools() {
            const searchTerm = (this.search || '').toLowerCase();

            let result = this.schools.filter(school => {
                const name = (school.school_name || '').toLowerCase();
                const address = (school.address || '').toLowerCase();
                const contactPerson = (school.contact_person || '').toLowerCase();
                const contactNumber = (school.contact_number || '').toLowerCase();

                const matchesSearch = !searchTerm ||
                    name.includes(searchTerm) ||
                    address.includes(searchTerm) ||
                    contactPerson.includes(searchTerm) ||
                    contactNumber.includes(searchTerm);

                const matchesType = !this.filterType || school.school_type === this.filterType;
                const matchesMoa = !this.filterMoa || school.moa_status_display === this.filterMoa;
                const matchesAcceptance = !this.filterAcceptance || school.acceptance_status === this.filterAcceptance;

                return matchesSearch && matchesType && matchesMoa && matchesAcceptance;
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
            this.filterMoa = '';
            this.filterAcceptance = '';
            this.sort = 'newest';
        },

        toggleAcceptance(value) {
            this.filterAcceptance = this.filterAcceptance === value ? '' : value;
        },

        toggleMoa(value) {
            this.filterMoa = this.filterMoa === value ? '' : value;
        },

        moaLabel(status) {
            switch (status) {
                case 'active': return 'Active';
                case 'expired': return 'Expired';
                case 'renewal_needed': return 'Renewal Needed';
                default: return 'Unknown';
            }
        },

        acceptanceLabel(status) {
            switch (status) {
                case 'accepting': return 'Accepting';
                case 'full': return 'Full';
                case 'closed': return 'Closed';
                default: return 'Unknown';
            }
        }
    }
}
</script>
</body>
</html>