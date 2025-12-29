<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'support_coordinator_id',
        'seeking_participant_id',
        'matched_participant_id',
        'compatibility_score',
        'compatibility_factors',
        'match_details',
        'status',
        'last_viewed_at',
        'contacted_at',
        'notes',
        'conversation_id',
    ];

    protected $casts = [
        'compatibility_factors' => 'array',
        'match_details' => 'array',
        'last_viewed_at' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    /**
     * Get the provider that owns this match
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the support coordinator that owns this match
     */
    public function supportCoordinator()
    {
        return $this->belongsTo(SupportCoordinator::class);
    }

    /**
     * Get the participant seeking a match
     */
    public function seekingParticipant()
    {
        return $this->belongsTo(Participant::class, 'seeking_participant_id');
    }

    /**
     * Get the matched participant
     */
    public function matchedParticipant()
    {
        return $this->belongsTo(Participant::class, 'matched_participant_id');
    }

    /**
     * Get the conversation associated with this match
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Scope to get matches for a specific participant
     */
    public function scopeForParticipant($query, $participantId)
    {
        return $query->where('seeking_participant_id', $participantId);
    }

    /**
     * Scope to get matches by provider
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Scope to get matches by support coordinator
     */
    public function scopeForSupportCoordinator($query, $supportCoordinatorId)
    {
        return $query->where('support_coordinator_id', $supportCoordinatorId);
    }

    /**
     * Scope to get matches by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get high-quality matches
     */
    public function scopeHighQuality($query, $minScore = 70)
    {
        return $query->where('compatibility_score', '>=', $minScore);
    }

    /**
     * Mark match as contacted
     */
    public function markAsContacted()
    {
        $this->update([
            'status' => 'contacted',
            'contacted_at' => now(),
        ]);
    }

    /**
     * Update match status
     */
    public function updateStatus($status, $notes = null)
    {
        $this->update([
            'status' => $status,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark as viewed
     */
    public function markAsViewed()
    {
        $this->update(['last_viewed_at' => now()]);
    }
}