<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
        'sender_participant_id',
        'receiver_participant_id',
        'status',
        'message',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the user who sent the request
     */
    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Get the user who received the request
     */
    public function receiverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    /**
     * Get the participant profile of the sender
     */
    public function senderParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_participant_id');
    }

    /**
     * Get the participant profile of the receiver
     */
    public function receiverParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'receiver_participant_id');
    }

    /**
     * Scope to get pending requests for a user
     */
    public function scopePendingForUser($query, $userId)
    {
        return $query->where('receiver_user_id', $userId)->where('status', 'pending');
    }

    /**
     * Scope to get sent requests by a user
     */
    public function scopeSentByUser($query, $userId)
    {
        return $query->where('sender_user_id', $userId);
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
