{{-- resources/views/supervisor/evaluations/other-evaluator-results/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Other Evaluator Results – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

            <nav class="hidden md:flex items-center gap-1" aria-label="Supervisor navigation">
                <a href="{{ route('supervisor.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Dashboard
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                    Students
                </a>
                @if (Route::has('supervisor.observations.index'))
                    <a href="{{ route('supervisor.observations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors duration-150">
                        Observation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Observation</span>
                @endif
                @if (Route::has('supervisor.evaluations.index'))
                    <a href="{{ route('supervisor.evaluations.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600">
                        Evaluation
                    </a>
                @else
                    <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Evaluation</span>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supervisor</span>
                    <span class="text-xs font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl border border-slate-200
                                   bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->first_name ?? 'Supervisor' }}</span>
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

<main
    class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
    x-data="otherEvaluatorResults({
        evaluationId: {{ $evaluation->id }},
        initialResults: @json($otherEvaluatorResults),
        storeUrl: '{{ route('supervisor.evaluations.other-evaluator-results.store', $evaluation) }}',
        updateUrlTemplate: '{{ route('supervisor.other-evaluator-results.update', ['otherEvaluatorResult' => '__ID__']) }}',
        destroyUrlTemplate: '{{ route('supervisor.other-evaluator-results.destroy', ['otherEvaluatorResult' => '__ID__']) }}',
    })"
>

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
            <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Other Evaluator Results</h1>
            <p class="text-sm text-slate-400 mt-0.5">
                Additional evaluator grades recorded for this evaluation
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('supervisor.evaluations.show', $evaluation->id) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-slate-200
                      hover:bg-slate-100 text-slate-600 text-sm font-semibold transition-all duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Back to Evaluation
            </a>

            <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                           bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                           shadow-sm shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Evaluator Result
            </button>
        </div>
    </div>

    {{-- ── Inline Alert (JS-driven) ── --}}
    <div x-show="alert.message" x-transition
         :class="alert.type === 'error'
            ? 'bg-rose-50 border-rose-200 text-rose-600'
            : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
         class="px-4 py-3 rounded-xl border text-sm" x-cloak>
        <span x-text="alert.message"></span>
    </div>

    {{-- ── Table Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-widest text-slate-400">Evaluator</th>
                        <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-widest text-slate-400">Final Grade</th>
                        <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-widest text-slate-400">Grade Image</th>
                        <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-widest text-slate-400">Remarks</th>
                        <th class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <template x-if="results.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">
                                No other evaluator results yet. Click "Add Evaluator Result" to record one.
                            </td>
                        </tr>
                    </template>

                    <template x-for="result in results" :key="result.id">
                        <tr class="hover:bg-slate-50 transition-colors duration-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-700" x-text="result.evaluator_name"></div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                                    <span x-text="Number(result.final_grade).toFixed(2)"></span>
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <template x-if="result.grade_image_url">
                                    <a :href="result.grade_image_url" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <img :src="result.grade_image_url" alt="Grade image"
                                             class="w-10 h-10 rounded-lg object-cover ring-1 ring-slate-200">
                                        <span>View</span>
                                    </a>
                                </template>
                                <template x-if="!result.grade_image_url">
                                    <span class="text-slate-300 text-sm">—</span>
                                </template>
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-sm text-slate-600 truncate" :title="result.remarks || ''" x-text="result.remarks || '—'"></p>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <button type="button" @click="openEditModal(result)"
                                        class="text-slate-500 hover:text-slate-800 font-semibold">
                                    Edit
                                </button>
                                <button type="button" @click="confirmDelete(result)"
                                        class="text-rose-500 hover:text-rose-700 font-semibold">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ CREATE / EDIT MODAL ══ --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-slate-900/50" @click="closeModal()"></div>

        <div class="relative bg-white w-full max-w-lg rounded-2xl border border-slate-100 shadow-xl shadow-slate-900/10 p-6 space-y-5"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-slate-800" x-text="editingId ? 'Edit Evaluator Result' : 'Add Evaluator Result'"></h2>
                <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            {{-- Form validation errors --}}
            <div x-show="Object.keys(formErrors).length > 0" x-cloak
                 class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    <template x-for="(messages, field) in formErrors" :key="field">
                        <template x-for="message in messages" :key="message">
                            <li x-text="message"></li>
                        </template>
                    </template>
                </ul>
            </div>

            <form @submit.prevent="submitForm()" class="space-y-4">

                {{-- Evaluator Name --}}
                <div>
                    <label for="evaluator_name" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Evaluator Name
                    </label>
                    <input type="text" id="evaluator_name" x-model="form.evaluator_name" required maxlength="255"
                           placeholder="e.g. Cooperating Teacher, Principal"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                  placeholder-slate-400 outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                </div>

                {{-- Final Grade --}}
                <div>
                    <label for="final_grade" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Final Grade
                    </label>
                    <input type="number" id="final_grade" x-model="form.final_grade" required
                           step="0.01" min="0" max="999.99"
                           placeholder="e.g. 95.00"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                  placeholder-slate-400 outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400" />
                </div>

                {{-- Grade Image --}}
                <div>
                    <label for="grade_image" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Grade Image <span class="normal-case font-medium text-slate-400">(optional)</span>
                    </label>
                    <input type="file" id="grade_image" accept="image/png,image/jpeg,image/webp"
                           @change="onImageSelected($event)"
                           class="w-full text-sm text-slate-600
                                  file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600
                                  hover:file:bg-blue-100 file:transition-colors" />

                    {{-- Existing / preview image --}}
                    <div class="mt-2 flex items-center gap-3" x-show="imagePreviewUrl || (editingId && form.existing_image_url && !removeExistingImage)">
                        <img :src="imagePreviewUrl || form.existing_image_url" alt="Preview"
                             class="w-14 h-14 rounded-lg object-cover ring-1 ring-slate-200">
                        <button type="button" x-show="editingId && form.existing_image_url && !imagePreviewUrl"
                                @click="removeExistingImage = true"
                                class="text-xs font-semibold text-rose-500 hover:text-rose-700">
                            Remove image
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-slate-400" x-show="removeExistingImage">
                        Image will be removed when you save.
                    </p>
                </div>

                {{-- Remarks --}}
                <div>
                    <label for="remarks" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Remarks <span class="normal-case font-medium text-slate-400">(optional)</span>
                    </label>
                    <textarea id="remarks" x-model="form.remarks" rows="3" maxlength="2000"
                              placeholder="Any additional notes…"
                              class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-sm text-slate-700
                                     placeholder-slate-400 outline-none transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="submitting"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all duration-150
                                   shadow-sm shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting" x-text="editingId ? 'Save Changes' : 'Add Result'"></span>
                        <span x-show="submitting" x-cloak>Saving…</span>
                    </button>

                    <button type="button" @click="closeModal()"
                            class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100
                                   text-slate-600 text-sm font-semibold transition-all duration-150">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ DELETE CONFIRM MODAL ══ --}}
    <div x-show="deleteTarget !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-slate-900/50" @click="deleteTarget = null"></div>

        <div class="relative bg-white w-full max-w-sm rounded-2xl border border-slate-100 shadow-xl shadow-slate-900/10 p-6 space-y-4"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <h2 class="text-lg font-extrabold text-slate-800">Delete Evaluator Result</h2>
            <p class="text-sm text-slate-500">
                Are you sure you want to delete the result for
                <span class="font-semibold text-slate-700" x-text="deleteTarget?.evaluator_name"></span>?
                This action cannot be undone.
            </p>

            <div class="flex items-center gap-3 pt-1">
                <button type="button" @click="performDelete()" :disabled="deleting"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition-all duration-150
                               shadow-sm shadow-rose-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!deleting">Delete</span>
                    <span x-show="deleting" x-cloak>Deleting…</span>
                </button>
                <button type="button" @click="deleteTarget = null"
                        class="px-5 py-2.5 rounded-xl border-2 border-slate-200 hover:bg-slate-100
                               text-slate-600 text-sm font-semibold transition-all duration-150">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</main>

<script>
    function otherEvaluatorResults(config) {
        return {
            evaluationId: config.evaluationId,
            storeUrl: config.storeUrl,
            updateUrlTemplate: config.updateUrlTemplate,
            destroyUrlTemplate: config.destroyUrlTemplate,

            results: config.initialResults || [],

            modalOpen: false,
            editingId: null,
            submitting: false,
            formErrors: {},

            deleteTarget: null,
            deleting: false,

            alert: { type: 'success', message: '' },

            form: {
                evaluator_name: '',
                final_grade: '',
                remarks: '',
                existing_image_url: null,
            },
            imageFile: null,
            imagePreviewUrl: null,
            removeExistingImage: false,

            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            },

            showAlert(type, message) {
                this.alert = { type, message };
                setTimeout(() => { this.alert.message = ''; }, 4000);
            },

            resetForm() {
                this.form = {
                    evaluator_name: '',
                    final_grade: '',
                    remarks: '',
                    existing_image_url: null,
                };
                this.imageFile = null;
                this.imagePreviewUrl = null;
                this.removeExistingImage = false;
                this.formErrors = {};
            },

            openCreateModal() {
                this.editingId = null;
                this.resetForm();
                this.modalOpen = true;
            },

            openEditModal(result) {
                this.editingId = result.id;
                this.form = {
                    evaluator_name: result.evaluator_name,
                    final_grade: result.final_grade,
                    remarks: result.remarks || '',
                    existing_image_url: result.grade_image_url || null,
                };
                this.imageFile = null;
                this.imagePreviewUrl = null;
                this.removeExistingImage = false;
                this.formErrors = {};
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
                this.editingId = null;
                this.resetForm();
            },

            onImageSelected(event) {
                const file = event.target.files[0];
                this.imageFile = file || null;
                this.removeExistingImage = false;

                if (file) {
                    this.imagePreviewUrl = URL.createObjectURL(file);
                } else {
                    this.imagePreviewUrl = null;
                }
            },

            buildFormData() {
                const data = new FormData();
                data.append('evaluator_name', this.form.evaluator_name);
                data.append('final_grade', this.form.final_grade);
                data.append('remarks', this.form.remarks || '');

                if (this.imageFile) {
                    data.append('grade_image', this.imageFile);
                }

                if (this.editingId && this.removeExistingImage && !this.imageFile) {
                    data.append('remove_image', '1');
                }

                return data;
            },

            async submitForm() {
                this.submitting = true;
                this.formErrors = {};

                try {
                    const formData = this.buildFormData();
                    let url = this.storeUrl;

                    if (this.editingId) {
                        url = this.updateUrlTemplate.replace('__ID__', this.editingId);
                        formData.append('_method', 'PUT');
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken(),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const payload = await response.json();

                    if (response.status === 422) {
                        this.formErrors = payload.errors || {};
                        return;
                    }

                    if (!response.ok || !payload.success) {
                        this.showAlert('error', payload.message || 'Something went wrong. Please try again.');
                        return;
                    }

                    if (this.editingId) {
                        const index = this.results.findIndex(r => r.id === this.editingId);
                        if (index !== -1) {
                            this.results.splice(index, 1, payload.data);
                        }
                        this.showAlert('success', 'Evaluator result updated successfully.');
                    } else {
                        this.results.unshift(payload.data);
                        this.showAlert('success', 'Evaluator result added successfully.');
                    }

                    this.closeModal();
                } catch (error) {
                    this.showAlert('error', 'Network error. Please check your connection and try again.');
                } finally {
                    this.submitting = false;
                }
            },

            confirmDelete(result) {
                this.deleteTarget = result;
            },

            async performDelete() {
                if (!this.deleteTarget) return;

                this.deleting = true;

                try {
                    const url = this.destroyUrlTemplate.replace('__ID__', this.deleteTarget.id);

                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken(),
                            'Accept': 'application/json',
                        },
                    });

                    const payload = await response.json();

                    if (!response.ok || !payload.success) {
                        this.showAlert('error', payload.message || 'Failed to delete evaluator result.');
                        return;
                    }

                    this.results = this.results.filter(r => r.id !== this.deleteTarget.id);
                    this.showAlert('success', 'Evaluator result deleted successfully.');
                } catch (error) {
                    this.showAlert('error', 'Network error. Please check your connection and try again.');
                } finally {
                    this.deleting = false;
                    this.deleteTarget = null;
                }
            },
        };
    }
</script>

</body>
</html>