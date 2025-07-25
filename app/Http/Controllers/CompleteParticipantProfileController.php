<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompleteParticipantProfileController extends Controller
{
    /**
     * Display the participant profile completion form.
     */
    public function create()
    {
        $user = Auth::user();
        $participant = null;

        // NEW LOGIC: Check is_representative FIRST for a representative user
        if ($user->is_representative) {
            // This user is a representative. They manage a participant.
            // A representative can potentially manage multiple participants,
            // but your current setup ($user->participantsRepresented->first())
            // suggests a 1:1 or 1:many where 'first' is relevant.
            // Ensure your User model's participantsRepresented relationship is correctly defined:
            // public function participantsRepresented() { return $this->hasMany(Participant::class, 'representative_user_id'); }
            $participant = $user->participantsRepresented()->first();

            if (!$participant) {
                // If no participant record exists yet for this representative, create a new instance
                $participant = new Participant();
                $participant->representative_user_id = $user->id;
                $participant->added_by_user_id = $user->id; // The representative themselves added it

                // Participant's name (from users.first_name/last_name for representatives)
                $participant->first_name = $user->first_name;
                $participant->last_name = $user->last_name;

                // Relative's name (which is the representative's name, from users.representative_first_name/last_name)
                $participant->relative_name = $user->representative_first_name . ' ' . $user->representative_last_name;
                $participant->relative_relationship = $user->relationship_to_participant ?? null;
                $participant->relative_phone = $user->phone_number ?? null; // Make sure 'phone_number' column exists on 'users' table
                $participant->relative_email = $user->email; // Assuming representative's email is the relative's email

                // DD should now hit here if you're logging in as a representative
                // dd([
                //     'Debug Point' => 'Inside Representative new participant creation block (is_representative is true)',
                //     'Authenticated User Data' => $user->toArray(),
                //     'Participant Object Prepared for View' => $participant->toArray(),
                //     'Is User Representative?' => $user->is_representative,
                //     'User Role' => $user->role, // Will likely be 'participant' as you stated
                // ]);
            }
            // If $participant DOES exist, its existing data will be used.
            // In this case, $participant->relative_name would already be populated from the database.
        }
        // If the user is NOT a representative, and their role is 'participant' (which it always is)
        // This block handles direct participants or the scenario where a representative's `is_representative` is false
        // but their role is still 'participant'. Given your clarification, this path
        // is now primarily for users who are *not* representatives.
        elseif ($user->role === 'participant' && !$user->is_representative) {
            $participant = $user->participant;
            if (!$participant) {
                $participant = new Participant();
                $participant->user_id = $user->id;
                $participant->added_by_user_id = $user->id;
                // For a direct participant, their name comes from their user record
                $participant->first_name = $user->first_name;
                $participant->last_name = $user->last_name;
                // Relative fields are left null for the participant to fill.
            }
        }
        // Fallback for unexpected roles/scenarios
        else {
            return redirect()->route('dashboard')->with('error', 'You do not have a participant profile to complete or an unrecognized user state.');
        }

        return view('profile.complete-participant-profile', compact('participant', 'user'));
    }

    /**
     * Store or update the participant profile.
     */
    public function store(Request $request)
    {
        // dd($request->all()); // Uncomment for debugging raw request data

        $user = Auth::user();
        $participant = null;
        $isFirstTimeCreation = false;

        // NEW LOGIC: Check is_representative FIRST for a representative user
        if ($user->is_representative) {
            $participantId = $request->input('participant_id');

            if ($participantId) {
                // Find participant associated with this representative
                $participant = Participant::where('representative_user_id', $user->id)->find($participantId);
                if (!$participant) {
                    return back()->withInput()->withErrors(['message' => 'Participant not found or you do not have permission to edit this record.']);
                }
            } else {
                // Representative creating a new participant record
                $isFirstTimeCreation = true;
                $participant = new Participant([
                    'representative_user_id' => $user->id,
                    'added_by_user_id' => $user->id,
                ]);
            }
        }
        // If the user is NOT a representative, and their role is 'participant' (which it always is)
        elseif ($user->role === 'participant' && !$user->is_representative) {
            $participant = $user->participant;
            if (!$participant) {
                $isFirstTimeCreation = true;
                $participant = new Participant([
                    'user_id' => $user->id,
                    'added_by_user_id' => $user->id,
                ]);
            }
        }
        // Fallback for unexpected roles/scenarios
        else {
            return redirect()->route('dashboard')->with('error', 'Unauthorized role for profile completion or an unrecognized user state.');
        }

        if (!$participant) {
            Log::error("CompleteParticipantProfileController@store: \$participant is null after determination logic.", ['user_id' => $user->id, 'user_role' => $user->role, 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['message' => 'A critical error occurred: Participant record could not be found or initialized. Please try again or contact support.']);
        }

        $rules = [
            'participant_first_name' => ['required', 'string', 'max:255'],
            'participant_last_name' => ['required', 'string', 'max:255'],
            'participant_middle_name' => ['nullable', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before:today', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')], // Ensures 18+
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
            'participant_id' => ['nullable', 'exists:participants,id'],
        ];

        // The relative fields are only user-editable if the user is a direct participant (i.e., not a representative).
        // If the user is a representative, these fields are pre-filled from their own user record and should be readonly.
        if (!$user->is_representative) { // This condition is now correct
            $rules['relative_name'] = ['nullable', 'string', 'max:255'];
            $rules['relative_phone'] = ['nullable', 'string', 'max:20'];
            $rules['relative_email'] = ['nullable', 'email', 'max:255'];
            $rules['relationship_to_participant'] = ['nullable', 'string', 'max:255'];
        }

        try {
            $validatedData = $request->validate($rules);

            DB::transaction(function () use ($request, $user, $participant, $isFirstTimeCreation, $validatedData) {
                // Participant's own name fields (always from form input)
                $participant->first_name = $validatedData['participant_first_name'];
                $participant->middle_name = $validatedData['participant_middle_name'] ?? null;
                $participant->last_name = $validatedData['participant_last_name'];

                // Relative's name fields (Emergency Contact)
                if ($user->is_representative) { // This condition is now correct
                    // For representatives, the relative IS the representative themselves.
                    // Their name is stored in users.representative_first_name/last_name.
                    $participant->relative_name = $user->representative_first_name . ' ' . $user->representative_last_name;
                    $participant->relative_phone = $user->phone_number ?? null; // Still ensure phone_number column exists on User
                    $participant->relative_email = $user->email; // Assuming representative's email is the relative's email
                    $participant->relative_relationship = $user->relationship_to_participant ?? null;
                } else {
                    // For direct participants (who are not representatives), they input their emergency contact.
                    $participant->relative_name = $validatedData['relative_name'] ?? null;
                    $participant->relative_phone = $validatedData['relative_phone'] ?? null;
                    $participant->relative_email = $validatedData['relative_email'] ?? null;
                    $participant->relative_relationship = $validatedData['relationship_to_participant'] ?? null;
                }

                $participant->fill([
                    'birthday' => $validatedData['birthday'],
                    'disability_type' => json_encode($validatedData['disability_type'] ?? []),
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

                if ($isFirstTimeCreation && empty($participant->participant_code_name)) {
                    $participant->participant_code_name = 'PA' . str_pad($participant->id, 4, '0', STR_PAD_LEFT);
                    $participant->save();
                }

                $user->profile_completed = true;
                $user->save();
            });

            if ($isFirstTimeCreation) {
                $request->session()->flash('success', 'Welcome aboard! Your profile has been successfully created. 🎉 You\'re now ready to explore opportunities and connect with the community. Let\'s get started!');
            } else {
                $request->session()->flash('success', 'Great news! Your profile has been successfully updated. ✅ Your changes are now live, ensuring your information is always current and accurate.');
            }

            return redirect()->route('indiv.dashboard');

        } catch (MassAssignmentException $e) {
            Log::error("Mass Assignment Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'fillable_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A configuration error occurred. Missing fillable attributes in the Participant model. Please contact support.']);
        } catch (QueryException $e) {
            Log::error("Database Query Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A database error occurred while saving your profile. Please try again.']);
        } catch (\Exception $e) {
            Log::error("General Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred. Please try again or contact support.']);
        }
    }
}