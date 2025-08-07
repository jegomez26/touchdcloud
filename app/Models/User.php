<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'profile_completed',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_completed' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if the user's profile is completed.
     */
    public function isProfileComplete(): bool
    {
        return (bool) $this->profile_completed;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the user's account is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the participant profile associated with this user, if this user IS a participant.
     * This applies when a 'participant' role user self-registers.
     */
    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id');
    }

    /**
     * Get the participants that this user has added (e.g., a representative, coordinator, or provider adding participants).
     */
    public function participantsAdded(): HasMany
    {
        return $this->hasMany(Participant::class, 'added_by_user_id');
    }

    /**
     * Get the support coordinator specific profile if this user has the 'coordinator' role.
     * This assumes a separate 'SupportCoordinator' model/table which links back to 'users'.
     */
    public function supportCoordinatorProfile(): HasOne
    {
        return $this->hasOne(SupportCoordinator::class, 'user_id');
    }

    /**
     * Get the provider specific profile if this user has the 'provider' role.
     * This assumes a separate 'Provider' model/table which links back to 'users'.
     */
    public function providerProfile(): HasOne
    {
        return $this->hasOne(Provider::class, 'user_id');
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Polymorphic method to get the specific profile related to the user's role.
     * Returns the associated profile model or null if not applicable.
     */
    public function getRoleProfile()
    {
        switch ($this->role) {
            case 'participant':
                return $this->participant; // Accessing the relationship directly
            case 'coordinator':
                return $this->supportCoordinatorProfile; // Accessing the relationship directly
            case 'provider':
                return $this->providerProfile; // Accessing the relationship directly
            case 'admin':
                return null;
            default:
                return null;
        }
    }
}