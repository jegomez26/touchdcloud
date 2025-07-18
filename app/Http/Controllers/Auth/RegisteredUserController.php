<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Change this line to point to your new primary registration view
        return view('auth.register-individual');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'registration_type' => ['required', 'in:participant,representative'],
            // Validate 'first_name' and 'last_name' as they are always present,
            // but their meaning changes based on 'registration_type'.
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->input('registration_type') === 'representative') {
            $request->validate([
                'representative_first_name' => ['required', 'string', 'max:255'],
                'representative_last_name' => ['required', 'string', 'max:255'],
                'relationship_to_participant' => ['required', 'string', 'max:255'],
            ]);

            $user = User::create([
                'first_name' => $request->representative_first_name, // Rep's first name
                'last_name' => $request->representative_last_name,   // Rep's last name
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'representative', // Assign a 'representative' role
                'profile_completed' => false, // Set to false initially
                'is_representative' => true, // Flag this user as a representative
                'relationship_to_participant' => $request->relationship_to_participant, // Store relationship
            ]);

            $participant = new \App\Models\Participant([
                'first_name' => $request->first_name, // Participant's first name
                'last_name' => $request->last_name,   // Participant's last name
                // Add any other participant-specific fields here
            ]);
            // Ensure you have the 'participants' relationship defined in your User model
            // public function participants() { return $this->hasMany(Participant::class, 'representative_user_id'); }
            $user->participants()->save($participant);

        } else { // Registering as the participant
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'individual', // Assign 'individual' role
                'profile_completed' => false, // Set to false initially
                'is_representative' => false, // Flag as not a representative
            ]);

            // Ensure you have the 'participant' relationship defined in your User model
            // public function participant() { return $this->hasOne(Participant::class); }
            $participant = new \App\Models\Participant([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
            $user->participant()->save($participant);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}