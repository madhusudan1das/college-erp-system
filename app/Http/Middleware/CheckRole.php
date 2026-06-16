<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Load the role relation if not loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $userRole = $user->role->name;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect based on role
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($userRole === 'faculty') {
            return redirect()->route('faculty.dashboard');
        } elseif ($userRole === 'student') {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('login');
    }
}
