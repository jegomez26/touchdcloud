<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        'role', // 'individual', 'coordinator', 'provider'
        'profile_completed',
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
        ];
    }

    /**
     * Get the participant profile associated with the user.
     */
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    /**
     * Get the support coordinator profile associated with the user.
     */
    public function supportCoordinator()
    {
        return $this->hasOne(SupportCoordinator::class);
    }

    /**
     * Get the provider profile associated with the user.
     */
    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    /**
     * Dynamically get the specific profile based on the user's role.
     */
    public function profile()
    {
        switch ($this->role) {
            case 'individual':
                return $this->participant();
            case 'coordinator':
                return $this->supportCoordinator();
            case 'provider':
                return $this->provider();
            default:
                return null;
        }
    }
}