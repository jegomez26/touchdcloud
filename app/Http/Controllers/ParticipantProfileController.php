<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Participant;
use App\Models\User;
use App\Models\ParticipantContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ParticipantProfileController extends Controller
{
    /**
     * The single entry point for the participant profile creation flow.
     * This method ensures a participant record exists before redirecting to the first form step.
     */
    public function create()
    {
        $user = Auth::user();

        // If the user has already completed their profile, redirect them to the dashboard.
        if ($user->profile_completed) {
            return redirect()->route('indiv.dashboard');
        }

        // Find or create the participant record based on user type.
        $participant = $this->findOrCreateParticipant($user);
        
        // If the participant record was just created, we'll pre-fill some details
        // before redirecting to the first form page.
        if (!$participant->exists) {
            $participant->save();
        }

        // Redirect to the first step of the profile creation.
        // The basic-details method will now assume the participant record exists.
        return redirect()->route('indiv.profile.basic-details');
    }

    /**
     * Finds an existing participant record or creates a new one.
     * This method is now private and should only be called once at the start of the flow.
     * @param User $user
     * @return Participant
     */
    private function findOrCreateParticipant(User $user)
    {
        if ($user->is_representative) {
            $participant = $user->participantsRepresented()->first() ?? new Participant([
                'representative_user_id' => $user->id,
                'added_by_user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'relative_name' => $user->representative_first_name . ' ' . $user->representative_last_name,
                'relative_relationship' => $user->relationship_to_participant,
                'relative_phone' => $user->phone_number,
                'relative_email' => $user->email,
            ]);
        } elseif ($user->role === 'participant') {
            $participant = $user->participant ?? new Participant([
                'user_id' => $user->id,
                'added_by_user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);
        } else {
            // Fallback for unexpected roles/scenarios.
            // This case should be handled by middleware or the create method's initial checks.
            abort(403, 'Unauthorized access.');
        }

        return $participant;
    }

    /**
     * Retrieves the participant record for the authenticated user.
     * This method no longer redirects, as the record is assumed to exist.
     * @return Participant
     */
    private function getParticipant(): Participant
    {
        $user = Auth::user();

        if ($user->role === 'participant') {
            // This is the key part: eager load the participantContact relationship
            return Participant::where('user_id', $user->id)
                            ->with('participantContact') // <--- Make sure this line exists!
                            ->firstOrFail(); // Ensures a participant is found or throws 404
        } elseif ($user->is_representative) {
            $participant = $user->participantsRepresented()
                                ->with('participantContact') // <--- Make sure this line exists!
                                ->first(); // Assuming a representative manages one participant for this profile flow
            if (!$participant) {
                throw new \Exception("No participant record found for this representative.");
            }
            return $participant;
        }

        // Fallback for unexpected roles/scenarios
        abort(403, 'Unauthorized access or participant record missing.');
    }

    /**
     * Show Basic Details page.
     */
    public function basicDetails()
    {
        $participant = $this->getParticipant();

        // dd($participant->participantContact);
        
        return view('indiv.profile.basic-details', compact('participant'));
    }

    /**
     * Update Basic Details.
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateBasicDetails(Request $request): RedirectResponse
    {
        $participant = $this->getParticipant();

        $validatedParticipant = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'participant_email' => 'nullable|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'participant_contact_method' => 'nullable|in:Phone,Email,Either',
            'is_participant_best_contact' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender_identity' => 'nullable|in:Female,Male,Non-binary,Prefer not to say,Other',
            'gender_identity_other' => 'nullable|string|max:255',
            'pronouns' => 'nullable|array',
            'languages_spoken' => 'nullable|array',
            'aboriginal_torres_strait_islander' => 'nullable|in:Yes,No,Prefer not to say',
        ]);

        $validatedContact = $request->validate([
            'contact_full_name' => 'nullable|string|max:255',
            'contact_relationship' => 'nullable|string|max:255', // Adjust based on your relationships list
            'contact_organisation' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_method' => 'nullable|in:Phone,Email,Either',
            'contact_consent' => 'nullable|in:Yes,No,Consent pending or unsure',
        ]);

        // Process participant specific data
        $validatedParticipant['pronouns'] = json_encode(array_filter($request->input('pronouns') ?? []));
        $validatedParticipant['languages_spoken'] = json_encode(array_filter($request->input('languages_spoken') ?? []));
        $validatedParticipant['is_participant_best_contact'] = $request->has('is_participant_best_contact');

        if (array_key_exists('gender_identity', $validatedParticipant) && $validatedParticipant['gender_identity'] !== 'Other') {
            $validatedParticipant['gender_identity_other'] = null;
        }

        $participant->update($validatedParticipant);

        // Process contact person data if checkbox is unchecked (meaning contact person fields are visible and potentially filled)
        if (!$validatedParticipant['is_participant_best_contact']) {
            ParticipantContact::updateOrCreate(
                ['participant_id' => $participant->id],
                [
                    'full_name' => $validatedContact['contact_full_name'] ?? null,
                    'relationship_to_participant' => $validatedContact['contact_relationship'] ?? null,
                    'organisation' => $validatedContact['contact_organisation'] ?? null,
                    'phone_number' => $validatedContact['contact_phone'] ?? null,
                    'email_address' => $validatedContact['contact_email'] ?? null,
                    'preferred_method_of_contact' => $validatedContact['contact_method'] ?? null,
                    'consent_to_speak_on_behalf' => $validatedContact['contact_consent'] ?? null,
                ]
            );
        } else {
            // If the participant IS the best contact, you might want to clear existing contact details
            // or just ensure no new contact details are saved if they weren't submitted.
            // For now, let's assume they should be cleared if the checkbox is checked.
            $participant->participantContact()->delete();
        }

        return redirect()->route('indiv.profile.basic-details')
                        ->with('success', 'Basic details saved. Please complete the next section.');
    }
    

    /**
     * Show NDIS Details page.
     */
    public function ndisDetails()
    {
        $participant = $this->getParticipant();
        // dd($participant);
        return view('indiv.profile.ndis-support-needs', compact('participant'));
    }

    /**
     * Update NDIS Details.
     */
    public function updateNdisDetails(Request $request)
    {
        // dd($request->all());
        $participant = $this->getParticipant();

        $requestData = $request->all();
        if (isset($requestData[1]) && $requestData[1] === null) {
            unset($requestData[1]);
            $request->replace($requestData); // Replace the request instance's data
        }
        
        $validated = $request->validate([
            'sil_funding_status' => 'nullable|in:Yes,No,Not sure',
            'ndis_plan_review_date' => 'nullable|date',
            'ndis_plan_manager' => 'nullable|in:Self-managed,Plan-managed,NDIA-managed,Not sure',
            'has_support_coordinator' => 'nullable|string',
            'daily_living_support_needs' => 'nullable|array',
            'daily_living_support_needs_other' => 'nullable|string',
            'primary_disability' => 'nullable|string|max:255',
            'secondary_disability' => 'nullable|string|max:255',
            'estimated_support_hours_sil_level' => 'nullable|string|max:50',
            'night_support_type' => 'nullable|in:Active overnight,Sleepover,None',
            'uses_assistive_technology_mobility_aids' => 'nullable|string',
            'assistive_technology_mobility_aids_list' => 'nullable|string',
        ]);

        

        $participant->daily_living_support_needs = $validated['daily_living_support_needs'] ?? null;

        // Handle 'Other' if it's not selected, clear the text field.
        if (!isset($validated['daily_living_support_needs']) || !in_array('Other', $validated['daily_living_support_needs'])) {
            $validated['daily_living_support_needs_other'] = null;
        }

        // Handle assistive technology list if 'No' is selected
        if (!isset($validated['uses_assistive_technology_mobility_aids']) || !$validated['uses_assistive_technology_mobility_aids']) { // If false or null
            $validated['assistive_technology_mobility_aids_list'] = null;
        }


        // Update the participant with all validated data
        $participant->update($validated);

        return redirect()->route('indiv.profile.ndis-support-needs')
                        ->with('success', 'NDIS details updated successfully!');
    }

    /**
     * Show Health & Safety page.
     */
    public function healthSafety()
    {
        $participant = $this->getParticipant();
        return view('indiv.profile.health-safety', compact('participant'));
    }

    /**
     * Update Health & Safety.
     */
    public function updateHealthSafety(Request $request)
    {
        $participant = $this->getParticipant();
        
        $validated = $request->validate([
            'medical_conditions_relevant' => 'nullable|string',
            'medication_administration_help' => 'nullable|in:Yes,No,Sometimes',
            'behaviour_support_plan_status' => 'nullable|in:Yes,No,In development',
            'behaviours_of_concern_housemates' => 'nullable|string|required_if:behaviour_support_plan_status,Yes',
        ]);

        // If behaviour_support_plan_status is not 'Yes', ensure behaviours_of_concern_housemates is null
        if ($validated['behaviour_support_plan_status'] !== 'Yes') {
            $validated['behaviours_of_concern_housemates'] = null;
        }

        $participant->update($validated);

        return redirect()->route('indiv.profile.health-safety')
                         ->with('success', 'Health and safety information updated successfully!');
    }

    /**
     * Show Living Preferences page.
     */
    public function livingPreferences()
    {
        $participant = $this->getParticipant();
        return view('indiv.profile.living-preferences', compact('participant'));
    }

    /**
     * Update Living Preferences.
     */
    public function updateLivingPreferences(Request $request)
    {
        $participant = $this->getParticipant();
        
        $validated = $request->validate([
            'preferred_sil_locations' => 'nullable|string',
            'housemate_preferences' => 'nullable|string',
            'housemate_preferences_other' => 'nullable|string',
            'preferred_number_of_housemates' => 'nullable|in:1,2,3+,No preference',
            'accessibility_needs_in_home' => 'nullable|in:Fully accessible,Some modifications required,No specific needs',
            'accessibility_needs_details' => 'nullable|string',
            'pets_in_home_preference' => 'nullable|in:Have pets,Can live with pets,Do not want to live with pets',
            'own_pet_type' => 'nullable|string|max:255',
            'good_home_environment_looks_like' => 'nullable|string',
            'good_home_environment_looks_like_other' => 'nullable|string',
        ]);

        if (array_key_exists('preferred_sil_locations', $validated)) {
            $locationsArray = array_map('trim', explode(',', $validated['preferred_sil_locations']));
            $validated['preferred_sil_locations'] = json_encode(array_filter($locationsArray));
        } else {
            $validated['preferred_sil_locations'] = null;
        }
        
        if (array_key_exists('housemate_preferences', $validated)) {
            $housemateArray = array_map('trim', explode(',', $validated['housemate_preferences']));
            $validated['housemate_preferences'] = json_encode(array_filter($housemateArray));
        } else {
            $validated['housemate_preferences'] = null;
        }
        
        if (array_key_exists('good_home_environment_looks_like', $validated)) {
            $environmentArray = array_map('trim', explode(',', $validated['good_home_environment_looks_like']));
            $validated['good_home_environment_looks_like'] = json_encode(array_filter($environmentArray));
        } else {
            $validated['good_home_environment_looks_like'] = null;
        }

        $participant->update($validated);

        return redirect()->route('indiv.profile.living-preferences')
                         ->with('success', 'Living preferences updated successfully!');
    }

    /**
     * Show Compatibility & Personality page.
     */
    public function compatibilityPersonality()
    {
        $participant = $this->getParticipant();
        return view('indiv.profile.compatibility-personality', compact('participant'));
    }

    /**
     * Update Compatibility & Personality.
     */
    public function updateCompatibilityPersonality(Request $request)
    {
        $participant = $this->getParticipant();
        
        $validated = $request->validate([
            'self_description' => 'nullable|string',
            'self_description_other' => 'nullable|string',
            'smokes' => 'nullable|string',
            'deal_breakers_housemates' => 'nullable|string',
            'cultural_religious_practices' => 'nullable|string',
            'interests_hobbies' => 'nullable|string',
        ]);

        $validated['smokes'] = $request->has('smokes');
        
        if (array_key_exists('self_description', $validated)) {
            $descriptionArray = array_map('trim', explode(',', $validated['self_description']));
            $validated['self_description'] = json_encode(array_filter($descriptionArray));
        } else {
            $validated['self_description'] = null;
        }

        $participant->update($validated);

        return redirect()->route('indiv.profile.compatibility-personality')
                         ->with('success', 'Compatibility and personality updated successfully!');
    }

    /**
     * Show Availability page.
     */
    public function availability()
    {
        $participant = $this->getParticipant();
        return view('indiv.profile.availability', compact('participant'));
    }

    /**
     * Update Availability.
     */
    public function updateAvailability(Request $request)
    {
        $participant = $this->getParticipant();
        
        $validated = $request->validate([
            'move_in_availability' => 'nullable|in:ASAP,Within 1–3 months,Within 3–6 months,Just exploring options',
            'current_living_situation' => 'nullable|in:SIL or SDA accommodation,Group home,With family,Living alone,Other',
            'current_living_situation_other' => 'nullable|string',
            'contact_for_suitable_match' => 'nullable|string',
            'preferred_contact_method_match' => 'nullable|in:Phone,Email,Via support coordinator,Other',
            'preferred_contact_method_match_other' => 'nullable|string',
        ]);
        
        $validated['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');

        $participant->update($validated);

        return redirect()->route('indiv.profile.availability')
                         ->with('success', 'Availability updated successfully!');
    }

    

    /**
     * Get decoded JSON field for forms
     */
    public static function getDecodedField($participant, $field)
    {
        if (!$participant || !$participant->$field) {
            return [];
        }
        
        $decoded = json_decode($participant->$field, true);
        return is_array($decoded) ? $decoded : [];
    }
}
