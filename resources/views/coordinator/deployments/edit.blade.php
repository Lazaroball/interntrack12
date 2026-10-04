{{-- resources/views/coordinator/deployments/edit.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Deployment – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50/50 text-slate-900 antialiased selection:bg-blue-500 selection:text-white">

@php
    $isCancelled = $deployment->status === 'cancelled';
    $isLocked    = $deployment->completed_at || $isCancelled;
@endphp

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
                <a href="{{ route('coordinator.requirements.review.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">Requirements</a>
                <a href="{{ route('coordinator.partner-schools.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">Partner Schools</a>
                <a href="{{ route('coordinator.deployments.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold text-blue-600 bg-blue-50/80">Deployments</a>
            </nav>
        </div>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back to Deployments
    </a>

    {{-- Header --}}
    <div class="border-b border-slate-100 pb-5">
        <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-100">Edit Deployment</span>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight mt-1">
            {{ $deployment->student->first_name }} {{ $deployment->student->last_name }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Student No. {{ $deployment->student->student_number }}
            &middot; {{ $deployment->student->program ?: 'No program' }}
            &middot;
            @if ($deployment->student->block)
                Block {{ $deployment->student->block }}
            @else
                <span class="font-semibold text-amber-500">No block</span>
            @endif
        </p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <p class="font-bold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Locked banner --}}
    @if ($isLocked)
        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <p class="font-bold">This deployment is {{ $isCancelled ? 'cancelled' : 'completed' }} and can no longer be edited.</p>
        </div>
    @endif

    {{-- Current state summary --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm shadow-blue-50/50 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center border
            @if ($isCancelled) bg-rose-50 text-rose-600 border-rose-100
            @elseif ($deployment->completed_at) bg-slate-100 text-slate-500 border-slate-200
            @elseif ($deployment->supervisor_id) bg-blue-50 text-blue-600 border-blue-100
            @else bg-amber-50 text-amber-600 border-amber-100 @endif">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800">
                @if ($isCancelled) Cancelled
                @elseif ($deployment->completed_at) Completed
                @elseif ($deployment->supervisor_id) Current deployment
                @else Waiting for approval @endif
            </p>
            <p class="text-xs text-slate-400">
                @if ($isCancelled)
                    Cancelled on {{ $deployment->updated_at->format('M d, Y') }}
                @elseif ($deployment->completed_at)
                    Completed on {{ $deployment->completed_at->format('M d, Y') }}
                @elseif ($deployment->deployment_date)
                    Deployed on {{ $deployment->deployment_date->format('M d, Y') }}
                @else
                    Requested on {{ $deployment->created_at->format('M d, Y') }}
                @endif
            </p>
        </div>
    </div>

    {{-- Edit form --}}
    <form method="POST" action="{{ route('coordinator.deployments.update', $deployment) }}" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm shadow-blue-50/50 space-y-5">
        @csrf
        @method('PUT')

        <fieldset @if ($isLocked) disabled @endif class="space-y-5">

            <div>
                <label for="partner_school_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Partner School</label>
                <select
                    id="partner_school_id"
                    name="partner_school_id"
                    required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    @foreach ($partnerSchools as $school)
                        <option value="{{ $school->id }}" @selected(old('partner_school_id', $deployment->partner_school_id) == $school->id)>
                            {{ $school->school_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="supervisor_id" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor</label>
                <select
                    id="supervisor_id"
                    name="supervisor_id"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >
                    <option value="">Not yet assigned</option>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @selected(old('supervisor_id', $deployment->supervisor_id) == $supervisor->id)>
                            {{ $supervisor->last_name }}, {{ $supervisor->first_name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Assigning a supervisor here approves the deployment, same as approving it from the dashboard.</p>
            </div>

            {{-- Deployment type is read-only. The hidden input submits the value so validation still passes. --}}
            <div>
                <label for="program_display" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Deployment Type</label>
                <select
                    id="program_display"
                    disabled
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-500 bg-slate-50 outline-none cursor-not-allowed"
                >
                    <option value="Field Study" @selected($deployment->program == 'Field Study')>Field Study</option>
                    <option value="Internship" @selected($deployment->program == 'Internship')>Internship</option>
                </select>
                <input type="hidden" name="program" value="{{ $deployment->program }}">
                <p class="mt-1.5 text-xs text-slate-400">The deployment type cannot be changed. To change it, cancel this deployment and create a new one.</p>
            </div>

            <div>
                <label for="remarks" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Remarks</label>
                <textarea
                    id="remarks"
                    name="remarks"
                    rows="4"
                    maxlength="1000"
                    placeholder="Optional notes about this deployment"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                >{{ old('remarks', $deployment->remarks) }}</textarea>
            </div>
        </fieldset>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
            <a href="{{ route('coordinator.deployments.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                Back
            </a>
            @unless ($isLocked)
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/15 transition">
                    Save Changes
                </button>
            @endunless
        </div>
    </form>

    {{-- Cancel deployment --}}
    @unless ($isLocked)
        <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-slate-800">Cancel this deployment</p>
                <p class="text-xs text-slate-400">The student will be able to be deployed again. The record is kept in the Cancelled list.</p>
            </div>
            <form
                method="POST"
                action="{{ route('coordinator.deployments.cancel', $deployment) }}"
                onsubmit="return confirm('Cancel this deployment? The student will need to be deployed again.');"
            >
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition">
                    Cancel Deployment
                </button>
            </form>
        </div>
    @endunless

    {{-- Danger zone --}}
    <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-slate-800">Delete this deployment</p>
            <p class="text-xs text-slate-400">This permanently removes the record. This action cannot be undone.</p>
        </div>
        <form
            method="POST"
            action="{{ route('coordinator.deployments.destroy', $deployment) }}"
            onsubmit="return confirm('Are you sure you want to delete this deployment? This action cannot be undone.');"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition">
                Delete
            </button>
        </form>
    </div>

</main>
</body>
</html>