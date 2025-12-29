<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Provider;
use App\Models\Participant;
use App\Models\Property;
use App\Models\Payment;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    /**
     * Display the plan selection page.
     */
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has a provider profile
        $provider = Provider::where('user_id', $user->id)->first();
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        
        // Check current subscription status
        $currentSubscription = null;
        if ($provider) {
            $currentSubscription = Subscription::where('user_id', $user->id)
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

        // Debug logging
        Log::info('Subscription Plans Page', [
            'user_id' => $user->id,
            'plans_count' => $plans->count(),
            'has_provider' => $provider ? true : false,
            'has_subscription' => $currentSubscription ? true : false,
        ]);

        return view('subscription.plans', compact('plans', 'currentSubscription', 'provider', 'subscriptionStatus'));
    }

    /**
     * Display the subscription management page.
     */
    public function manage()
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        // Get subscription status
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        $subscription = Subscription::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('paypal_status', 'active');
            })
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            })
            ->with('plan')
            ->first();

        // Get usage statistics
        $participantCount = Participant::where('added_by_user_id', $user->id)->count();
        $accommodationCount = Property::where('provider_id', $provider->id)->count();

        return view('subscription.manage', compact('subscription', 'participantCount', 'accommodationCount', 'provider', 'subscriptionStatus'));
    }

    /**
     * Start a free trial for a plan.
     */
    public function startTrial(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'trial_days' => 'sometimes|integer|min:1|max:30',
        ]);

        try {
            $trialDays = $request->input('trial_days', 14);
            
            // Check if user can start trial
            if (!\App\Services\SubscriptionService::canStartTrial()) {
                // Instead of throwing error, return trial information for modal display
                $plan = \App\Models\Plan::findOrFail($request->plan_id);
                $trialEndsAt = now()->addDays($trialDays);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'show_subscription_modal' => true,
                        'trial_info' => [
                            'plan_id' => $plan->id,
                            'plan_name' => $plan->name,
                            'trial_days' => $trialDays,
                            'trial_ends_at' => $trialEndsAt->format('Y-m-d H:i:s'),
                            'trial_start_date' => now()->format('Y-m-d H:i:s'),
                            'charging_starts_at' => $trialEndsAt->format('Y-m-d H:i:s'),
                            'monthly_price' => $plan->monthly_price,
                            'yearly_price' => $plan->yearly_price,
                            'yearly_savings' => $plan->yearly_savings,
                            'message' => 'Complete your subscription to start your trial period.'
                        ]
                    ]);
                }
                
                return back()->with('show_subscription_modal', true)
                           ->with('trial_info', [
                               'plan_id' => $plan->id,
                               'plan_name' => $plan->name,
                               'trial_days' => $trialDays,
                               'trial_ends_at' => $trialEndsAt->format('Y-m-d H:i:s'),
                               'trial_start_date' => now()->format('Y-m-d H:i:s'),
                               'charging_starts_at' => $trialEndsAt->format('Y-m-d H:i:s'),
                               'price' => $plan->monthly_price,
                           ]);
            }
            
            $subscription = \App\Services\SubscriptionService::startTrial($request->plan_id, $trialDays);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Free trial started! You have {$trialDays} days to explore the features.",
                    'subscription' => [
                        'id' => $subscription->id,
                        'plan_name' => $subscription->plan_name,
                        'trial_ends_at' => $subscription->trial_ends_at->format('Y-m-d H:i:s'),
                        'trial_remaining_days' => $subscription->trial_remaining_days,
                    ]
                ]);
            }

            return redirect()->route('subscription.manage')->with('success', "Free trial started! You have {$trialDays} days to explore the features.");
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment form for subscription.
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        $plan = Plan::findOrFail($request->plan_id);
        $price = $request->billing_period === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        // Set Stripe API key
        $stripeSecret = config('services.stripe.secret');
        $stripeKey = config('services.stripe.key');
        
        if (!$stripeSecret || !$stripeKey) {
            return redirect()->back()->with('error', 'Payment system is not configured. Please contact support.');
        }

        Stripe::setApiKey($stripeSecret);

        try {
            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateStripeCustomer($user);

            // Create Setup Intent for payment method collection
            $setupIntent = SetupIntent::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'billing_period' => $request->billing_period,
                ],
            ]);

            return view('subscription.payment', [
                'plan' => $plan,
                'billingPeriod' => $request->billing_period,
                'price' => $price,
                'stripeKey' => $stripeKey,
                'clientSecret' => $setupIntent->client_secret,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Setup Intent Error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->with('error', 'Failed to initialize payment. Please try again.');
        }
    }

    /**
     * Subscribe to a plan - Create Stripe Checkout Session (Hosted Page).
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly',
            'use_promo' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return response()->json(['error' => 'Please complete your provider profile first.'], 400);
        }

        $plan = Plan::findOrFail($request->plan_id);
        $usePromo = $request->input('use_promo', false);
        
        // Check if promo is available and applicable
        $isPromoEligible = false;
        $promoPrice = null;
        
        if ($usePromo && $plan->slug === 'growth') {
            $foundingPartnerCount = Subscription::where('is_founding_partner', true)->count();
            if ($foundingPartnerCount < 10) {
                $isPromoEligible = true;
                $promoPrice = 399.00; // $399/month for 12 months
            }
        }
        
        // Determine pricing
        if ($isPromoEligible && $request->billing_period === 'monthly') {
            $price = $promoPrice;
        } else {
            $price = $request->billing_period === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        }

        // Determine trial period
        $trialPeriodDays = 0;
        if ($plan->slug === 'growth' || $plan->slug === 'premium') {
            $trialPeriodDays = 14;
        }

        // Set Stripe API key
        $stripeSecret = config('services.stripe.secret');
        
        if (!$stripeSecret) {
            Log::error('Stripe Secret Key is not configured');
            return response()->json(['error' => 'Payment system is not configured. Please contact support.'], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            // Create Stripe Checkout Session
            $checkoutParams = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aud',
                        'product_data' => [
                            'name' => $plan->name . ' Subscription' . ($isPromoEligible ? ' (Founding Partner)' : ''),
                            'description' => $plan->description ?? $plan->name . ' - ' . ucfirst($request->billing_period) . ' billing',
                        ],
                        'unit_amount' => $price * 100, // Convert to cents
                        'recurring' => [
                            'interval' => $request->billing_period === 'yearly' ? 'year' : 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => (string)$user->id,
                    'plan_id' => (string)$plan->id,
                    'billing_period' => $request->billing_period,
                    'is_founding_partner' => $isPromoEligible ? 'true' : 'false',
                    'promo_price' => $isPromoEligible ? (string)$promoPrice : '',
                    'trial_days' => $trialPeriodDays > 0 && !$isPromoEligible ? (string)$trialPeriodDays : '0',
                ],
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => (string)$user->id,
                        'plan_id' => (string)$plan->id,
                        'billing_period' => $request->billing_period,
                        'is_founding_partner' => $isPromoEligible ? 'true' : 'false',
                    ],
                ],
            ];

            // Add trial period if applicable
            if ($trialPeriodDays > 0 && !$isPromoEligible) {
                $checkoutParams['subscription_data']['trial_period_days'] = $trialPeriodDays;
            }

            $checkoutSession = Session::create($checkoutParams);

            // Always return JSON for AJAX requests, redirect for form submissions
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'checkout_url' => $checkoutSession->url,
                ]);
            }

            // For non-AJAX requests, redirect directly
            return redirect($checkoutSession->url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Error', [
                'message' => $e->getMessage(),
                'stripe_error' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);
            
            $errorMessage = 'Failed to create checkout session.';
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage();
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating checkout', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
            }
            
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Create Payment Intent for Stripe Elements.
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly',
            'use_promo' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return response()->json(['error' => 'Please complete your provider profile first.'], 400);
        }

        $plan = Plan::findOrFail($request->plan_id);
        
        // Validate plan has required prices BEFORE processing
        if ($request->billing_period === 'yearly' && (!$plan->yearly_price || $plan->yearly_price <= 0)) {
            Log::error('Plan does not have yearly price in createPaymentIntent', [
                'plan_id' => $plan->id,
                'plan_slug' => $plan->slug,
                'yearly_price' => $plan->yearly_price,
            ]);
            return response()->json(['error' => 'Yearly pricing is not available for this plan. Please select monthly billing.'], 400);
        }
        
        if ($request->billing_period === 'monthly' && (!$plan->monthly_price || $plan->monthly_price <= 0)) {
            Log::error('Plan does not have monthly price in createPaymentIntent', [
                'plan_id' => $plan->id,
                'plan_slug' => $plan->slug,
                'monthly_price' => $plan->monthly_price,
            ]);
            return response()->json(['error' => 'Monthly pricing is not available for this plan.'], 400);
        }
        
        $usePromo = $request->input('use_promo', false);
        
        // Check if promo is available and applicable
        $isPromoEligible = false;
        $promoPrice = null;
        
        if ($usePromo && $plan->slug === 'growth') {
            $foundingPartnerCount = Subscription::where('is_founding_partner', true)->count();
            if ($foundingPartnerCount < 10) {
                $isPromoEligible = true;
                $promoPrice = 399.00; // $399/month for 12 months
            }
        }
        
        // Determine pricing
        if ($isPromoEligible && $request->billing_period === 'monthly') {
            $price = $promoPrice;
        } else {
            $price = $request->billing_period === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        }
        
        // Final validation - ensure price is valid
        if (!$price || $price <= 0) {
            Log::error('Invalid price calculated for payment intent', [
                'plan_id' => $plan->id,
                'plan_slug' => $plan->slug,
                'billing_period' => $request->billing_period,
                'calculated_price' => $price,
                'is_promo' => $isPromoEligible,
                'plan_monthly_price' => $plan->monthly_price,
                'plan_yearly_price' => $plan->yearly_price,
            ]);
            return response()->json(['error' => 'Invalid pricing for this plan. Please contact support.'], 400);
        }

        // Determine trial period
        $trialPeriodDays = 0;
        if ($plan->slug === 'growth' || $plan->slug === 'premium') {
            $trialPeriodDays = 14;
        }

        // Set Stripe API key
        $stripeSecret = config('services.stripe.secret');
        
        if (!$stripeSecret) {
            Log::error('Stripe Secret Key is not configured');
            return response()->json(['error' => 'Payment system is not configured. Please contact support.'], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $customer = $this->getOrCreateStripeCustomer($user);

            // If it's a trial, amount is 0, otherwise calculate price
            $amount = ($trialPeriodDays > 0 && !$isPromoEligible) ? 0 : ($price * 100);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'aud',
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'billing_period' => $request->billing_period,
                    'is_founding_partner' => $isPromoEligible ? 'true' : 'false',
                    'promo_price' => $isPromoEligible ? (string)$promoPrice : '',
                    'trial_days' => (string)$trialPeriodDays,
                ],
                'capture_method' => ($trialPeriodDays > 0 && !$isPromoEligible) ? 'manual' : 'automatic',
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'plan' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $price,
                    'billing_period' => $request->billing_period,
                    'trial_days' => $trialPeriodDays,
                    'is_promo' => $isPromoEligible,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Error', [
                'message' => $e->getMessage(),
                'stripe_error' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to initialize payment. Please try again.'], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Payment Intent', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Confirm payment and create subscription (without webhooks).
     */
    public function confirmSubscription(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return response()->json(['error' => 'Please complete your provider profile first.'], 400);
        }

        $plan = Plan::findOrFail($request->plan_id);
        $stripeSecret = config('services.stripe.secret');
        
        if (!$stripeSecret) {
            return response()->json(['error' => 'Payment system is not configured.'], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            // Retrieve the payment intent with expanded payment method
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id, [
                'expand' => ['payment_method', 'latest_charge.payment_method'],
            ]);
            
            // For trials, payment might be $0 and status might be 'succeeded' or 'requires_capture'
            // For non-trials, status should be 'succeeded'
            $allowedStatuses = ['succeeded', 'requires_capture'];
            if (!in_array($paymentIntent->status, $allowedStatuses)) {
                Log::error('Payment intent not in valid status', [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ]);
                return response()->json(['error' => 'Payment was not successful. Status: ' . $paymentIntent->status], 400);
            }

            // Get metadata - convert to array for easier access
            $metadata = (array) $paymentIntent->metadata;
            $isPromo = isset($metadata['is_founding_partner']) && $metadata['is_founding_partner'] === 'true';
            $promoPrice = isset($metadata['promo_price']) && !empty($metadata['promo_price']) ? (float)$metadata['promo_price'] : null;
            $trialDays = isset($metadata['trial_days']) ? (int)$metadata['trial_days'] : 0;
            
            // Use billing_period from request (more reliable than metadata)
            $billingPeriod = $request->billing_period;
            
            // Log for debugging
            Log::info('Confirming subscription', [
                'payment_intent_id' => $paymentIntent->id,
                'plan_id' => $plan->id,
                'billing_period_request' => $request->billing_period,
                'billing_period_metadata' => $metadata['billing_period'] ?? 'not set',
                'plan_yearly_price' => $plan->yearly_price,
                'plan_monthly_price' => $plan->monthly_price,
            ]);
            
            // Determine price - use request billing_period, not metadata
            if ($isPromo && $promoPrice && $billingPeriod === 'monthly') {
                $price = $promoPrice;
            } else {
                $price = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
            }
            
            // Validate price exists
            if ($billingPeriod === 'yearly' && (!$plan->yearly_price || $plan->yearly_price <= 0)) {
                Log::error('Yearly price not available for plan', [
                    'plan_id' => $plan->id,
                    'billing_period' => $billingPeriod,
                ]);
                return response()->json(['error' => 'Yearly pricing is not available for this plan.'], 400);
            }
            
            if ($billingPeriod === 'monthly' && (!$plan->monthly_price || $plan->monthly_price <= 0)) {
                Log::error('Monthly price not available for plan', [
                    'plan_id' => $plan->id,
                    'billing_period' => $billingPeriod,
                ]);
                return response()->json(['error' => 'Monthly pricing is not available for this plan.'], 400);
            }

            // Get customer and payment method
            if (!$paymentIntent->customer) {
                return response()->json(['error' => 'Customer not found in payment intent.'], 400);
            }
            
            $customer = Customer::retrieve($paymentIntent->customer);
            
            // Get payment method - try multiple ways
            $paymentMethodId = null;
            
            // First, try direct payment_method property (may be string ID or expanded object)
            if (isset($paymentIntent->payment_method) && $paymentIntent->payment_method) {
                if (is_string($paymentIntent->payment_method)) {
                    $paymentMethodId = $paymentIntent->payment_method;
                } elseif (is_object($paymentIntent->payment_method) && isset($paymentIntent->payment_method->id)) {
                    $paymentMethodId = $paymentIntent->payment_method->id;
                }
            }
            
            // If not found and we have latest_charge, try to get from charge
            if (!$paymentMethodId && isset($paymentIntent->latest_charge)) {
                try {
                    // latest_charge might be expanded or just an ID
                    if (is_string($paymentIntent->latest_charge)) {
                        $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge, [
                            'expand' => ['payment_method'],
                        ]);
                    } else {
                        $charge = $paymentIntent->latest_charge;
                    }
                    
                    if (isset($charge->payment_method)) {
                        $paymentMethodId = is_string($charge->payment_method)
                            ? $charge->payment_method
                            : ($charge->payment_method->id ?? null);
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve charge for payment method', [
                        'charge_id' => is_string($paymentIntent->latest_charge) ? $paymentIntent->latest_charge : 'object',
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // If still not found, list payment methods for the customer and use the most recent one
            if (!$paymentMethodId && $paymentIntent->customer) {
                try {
                    $paymentMethods = \Stripe\PaymentMethod::all([
                        'customer' => $paymentIntent->customer,
                        'type' => 'card',
                        'limit' => 1,
                    ]);
                    
                    if ($paymentMethods->data && count($paymentMethods->data) > 0) {
                        $paymentMethodId = $paymentMethods->data[0]->id;
                        Log::info('Using customer default payment method', [
                            'payment_method_id' => $paymentMethodId,
                            'customer_id' => $paymentIntent->customer,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not list payment methods for customer', [
                        'customer_id' => $paymentIntent->customer,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // If still not found, try to attach payment method from the payment intent's payment method
            if (!$paymentMethodId && isset($paymentIntent->payment_method) && $paymentIntent->payment_method) {
                // The payment method might need to be attached to the customer
                try {
                    $pmId = is_string($paymentIntent->payment_method) 
                        ? $paymentIntent->payment_method 
                        : $paymentIntent->payment_method->id ?? null;
                    
                    if ($pmId) {
                        // Try to attach it to customer if not already attached
                        try {
                            $pm = \Stripe\PaymentMethod::retrieve($pmId);
                            if (!$pm->customer) {
                                $pm->attach(['customer' => $paymentIntent->customer]);
                            }
                            $paymentMethodId = $pmId;
                        } catch (\Exception $e) {
                            // If attach fails, try using it anyway
                            $paymentMethodId = $pmId;
                            Log::info('Using payment method from payment intent', [
                                'payment_method_id' => $paymentMethodId,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve payment method', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            if (!$paymentMethodId) {
                Log::error('Payment method not found in payment intent', [
                    'payment_intent_id' => $paymentIntent->id,
                    'payment_intent_status' => $paymentIntent->status,
                    'has_payment_method' => isset($paymentIntent->payment_method),
                    'has_latest_charge' => isset($paymentIntent->latest_charge),
                    'customer_id' => $paymentIntent->customer ?? 'none',
                    'payment_intent_object' => json_encode($paymentIntent->toArray()),
                ]);
                return response()->json(['error' => 'Payment method not found. Please try again.'], 400);
            }

            // Set payment method as default
            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            // Calculate trial end date if applicable
            $trialEnd = null;
            if ($trialDays > 0 && !$isPromo) {
                $trialEnd = now()->addDays($trialDays)->timestamp;
            }

            // Create Product first
            $product = \Stripe\Product::create([
                'name' => $plan->name . ' Subscription' . ($isPromo ? ' (Founding Partner)' : ''),
                'description' => $plan->description ?? $plan->name . ' - ' . ucfirst($request->billing_period) . ' billing',
            ]);

            // Create Price for the Product
            $stripePrice = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => $price * 100,
                'currency' => 'aud',
                'recurring' => [
                    'interval' => $billingPeriod === 'yearly' ? 'year' : 'month',
                ],
            ]);

            // Create subscription in Stripe using the Price ID
            $subscriptionParams = [
                'customer' => $customer->id,
                'items' => [[
                    'price' => $stripePrice->id,
                ]],
                'default_payment_method' => $paymentMethodId,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'billing_period' => $billingPeriod,
                    'is_founding_partner' => $isPromo ? 'true' : 'false',
                ],
            ];
            
            // Only add trial_end if it's set
            if ($trialEnd) {
                $subscriptionParams['trial_end'] = $trialEnd;
            }
            
            $stripeSubscription = StripeSubscription::create($subscriptionParams);
            
            Log::info('Stripe subscription created', [
                'subscription_id' => $stripeSubscription->id,
                'plan_id' => $plan->id,
                'billing_period' => $billingPeriod,
                'price' => $price,
            ]);

            // Create local subscription immediately (no webhook needed)
            $subscription = $this->createLocalSubscription($user, $plan, $stripeSubscription, $billingPeriod, [
                'is_founding_partner' => $isPromo ? 'true' : 'false',
                'promo_price' => $promoPrice ? (string)$promoPrice : '',
            ], $trialDays);
            
            Log::info('Local subscription created', [
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'billing_period' => $billingPeriod,
            ]);

            // Create payment record - always create it, even for trials (amount will be 0)
            try {
                Payment::updateOrCreate(
                    ['payment_intent_id' => $paymentIntent->id],
                    [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'status' => $paymentIntent->status === 'succeeded' ? 'succeeded' : ($paymentIntent->status === 'requires_capture' ? 'pending' : 'failed'),
                        'amount' => $paymentIntent->amount / 100, // Convert from cents (will be 0 for trials)
                        'currency' => strtolower($paymentIntent->currency),
                        'description' => $plan->name . ' Subscription - ' . ucfirst($billingPeriod) . ($trialDays > 0 && !$isPromo ? ' (Trial)' : ''),
                        'paid_at' => $paymentIntent->amount > 0 ? now() : null,
                    ]
                );
                
                Log::info('Payment record created', [
                    'payment_intent_id' => $paymentIntent->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $paymentIntent->amount / 100,
                ]);
            } catch (\Exception $e) {
                // Log but don't fail the subscription creation
                Log::error('Failed to create payment record', [
                    'error' => $e->getMessage(),
                    'payment_intent_id' => $paymentIntent->id,
                    'subscription_id' => $subscription->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription activated successfully!',
                'subscription' => [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan_name,
                    'status' => $subscription->stripe_status,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Creation Error', [
                'message' => $e->getMessage(),
                'stripe_error' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_intent_id' => $request->payment_intent_id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = 'Failed to create subscription.';
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage();
            }
            
            return response()->json(['error' => $errorMessage], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating subscription', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);
            
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Create local subscription record.
     */
    private function createLocalSubscription($user, $plan, $stripeSubscription, $billingPeriod, $metadata = [], $trialDays = 0)
    {
        $isPromo = isset($metadata['is_founding_partner']) && $metadata['is_founding_partner'] === 'true';
        $promoPrice = isset($metadata['promo_price']) && !empty($metadata['promo_price']) ? (float)$metadata['promo_price'] : null;
        
        // Determine price
        if ($isPromo && $promoPrice && $billingPeriod === 'monthly') {
            $price = $promoPrice;
        } else {
            $price = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        }
        
        // Determine trial end date
        $trialEndsAt = null;
        if ($trialDays > 0 && !$isPromo) {
            $trialEndsAt = now()->addDays($trialDays);
        } elseif ($stripeSubscription->trial_end) {
            $trialEndsAt = Carbon::createFromTimestamp($stripeSubscription->trial_end);
        }

        // Cancel existing subscription if any
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
        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'billing_period' => $billingPeriod,
            'price' => $price,
            'stripe_id' => $stripeSubscription->id,
            'stripe_status' => $stripeSubscription->status,
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
            'starts_at' => $stripeSubscription->current_period_start 
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_start) 
                : now(),
            'ends_at' => $stripeSubscription->cancel_at 
                ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) 
                : ($stripeSubscription->current_period_end 
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_end) 
                    : null),
            'is_founding_partner' => $isPromo,
            'auto_renew' => !$stripeSubscription->cancel_at_period_end,
        ]);
    }
    
    /**
     * Check if founding partner promo is available.
     */
    public function checkPromoAvailability()
    {
        $foundingPartnerCount = Subscription::where('is_founding_partner', true)->count();
        $isAvailable = $foundingPartnerCount < 10;
        
        return response()->json([
            'available' => $isAvailable,
            'remaining' => max(0, 10 - $foundingPartnerCount),
            'total' => 10,
        ]);
    }

    /**
     * Handle successful checkout.
     */
    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('subscription.plans')->with('error', 'Invalid checkout session.');
        }

        $stripeSecret = config('services.stripe.secret');
        
        if (!$stripeSecret) {
            Log::error('Stripe Secret Key is not configured');
            return redirect()->route('subscription.plans')->with('error', 'Payment system is not configured. Please contact support.');
        }

        Stripe::setApiKey($stripeSecret);

        try {
            // Retrieve the checkout session with expanded subscription and invoice
            $session = Session::retrieve($sessionId, [
                'expand' => ['subscription', 'subscription.latest_invoice', 'subscription.latest_invoice.payment_intent'],
            ]);
            
            // Check if payment was successful
            if ($session->payment_status !== 'paid') {
                Log::warning('Checkout session not paid', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                ]);
                return redirect()->route('subscription.plans')->with('error', 'Payment was not completed. Please try again.');
            }

            // Get subscription ID
            if (!$session->subscription) {
                Log::error('No subscription in checkout session', ['session_id' => $sessionId]);
                return redirect()->route('subscription.plans')->with('error', 'Subscription not found. Please contact support.');
            }

            $stripeSubscriptionId = is_string($session->subscription) 
                ? $session->subscription 
                : $session->subscription->id;
            
            // Get metadata from session - convert to array
            $metadata = (array) $session->metadata;
            $userId = isset($metadata['user_id']) ? (int)$metadata['user_id'] : null;
            $planId = isset($metadata['plan_id']) ? (int)$metadata['plan_id'] : null;
            $billingPeriod = $metadata['billing_period'] ?? 'monthly';
            $isPromo = isset($metadata['is_founding_partner']) && $metadata['is_founding_partner'] === 'true';
            $promoPrice = isset($metadata['promo_price']) && !empty($metadata['promo_price']) ? (float)$metadata['promo_price'] : null;
            $trialDays = isset($metadata['trial_days']) ? (int)$metadata['trial_days'] : 0;
            
            if (!$userId || !$planId) {
                Log::error('Missing metadata in checkout session', [
                    'session_id' => $sessionId,
                    'metadata' => $metadata,
                ]);
                return redirect()->route('subscription.plans')->with('error', 'Invalid session data. Please contact support.');
            }

            $user = \App\Models\User::find($userId);
            $plan = Plan::find($planId);
            
            if (!$user || !$plan) {
                Log::error('User or Plan not found', [
                    'user_id' => $userId,
                    'plan_id' => $planId,
                ]);
                return redirect()->route('subscription.plans')->with('error', 'User or plan not found. Please contact support.');
            }

            // Retrieve the full subscription object from Stripe
            $stripeSubscription = StripeSubscription::retrieve($stripeSubscriptionId, [
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            
            // Create local subscription immediately (direct payment processing)
            $subscription = $this->createLocalSubscription($user, $plan, $stripeSubscription, $billingPeriod, [
                'is_founding_partner' => $isPromo ? 'true' : 'false',
                'promo_price' => $promoPrice ? (string)$promoPrice : '',
            ], $trialDays);

            // Get payment information from invoice
            $paymentAmount = 0;
            $paymentIntentId = null;
            
            if (isset($stripeSubscription->latest_invoice)) {
                $invoice = $stripeSubscription->latest_invoice;
                if (is_string($invoice)) {
                    $invoice = \Stripe\Invoice::retrieve($invoice, ['expand' => ['payment_intent']]);
                }
                
                if ($invoice && $invoice->amount_paid > 0) {
                    $paymentAmount = $invoice->amount_paid / 100; // Convert from cents
                    $paymentIntentId = $invoice->payment_intent;
                    if (is_object($paymentIntentId)) {
                        $paymentIntentId = $paymentIntentId->id;
                    }
                }
            }

            // Create payment record if payment was made (not trial)
            if ($paymentAmount > 0 && $paymentIntentId) {
                try {
                    Payment::updateOrCreate(
                        ['payment_intent_id' => $paymentIntentId],
                        [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id,
                            'status' => 'succeeded',
                            'amount' => $paymentAmount,
                            'currency' => 'aud',
                            'description' => $plan->name . ' Subscription - ' . ucfirst($billingPeriod),
                            'paid_at' => now(),
                            'invoice_url' => isset($invoice->hosted_invoice_url) ? $invoice->hosted_invoice_url : null,
                            'receipt_url' => isset($invoice->receipt_url) ? $invoice->receipt_url : null,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to create payment record', [
                        'error' => $e->getMessage(),
                        'payment_intent_id' => $paymentIntentId,
                    ]);
                }
            }

            Log::info('Subscription created successfully from checkout', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'stripe_subscription_id' => $stripeSubscriptionId,
                'payment_amount' => $paymentAmount,
            ]);
            
            return redirect()->route('subscription.manage')->with('success', 'Payment successful! Your subscription is now active.');
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Retrieve Error', [
                'message' => $e->getMessage(),
                'stripe_error' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('subscription.plans')->with('error', 'Failed to verify payment. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Unexpected error processing checkout', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId,
            ]);
            return redirect()->route('subscription.plans')->with('error', 'An error occurred processing your payment. Please contact support.');
        }
    }

    /**
     * Handle canceled checkout.
     */
    public function checkoutCancel(Request $request)
    {
        return view('subscription.checkout-cancel');
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        
        $subscription = Subscription::where('user_id', $user->id)
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
            ->first();

        if (!$subscription) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No active subscription found.'], 400);
            }
            return back()->with('error', 'No active subscription found.');
        }

        // If subscription has a Stripe ID, cancel it in Stripe
        if ($subscription->stripe_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            try {
                $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_id);
                $stripeSubscription->cancel();
                
                // Update local subscription
                $subscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                ]);
            } catch (ApiErrorException $e) {
                Log::error('Stripe Cancel Error: ' . $e->getMessage());
                
                // Still update local subscription even if Stripe call fails
                $subscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => now()->addMonth(),
                ]);
            }
        } else {
            // For non-Stripe subscriptions, just update locally
        $subscription->update([
                'ends_at' => now()->addMonth(),
            'stripe_status' => 'canceled',
            'paypal_status' => 'canceled',
        ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription will be canceled at the end of your current billing period.'
            ]);
        }

        return back()->with('success', 'Subscription will be canceled at the end of your current billing period.');
    }

    /**
     * Check if user can add more participant profiles.
     */
    public function canAddParticipantProfiles()
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return response()->json(['can_add' => false, 'reason' => 'No provider profile']);
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('paypal_status', 'active');
            })
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            })
            ->first();

        if (!$subscription) {
            return response()->json(['can_add' => false, 'reason' => 'No active subscription']);
        }

        $currentCount = Participant::where('added_by_user_id', $user->id)->count();
        $canAdd = $subscription->canAddParticipantProfiles($currentCount);

        return response()->json([
            'can_add' => $canAdd,
            'current_count' => $currentCount,
            'limit' => $subscription->participant_profile_limit,
            'reason' => $canAdd ? null : 'Participant profile limit reached'
        ]);
    }

    /**
     * Check if user can add more accommodation listings.
     */
    public function canAddAccommodationListings()
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return response()->json(['can_add' => false, 'reason' => 'No provider profile']);
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('paypal_status', 'active');
            })
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            })
            ->first();

        if (!$subscription) {
            return response()->json(['can_add' => false, 'reason' => 'No active subscription']);
        }

        $currentCount = Property::where('provider_id', $provider->id)->count();
        $canAdd = $subscription->accommodation_listing_limit > $currentCount;

        return response()->json([
            'can_add' => $canAdd,
            'current_count' => $currentCount,
            'limit' => $subscription->accommodation_listing_limit,
            'reason' => $canAdd ? null : 'Accommodation listing limit reached'
        ]);
    }

    /**
     * Convert trial to paid subscription - Create Stripe Checkout Session.
     */
    public function convertTrial(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'billing_period' => 'sometimes|in:monthly,yearly',
        ]);

        try {
            $subscription = Subscription::with('plan')->findOrFail($request->subscription_id);
            
            if (!$subscription->inTrial()) {
                throw new \Exception('This subscription is not in trial period.');
            }

            $user = Auth::user();
            $plan = $subscription->plan;
            $billingPeriod = $request->input('billing_period', $subscription->billing_period ?? 'monthly');
            $price = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

            // Set Stripe API key
            $stripeSecret = config('services.stripe.secret');
            
            if (!$stripeSecret) {
                Log::error('Stripe Secret Key is not configured');
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Payment system is not configured. Please contact support.'], 500);
                }
                return back()->with('error', 'Payment system is not configured. Please contact support.');
            }

            Stripe::setApiKey($stripeSecret);

            // Create Stripe Checkout Session
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aud',
                        'product_data' => [
                            'name' => $plan->name . ' Subscription',
                            'description' => 'Convert trial to paid subscription - ' . ucfirst($billingPeriod) . ' billing',
                        ],
                        'unit_amount' => $price * 100, // Convert to cents
                        'recurring' => [
                            'interval' => $billingPeriod === 'yearly' ? 'year' : 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&trial_conversion=true',
                'cancel_url' => route('subscription.manage') . '?canceled=true',
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'subscription_id' => $subscription->id,
                    'billing_period' => $billingPeriod,
                    'trial_conversion' => 'true',
                ],
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'subscription_id' => $subscription->id,
                        'billing_period' => $billingPeriod,
                        'trial_conversion' => 'true',
                    ],
                ],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'checkout_url' => $checkoutSession->url,
                ]);
            }

            return redirect($checkoutSession->url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Trial Conversion Error', [
                'message' => $e->getMessage(),
                'stripe_error' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
                'subscription_id' => $subscription->id,
            ]);
            
            $errorMessage = 'Failed to create checkout session.';
            if (config('app.debug')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Trial Conversion Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get trial status and warnings.
     */
    public function getTrialStatus(Request $request)
    {
        $subscription = \App\Services\SubscriptionService::getCurrentSubscription();
        $warnings = \App\Services\SubscriptionService::getSubscriptionWarnings();

        if ($request->expectsJson()) {
            return response()->json([
                'subscription' => $subscription,
                'warnings' => $warnings,
                'trial_plans' => \App\Services\SubscriptionService::getTrialPlans(),
                'can_start_trial' => \App\Services\SubscriptionService::canStartTrial(),
            ]);
        }

        return view('subscription.trial-status', compact('subscription', 'warnings'));
    }

    /**
     * Simulate Stripe webhook for subscription updates (Admin/Testing only).
     * Note: This is for testing purposes only. In production, use actual Stripe webhooks.
     */
    public function simulateWebhook(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'event_type' => 'required|in:subscription.updated,subscription.cancelled,subscription.payment_failed',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);
        $eventType = $request->event_type;

        switch ($eventType) {
            case 'subscription.updated':
                $subscription->update(['stripe_status' => 'active']);
                break;
            case 'subscription.cancelled':
                $subscription->update([
                    'stripe_status' => 'cancelled',
                    'ends_at' => now()->addDays(30)
                ]);
                break;
            case 'subscription.payment_failed':
                $subscription->update(['stripe_status' => 'past_due']);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Webhook event '{$eventType}' processed successfully.",
            'subscription' => $subscription->fresh()
        ]);
    }

    /**
     * Get subscription analytics and metrics.
     */
    public function getAnalytics(Request $request)
    {
        $user = Auth::user();
        $subscription = \App\Services\SubscriptionService::getCurrentSubscription();

        if (!$subscription) {
            return response()->json(['error' => 'No active subscription found.'], 404);
        }

        $analytics = [
            'subscription' => $subscription,
            'usage' => [
                'participants' => Participant::where('provider_id', $user->provider->id)->count(),
                'accommodations' => Property::where('provider_id', $user->provider->id)->count(),
                'participant_limit' => $subscription->participant_profile_limit,
                'accommodation_limit' => $subscription->accommodation_listing_limit,
            ],
            'trial_info' => [
                'in_trial' => $subscription->inTrial(),
                'trial_remaining_days' => $subscription->trial_remaining_days,
                'trial_progress' => $subscription->trial_progress,
                'trial_ending_soon' => $subscription->isTrialEndingSoon(),
            ],
            'billing' => [
                'next_billing_date' => $subscription->next_billing_date?->format('Y-m-d'),
                'price' => $subscription->price,
                'billing_period' => $subscription->billing_period,
            ]
        ];

        if ($request->expectsJson()) {
            return response()->json($analytics);
        }

        return view('subscription.analytics', $analytics);
    }

    /**
     * Toggle auto-renewal for subscription.
     */
    public function toggleAutoRenew(Request $request)
    {
        $user = Auth::user();
        
        $subscription = Subscription::where('user_id', $user->id)
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
            ->first();

        if (!$subscription) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No active subscription found.'], 400);
            }
            return back()->with('error', 'No active subscription found.');
        }

        $autoRenew = $request->input('auto_renew', !$subscription->auto_renew);
        
        $subscription->update(['auto_renew' => $autoRenew]);

        $message = $autoRenew ? 'Auto-renewal enabled successfully!' : 'Auto-renewal disabled successfully!';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'auto_renew' => $autoRenew
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get or create Stripe customer for user.
     */
    private function getOrCreateStripeCustomer($user)
    {
        // Check if user already has a Stripe customer ID stored
        // For now, we'll create a new customer each time or store it in user metadata
        // In production, you might want to store stripe_customer_id in users table
        
        try {
            // Try to find existing customer by email
            $customers = Customer::all([
                'email' => $user->email,
                'limit' => 1,
            ]);

            if (count($customers->data) > 0) {
                return $customers->data[0];
            }
        } catch (\Exception $e) {
            // If search fails, create new customer
        }

        // Create new customer
        return Customer::create([
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);
    }

    /**
     * Create or update local subscription from Stripe subscription.
     */
    private function createOrUpdateLocalSubscription($user, $plan, $stripeSubscription, $billingPeriod, $metadata = [])
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
                'starts_at' => $stripeSubscription->current_period_start 
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_start) 
                    : now(),
                'ends_at' => $stripeSubscription->cancel_at 
                    ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) 
                    : ($stripeSubscription->current_period_end 
                        ? Carbon::createFromTimestamp($stripeSubscription->current_period_end) 
                        : null),
                'is_founding_partner' => $isPromo,
                'auto_renew' => !$stripeSubscription->cancel_at_period_end,
            ]);
        } else {
            // Update existing subscription
            $subscription->update([
                'stripe_status' => $stripeSubscription->status,
                'trial_ends_at' => $trialEndsAt,
                'starts_at' => $stripeSubscription->current_period_start 
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_start) 
                    : ($subscription->starts_at ?? now()),
                'ends_at' => $stripeSubscription->cancel_at 
                    ? Carbon::createFromTimestamp($stripeSubscription->cancel_at) 
                    : ($stripeSubscription->current_period_end 
                        ? Carbon::createFromTimestamp($stripeSubscription->current_period_end) 
                        : null),
                'auto_renew' => !$stripeSubscription->cancel_at_period_end,
            ]);
        }

        return $subscription;
    }
}