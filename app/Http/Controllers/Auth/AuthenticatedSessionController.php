<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider; // Assuming this exists or define a fallback

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
        $request->authenticate(); // Authenticates the user

        $request->session()->regenerate(); // Regenerates the session ID

        $user = Auth::user(); // Get the authenticated user

        // 1. --- Check Email Verification FIRST ---
        if ($user && !$user->hasVerifiedEmail()) {
            // Redirect to the email verification notice if not verified
            return redirect()->route('verification.notice');
        }

        // 2. --- Then, check Profile Completion (ONLY if email is verified) ---
        if ($user && $user->role === 'participant' && !$user->isProfileComplete()) {
            return redirect()->route('profile.complete.show');
        }

        // 3. --- Default Redirection Based on Role ---
        if ($user->role === 'participant') {
            return redirect()->intended(route('indiv.dashboard', absolute: false));
        }

        if ($user->role === 'admin') {
            return redirect()->intended(route('superadmin.dashboard', absolute: false));
        }

        // Fallback for other roles or if no specific dashboard route
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