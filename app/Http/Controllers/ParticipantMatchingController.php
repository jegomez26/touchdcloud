<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;

class ParticipantMatchingController extends Controller
{
    /**
     * Display possible matches for the authenticated participant
     */
    public function index()
    {
        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            abort(403, 'Participant profile not found.');
        }

        // Get other participants who might be compatible
        // For now, we'll show all other participants (in a real app, you'd implement matching logic)
        $possibleMatches = Participant::where('id', '!=', $participant->id)
            ->whereNotNull('participant_code_name')
            ->with('user')
            ->limit(20)
            ->get()
            ->map(function($match) {
                return [
                    'id' => $match->id,
                    'code' => $match->participant_code_name,
                    'age_range' => $this->getAgeRange($match),
                    'location' => $match->preferred_location ?? 'Melbourne, VIC',
                    'accommodation_type' => $match->accommodation_type ?? 'Shared accommodation preferred',
                    'interests' => $match->interests ?? 'Similar daily routine',
                    'match_percentage' => rand(75, 95), // Random for demo - implement real matching logic
                ];
            });

        return response()->json([
            'matches' => $possibleMatches,
            'current_participant' => [
                'id' => $participant->id,
                'code' => $participant->participant_code_name,
            ]
        ]);
    }

    /**
     * Get age range for a participant
     */
    private function getAgeRange($participant)
    {
        if ($participant->date_of_birth) {
            $age = now()->diffInYears($participant->date_of_birth);
            if ($age >= 18 && $age <= 25) {
                return '18-25';
            } elseif ($age >= 26 && $age <= 35) {
                return '26-35';
            } elseif ($age >= 36 && $age <= 45) {
                return '36-45';
            } elseif ($age >= 46 && $age <= 55) {
                return '46-55';
            } else {
                return '55+';
            }
        }
        return '25-30'; // Default
    }
}