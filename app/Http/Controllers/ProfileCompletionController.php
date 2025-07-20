<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\SupportCoordinator;
use App\Models\Provider;
use App\Models\User;
use App\Models\Relative; // Make sure this is imported if you're creating Relative in this flow
use App\Models\NdisBusiness; // If you plan to create NDIS Business in this flow
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller; // Required for $this->middleware()
use Illuminate\Support\Facades\Log; // Required for Log::error()

class ProfileCompletionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function complete(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_completed) {
            return redirect()->route('dashboard')->with('status', 'Your profile is already complete!');
        }

        $rules = [];
        $data = $request->all(); // Consider only specific fields from request

        switch ($user->role) {
            case 'participant':
                $rules = [
                    'first_name' => ['required', 'string', 'max:255'],
                    'middle_name' => ['nullable', 'string', 'max:255'],
                    'last_name' => ['required', 'string', 'max:255'],
                    'birthday' => ['required', 'date'],
                    'disability_type' => ['required', 'string', 'max:255'],
                    'specific_disability' => ['nullable', 'string'],
                    'accommodation_type' => ['required', 'string', 'max:255'],
                    'street_address' => ['nullable', 'string', 'max:255'],
                    'suburb' => ['nullable', 'string', 'max:255'],
                    'state' => ['nullable', 'string', 'max:255'],
                    'post_code' => ['nullable', 'string', 'max:20'],
                    'is_looking_hm' => ['required', 'boolean'],
                    'has_accommodation' => ['required', 'boolean'],
                    // For Relative: If you're saving it via the participant form
                    'relative_name' => ['nullable', 'string', 'max:255'],
                    'relationship_to_participant' => ['nullable', 'string', 'max:255'],
                    'relative_phone' => ['nullable', 'string', 'max:20'],
                    'relative_email' => ['nullable', 'email', 'max:255'],
                    // Support Coordinator ID: if this is selected during profile completion
                    'support_coordinator_id' => ['nullable', 'exists:support_coordinators,id'],
                    'participant_code_name' => ['required', 'string', 'unique:participants,participant_code_name'],
                ];
                break;

            case 'coordinator':
                $rules = [
                    'first_name' => ['required', 'string', 'max:255'],
                    'middle_name' => ['nullable', 'string', 'max:255'],
                    'last_name' => ['required', 'string', 'max:255'],
                    'ndis_business_id' => ['required', 'exists:ndis_businesses,id'],
                    'sup_coor_code_name' => ['required', 'string', 'unique:support_coordinators,sup_coor_code_name'],
                    'sup_coor_image' => ['nullable', 'image', 'max:2048'], // For image upload
                    // Status and verification_notes are usually set by admin, not user
                ];
                break;

            case 'provider':
                $rules = [
                    'company_name' => ['required', 'string', 'max:255'],
                    'abn' => ['required', 'string', 'digits:11', 'unique:providers,abn'],
                    'plan' => ['required', 'string', 'in:basic,standard,advanced'], // Example plans
                    'provider_code_name' => ['required', 'string', 'unique:providers,provider_code_name'],
                    'provider_logo' => ['nullable', 'image', 'max:2048'], // For logo upload
                    'contact_email' => ['required', 'email', 'max:255'],
                    'contact_phone' => ['nullable', 'string', 'max:20'],
                    'address' => ['nullable', 'string', 'max:255'],
                    'suburb' => ['nullable', 'string', 'max:255'],
                    'state' => ['nullable', 'string', 'max:255'],
                    'post_code' => ['nullable', 'string', 'max:20'],
                ];
                break;

            default:
                // Minimal validation for unexpected roles
                break;
        }

        $request->validate($rules);

        try {
            switch ($user->role) {
                case 'participant':
                    $participantData = [
                        'first_name' => $request->first_name,
                        'middle_name' => $request->middle_name,
                        'last_name' => $request->last_name,
                        'birthday' => $request->birthday,
                        'disability_type' => $request->disability_type,
                        'specific_disability' => $request->specific_disability,
                        'accommodation_type' => $request->accommodation_type,
                        'street_address' => $request->street_address,
                        'suburb' => $request->suburb,
                        'state' => $request->state,
                        'post_code' => $request->post_code,
                        'is_looking_hm' => $request->boolean('is_looking_hm'), // Cast boolean from request
                        'has_accommodation' => $request->boolean('has_accommodation'), // Cast boolean from request
                        'support_coordinator_id' => $request->support_coordinator_id,
                        'participant_code_name' => $request->participant_code_name,
                        'added_by_user_id' => $user->id, // The user completing the profile is the one who "added" it
                    ];

                    $participant = Participant::updateOrCreate(
                        ['user_id' => $user->id],
                        $participantData
                    );

                    // If you're using a dedicated Relative model and want to create/update it here
                    if ($request->filled('relative_name')) {
                        Relative::updateOrCreate(
                            ['participant_id' => $participant->id],
                            [
                                'name' => $request->relative_name,
                                'relationship_to_participant' => $request->relationship_to_participant,
                                'phone' => $request->relative_phone,
                                'email' => $request->relative_email,
                            ]
                        );
                    }
                    break;

                case 'coordinator':
                    $coordinatorData = [
                        'first_name' => $request->first_name,
                        'middle_name' => $request->middle_name,
                        'last_name' => $request->last_name,
                        'ndis_business_id' => $request->ndis_business_id,
                        'sup_coor_code_name' => $request->sup_coor_code_name,
                        // status and verification_notes are usually managed by admin after submission
                        // For image upload, you'd add logic here
                    ];

                    // Handle sup_coor_image upload
                    if ($request->hasFile('sup_coor_image')) {
                        $imagePath = $request->file('sup_coor_image')->store('coordinator_images', 'public');
                        $coordinatorData['sup_coor_image'] = $imagePath;
                    }

                    SupportCoordinator::updateOrCreate(
                        ['user_id' => $user->id],
                        $coordinatorData
                    );
                    break;

                case 'provider':
                    $providerData = [
                        'company_name' => $request->company_name,
                        'abn' => $request->abn,
                        'plan' => $request->plan,
                        'provider_code_name' => $request->provider_code_name,
                        'contact_email' => $request->contact_email,
                        'contact_phone' => $request->contact_phone,
                        'address' => $request->address,
                        'suburb' => $request->suburb,
                        'state' => $request->state,
                        'post_code' => $request->post_code,
                    ];

                    // Handle provider_logo upload
                    if ($request->hasFile('provider_logo')) {
                        $logoPath = $request->file('provider_logo')->store('provider_logos', 'public');
                        $providerData['provider_logo'] = $logoPath;
                    }

                    Provider::updateOrCreate(
                        ['user_id' => $user->id],
                        $providerData
                    );
                    break;
            }

            // Mark user profile as completed
            $user->profile_completed = true;
            dd($user);
            $user->save(); // This is the save() method you mentioned. It should work fine on the User model.

            return redirect()->route('dashboard')->with('status', 'Profile completed successfully!');

        } catch (\Exception $e) {
            Log::error('Profile completion failed for user ' . $user->id . ': ' . $e->getMessage());
            // You might want to also log the stack trace for more detailed debugging
            // Log::error('Stack trace: ' . $e->getTraceAsString());

            throw ValidationException::withMessages([
                'profile_completion_error' => ['There was an issue completing your profile. Please try again.'],
            ])->redirectTo(route('dashboard'));
        }
    }
}