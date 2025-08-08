<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Participant;
use App\Models\User;

class CompleteParticipantProfileController extends Controller
{
    /**
     * Display the participant profile completion form.
     */
    public function create()
    {
        $user = Auth::user();
        $participant = null;

        // Check if the user is a representative.
        if ($user->is_representative) {
            // A representative manages a participant record.
            // We'll find the first one associated with them, or create a new one.
            $participant = $user->participantsRepresented()->first() ?? new Participant([
                'representative_user_id' => $user->id,
                'added_by_user_id' => $user->id,
            ]);

            // For a new participant record created by a representative, pre-fill some details.
            if (!$participant->exists) {
                // Participant's name (from users.first_name/last_name for representatives)
                $participant->first_name = $user->first_name;
                $participant->last_name = $user->last_name;

                // Relative's name (which is the representative's name)
                $participant->relative_name = $user->representative_first_name . ' ' . $user->representative_last_name;
                $participant->relative_relationship = $user->relationship_to_participant ?? null;
                $participant->relative_phone = $user->phone_number ?? null;
                $participant->relative_email = $user->email;
            }
        }
        // If the user is a direct participant (not a representative).
        elseif ($user->role === 'participant') {
            // Find the participant record linked to this user, or create a new one.
            $participant = $user->participant ?? new Participant([
                'user_id' => $user->id,
                'added_by_user_id' => $user->id,
            ]);

            // For a new participant record, pre-fill their name from the user record.
            if (!$participant->exists) {
                $participant->first_name = $user->first_name;
                $participant->last_name = $user->last_name;
                // Relative fields are left null for the participant to fill.
            }
        } else {
            // Fallback for unexpected roles/scenarios.
            return redirect()->route('dashboard')->with('error', 'You do not have a participant profile to complete.');
        }

        return view('profile.complete-participant-profile', compact('participant', 'user'));
    }

    /**
     * Store or update the participant profile.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $participant = null;
        $isFirstTimeCreation = false;

        // Determine if the user is a representative or a direct participant, and find/create the participant record.
        if ($user->is_representative) {
            $participant = $user->participantsRepresented()->firstOrNew(['representative_user_id' => $user->id, 'added_by_user_id' => $user->id]);
        } elseif ($user->role === 'participant') {
            $participant = $user->participant()->firstOrNew(['user_id' => $user->id, 'added_by_user_id' => $user->id]);
        }

        if (!$participant) {
            Log::error("CompleteParticipantProfileController@store: Participant record could not be found or initialized.", ['user_id' => $user->id, 'user_role' => $user->role, 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['message' => 'A critical error occurred. Please try again or contact support.']);
        }

        if (!$participant->exists) {
            $isFirstTimeCreation = true;
        }

        // Define validation rules based on the user's role.
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'is_looking_hm' => ['nullable', 'boolean'],
            'has_accommodation' => ['nullable', 'boolean'],
            'id' => ['nullable', 'exists:participants,id'], // Check for the participant_id from the form
        ];

        if (!$user->is_representative) {
            $rules['relative_name'] = ['nullable', 'string', 'max:255'];
            $rules['relative_phone'] = ['nullable', 'string', 'max:20'];
            $rules['relative_email'] = ['nullable', 'email', 'max:255'];
            $rules['relative_relationship'] = ['nullable', 'string', 'max:255'];
        }

        try {
            $validatedData = $request->validate($rules);

            DB::transaction(function () use ($user, $participant, $isFirstTimeCreation, $validatedData) {
                // Update participant's name fields from form input
                $participant->first_name = $validatedData['first_name'];
                $participant->middle_name = $validatedData['middle_name'] ?? null;
                $participant->last_name = $validatedData['last_name'];
                $participant->date_of_birth = $validatedData['date_of_birth'];

                // Relative's name fields (Emergency Contact)
                if ($user->is_representative) {
                    $participant->relative_name = $user->representative_first_name . ' ' . $user->representative_last_name;
                    $participant->relative_phone = $user->phone_number ?? null;
                    $participant->relative_email = $user->email;
                    $participant->relative_relationship = $user->relationship_to_participant ?? null;
                } else {
                    $participant->relative_name = $validatedData['relative_name'] ?? null;
                    $participant->relative_phone = $validatedData['relative_phone'] ?? null;
                    $participant->relative_email = $validatedData['relative_email'] ?? null;
                    $participant->relative_relationship = $validatedData['relative_relationship'] ?? null;
                }

                // Fill the rest of the attributes using the validated data
                $participant->fill([
                    'disability_type' => $validatedData['disability_type'] ?? [],
                    'specific_disability' => $validatedData['specific_disability'] ?? null,
                    'accommodation_type' => $validatedData['accommodation_type'] ?? null,
                    'street_address' => $validatedData['street_address'] ?? null,
                    'suburb' => $validatedData['suburb'] ?? null,
                    'state' => $validatedData['state'] ?? null,
                    'post_code' => $validatedData['post_code'] ?? null,
                    'is_looking_hm' => $validatedData['is_looking_hm'] ?? false,
                    'has_accommodation' => $validatedData['has_accommodation'] ?? false,
                ]);

                $participant->save();

                // If this is the first time, create a participant code.
                if ($isFirstTimeCreation && empty($participant->participant_code_name)) {
                    $participant->participant_code_name = 'PA' . str_pad($participant->id, 4, '0', STR_PAD_LEFT);
                    $participant->save();
                }

                // Mark the user's profile as completed.
                $user->profile_completed = true;
                $user->save();
            });

            // Set a flash message and redirect.
            $message = $isFirstTimeCreation
                ? 'Welcome aboard! Your profile has been successfully created. 🎉 You\'re now ready to explore opportunities and connect with the community. Let\'s get started!'
                : 'Great news! Your profile has been successfully updated. ✅ Your changes are now live, ensuring your information is always current and accurate.';

            $request->session()->flash('success', $message);
            return redirect()->route('indiv.dashboard');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info("Validation Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['errors' => $e->errors()]);
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error("General Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred. Please try again or contact support.']);
        }
    }
}
