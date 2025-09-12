<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'title',
        'description',
        'type',
        'address',
        'suburb',
        'state',
        'post_code',
        'num_bedrooms',
        'num_bathrooms',
        'rent_per_week',
        'is_available_for_hm',
        'amenities',
        'photos',
        'status',
        'total_vacancies',
        'current_occupancy',
    ];

    protected $casts = [
        'is_available_for_hm' => 'boolean',
        'amenities' => 'array',
        'photos' => 'array',
        'rent_per_week' => 'decimal:2',
    ];

    /**
     * Get the provider that owns the accommodation.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}