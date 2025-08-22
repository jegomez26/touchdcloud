<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ParticipantProfileController;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IndividualDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Authentication error. Please log in again.');
        }

        $participant = null;
       
        if ($user->role === 'participant') {
            if ($user->is_representative) {
                // Scenario 1: User is a representative
                // They need to represent *at least one* participant to use the dashboard.
                // The participant they represent could be themselves, or someone else.

                // First, check if *they* (the representative) have a participant profile associated with their user_id
                $participant = $user->participant; // This checks the 1:1 relationship (self-represented)

                if (!$participant) {
                    // If they don't have their own participant profile linked (i.e., they only want to add others)
                    // Then, check if they have *any* participants they represent (via participantsRepresented relationship)
                    $representedParticipants = $user->participantsRepresented;

                    if ($representedParticipants->isEmpty()) {
                        // This representative hasn't set up their own participant profile
                        // AND hasn't added any other participants they represent.
                        // Redirect to a page where they can either:
                        // A) Set up their OWN participant profile OR
                        // B) Add a NEW participant they will represent.
                        // For simplicity, let's redirect them to the basic-details form to create *their own* participant profile first,
                        // assuming that's the primary path for all 'participant' role users initially.
                        Log::warning('Representative user logged in without any associated participant records.', ['user_id' => $user->id]);
                        return redirect()->route('indiv.profile.basic-details')
                                         ->with('error', 'Please set up your participant profile or add a participant you represent to continue.');
                    } else {
                        // If they represent other participants, default to showing the first one's dashboard,
                        // or provide a selection mechanism. For now, take the first.
                        $participant = $representedParticipants->first();
                    }
                }
            } else {
                // Scenario 2: User is a regular participant (not a representative)
                $participant = $user->participant;
            }
        }

        if (!$participant) {
            // This should ideally not be reached if the above logic is sound,
            // but acts as a final safety net.
            Log::error('IndividualDashboardController: No participant found despite user role/representative status.', ['user_id' => $user->id, 'user_role' => $user->role, 'is_representative' => $user->is_representative]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Could not find a participant profile. Please contact support.');
        }

        $profileController = new ParticipantProfileController();
        $basicDetailsComplete = $profileController->isBasicDetailsComplete($participant);
        $profileCompletionPercentage = $profileController->calculateProfileCompletion($participant);

        $applyingCoordinators = collect(); // Default to empty collection
        $latestMessages = collect(); // Default to empty collection

        if ($basicDetailsComplete) {
            // Your logic for fetching applying coordinators and latest messages
            // (uncomment and ensure models are imported as needed)
        } else {
            if ($user->profile_completed && !$participant->id) {
                $user->update(['profile_completed' => false]);
            }
        }

        return view('indiv.main-content', compact(
            'basicDetailsComplete',
            'profileCompletionPercentage',
            'applyingCoordinators',
            'latestMessages',
            'participant',
            
        ));
    }

    
}