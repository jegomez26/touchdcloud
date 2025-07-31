<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provider; // Ensure you import your Provider model

class ProviderDashboardController extends Controller
{
    public function index()
    {
        // Load the authenticated user's associated provider profile
        $provider = Auth::user()->provider;

        return view('company.provider-db', compact('provider')); // Create provider/dashboard.blade.php
    }

    public function editProfile()
    {
        $provider = Auth::user()->provider;
        if (!$provider) {
            // Handle case where provider profile doesn't exist (shouldn't happen after registration)
            abort(404);
        }
        return view('company.edit-profile', compact('provider')); // Create provider/edit-profile.blade.php
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Define validation rules for provider profile updates
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'abn' => ['required', 'string', 'digits:11', 'unique:providers,abn,' . $provider->id], // Exclude current provider's ABN
            'contact_person_first_name' => ['required', 'string', 'max:255'],
            'contact_person_last_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:10'],
            // Add validation for other fields like plan, provider_logo if they'll be editable here
        ]);

        // Update the user's name if it's tied to the contact person
        $user->update([
            'first_name' => $request->contact_person_first_name,
            'last_name' => $request->contact_person_last_name,
        ]);

        // Update the provider's details
        $provider->update([
            'company_name' => $request->company_name,
            'abn' => $request->abn,
            'contact_person_first_name' => $request->contact_person_first_name,
            'contact_person_last_name' => $request->contact_person_last_name,
            'address' => $request->address,
            'suburb' => $request->suburb,
            'state' => $request->state,
            'post_code' => $request->post_code,
        ]);

        return redirect()->route('provider.dashboard')->with('status', 'Provider profile updated successfully.');
    }
}