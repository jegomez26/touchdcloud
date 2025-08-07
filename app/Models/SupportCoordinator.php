<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportCoordinator extends Model
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
        'company_name',
        'abn',
        'sup_coor_code_name',
        'profile_picture_path', // Updated: Changed from 'sup_coor_image'
        'status',
        'verification_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string', // 'pending_verification', 'verified', 'rejected'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the support coordinator profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the NDIS business associated with the support coordinator.
     * IMPORTANT: This relationship 'ndisBusiness' was in your original model,
     * but 'abn' and 'company_name' are now directly on the SupportCoordinator table.
     * If 'NdisBusiness' is a separate entity that a SupportCoordinator *belongsTo*
     * (e.g., many SCs work for one NDIS Business), you'd need a foreign key here.
     * Otherwise, if it's just general company info, this relationship might be redundant.
     * I've commented it out as it doesn't align with the new migration provided.
     */
    // public function ndisBusiness(): BelongsTo
    // {
    //     return $this->belongsTo(NdisBusiness::class);
    // }

    /**
     * Get the participants that this support coordinator manages.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'support_coordinator_id');
    }

    /**
     * Get the conversations initiated by or involving this support coordinator.
     * Assuming a 'Conversation' model and a 'support_coordinator_id' on that model.
     */
    public function conversations(): HasMany
    {
        // Adjust the foreign key if it's named differently on the Conversation model
        return $this->hasMany(Conversation::class, 'support_coordinator_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional, for formatting)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the support coordinator's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the full URL to the support coordinator's profile picture.
     * Assuming pictures are stored in 'storage/app/public/profile_pictures'
     * and symlinked to 'public/storage'.
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if ($this->profile_picture_path) {
            return asset('storage/' . $this->profile_picture_path);
        }
        return null; // Or return a default image URL
    }
}