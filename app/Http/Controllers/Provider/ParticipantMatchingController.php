<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ParticipantMatchingController extends Controller
{
    /**
     * Display all participants added by the provider
     */
    public function index()
    {
        $provider = Auth::user();
        $participants = $provider->participantsAdded()->paginate(12);
        
        return view('company.participants.matching.index', compact('participants'));
    }

    /**
     * Show the matching interface for a specific participant
     */
    public function show(Participant $participant)
    {
        // Ensure the participant belongs to the current provider
        if ($participant->added_by_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to participant.');
        }

        return view('company.participants.matching.show', compact('participant'));
    }

    /**
     * Find potential matches for a participant
     */
    public function findMatches(Participant $participant)
    {
        // Ensure the participant belongs to the current provider
        if ($participant->added_by_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to participant.');
        }

        // Get all other participants (excluding the current one)
        $allParticipants = Participant::where('id', '!=', $participant->id)
            ->where('contact_for_suitable_match', true)
            ->get();

        $matches = $this->calculateMatches($participant, $allParticipants);

        return response()->json([
            'participant' => $participant,
            'matches' => $matches
        ]);
    }

    /**
     * Calculate compatibility scores for potential matches
     */
    private function calculateMatches(Participant $participant, $allParticipants)
    {
        $matches = [];

        foreach ($allParticipants as $potentialMatch) {
            $score = $this->calculateCompatibilityScore($participant, $potentialMatch);
            
            if ($score > 0) {
                $matches[] = [
                    'participant' => $potentialMatch,
                    'score' => $score,
                    'compatibility_factors' => $this->getCompatibilityFactors($participant, $potentialMatch)
                ];
            }
        }

        // Sort by compatibility score (highest first)
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $matches;
    }

    /**
     * Calculate compatibility score between two participants
     */
    private function calculateCompatibilityScore(Participant $participant1, Participant $participant2)
    {
        $score = 0;
        $maxScore = 0;

        // Age compatibility (within 10 years gets full points)
        if ($participant1->date_of_birth && $participant2->date_of_birth) {
            $age1 = $participant1->age;
            $age2 = $participant2->age;
            $ageDiff = abs($age1 - $age2);
            
            if ($ageDiff <= 5) {
                $score += 20;
            } elseif ($ageDiff <= 10) {
                $score += 15;
            } elseif ($ageDiff <= 15) {
                $score += 10;
            }
            $maxScore += 20;
        }

        // Gender compatibility (same gender preference)
        if ($participant1->gender_identity && $participant2->gender_identity) {
            if ($participant1->gender_identity === $participant2->gender_identity) {
                $score += 15;
            }
            $maxScore += 15;
        }

        // Location compatibility (same state gets points)
        if ($participant1->state && $participant2->state) {
            if ($participant1->state === $participant2->state) {
                $score += 10;
                if ($participant1->suburb && $participant2->suburb && 
                    $participant1->suburb === $participant2->suburb) {
                    $score += 10;
                }
            }
            $maxScore += 20;
        }

        // Disability compatibility
        if ($participant1->primary_disability && $participant2->primary_disability) {
            if ($participant1->primary_disability === $participant2->primary_disability) {
                $score += 15;
            }
            $maxScore += 15;
        }

        // Support level compatibility
        if ($participant1->estimated_support_hours_sil_level && $participant2->estimated_support_hours_sil_level) {
            if ($participant1->estimated_support_hours_sil_level === $participant2->estimated_support_hours_sil_level) {
                $score += 10;
            }
            $maxScore += 10;
        }

        // Smoking compatibility
        if (isset($participant1->smokes) && isset($participant2->smokes)) {
            if ($participant1->smokes === $participant2->smokes) {
                $score += 10;
            }
            $maxScore += 10;
        }

        // Housemate preferences compatibility
        $preferences1 = $this->getDecodedField($participant1, 'housemate_preferences');
        $preferences2 = $this->getDecodedField($participant2, 'housemate_preferences');
        
        if (!empty($preferences1) && !empty($preferences2)) {
            $commonPreferences = array_intersect($preferences1, $preferences2);
            if (!empty($commonPreferences)) {
                $score += count($commonPreferences) * 5;
            }
            $maxScore += 20;
        }

        // Interests compatibility
        if ($participant1->interests_hobbies && $participant2->interests_hobbies) {
            $interests1 = explode(',', $participant1->interests_hobbies);
            $interests2 = explode(',', $participant2->interests_hobbies);
            $commonInterests = array_intersect($interests1, $interests2);
            
            if (!empty($commonInterests)) {
                $score += count($commonInterests) * 3;
            }
            $maxScore += 15;
        }

        // Calculate percentage
        if ($maxScore > 0) {
            return round(($score / $maxScore) * 100);
        }

        return 0;
    }

    /**
     * Get compatibility factors for display
     */
    private function getCompatibilityFactors(Participant $participant1, Participant $participant2)
    {
        $factors = [];

        // Age compatibility
        if ($participant1->date_of_birth && $participant2->date_of_birth) {
            $age1 = $participant1->age;
            $age2 = $participant2->age;
            $ageDiff = abs($age1 - $age2);
            
            if ($ageDiff <= 5) {
                $factors[] = "Similar age (within 5 years)";
            } elseif ($ageDiff <= 10) {
                $factors[] = "Compatible age (within 10 years)";
            }
        }

        // Location compatibility
        if ($participant1->state && $participant2->state && 
            $participant1->state === $participant2->state) {
            $factors[] = "Same state";
            
            if ($participant1->suburb && $participant2->suburb && 
                $participant1->suburb === $participant2->suburb) {
                $factors[] = "Same suburb";
            }
        }

        // Disability compatibility
        if ($participant1->primary_disability && $participant2->primary_disability && 
            $participant1->primary_disability === $participant2->primary_disability) {
            $factors[] = "Similar disability type";
        }

        // Support level compatibility
        if ($participant1->estimated_support_hours_sil_level && $participant2->estimated_support_hours_sil_level && 
            $participant1->estimated_support_hours_sil_level === $participant2->estimated_support_hours_sil_level) {
            $factors[] = "Similar support needs";
        }

        // Smoking compatibility
        if (isset($participant1->smokes) && isset($participant2->smokes) && 
            $participant1->smokes === $participant2->smokes) {
            if ($participant1->smokes) {
                $factors[] = "Both smoke";
            } else {
                $factors[] = "Both non-smokers";
            }
        }

        // Common interests
        if ($participant1->interests_hobbies && $participant2->interests_hobbies) {
            $interests1 = explode(',', $participant1->interests_hobbies);
            $interests2 = explode(',', $participant2->interests_hobbies);
            $commonInterests = array_intersect($interests1, $interests2);
            
            if (!empty($commonInterests)) {
                $factors[] = "Shared interests: " . implode(', ', array_slice($commonInterests, 0, 3));
            }
        }

        return $factors;
    }

    /**
     * Helper method to decode JSON fields
     */
    private function getDecodedField($participant, $field)
    {
        $value = $participant->$field;
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return $value ?: [];
    }
}
