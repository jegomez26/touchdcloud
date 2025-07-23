<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Log; // Required for Log::error()
use Illuminate\Validation\Rule; // Required for validation rules (though not strictly used in current setup)


class IndividualDashboardController extends Controller
{
    /**
     * Handle the individual dashboard view after email verification.
     * Checks if the user's profile is completed based on the User model.
     */
    public function index(Request $request)
    {
        // The 'auth' and 'verified' middleware should already ensure these conditions.
        // These checks are redundant if middleware is configured correctly, but safe to keep for robustness.
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Should not happen if Auth::check() is true, but defensive programming
        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Authentication error. Please log in again.');
        }

        // IMPORTANT: REDIRECT TO PROFILE COMPLETION IF NOT COMPLETED
        // This ensures the showCompleteProfileForm method is always used
        // and that the 'profile.complete.show' route's middleware apply.
        // Only redirect if role is 'participant' AND profile is NOT completed
        if ($user->role === 'participant' && !$user->profile_completed) {
            return redirect()->route('profile.complete.show');
        }

        // If the profile is completed (and verified, due to middleware), show the normal dashboard content
        // For other roles (e.g., admin), they would also proceed here directly.
        return view('indiv.main-content', ['user' => $user]);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function showCompleteProfileForm()
    {
        $user = Auth::user();

        if (!$user) { // Should not happen if 'auth' middleware is applied
            return redirect()->route('login')->with('error', 'Authentication required to complete profile.');
        }

        // Find or create a Participant record for the current user.
        // This ensures $participant is always an object, even if no record exists yet.
        $participant = Participant::firstOrNew(['user_id' => $user->id]);

        // If the profile is already completed, redirect to the dashboard.
        // This prevents users from re-accessing the form directly after completion.
        if ($user->profile_completed) {
            return redirect()->route('indiv.dashboard')->with('info', 'Your profile is already completed.');
        } else {
            // If profile is not completed, show the form
            return view('profile.complete-participant-profile', [
                'user' => $user,
                'participant' => $participant,
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Handle the submission of the profile completion form.
     * Updates the User model and creates/updates a Participant record.
     */
    public function completeProfile(Request $request)
    {
        $user = Auth::user();

        // Defensive check: If user is somehow null, force logout.
        if (!$user) {
            Log::error("Auth::user() returned null during completeProfile submission. Attempting logout.");
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
        }

        // --- 1. Define Validation Rules ---
        // Rules are now exclusively for the Participant's details, as per the updated form.
        // The logged-in User's name is assumed to be set elsewhere or not mutable here.
        $rules = [
            'participant_first_name' => ['required', 'string', 'max:255'],
            'participant_middle_name' => ['nullable', 'string', 'max:255'],
            'participant_last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'disability_type' => ['nullable', 'array'], // Changed for multiple selection, can be empty
            'disability_type.*' => ['string', 'max:255'],
            'specific_disability' => ['nullable', 'string'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'is_looking_hm' => ['boolean'],
            'has_accommodation' => ['boolean'],
            'relative_name' => ['nullable', 'string', 'max:255'],
        ];

        $request->validate($rules);

        // --- 2. Update the Logged-in User's Status ---
        // Only mark profile as completed for the User model.
        // User's name fields are no longer updated here, as the form doesn't provide them.
        $user->profile_completed = true;
        $user->save();

        // --- 3. Create or Update the Participant Record ---
        // This section will always run if the user is a 'participant' role
        if ($user->role === 'participant') {
            $participant = Participant::firstOrNew(['user_id' => $user->id]);

            if (!$participant->exists) {
                $participant->user_id = $user->id;
                $participant->added_by_user_id = $user->id; // Assuming the logged-in user is adding/completing their own or a represented profile
            }

            // Participant's name always comes from the form's 'participant_' fields
            $participant->first_name = $request->input('participant_first_name');
            $participant->middle_name = $request->input('participant_middle_name');
            $participant->last_name = $request->input('participant_last_name');

            // Handle 'relative_name' based on 'is_representative' flag
            if ($user->is_representative) {
                // If the user is a representative, their own name (from User model, stored as representative_name) becomes the relative_name.
                $participant->relative_name = $user->representative_first_name . ' ' . $user->representative_last_name;
            } else {
                // If a direct participant, the relative_name comes from the form input.
                $participant->relative_name = $request->input('relative_name');
            }

            // Fill other common participant details
            $participant->fill($request->only([
                'birthday',
                'disability_type',
                'specific_disability',
                'accommodation_type',
                'street_address',
                'suburb',
                'state',
                'post_code',
                // 'relative_name' is handled separately above
            ]));

            // Handle boolean checkboxes explicitly
            $participant->is_looking_hm = $request->boolean('is_looking_hm');
            $participant->has_accommodation = $request->boolean('has_accommodation');

            $participant->save();
        }

        return redirect()->route('indiv.dashboard')->with('success', 'Profile completed successfully!');
    }
}