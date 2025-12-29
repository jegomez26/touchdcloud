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
        'type', // 'sc_to_participant', 'provider_to_sc', 'participant_to_participant', etc.
        'support_coordinator_id', // Foreign key to support_coordinators table
        'participant_id',         // Foreign key to participants table (target participant)
        'matching_for_participant_id', // Foreign key to participants table (participant being matched for)
        'sender_participant_id',  // Foreign key to participants table (sender participant for participant-to-participant conversations)
        'provider_id',            // Foreign key to providers table
        'initiator_user_id',      // Foreign key to users table (who started the conversation)
        'recipient_user_id',      // Foreign key to users table (who receives the conversation)
        'initiator_participant_id', // Foreign key to participants table (initiator's participant)
        'recipient_participant_id', // Foreign key to participants table (recipient's participant)
        'last_message_at',
        // 'subject', // <-- Add this if you want to store the subject here
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

    public function matchingForParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'matching_for_participant_id');
    }

    public function senderParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_participant_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function initiatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function initiatorParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'initiator_participant_id');
    }

    public function recipientParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'recipient_participant_id');
    }
    public function scopeForSupportCoordinator($query, $supportCoordinatorId)
    {
        return $query->where('support_coordinator_id', $supportCoordinatorId);
    }

    public function scopeForParticipant($query, $participantId)
    {
        return $query->where('participant_id', $participantId);
    }
}