<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Import Str facade for code generation
use Illuminate\Validation\ValidationException; // Import ValidationException
use Illuminate\Support\Facades\Storage; // Import Storage facade for file deletion

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
        // Ensure these counts are specific to the current coordinator's managed participants
        $totalParticipants = $coordinator->participantManaged()->count();
        $participantsWithoutAccommodation = $coordinator->participantManaged()->where('has_accommodation', false)->count();
        $participantsLookingForAccommodation = $coordinator->participantManaged()->where('is_looking_hm', true)->count();

        // Chart Data: Participants With and Without Accommodations (for current coordinator)
        $accommodationCounts = $coordinator->participantManaged()
            ->select(
                DB::raw("SUM(CASE WHEN has_accommodation = 1 THEN 1 ELSE 0 END) as with_accommodation"),
                DB::raw("SUM(CASE WHEN has_accommodation = 0 THEN 1 ELSE 0 END) as without_accommodation")
            )
            ->first();

        // Chart Data: Participants Per State (for current coordinator)
        $participantsPerState = $coordinator->participantManaged()
            ->select('state', DB::raw('count(*) as total'))
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->get();

        // Chart Data: Participants Per Suburb (Top 10, for current coordinator)
        $participantsPerSuburb = $coordinator->participantManaged()
            ->select('suburb', DB::raw('count(*) as total'))
            ->groupBy('suburb')
            ->orderBy('total', 'desc')
            ->limit(10) // Limit to top 10 for readability
            ->get();

        // Chart Data: Participants Per Accommodation Needs (Top 10 current type, for current coordinator)
        $participantsPerAccommodationType = $coordinator->participantManaged()
            ->select('accommodation_type', DB::raw('count(*) as total'))
            ->whereNotNull('accommodation_type')
            ->where('accommodation_type', '!=', '')
            ->groupBy('accommodation_type')
            ->orderBy('total', 'desc')
            ->limit(10) // Limit to top 10
            ->get();

        // Chart Data: Participants Per Disability (for current coordinator)
        // This assumes disability_type is stored as JSON array or comma-separated string
        $allDisabilities = $coordinator->participantManaged()->pluck('disability_type')->filter()->flatMap(function ($disabilitiesArray) {
            // Because of model casting, $disabilitiesArray is ALREADY a PHP array.
            // No need for json_decode() or explode().
            return $disabilitiesArray;
        })->map(fn($item) => trim($item))->filter()->toArray();

        $disabilityCounts = array_count_values($allDisabilities);
        arsort($disabilityCounts); // Sort by count, descending
        $topDisabilities = array_slice($disabilityCounts, 0, 10, true); // Get top 10

        return view('supcoor.dashboard', compact(
            'totalParticipants',
            'participantsWithoutAccommodation',
            'participantsLookingForAccommodation',
            'accommodationCounts',
            'participantsPerState',
            'participantsPerSuburb',
            'participantsPerAccommodationType',
            'topDisabilities'
        ));
    }

    /**
     * Display the support coordinator dashboard with a list of participants.
     */
    public function listParticipants(Request $request)
    {
        $coordinator = Auth::user();

        // Start with the participants managed by the current coordinator
        $query = $coordinator->participantManaged();

        // --- Search and Filter Logic ---

        // Search by name (first, last, or middle) or specific disability
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('middle_name', 'like', '%' . $search . '%')
                  ->orWhere('specific_disability', 'like', '%' . $search . '%');
            });
        }

        // Filter by Disability Type
        if ($request->filled('disability_type')) {
            $disabilityType = $request->input('disability_type');
            // Assuming disability_type is stored as a JSON array or comma-separated string
            // For JSON array:
            $query->whereRaw('JSON_CONTAINS(disability_type, ?)', [json_encode($disabilityType)]);
            // If it's stored as a comma-separated string, you'd use:
            // $query->where('disability_type', 'like', '%' . $disabilityType . '%');
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

        $participants = $query->paginate(10); // Paginate the results

        // For the filter dropdowns:
        $disabilityTypes = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];

        // Fetch suburbs for the current selected state for the filter dropdown
        $suburbsForFilter = [];
        if ($request->filled('state')) {
            $suburbsForFilter = DB::table('participants')
                                  ->where('state', $request->input('state'))
                                  ->distinct()
                                  ->orderBy('suburb')
                                  ->pluck('suburb')
                                  ->toArray();
        }


        return view('supcoor.participants.index', compact('participants', 'disabilityTypes', 'suburbsForFilter'));
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

        // This assumes 'accommodation_needed' is the field storing the array of required accommodation types.
        // If your database field is actually `accommodation_type` and it stores a single string,
        // change `accommodation_needed` to `accommodation_type` below.
        if ($request->filled('accommodation_type')) {
            $query->whereJsonContains('accommodation_needed', $request->accommodation_type);
        }

        if ($request->filled('disability_type')) {
            $query->whereJsonContains('disability_type', $request->disability_type);
        }

        // Search by Participant Code Name or text within Disability Type
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code_name', 'like', '%' . $searchTerm . '%')
                  ->orWhereJsonContains('disability_type', $searchTerm);
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


        // Predefined list for accommodation types (assuming they are fixed options)
        $accommodationTypes = [
            'Supported Independent Living (SIL)',
            'Improved Livability',
            'Fully Accessible',
            'High Physical Support',
            'Robust',
            'Community Participation'
            // Add any other types your system might have for "accommodation_needed"
        ];

        // Dynamically collect ALL unique disability types from unassigned participants
        $allDisabilities = Participant::withoutSupportCoordinator()
                            ->pluck('disability_type')
                            ->filter()
                            ->flatMap(function ($disabilities) {
                                return $disabilities; // Already an array due to model casting
                            })
                            ->unique()
                            ->sort()
                            ->values()
                            ->toArray();


        return view('supcoor.unassigned_participants', [
            'participants' => $participants,
            'states' => $states,
            'suburbs' => $suburbs,
            'accommodationTypes' => $accommodationTypes, // Predefined list of options
            'disabilityTypes' => $allDisabilities,      // Dynamically collected unique types
            'filters' => $request->all(),
        ]);
    }

    /**
     * Handle sending a message to a participant.
     */
    public function sendMessage(Request $request, Participant $participant)
    {
        // Important: Ensure the support coordinator is not messaging an *assigned* participant unless allowed.
        // For unassigned, no check needed here specific to the coordinator's managed list.
        // If you want to prevent sending message to participants that already have a support coordinator,
        // you might add:
        // if ($participant->support_coordinator_id !== null) {
        //    return response()->json(['message' => 'Participant is already assigned to a coordinator.'], 403);
        // }


        $request->validate([
            'message_subject' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);

        // --- Messaging Logic Placeholder ---
        // This is where you integrate your actual messaging system.
        // Example with Laravel Notifications (you'd create a ParticipantMessage notification):
        // $coordinator = Auth::user();
        // $participant->notify(new \App\Notifications\ParticipantMessage($request->message_subject, $request->message_body, $coordinator->id));

        // Example with a messages table:
        DB::table('messages')->insert([
            'sender_id' => Auth::id(), // ID of the logged-in Support Coordinator
            'recipient_id' => $participant->id,
            'subject' => $request->message_subject,
            'body' => $request->message_body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Make sure you have a 'messages' table with sender_id, recipient_id, subject, body columns.
        // You might want to add polymorphic relations if User and Participant can both be senders/recipients.
        // Or simply add a type column (e.g., sender_type = 'coordinator', recipient_type = 'participant').
        // For simplicity, assuming direct user_id to participant_id.
        // --- End Messaging Logic Placeholder ---

        return response()->json(['message' => 'Message sent successfully!'], 200);
    }


    /**
     * Show the form for adding a new participant.
     */
    public function createParticipant()
    {
        // For 'disability_type', you might want to pass options to the view
        $disabilityTypes = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $accommodationTypes = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];
        return view('supcoor.participants.create', compact('disabilityTypes', 'accommodationTypes'));
    }

    /**
     * Store a newly created participant in storage.
     */
    public function storeParticipant(Request $request)
    {
        $coordinator = Auth::user();

        $rules = [
            // Core Participant Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],

            // Disability and Accommodation Details
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string', 'max:255'], // Validate each item in the array
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'approved_accommodation_type' => ['nullable', Rule::in(['SDA', 'SIL'])],
            'behavior_of_concern' => ['nullable', 'string', 'max:1000'],

            // Address Details
            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],

            // Funding and Looking Status
            'is_looking_hm' => ['boolean'],
            'has_accommodation' => ['boolean'],
            'funding_amount_support_coor' => ['nullable', 'numeric', 'min:0'],
            'funding_amount_accommodation' => ['nullable', 'numeric', 'min:0'],

            // Health Report / Assessment
            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'], // This is the column for the text
            // 'assessment_path' is not in the blade, assuming it's removed or not yet implemented
            // 'assessment_path' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
        ];

        // Custom validation for 'Other' disability type
        if ($request->filled('disability_type') && in_array('Other', $request->input('disability_type'))) {
            $rules['specific_disability'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes
        $validatedData['is_looking_hm'] = $request->has('is_looking_hm');
        $validatedData['has_accommodation'] = $request->has('has_accommodation');

        // Assign who added the participant
        $validatedData['added_by_user_id'] = $coordinator->id;
        $validatedData['support_coordinator_id'] = $coordinator->id; // Link to the current coordinator

        // Initialize health report path to null (if no file is uploaded)
        $validatedData['health_report_path'] = null; // Default to null, will be overridden if file is present

        // Handle health_report_file upload
        if ($request->hasFile('health_report_file')) {
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        }

        // The 'health_report_text' from the request is directly passed to the corresponding column.
        // It's already in $validatedData because it was in the $rules array.
        // No need for a separate `if ($request->filled('health_report_text'))` check for storing to $validatedData,
        // as `validate()` already handles populating it, and it will be null if not provided.

        // Handle assessment_path if it's uploaded (if you re-add this to the blade)
        // if ($request->hasFile('assessment_path')) {
        //     $assessmentFilePath = $request->file('assessment_path')->store('assessments', 'public');
        //     $validatedData['assessment_path'] = $assessmentFilePath;
        // } else {
        //     $validatedData['assessment_path'] = null;
        // }


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
        // Ensure the participant belongs to the logged-in coordinator
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
        // Ensure the participant belongs to the logged-in coordinator
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $disabilityTypes = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $accommodationTypes = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];

        return view('supcoor.participants.edit', compact('participant', 'disabilityTypes', 'accommodationTypes'));
    }

    /**
     * Update the specified participant in storage.
     */
    public function updateParticipant(Request $request, Participant $participant)
    {
        // Ensure the participant belongs to the logged-in coordinator
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string', 'max:255'],
            'specific_disability' => ['nullable', 'string', 'max:1000'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'approved_accommodation_type' => ['nullable', Rule::in(['SDA', 'SIL'])],
            'behavior_of_concern' => ['nullable', 'string', 'max:1000'],
            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],
            'is_looking_hm' => ['boolean'],
            'has_accommodation' => ['boolean'],
            'funding_amount_support_coor' => ['nullable', 'numeric', 'min:0'],
            'funding_amount_accommodation' => ['nullable', 'numeric', 'min:0'],
            // Health Report / Assessment
            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'], // This is the column for the text
        ];

        // Custom validation for 'Other' disability type
        if ($request->filled('disability_type') && in_array('Other', $request->input('disability_type'))) {
            $rules['specific_disability'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes
        $validatedData['is_looking_hm'] = $request->has('is_looking_hm');
        $validatedData['has_accommodation'] = $request->has('has_accommodation');

        // Handle health_report_file upload and existing file deletion
        if ($request->hasFile('health_report_file')) {
            // Delete old file if it exists
            if ($participant->health_report_path) {
                Storage::disk('public')->delete($participant->health_report_path);
            }
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        } else {
            // If no new file is uploaded, retain the existing file path.
            // This ensures the health_report_path isn't nullified unless a new file is provided.
            // If you wanted to allow clearing the file without uploading a new one,
            // you'd need a specific checkbox for "Clear File".
            unset($validatedData['health_report_path']); // Unset so it's not included in update, keeping existing value
        }


        // The 'health_report_text' from the request is directly passed to the corresponding column.
        // It's already in $validatedData because it was in the $rules array.
        // It will be null if the field was empty in the request.
        // No explicit line needed here like `$validatedData['health_report_text'] = $request->input('health_report_text');`
        // as it's already present in $validatedData from the `validate()` call.

        $participant->update($validatedData);

        return redirect()->route('sc.participants.list')->with('success', 'Participant updated successfully!');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroyParticipant(Participant $participant)
    {
        // Ensure the participant belongs to the logged-in coordinator
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated health report file if it exists
        if ($participant->health_report_path) {
            Storage::disk('public')->delete($participant->health_report_path);
        }
        // If there are other files like 'assessment_path', delete them too
        // if ($participant->assessment_path) {
        //     Storage::disk('public')->delete($participant->assessment_path);
        // }

        $participant->delete();

        return redirect()->route('sc.participants.list')->with('success', 'Participant deleted successfully!');
    }
}