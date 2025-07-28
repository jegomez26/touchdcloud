<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // 'sc_to_participant', 'provider_to_sc', etc.
        'support_coordinator_id', // Foreign key to support_coordinators table
        'participant_id',         // Foreign key to participants table
        'provider_id',            // Foreign key to providers table
        'last_message_at',
        'subject', // <-- Add this if you want to store the subject here
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function supportCoordinator(): BelongsTo
    {
        return $this->belongsTo(SupportCoordinator::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}