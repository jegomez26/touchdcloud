<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'participant_id',
        'full_name',
        'relationship_to_participant',
        'organisation',
        'phone_number',
        'email_address',
        'preferred_method_of_contact',
        'consent_to_speak_on_behalf',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for simple strings or enums in this model currently.
        // If 'consent_to_speak_on_behalf' was stored as a boolean (0/1) instead of enum,
        // it would be cast as 'boolean'.
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the participant that this contact person is associated with.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional, for formatting)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted contact method.
     */
    public function getPreferredContactMethodFormattedAttribute(): string
    {
        return $this->preferred_method_of_contact ?? 'N/A';
    }

    /**
     * Get the consent status formatted.
     */
    public function getConsentToSpeakFormattedAttribute(): string
    {
        return $this->consent_to_speak_on_behalf ?? 'Unspecified';
    }
}