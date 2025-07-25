<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Participant;
use App\Models\SupportCoordinator;
use App\Models\Provider; // Ensure this is imported if used
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Ensure this is imported if you use Rule::in

class ProfileCompletionController extends Controller
{
    // ... (showCompleteProfileForm method) ...

    public function complete(Request $request)
    {
        $user = Auth::user();

        // Initialize variables for different profiles
        $participant = null;
        $coordinator = null;
        $provider = null;

        // Fetch existing profile data if available
        if ($user->role === 'participant') {
            $participant = $user->participant()->first();
        } elseif ($user->role === 'coordinator') {
            $coordinator = $user->supportCoordinator()->first();
        } elseif ($user->role === 'provider') {
            $provider = $user->provider()->first();
        }

        // Define validation rules based on user role
        $rules = [
            // Common rules, if any
        ];

        switch ($user->role) {
            case 'participant':
                $rules = [
                    'participant_first_name' => ['required', 'string', 'max:255'],
                    'participant_middle_name' => ['nullable', 'string', 'max:255'],
                    'participant_last_name' => ['required', 'string', 'max:255'],
                    'birthday' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')], // Must be 18+
                    'disability_type' => ['required', 'array'], // Expects an array
                    'disability_type.*' => ['string', 'max:255'], // Each item in the array
                    'specific_disability' => ['nullable', 'string', 'max:2000'],
                    'accommodation_type' => ['required', 'string', 'max:255'],
                    'street_address' => ['nullable', 'string', 'max:255'],
                    'suburb' => ['nullable', 'string', 'max:255'],
                    'state' => ['nullable', 'string', 'max:255'],
                    'post_code' => ['nullable', 'string', 'max:20'],
                    'is_looking_hm' => ['boolean'],
                    'has_accommodation' => ['boolean'],
                    'support_coordinator_id' => ['nullable', 'exists:support_coordinators,id'],

                    // *** VALIDATION RULES for Relative Fields - Ensure these match your form's 'name' attributes ***
                    'relative_name' => ['nullable', 'string', 'max:255'],
                    'relative_phone' => ['nullable', 'string', 'max:20'], // Assuming phone number can be string
                    'relative_email' => ['nullable', 'email', 'max:255'],
                    // The form field is 'relationship_to_participant'
                    'relationship_to_participant' => ['nullable', 'string', 'max:255'],
                ];
                break;

            case 'coordinator':
                $rules = [
                    'company_name' => ['required', 'string', 'max:255'],
                    'abn' => ['required', 'string', 'max:11'], // ABN is 11 digits
                    'website' => ['nullable', 'url', 'max:255'],
                    'phone_number' => ['nullable', 'string', 'max:20'],
                    'email' => ['required', 'email', 'max:255'],
                    // 'first_name', 'last_name' are for the User model, not SupportCoordinator
                ];
                break;

            case 'provider':
                $rules = [
                    'company_name' => ['required', 'string', 'max:255'],
                    'abn' => ['required', 'string', 'max:11'],
                    'website' => ['nullable', 'url', 'max:255'],
                    'phone_number' => ['nullable', 'string', 'max:20'],
                    'email' => ['required', 'email', 'max:255'],
                    'service_type' => ['required', 'string', Rule::in(['SDA', 'SIL', 'Both'])],
                    'has_vacancies' => ['boolean'],
                    'description' => ['nullable', 'string', 'max:2000'],
                ];
                break;
        }

        try {
            $validatedData = $request->validate($rules);

            // Update User model (first_name, last_name only for coordinator/provider)
            if ($user->role === 'coordinator' || $user->role === 'provider') {
                $user->update([
                    'first_name' => $validatedData['first_name'], // Assuming these come from your coordinator/provider forms
                    'last_name' => $validatedData['last_name'],
                ]);
            }

            // Update specific profile based on role
            switch ($user->role) {
                case 'participant':
                    // If a representative is completing the profile for a participant,
                    // the participant's `representative_user_id` should be the representative's user ID.
                    // The `added_by_user_id` should also be the representative's user ID.
                    // If the participant is self-registering, `representative_user_id` is null,
                    // and `added_by_user_id` is the participant's user ID.

                    $participantData = [
                        'first_name' => $validatedData['participant_first_name'],
                        'middle_name' => $validatedData['participant_middle_name'] ?? null,
                        'last_name' => $validatedData['participant_last_name'],
                        'birthday' => $validatedData['birthday'],
                        'disability_type' => json_encode($validatedData['disability_type']), // Ensure this is cast as 'array' in Participant model
                        'specific_disability' => $validatedData['specific_disability'] ?? null,
                        'accommodation_type' => $validatedData['accommodation_type'],
                        'street_address' => $validatedData['street_address'] ?? null,
                        'suburb' => $validatedData['suburb'] ?? null,
                        'state' => $validatedData['state'] ?? null,
                        'post_code' => $validatedData['post_code'] ?? null,
                        'is_looking_hm' => $validatedData['is_looking_hm'] ?? false,
                        'has_accommodation' => $validatedData['has_accommodation'] ?? false,
                        'support_coordinator_id' => $validatedData['support_coordinator_id'] ?? null,
                        'added_by_user_id' => $user->id, // The user who is logged in and adding/completing the profile

                        // Determine representative_user_id:
                        // If the current user is a representative, they are the representative for this participant.
                        // If the current user is NOT a representative, then the participant *is* the user, so no separate representative.
                        'representative_user_id' => $user->is_representative ? $user->id : null,

                        // *** These are the crucial lines! ***
                        // Ensure keys here match your Participant model's $fillable attributes
                        // and their values are taken from $validatedData using the form's 'name' attributes.
                        'relative_name' => $validatedData['relative_name'] ?? null,
                        'relative_phone' => $validatedData['relative_phone'] ?? null,
                        'relative_email' => $validatedData['relative_email'] ?? null,
                        // Map the form's 'relationship_to_participant' to the DB column 'relative_relationship'
                        'relative_relationship' => $validatedData['relationship_to_participant'] ?? null,
                    ];

                    
                    Participant::updateOrCreate(
                        ['user_id' => $user->id], // Use the current user's ID to find/create the participant record
                        $participantData
                    );
                    break;

                case 'coordinator':
                    // ... (Coordinator update logic) ...
                    break;

                case 'provider':
                    // ... (Provider update logic) ...
                    break;
            }

            // Mark profile as completed for the current user
            $user->profile_completed = true;
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Profile completed successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Profile completion error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }
}