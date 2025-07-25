<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Foreign key to the User who *is* this participant (if self-registering)
        'first_name',
        'last_name',
        'middle_name',
        'birthday',
        'gender', // Added from migration
        'disability_type',
        'specific_disability',
        'accommodation_type',
        'approved_accommodation_type', // Added from migration
        'behavior_of_concern',       // Added from migration
        'street_address',
        'suburb',
        'state',
        'post_code',
        'is_looking_hm',
        'has_accommodation',
        'funding_amount_support_coor', // Added from migration
        'funding_amount_accommodation', // Added from migration

        // These are the relative/emergency contact fields directly on the participants table
        'relative_name',
        'relative_phone',
        'relative_email',
        'relative_relationship', // Corrected to match migration field name

        'support_coordinator_id',
        'representative_user_id', // Foreign key to the User who *represents* this participant
        'added_by_user_id',       // Foreign key to the User who *added* this participant
        'participant_code_name',
        'health_report_path',     // Added for file paths
        'assessment_path',        // Added for file paths
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_looking_hm' => 'boolean',
        'has_accommodation' => 'boolean',
        'disability_type' => 'array', // This is correct for JSON column
        'funding_amount_support_coor' => 'decimal:2', // Cast to decimal with 2 places
        'funding_amount_accommodation' => 'decimal:2', // Cast to decimal with 2 places
        // 'approved_accommodation_type' => \App\Enums\AccommodationType::class, // If you create an Enum for this
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supportCoordinator()
    {
        return $this->belongsTo(SupportCoordinator::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function representativeUser()
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }

    // You had 'participant_code_name' in $fillable, but your migration comments indicate it's removed
    // from participants and now on the 'users' table. So, it should *not* be in this model's $fillable.
    // Make sure your User model has 'participant_code_name' in its $fillable if it's there.
}