<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signs out a student whose record was archived, even if their session
 * was already open when the admin archived them.
 */
class EnsureStudentNotArchived
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user
            && $user->role === 'student'
            && Student::onlyArchived()->where('user_id', $user->id)->exists()
        ) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This account has been archived. Please contact the administrator.']);
        }

        return $next($request);
    }
}