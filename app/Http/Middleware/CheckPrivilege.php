<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $privilege): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => 'Please log in to access this page.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();

        // Check if user has admin role
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Check if user has the specific privilege
        if (!$user->hasPrivilege($privilege)) {
            $privilegeName = ucfirst(str_replace('_', ' ', $privilege));
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => "You don't have permission to access this feature. Required privilege: {$privilegeName}",
                    'privilege' => $privilege,
                    'privilegeName' => $privilegeName
                ], 403);
            }
            
            // For regular page requests, redirect to dashboard with error message
            return redirect()->route('superadmin.dashboard')->with('error', "You don't have permission to access this feature. Required privilege: {$privilegeName}");
        }

        return $next($request);
    }
}
