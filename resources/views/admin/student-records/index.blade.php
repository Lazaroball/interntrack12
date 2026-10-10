{{-- resources/views/admin/student-records/index.blade.php --}}
@php
    $archivedTab      = $tab === 'archived';
    $selectedPrograms = array_values(array_filter((array) request('program')));

    $inputCls = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none
                 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 hover:border-blue-300 transition-all duration-150';
    $card     = 'bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50';
@endphp

<x-admin-layout title="Student Records">
    <x-slot name="header">
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Super Admin</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Student Records</h1>
        <p class="text-sm text-slate-400 mt-0.5">Archive, restore or permanently delete student records.</p>
    </x-slot>

    <div class="py-6"
         @keydown.escape.window="close()"
         x-data="{
            selected: [],
            pageIds: @js($students->pluck('id')->values()),
            completedIds: @js($completedIds),
            allMatching: false,
            modal: null,
            single: null,
            reason: 'passed',
            confirmText: '',
            get mode() { return this.allMatching && !this.single ? 'filtered' : 'selected'; },
            get ids() { return this.single ? [this.single.id] : this.selected; },
            get count() { return this.single ? 1 : (this.allMatching ? {{ $students->total() }} : this.selected.length); },
            get eligible() {
                if (this.single) return this.completedIds.includes(this.single.id) ? 1 : 0;
                if (this.allMatching) return {{ $matchingCompleted }};
                return this.selected.filter(id => this.completedIds.includes(id)).length;
            },
            toggleAll(e) { this.selected = e.target.checked ? [...this.pageIds] : []; this.allMatching = false; },
            open(type, student = null) { this.single = student; this.modal = type; this.reason = 'passed'; this.confirmText = ''; },
            close() { this.modal = null; this.single = null; }
         }">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ── Alerts ── --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                     class="flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <span class="font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 hover:text-green-700" aria-label="Dismiss">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="flex items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <span class="font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-500 hover:text-red-700" aria-label="Dismiss">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- ── Summary ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach([
                    ['Active students',     $counts['active']],
                    ['Archived as Passed',  $counts['passed']],
                    ['Archived as Dropped', $counts['dropped']],
                ] as [$label, $value])
                    <div class="{{ $card }} px-5 py-4 hover:shadow-md hover:shadow-blue-100/60 transition-shadow duration-200">
                        <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ $value }}</p>
                        <p class="text-xs font-semibold text-slate-400 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ── Tabs ── --}}
            <div class="flex gap-1 border-b border-slate-200">
                <a href="{{ route('admin.student-records.index', ['tab' => 'active']) }}"
                   class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors
                          {{ !$archivedTab ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Active
                    <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $counts['active'] }}</span>
                </a>
                <a href="{{ route('admin.student-records.index', ['tab' => 'archived']) }}"
                   class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors
                          {{ $archivedTab ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Archive
                    <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $counts['archived'] }}</span>
                </a>
            </div>

            {{-- ── Filters ── --}}
            <form method="GET" action="{{ route('admin.student-records.index') }}"
                  class="{{ $card }} flex flex-wrap gap-3 items-end px-5 py-4">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or student number…"
                           class="{{ $inputCls }} placeholder:text-slate-400">
                </div>

                {{-- Program (multi-select) --}}
                <div class="relative w-56" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Program</label>
                    <button type="button" @click="open = !open" class="{{ $inputCls }} text-left">
                        {{ count($selectedPrograms) ? count($selectedPrograms) . ' selected' : 'All programs' }}
                    </button>
                    <div x-show="open" x-transition style="display:none"
                         class="absolute z-30 mt-1 w-80 max-h-64 overflow-auto rounded-xl border border-slate-100 bg-white shadow-lg p-2 space-y-0.5">
                        @forelse($programs as $p)
                            <label class="flex items-start gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="program[]" value="{{ $p }}"
                                       @checked(in_array($p, $selectedPrograms, true))
                                       class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-300">
                                <span>{{ $p }}</span>
                            </label>
                        @empty
                            <p class="px-2 py-1.5 text-sm text-slate-400">No programs found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="w-36">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Year level</label>
                    <select name="year_level" class="{{ $inputCls }}">
                        <option value="">All</option>
                        @foreach($yearLevels as $y)
                            <option value="{{ $y }}" @selected((string) request('year_level') === (string) $y)>Year {{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-36">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Block</label>
                    <select name="block" class="{{ $inputCls }}">
                        <option value="">All</option>
                        @foreach($blocks as $b)
                            <option value="{{ $b }}" @selected((string) request('block') === (string) $b)>Block {{ $b }}</option>
                        @endforeach
                        @if($hasNoBlock)
                            <option value="none" @selected(request('block') === 'none')>No block</option>
                        @endif
                    </select>
                </div>

                <div class="w-48">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Internship</label>
                    <select name="internship" class="{{ $inputCls }}">
                        <option value="">Any</option>
                        <option value="completed"  @selected(request('internship') === 'completed')>Completed</option>
                        <option value="incomplete" @selected(request('internship') === 'incomplete')>Not completed</option>
                    </select>
                </div>

                @if($archivedTab)
                    <div class="w-40">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Archived as</label>
                        <select name="archived_as" class="{{ $inputCls }}">
                            <option value="">Passed &amp; Dropped</option>
                            <option value="passed"  @selected(request('archived_as') === 'passed')>Passed</option>
                            <option value="dropped" @selected(request('archived_as') === 'dropped')>Dropped</option>
                        </select>
                    </div>

                    <div class="w-64">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Archive batch</label>
                        <select name="batch" class="{{ $inputCls }}">
                            <option value="">All batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->archive_batch }}" @selected(request('batch') === $batch->archive_batch)>
                                    {{ \Carbon\Carbon::parse($batch->archived_at)->format('M d, Y g:i A') }}
                                    · {{ ucfirst($batch->archive_reason) }} · {{ $batch->total }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="w-28">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Per page</label>
                    <select name="per_page" class="{{ $inputCls }}">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150">
                        Filter
                    </button>
                    @if($hasFilter)
                        <a href="{{ route('admin.student-records.index', ['tab' => $tab]) }}"
                           class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- ── Selection bar ── --}}
            <div x-show="selected.length > 0 || allMatching" x-transition style="display:none"
                 class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-3">
                <p class="text-sm text-blue-800">
                    <span class="font-bold" x-text="count"></span>
                    <span x-text="count === 1 ? 'student selected' : 'students selected'"></span>

                    @if($hasFilter && $students->total() > $students->count())
                        <template x-if="!allMatching && selected.length === pageIds.length">
                            <button type="button" @click="allMatching = true" class="ml-2 font-semibold underline hover:text-blue-900">
                                Select all {{ $students->total() }} that match these filters
                            </button>
                        </template>
                        <template x-if="allMatching">
                            <button type="button" @click="allMatching = false; selected = []" class="ml-2 font-semibold underline hover:text-blue-900">
                                Clear selection
                            </button>
                        </template>
                    @endif
                </p>

                <div class="flex gap-2">
                    @if(!$archivedTab)
                        <button type="button" @click="open('archive')"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150">
                            Archive selected
                        </button>
                    @else
                        <button type="button" @click="open('restore')"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors duration-150">
                            Restore selected
                        </button>
                        <button type="button" @click="open('delete')"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors duration-150">
                            Delete permanently
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── Table ── --}}
            <div class="{{ $card }} overflow-hidden">
                @if($students->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 gap-2 text-center">
                        <p class="text-sm font-semibold text-slate-600">
                            {{ $archivedTab ? 'No archived students found' : 'No students found' }}
                        </p>
                        <p class="text-xs text-slate-400">
                            @if($hasFilter)
                                Try adjusting your filters.
                            @elseif($archivedTab)
                                Students you archive will appear here.
                            @else
                                Students will appear here once they register or are imported.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-5 py-3.5 w-10">
                                        <input type="checkbox" @change="toggleAll($event)"
                                               :checked="pageIds.length > 0 && selected.length === pageIds.length"
                                               aria-label="Select all on this page"
                                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-300">
                                    </th>
                                    @php
                                        $columns = $archivedTab
                                            ? ['Student', 'Program', 'Year / Block', 'Archived as', 'Remarks', 'Archived', 'Actions']
                                            : ['Student', 'Program', 'Year / Block', 'Internship', 'Actions'];
                                    @endphp
                                    @foreach($columns as $col)
                                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($students as $student)
                                    <tr class="hover:bg-slate-50/60 transition-colors duration-100">
                                        <td class="px-5 py-4">
                                            <input type="checkbox" :value="{{ $student->id }}" x-model="selected"
                                                   @change="allMatching = false"
                                                   aria-label="Select {{ $student->full_name }}"
                                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-300">
                                        </td>

                                        <td class="px-5 py-4">
                                            <p class="text-sm font-semibold text-slate-800">{{ $student->full_name }}</p>
                                            <p class="text-xs text-slate-400">{{ $student->student_number }}</p>
                                        </td>

                                        <td class="px-5 py-4 text-sm text-slate-600 max-w-[16rem]">{{ $student->program }}</td>

                                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">
                                            Year {{ $student->year_level }}
                                            · {{ $student->block ? 'Block ' . $student->block : 'No block' }}
                                        </td>

                                        @if(!$archivedTab)
                                            <td class="px-5 py-4">
                                                @if($student->internship_completed_at)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed
                                                    </span>
                                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $student->internship_completed_at->format('M d, Y') }}</p>
                                                @else
                                                    <span class="text-xs text-slate-500">{{ $student->internship_status_label }}</span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4">
                                                <button type="button"
                                                        @click="open('archive', { id: {{ $student->id }}, name: @js($student->full_name) })"
                                                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600
                                                               hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all duration-150">
                                                    Archive
                                                </button>
                                            </td>
                                        @else
                                            <td class="px-5 py-4">
                                                @if($student->archive_reason === 'passed')
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Passed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Dropped
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4 text-sm text-slate-600 max-w-[14rem]">
                                                <p class="truncate" title="{{ $student->archive_remarks }}">{{ $student->archive_remarks ?: '—' }}</p>
                                            </td>

                                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                                {{ $student->archived_at->format('M d, Y g:i A') }}
                                                <p class="text-slate-400">by {{ $student->archivedBy?->name ?? 'Unknown' }}</p>
                                            </td>

                                            <td class="px-5 py-4">
                                                <div class="flex gap-2">
                                                    <button type="button"
                                                            @click="open('restore', { id: {{ $student->id }}, name: @js($student->full_name) })"
                                                            class="rounded-lg border border-green-200 bg-white px-3 py-1.5 text-xs font-semibold text-green-700
                                                                   hover:bg-green-50 transition-colors duration-150">
                                                        Restore
                                                    </button>
                                                    <button type="button"
                                                            @click="open('delete', { id: {{ $student->id }}, name: @js($student->full_name) })"
                                                            class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600
                                                                   hover:bg-red-50 transition-colors duration-150">
                                                        Delete permanently
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($students->hasPages())
                        <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-4">
                            {{ $students->links() }}
                        </div>
                    @endif
                @endif
            </div>

            @if($students->isNotEmpty())
                <p class="text-xs text-slate-400 text-right">
                    Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
                </p>
            @endif
        </div>

        {{-- ═════════════ MODALS ═════════════ --}}

        {{-- Archive --}}
        <div x-show="modal === 'archive'" x-transition.opacity style="display:none" @click.self="close()"
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
            <form method="POST" action="{{ route('admin.student-records.archive') }}"
                  class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-lg p-6 space-y-5">
                @csrf
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="mode" :value="mode">
                <template x-for="id in (mode === 'selected' ? ids : [])" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                @include('admin.student-records._filter-inputs')

                <div>
                    <h3 class="text-base font-bold text-slate-800"
                        x-text="single ? 'Archive student' : 'Archive ' + count + ' students'"></h3>
                    <p class="text-sm text-slate-500 mt-0.5" x-show="single" x-text="single ? single.name : ''"></p>
                </div>

                <p class="text-sm text-slate-600">
                    Archived students are hidden from coordinators and supervisors and can no longer sign in.
                    Their records stay saved, and you can restore them any time from the Archive tab.
                </p>

                <fieldset class="space-y-2">
                    <legend class="text-xs font-semibold text-slate-500 mb-1.5">Archive as</legend>
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:border-blue-300"
                           :class="reason === 'passed' ? 'border-blue-400 bg-blue-50/50' : ''">
                        <input type="radio" name="reason" value="passed" x-model="reason" class="mt-1 text-blue-600 focus:ring-blue-300">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Passed</span>
                            <span class="block text-xs text-slate-500">Completed the Internship.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:border-orange-300"
                           :class="reason === 'dropped' ? 'border-orange-400 bg-orange-50/50' : ''">
                        <input type="radio" name="reason" value="dropped" x-model="reason" class="mt-1 text-orange-500 focus:ring-orange-300">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Dropped</span>
                            <span class="block text-xs text-slate-500">No longer needed or left the program. Allowed at any stage.</span>
                        </span>
                    </label>
                </fieldset>

                <div x-show="reason === 'passed'" class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <span x-text="eligible"></span> of <span x-text="count"></span>
                    <span x-text="count === 1 ? 'student has' : 'students have'"></span> completed the Internship.
                    <span x-show="eligible < count">The rest will be skipped.</span>
                    <span x-show="eligible === 0" class="font-semibold">Choose Dropped instead.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                        Remarks <span x-show="reason === 'dropped'" class="text-red-500">(required)</span>
                        <span x-show="reason !== 'dropped'" class="text-slate-400">(optional)</span>
                    </label>
                    <textarea name="remarks" rows="3" maxlength="500" :required="reason === 'dropped'"
                              placeholder="Short note, e.g. transferred to another school"
                              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none
                                     focus:ring-2 focus:ring-blue-300 focus:border-blue-400"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="close()"
                            class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                        Cancel
                    </button>
                    <button type="submit" :disabled="reason === 'passed' && eligible === 0"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150
                                   disabled:opacity-40 disabled:cursor-not-allowed">
                        Archive
                    </button>
                </div>
            </form>
        </div>

        {{-- Restore --}}
        <div x-show="modal === 'restore'" x-transition.opacity style="display:none" @click.self="close()"
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
            <form method="POST" action="{{ route('admin.student-records.restore') }}"
                  class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
                @csrf
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="mode" :value="mode">
                <template x-for="id in (mode === 'selected' ? ids : [])" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                @include('admin.student-records._filter-inputs')

                <div>
                    <h3 class="text-base font-bold text-slate-800"
                        x-text="single ? 'Restore student' : 'Restore ' + count + ' students'"></h3>
                    <p class="text-sm text-slate-500 mt-0.5" x-show="single" x-text="single ? single.name : ''"></p>
                </div>

                <p class="text-sm text-slate-600">
                    The records return to the Active list exactly as they were, and the accounts can sign in again.
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="close()"
                            class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors duration-150">
                        Restore
                    </button>
                </div>
            </form>
        </div>

        {{-- Permanent delete --}}
        <div x-show="modal === 'delete'" x-transition.opacity style="display:none" @click.self="close()"
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
            <form method="POST" action="{{ route('admin.student-records.force-delete') }}"
                  class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="mode" :value="mode">
                <template x-for="id in (mode === 'selected' ? ids : [])" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                @include('admin.student-records._filter-inputs')

                <div>
                    <h3 class="text-base font-bold text-slate-800"
                        x-text="single ? 'Delete student permanently' : 'Delete ' + count + ' students permanently'"></h3>
                    <p class="text-sm text-slate-500 mt-0.5" x-show="single" x-text="single ? single.name : ''"></p>
                </div>

                <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 font-medium">
                    This cannot be undone. The archived records are removed for good.
                </div>

                <p class="text-sm text-slate-600">
                    This deletes the student accounts, deployments, requirements and uploaded files, daily logs,
                    evaluations, observation schedules and lesson plans.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                        Type <span class="font-mono font-bold text-red-600">DELETE</span> to confirm
                    </label>
                    <input type="text" name="confirm" x-model="confirmText" autocomplete="off"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-mono text-slate-800 outline-none
                                  focus:ring-2 focus:ring-red-300 focus:border-red-400">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="close()"
                            class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                        Cancel
                    </button>
                    <button type="submit" :disabled="confirmText !== 'DELETE'"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors duration-150
                                   disabled:opacity-40 disabled:cursor-not-allowed">
                        Delete permanently
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>