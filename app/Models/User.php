<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait; // Correct alias, but often not needed if implementing the interface directly

class User extends Authenticatable implements MustVerifyEmail // Implement the interface
{
    use HasFactory, Notifiable;
    use MustVerifyEmailTrait; // Use the trait to get the hasVerifiedEmail() method and others

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
        'relationship_to_participant', // <--- This is on the User model
        'representative_first_name', // <--- This is on the User model
        'representative_last_name',  // <--- This is on the User model
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
     * Get the participant associated with the user.
     */
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    /**
     * Get the participants that this user (as a representative) represents.
     * Assuming 'representative_user_id' is the foreign key on the 'participants' table.
     */
    public function participantsRepresented()
    {
        return $this->hasMany(Participant::class, 'representative_user_id');
    }

    /**
     * Get the support coordinator record associated with the user.
     */
    public function supportCoordinator()
    {
        return $this->hasOne(SupportCoordinator::class);
    }

    /**
     * Get the provider record associated with the user.
     */
    public function provider()
    {
        // Assuming you have a Provider model for this relationship
        // If 'Provider' maps directly to 'NDISBusiness', you might want to rename this method.
        return $this->hasOne(Provider::class); // Make sure this Provider model exists or is imported
    }

    /**
     * Polymorphic method to get the specific profile related to the user's role.
     */
    public function profile()
    {
        switch ($this->role) {
            case 'participant':
                return $this->participant();
            case 'coordinator':
                return $this->supportCoordinator();
            case 'provider':
                return $this->provider(); // Using the provider() relationship here
            case 'admin':
                return null;
            default:
                return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Potential Redundancies / Clarifications
    |--------------------------------------------------------------------------
    */

    // This 'user()' relationship is commented out, which is good.
    // If you uncomment it, ensure it's for a specific self-referencing purpose
    // and your 'users' table has a corresponding foreign key.
    // public function user()
    // {
    //      return $this->belongsTo(User::class);
    // }
}