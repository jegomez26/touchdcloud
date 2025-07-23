<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Participant;
use App\Models\User; // Make sure User model is imported
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\Log;

class CompleteParticipantProfileController extends Controller
{
    /**
     * Display the participant profile completion form.
     */
    public function create()
    {
        $user = Auth::user();
        $participant = null;

        // Determine which participant record to load/create based on user role
        if ($user->role === 'participant') {
            // For a direct participant, try to load their linked participant record
            $participant = $user->participant;
            if (!$participant) {
                // If no participant record exists, create a new instance
                $participant = new Participant();
                $participant->user_id = $user->id;
                $participant->added_by_user_id = $user->id;
            }
        } elseif ($user->role === 'representative') {
            // For a representative, assume they are completing the profile for *one* participant.
            // You might need more sophisticated logic here if a representative manages multiple participants
            // and you want to allow them to select which one to complete/edit.
            // For now, we take the first one or create a new one if none exist.
            $participant = $user->participantsRepresented->first();
            if (!$participant) {
                // If the representative hasn't created a participant profile yet
                $participant = new Participant();
                $participant->representative_user_id = $user->id; // Link to the representative user
                $participant->added_by_user_id = $user->id; // Representative added this participant
            }
        } else {
            // Redirect users with other roles away from this form
            return redirect()->route('dashboard')->with('error', 'You do not have a participant profile to complete.');
        }

        // Pass both $participant and $user to the view
        return view('profile.complete-participant-profile', compact('participant', 'user'));
    }

    /**
     * Store or update the participant profile.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $participant = null;
        $isFirstTimeCreation = false; // Flag to determine success message

        // Determine if creating new or updating existing participant profile
        if ($user->role === 'participant') {
            $participant = $user->participant;
            if (!$participant) {
                $isFirstTimeCreation = true;
                $participant = new Participant(['user_id' => $user->id, 'added_by_user_id' => $user->id]);
            }
        } elseif ($user->role === 'representative') {
            // If the representative is updating an existing participant profile,
            // they would ideally pass a participant_id in a hidden field.
            // If participant_id is NOT provided, assume it's a new participant being added.
            $participantId = $request->input('participant_id');

            if ($participantId) {
                // Attempt to find the participant linked to this representative
                $participant = Participant::where('representative_user_id', $user->id)
                                          ->find($participantId);
                if (!$participant) {
                    return back()->withInput()->withErrors(['message' => 'Participant not found or you do not have permission to edit this record.']);
                }
            } else {
                // If no participant_id, it's a new participant for this representative
                $isFirstTimeCreation = true;
                $participant = new Participant(['representative_user_id' => $user->id, 'added_by_user_id' => $user->id]);
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Unauthorized role for profile completion.');
        }

        // --- Critical check: If $participant is still null, something went wrong ---
        if (!$participant) {
            Log::error("CompleteParticipantProfileController@store: \$participant is null after determination logic.", ['user_id' => $user->id, 'user_role' => $user->role, 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['message' => 'A critical error occurred: Participant record could not be found or initialized. Please try again or contact support.']);
        }

        // Validation rules
        $rules = [
            'participant_first_name' => ['required', 'string', 'max:255'],
            'participant_last_name' => ['required', 'string', 'max:255'],
            'participant_middle_name' => ['nullable', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before:today', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            'is_looking_hm' => ['nullable', 'boolean'], // 'boolean' rule correctly handles presence/absence for checkboxes
            'has_accommodation' => ['nullable', 'boolean'], // 'boolean' rule correctly handles presence/absence for checkboxes
            'relative_name' => ['nullable', 'string', 'max:255'],
            // 'participant_id' is used internally for logic, not stored directly on Participant model
            'participant_id' => ['nullable', 'exists:participants,id'],
        ];

        // Validate the request data
        $validatedData = $request->validate($rules);

        try {
            // Assign participant's name fields (these might be separate from the `fillable` array if desired)
            $participant->first_name = $validatedData['participant_first_name'];
            $participant->middle_name = $validatedData['participant_middle_name'];
            $participant->last_name = $validatedData['participant_last_name'];

            // Remove participant name fields from validatedData to prevent mass assignment errors
            // if your Participant model's $fillable does not include 'participant_first_name' etc.
            unset(
                $validatedData['participant_first_name'],
                $validatedData['participant_middle_name'],
                $validatedData['participant_last_name'],
                $validatedData['participant_id'] // Remove internal ID
            );

            // Fill the rest of the validated data into the participant model
            // This relies on your Participant model having these fields in its $fillable array
            $participant->fill($validatedData);

            // Explicitly set boolean fields if they might not be present in $validatedData
            // (e.g., if checkboxes are unchecked, they don't appear in request, 'boolean' rule helps)
            $participant->is_looking_hm = $request->has('is_looking_hm');
            $participant->has_accommodation = $request->has('has_accommodation');

            $participant->save();

            // Always mark the user's profile as completed after a successful participant profile save
            // This is crucial for the redirect logic in AuthenticatedSessionController
            $user->profile_completed = true;
            $user->save();

            // Set the appropriate flash message based on whether it was a creation or update
            if ($isFirstTimeCreation) {
                $request->session()->flash('success', 'Welcome aboard! Your profile has been successfully created. 🎉 You\'re now ready to explore opportunities and connect with the community. Let\'s get started!');
            } else {
                $request->session()->flash('success', 'Great news! Your profile has been successfully updated. ✅ Your changes are now live, ensuring your information is always current and accurate.');
            }

            // Redirect to the individual dashboard (or a relevant dashboard)
            return redirect()->route('indiv.dashboard');

        } catch (MassAssignmentException $e) {
            Log::error("Mass Assignment Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'fillable_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A configuration error occurred. Missing fillable attributes in the Participant model. Please contact support.']);
        } catch (QueryException $e) {
            // Check for specific error codes if needed, e.g., unique constraint violation
            Log::error("Database Query Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A database error occurred while saving your profile. Please try again.']);
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions
            Log::error("General Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred. Please try again or contact support.']);
        }
    }
}