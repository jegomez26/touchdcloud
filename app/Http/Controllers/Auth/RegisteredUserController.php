<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Import Rule for conditional validation
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // ... (create method as before)

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'], // Participant's first name
            'last_name' => ['required', 'string', 'max:255'],   // Participant's last name
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['participant', 'coordinator', 'provider'])], // Use Rule::in for better readability
        ];

        // Add conditional validation for 'participant' role when 'representative' is chosen
        if ($request->input('role') === 'participant' && $request->input('registration_type') === 'representative') {
            $rules['representative_first_name'] = ['required', 'string', 'max:255'];
            $rules['representative_last_name'] = ['required', 'string', 'max:255'];
            $rules['relationship_to_participant'] = ['required', 'string', 'max:255'];
        }

        // Validate the request
        $request->validate($rules);

        // Prepare user data based on role and registration type
        $userData = [
            'first_name' => $request->first_name, // This is always the Participant's first name
            'last_name' => $request->last_name,   // This is always the Participant's last name
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_completed' => false,
        ];

        // If 'participant' role and registering as 'representative', store representative details separately
        // You'll need to decide where to store this.
        // Option 1: Add new columns to the 'users' table (e.g., 'representative_first_name', 'representative_last_name', 'relationship_to_participant')
        //           This would make the User model quite bloated if these fields only apply to one role/scenario.
        // Option 2: Create a separate 'representatives' table and link it to the 'users' table.
        //           This is generally a cleaner architectural approach for complex relationships.
        // Option 3: Store as a JSON column in 'users' table (less structured but quick).
        // For now, I'll demonstrate adding to the user data if you decide to add columns to the 'users' table.
        // **YOU WILL NEED TO ADD THESE COLUMNS TO YOUR USERS MIGRATION FIRST if you choose Option 1.**

        if ($request->input('role') === 'participant' && $request->input('registration_type') === 'representative') {
            $userData['representative_first_name'] = $request->representative_first_name;
            $userData['representative_last_name'] = $request->representative_last_name;
            $userData['relationship_to_participant'] = $request->relationship_to_participant;
            // You might also want to set the email and password to be for the representative, not the participant.
            // This design choice is critical: Is the user account for the participant or the representative?
            // Current code assumes the user account is for the participant, and representative details are extra.
            // If the account IS the representative, then email/password should be theirs, and participant details are extra.
            // Let's assume for now the *account* is the participant, and the representative details are additional.
            // If the account should be the representative, you'd swap 'first_name', 'last_name' with 'representative_first_name', 'representative_last_name' for the primary user fields.
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        // Redirect after successful registration
        return redirect(route('dashboard', absolute: false)); // Your middleware should handle profile_completed redirection
    }
}