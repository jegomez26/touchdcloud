<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'abn',
        'plan',
        'provider_code_name',
        'provider_logo',
        'contact_email',
        'contact_phone',
        'address',
        'suburb',
        'state',
        'post_code',
    ];

    /**
     * Get the user that owns the provider profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // You might add relationships for services, listings, etc., here later if needed.
}