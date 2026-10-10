{{-- resources/views/supervisor/field-study-requests/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Field Study Completion Requests – InternTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<x-supervisor-nav />

<main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div>
        <p class="text-[11px] font-bold tracking-[0.2em] uppercase text-blue-500 mb-0.5">Supervisor</p>
        <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">Field Study Completion Requests</h1>
        <p class="text-sm text-slate-400 mt-0.5">Review and act on completion requests submitted by your assigned students.</p>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm shadow-blue-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Student No.</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Requested Hours</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Request Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($requests as $fieldStudyRequest)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $fieldStudyRequest->student->full_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                {{ $fieldStudyRequest->student->student_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $fieldStudyRequest->requested_hours }} hrs
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                {{ $fieldStudyRequest->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusStyles = [
                                        'pending'  => 'bg-slate-100 text-slate-600 ring-slate-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-600 ring-red-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 capitalize {{ $statusStyles[$fieldStudyRequest->status] ?? $statusStyles['pending'] }}">
                                    {{ $fieldStudyRequest->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($fieldStudyRequest->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('supervisor.field-study-requests.approve', $fieldStudyRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors duration-150">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('supervisor.field-study-requests.reject', $fieldStudyRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition-colors duration-150">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No action available</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400 italic">
                                No pending Field Study completion requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>