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
        'rent_per_week' => 'decimal:2',
    ];

    /**
     * Get the photos attribute, ensuring it's always an array.
     */
    public function getPhotosAttribute($value)
    {
        if (is_string($value)) {
            // Handle double-encoded JSON
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return $decoded ?? [];
        }
        return $value ?? [];
    }

    /**
     * Get the amenities attribute, ensuring it's always an array.
     */
    public function getAmenitiesAttribute($value)
    {
        if (is_string($value)) {
            // Handle double-encoded JSON
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return $decoded ?? [];
        }
        return $value ?? [];
    }

    /**
     * Get the provider that owns the accommodation.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the enquiries for this property.
     */
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }
}