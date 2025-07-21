<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // This method would render your 'register-individual' Blade view
        return view('auth.register-individual');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Define base rules for all registrations through this form
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['participant'])], // Fixed to 'participant' from hidden input
            'registration_type' => ['required', 'string', Rule::in(['participant', 'representative'])], // From radio buttons
        ];

        // Conditional validation based on registration_type
        if ($request->input('registration_type') === 'representative') {
            // If registering as a representative, the primary 'first_name' and 'last_name'
            // are for the *representative*.
            $rules['first_name'] = ['required', 'string', 'max:255']; // Representative's first name
            $rules['last_name'] = ['required', 'string', 'max:255'];  // Representative's last name

            // The 'representative_first_name' and 'representative_last_name' from the form
            // are for the *participant*. However, your migration defines these on the User table.
            // This is a potential source of confusion based on your variable names.
            // Let's assume you intended 'representative_first_name' and 'representative_last_name'
            // to be the *participant's* names when a representative registers.
            // And 'first_name'/'last_name' are for the representative.
            // This is the interpretation of your Blade, so we'll stick to it.
            $rules['representative_first_name'] = ['required', 'string', 'max:255']; // Participant's first name
            $rules['representative_last_name'] = ['required', 'string', 'max:255'];  // Participant's last name

            $rules['relationship_to_participant'] = ['required', 'string', 'max:255'];
        } else { // 'participant' (self) registration
            // If registering as the participant themselves, 'first_name' and 'last_name'
            // are for the participant (who is also the user account holder).
            $rules['first_name'] = ['required', 'string', 'max:255']; // Participant's (and user's) first name
            $rules['last_name'] = ['required', 'string', 'max:255'];  // Participant's (and user's) last name
        }

        $request->validate($rules);

        // Determine the value for 'is_representative' column
        $isRepresentativeAccount = ($request->input('registration_type') === 'representative');

        $userData = [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'participant', // Always 'participant' from the hidden input
            'profile_completed' => false,
            'is_representative' => $isRepresentativeAccount, // Set based on the registration_type
        ];

        if ($isRepresentativeAccount) {
            // If this account is a representative's account:
            // 'first_name' and 'last_name' columns of the User model store the Representative's name.
            $userData['first_name'] = $request->first_name;
            $userData['last_name'] = $request->last_name;
            // 'relationship_to_participant' stores the relationship.
            $userData['relationship_to_participant'] = $request->relationship_to_participant;
            // 'representative_first_name' and 'representative_last_name' store the Participant's name.
            $userData['representative_first_name'] = $request->representative_first_name;
            $userData['representative_last_name'] = $request->representative_last_name;
        } else {
            // If this account is the participant's own account:
            // 'first_name' and 'last_name' columns of the User model store the Participant's own name.
            $userData['first_name'] = $request->first_name;
            $userData['last_name'] = $request->last_name;
            // Set representative-specific fields to null if not applicable to avoid errors
            $userData['relationship_to_participant'] = null;
            $userData['representative_first_name'] = null;
            $userData['representative_last_name'] = null;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        // Redirect after successful registration.
        // The VerificationController's `verified` method should be configured
        // to redirect to `profile.complete.show` with a flash message after email verification.
        return redirect(route('dashboard', absolute: false));
    }
}