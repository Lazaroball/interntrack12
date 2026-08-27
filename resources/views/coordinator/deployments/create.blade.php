<x-app-layout>
    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Back Link -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('coordinator.deployments.index') }}" 
                   class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-2 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Deployments
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Manual Student Deployment</h1>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                <p class="font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <form action="{{ route('coordinator.deployments.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Student Selection -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Select Student <span class="text-red-500">*</span>
                    </label>

                    @if(isset($selectedStudent))
                        <!-- Pre-selected student from Student Dashboard -->
                        <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-lg flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold text-indigo-950">
                                    {{ $selectedStudent->last_name }}, {{ $selectedStudent->first_name }}
                                </p>
                                <p class="text-xs text-indigo-700">
                                    Student ID: {{ $selectedStudent->student_number ?? 'N/A' }} | Program: {{ $selectedStudent->program ?? 'N/A' }}
                                </p>
                            </div>
                            <a href="{{ route('coordinator.deployments.create') }}" class="text-xs text-indigo-600 hover:underline">Change</a>
                        </div>
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                    @else
                        <!-- Dropdown choice -->
                        <select name="student_id" id="student_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Choose an undeployed student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->last_name }}, {{ $student->first_name }} ({{ $student->student_number ?? 'No ID' }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Partner School -->
                    <div>
                        <label for="partner_school_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Partner School / Company <span class="text-red-500">*</span>
                        </label>
                        <select name="partner_school_id" id="partner_school_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Select Partner School --</option>
                            @foreach($partnerSchools as $school)
                                <option value="{{ $school->id }}" {{ old('partner_school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->school_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Supervisor -->
                    <div>
                        <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Assigned Supervisor <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <select name="supervisor_id" id="supervisor_id"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Select Supervisor --</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->last_name }}, {{ $supervisor->first_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program Type -->
                    <div>
                        <label for="program" class="block text-sm font-medium text-gray-700 mb-1">
                            Deployment Program <span class="text-red-500">*</span>
                        </label>
                        <select name="program" id="program" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Select Program --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog }}" {{ old('program') == $prog ? 'selected' : '' }}>
                                    {{ $prog }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- School Year -->
                    <div>
                        <label for="school_year" class="block text-sm font-medium text-gray-700 mb-1">
                            School Year <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="school_year" id="school_year" 
                               value="{{ old('school_year', '2025-2026') }}" required placeholder="e.g. 2025-2026"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Semester -->
                    <div class="sm:col-span-2">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="semester" id="semester" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="1st Semester" {{ old('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ old('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ old('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks / Notes <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <textarea name="remarks" id="remarks" rows="3" placeholder="Add optional deployment notes..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('remarks') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                    <a href="{{ route('coordinator.deployments.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        Deploy Student Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>