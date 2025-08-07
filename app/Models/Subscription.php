<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'plan_name',
        'plan_slug',
        'billing_period',
        'price',
        'stripe_id',
        'stripe_status',
        'stripe_price_id',
        'paypal_id',
        'paypal_status',
        'paypal_plan_id',
        'payment_gateway',
        'participant_profile_limit',
        'has_advanced_matching_filters',
        'has_phone_support',
        'has_early_feature_access',
        'has_dedicated_support',
        'has_custom_onboarding',
        'includes_property_listings',
        'has_featured_placement',
        'trial_ends_at',
        'ends_at',
        'starts_at',
        'is_founding_partner',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'participant_profile_limit' => 'integer',
        'has_advanced_matching_filters' => 'boolean',
        'has_phone_support' => 'boolean',
        'has_early_feature_access' => 'boolean',
        'has_dedicated_support' => 'boolean',
        'has_custom_onboarding' => 'boolean',
        'includes_property_listings' => 'boolean',
        'has_featured_placement' => 'boolean',
        'is_founding_partner' => 'boolean',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'starts_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active subscriptions.
     * Note: This might need more complex logic depending on gateway status.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('ends_at', '>', Carbon::now()) // Not ended yet
              ->orWhereNull('ends_at'); // No end date (e.g., indefinitely active)
        })
        ->where(function ($q) {
            $q->where('stripe_status', 'active') // For Stripe
              ->orWhere('paypal_status', 'active'); // For PayPal
        });
        // You might need to refine this based on actual gateway statuses
    }

    /**
     * Scope a query to only include trialing subscriptions.
     */
    public function scopeTrialing($query)
    {
        return $query->whereNotNull('trial_ends_at')
                     ->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Scope a query to find subscriptions by plan slug.
     */
    public function scopeByPlan($query, string $planSlug)
    {
        return $query->where('plan_slug', $planSlug);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods / Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the subscription is currently active.
     */
    public function isActive(): bool
    {
        // Add more robust checks based on stripe_status/paypal_status
        return $this->ends_at === null || $this->ends_at->isFuture();
    }

    /**
     * Check if the subscription is currently in trial.
     */
    public function inTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    /**
     * Get the remaining trial days.
     */
    public function getTrialRemainingDaysAttribute(): ?int
    {
        if ($this->inTrial()) {
            return $this->trial_ends_at->diffInDays(Carbon::now());
        }
        return null;
    }

    /**
     * Determine if the subscription is eligible for a specific feature (e.g., advanced filters).
     */
    public function hasAdvancedMatchingFilters(): bool
    {
        return $this->has_advanced_matching_filters;
    }

    /**
     * Determine if the subscription includes property listings.
     */
    public function includesPropertyListings(): bool
    {
        return $this->includes_property_listings;
    }

    /**
     * Check if the subscription allows a certain number of participant profiles.
     * Returns true if `profile_limit` is null (unlimited) or if `count` is within the limit.
     */
    public function canAddParticipantProfiles(int $currentCount): bool
    {
        return $this->participant_profile_limit === null || $currentCount < $this->participant_profile_limit;
    }

    /**
     * Get the formatted billing period.
     */
    public function getBillingPeriodFormattedAttribute(): string
    {
        return ucfirst($this->billing_period);
    }
}