<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NdisBusiness extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_name',
        'abn',
        'services_offered',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'services_offered' => 'array',
    ];

    /**
     * Get the support coordinators associated with the NDIS business.
     */
    public function supportCoordinators()
    {
        return $this->hasMany(SupportCoordinator::class);
    }

    // You might also add a relationship to Providers if an NDIS Business can be a Provider
    // public function providers()
    // {
    //     return $this->hasMany(Provider::class);
    // }
}