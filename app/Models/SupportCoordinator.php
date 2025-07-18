<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportCoordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'ndis_business_id',
        'sup_coor_code_name',
        'sup_coor_image',
        'status',
        'verification_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string', // or an enum cast if you prefer, but 'string' is fine for enum columns
    ];

    /**
     * Get the user that owns the support coordinator profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the NDIS business associated with the support coordinator.
     */
    public function ndisBusiness()
    {
        return $this->belongsTo(NdisBusiness::class); // Ensure NdisBusiness model exists
    }

    /**
     * Get the participants that this support coordinator manages.
     */
    public function participants()
    {
        return $this->hasMany(Participant::class, 'support_coordinator_id');
    }
}