<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;

class SubscriptionService
{
    /**
     * Get the current active subscription for the authenticated user.
     */
    public static function getCurrentSubscription()
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return Subscription::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('stripe_status', 'trialing')
                      ->orWhere('paypal_status', 'active')
                      ->orWhere('paypal_status', 'trialing');
            })
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            })
            ->with('plan')
            ->first();
    }

    /**
     * Check if user has an active subscription.
     */
    public static function hasActiveSubscription(): bool
    {
        return self::getCurrentSubscription() !== null;
    }

    /**
     * Check if user can access messaging features.
     */
    public static function canAccessMessaging(): bool
    {
        return self::hasActiveSubscription();
    }

    /**
     * Check if user can access accommodation listings.
     */
    public static function canAccessAccommodations(): bool
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->accommodation_listing_limit > 0;
    }

    /**
     * Check if user can access participant matching.
     */
    public static function canAccessMatching(): bool
    {
        return self::hasActiveSubscription();
    }

    /**
     * Check if user can add more participants based on their subscription limit.
     */
    public static function canAddParticipant(int $currentCount): bool
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->canAddParticipantProfiles($currentCount);
    }

    /**
     * Get the participant limit for the current subscription.
     */
    public static function getParticipantLimit(): ?int
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return null;
        }

        return $subscription->participant_profile_limit;
    }

    /**
     * Check if user can add more accommodations based on their subscription limit.
     */
    public static function canAddAccommodation(int $currentCount): bool
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->accommodation_listing_limit === null || $currentCount < $subscription->accommodation_listing_limit;
    }

    /**
     * Get the accommodation limit for the current subscription.
     */
    public static function getAccommodationLimit(): ?int
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return null;
        }

        return $subscription->accommodation_listing_limit;
    }

    /**
     * Get subscription status for display.
     */
    public static function getSubscriptionStatus(): array
    {
        $subscription = self::getCurrentSubscription();
        
        if (!$subscription) {
            return [
                'has_subscription' => false,
                'subscription' => null,
                'plan_name' => null,
                'can_access_messaging' => false,
                'can_access_accommodations' => false,
                'can_access_matching' => false,
                'trial_active' => false,
                'trial_remaining_days' => null,
                'trial_progress' => 0,
                'trial_ending_soon' => false,
                'status' => 'inactive',
                'next_billing_date' => null,
                'can_cancel' => false,
                'can_upgrade' => false,
                'auto_renew' => false,
            ];
        }

        return [
            'has_subscription' => true,
            'subscription' => $subscription, // Include the full subscription object
            'plan_name' => $subscription->plan_name,
            'display_name' => $subscription->display_name,
            'can_access_messaging' => self::canAccessMessaging(),
            'can_access_accommodations' => self::canAccessAccommodations(),
            'can_access_matching' => self::canAccessMatching(),
            'participant_limit' => $subscription->participant_profile_limit,
            'accommodation_limit' => $subscription->accommodation_listing_limit,
            'trial_active' => $subscription->inTrial(),
            'trial_remaining_days' => $subscription->trial_remaining_days,
            'trial_progress' => $subscription->trial_progress,
            'trial_ending_soon' => $subscription->isTrialEndingSoon(),
            'trial_ended' => $subscription->hasTrialEnded(),
            'status' => $subscription->status,
            'next_billing_date' => $subscription->next_billing_date?->format('Y-m-d'),
            'can_cancel' => $subscription->canBeCancelled(),
            'can_upgrade' => $subscription->canBeUpgraded(),
            'price' => $subscription->price,
            'billing_period' => $subscription->billing_period,
            'auto_renew' => $subscription->auto_renew,
        ];
    }

    /**
     * Check if user is in trial period.
     */
    public static function isInTrial(): bool
    {
        $subscription = self::getCurrentSubscription();
        return $subscription ? $subscription->inTrial() : false;
    }

    /**
     * Check if trial is ending soon.
     */
    public static function isTrialEndingSoon(): bool
    {
        $subscription = self::getCurrentSubscription();
        return $subscription ? $subscription->isTrialEndingSoon() : false;
    }

    /**
     * Get trial remaining days.
     */
    public static function getTrialRemainingDays(): ?int
    {
        $subscription = self::getCurrentSubscription();
        return $subscription ? $subscription->trial_remaining_days : null;
    }

    /**
     * Get trial progress percentage.
     */
    public static function getTrialProgress(): float
    {
        $subscription = self::getCurrentSubscription();
        return $subscription ? $subscription->trial_progress : 0;
    }

    /**
     * Check if user can start a trial.
     */
    public static function canStartTrial(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Check if user already has an active subscription
        if (self::hasActiveSubscription()) {
            return false;
        }

        // Check if user has a provider profile
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        if (!$provider) {
            return false;
        }

        // Check if user has already used a trial
        $hasUsedTrial = \App\Models\Subscription::where('user_id', $user->id)
            ->whereNotNull('trial_ends_at')
            ->exists();

        return !$hasUsedTrial;
    }

    /**
     * Check if provider can delete a participant.
     * Providers can only delete participants once per month based on subscription date.
     */
    public static function canDeleteParticipant(): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'provider') {
            return [
                'can_delete' => false,
                'message' => 'Only providers can delete participants.',
                'next_deletion_at' => null,
                'days_remaining' => null,
            ];
        }

        $provider = $user->provider;
        if (!$provider) {
            return [
                'can_delete' => false,
                'message' => 'Provider profile not found.',
                'next_deletion_at' => null,
                'days_remaining' => null,
            ];
        }

        $subscription = self::getCurrentSubscription();
        if (!$subscription || !$subscription->starts_at) {
            return [
                'can_delete' => false,
                'message' => 'No active subscription found.',
                'next_deletion_at' => null,
                'days_remaining' => null,
            ];
        }

        // Get the last deletion date or subscription start date (whichever is later)
        $lastDeletionDate = $provider->last_participant_deletion_at;
        $referenceDate = $lastDeletionDate ? $lastDeletionDate : $subscription->starts_at;
        
        // Check if a month has passed since the reference date
        $oneMonthLater = $referenceDate->copy()->addMonth();
        $canDelete = now()->greaterThanOrEqualTo($oneMonthLater);
        
        return [
            'can_delete' => $canDelete,
            'message' => $canDelete 
                ? 'You can delete a participant.' 
                : 'You can only delete participants once per month. Next deletion available on ' . $oneMonthLater->format('M d, Y'),
            'next_deletion_at' => $oneMonthLater,
            'days_remaining' => $canDelete ? 0 : max(0, now()->diffInDays($oneMonthLater, false)),
        ];
    }

    /**
     * Get available trial plans.
     */
    public static function getTrialPlans()
    {
        return \App\Models\Plan::active()
            ->whereIn('slug', ['growth', 'premium'])
            ->ordered()
            ->get();
    }

    /**
     * Start a trial for a specific plan.
     */
    public static function startTrial($planId, $trialDays = 14)
    {
        $user = Auth::user();
        $plan = \App\Models\Plan::findOrFail($planId);

        if (!self::canStartTrial()) {
            throw new \Exception('Cannot start trial. Please check your eligibility.');
        }

        $trialEndsAt = now()->addDays($trialDays);

        return \App\Models\Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'billing_period' => 'monthly',
            'price' => $plan->monthly_price,
            'participant_profile_limit' => $plan->participant_profile_limit,
            'accommodation_listing_limit' => $plan->accommodation_listing_limit,
            'trial_ends_at' => $trialEndsAt,
            'starts_at' => now(),
            'stripe_status' => 'trialing',
            'paypal_status' => 'trialing',
            'auto_renew' => true, // Default to auto-renewal
        ]);
    }

    /**
     * Convert trial to paid subscription.
     * Note: This method is now called by Stripe webhook after successful payment.
     * The stripe_id should be set from the Stripe subscription object.
     */
    public static function convertTrialToPaid($subscription, $stripeSubscriptionId = null)
    {
        $subscription->update([
            'stripe_id' => $stripeSubscriptionId,
            'stripe_status' => 'active',
            'paypal_status' => 'active',
            'trial_ends_at' => null, // Remove trial status
            'starts_at' => now(),
        ]);

        return $subscription;
    }

    /**
     * Cancel subscription.
     */
    public static function cancelSubscription($subscription)
    {
        if (!$subscription->canBeCancelled()) {
            throw new \Exception('Subscription cannot be cancelled.');
        }

        $subscription->update([
            'stripe_status' => 'cancelled',
            'paypal_status' => 'cancelled',
            'ends_at' => now()->addDays(30), // Grace period
        ]);

        return $subscription;
    }

    /**
     * Get subscription warnings and notifications.
     */
    public static function getSubscriptionWarnings(): array
    {
        $warnings = [];
        $subscription = self::getCurrentSubscription();

        if (!$subscription) {
            return $warnings;
        }

        if ($subscription->inTrial()) {
            if ($subscription->isTrialEndingSoon()) {
                $warnings[] = [
                    'type' => 'warning',
                    'message' => "Your trial ends in {$subscription->trial_remaining_days} days. Subscribe now to continue using premium features.",
                    'action' => 'subscribe',
                    'priority' => 'high'
                ];
            } else {
                $warnings[] = [
                    'type' => 'info',
                    'message' => "You're currently in a {$subscription->trial_remaining_days}-day trial. Enjoy exploring our premium features!",
                    'action' => null,
                    'priority' => 'low'
                ];
            }
        }

        if ($subscription->hasTrialEnded() && !$subscription->isActive()) {
            $warnings[] = [
                'type' => 'error',
                'message' => 'Your trial has ended. Subscribe now to regain access to premium features.',
                'action' => 'subscribe',
                'priority' => 'critical'
            ];
        }

        return $warnings;
    }
}


