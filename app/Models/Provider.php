<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'organisation_name',
        'abn',
        'ndis_registration_number',
        'provider_types', // JSON
        'main_contact_name',
        'main_contact_role_title',
        'phone_number',
        'email_address',
        'website',
        'office_address',
        'office_suburb',
        'office_state',
        'office_post_code',
        'states_operated_in', // JSON
        'sil_support_types', // JSON
        'sil_support_types_other',
        'clinical_team_involvement',
        'staff_training_areas', // JSON
        'staff_training_areas_other',
        'plan',
        'provider_code_name',
        'provider_logo_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'provider_types' => 'array',
        'states_operated_in' => 'array',
        'sil_support_types' => 'array',
        'staff_training_areas' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the provider profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the properties/listings associated with this provider.
     * (Assuming you'll have a 'Property' or 'Listing' model in the future)
     */
    public function properties(): HasMany
    {
        // Example: return $this->hasMany(Property::class, 'provider_id');
        // You'll need to create a Property model and migration for this later.
        return $this->hasMany(Property::class); // Placeholder
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the full URL to the provider's logo.
     */
    public function getProviderLogoUrlAttribute(): ?string
    {
        if ($this->provider_logo_path) {
            return asset('storage/' . $this->provider_logo_path);
        }
        return null; // Or return a default logo URL
    }

    /**
     * Format the provider types into a readable string.
     */
    public function getProviderTypesFormattedAttribute(): string
    {
        return is_array($this->provider_types) && !empty($this->provider_types)
            ? implode(', ', $this->provider_types)
            : 'N/A';
    }

    /**
     * Format the states operated in into a readable string.
     */
    public function getStatesOperatedInFormattedAttribute(): string
    {
        return is_array($this->states_operated_in) && !empty($this->states_operated_in)
            ? implode(', ', $this->states_operated_in)
            : 'N/A';
    }

    /**
     * Format the SIL support types into a readable string.
     */
    public function getSilSupportTypesFormattedAttribute(): string
    {
        $types = $this->sil_support_types;
        $other = $this->sil_support_types_other;
        $formatted = '';

        if (is_array($types) && !empty($types)) {
            $formatted = implode(', ', $types);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'N/A';
    }

    /**
     * Format the staff training areas into a readable string.
     */
    public function getStaffTrainingAreasFormattedAttribute(): string
    {
        $areas = $this->staff_training_areas;
        $other = $this->staff_training_areas_other;
        $formatted = '';

        if (is_array($areas) && !empty($areas)) {
            $formatted = implode(', ', $areas);
        }

        if ($other) {
            $formatted .= ($formatted ? ', ' : '') . 'Other: ' . $other;
        }

        return $formatted ?: 'N/A';
    }
}