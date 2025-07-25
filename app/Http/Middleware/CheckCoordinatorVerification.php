<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckCoordinatorVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is a coordinator
        if (Auth::check() && Auth::user()->role === 'coordinator') {
            $user = Auth::user();

            // Eager load the supportCoordinator relationship to avoid N+1 queries
            // and ensure the relationship exists before accessing it.
            $user->loadMissing('supportCoordinator');

            // Check if the support coordinator record exists and its status is 'verified'
            if (!$user->supportCoordinator || $user->supportCoordinator->status !== 'verified') {
                // Redirect to the pending approval page if not verified
                if ($request->route()->getName() !== 'coordinator.account.pending') {
                    return redirect()->route('coordinator.account.pending')
                                     ->with('status', 'Your account is pending admin approval.');
                }
            }
        }

        return $next($request);
    }
}