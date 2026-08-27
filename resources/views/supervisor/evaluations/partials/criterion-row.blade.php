{{-- resources/views/supervisor/evaluations/partials/criterion-row.blade.php
     Expects: $num (int), $label (string), $max (int), $value (mixed, optional) --}}

<div class="flex items-center gap-4 px-6 py-3.5">
    <div class="flex-1 min-w-0">
        <p class="text-sm text-slate-700 leading-snug">
            <span class="font-semibold text-slate-400 mr-1.5">{{ $num }}.</span>{{ $label }}
        </p>
    </div>
    <div class="flex-shrink-0 flex items-center gap-1.5">
        <input
            type="number"
            name="criterion_{{ $num }}_score"
            id="criterion_{{ $num }}_score"
            x-model.number="scores[{{ $num }}]"
            value="{{ old('criterion_' . $num . '_score', $value ?? '') }}"
            min="0"
            max="{{ $max }}"
            step="0.5"
            required
            class="w-16 px-2 py-1.5 rounded-lg border border-slate-200 text-sm text-center font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
        >
        <span class="text-xs text-slate-400 font-medium">/ {{ $max }}</span>
    </div>
    @error('criterion_' . $num . '_score')
        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
    @enderror
</div>