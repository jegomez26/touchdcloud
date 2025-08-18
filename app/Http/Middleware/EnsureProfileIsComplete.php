<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Check the 'profile_completed' flag on the user
            //    AND make sure they are NOT already on the profile completion page
            if (!$user->profile_completed &&
            $request->route()->getName() !== 'profile.complete.show' &&
            $request->route()->getName() !== 'home') // <--- Make sure 'home' is here
            {
                return redirect()->route('profile.complete.show')->with('error', 'Please complete your profile to access all features.');
            }
        }

        // If the user is not logged in, or their profile is complete, or they are already on the profile page,
        // let the request proceed.
        return $next($request);
    }
}