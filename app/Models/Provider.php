<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    // Assuming the table name for providers is 'providers'
    // If your table is still 'ndis_businesses', you'll need to add:
    // protected $table = 'ndis_businesses';

    protected $fillable = [
        'user_id',
        'company_name', // Changed from 'company_name' to match the NDISBusiness/old logic, though the form sends 'company_name'
        'abn', // Added to match the data being saved in storeProvider
        'address',
        'suburb',
        'state',
        'post_code',
        'provider_code_name', // Ensure this field exists in the table
        
        'plan', // Removed if not immediately used in registration
        // 'provider_logo', // Removed if not immediately used in registration
        'contact_email', // Removed if not immediately used in registration (email is on User model)
        'contact_phone', // Removed if not immediately used in registration
    ];

    /**
     * Get the user that owns the provider profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }

    // You might add relationships for services, listings, etc., here later if needed.
}