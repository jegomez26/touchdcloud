<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\QueryException; // Import for better error handling
use Illuminate\Database\Eloquent\MassAssignmentException; // Import for better error handling
use Illuminate\Support\Facades\Log; // Import for logging

class CompleteParticipantProfileController extends Controller
{
    public function create()
    {
        $participant = null;
        if (Auth::user()->role === 'participant') {
            $participant = Auth::user()->participant;
        } elseif (Auth::user()->role === 'representative') {
            $participant = Auth::user()->participantsRepresented->first();
        }
        return view('profile.complete-participant-profile', compact('participant'));

    }



    /**
     * Store or update the participant profile.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $participant = null;

        if ($user->role === 'participant') {
            $participant = $user->participant;
            if (!$participant) {
                // If a direct participant doesn't have a record, create a new one.
                $participant = new Participant();
                $participant->user_id = $user->id; // Link to the current user
                $participant->added_by_user_id = $user->id; // User added themselves
            }
        } elseif ($user->role === 'representative') {
            // For representatives, we expect a participant record to already exist,
            // perhaps created during the registration process by the representative.
            // If the representative is completing the profile for the *first* participant
            // they just registered, this logic might need adjustment.
            // For now, it assumes they have at most one participant associated via `participantsRepresented`.

            // IMPORTANT: If a representative can complete profiles for *multiple* participants,
            // you'd need a hidden input or route parameter to specify *which* participant ID is being updated.
            // Example: <input type="hidden" name="participant_id" value="{{ $participant->id }}">
            // Then: $participant = Participant::find($request->input('participant_id'));

            // Current simplified logic: assuming one participant or first one for this representative
            if ($user->participantsRepresented->count() >= 1) { // Changed to >= 1 for robustness
                $participant = $user->participantsRepresented->first();
            } else {
                // Handle case where representative has no participants yet, or you need to select one
                return back()->withErrors(['message' => 'No participant record found for this representative to complete.']);
            }

        } else {
            return redirect()->route('dashboard')->with('error', 'You do not have a participant profile to complete.');
        }

        // --- Crucial Debugging Point ---
        // dump($participant); // Use dump() to see what $participant is without halting execution
        // dd(get_class($participant)); // Use dd() to see the class name and halt
        // --- End Debugging Point ---

        // This check should now be redundant if logic above ensures $participant is always an instance
        if (!$participant) {
            // This line should ideally not be reached if the above logic is sound.
            Log::error("CompleteParticipantProfileController: \$participant is null after determination logic.", ['user_id' => $user->id, 'user_role' => $user->role]);
            return back()->withErrors(['message' => 'Participant record could not be found or initialized.']);
        }

        // Validation rules
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before:today'],
            'disability_type' => ['required', 'string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'accommodation_type' => ['required', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            // Checkboxes: HTML sends 'on' or nothing. Cast in model handles this, or manually convert.
            'is_looking_hm' => ['nullable', 'boolean'], // Allow nullable for checkbox (if not checked)
            'has_accommodation' => ['nullable', 'boolean'], // Allow nullable for checkbox (if not checked)
            'participant_code_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('participants')->ignore($participant->id ?? null, 'id'), // Handles null ID for new records
            ],
            'relative_name' => ['nullable', 'string', 'max:255'],
        ];

        // Manually convert checkbox values from 'on' to boolean if not casted in model
        $request->merge([
            'is_looking_hm' => $request->has('is_looking_hm'),
            'has_accommodation' => $request->has('has_accommodation'),
        ]);

        $validatedData = $request->validate($rules);

        try {
            // Fill and save the participant record
            $participant->fill($validatedData);

            // You might explicitly save certain fields that are not in $validatedData but are needed
            // e.g., if you only set user_id/added_by_user_id on new Participant() and not in $validatedData
            // Example: $participant->user_id = $user->id; // Only if not already set or in $validatedData

            $participant->save(); // This should now work if $participant is a valid Model instance

            // Update the `profile_completed` flag on the User model
            $user->profile_completed = true;
            dd($participant);
            $user->save();

            return redirect()->route('dashboard')->with('status', 'Participant profile completed successfully!');

        } catch (MassAssignmentException $e) {
            Log::error("Mass Assignment Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'fillable_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A configuration error occurred. Missing fillable attributes. Please contact support.']);
        } catch (QueryException $e) {
            Log::error("Database Query Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'A database error occurred: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error("General Exception in CompleteParticipantProfileController@store: " . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }
}