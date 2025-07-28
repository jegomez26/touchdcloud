<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon; // <--- ADD THIS LINE to import Carbon

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Foreign key to the User who *is* this participant (if self-registering)
        'first_name',
        'last_name',
        'middle_name',
        'birthday', // <--- This is your date column
        'gender',
        'disability_type',
        'specific_disability',
        'accommodation_type', // <--- This is your column for accommodation
        'approved_accommodation_type',
        'behavior_of_concern',
        'street_address',
        'suburb',
        'state',
        'post_code',
        'is_looking_hm',
        'has_accommodation',
        'funding_amount_support_coor',
        'funding_amount_accommodation',

        // These are the relative/emergency contact fields directly on the participants table
        'relative_name',
        'relative_phone',
        'relative_email',
        'relative_relationship',

        'support_coordinator_id',
        'representative_user_id',
        'added_by_user_id',
        // 'participant_code_name', // <--- REMOVED: Based on your comment, this seems to be on the User model now.
                                 // If it's still on the Participant model, add it back.
        'health_report_path',
        'health_report_text',
        'assessment_path',
    ];

    protected $casts = [
        'birthday' => 'date', // <--- Correctly casts to Carbon instance
        'is_looking_hm' => 'boolean',
        'has_accommodation' => 'boolean',
        'disability_type' => 'array', // This is correct for JSON column
        'funding_amount_support_coor' => 'decimal:2',
        'funding_amount_accommodation' => 'decimal:2',
        // 'approved_accommodation_type' => \App\Enums\AccommodationType::class, // If you create an Enum for this
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supportCoordinator(): BelongsTo
    {
        // Assuming SupportCoordinator is a separate model, if it's the User model with a role,
        // this relationship might be to User::class
        return $this->belongsTo(User::class, 'support_coordinator_id'); // <--- Changed to User::class if support_coordinator_id refers to Users table
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function representativeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }

    // Scope to filter participants without a support coordinator
    public function scopeWithoutSupportCoordinator($query)
    {
        return $query->whereNull('support_coordinator_id');
    }

    // Scope to filter by state
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    // Scope to filter by suburb
    public function scopeBySuburb($query, $suburb)
    {
        return $query->where('suburb', 'like', '%' . $suburb . '%');
    }

    // Scope to filter by accommodation type
    public function scopeByAccommodationType($query, $accommodationType)
    {
        // <--- CRITICAL CHANGE HERE: Use 'accommodation_type' consistent with your $fillable
        // If 'accommodation_type' in your DB is a single string, use `where`.
        // If it's a JSON array, use `whereJsonContains`.
        // Based on your controller, it seems to be a single string for display, but filtering was on a JSON field.
        // Let's assume for now 'accommodation_type' is a single string field, and you want to filter by exact match.
        // If it's a JSON array of needed accommodations, then you'd need a different field for what they *have*.
        return $query->where('accommodation_type', $accommodationType); // Assuming it's a direct string match
    }

    // Scope to filter by disability type (searching within the JSON array)
    public function scopeByDisabilityType($query, $disabilityType)
    {
        // This checks if any of the disabilities in the array contain the search term
        return $query->whereJsonContains('disability_type', $disabilityType);
    }

    // Scope for general search (e.g., by code name or specific disability text)
    public function scopeSearch($query, $term)
    {
        $term = '%' . $term . '%';
        return $query->where('participant_code_name', 'like', $term) // <--- Corrected column name if it exists on participant
                     ->orWhereJsonContains('disability_type', $searchTerm); // <--- Use $searchTerm from the controller, not $term twice
                                                                         // Also, make sure 'participant_code_name' is indeed on this model.
    }


    // Accessor for Age - <--- CRITICAL CHANGE HERE
    public function getAgeAttribute()
    {
        // Use $this->birthday because that's the column name, and it's cast to a Carbon instance.
        return $this->birthday ? $this->birthday->age : null;
    }

    // Accessor for formatted accommodation type (optional, if 'accommodation_type' is a single value but you want to ensure consistent output)
    public function getFormattedAccommodationTypeAttribute()
    {
        return $this->accommodation_type ?? 'N/A'; // Provide a default if null
    }

    // If 'accommodation_needed' is truly a separate JSON field in your DB that stores
    // what *type* of accommodation they *need* (as an array), and 'accommodation_type'
    // is what they *currently have*, then you need to make that clear.
    // Based on your controller, `viewUnassignedParticipants` was filtering on `accommodation_needed`.
    // Let's assume you intend to display `accommodation_type` (what they currently have)
    // and potentially filter by `accommodation_needed` (what they require, if that field exists).
    // If 'accommodation_needed' exists and is a JSON array:
    public function getAccommodationNeededFormattedAttribute()
    {
        if (is_array($this->accommodation_needed) && !empty($this->accommodation_needed)) {
            return implode(', ', $this->accommodation_needed);
        }
        return 'N/A';
    }
}