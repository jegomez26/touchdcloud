<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required role for the user to have
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if the user is authenticated
        if (!Auth::check()) {
            // If not authenticated, redirect to login page
            return redirect()->route('login');
        }

        // 2. Get the authenticated user
        $user = Auth::user();

        // 3. Check if the user has the required role
        // Assuming your User model has a 'role' column (e.g., 'admin', 'participant', 'coordinator')
        if ($user->role !== $role) {
            // If the user does not have the required role,
            // you can choose to:
            // a) Abort with a 403 Forbidden error:
            //    abort(403, 'Unauthorized action.');

            // b) Redirect to a general dashboard or home page with an error message:
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
        }

        // 4. If the user has the required role, proceed with the request
        return $next($request);
    }
}