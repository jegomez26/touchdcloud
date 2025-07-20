<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // REMOVE THESE, as per your updated User model:
        // 'first_name',
        // 'last_name',
        'middle_name',
        'birthday',
        'disability_type',
        'specific_disability',
        'accommodation_type',
        'street_address',
        'suburb',
        'state',
        'post_code',
        'is_looking_hm',
        'relative_name', // This is a string for now, but could be an FK later
        'support_coordinator_id',
        'participant_code_name',
        'has_accommodation',
        'added_by_user_id',
        'representative_user_id', // ADD THIS if it's not there
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_looking_hm' => 'boolean',
        'has_accommodation' => 'boolean',
    ];

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
}