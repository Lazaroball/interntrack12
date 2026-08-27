<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student School Selection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">

    <div class="max-w-2xl mx-auto px-4">
        
        <!-- Active Mode Info -->
        <div class="mb-6 p-4 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs">
            <p class="font-bold">Student Deployment Portal</p>
            <p>Active Student ID: <span class="font-bold">{{ $student->id }}</span> ({{ $student->first_name ?? $student->name ?? 'Student' }})</p>
        </div>

        <!-- System Alerts -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-xl border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Select Partner School</h2>
            <p class="text-xs text-gray-500 mb-6">Choose one partner school for Internship or Field Study.</p>

            @if($existingDeployment && $existingDeployment->status !== 'pending')
                <!-- Read-Only Badge when Approved/Locked -->
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-green-600 tracking-wider">Locked Deployment</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-green-200 text-green-800 uppercase">
                            {{ $existingDeployment->status }}
                        </span>
                    </div>
                    <p class="text-base font-bold text-gray-800 mt-2">
                        {{ $existingDeployment->partnerSchool->school_name ?? 'Assigned School' }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">Program: {{ $existingDeployment->program }}</p>
                </div>
            @else
                <!-- Form to Select / Edit Choice -->
                <form action="{{ route('student.deployment.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Program Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Program</label>
                        <select name="program" required class="w-full border-gray-300 rounded-lg text-sm p-2.5 border focus:ring-indigo-500">
                            <option value="">-- Select Program --</option>
                            <option value="Field Study" {{ old('program', $existingDeployment->program ?? '') == 'Field Study' ? 'selected' : '' }}>Field Study</option>
                            <option value="Internship" {{ old('program', $existingDeployment->program ?? '') == 'Internship' ? 'selected' : '' }}>Internship</option>
                        </select>
                    </div>

                    <!-- School Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Partner School</label>
                        <select name="partner_school_id" required class="w-full border-gray-300 rounded-lg text-sm p-2.5 border focus:ring-indigo-500">
                            <option value="">-- Choose School --</option>
                            @foreach($partnerSchools as $school)
                                <option value="{{ $school->id }}" {{ old('partner_school_id', $existingDeployment->partner_school_id ?? '') == $school->id ? 'selected' : '' }}>
                                    {{ $school->school_name }} 
                                    @if(isset($school->remaining_slots))
                                        ({{ $school->remaining_slots }} slots remaining)
                                    @elseif(isset($school->max_slots))
                                        ({{ $school->max_slots }} total slots)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition">
                        {{ $existingDeployment ? 'Update School Choice' : 'Submit School Choice' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

</body>
</html>