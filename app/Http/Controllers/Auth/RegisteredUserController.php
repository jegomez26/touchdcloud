<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NDISBusiness; // Keep NDISBusiness if used elsewhere, but not for SupportCoordinator registration now
use App\Models\SupportCoordinator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the default registration view (for individuals/participants).
     */
    public function create(): View
    {
        return view('auth.register-individual');
    }

    /**
     * Display the registration view for a support coordinator.
     */
    public function createCoordinator(): View
    {
        // Removed: $ndisBusinesses = NDISBusiness::all(['id', 'business_name', 'abn']);
        // Removed: compact('ndisBusinesses')
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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', 'string', Rule::in(['participant'])],
            'registration_type' => ['required', 'string', Rule::in(['participant', 'representative'])],
            'terms_and_privacy' => ['accepted'],
        ];

        if ($request->input('registration_type') === 'representative') {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['representative_first_name'] = ['required', 'string', 'max:255'];
            $rules['representative_last_name'] = ['required', 'string', 'max:255'];
            $rules['relationship_to_participant'] = ['required', 'string', 'max:255'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules);

        $isRepresentativeAccount = ($request->input('registration_type') === 'representative');

        $userData = [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'participant',
            'profile_completed' => false,
            'is_representative' => $isRepresentativeAccount,
        ];

        if ($isRepresentativeAccount) {
            $userData['first_name'] = $request->first_name;
            $userData['last_name'] = $request->last_name;
            $userData['relationship_to_participant'] = $request->relationship_to_participant;
            $userData['representative_first_name'] = $request->representative_first_name;
            $userData['representative_last_name'] = $request->representative_last_name;
        } else {
            $userData['first_name'] = $request->first_name;
            $userData['last_name'] = $request->last_name;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
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
            'is_representative' => false,
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
            'business_name' => ['required', 'string', 'max:255'],
            'abn' => ['required', 'string', 'digits:11', 'unique:ndis_businesses,abn'],
            'contact_person_first_name' => ['required', 'string', 'max:255'],
            'contact_person_last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', 'string', Rule::in(['provider'])],
            'terms_and_privacy' => ['accepted'],
        ]);

        $user = User::create([
            'first_name' => $request->contact_person_first_name,
            'last_name' => $request->contact_person_last_name,
            'email' => $request->email,
            'role' => 'provider',
            'password' => Hash::make($request->password),
            'profile_completed' => false,
            'is_representative' => false,
        ]);

        // Assuming 'provider_code_name' is a column on your `users` table
        // If it's on `ndis_businesses` table, move this assignment to NDISBusiness::create
        $user->provider_code_name = 'PR' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $user->save(); // This save might not be necessary if provider_code_name is only on NDISBusiness

        NDISBusiness::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'abn' => $request->abn,
            'contact_person_first_name' => $request->contact_person_first_name,
            'contact_person_last_name' => $request->contact_person_last_name,
            'provider_code_name' => $user->provider_code_name, // If moved, generate here directly
            'status' => 'pending_verification',
        ]);

        event(new Registered($user));

        return redirect(route('provider.account.pending'));
    }
}