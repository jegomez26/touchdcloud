<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Stripe Webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle checkout.session.completed event.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;
        $billingPeriod = $session->metadata->billing_period ?? 'monthly';
        $isTrialConversion = isset($session->metadata->trial_conversion) && $session->metadata->trial_conversion === 'true';
        $subscriptionId = $session->metadata->subscription_id ?? null;
        $isPromo = isset($session->metadata->is_founding_partner) && $session->metadata->is_founding_partner === 'true';
        $promoPrice = isset($session->metadata->promo_price) && !empty($session->metadata->promo_price) ? (float)$session->metadata->promo_price : null;

        if (!$userId || !$planId) {
            Log::error('Stripe Webhook: Missing metadata in checkout session', ['session_id' => $session->id]);
            return;
        }

        $user = User::find($userId);
        $plan = Plan::find($planId);

        if (!$user || !$plan) {
            Log::error('Stripe Webhook: User or Plan not found', ['user_id' => $userId, 'plan_id' => $planId]);
            return;
        }

        // Get the subscription from Stripe
        $stripeSubscriptionId = $session->subscription;
        
        if (!$stripeSubscriptionId) {
            Log::error('Stripe Webhook: No subscription ID in checkout session', ['session_id' => $session->id]);
            return;
        }

        try {
            $stripeSubscription = \Stripe\Subscription::retrieve($stripeSubscriptionId);
            
            // If this is a trial conversion, update the existing subscription
            if ($isTrialConversion && $subscriptionId) {
                $existingSubscription = Subscription::find($subscriptionId);
                if ($existingSubscription && $existingSubscription->inTrial()) {
                    $this->updateSubscriptionFromStripe($existingSubscription, $stripeSubscription);
                    $existingSubscription->update([
                        'stripe_id' => $stripeSubscription->id,
                        'trial_ends_at' => null,
                        'stripe_status' => 'active',
                    ]);
                    return;
                }
            }
            
            // Otherwise, create or update subscription normally with promo metadata
            $metadata = [
                'is_founding_partner' => $isPromo ? 'true' : 'false',
                'promo_price' => $promoPrice ? (string)$promoPrice : '',
            ];
            $this->createOrUpdateSubscription($user, $plan, $stripeSubscription, $billingPeriod, $metadata);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook: Error retrieving subscription', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle subscription updated event.
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $this->updateSubscriptionFromStripe($subscription, $stripeSubscription);
        }
    }

    /**
     * Handle subscription deleted event.
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'stripe_status' => 'canceled',
                'ends_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);
        }
    }

    /**
     * Handle invoice payment succeeded.
     */
    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $stripeSubscriptionId = $invoice->subscription;
        
        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            // Create payment record
            Payment::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'payment_intent_id' => $invoice->payment_intent,
                'status' => 'succeeded',
                'amount' => $invoice->amount_paid / 100, // Convert from cents
                'currency' => strtoupper($invoice->currency),
                'description' => $invoice->description ?? 'Subscription payment',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_url' => $invoice->hosted_invoice_url,
                ],
                'paid_at' => Carbon::createFromTimestamp($invoice->created),
                'invoice_url' => $invoice->hosted_invoice_url,
                'receipt_url' => $invoice->receipt_url,
            ]);

            // Update subscription status
            $subscription->update([
                'stripe_status' => 'active',
            ]);
        }
    }

    /**
     * Handle invoice payment failed.
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        $stripeSubscriptionId = $invoice->subscription;
        
        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            $subscription->update([
                'stripe_status' => 'past_due',
            ]);
        }
    }

    /**
     * Create or update subscription from Stripe data.
     */
    protected function createOrUpdateSubscription($user, $plan, $stripeSubscription, $billingPeriod, $metadata = [])
    {
        // Check for promo pricing in metadata
        $isPromo = isset($metadata['is_founding_partner']) && $metadata['is_founding_partner'] === 'true';
        $promoPrice = isset($metadata['promo_price']) && !empty($metadata['promo_price']) ? (float)$metadata['promo_price'] : null;
        
        // Determine price
        if ($isPromo && $promoPrice && $billingPeriod === 'monthly') {
            $price = $promoPrice;
        } else {
            $price = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        }
        
        // Determine trial period
        $trialEndsAt = null;
        if ($plan->slug === 'growth' || $plan->slug === 'premium') {
            if ($stripeSubscription->trial_end) {
                $trialEndsAt = Carbon::createFromTimestamp($stripeSubscription->trial_end);
            } elseif (!$isPromo) {
                // If no trial in Stripe but plan should have trial, set it
                $trialEndsAt = now()->addDays(14);
            }
        }

        // Check if subscription already exists
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            // Check if user has an existing subscription to cancel
            $existingSubscription = Subscription::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('stripe_status', 'active')
                          ->orWhere('stripe_status', 'trialing');
                })
                ->where(function($query) {
                    $query->where('ends_at', '>', now())
                          ->orWhereNull('ends_at');
                })
                ->first();

            if ($existingSubscription) {
                $existingSubscription->update([
                    'ends_at' => now(),
                    'stripe_status' => 'canceled',
                ]);
            }

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'plan_name' => $plan->name,
                'plan_slug' => $plan->slug,
                'billing_period' => $billingPeriod,
                'price' => $price,
                'stripe_id' => $stripeSubscription->id,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price_id' => $stripeSubscription->items->data[0]->price->id ?? null,
                'payment_gateway' => 'stripe',
                'participant_profile_limit' => $plan->participant_profile_limit,
                'accommodation_listing_limit' => $plan->accommodation_listing_limit,
                'has_advanced_matching_filters' => $plan->has_advanced_matching_filters,
                'has_phone_support' => $plan->has_phone_support,
                'has_early_feature_access' => $plan->has_early_feature_access,
                'has_dedicated_support' => $plan->has_dedicated_support,
                'has_custom_onboarding' => $plan->has_custom_onboarding,
                'includes_property_listings' => $plan->includes_property_listings,
                'has_featured_placement' => $plan->has_featured_placement,
                'trial_ends_at' => $trialEndsAt,
                'starts_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'ends_at' => $stripeSubscription->cancel_at ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null,
                'is_founding_partner' => $isPromo,
                'auto_renew' => !$stripeSubscription->cancel_at_period_end,
            ]);
        } else {
            // Update existing subscription
            $subscription->update([
                'stripe_status' => $stripeSubscription->status,
                'trial_ends_at' => $trialEndsAt,
                'starts_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'ends_at' => $stripeSubscription->cancel_at ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null,
                'auto_renew' => !$stripeSubscription->cancel_at_period_end,
            ]);
        }

        return $subscription;
    }

    /**
     * Update subscription from Stripe subscription object.
     */
    protected function updateSubscriptionFromStripe($subscription, $stripeSubscription)
    {
        $subscription->update([
            'stripe_status' => $stripeSubscription->status,
            'starts_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'ends_at' => $stripeSubscription->cancel_at ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null,
            'auto_renew' => !$stripeSubscription->cancel_at_period_end,
        ]);
    }
}

