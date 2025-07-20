<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Log; // Required for Log::error()


class IndividualDashboardController extends Controller
{
    /**
     * Handle the individual dashboard view after email verification.
     * Checks if the user's profile is completed based on the User model.
     */
    public function index(Request $request)
    {
        // dd('IndividualDashboardController@index is being hit!');
        // Ensure the user is authenticated
        if (!Auth::check()) {
            // dd('Auth::check() is false. User not authenticated.');
            return redirect()->route('login');
        }

        $user = Auth::user(); // <--- This line retrieves the authenticated user

        // If Auth::user() returned null, force re-login as a safeguard
        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Authentication error. Please log in again.');
        }

        // Check the 'profile_completed' flag directly on the User model
        if (!$user->profile_completed) {
            // Profile is not completed, display the profile completion form within the dashboard layout
            // We pass $user because the form needs access to Auth::user() for pre-filling
            return view('profile.complete-participant-profile', ['user' => $user]);
        }

        // If the profile is completed, show the normal dashboard content
        // This view should also extend indiv-db and yield to 'main-content'
        return view('indiv.main-content', ['user' => $user]);
    }

    public function showCompleteProfileForm()
    {
        $user = Auth::user();

        // dd($user);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required to complete profile.');
        }

        // --- NEW LOGIC HERE ---
        // Attempt to find the participant record for the current user.
        // If it doesn't exist, create a new, empty Participant instance.
        $participant = Participant::where('user_id', $user->id)->first();

        if (is_null($participant)) {
            // If no participant record is found, create a new (empty) Participant model instance.
            // This instance will be used to pre-fill the form fields with empty values,
            // preventing "Undefined property" errors in the Blade view.
            $participant = new Participant();
        }
        // --- END NEW LOGIC ---

        // Pass both $user and $participant to the view
        return view('profile.complete-participant-profile', [
            'user' => $user,
            'participant' => $participant // <--- Pass participant as a separate variable
        ]);
    }

    /**
     * Handle the submission of the profile completion form.
     * Updates the User model and potentially creates/updates a Participant record.
     */
    public function completeProfile(Request $request)
    {
        // dd(Auth::user());
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user(); // This is returning null

        // Let's try to get the user ID directly from the session or Auth facade
        $userId = Auth::id(); // Get the ID that Auth thinks is logged in

        if ($user === null) {
            Log::error("Auth::user() returned null. User ID from Auth::id(): " . ($userId ?? 'N/A'));
            // Attempt to re-fetch the user by ID
            if ($userId) {
                $user = User::find($userId);
                if ($user === null) {
                    Log::error("User ID {$userId} not found in database when re-fetching.");
                    // This is a major issue: user ID in session but no matching user in DB.
                    // Force logout and redirect to login.
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Your session expired or your account no longer exists. Please log in again.');
                } else {
                    Log::info("Successfully re-fetched user {$userId} from database.");
                    // User found, continue with the logic
                }
            } else {
                // This case shouldn't happen if Auth::check() was true
                Log::error("Auth::check() true, but Auth::id() is null. This is highly unusual.");
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Authentication error. Please log in again.');
            }
        }

        // dd($user); // Now, dd($user) again to see what it contains after re-fetching attempt

        $rules = [
            'first_name' => ['required', 'string', 'max:255'], // Logged-in user's first name (for User model)
            'last_name' => ['required', 'string', 'max:255'],  // Logged-in user's last name (for User model)
            'middle_name' => ['nullable', 'string', 'max:255'], // Logged-in user's middle name (will go to Participant model)
        ];

        if ($user->role === 'individual') {
            if ($user->is_representative) { // If the logged-in user is a representative
                $rules = array_merge($rules, [
                    'represented_first_name' => ['required', 'string', 'max:255'],
                    'represented_middle_name' => ['nullable', 'string', 'max:255'],
                    'represented_last_name' => ['required', 'string', 'max:255'],
                ]);
            }

            // Common participant fields (apply to both direct participant and represented participant)
            $rules = array_merge($rules, [
                'birthday' => ['required', 'date'],
                'disability_type' => ['nullable', 'string', 'max:255'],
                'specific_disability' => ['nullable', 'string'],
                'accommodation_type' => ['nullable', 'string', 'max:255'],
                'street_address' => ['nullable', 'string', 'max:255'],
                'suburb' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'post_code' => ['nullable', 'string', 'max:10'],
                'is_looking_hm' => ['boolean'],
                'relative_name' => ['nullable', 'string', 'max:255'],
                'has_accommodation' => ['boolean'],
            ]);
        }

        $request->validate($rules);

        // Update logged-in user's first_name and last_name in the 'users' table
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->profile_completed = true;
        $user->save();

        if ($user->role === 'participant') {
            $participant = Participant::firstOrNew(['user_id' => $user->id]);

            if (!$participant->exists) {
                $participant->user_id = $user->id;
                $participant->added_by_user_id = $user->id; // Assuming user adds themselves or a representative adds for someone
            }

            // Handle participant name details
            if ($user->is_representative) {
                // Save represented participant's full name to the participant model
                $participant->first_name = $request->represented_first_name;
                $participant->middle_name = $request->represented_middle_name;
                $participant->last_name = $request->represented_last_name;
            } else {
                // For direct participant, first and last name are in the User model.
                // Only save middle name to the participant model.
                $participant->first_name = $user->first_name; // Copy from user to participant model
                $participant->last_name = $user->last_name;   // Copy from user to participant model
                $participant->middle_name = $request->middle_name; // Get middle name from the form
            }

            $participant->fill($request->only([
                // Common participant fields to fill, regardless of representative status
                'birthday',
                'disability_type', 'specific_disability', 'accommodation_type',
                'street_address', 'suburb', 'state', 'post_code',
                'relative_name'
            ]));

            $participant->is_looking_hm = $request->boolean('is_looking_hm');
            $participant->has_accommodation = $request->boolean('has_accommodation');

            $participant->save();
        }

        return redirect()->route('indiv.dashboard')->with('success', 'Profile completed successfully!');
    }
}