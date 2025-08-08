<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'representative_user_id',
        'added_by_user_id',
        'first_name',
        'last_name',
        'middle_name',
        // ... (all the other fillable fields from your form)
        'participant_email',
        'participant_phone',
        'participant_contact_method',
        'is_participant_best_contact',
        'date_of_birth',
        'gender_identity',
        'gender_identity_other',
        'pronouns',
        'pronouns_other',
        'languages_spoken',
        'aboriginal_torres_strait_islander',
        'sil_funding_status',
        'ndis_plan_review_date',
        'ndis_plan_manager',
        'has_support_coordinator',
        'daily_living_support_needs',
        'daily_living_support_needs_other',
        'primary_disability',
        'secondary_disability',
        'estimated_support_hours_sil_level',
        'night_support_type',
        'uses_assistive_technology_mobility_aids',
        'assistive_technology_mobility_aids_list',
        'medical_conditions_relevant',
        'medication_administration_help',
        'behaviour_support_plan_status',
        'behaviours_of_concern_housemates',
        'preferred_sil_locations',
        'housemate_preferences',
        'housemate_preferences_other',
        'preferred_number_of_housemates',
        'accessibility_needs_in_home',
        'accessibility_needs_details',
        'pets_in_home_preference',
        'own_pet_type',
        'good_home_environment_looks_like',
        'good_home_environment_looks_like_other',
        'self_description',
        'self_description_other',
        'smokes',
        'deal_breakers_housemates',
        'cultural_religious_practices',
        'interests_hobbies',
        'move_in_availability',
        'current_living_situation',
        'current_living_situation_other',
        'contact_for_suitable_match',
        'preferred_contact_method_match',
        'preferred_contact_method_match_other',
        'street_address',
        'suburb',
        'state',
        'post_code',
        'support_coordinator_id',
        'participant_code_name',
        // Ensure all fields from your schema that are filled by the form are listed here
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_participant_best_contact' => 'boolean',
        'pronouns' => 'array',
        'languages_spoken' => 'array',
        'has_support_coordinator' => 'boolean',
        'daily_living_support_needs' => 'array',
        'uses_assistive_technology_mobility_aids' => 'boolean',
        'medication_administration_help' => 'boolean',
        'preferred_sil_locations' => 'array',
        'housemate_preferences' => 'array',
        'good_home_environment_looks_like' => 'array',
        'self_description' => 'array',
        'smokes' => 'boolean',
        'contact_for_suitable_match' => 'boolean',
        'date_of_birth' => 'date',
        'disability_type' => 'array', // Assuming this is also a JSON array
        'is_looking_hm' => 'boolean',
        'has_accommodation' => 'boolean',
        // Add any other boolean or JSON fields from your schema here
    ];

    /**
     * Get the user that owns the participant record (for direct participants).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who is the representative for this participant.
     */
    public function representativeUser()
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }
}
