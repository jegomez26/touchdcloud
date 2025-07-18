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
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'disability_type',
        'specific_disability',
        'accommodation_type',
        'street_address',
        'suburb',
        'state',
        'post_code',
        'is_looking_hm',
        'relative_name', // Ensure this is present
        'support_coordinator_id',
        'participant_code_name',
        'has_accommodation',
        'added_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'is_looking_hm' => 'boolean',
        'has_accommodation' => 'boolean',
    ];

    /**
     * Get the user that owns the participant profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the support coordinator associated with the participant.
     */
    public function supportCoordinator()
    {
        return $this->belongsTo(SupportCoordinator::class); // Make sure SupportCoordinator model exists
    }

    public function relative()
    {
        return $this->belongsTo(Relative::class); // If one participant has one primary relative
        // OR
        // return $this->hasMany(Relative::class); // If a participant can have multiple relatives
    }

    /**
     * Get the user who added this participant record.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}