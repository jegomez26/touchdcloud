<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'provider_notes',
        'tended_at',
    ];

    protected $casts = [
        'tended_at' => 'datetime',
    ];

    /**
     * Get the property that the enquiry is about.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the provider through the property relationship.
     */
    public function provider()
    {
        return $this->hasOneThrough(Provider::class, Property::class, 'id', 'id', 'property_id', 'provider_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by property.
     */
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }
}