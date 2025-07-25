<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCoordinatorIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated
        if (! Auth::check()) {
            return redirect()->route('login'); // Or wherever unauthenticated users go
        }

        $user = Auth::user();

        // 2. Check if the user is a 'coordinator'
        if ($user->role !== 'coordinator') {
            // Redirect based on their role, or to a general unauthorized page
            return redirect()->route('dashboard'); // Assuming 'dashboard' handles other roles
        }

        // 3. Check if the coordinator's email is verified (Laravel's built-in check)
        // This is usually handled by `verified` middleware, but good to ensure
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice'); // Send them back to verify email
        }

        // 4. Check the SupportCoordinator status
        // Eager load the supportCoordinator relationship to avoid N+1 query
        $user->loadMissing('supportCoordinator');

        if (! $user->supportCoordinator || $user->supportCoordinator->status !== 'verified') {
            // If not found or status is not 'verified', redirect to a pending approval page
            return redirect()->route('coordinator.account.pending-approval');
        }

        // If all checks pass, allow access
        return $next($request);
    }
}