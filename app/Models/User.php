<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Extends Authenticatable
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

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
        'role', // Now includes 'participant', 'representative', 'support_coordinator', 'provider', 'admin'
        'profile_completed',
        'is_representative',
        'relationship_to_participant',
        // ADD THESE TWO FIELDS if you intend to store them directly on the User model
        'representative_first_name', // Added
        'representative_last_name',  // Added
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

    // ... (rest of your relationships and profile method)
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function participantsRepresented() // Renamed for clarity
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