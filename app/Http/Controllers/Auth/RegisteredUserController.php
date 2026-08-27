<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'      => ['required', 'string', 'max:255'],
            'middle_name'     => ['nullable', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'student_number'  => ['required', 'string', 'max:255'],
            'course'          => ['required', 'string'],
            'year_level'      => ['required'],
            'program_type'    => ['required', 'string'],
            'mobile_number'   => ['required', 'string', 'max:20'],
            'email'           => ['required', 'email', 'unique:users,email'],
            'password'        => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create User Account
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name'                  => trim($request->first_name . ' ' . $request->last_name),

            'first_name'            => $request->first_name,
            'middle_name'           => $request->middle_name,
            'last_name'             => $request->last_name,

            'email'                 => $request->email,
            'mobile_number'         => $request->mobile_number,

            'password'              => Hash::make($request->password),

            'role'                  => 'student',
            'status'                => true,
            'must_change_password'  => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Student Profile
        |--------------------------------------------------------------------------
        */

       Student::create([
    'user_id' => $user->id,

    'student_number' => $request->student_number,
    'reference_number' => 'INT-' . time(),

    'first_name' => $request->first_name,
    'middle_name' => $request->middle_name,
    'last_name' => $request->last_name,

    'program' => $request->course,
    'year_level' => $request->year_level,
    'program_type' => $request->program_type,

    

    'field_study_hours' => 0,
    'internship_hours' => 0,

    'is_eligible' => false,
    'status' => 'active',
]);

        event(new Registered($user));

        return redirect()->route('register.success');
    }
} 