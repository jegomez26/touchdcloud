<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is a participant
        if (Auth::check() && Auth::user()->role === 'participant') {
            $user = Auth::user();

            // Assuming your User model has a 'profile_completed' boolean column
            if (!$user->profile_completed) {
                // Prevent infinite redirect loop if already on the completion page
                if ($request->route()->getName() !== 'profile.complete.show' &&
                    $request->route()->getName() !== 'profile.complete') { // Allow POST to complete
                    return redirect()->route('profile.complete.show')
                                     ->with('error', 'Please complete your profile.');
                }
            }
        }

        return $next($request);
    }
}