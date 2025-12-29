<?php

namespace App\Services;

use App\Models\Participant;

class ParticipantMatchingService
{
    /**
     * Calculate compatibility score between two participants using standardized criteria
     */
    public function calculateCompatibilityScore(Participant $participant1, Participant $participant2)
    {
        $score = 0;
        $maxScore = 0;
        
        // Standardized weights for all user types
        $weights = [
            'age' => 20,
            'gender' => 15,
            'location' => 25,
            'disability' => 20,
            'support_level' => 15,
            'lifestyle' => 15,
            'preferences' => 20,
            'interests' => 15,
            'communication' => 10,
            'personality' => 10,
            'availability' => 10,
        ];

        // Age compatibility with nuanced scoring
        if ($participant1->date_of_birth && $participant2->date_of_birth) {
            $age1 = $participant1->age;
            $age2 = $participant2->age;
            $ageDiff = abs($age1 - $age2);
            
            if ($ageDiff <= 2) {
                $score += $weights['age']; // Perfect age match
            } elseif ($ageDiff <= 5) {
                $score += $weights['age'] * 0.9; // Very close
            } elseif ($ageDiff <= 10) {
                $score += $weights['age'] * 0.7; // Good match
            } elseif ($ageDiff <= 15) {
                $score += $weights['age'] * 0.5; // Acceptable
            } elseif ($ageDiff <= 20) {
                $score += $weights['age'] * 0.3; // Marginal
            }
            $maxScore += $weights['age'];
        }

        // Gender compatibility with preferences
        if ($participant1->gender_identity && $participant2->gender_identity) {
            if ($participant1->gender_identity === $participant2->gender_identity) {
                $score += $weights['gender'];
            } else {
                // Check if either participant has specific gender preferences
                $genderPrefs1 = $this->getDecodedField($participant1, 'gender_preferences');
                $genderPrefs2 = $this->getDecodedField($participant2, 'gender_preferences');
                
                if (!empty($genderPrefs1) && in_array($participant2->gender_identity, $genderPrefs1)) {
                    $score += $weights['gender'] * 0.8;
                } elseif (!empty($genderPrefs2) && in_array($participant1->gender_identity, $genderPrefs2)) {
                    $score += $weights['gender'] * 0.8;
                }
            }
            $maxScore += $weights['gender'];
        }

        // Enhanced location compatibility
        if ($participant1->state && $participant2->state) {
            if ($participant1->state === $participant2->state) {
                $score += $weights['location'] * 0.6; // Same state
                
                if ($participant1->suburb && $participant2->suburb) {
                    if ($participant1->suburb === $participant2->suburb) {
                        $score += $weights['location'] * 0.4; // Same suburb - perfect location match
                    } else {
                        // Check if suburbs are in same region/area
                        $score += $weights['location'] * 0.2; // Same state, different suburb
                    }
                }
            }
            $maxScore += $weights['location'];
        }

        // Enhanced disability compatibility
        if ($participant1->primary_disability && $participant2->primary_disability) {
            if ($participant1->primary_disability === $participant2->primary_disability) {
                $score += $weights['disability']; // Exact match
            } else {
                // Check for similar disability categories
                $similarDisabilities = $this->getSimilarDisabilities($participant1->primary_disability, $participant2->primary_disability);
                if ($similarDisabilities) {
                    $score += $weights['disability'] * 0.7; // Similar category
                }
            }
            $maxScore += $weights['disability'];
        }

        // Enhanced support level compatibility
        if ($participant1->estimated_support_hours_sil_level && $participant2->estimated_support_hours_sil_level) {
            $supportLevels = ['Low', 'Medium', 'High', 'Very High'];
            $level1Index = array_search($participant1->estimated_support_hours_sil_level, $supportLevels);
            $level2Index = array_search($participant2->estimated_support_hours_sil_level, $supportLevels);
            
            if ($level1Index !== false && $level2Index !== false) {
                $levelDiff = abs($level1Index - $level2Index);
                if ($levelDiff === 0) {
                    $score += $weights['support_level']; // Exact match
                } elseif ($levelDiff === 1) {
                    $score += $weights['support_level'] * 0.8; // Adjacent levels
                } elseif ($levelDiff === 2) {
                    $score += $weights['support_level'] * 0.5; // Moderate difference
                }
            }
            $maxScore += $weights['support_level'];
        }

        // Enhanced lifestyle compatibility
        $lifestyleScore = 0;
        $lifestyleMax = 0;
        
        // Smoking compatibility
        if (isset($participant1->smokes) && isset($participant2->smokes)) {
            if ($participant1->smokes === $participant2->smokes) {
                $lifestyleScore += 5;
            }
            $lifestyleMax += 5;
        }
        
        // Pet preferences
        $pets1 = $this->getDecodedField($participant1, 'pet_preferences');
        $pets2 = $this->getDecodedField($participant2, 'pet_preferences');
        if (!empty($pets1) && !empty($pets2)) {
            $commonPets = array_intersect($pets1, $pets2);
            if (!empty($commonPets)) {
                $lifestyleScore += 5;
            }
            $lifestyleMax += 5;
        }
        
        // Daily routine compatibility
        if ($participant1->daily_routine_preferences && $participant2->daily_routine_preferences) {
            $routine1 = explode(',', $participant1->daily_routine_preferences);
            $routine2 = explode(',', $participant2->daily_routine_preferences);
            $commonRoutines = array_intersect($routine1, $routine2);
            if (!empty($commonRoutines)) {
                $lifestyleScore += 5;
            }
            $lifestyleMax += 5;
        }
        
        if ($lifestyleMax > 0) {
            $score += ($lifestyleScore / $lifestyleMax) * $weights['lifestyle'];
        }
        $maxScore += $weights['lifestyle'];

        // Enhanced housemate preferences compatibility
        $preferences1 = $this->getDecodedField($participant1, 'housemate_preferences');
        $preferences2 = $this->getDecodedField($participant2, 'housemate_preferences');
        
        if (!empty($preferences1) && !empty($preferences2)) {
            $commonPreferences = array_intersect($preferences1, $preferences2);
            $totalPreferences = array_unique(array_merge($preferences1, $preferences2));
            
            if (!empty($commonPreferences)) {
                $preferenceScore = (count($commonPreferences) / count($totalPreferences)) * $weights['preferences'];
                $score += $preferenceScore;
            }
            $maxScore += $weights['preferences'];
        }

        // Enhanced interests compatibility with weighted scoring
        if ($participant1->interests_hobbies && $participant2->interests_hobbies) {
            $interests1 = array_map('trim', explode(',', $participant1->interests_hobbies));
            $interests2 = array_map('trim', explode(',', $participant2->interests_hobbies));
            $commonInterests = array_intersect($interests1, $interests2);
            
            if (!empty($commonInterests)) {
                // Weight interests by importance/activity level
                $interestScore = min(count($commonInterests) * 3, $weights['interests']);
                $score += $interestScore;
            }
            $maxScore += $weights['interests'];
        }

        // Communication preferences compatibility
        $commPrefs1 = $this->getDecodedField($participant1, 'communication_preferences');
        $commPrefs2 = $this->getDecodedField($participant2, 'communication_preferences');
        
        if (!empty($commPrefs1) && !empty($commPrefs2)) {
            $commonCommPrefs = array_intersect($commPrefs1, $commPrefs2);
            if (!empty($commonCommPrefs)) {
                $score += (count($commonCommPrefs) / max(count($commPrefs1), count($commPrefs2))) * $weights['communication'];
            }
            $maxScore += $weights['communication'];
        }

        // Personality traits compatibility
        $personality1 = $this->getDecodedField($participant1, 'personality_traits');
        $personality2 = $this->getDecodedField($participant2, 'personality_traits');
        
        if (!empty($personality1) && !empty($personality2)) {
            $commonTraits = array_intersect($personality1, $personality2);
            if (!empty($commonTraits)) {
                $score += (count($commonTraits) / max(count($personality1), count($personality2))) * $weights['personality'];
            }
            $maxScore += $weights['personality'];
        }

        // Availability compatibility
        if ($participant1->move_in_availability && $participant2->move_in_availability) {
            $availability1 = strtolower($participant1->move_in_availability);
            $availability2 = strtolower($participant2->move_in_availability);
            
            if ($availability1 === $availability2) {
                $score += $weights['availability'];
            } elseif (strpos($availability1, 'flexible') !== false || strpos($availability2, 'flexible') !== false) {
                $score += $weights['availability'] * 0.7;
            }
            $maxScore += $weights['availability'];
        }

        // Calculate percentage with minimum threshold
        if ($maxScore > 0) {
            $percentage = round(($score / $maxScore) * 100);
            
            // Apply bonus for high compatibility
            if ($percentage >= 80) {
                $percentage = min(100, $percentage + 5); // Bonus for excellent matches
            }
            
            return $percentage;
        }

        return 0;
    }

    /**
     * Get compatibility factors for display
     */
    public function getCompatibilityFactors(Participant $participant1, Participant $participant2)
    {
        $factors = [];

        // Age compatibility
        if ($participant1->date_of_birth && $participant2->date_of_birth) {
            $age1 = $participant1->age;
            $age2 = $participant2->age;
            $ageDiff = abs($age1 - $age2);
            
            if ($ageDiff <= 5) {
                $factors[] = 'Similar age (within 5 years)';
            } elseif ($ageDiff <= 10) {
                $factors[] = 'Close age range (within 10 years)';
            }
        }

        // Gender compatibility
        if ($participant1->gender_identity && $participant2->gender_identity) {
            if ($participant1->gender_identity === $participant2->gender_identity) {
                $factors[] = 'Same gender identity';
            }
        }

        // Location compatibility
        if ($participant1->state && $participant2->state) {
            if ($participant1->state === $participant2->state) {
                $factors[] = 'Same state';
                if ($participant1->suburb && $participant2->suburb && $participant1->suburb === $participant2->suburb) {
                    $factors[] = 'Same suburb';
                }
            }
        }

        // Disability compatibility
        if ($participant1->primary_disability && $participant2->primary_disability) {
            if ($participant1->primary_disability === $participant2->primary_disability) {
                $factors[] = 'Same disability type';
            } else {
                $similarDisabilities = $this->getSimilarDisabilities($participant1->primary_disability, $participant2->primary_disability);
                if ($similarDisabilities) {
                    $factors[] = 'Similar disability category';
                }
            }
        }

        // Support level compatibility
        if ($participant1->estimated_support_hours_sil_level && $participant2->estimated_support_hours_sil_level) {
            if ($participant1->estimated_support_hours_sil_level === $participant2->estimated_support_hours_sil_level) {
                $factors[] = 'Same support level';
            }
        }

        // Lifestyle compatibility
        if (isset($participant1->smokes) && isset($participant2->smokes) && $participant1->smokes === $participant2->smokes) {
            $factors[] = 'Compatible smoking preferences';
        }

        // Housemate preferences
        $preferences1 = $this->getDecodedField($participant1, 'housemate_preferences');
        $preferences2 = $this->getDecodedField($participant2, 'housemate_preferences');
        if (!empty($preferences1) && !empty($preferences2)) {
            $commonPreferences = array_intersect($preferences1, $preferences2);
            if (!empty($commonPreferences)) {
                $factors[] = 'Shared housemate preferences: ' . implode(', ', array_slice($commonPreferences, 0, 3));
            }
        }

        // Interests compatibility
        if ($participant1->interests_hobbies && $participant2->interests_hobbies) {
            $interests1 = array_map('trim', explode(',', $participant1->interests_hobbies));
            $interests2 = array_map('trim', explode(',', $participant2->interests_hobbies));
            $commonInterests = array_intersect($interests1, $interests2);
            if (!empty($commonInterests)) {
                $factors[] = 'Shared interests: ' . implode(', ', array_slice($commonInterests, 0, 3));
            }
        }

        // Communication preferences
        $commPrefs1 = $this->getDecodedField($participant1, 'communication_preferences');
        $commPrefs2 = $this->getDecodedField($participant2, 'communication_preferences');
        if (!empty($commPrefs1) && !empty($commPrefs2)) {
            $commonCommPrefs = array_intersect($commPrefs1, $commPrefs2);
            if (!empty($commonCommPrefs)) {
                $factors[] = 'Compatible communication styles';
            }
        }

        // Personality traits
        $personality1 = $this->getDecodedField($participant1, 'personality_traits');
        $personality2 = $this->getDecodedField($participant2, 'personality_traits');
        if (!empty($personality1) && !empty($personality2)) {
            $commonTraits = array_intersect($personality1, $personality2);
            if (!empty($commonTraits)) {
                $factors[] = 'Similar personality traits';
            }
        }

        // Availability
        if ($participant1->move_in_availability && $participant2->move_in_availability) {
            if ($participant1->move_in_availability === $participant2->move_in_availability) {
                $factors[] = 'Compatible move-in timeline';
            }
        }

        return $factors;
    }

    /**
     * Get detailed match information
     */
    public function getMatchDetails(Participant $participant1, Participant $participant2)
    {
        $details = [];
        
        // Age compatibility
        if ($participant1->date_of_birth && $participant2->date_of_birth) {
            $age1 = $participant1->age;
            $age2 = $participant2->age;
            $ageDiff = abs($age1 - $age2);
            $details['age_compatibility'] = "Age difference: {$ageDiff} years";
        }

        // Location details
        if ($participant1->state && $participant2->state) {
            $details['location'] = $participant1->state === $participant2->state ? 'Same state' : 'Different states';
            if ($participant1->suburb && $participant2->suburb) {
                $details['suburb'] = $participant1->suburb === $participant2->suburb ? 'Same suburb' : 'Different suburbs';
            }
        }

        // Support level details
        if ($participant1->estimated_support_hours_sil_level && $participant2->estimated_support_hours_sil_level) {
            $details['support_level'] = $participant1->estimated_support_hours_sil_level === $participant2->estimated_support_hours_sil_level ? 'Same support level' : 'Different support levels';
        }

        // Disability details
        if ($participant1->primary_disability && $participant2->primary_disability) {
            $details['disability'] = $participant1->primary_disability === $participant2->primary_disability ? 'Same disability type' : 'Different disability types';
        }

        return $details;
    }

    /**
     * Check if two disabilities are similar (same category)
     */
    private function getSimilarDisabilities($disability1, $disability2)
    {
        $disabilityCategories = [
            'intellectual' => ['Intellectual Disability', 'Down Syndrome', 'Autism Spectrum Disorder'],
            'physical' => ['Physical Disability', 'Cerebral Palsy', 'Spinal Cord Injury', 'Muscular Dystrophy'],
            'sensory' => ['Vision Impairment', 'Hearing Impairment', 'Deaf-Blind'],
            'psychiatric' => ['Mental Health Condition', 'Bipolar Disorder', 'Depression', 'Anxiety'],
            'neurological' => ['Epilepsy', 'Acquired Brain Injury', 'Multiple Sclerosis']
        ];

        foreach ($disabilityCategories as $category => $disabilities) {
            if (in_array($disability1, $disabilities) && in_array($disability2, $disabilities)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get decoded field value (handles both JSON strings and arrays)
     */
    private function getDecodedField($participant, $field)
    {
        if (!$participant || !isset($participant->$field) || $participant->$field === null) {
            return [];
        }

        $value = $participant->$field;
        
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
