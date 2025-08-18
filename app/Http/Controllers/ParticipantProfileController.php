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
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
        $participant = null;

        if ($user->is_representative) {
            // For a representative, find the first participant they represent.
            // If they are meant to *add multiple*, you'd need a different flow
            // (e.g., a "Add New Participant" button from their dashboard).
            // For this 'create' flow, we assume it's either the first one, or they are editing it.
            $participant = $user->participantsRepresented()->first();
            if (!$participant) {
                // Pre-fill relative contact details for the representative
                $participant = new Participant([
                    'representative_user_id' => $user->id,
                    'user_id' => $user->id,
                    'added_by_user_id' => $user->id,
                    // Participant's own name/email fields are left empty for the rep to fill
                    'first_name' => '',
                    'last_name' => '',
                    'participant_email' => '',
                    'relative_name' => $user->first_name . ' ' . $user->last_name, // Using representative's name
                    'relative_relationship' => $user->relationship_to_participant ?? 'Representative', // Assuming this field exists on User model
                    'relative_phone' => $user->phone_number, // Assuming this field exists on User model
                    'relative_email' => $user->email, // Using representative's email
                ]);
            }
        }elseif ($user->role === 'participant') {
            $participant = $user->participant;
            if (!$participant) {
                // Pre-fill for participant directly
                $participant = new Participant([
                    'user_id' => $user->id,
                    'added_by_user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'participant_email' => $user->email, // Pre-fill participant's email
                ]);
            }
        }  else {
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

    public function isBasicDetailsComplete(Participant $participant): bool
    {
        // This logic should match the one in calculateProfileCompletion for the basic details section.
        return (
            !empty($participant->first_name) &&
            !empty($participant->last_name) &&
            (!empty($participant->participant_email) || !empty($participant->participant_phone)) &&
            !empty($participant->date_of_birth) &&
            !empty($participant->gender_identity)
        );
    }


    /**
     * Calculates the profile completion status for a given participant.
     *
     * @param Participant $participant
     * @return array
     */
    public function calculateProfileCompletion(Participant $participant): int
    {
        $totalSections = 6; // As per your defined sections
        $completedSections = 0;

        // Section 1: Basic Details (Must match isBasicDetailsComplete)
        if (
            !empty($participant->first_name) &&
            !empty($participant->last_name) &&
            (!empty($participant->participant_email) || !empty($participant->participant_phone)) && // At least one contact method
            !empty($participant->date_of_birth) &&
            !empty($participant->gender_identity)
        ) {
            $completedSections++;
        }

        // Section 2: NDIS Details and Support Needs
        if (
            !empty($participant->sil_funding_status) &&
            !empty($participant->primary_disability) &&
            !empty($participant->ndis_plan_manager) &&
            !empty($participant->estimated_support_hours_sil_level) &&
            !empty($participant->night_support_type)
        ) {
            $completedSections++;
        }

        // Section 3: Health and Safety
        if (
            !empty($participant->medical_conditions_relevant) &&
            !empty($participant->medication_administration_help) &&
            !empty($participant->behaviour_support_plan_status)
        ) {
            $completedSections++;
        }

        // Section 4: Living Preferences
        $preferredLocations = self::getDecodedField($participant, 'preferred_sil_locations');
        $housematePreferences = self::getDecodedField($participant, 'housemate_preferences');
        $goodHomeEnvironment = self::getDecodedField($participant, 'good_home_environment_looks_like');

        if (
            !empty($preferredLocations) &&
            !empty($housematePreferences) &&
            !empty($participant->preferred_number_of_housemates) &&
            !empty($participant->accessibility_needs_in_home) &&
            !empty($participant->pets_in_home_preference) &&
            !empty($goodHomeEnvironment)
        ) {
            $completedSections++;
        }

        // Section 5: Compatibility and Personality
        $selfDescription = self::getDecodedField($participant, 'self_description');
        if (
            !empty($selfDescription) &&
            isset($participant->smokes) && is_bool($participant->smokes) && // Check if it's set and a boolean
            !empty($participant->deal_breakers_housemates) &&
            !empty($participant->cultural_religious_practices) &&
            !empty($participant->interests_hobbies)
        ) {
            $completedSections++;
        }

        // Section 6: Availability
        if (
            !empty($participant->move_in_availability) &&
            !empty($participant->current_living_situation) &&
            isset($participant->contact_for_suitable_match) && is_bool($participant->contact_for_suitable_match) &&
            !empty($participant->preferred_contact_method_match)
        ) {
            $completedSections++;
        }

        if ($totalSections === 0) {
            return 0; // Avoid division by zero
        }

        return (int) round(($completedSections / $totalSections) * 100);
    }

    /**
     * Show Basic Details page.
     */
    public function basicDetails()
    {
        $user = Auth::user(); // Get the authenticated user
        $participant = null; // Initialize participant outside try-catch

        try {
            $participant = $this->getParticipant();
        } catch (ModelNotFoundException $e) {
            // If getParticipant() indicates no participant (for a representative accessing directly),
            // redirect them to the initial setup flow.
            return redirect()->route('indiv.dashboard')->with('error', $e->getMessage());
        }

        // Call calculateProfileCompletion to get the percentage
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);

        // Pre-fill initial data on the view itself if the fields are empty on the participant model
        // This is important for subsequent visits to the form after initial creation
        if (!$participant->exists || empty($participant->first_name)) {
            if($user->is_representative) {
                $participantContact = $participant->participantContact ?? new ParticipantContact();

                if (empty($participantContact->full_name)) {
                    $participantContact->full_name = $user->first_name . ' ' . $user->last_name;
                    $participantContact->phone_number = $user->phone_number;
                    $participantContact->email_address = $user->email;
                    $participantContact->relationship_to_participant = $user->relationship_to_participant;

                    // Important: If a new ParticipantContact was created *in memory* here,
                    // and the form doesn't save it (e.g., if 'is_participant_best_contact' is true),
                    // it won't be persisted. This pre-fill is mainly for the view.
                    // Actual saving/updating happens in updateBasicDetails.
                }
                // Set the relation for the view, whether it's existing or newly prepared
                $participant->setRelation('participantContact', $participantContact);

            }
            elseif ($user->role === 'participant') {
                $participant->first_name = $user->first_name;
                $participant->last_name = $user->last_name;
                $participant->participant_email = $user->email;
            }
            // For representatives, participant's name/email are not pre-filled by user registration data
            // (as they are filling for someone else).
        }



        // Pass the participant and the completion percentage to the view
        return view('indiv.profile.basic-details', compact('participant', 'profileCompletionPercentage', 'user'));
    }

    /**
     * Update Basic Details.
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateBasicDetails(Request $request): RedirectResponse
    {
        $participant = $this->getParticipant();
        $user = Auth::user(); // Get the authenticated user

        // Determine if participant is the best contact for conditional validation
        $isParticipantBestContact = $request->has('is_participant_best_contact');

        $validatedParticipant = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'participant_email' => 'nullable|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'participant_contact_method' => 'nullable|in:Phone,Email,Either',
            'is_participant_best_contact' => 'nullable|boolean', // Use boolean for checkbox

            // New Address Fields
            'street_address' => 'nullable|string|max:255',
            'state' => ['nullable', Rule::in(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'])],
            'suburb' => 'nullable|string|max:255',
            'post_code' => 'nullable|string|max:10', // Corrected from 'post_code'

            // Basic Demographics
            'date_of_birth' => 'nullable|date',
            'gender_identity' => 'nullable|in:Female,Male,Non-binary,Prefer not to say,Other',
            'gender_identity_other' => 'nullable|string|max:255',
            'pronouns' => 'nullable|array',
            'pronouns.*' => 'string|max:255', // Validate each item in the array
            'pronouns_other_text' => 'nullable|string|max:255', // Add validation for the "other" text input
            'languages_spoken' => 'nullable|array',
            'languages_spoken.*' => 'string|max:255', // Validate each item in the array
            'languages_other_text' => 'nullable|string|max:255', // Add validation for the "other" text input
            'aboriginal_torres_strait_islander' => 'nullable|in:Yes,No,Prefer not to say',
        ]);

        // Validate contact person details only if 'is_participant_best_contact' is false
        $validatedContact = $request->validate([
            'contact_full_name' => 'nullable|string|max:255',
            'contact_relationship' => ['nullable', Rule::in(['Family member', 'Carer', 'Public Guardian', 'Support Worker', 'Other'])], // Use Rule::in for consistency with select options
            'contact_organisation' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'contact_consent' => ['nullable', Rule::in(['Yes', 'No', 'Consent pending or unsure'])],
        ]);

        // --- Participant Code Name Generation ---
        // Generate participant_code_name if it doesn't already exist
        if (empty($participant->participant_code_name)) {
            do {
                $code = 'PA' . Str::random(5); // Generate 'PA' + 5 random alphanumeric chars
            } while (Participant::where('participant_code_name', $code)->exists()); // Ensure uniqueness

            $validatedParticipant['participant_code_name'] = strtoupper($code); // Store it in uppercase
        }
        // --- End Participant Code Name Generation ---

        // Process participant specific data
        // Convert checkbox value to boolean
        $validatedParticipant['is_participant_best_contact'] = $isParticipantBestContact;

        // Handle 'Other' for gender identity
        if (isset($validatedParticipant['gender_identity']) && $validatedParticipant['gender_identity'] !== 'Other') {
            $validatedParticipant['gender_identity_other'] = null;
        }

        // Process pronouns: If 'Other' is selected and custom text is provided, merge them
        $processedPronouns = $validatedParticipant['pronouns'] ?? [];
        if (in_array('Other', $processedPronouns) && !empty($validatedParticipant['pronouns_other_text'])) {
            $processedPronouns = array_diff($processedPronouns, ['Other']); // Remove 'Other'
            $processedPronouns[] = $validatedParticipant['pronouns_other_text']; // Add the custom text
        }
        $validatedParticipant['pronouns'] = json_encode(array_values(array_unique(array_filter($processedPronouns)))); // Ensure unique values, filter empty, and re-index

        // Process languages_spoken: If 'Other' is selected and custom text is provided, merge them
        $processedLanguages = $validatedParticipant['languages_spoken'] ?? [];
        if (in_array('Other', $processedLanguages) && !empty($validatedParticipant['languages_other_text'])) {
            $processedLanguages = array_diff($processedLanguages, ['Other']); // Remove 'Other'
            $processedLanguages[] = $validatedParticipant['languages_other_text']; // Add the custom text
        }
        $validatedParticipant['languages_spoken'] = json_encode(array_values(array_unique(array_filter($processedLanguages)))); // Ensure unique values, filter empty, and re-index

        // Remove the 'other_text' fields from the participant array before updating, as they are now merged into the JSON arrays
        unset($validatedParticipant['pronouns_other_text']);
        unset($validatedParticipant['languages_other_text']);


        $participant->update($validatedParticipant);

        // Process contact person data based on the 'is_participant_best_contact' checkbox
        if (!$isParticipantBestContact) {
            $participant->participantContact()->updateOrCreate(
                ['participant_id' => $participant->id],
                [
                    'full_name' => $validatedContact['contact_full_name'] ?? null, // Null coalescing for safety with nullable rule
                    'relationship_to_participant' => $validatedContact['contact_relationship'] ?? null,
                    'organisation' => $validatedContact['contact_organisation'] ?? null,
                    'phone_number' => $validatedContact['contact_phone'] ?? null,
                    'email_address' => $validatedContact['contact_email'] ?? null,
                    'preferred_method_of_contact' => $validatedContact['contact_method'] ?? null,
                    'consent_to_speak_on_behalf' => $validatedContact['contact_consent'] ?? null,
                ]
            );
        } else {
            // If the participant IS the best contact, delete any existing associated contact person details
            $participant->participantContact()->delete();
        }

        // --- NEW LOGIC TO UPDATE user->profile_completed ---
        // After updating the participant details, re-check if basic details are complete
        if ($this->isBasicDetailsComplete($participant)) {
            // Check if the profile_completed flag is already true to avoid unnecessary updates
            if (!$user->profile_completed) {
                $user->update(['profile_completed' => true]);
            }
        } else {
            // If basic details are somehow incomplete after the update (e.g., due to validation changes),
            // ensure the profile_completed flag is false. This is a good fallback.
            if ($user->profile_completed) {
                $user->update(['profile_completed' => false]);
            }
        }
        // --- END NEW LOGIC ---

        return redirect()->route('indiv.profile.basic-details')
                         ->with('success', 'Basic details updated successfully!');
    }
    
    /**
     * Show NDIS Details page.
     */
    public function ndisDetails()
    {
        $participant = $this->getParticipant();
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('indiv.profile.ndis-support-needs', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update NDIS Details.
     */
    public function updateNdisDetails(Request $request)
    {
        $participant = $this->getParticipant();

        // The requestData[1] === null part seems unusual for typical form submissions.
        // It's removed unless there's a specific frontend reason for it.
        // If it was for unsetting an array element by index, a different approach might be needed.
        
        $validated = $request->validate([
            'sil_funding_status' => 'nullable|in:Yes,No,Not sure',
            'ndis_plan_review_date' => 'nullable|date',
            'ndis_plan_manager' => 'nullable|in:Self-managed,Plan-managed,NDIA-managed,Not sure',
            'has_support_coordinator' => 'nullable|boolean', // Assuming checkbox, storing boolean
            'daily_living_support_needs' => 'nullable|array',
            'daily_living_support_needs.*' => 'string|max:255',
            'daily_living_support_needs_other' => 'nullable|string',
            'primary_disability' => 'nullable|string|max:255',
            'secondary_disability' => 'nullable|string|max:255',
            'estimated_support_hours_sil_level' => 'nullable|string|max:50',
            'night_support_type' => 'nullable|in:Active overnight,Sleepover,None',
            'uses_assistive_technology_mobility_aids' => 'nullable|boolean', // Assuming checkbox, storing boolean
            'assistive_technology_mobility_aids_list' => 'nullable|string',
        ]);

        // Convert has_support_coordinator checkbox to boolean
        $validated['has_support_coordinator'] = $request->has('has_support_coordinator');
        // Convert uses_assistive_technology_mobility_aids checkbox to boolean
        $validated['uses_assistive_technology_mobility_aids'] = $request->has('uses_assistive_technology_mobility_aids');

        // Handle 'Other' for daily_living_support_needs if it's not selected, clear the text field.
        $processedDailyLivingNeeds = $validated['daily_living_support_needs'] ?? [];
        if (in_array('Other', $processedDailyLivingNeeds) && !empty($validated['daily_living_support_needs_other'])) {
            $processedDailyLivingNeeds = array_diff($processedDailyLivingNeeds, ['Other']); // Remove 'Other'
            $processedDailyLivingNeeds[] = $validated['daily_living_support_needs_other']; // Add the custom text
        }
        $participant->daily_living_support_needs = json_encode(array_values(array_unique(array_filter($processedDailyLivingNeeds))));
        unset($validated['daily_living_support_needs_other']); // Remove from $validated after processing

        // Handle assistive technology list if 'No' is selected or checkbox is unchecked
        if (!$validated['uses_assistive_technology_mobility_aids']) { 
            $validated['assistive_technology_mobility_aids_list'] = null;
        }

        // Exclude processed array fields and other_text fields from $validated before update
        unset($validated['daily_living_support_needs']); // We already handled this
        
        // Update the participant with all validated data
        $participant->update($validated);

        return redirect()->route('indiv.profile.ndis-support-needs')
                         ->with('success', 'NDIS details updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Health & Safety page.
     */
    public function healthSafety()
    {
        $participant = $this->getParticipant();
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('indiv.profile.health-safety', compact('participant', 'profileCompletionPercentage'));
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
            // 'behaviours_of_concern_housemates' is required_if 'behaviour_support_plan_status' is 'Yes'
            // Added max to ensure consistency
            'behaviours_of_concern_housemates' => 'nullable|string|max:1000|required_if:behaviour_support_plan_status,Yes', 
        ]);

        // If behaviour_support_plan_status is not 'Yes', ensure behaviours_of_concern_housemates is null
        if (($validated['behaviour_support_plan_status'] ?? null) !== 'Yes') { // Use null coalescing for safety
            $validated['behaviours_of_concern_housemates'] = null;
        }

        $participant->update($validated);

        return redirect()->route('indiv.profile.health-safety')
                         ->with('success', 'Health and safety information updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Living Preferences page.
     */
    public function livingPreferences()
    {
        $participant = $this->getParticipant();
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('indiv.profile.living-preferences', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update Living Preferences.
     */
    public function updateLivingPreferences(Request $request)
    {
        $participant = $this->getParticipant();

        $validated = $request->validate([
            'preferred_sil_locations' => 'nullable|array',
            'preferred_sil_locations.*.state' => 'required_with:preferred_sil_locations|string|max:255',
            'preferred_sil_locations.*.suburb' => 'required_with:preferred_sil_locations|string|max:255',

            'housemate_preferences' => 'nullable|array',
            'housemate_preferences.*' => 'string|max:255', 
            'housemate_preferences_other' => 'nullable|string|max:1000', // Added max

            'preferred_number_of_housemates' => 'nullable|in:1,2,3+,No preference',
            'accessibility_needs_in_home' => 'nullable|in:Fully accessible,Some modifications required,No specific needs',
            'accessibility_needs_details' => 'nullable|string|max:1000', // Added max

            'pets_in_home_preference' => 'nullable|in:Have pets,Can live with pets,Do not want to live with pets',
            'own_pet_type' => 'nullable|string|max:255',

            'good_home_environment_looks_like' => 'nullable|array',
            'good_home_environment_looks_like.*' => 'string|max:255',
            'good_home_environment_looks_like_other' => 'nullable|string|max:1000', // Added max
        ]);

        // Process housemate_preferences: If 'Other' is selected and custom text, merge
        $processedHousematePreferences = $validated['housemate_preferences'] ?? [];
        if (in_array('Other', $processedHousematePreferences) && !empty($validated['housemate_preferences_other'])) {
            $processedHousematePreferences = array_diff($processedHousematePreferences, ['Other']);
            $processedHousematePreferences[] = $validated['housemate_preferences_other'];
        }
        $participant->housemate_preferences = json_encode(array_values(array_unique(array_filter($processedHousematePreferences))));
        unset($validated['housemate_preferences_other']); // Remove after processing

        // Process good_home_environment_looks_like: If 'Other' is selected and custom text, merge
        $processedHomeEnvironment = $validated['good_home_environment_looks_like'] ?? [];
        if (in_array('Other', $processedHomeEnvironment) && !empty($validated['good_home_environment_looks_like_other'])) {
            $processedHomeEnvironment = array_diff($processedHomeEnvironment, ['Other']);
            $processedHomeEnvironment[] = $validated['good_home_environment_looks_like_other'];
        }
        $participant->good_home_environment_looks_like = json_encode(array_values(array_unique(array_filter($processedHomeEnvironment))));
        unset($validated['good_home_environment_looks_like_other']); // Remove after processing


        // Handle preferred_sil_locations
        if (isset($validated['preferred_sil_locations'])) {
            $filteredLocations = array_filter($validated['preferred_sil_locations'], function($location) {
                return !empty($location['state']) && !empty($location['suburb']);
            });
            $participant->preferred_sil_locations = json_encode(array_values($filteredLocations));
        } else {
            $participant->preferred_sil_locations = null;
        }
        unset($validated['preferred_sil_locations']); // Remove after processing

        // The remaining validated fields can be updated directly
        $participant->update($validated);

        return redirect()->route('indiv.profile.living-preferences')
            ->with('success', 'Living preferences updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Compatibility & Personality page.
     */
    public function compatibilityPersonality()
    {
        $participant = $this->getParticipant();
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('indiv.profile.compatibility-personality', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update Compatibility & Personality.
     */
    public function updateCompatibilityPersonality(Request $request)
    {
        $participant = $this->getParticipant(); // Fetch the participant

        $validated = $request->validate([
            'self_description' => 'nullable|array',
            'self_description.*' => 'string|max:255', 
            'self_description_other' => 'nullable|string|max:1000',
            'smokes' => ['nullable', Rule::in([0, 1])], // Validate as 0 or 1
            'deal_breakers_housemates' => 'nullable|string|max:1000',
            'cultural_religious_practices' => 'nullable|string|max:1000',
            'interests_hobbies' => 'nullable|string|max:1000',
        ]);

        // Process self_description: If 'Other' is selected and custom text, merge
        $processedSelfDescription = $validated['self_description'] ?? [];
        if (in_array('Other', $processedSelfDescription) && !empty($validated['self_description_other'])) {
            $processedSelfDescription = array_diff($processedSelfDescription, ['Other']);
            $processedSelfDescription[] = $validated['self_description_other'];
        }
        $participant->self_description = json_encode(array_values(array_unique(array_filter($processedSelfDescription))));
        unset($validated['self_description_other']); // Remove after processing
        unset($validated['self_description']); // Remove after processing

        // The 'smokes' field is a boolean, ensure it's stored as true/false
        // It comes as '0' or '1' from the form, so convert it.
        if (isset($validated['smokes'])) {
            $validated['smokes'] = (bool) $validated['smokes'];
        } else {
            $validated['smokes'] = null; 
        }

        // Update other fields directly
        $participant->update($validated);

        return redirect()->route('indiv.profile.compatibility-personality')
                         ->with('success', 'Compatibility & Personality updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Availability page.
     */
    public function availability()
    {
        $participant = $this->getParticipant();
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('indiv.profile.availability', compact('participant', 'profileCompletionPercentage'));
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
            'current_living_situation_other' => 'nullable|string|max:1000', // Added max

            // contact_for_suitable_match is a checkbox, so its presence determines true
            'contact_for_suitable_match' => 'nullable|boolean', 

            'preferred_contact_method_match' => 'nullable|in:Phone,Email,Via support coordinator,Other',
            'preferred_contact_method_match_other' => 'nullable|string|max:1000', // Added max
        ]);
        
        // Handle current_living_situation_other
        if (($validated['current_living_situation'] ?? null) !== 'Other') {
            $validated['current_living_situation_other'] = null;
        }

        // Handle preferred_contact_method_match_other
        if (($validated['preferred_contact_method_match'] ?? null) !== 'Other') {
            $validated['preferred_contact_method_match_other'] = null;
        }

        // The 'contact_for_suitable_match' is a boolean checkbox
        $validated['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');

        $participant->update($validated);

        return redirect()->route('indiv.profile.availability')
                         ->with('success', 'Availability updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Get decoded JSON field for forms
     */
    public static function getDecodedField($participant, $field)
    {
        if (!$participant || !isset($participant->$field) || $participant->$field === null) {
            return [];
        }
        
        $decoded = json_decode($participant->$field, true);
        return is_array($decoded) ? $decoded : [];
    }
}