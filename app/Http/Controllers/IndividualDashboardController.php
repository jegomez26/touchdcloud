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
            $participant = $user->participant;
        } elseif ($user->is_representative) {
            // This now correctly uses the relationship defined in User.php
            // that points to the 'representative_user_id' on the 'participants' table.
            $participant = $user->participantsRepresented()->first();
        }

        if (!$participant) {
            Log::warning('User logged in without an associated participant record.', ['user_id' => $user->id, 'user_role' => $user->role]);

            if ($user->role === 'participant') {
                return redirect()->route('indiv.profile.basic-details')
                                 ->with('error', 'Please complete your profile to set up your participant details.');
            } elseif ($user->is_representative) {
                // Redirect representative to a form specifically for adding a participant
                return redirect()->route('rep.add.participant.form')
                                 ->with('error', 'Please add a participant to your profile to continue.');
            }
            return redirect()->route('login')->with('error', 'Cannot access dashboard. User type not recognized or participant missing.');
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