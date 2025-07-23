<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait; // Correct alias


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use MustVerifyEmailTrait; // <-- Use the ALIAS for the trait here

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
        'is_representative',
        'relationship_to_participant',
        'representative_first_name',
        'representative_last_name',
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
            'is_representative' => 'boolean',
        ];
    }

    // You should also add an `isProfileComplete` method to your User model
    // as it's being used in your AuthenticatedSessionController.
    public function isProfileComplete()
    {
        return (bool) $this->profile_completed;
    }

    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function participantsRepresented()
    {
        return $this->hasMany(Participant::class, 'representative_user_id');
    }

    public function supportCoordinator()
    {
        return $this->hasOne(SupportCoordinator::class);
    }

    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    // This method seems redundant as 'user()' typically implies belongsTo.
    // If a User 'belongs to' another User, clarify this relationship.
    // If it's meant to be polymorphic, it would be set up differently.
    // For now, it's not directly causing the `hasVerifiedEmail` error but keep an eye on it.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        switch ($this->role) {
            case 'participant':
                return $this->participant();
            case 'representative':
                return $this->participantsRepresented();
            case 'coordinator':
                return $this->supportCoordinator();
            case 'provider':
                return $this->provider();
            default:
                return null;
        }
    }
}