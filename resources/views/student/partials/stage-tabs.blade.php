{{-- resources/views/student/partials/stage-tabs.blade.php --}}
{{-- Usage: @include('student.partials.stage-tabs', ['section' => 'requirements|overview|hours', 'activeStage' => 'Field Study|Internship']) --}}
@php
    $tabStudent     = $student ?? auth()->user()->student;
    $internshipOpen = (bool) optional($tabStudent)->is_internship_unlocked;

    $tabRoutes = [
        'requirements' => [
            'Field Study' => route('student.field-study.requirements'),
            'Internship'  => route('student.internship.requirements'),
        ],
        'overview' => [
            'Field Study' => route('student.field-study'),
            'Internship'  => route('student.internship'),
        ],
        'hours' => [
            'Field Study' => route('student.teaching-hours', ['stage' => 'Field Study']),
            'Internship'  => route('student.teaching-hours', ['stage' => 'Internship']),
        ],
    ][$section];
@endphp

<div class="inline-flex items-center gap-1 p-1 rounded-xl bg-white border border-slate-200 shadow-sm" role="tablist">
    @foreach (['Field Study', 'Internship'] as $tabStage)
        @php
            $isActive = $activeStage === $tabStage;
            $isLocked = $tabStage === 'Internship' && ! $internshipOpen;
        @endphp

        @if ($isLocked)
            <span title="Unlocks after the coordinator clears your Field Study"
                  class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold text-slate-300 cursor-not-allowed select-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                {{ $tabStage }}
            </span>
        @else
            <a href="{{ $tabRoutes[$tabStage] }}"
               role="tab"
               @if ($isActive) aria-selected="true" @endif
               class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors duration-150
                      {{ $isActive ? 'bg-blue-600 text-white shadow shadow-blue-200' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                {{ $tabStage }}
            </a>
        @endif
    @endforeach
</div>