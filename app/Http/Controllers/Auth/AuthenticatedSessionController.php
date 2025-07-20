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
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // --- ADD THIS CONDITIONAL REDIRECTION LOGIC ---
        $user = Auth::user(); // Get the authenticated user

        if ($user && !$user->profile_completed) {
            // If the user's profile is not completed, redirect to the profile completion route
            return redirect()->route('profile.complete'); // Or 'participant.profile.complete' if you kept that route name
        }

        // Default redirection if profile is completed or for other roles
        // You might want more sophisticated role-based redirection here
        if ($user->role === 'participant') {
             return redirect()->intended(route('indiv.dashboard', absolute: false));
        }
        // Add conditions for other roles, e.g., if ($user->role === 'coordinator') { ... }
        // Fallback for other roles or if no specific dashboard route:
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
