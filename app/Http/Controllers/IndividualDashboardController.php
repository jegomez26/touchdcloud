<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // This is good to keep, even if not directly used for simple rules

class IndividualDashboardController extends Controller
{
    /**
     * Handle the individual dashboard view after email verification.
     * Checks if the user's profile is completed based on the User model.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Authentication error. Please log in again.');
        }

        if ($user->role === 'participant' && !$user->profile_completed) {
            return redirect()->route('profile.complete.show'); // Ensure this route points to showCompleteProfileForm in THIS controller
        }

        return view('indiv.main-content', ['user' => $user]);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function showCompleteProfileForm()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required to complete profile.');
        }

        $participant = Participant::firstOrNew(['user_id' => $user->id]);

        if ($user->profile_completed) {
            return redirect()->route('indiv.dashboard')->with('info', 'Your profile is already completed.');
        } else {
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
        // Add dd() here to confirm you're hitting the correct method!
        dd($request->all());

        $user = Auth::user();

        if (!$user) {
            Log::error("Auth::user() returned null during completeProfile submission. Attempting logout.");
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
        }

        // --- 1. Define Validation Rules ---
        $rules = [
            'participant_first_name' => ['required', 'string', 'max:255'],
            'participant_middle_name' => ['nullable', 'string', 'max:255'],
            'participant_last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string', 'max:255'],
            'specific_disability' => ['nullable', 'string'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'is_looking_hm' => ['boolean'],
            'has_accommodation' => ['boolean'],

            // *** IMPORTANT: Add validation rules for these fields! ***
            // These keys must match the `name` attributes in your Blade form.
            'relative_name' => ['nullable', 'string', 'max:255'],
            'relative_phone' => ['nullable', 'string', 'max:20'], // Ensure rule matches your data type
            'relative_email' => ['nullable', 'email', 'max:255'],
            'relationship_to_participant' => ['nullable', 'string', 'max:255'], // This is the form field name
        ];

        try {
            // Use $validatedData after validation
            $validatedData = $request->validate($rules);

            // --- 2. Update the Logged-in User's Status ---
            $user->profile_completed = true;
            $user->save();

            // --- 3. Create or Update the Participant Record ---
            if ($user->role === 'participant') {
                $participant = Participant::firstOrNew(['user_id' => $user->id]);

                if (!$participant->exists) {
                    $participant->user_id = $user->id;
                    $participant->added_by_user_id = $user->id;
                }

                // Participant's name always comes from the form's 'participant_' fields
                $participant->first_name = $validatedData['participant_first_name'];
                $participant->middle_name = $validatedData['participant_middle_name'] ?? null; // Use validatedData
                $participant->last_name = $validatedData['participant_last_name'];

                // Handle 'relative_name', 'relative_phone', 'relative_email', 'relative_relationship'
                if ($user->is_representative) {
                    // If the user is a representative, their own name and contact details become the relative_name.
                    // This assumes representative_first_name and representative_last_name exist and are accurate on the User model.
                    $participant->relative_name = $user->first_name . ' ' . $user->last_name; // Use user's own first/last name
                    $participant->relative_phone = $user->phone_number ?? null; // Assuming user has a 'phone_number' field
                    $participant->relative_email = $user->email;
                    $participant->relative_relationship = $user->relationship_to_participant ?? null; // From User model
                } else {
                    // If a direct participant, the relative_name, phone, email, and relationship come from the form input.
                    $participant->relative_name = $validatedData['relative_name'] ?? null;
                    $participant->relative_phone = $validatedData['relative_phone'] ?? null;
                    $participant->relative_email = $validatedData['relative_email'] ?? null;
                    $participant->relative_relationship = $validatedData['relationship_to_participant'] ?? null; // Map from form name
                }

                // Fill other common participant details using fill method
                // Note: 'disability_type' should be JSON encoded before saving if it's an array
                $participant->fill([
                    'birthday' => $validatedData['birthday'],
                    'disability_type' => json_encode($validatedData['disability_type'] ?? []), // Ensure this handles null/empty correctly
                    'specific_disability' => $validatedData['specific_disability'] ?? null,
                    'accommodation_type' => $validatedData['accommodation_type'] ?? null,
                    'street_address' => $validatedData['street_address'] ?? null,
                    'suburb' => $validatedData['suburb'] ?? null,
                    'state' => $validatedData['state'] ?? null,
                    'post_code' => $validatedData['post_code'] ?? null,
                    // Relative fields are handled above separately due to conditional logic
                ]);

                // Handle boolean checkboxes explicitly (using validatedData)
                $participant->is_looking_hm = $validatedData['is_looking_hm'] ?? false;
                $participant->has_accommodation = $validatedData['has_accommodation'] ?? false;

                $participant->save();
            }

            return redirect()->route('indiv.dashboard')->with('success', 'Profile completed successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Profile completion error: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $user->id]);
            return back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }
}