<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
 * Handle login
 */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();

    // Save last login date & time
    $user->update([
        'last_login_at' => now(),
    ]);

    return match ($user->role) {

        'admin' => redirect()->route('admin.dashboard'),

        'coordinator' => redirect()->route('coordinator.dashboard'),

        'supervisor' => redirect()->route('supervisor.dashboard'),

        'student' => redirect()->route('student.dashboard'),

        default => redirect()->route('login'),
    };
}
    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}