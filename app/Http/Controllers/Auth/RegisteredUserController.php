<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SupportCoordinator;
use App\Models\Provider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Added for logging exceptions

class RegisteredUserController extends Controller
{
    /**
     * Display the default registration view (for individuals/participants/representatives).
     */
    public function create(): View
    {
        return view('auth.register-individual'); // Renamed to 'register' as it's the primary general registration
    }

    /**
     * Display the registration view for a support coordinator.
     */
    public function createCoordinator(): View
    {
        return view('auth.register-coordinator');
    }

    /**
     * Display the registration view for an accommodation provider.
     */
    public function createProvider(): View
    {
        return view('auth.register-provider');
    }

    /**
     * Handle an incoming registration request for Participants (Individual or Representative).
     * This method now only creates the User account for the person signing up.
     * The detailed participant/representative profile is handled in a subsequent step.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation for the User account creator's details
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            // 'role' can now be 'participant' OR 'representative'
            'role' => ['required', 'string', Rule::in(['participant', 'representative'])],
            'terms_and_privacy' => ['accepted'],
        ]);
        // dd($request->role);
        $isRep = $request->role === 'representative' ? 1 : 0;
        // dd($isRep);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // The `role` is still `participant` because that is the broader user type.
            'role' => 'participant', 
            // The `is_representative` column is used to differentiate between a participant
            // registering for themselves and a representative registering on their behalf.
            'is_representative' => $isRep,
            'profile_completed' => false, // Set to false, indicates they need to complete their specific profile
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to a dedicated route for completing the participant/representative profile
        // This is where they will specify if they are the participant or a representative,
        // and provide all related NDIS participant details.
        return redirect()->route('dashboard');
    }

    /**
     * Handle an incoming registration request for Support Coordinators.
     */
    public function storeCoordinator(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'abn' => ['required', 'string', 'digits:11'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', 'string', Rule::in(['coordinator'])],
            'terms_and_privacy' => ['accepted'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => 'coordinator',
            'password' => Hash::make($request->password),
            'profile_completed' => false,
            // 'is_representative' is NOT stored in the 'users' table.
        ]);

        $supCoorCodeName = 'SC' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        SupportCoordinator::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'company_name' => $request->company_name,
            'abn' => $request->abn,
            'sup_coor_code_name' => $supCoorCodeName,
            'status' => 'pending_verification', // Initial status
        ]);

        Auth::login($user);

        event(new Registered($user));

        // Redirect to Laravel's default email verification notice page
        return redirect(route('verification.notice'));
    }

    /**
     * Handle an incoming registration request for Providers.
     */
    public function storeProvider(Request $request): RedirectResponse
{
        $request->validate([
            // SECTION 1: Organisation Details
            'organisation_name' => ['required', 'string', 'max:255'], // Renamed from company_name
            'abn' => ['required', 'string', 'digits:11', 'unique:providers,abn'],
            'ndis_registration_number' => ['nullable', 'string', 'max:255'], // New field
            'provider_types' => ['required', 'string', Rule::in(['SIL Provider', 'SDA Provider', 'Both'])], // New field, expects array
            'provider_types.*' => ['string'], // Validate each item in the array

            'main_contact_name' => ['required', 'string', 'max:255'], // Renamed from first_name/last_name combination
            'main_contact_last_name' => ['required', 'string', 'max:255'], // Renamed from first_name/last_name combination
            'main_contact_role_title' => ['nullable', 'string', 'max:255'], // New field
            'phone_number' => ['required', 'string', 'max:20'], // Renamed from contact_phone
            'email' => ['required', 'string', 'email', 'max:255',  'unique:' . User::class], // Renamed from email, unique to providers table
            'website' => ['nullable', 'url', 'max:255'], // New field

            'office_address' => ['nullable', 'string', 'max:255'], // Renamed from address
            'office_suburb' => ['nullable', 'string', 'max:255'], // Renamed from suburb
            'office_state' => ['nullable', 'string', 'max:255'], // Renamed from state
            'office_post_code' => ['nullable', 'string', 'max:10'], // Renamed from post_code
            'states_operated_in' => ['required', 'array'], // New field, expects array of states
            'states_operated_in.*' => ['string', 'max:255'], // Validate each item in the array

            // SECTION 2: Services Provided (assuming these are collected on the same form)
            'sil_support_types' => ['nullable', 'array'], // New field, expects array
            'sil_support_types.*' => ['string'], // Validate each item in the array
            'sil_support_types_other' => ['nullable', 'string'], // New field
            'clinical_team_involvement' => ['nullable', 'string', Rule::in(['Yes', 'No', 'In partnership with external providers'])], // New field
            'staff_training_areas' => ['nullable', 'array'], // New field, expects array
            'staff_training_areas.*' => ['string'], // Validate each item in the array
            'staff_training_areas_other' => ['nullable', 'string'], // New field

            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', 'string', Rule::in(['provider'])],
            'terms_and_privacy' => ['accepted'],
        ]);

        // Create the User account for the individual managing the provider account
        $user = User::create([
            'first_name' => $request->main_contact_name, // Use main_contact_name for user's first name if not separate fields
            'last_name' => $request->main_contact_last_name, // Assuming you might add a separate last name for the contact, otherwise it's empty
            'email' => $request->email, // This is the user's login email
            'role' => 'provider',
            'password' => Hash::make($request->password),
            'profile_completed' => true, // Providers are considered "profile_completed" upon registration
        ]);

        $providerCodeName = 'PR' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        try {
            Provider::create([
                'user_id' => $user->id,
                // SECTION 1: Organisation Details
                'organisation_name' => $request->organisation_name,
                'abn' => $request->abn,
                'ndis_registration_number' => $request->ndis_registration_number,
                'provider_types' => $request->provider_types, // Store as JSON string
                'main_contact_name' => $request->main_contact_name . ' ' . $request->main_contact_last_name,
                'main_contact_role_title' => $request->main_contact_role_title,
                'phone_number' => $request->phone_number,
                'email_address' => $request->email, // This is the provider's contact email, distinct from the user's login email
                'website' => $request->website,
                'office_address' => $request->office_address,
                'office_suburb' => $request->office_suburb,
                'office_state' => $request->office_state,
                'office_post_code' => $request->office_post_code,
                'states_operated_in' => json_encode($request->states_operated_in), // Store as JSON string

                // SECTION 2: Services Provided
                'sil_support_types' => $request->sil_support_types ? json_encode($request->sil_support_types) : null, // Store as JSON string, handle nullable
                'sil_support_types_other' => $request->sil_support_types_other,
                'clinical_team_involvement' => $request->clinical_team_involvement,
                'staff_training_areas' => $request->staff_training_areas ? json_encode($request->staff_training_areas) : null, // Store as JSON string, handle nullable
                'staff_training_areas_other' => $request->staff_training_areas_other,

                // Retained from your previous schema if still needed (not in new form spec)
                'plan' => 'free', // Default or from request if you add it to the form
                'provider_code_name' => $providerCodeName,
                // 'provider_logo_path' => // Will be handled if you add file upload
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating provider: " . $e->getMessage());
            return back()->withErrors(['db_error' => 'Could not create provider. Please try again.'])->withInput(); // Added withInput()
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('provider.dashboard');
    }
}
