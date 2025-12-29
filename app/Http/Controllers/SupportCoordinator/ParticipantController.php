<?php

namespace App\Http\Controllers\SupportCoordinator;

use App\Http\Controllers\Controller;
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


class ParticipantController extends Controller
{
    public function create()
    {
        $supportCoordinator = Auth::user();
        $participant = new Participant();


        return view('supcoor.participants.create.basic-details', compact('participant'));
    }

    public function show(Participant $participant)
    {
        $participant->load('participantContact'); // Eager load the contact details

        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.show', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Store a newly created participant in storage.
     * This method handles the form submission from the 'create' view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $supportCoordinator = Auth::user();

        // Validate the incoming request data for the new participant
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'participant_email' => 'nullable|email|max:255|unique:participants,participant_email', // Ensure email is unique across participants
            'participant_phone' => 'nullable|string|max:20',
            'participant_contact_method' => 'nullable|in:Phone,Email,Either',

            'street_address' => 'nullable|string|max:255',
            'state' => ['nullable', Rule::in(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'])],
            'suburb' => 'nullable|string|max:255',
            'post_code' => 'nullable|string|max:10',

            'date_of_birth' => 'nullable|date',
            'gender_identity' => 'nullable|in:Female,Male,Non-binary,Prefer not to say,Other',
            'gender_identity_other' => 'nullable|string|max:255',
            'pronouns' => 'nullable|array',
            'pronouns.*' => 'string|max:255',
            'pronouns_other_text' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|array',
            'languages_spoken.*' => 'string|max:255',
            'languages_other_text' => 'nullable|string|max:255',
            'aboriginal_torres_strait_islander' => 'nullable|in:Yes,No,Prefer not to say',

            // Contact Person Details (these are for the participant's emergency/secondary contact, not the support coord)
            'is_participant_best_contact' => 'nullable|boolean',
            'contact_full_name' => 'nullable|string|max:255',
            'contact_relationship' => ['nullable', Rule::in(['Family member', 'Carer', 'Public Guardian', 'Support Worker', 'Other'])],
            'contact_organisation' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'contact_consent' => ['nullable', Rule::in(['Yes', 'No', 'Consent pending or unsure'])],
        ]);

        // --- Participant Code Name Generation ---
        // Generate participant_code_name
        do {
            $code = 'PA' . Str::random(5); // Generate 'PA' + 5 random alphanumeric chars
        } while (Participant::where('participant_code_name', $code)->exists()); // Ensure uniqueness

        $validatedData['participant_code_name'] = strtoupper($code); // Store it in uppercase
        // --- End Participant Code Name Generation ---

        // Set the support coordinator as the user who added this participant
        $validatedData['added_by_user_id'] = $supportCoordinator->id;
        $validatedData['support_coordinator_id'] = $supportCoordinator->id;

        // Process boolean checkboxes
        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');

        // Handle 'Other' for gender identity
        if (isset($validatedData['gender_identity']) && $validatedData['gender_identity'] !== 'Other') {
            $validatedData['gender_identity_other'] = null;
        }

        // Process pronouns: If 'Other' is selected and custom text is provided, merge them
        $processedPronouns = $validatedData['pronouns'] ?? [];
        if (in_array('Other', $processedPronouns) && !empty($validatedData['pronouns_other_text'])) {
            $processedPronouns = array_diff($processedPronouns, ['Other']);
            $processedPronouns[] = $validatedData['pronouns_other_text'];
        }
        $validatedData['pronouns'] = json_encode(array_values(array_unique(array_filter($processedPronouns))));

        // Process languages_spoken: If 'Other' is selected and custom text is provided, merge them
        $processedLanguages = $validatedData['languages_spoken'] ?? [];
        if (in_array('Other', $processedLanguages) && !empty($validatedData['languages_other_text'])) {
            $processedLanguages = array_diff($processedLanguages, ['Other']);
            $processedLanguages[] = $validatedData['languages_other_text'];
        }
        $validatedData['languages_spoken'] = json_encode(array_values(array_unique(array_filter($processedLanguages))));

        // Remove the 'other_text' and array fields that were processed for JSON storage
        unset($validatedData['pronouns_other_text']);
        unset($validatedData['languages_other_text']);

        // Create the participant record
        $participant = Participant::create($validatedData);

        // Process and save contact person details if 'is_participant_best_contact' is false
        if (!$validatedData['is_participant_best_contact']) {
            $participant->participantContact()->create([
                'full_name' => $validatedData['contact_full_name'] ?? null,
                'relationship_to_participant' => $validatedData['contact_relationship'] ?? null,
                'organisation' => $validatedData['contact_organisation'] ?? null,
                'phone_number' => $validatedData['contact_phone'] ?? null,
                'email_address' => $validatedData['contact_email'] ?? null,
                'preferred_method_of_contact' => $validatedData['contact_method'] ?? null,
                'consent_to_speak_on_behalf' => $validatedData['contact_consent'] ?? null,
            ]);
        }

        // Redirect to a list of participants or the newly created participant's details
        return redirect()->route('sc.participants.profile.basic-details', $participant->id)->with('success', 'Participant created successfully! 🎉');
    }

    public function showBasicDetails(Participant $participant)
    {
        // This method will be used to display the form for editing basic details.
        // The $participant is automatically injected by Route Model Binding.
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.basic-details', compact('participant', 'profileCompletionPercentage'));
    }

    public function updateBasicDetails(Request $request, Participant $participant): RedirectResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'participant_email' => ['nullable', 'email', 'max:255', Rule::unique('participants', 'participant_email')->ignore($participant->id)], // Unique except for current participant
            'participant_phone' => 'nullable|string|max:20',
            'participant_contact_method' => 'nullable|in:Phone,Email,Either',

            'street_address' => 'nullable|string|max:255',
            'state' => ['nullable', Rule::in(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'])],
            'suburb' => 'nullable|string|max:255',
            'post_code' => 'nullable|string|max:10',

            'date_of_birth' => 'nullable|date',
            'gender_identity' => 'nullable|in:Female,Male,Non-binary,Prefer not to say,Other',
            'gender_identity_other' => 'nullable|string|max:255',
            'pronouns' => 'nullable|array',
            'pronouns.*' => 'string|max:255',
            'pronouns_other_text' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|array',
            'languages_spoken.*' => 'string|max:255',
            'languages_other_text' => 'nullable|string|max:255',
            'aboriginal_torres_strait_islander' => 'nullable|in:Yes,No,Prefer not to say',

            // Contact Person Details
            'is_participant_best_contact' => 'nullable|boolean',
            'contact_full_name' => 'nullable|string|max:255',
            'contact_relationship' => ['nullable', Rule::in(['Family member', 'Carer', 'Public Guardian', 'Support Worker', 'Other'])],
            'contact_organisation' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'contact_consent' => ['nullable', Rule::in(['Yes', 'No', 'Consent pending or unsure'])],
        ]);

        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');

        if (isset($validatedData['gender_identity']) && $validatedData['gender_identity'] !== 'Other') {
            $validatedData['gender_identity_other'] = null;
        }

        $processedPronouns = $validatedData['pronouns'] ?? [];
        if (in_array('Other', $processedPronouns) && !empty($validatedData['pronouns_other_text'])) {
            $processedPronouns = array_diff($processedPronouns, ['Other']);
            $processedPronouns[] = $validatedData['pronouns_other_text'];
        }
        $validatedData['pronouns'] = json_encode(array_values(array_unique(array_filter($processedPronouns))));

        $processedLanguages = $validatedData['languages_spoken'] ?? [];
        if (in_array('Other', $processedLanguages) && !empty($validatedData['languages_other_text'])) {
            $processedLanguages = array_diff($processedLanguages, ['Other']);
            $processedLanguages[] = $validatedData['languages_other_text'];
        }
        $validatedData['languages_spoken'] = json_encode(array_values(array_unique(array_filter($processedLanguages))));

        unset($validatedData['pronouns_other_text']);
        unset($validatedData['languages_other_text']);
        // Don't unset 'pronouns' or 'languages_spoken' from $validatedData if your model casts them.
        // If not, ensure you're using the JSON-encoded versions.


        $participant->update($validatedData);

        // Process and save contact person details if 'is_participant_best_contact' is false
        if (!$validatedData['is_participant_best_contact']) {
            $participant->participantContact()->updateOrCreate(
                ['participant_id' => $participant->id], // Find by participant_id
                [
                    'full_name' => $validatedData['contact_full_name'] ?? null,
                    'relationship_to_participant' => $validatedData['contact_relationship'] ?? null,
                    'organisation' => $validatedData['contact_organisation'] ?? null,
                    'phone_number' => $validatedData['contact_phone'] ?? null,
                    'email_address' => $validatedData['contact_email'] ?? null,
                    'preferred_method_of_contact' => $validatedData['contact_method'] ?? null,
                    'consent_to_speak_on_behalf' => $validatedData['contact_consent'] ?? null,
                ]
            );
        } else {
            // If is_participant_best_contact is true, remove any existing contact person details
            $participant->participantContact()->delete();
        }

        return redirect()->route('sc.participants.profile.basic-details', $participant->id)
                         ->with('success', 'Basic details updated successfully!');
    }




    public function showNdisDetails(Participant $participant)
    {
        // Optional: Ensure the support coordinator has permission to view this participant
        // For instance, check if $participant->added_by_user_id === Auth::id()
        // or if they are otherwise associated with this participant.

        // For a support coordinator, you might not display a "profile completion percentage"
        // in the same way as for the individual participant, as their view is more administrative.
        // However, if you want to reuse the logic, you can.
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant); // Reuse if calculateProfileCompletion is available/relevant

        return view('supcoor.participants.create.ndis-support-needs', compact('participant', 'profileCompletionPercentage'));
    }

    // Example: Update NDIS Details for a SPECIFIC participant
    public function updateNdisDetails(Request $request, Participant $participant): RedirectResponse
    {
        // Optional: Authorization check here as well

        $validated = $request->validate([
            'sil_funding_status' => 'nullable|in:Yes,No,Not sure',
            'ndis_plan_review_date' => 'nullable|date',
            'ndis_plan_manager' => 'nullable|in:Self-managed,Plan-managed,NDIA-managed,Not sure',
            'has_support_coordinator' => 'nullable|boolean',
            'daily_living_support_needs' => 'nullable|array',
            'daily_living_support_needs.*' => 'string|max:255',
            'daily_living_support_needs_other' => 'nullable|string',
            'primary_disability' => 'nullable|string|max:255',
            'secondary_disability' => 'nullable|string|max:255',
            'estimated_support_hours_sil_level' => 'nullable|string|max:50',
            'night_support_type' => 'nullable|in:Active overnight,Sleepover,None',
            'uses_assistive_technology_mobility_aids' => 'nullable|in:0,1',
            'assistive_technology_mobility_aids_list' => 'nullable|string',
        ]);

        $validated['has_support_coordinator'] = $request->has('has_support_coordinator');
        $validated['uses_assistive_technology_mobility_aids'] = $validated['uses_assistive_technology_mobility_aids'] == '1';

        $processedDailyLivingNeeds = $validated['daily_living_support_needs'] ?? [];
        if (in_array('Other', $processedDailyLivingNeeds) && !empty($validated['daily_living_support_needs_other'])) {
            $processedDailyLivingNeeds = array_diff($processedDailyLivingNeeds, ['Other']);
            $processedDailyLivingNeeds[] = $validated['daily_living_support_needs_other'];
        }
        $participant->daily_living_support_needs = json_encode(array_values(array_unique(array_filter($processedDailyLivingNeeds))));
        unset($validated['daily_living_support_needs_other']);

        if (!$validated['uses_assistive_technology_mobility_aids']) {
            $validated['assistive_technology_mobility_aids_list'] = null;
        }

        unset($validated['daily_living_support_needs']); // This line should be inside the original controller if it's there.

        $participant->update($validated);

        return redirect()->route('sc.participants.profile.ndis-support-needs', $participant->id) // Redirect to the participant's NDIS page
                         ->with('success', 'NDIS details updated successfully for ' . $participant->first_name . '!');
    }

    // You would replicate this pattern for Health & Safety, Living Preferences, etc.
    // For example:
    public function showHealthSafety(Participant $participant)
    {
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.health-safety', compact('participant', 'profileCompletionPercentage'));
    }

    public function updateHealthSafety(Request $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'medical_conditions_relevant' => 'nullable|string',
            'medication_administration_help' => 'nullable|in:Yes,No,Sometimes',
            'behaviour_support_plan_status' => 'nullable|in:Yes,No,In development',
            'behaviours_of_concern_housemates' => 'nullable|string|max:1000|required_if:behaviour_support_plan_status,Yes',
        ]);

        if (($validated['behaviour_support_plan_status'] ?? null) !== 'Yes') {
            $validated['behaviours_of_concern_housemates'] = null;
        }

        $participant->update($validated);

        return redirect()->route('sc.participants.profile.health-safety', $participant->id)
                         ->with('success', 'Health and safety information updated successfully for ' . $participant->first_name . '!');
    }

    /**
     * Show Health & Safety page.
     */
    public function healthSafety(Participant $participant)
    {
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.health-safety', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Show Living Preferences page.
     */
    public function showLivingPreferences (Participant $participant)
    {
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.living-preferences', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update Living Preferences.
     */
    public function updateLivingPreferences(Request $request, Participant $participant)
    {

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
            $participant->preferred_sil_locations = (array_values($filteredLocations));
        } else {
            $participant->preferred_sil_locations = [];
        }
        unset($validated['preferred_sil_locations']); // Remove after processing

        // The remaining validated fields can be updated directly
        $participant->update($validated);

        return redirect()->route('sc.participants.profile.living-preferences', $participant->id)
            ->with('success', 'Living preferences updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Compatibility & Personality page.
     */
    public function showCompatibilityPersonality (Participant $participant)
    {
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.compatibility-personality', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update Compatibility & Personality.
     */
    public function updateCompatibilityPersonality(Request $request, Participant $participant) : RedirectResponse
    {

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

        return redirect()->route('sc.participants.profile.compatibility-personality', $participant->id)
                         ->with('success', 'Compatibility & Personality updated successfully!'); // Changed 'success' to 'status'
    }

    /**
     * Show Availability page.
     */
    public function showAvailability(Participant $participant)
    {
        $profileCompletionPercentage = $this->calculateProfileCompletion($participant);
        return view('supcoor.participants.create.availability', compact('participant', 'profileCompletionPercentage'));
    }

    /**
     * Update Availability.
     */
    public function updateAvailability(Request $request, Participant $participant): RedirectResponse
    {
        
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

        return redirect()->route('sc.participants.profile.availability', $participant->id)
                         ->with('success', 'Availability updated successfully!'); // Changed 'success' to 'status'
    }

    // Re-use the helper function if needed, or place it in a trait
    public static function getDecodedField($participant, $field)
    {
        // First, check if the participant or the field itself is null/empty
        if (!$participant || !isset($participant->$field) || $participant->$field === null) {
            return [];
        }

        // If the field is already an array (due to model casting), return it directly
        if (is_array($participant->$field)) {
            return $participant->$field;
        }

        // If it's a string, attempt to decode it as JSON
        $decoded = json_decode($participant->$field, true);

        // Return the decoded array, or an empty array if decoding failed or wasn't an array
        return is_array($decoded) ? $decoded : [];
    }

    // If you need calculateProfileCompletion, you'd include it or a modified version
    // that might not set user->profile_completed directly as that's for the individual user.
    // The previous calculateProfileCompletion method is suitable as it is just a calculation.
    public function calculateProfileCompletion(Participant $participant): int
    {
        $totalSections = 6;
        $completedSections = 0;

        if (
            !empty($participant->first_name) &&
            !empty($participant->last_name) &&
            (!empty($participant->participant_email) || !empty($participant->participant_phone)) &&
            !empty($participant->date_of_birth) &&
            !empty($participant->gender_identity)
        ) {
            $completedSections++;
        }

        if (
            !empty($participant->sil_funding_status) &&
            !empty($participant->primary_disability) &&
            !empty($participant->ndis_plan_manager) &&
            !empty($participant->estimated_support_hours_sil_level) &&
            !empty($participant->night_support_type)
        ) {
            $completedSections++;
        }

        if (
            !empty($participant->medical_conditions_relevant) &&
            !empty($participant->medication_administration_help) &&
            !empty($participant->behaviour_support_plan_status)
        ) {
            $completedSections++;
        }

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

        $selfDescription = self::getDecodedField($participant, 'self_description');
        if (
            !empty($selfDescription) &&
            isset($participant->smokes) && is_bool($participant->smokes) &&
            !empty($participant->deal_breakers_housemates) &&
            !empty($participant->cultural_religious_practices) &&
            !empty($participant->interests_hobbies)
        ) {
            $completedSections++;
        }

        if (
            !empty($participant->move_in_availability) &&
            !empty($participant->current_living_situation) &&
            isset($participant->contact_for_suitable_match) && is_bool($participant->contact_for_suitable_match) &&
            !empty($participant->preferred_contact_method_match)
        ) {
            $completedSections++;
        }

        if ($totalSections === 0) {
            return 0;
        }

        return (int) round(($completedSections / $totalSections) * 100);
    }
}
