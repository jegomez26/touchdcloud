<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'participant_profile_limit',
        'accommodation_listing_limit',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'participant_profile_limit' => 'integer',
        'accommodation_listing_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order plans by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the price for a specific billing period.
     */
    public function getPriceForPeriod(string $period): float
    {
        return $period === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    /**
     * Get the yearly savings compared to monthly billing.
     */
    public function getYearlySavingsAttribute(): float
    {
        $monthlyTotal = $this->monthly_price * 12;
        return $monthlyTotal - $this->yearly_price;
    }

    /**
     * Get the yearly savings percentage.
     */
    public function getYearlySavingsPercentageAttribute(): float
    {
        $monthlyTotal = $this->monthly_price * 12;
        return round(($this->yearly_savings / $monthlyTotal) * 100, 1);
    }

    /**
     * Check if the plan allows a certain number of participant profiles.
     */
    public function canAddParticipantProfiles(int $currentCount): bool
    {
        return $this->participant_profile_limit === null || $currentCount < $this->participant_profile_limit;
    }

    /**
     * Check if the plan allows a certain number of accommodation listings.
     */
    public function canAddAccommodationListings(int $currentCount): bool
    {
        return $this->accommodation_listing_limit === null || $currentCount < $this->accommodation_listing_limit;
    }

    /**
     * Check if the plan includes accommodation listings.
     */
    public function includesAccommodationListings(): bool
    {
        return $this->accommodation_listing_limit > 0;
    }

    /**
     * Get the formatted price for display.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->monthly_price, 0);
    }

    /**
     * Get the formatted yearly price for display.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return '$' . number_format($this->yearly_price, 0);
    }
}

