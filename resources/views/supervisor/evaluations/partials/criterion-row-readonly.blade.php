{{-- resources/views/supervisor/evaluations/partials/criterion-row-readonly.blade.php
     Expects: $num (int), $label (string), $max (int), $value (mixed) --}}

<div class="flex items-center gap-4 px-6 py-3.5">
    <div class="flex-1 min-w-0">
        <p class="text-sm text-slate-700 leading-snug">
            <span class="font-semibold text-slate-400 mr-1.5">{{ $num }}.</span>{{ $label }}
        </p>
    </div>
    <div class="flex-shrink-0">
        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800">
            {{ $value ?? '0' }} / {{ $max }}
        </span>
    </div>
</div>