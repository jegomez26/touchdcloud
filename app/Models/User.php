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
        'privileges',
        'profile_completed',
        'is_active',
        'is_representative', // Indicates if the user is registering on behalf of someone else
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
            'privileges' => 'array',
            'profile_completed' => 'boolean',
            'is_active' => 'boolean',
            'is_representative' => 'boolean', // Cast to boolean for easier checks
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

    /**
     * Check if the user has a specific privilege.
     */
    public function hasPrivilege(string $privilege): bool
    {
        // Super admins have all privileges
        if ($this->role === 'super_admin') {
            return true;
        }

        // Check if user has the specific privilege
        return $this->privileges && in_array($privilege, $this->privileges);
    }

    /**
     * Check if the user has any of the specified privileges.
     */
    public function hasAnyPrivilege(array $privileges): bool
    {
        // Super admins have all privileges
        if ($this->role === 'super_admin') {
            return true;
        }

        // Check if user has any of the specified privileges
        return $this->privileges && !empty(array_intersect($privileges, $this->privileges));
    }

    /**
     * Check if the user has all of the specified privileges.
     */
    public function hasAllPrivileges(array $privileges): bool
    {
        // Super admins have all privileges
        if ($this->role === 'super_admin') {
            return true;
        }

        // Check if user has all of the specified privileges
        return $this->privileges && empty(array_diff($privileges, $this->privileges));
    }

    /**
     * Get the user's privileges as a readable array.
     */
    public function getReadablePrivileges(): array
    {
        if (!$this->privileges) {
            return [];
        }

        return array_map(function($privilege) {
            return ucfirst(str_replace('_', ' ', $privilege));
        }, $this->privileges);
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
    public function supportCoordinator(): HasOne
    {
        return $this->hasOne(SupportCoordinator::class, 'user_id');
    }

    public function participantsRepresented()
    {
        return $this->hasMany(Participant::class, 'representative_user_id');
    }

    /**
     * Get the provider specific profile if this user has the 'provider' role.
     * This assumes a separate 'Provider' model/table which links back to 'users'.
     */
    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class, 'user_id');
    }

    /**
     * Get the payments made by this user.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
                return $this->supportCoordinator; // Accessing the relationship directly
            case 'provider':
                return $this->provider; // Accessing the relationship directly
            case 'admin':
                return null;
            default:
                return null;
        }
    }
}