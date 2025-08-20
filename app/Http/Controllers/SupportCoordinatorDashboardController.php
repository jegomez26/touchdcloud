<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class SupportCoordinatorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:coordinator']);
    }

    public function index()
    {
        $coordinator = Auth::user();

        // Quick Links Data (Counts)
        $totalParticipants = $coordinator->participantsAdded()->count();

        // # of no current accommodation (where current living situation is not SIL or SDA accommodation)
        $noCurrentSilSdaAccommodation = $coordinator->participantsAdded()
            ->where('current_living_situation', '!=', 'SIL or SDA accommodation')
            ->count();
        
        $participantsLookingForAccommodation = $coordinator->participantsAdded()->where('move_in_availability', '!=', 'Just exploring options')->count(); // Using schema field for looking for accommodation

        // Chart Data: Participants Per State (for current coordinator)
        $participantsPerState = $coordinator->participantsAdded()
            ->select('state', DB::raw('count(*) as total'))
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->get();

        // Chart Data: Participants Per Suburb (Top 10, for current coordinator)
        $participantsPerSuburb = $coordinator->participantsAdded()
            ->select('suburb', DB::raw('count(*) as total'))
            ->whereNotNull('suburb')
            ->where('suburb', '!=', '')
            ->groupBy('suburb')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Chart Data: Participants Per Primary Disability (for current coordinator)
        $participantsPerPrimaryDisability = $coordinator->participantsAdded()
            ->select('primary_disability', DB::raw('count(*) as total'))
            ->whereNotNull('primary_disability')
            ->where('primary_disability', '!=', '')
            ->groupBy('primary_disability')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Chart Data: Participants Per Age Range (for current coordinator)
        $participantsPerAgeRange = $coordinator->participantsAdded()
            ->select(
                DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 25 THEN 1 ELSE 0 END) as age_18_25"),
                DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 26 AND 35 THEN 1 ELSE 0 END) as age_26_35"),
                DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN 1 ELSE 0 END) as age_36_50"),
                DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 50 THEN 1 ELSE 0 END) as age_51_plus"),
                DB::raw("SUM(CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END) as age_unknown")
            )
            ->first();

        // Chart Data: Participants Per Gender (for current coordinator)
        $participantsPerGender = $coordinator->participantsAdded()
            ->select('gender_identity', DB::raw('count(*) as total'))
            ->whereNotNull('gender_identity')
            ->where('gender_identity', '!=', '')
            ->groupBy('gender_identity')
            ->orderBy('total', 'desc')
            ->get();

        return view('supcoor.dashboard', compact(
            'totalParticipants',
            'noCurrentSilSdaAccommodation',
            'participantsLookingForAccommodation',
            'participantsPerState',
            'participantsPerSuburb',
            'participantsPerPrimaryDisability',
            'participantsPerAgeRange',
            'participantsPerGender'
        ));
    }

    /**
     * Display the support coordinator dashboard with a list of participants.
     */
    public function listParticipants(Request $request)
    {
        $coordinator = Auth::user();

        // Start with the participants managed by the current coordinator
        $query = $coordinator->participantsAdded();

        // --- Search and Filter Logic ---

        // Search by name (first, last, or middle) or specific disability
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_name', 'like', '%' . $search . '%')
                    ->orWhere('specific_disability', 'like', '%' . $search . '%')
                    ->orWhere('primary_disability', 'like', '%' . $search . '%')
                    ->orWhere('secondary_disability', 'like', '%' . $search . '%');
            });
        }

        // Filter by Primary Disability
        if ($request->filled('primary_disability')) {
            $query->where('primary_disability', $request->input('primary_disability'));
        }

        // Filter by State
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Filter by Suburb (only if state is also selected)
        if ($request->filled('suburb') && $request->filled('state')) {
            $query->where('suburb', $request->input('suburb'));
        }

        // --- End Search and Filter Logic ---

        $participants = $query->paginate(10);

        // For the filter dropdowns:
        $primaryDisabilityTypes = Participant::distinct()->pluck('primary_disability')->filter()->sort()->toArray();

        $suburbsForFilter = [];
        if ($request->filled('state')) {
            $suburbsForFilter = DB::table('participants')
                ->where('state', $request->input('state'))
                ->distinct()
                ->orderBy('suburb')
                ->pluck('suburb')
                ->toArray();
        }

        return view('supcoor.participants.index', compact('participants', 'primaryDisabilityTypes', 'suburbsForFilter'));
    }

    public function viewUnassignedParticipants(Request $request)
    {
        $query = Participant::withoutSupportCoordinator();

        // Apply filters
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('suburb')) {
            $query->where('suburb', $request->suburb);
        }

        if ($request->filled('current_living_situation')) {
            $query->where('current_living_situation', $request->current_living_situation);
        }

        if ($request->filled('primary_disability')) {
            $query->where('primary_disability', $request->primary_disability);
        }

        // Search by Participant Code Name or text within Disability Type
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('participant_code_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('primary_disability', 'like', '%' . $searchTerm . '%')
                    ->orWhere('secondary_disability', 'like', '%' . $searchTerm . '%')
                    ->orWhere('specific_disability', 'like', '%' . $searchTerm . '%');
            });
        }

        $participants = $query->paginate(10);

        // Get unique filter options from UNASSIGNED participants
        $states = Participant::withoutSupportCoordinator()->distinct()->pluck('state')->sort()->filter()->toArray();
        $suburbs = [];
        if ($request->filled('state')) {
            $suburbs = Participant::withoutSupportCoordinator()
                ->where('state', $request->input('state'))
                ->distinct()
                ->orderBy('suburb')
                ->pluck('suburb')
                ->filter()
                ->toArray();
        } else {
            $suburbs = Participant::withoutSupportCoordinator()->distinct()->pluck('suburb')->sort()->filter()->toArray();
        }

        // Predefined list for current living situation types
        $currentLivingSituations = [
            'SIL or SDA accommodation',
            'Group home',
            'With family',
            'Living alone',
            'Other'
        ];

        // Dynamically collect ALL unique primary disability types from unassigned participants
        $primaryDisabilityTypes = Participant::withoutSupportCoordinator()
            ->distinct()
            ->pluck('primary_disability')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return view('supcoor.unassigned_participants', [
            'participants' => $participants,
            'states' => $states,
            'suburbs' => $suburbs,
            'currentLivingSituations' => $currentLivingSituations,
            'primaryDisabilityTypes' => $primaryDisabilityTypes,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Handle sending a message to a participant.
     */
    public function sendMessage(Request $request, Participant $participant)
    {
        $request->validate([
            'message_subject' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);

        DB::table('messages')->insert([
            'sender_id' => Auth::id(),
            'recipient_id' => $participant->id,
            'subject' => $request->message_subject,
            'body' => $request->message_body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Message sent successfully!'], 200);
    }


    /**
     * Show the form for adding a new participant.
     */
    public function createParticipant()
    {
        $primaryDisabilities = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $currentLivingSituations = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];
        $genderIdentities = ['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'];
        $ndisPlanManagers = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
        $silFundingStatuses = ['Yes', 'No', 'Not sure'];
        $contactMethods = ['Phone', 'Email', 'Either'];
        $preferredHousemateNumbers = ['1', '2', '3+', 'No preference'];
        $accessibilityNeeds = ['Fully accessible', 'Some modifications required', 'No specific needs'];
        $petPreferences = ['Have pets', 'Can live with pets', 'Do not want to live with pets'];
        $moveInAvailabilities = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];
        $aboriginalTorresStraitIslanderOptions = ['Yes', 'No', 'Prefer not to say'];
        $medicationAdminHelpOptions = ['Yes', 'No', 'Sometimes'];
        $behaviourSupportPlanStatuses = ['Yes', 'No', 'In development'];
        $preferredContactMatchMethods = ['Phone', 'Email', 'Via support coordinator', 'Other'];

        // Assuming fixed options for other JSON fields for UI selection.
        // In a real application, you might load these from a config or database.
        $pronounOptions = ['She / Her', 'He / Him', 'They / Them', 'Other'];
        $dailyLivingSupportNeedsOptions = [
            'Personal care', 'Medication management', 'Meal preparation',
            'Household tasks', 'Community access', 'Transport', 'Financial management', 'Other'
        ];
        $housematePreferencesOptions = ['Male', 'Female', 'Mixed', 'No preference', 'Other'];
        $goodHomeEnvironmentLooksLikeOptions = [
            'Quiet', 'Social', 'Organized', 'Relaxed', 'Structured', 'Independent', 'Supportive', 'Other'
        ];
        $selfDescriptionOptions = [
            'Quiet', 'Social', 'Independent', 'Needs support', 'Organized', 'Relaxed', 'Active', 'Creative', 'Other'
        ];


        return view('supcoor.participants.create', compact(
            'primaryDisabilities', 'currentLivingSituations', 'genderIdentities',
            'ndisPlanManagers', 'silFundingStatuses', 'contactMethods',
            'preferredHousemateNumbers', 'accessibilityNeeds', 'petPreferences',
            'moveInAvailabilities', 'aboriginalTorresStraitIslanderOptions',
            'medicationAdminHelpOptions', 'behaviourSupportPlanStatuses',
            'preferredContactMatchMethods', 'pronounOptions', 'dailyLivingSupportNeedsOptions',
            'housematePreferencesOptions', 'goodHomeEnvironmentLooksLikeOptions', 'selfDescriptionOptions'
        ));
    }

    /**
     * Store a newly created participant in storage.
     */
    public function storeParticipant(Request $request)
    {
        $coordinator = Auth::user();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'participant_email' => ['nullable', 'email', 'max:255'],
            'participant_phone' => ['nullable', 'string', 'max:255'],
            'participant_contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'is_participant_best_contact' => ['boolean'],

            'date_of_birth' => ['nullable', 'date'],
            'gender_identity' => ['nullable', Rule::in(['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'])],
            'gender_identity_other' => ['nullable', 'string', 'max:255'],
            'pronouns' => ['nullable', 'array'],
            'pronouns_other' => ['nullable', 'string', 'max:255'],
            'languages_spoken' => ['nullable', 'array'],
            'aboriginal_torres_strait_islander' => ['nullable', Rule::in(['Yes', 'No', 'Prefer not to say'])],

            'sil_funding_status' => ['nullable', Rule::in(['Yes', 'No', 'Not sure'])],
            'ndis_plan_review_date' => ['nullable', 'date'],
            'ndis_plan_manager' => ['nullable', Rule::in(['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'])],
            'has_support_coordinator' => ['boolean'],

            'daily_living_support_needs' => ['nullable', 'array'],
            'daily_living_support_needs_other' => ['nullable', 'string', 'max:1000'],
            'primary_disability' => ['nullable', 'string', 'max:255'],
            'secondary_disability' => ['nullable', 'string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'estimated_support_hours_sil_level' => ['nullable', 'string', 'max:255'],
            'night_support_type' => ['nullable', Rule::in(['Active overnight', 'Sleepover', 'None'])],
            'uses_assistive_technology_mobility_aids' => ['boolean'],
            'assistive_technology_mobility_aids_list' => ['nullable', 'string', 'max:1000'],

            'medical_conditions_relevant' => ['nullable', 'string', 'max:1000'],
            'medication_administration_help' => ['nullable', Rule::in(['Yes', 'No', 'Sometimes'])],
            'behaviour_support_plan_status' => ['nullable', Rule::in(['Yes', 'No', 'In development'])],
            'behaviours_of_concern_housemates' => ['nullable', 'string', 'max:1000'],

            'preferred_sil_locations' => ['nullable', 'array'],
            'housemate_preferences' => ['nullable', 'array'],
            'housemate_preferences_other' => ['nullable', 'string', 'max:1000'],
            'preferred_number_of_housemates' => ['nullable', Rule::in(['1', '2', '3+', 'No preference'])],
            'accessibility_needs_in_home' => ['nullable', Rule::in(['Fully accessible', 'Some modifications required', 'No specific needs'])],
            'accessibility_needs_details' => ['nullable', 'string', 'max:1000'],
            'pets_in_home_preference' => ['nullable', Rule::in(['Have pets', 'Can live with pets', 'Do not want to live with pets'])],
            'own_pet_type' => ['nullable', 'string', 'max:255'],
            'good_home_environment_looks_like' => ['nullable', 'array'],
            'good_home_environment_looks_like_other' => ['nullable', 'string', 'max:1000'],

            'self_description' => ['nullable', 'array'],
            'self_description_other' => ['nullable', 'string', 'max:1000'],
            'smokes' => ['boolean'],
            'deal_breakers_housemates' => ['nullable', 'string', 'max:1000'],
            'cultural_religious_practices' => ['nullable', 'string', 'max:1000'],
            'interests_hobbies' => ['nullable', 'string', 'max:1000'],

            'move_in_availability' => ['nullable', Rule::in(['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'])],
            'current_living_situation' => ['nullable', Rule::in(['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'])],
            'current_living_situation_other' => ['nullable', 'string', 'max:1000'],
            'contact_for_suitable_match' => ['boolean'],
            'preferred_contact_method_match' => ['nullable', Rule::in(['Phone', 'Email', 'Via support coordinator', 'Other'])],
            'preferred_contact_method_match_other' => ['nullable', 'string', 'max:1000'],

            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],

            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'],
        ];

        if ($request->filled('primary_disability') && $request->input('primary_disability') === 'Other') {
            $rules['specific_disability'] = ['required', 'string', 'max:1000'];
        }
        if ($request->filled('gender_identity') && $request->input('gender_identity') === 'Other') {
            $rules['gender_identity_other'] = ['required', 'string', 'max:255'];
        }
        if ($request->filled('current_living_situation') && $request->input('current_living_situation') === 'Other') {
            $rules['current_living_situation_other'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('uses_assistive_technology_mobility_aids') && $request->input('uses_assistive_technology_mobility_aids')) {
            $rules['assistive_technology_mobility_aids_list'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('pets_in_home_preference') && $request->input('pets_in_home_preference') === 'Have pets') {
            $rules['own_pet_type'] = ['required', 'string', 'max:255'];
        }
        if ($request->has('contact_for_suitable_match') && $request->input('contact_for_suitable_match') && $request->input('preferred_contact_method_match') === 'Other') {
            $rules['preferred_contact_method_match_other'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes explicitly based on schema
        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');
        $validatedData['has_support_coordinator'] = $request->has('has_support_coordinator');
        $validatedData['uses_assistive_technology_mobility_aids'] = $request->has('uses_assistive_technology_mobility_aids');
        $validatedData['smokes'] = $request->has('smokes');
        $validatedData['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');
        
        // Handle JSON fields
        $validatedData['pronouns'] = json_encode($request->input('pronouns'));
        $validatedData['languages_spoken'] = json_encode($request->input('languages_spoken'));
        $validatedData['daily_living_support_needs'] = json_encode($request->input('daily_living_support_needs'));
        $validatedData['preferred_sil_locations'] = json_encode($request->input('preferred_sil_locations'));
        $validatedData['housemate_preferences'] = json_encode($request->input('housemate_preferences'));
        $validatedData['good_home_environment_looks_like'] = json_encode($request->input('good_home_environment_looks_like'));
        $validatedData['self_description'] = json_encode($request->input('self_description'));

        $validatedData['added_by_user_id'] = $coordinator->id;
        $validatedData['support_coordinator_id'] = $coordinator->id;

        $validatedData['health_report_path'] = null;
        if ($request->hasFile('health_report_file')) {
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        }

        $participant = Participant::create($validatedData);

        $participant->participant_code_name = 'PA' . str_pad($participant->id, 4, '0', STR_PAD_LEFT);
        $participant->save();

        return redirect()->route('sc.participants.list')->with('success', 'Participant added successfully!');
    }

    /**
     * Display the specified participant's profile.
     */
    public function showParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('supcoor.participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function editParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $primaryDisabilities = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $currentLivingSituations = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];
        $genderIdentities = ['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'];
        $ndisPlanManagers = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
        $silFundingStatuses = ['Yes', 'No', 'Not sure'];
        $contactMethods = ['Phone', 'Email', 'Either'];
        $preferredHousemateNumbers = ['1', '2', '3+', 'No preference'];
        $accessibilityNeeds = ['Fully accessible', 'Some modifications required', 'No specific needs'];
        $petPreferences = ['Have pets', 'Can live with pets', 'Do not want to live with pets'];
        $moveInAvailabilities = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];
        $aboriginalTorresStraitIslanderOptions = ['Yes', 'No', 'Prefer not to say'];
        $medicationAdminHelpOptions = ['Yes', 'No', 'Sometimes'];
        $behaviourSupportPlanStatuses = ['Yes', 'No', 'In development'];
        $preferredContactMatchMethods = ['Phone', 'Email', 'Via support coordinator', 'Other'];

        $pronounOptions = ['She / Her', 'He / Him', 'They / Them', 'Other'];
        $dailyLivingSupportNeedsOptions = [
            'Personal care', 'Medication management', 'Meal preparation',
            'Household tasks', 'Community access', 'Transport', 'Financial management', 'Other'
        ];
        $housematePreferencesOptions = ['Male', 'Female', 'Mixed', 'No preference', 'Other'];
        $goodHomeEnvironmentLooksLikeOptions = [
            'Quiet', 'Social', 'Organized', 'Relaxed', 'Structured', 'Independent', 'Supportive', 'Other'
        ];
        $selfDescriptionOptions = [
            'Quiet', 'Social', 'Independent', 'Needs support', 'Organized', 'Relaxed', 'Active', 'Creative', 'Other'
        ];

        return view('supcoor.participants.edit', compact(
            'participant', 'primaryDisabilities', 'currentLivingSituations', 'genderIdentities',
            'ndisPlanManagers', 'silFundingStatuses', 'contactMethods',
            'preferredHousemateNumbers', 'accessibilityNeeds', 'petPreferences',
            'moveInAvailabilities', 'aboriginalTorresStraitIslanderOptions',
            'medicationAdminHelpOptions', 'behaviourSupportPlanStatuses',
            'preferredContactMatchMethods', 'pronounOptions', 'dailyLivingSupportNeedsOptions',
            'housematePreferencesOptions', 'goodHomeEnvironmentLooksLikeOptions', 'selfDescriptionOptions'
        ));
    }

    /**
     * Update the specified participant in storage.
     */
    public function updateParticipant(Request $request, Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'participant_email' => ['nullable', 'email', 'max:255'],
            'participant_phone' => ['nullable', 'string', 'max:255'],
            'participant_contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'is_participant_best_contact' => ['boolean'],

            'date_of_birth' => ['nullable', 'date'],
            'gender_identity' => ['nullable', Rule::in(['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'])],
            'gender_identity_other' => ['nullable', 'string', 'max:255'],
            'pronouns' => ['nullable', 'array'],
            'pronouns_other' => ['nullable', 'string', 'max:255'],
            'languages_spoken' => ['nullable', 'array'],
            'aboriginal_torres_strait_islander' => ['nullable', Rule::in(['Yes', 'No', 'Prefer not to say'])],

            'sil_funding_status' => ['nullable', Rule::in(['Yes', 'No', 'Not sure'])],
            'ndis_plan_review_date' => ['nullable', 'date'],
            'ndis_plan_manager' => ['nullable', Rule::in(['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'])],
            'has_support_coordinator' => ['boolean'],

            'daily_living_support_needs' => ['nullable', 'array'],
            'daily_living_support_needs_other' => ['nullable', 'string', 'max:1000'],
            'primary_disability' => ['nullable', 'string', 'max:255'],
            'secondary_disability' => ['nullable', 'string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'estimated_support_hours_sil_level' => ['nullable', 'string', 'max:255'],
            'night_support_type' => ['nullable', Rule::in(['Active overnight', 'Sleepover', 'None'])],
            'uses_assistive_technology_mobility_aids' => ['boolean'],
            'assistive_technology_mobility_aids_list' => ['nullable', 'string', 'max:1000'],

            'medical_conditions_relevant' => ['nullable', 'string', 'max:1000'],
            'medication_administration_help' => ['nullable', Rule::in(['Yes', 'No', 'Sometimes'])],
            'behaviour_support_plan_status' => ['nullable', Rule::in(['Yes', 'No', 'In development'])],
            'behaviours_of_concern_housemates' => ['nullable', 'string', 'max:1000'],

            'preferred_sil_locations' => ['nullable', 'array'],
            'housemate_preferences' => ['nullable', 'array'],
            'housemate_preferences_other' => ['nullable', 'string', 'max:1000'],
            'preferred_number_of_housemates' => ['nullable', Rule::in(['1', '2', '3+', 'No preference'])],
            'accessibility_needs_in_home' => ['nullable', Rule::in(['Fully accessible', 'Some modifications required', 'No specific needs'])],
            'accessibility_needs_details' => ['nullable', 'string', 'max:1000'],
            'pets_in_home_preference' => ['nullable', Rule::in(['Have pets', 'Can live with pets', 'Do not want to live with pets'])],
            'own_pet_type' => ['nullable', 'string', 'max:255'],
            'good_home_environment_looks_like' => ['nullable', 'array'],
            'good_home_environment_looks_like_other' => ['nullable', 'string', 'max:1000'],

            'self_description' => ['nullable', 'array'],
            'self_description_other' => ['nullable', 'string', 'max:1000'],
            'smokes' => ['boolean'],
            'deal_breakers_housemates' => ['nullable', 'string', 'max:1000'],
            'cultural_religious_practices' => ['nullable', 'string', 'max:1000'],
            'interests_hobbies' => ['nullable', 'string', 'max:1000'],

            'move_in_availability' => ['nullable', Rule::in(['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'])],
            'current_living_situation' => ['nullable', Rule::in(['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'])],
            'current_living_situation_other' => ['nullable', 'string', 'max:1000'],
            'contact_for_suitable_match' => ['boolean'],
            'preferred_contact_method_match' => ['nullable', Rule::in(['Phone', 'Email', 'Via support coordinator', 'Other'])],
            'preferred_contact_method_match_other' => ['nullable', 'string', 'max:1000'],

            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],

            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'],
        ];

        if ($request->filled('primary_disability') && $request->input('primary_disability') === 'Other') {
            $rules['specific_disability'] = ['required', 'string', 'max:1000'];
        }
        if ($request->filled('gender_identity') && $request->input('gender_identity') === 'Other') {
            $rules['gender_identity_other'] = ['required', 'string', 'max:255'];
        }
        if ($request->filled('current_living_situation') && $request->input('current_living_situation') === 'Other') {
            $rules['current_living_situation_other'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('uses_assistive_technology_mobility_aids') && $request->input('uses_assistive_technology_mobility_aids')) {
            $rules['assistive_technology_mobility_aids_list'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('pets_in_home_preference') && $request->input('pets_in_home_preference') === 'Have pets') {
            $rules['own_pet_type'] = ['required', 'string', 'max:255'];
        }
        if ($request->has('contact_for_suitable_match') && $request->input('contact_for_suitable_match') && $request->input('preferred_contact_method_match') === 'Other') {
            $rules['preferred_contact_method_match_other'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes
        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');
        $validatedData['has_support_coordinator'] = $request->has('has_support_coordinator');
        $validatedData['uses_assistive_technology_mobility_aids'] = $request->has('uses_assistive_technology_mobility_aids');
        $validatedData['smokes'] = $request->has('smokes');
        $validatedData['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');

        // Handle JSON fields (encode arrays to JSON strings for storage)
        $validatedData['pronouns'] = json_encode($request->input('pronouns'));
        $validatedData['languages_spoken'] = json_encode($request->input('languages_spoken'));
        $validatedData['daily_living_support_needs'] = json_encode($request->input('daily_living_support_needs'));
        $validatedData['preferred_sil_locations'] = json_encode($request->input('preferred_sil_locations'));
        $validatedData['housemate_preferences'] = json_encode($request->input('housemate_preferences'));
        $validatedData['good_home_environment_looks_like'] = json_encode($request->input('good_home_environment_looks_like'));
        $validatedData['self_description'] = json_encode($request->input('self_description'));

        if ($request->hasFile('health_report_file')) {
            if ($participant->health_report_path) {
                Storage::disk('public')->delete($participant->health_report_path);
            }
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        } else {
            unset($validatedData['health_report_path']);
        }

        $participant->update($validatedData);

        return redirect()->route('sc.participants.list')->with('success', 'Participant updated successfully!');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroyParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($participant->health_report_path) {
            Storage::disk('public')->delete($participant->health_report_path);
        }

        $participant->delete();

        return redirect()->route('sc.participants.list')->with('success', 'Participant deleted successfully!');
    }
}