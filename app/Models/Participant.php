<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Participant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Foreign key to the User who *is* this participant (if self-registering)
        'first_name',
        'last_name',
        'middle_name',

        // SECTION 0: Contact & Form Completion Details (for the participant themself)
        'participant_email',
        'participant_phone',
        'participant_contact_method',
        'is_participant_best_contact',

        // SECTION 1: Basic Demographics
        'date_of_birth', // Changed from 'birthday' to match migration
        'gender_identity',
        'gender_identity_other',
        'pronouns', // JSON field
        'pronouns_other',
        'languages_spoken', // JSON field
        'aboriginal_torres_strait_islander',

        // SECTION 2: NDIS Details
        'sil_funding_status',
        'ndis_plan_review_date',
        'ndis_plan_manager',
        'has_support_coordinator',

        // SECTION 3: Support Needs
        'daily_living_support_needs', // JSON field
        'daily_living_support_needs_other',
        'primary_disability',
        'secondary_disability',
        'estimated_support_hours_sil_level',
        'night_support_type',
        'uses_assistive_technology_mobility_aids',
        'assistive_technology_mobility_aids_list',

        // SECTION 4: Health & Safety
        'medical_conditions_relevant',
        'medication_administration_help',
        'behaviour_support_plan_status',
        'behaviours_of_concern_housemates',

        // SECTION 5: Living Preferences
        'preferred_sil_locations', // JSON field
        'housemate_preferences', // JSON field
        'housemate_preferences_other',
        'preferred_number_of_housemates',
        'accessibility_needs_in_home',
        'accessibility_needs_details',
        'pets_in_home_preference',
        'own_pet_type',
        'good_home_environment_looks_like', // JSON field
        'good_home_environment_looks_like_other',

        // SECTION 6: Compatibility & Personality
        'self_description', // JSON field
        'self_description_other',
        'smokes',
        'deal_breakers_housemates',
        'cultural_religious_practices',
        'interests_hobbies',

        // SECTION 7: Availability & Next Steps
        'move_in_availability',
        'current_living_situation',
        'current_living_situation_other',
        'contact_for_suitable_match',
        'preferred_contact_method_match',
        'preferred_contact_method_match_other',

        // Address Details (retained from previous structure, not explicitly in form)
        'street_address',
        'suburb',
        'state',
        'post_code',

        'support_coordinator_id', // Foreign key to the User acting as SC
        'added_by_user_id', // Foreign key to the User who added this participant record
        'participant_code_name', // Retained and corrected as it's on the Participant model

        // Removed: funding_amount_support_coor, funding_amount_accommodation, health_report_path, health_report_text, assessment_path
        // These were not in the new form description. If still needed, you'll need to add them back to the migration and fillable.
        // Removed: 'gender', 'disability_type', 'specific_disability', 'accommodation_type', 'approved_accommodation_type', 'behavior_of_concern',
        // 'is_looking_hm', 'has_accommodation', 'relative_name', 'relative_phone', 'relative_email', 'relative_relationship',
        // 'representative_user_id'
        // These were either replaced by new form fields or moved to participant_contacts.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date', // Casted to Carbon instance, aligned with migration
        'ndis_plan_review_date' => 'date', // New date field
        'is_participant_best_contact' => 'boolean',
        'has_support_coordinator' => 'boolean',
        'uses_assistive_technology_mobility_aids' => 'boolean',
        'smokes' => 'boolean',
        'contact_for_suitable_match' => 'boolean',

        // JSON casts for multi-select fields from the form
        'pronouns' => 'array',
        'languages_spoken' => 'array',
        'daily_living_support_needs' => 'array',
        'preferred_sil_locations' => 'array',
        'housemate_preferences' => 'array',
        'good_home_environment_looks_like' => 'array',
        'self_description' => 'array',

        // Your previous casts that are no longer directly relevant to the new fields
        // 'birthday' => 'date', // Replaced by date_of_birth
        // 'is_looking_hm' => 'boolean', // Replaced by new availability/contact fields
        // 'has_accommodation' => 'boolean', // Replaced by current_living_situation
        // 'disability_type' => 'array', // Replaced by primary/secondary_disability (which are strings now)
        // 'funding_amount_support_coor' => 'decimal:2', // Removed if not in new form
        // 'funding_amount_accommodation' => 'decimal:2', // Removed if not in new form
        // 'approved_accommodation_type' => \App\Enums\AccommodationType::class, // Keep if you re-add this field and its enum
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user who *is* this participant (if self-registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who is this participant's support coordinator.
     */
    public function supportCoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'support_coordinator_id');
    }

    /**
     * Get the user who added this participant record (e.g., representative, coordinator, provider).
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    /**
     * Get the primary contact person for this participant (if not the participant themselves).
     */
    public function contactPerson(): HasOne
    {
        return $this->hasOne(ParticipantContact::class, 'participant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include participants without a support coordinator.
     */
    public function scopeWithoutSupportCoordinator($query)
    {
        return $query->whereNull('support_coordinator_id');
    }

    /**
     * Scope a query to filter by state.
     */
    public function scopeByState($query, string $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope a query to filter by suburb.
     */
    public function scopeBySuburb($query, string $suburb)
    {
        return $query->where('suburb', 'like', '%' . $suburb . '%');
    }

    /**
     * Scope a query to filter by desired SIL location.
     * Assumes preferred_sil_locations is a JSON array.
     */
    public function scopeByPreferredSilLocation($query, string $location)
    {
        return $query->whereJsonContains('preferred_sil_locations', $location);
    }

    /**
     * Scope a query to filter by primary or secondary disability.
     */
    public function scopeByDisability($query, string $disability)
    {
        return $query->where('primary_disability', 'like', '%' . $disability . '%')
                     ->orWhere('secondary_disability', 'like', '%' . $disability . '%');
    }

    /**
     * Scope for general search (e.g., by code name, first name, last name, or disabilities).
     */
    public function scopeSearch($query, string $term)
    {
        $term = '%' . $term . '%';
        return $query->where('participant_code_name', 'like', $term)
                     ->orWhere('first_name', 'like', $term)
                     ->orWhere('last_name', 'like', $term)
                     ->orWhere('primary_disability', 'like', $term)
                     ->orWhere('secondary_disability', 'like', $term);
        // You might consider searching in JSON fields using whereJsonContains for multi-selects like languages, etc.
        // ->orWhereJsonContains('languages_spoken', $searchTerm); // Example if you want to search within JSON arrays
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the participant's age based on date_of_birth.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get the participant's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Format the daily living support needs into a readable string.
     */
    public function getDailyLivingSupportNeedsFormattedAttribute(): string
    {
        $needs = $this->daily_living_support_needs;
        $other = $this->daily_living_support_needs_other;
        $formatted = '';

        if (is_array($needs) && !empty($needs)) {
            $formatted = implode(', ', $needs);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'N/A';
    }

    /**
     * Format the preferred SIL locations into a readable string.
     */
    public function getPreferredSilLocationsFormattedAttribute(): string
    {
        return is_array($this->preferred_sil_locations) && !empty($this->preferred_sil_locations)
            ? implode(', ', $this->preferred_sil_locations)
            : 'N/A';
    }

    /**
     * Format the housemate preferences into a readable string.
     */
    public function getHousematePreferencesFormattedAttribute(): string
    {
        $preferences = $this->housemate_preferences;
        $other = $this->housemate_preferences_other;
        $formatted = '';

        if (is_array($preferences) && !empty($preferences)) {
            $formatted = implode(', ', $preferences);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'No strong preference';
    }

    /**
     * Format the good home environment description into a readable string.
     */
    public function getGoodHomeEnvironmentFormattedAttribute(): string
    {
        $env = $this->good_home_environment_looks_like;
        $other = $this->good_home_environment_looks_like_other;
        $formatted = '';

        if (is_array($env) && !empty($env)) {
            $formatted = implode(', ', $env);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'N/A';
    }

    /**
     * Format the self-description into a readable string.
     */
    public function getSelfDescriptionFormattedAttribute(): string
    {
        $desc = $this->self_description;
        $other = $this->self_description_other;
        $formatted = '';

        if (is_array($desc) && !empty($desc)) {
            $formatted = implode(', ', $desc);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'N/A';
    }

    /**
     * Get the gender identity and pronouns formatted for display.
     */
    public function getGenderPronounsFormattedAttribute(): string
    {
        $gender = $this->gender_identity;
        if ($this->gender_identity === 'Other' && $this->gender_identity_other) {
            $gender = $this->gender_identity_other;
        }

        $pronouns = is_array($this->pronouns) && !empty($this->pronouns)
            ? implode(', ', $this->pronouns)
            : null;
        if ($this->pronouns_other) {
            $pronouns .= ($pronouns ? ', ' : '') . 'Other: ' . $this->pronouns_other;
        }

        if ($gender && $pronouns) {
            return "{$gender} ({$pronouns})";
        } elseif ($gender) {
            return $gender;
        } elseif ($pronouns) {
            return $pronouns;
        }
        return 'N/A';
    }

    /**
     * Get the contact method for suitable matches.
     */
    public function getContactMethodMatchFormattedAttribute(): string
    {
        $method = $this->preferred_contact_method_match;
        $other = $this->preferred_contact_method_match_other;

        if ($method === 'Other' && $other) {
            return 'Other: ' . $other;
        }
        return $method ?: 'N/A';
    }
}