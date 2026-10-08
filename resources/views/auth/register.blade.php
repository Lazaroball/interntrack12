{{-- resources/views/auth/register.blade.php --}}

<x-auth-layout title="Student Registration" subtitle="Create your InternTrack account.">

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="flex flex-col gap-5">

            {{-- First Name / Last Name --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- First Name --}}
                <div class="flex flex-col gap-1.5">
                    <label for="first_name" class="text-sm font-semibold text-slate-700 tracking-wide">
                        First Name <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="first_name"
                        name="first_name"
                        type="text"
                        value="{{ old('first_name') }}"
                        autocomplete="given-name"
                        autofocus
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                               placeholder-slate-300 outline-none transition-all duration-200
                               focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                               @error('first_name') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                    />
                    @error('first_name')
                        <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="flex flex-col gap-1.5">
                    <label for="last_name" class="text-sm font-semibold text-slate-700 tracking-wide">
                        Last Name <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="last_name"
                        name="last_name"
                        type="text"
                        value="{{ old('last_name') }}"
                        autocomplete="family-name"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                               placeholder-slate-300 outline-none transition-all duration-200
                               focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                               @error('last_name') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                    />
                    @error('last_name')
                        <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Middle Name --}}
            <div class="flex flex-col gap-1.5">
                <label for="middle_name" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Middle Name
                    <span class="text-xs font-normal text-slate-400">(Optional)</span>
                </label>
                <input
                    id="middle_name"
                    name="middle_name"
                    type="text"
                    value="{{ old('middle_name') }}"
                    autocomplete="additional-name"
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white
                           text-sm text-slate-800 placeholder-slate-300 outline-none
                           transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400"
                />
            </div>

            {{-- Student Number --}}
            <div class="flex flex-col gap-1.5">
                <label for="student_number" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Student Number <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="student_number"
                    name="student_number"
                    type="text"
                    value="{{ old('student_number') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('student_number') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
                @error('student_number')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

           {{-- Program --}}
<div class="flex flex-col gap-1.5">
    <label for="course" class="text-sm font-semibold text-slate-700 tracking-wide">
        Program <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
    </label>

    <select
        id="course"
        name="course"
        required
        class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
               outline-none transition-all duration-200
               focus:ring-2 focus:ring-blue-300 focus:border-blue-400
               @error('course') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
    >
        <option value="">Select Program</option>

        @foreach ([
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Computer Science (BSCS)',
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Secondary Education (BSEd) – English',
            'Bachelor of Secondary Education (BSEd) – Filipino',
            'Bachelor of Secondary Education (BSEd) – Mathematics',
            'Bachelor of Secondary Education (BSEd) – Science',
            'Bachelor of Secondary Education (BSEd) – Social Studies',
            'Bachelor of Physical Education (BPEd)',
            'Bachelor of Early Childhood Education (BECEd)',
            'Bachelor of Special Needs Education (BSNEd)',
            'Bachelor of Technology and Livelihood Education (BTLEd)',
        ] as $program)

            <option value="{{ $program }}" {{ old('course') === $program ? 'selected' : '' }}>
                {{ $program }}
            </option>

        @endforeach

    </select>

    @error('course')
        <span class="text-sm text-red-600">{{ $message }}</span>
    @enderror
</div>

            {{-- Year Level --}}
            <div class="flex flex-col gap-1.5">
                <label for="year_level" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Year Level <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <select
                    id="year_level"
                    name="year_level"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('year_level') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                >
                    <option value="">Select Year Level</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
                @error('year_level')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Block --}}
            <div class="flex flex-col gap-1.5">
                <label for="block" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Block <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <select
                    id="block"
                    name="block"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('block') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                >
                    <option value="">Select Block</option>
                    @foreach (range(1, 10) as $number)
                        <option value="{{ $number }}" {{ (string) old('block') === (string) $number ? 'selected' : '' }}>
                            {{ $number }}
                        </option>
                    @endforeach
                </select>
                @error('block')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Program Type --}}
<div class="flex flex-col gap-1.5">
    <label for="program_type" class="text-sm font-semibold text-slate-700 tracking-wide">
        Program Type <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
    </label>

    <select
        id="program_type"
        name="program_type"
        required
        class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
               outline-none transition-all duration-200
               focus:ring-2 focus:ring-blue-300 focus:border-blue-400
               @error('program_type') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
    >
        <option value="">Select Program Type</option>

        @foreach ([
            'Field Study',
            'Internship',
        ] as $type)

            <option value="{{ $type }}" {{ old('program_type') === $type ? 'selected' : '' }}>
                {{ $type }}
            </option>

        @endforeach

    </select>

    @error('program_type')
        <span class="text-sm text-red-600">{{ $message }}</span>
    @enderror
</div>

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Email <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="you@ucu.edu.ph"
                    autocomplete="email"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('email') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
                @error('email')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Mobile Number --}}
            <div class="flex flex-col gap-1.5">
                <label for="mobile_number" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Mobile Number <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="mobile_number"
                    name="mobile_number"
                    type="tel"
                    value="{{ old('mobile_number') }}"
                    autocomplete="tel"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('mobile_number') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
                @error('mobile_number')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 text-sm text-slate-800
                           placeholder-slate-300 outline-none transition-all duration-200
                           focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                           @error('password') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror"
                />
                @error('password')
                    <p role="alert" class="flex items-center gap-1.5 text-xs font-medium text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" class="w-3.5 h-3.5 flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            {{--
                Laravel's 'confirmed' validation rule checks for a matching
                'password_confirmation' field automatically — no @error needed here.
            --}}
            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700 tracking-wide">
                    Confirm Password <span class="text-blue-500 font-bold" aria-hidden="true">*</span>
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    placeholder="Re-enter your password"
                    autocomplete="new-password"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 bg-white
                           text-sm text-slate-800 placeholder-slate-300 outline-none
                           transition-all duration-200 focus:ring-2 focus:ring-blue-300 focus:border-blue-400"
                />
            </div>

            {{-- Submit --}}
            <button
                type="submit"   
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                       active:bg-blue-800 text-white text-sm font-semibold
                       transition-colors duration-200 focus:outline-none
                       focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Create Account
            </button>

            {{-- Login link --}}
            <p class="text-sm text-center text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}"
                   class="text-blue-500 font-semibold hover:text-blue-600
                          hover:underline transition-colors duration-150">
                    Login
                </a>
            </p>

        </div>
    </form>

</x-auth-layout>