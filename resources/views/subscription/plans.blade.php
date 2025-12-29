@extends('layouts.app')

@section('title', 'Choose Your Plan')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Session Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Plan</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Select the perfect plan for your provider needs. All plans include participant matching and messaging features.
            </p>
        </div>

        <!-- Current Subscription Alert -->
        @if(isset($currentSubscription) && $currentSubscription)
        <div class="mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Current Subscription: {{ $currentSubscription->plan_name }}
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>You currently have an active subscription. <a href="{{ route('subscription.manage') }}" class="font-medium underline">Manage your subscription</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Debug Info (remove in production) -->
        @if(config('app.debug'))
        <div class="mb-4 p-4 bg-gray-100 rounded text-sm">
            <p>Debug: Plans count = {{ isset($plans) ? $plans->count() : 'not set' }}</p>
            <p>Debug: Current Subscription = {{ isset($currentSubscription) && $currentSubscription ? $currentSubscription->plan_name : 'none' }}</p>
            <p>Debug: Provider = {{ isset($provider) && $provider ? 'exists' : 'none' }}</p>
        </div>
        @endif

        <!-- Plans Grid -->
        @if(isset($plans) && $plans && $plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($plans as $plan)
            <div class="relative bg-white rounded-2xl shadow-lg border-2 {{ $plan->is_featured ? 'border-blue-500' : 'border-gray-200' }} {{ $plan->is_featured ? 'ring-2 ring-blue-500 ring-opacity-50' : '' }}">
                @if($plan->is_featured)
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
                </div>
                @endif

                <div class="p-8">
                    <!-- Plan Header -->
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600">{{ $plan->description }}</p>
                    </div>

                    <!-- Pricing -->
                    <div class="text-center mb-6">
                        <div class="mb-4">
                            <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-100">
                                <button type="button" onclick="setBillingPeriod({{ $plan->id }}, 'monthly')" 
                                        id="billing-monthly-{{ $plan->id }}"
                                        class="px-4 py-2 rounded-md text-sm font-medium billing-toggle active">
                                    Monthly
                                </button>
                                <button type="button" onclick="setBillingPeriod({{ $plan->id }}, 'yearly')" 
                                        id="billing-yearly-{{ $plan->id }}"
                                        class="px-4 py-2 rounded-md text-sm font-medium billing-toggle">
                                    Yearly
                                </button>
                            </div>
                        </div>
                        <div class="flex items-baseline justify-center">
                            <span class="text-5xl font-bold text-gray-900" id="price-{{ $plan->id }}">${{ number_format($plan->monthly_price, 0) }}</span>
                            <span class="text-xl text-gray-500 ml-1" id="period-{{ $plan->id }}">/month</span>
                        </div>
                        <div class="text-sm text-gray-500 mt-1" id="savings-{{ $plan->id }}" style="display: none;">
                            <span class="text-green-600 font-medium" id="savings-amount-{{ $plan->id }}">Save ${{ number_format($plan->yearly_savings, 0) }}</span>
                        </div>
                        <input type="hidden" name="billing_period" id="billing-period-{{ $plan->id }}" value="monthly">
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 mb-8">
                        @if(is_array($plan->features) && count($plan->features) > 0)
                            @foreach($plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                            @endforeach
                        @else
                            <li class="text-gray-500 text-sm">No features listed</li>
                        @endif
                    </ul>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        @if(isset($currentSubscription) && $currentSubscription)
                            @if($currentSubscription->plan_id === $plan->id)
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                    Current Plan
                                </button>
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                    Upgrade/Downgrade (Coming Soon)
                                </button>
                            @endif
                        @else
                            @if(in_array($plan->slug, ['growth', 'premium']))
                                <button type="button" onclick="startTrial({{ $plan->id }})" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors mb-3">
                                        Start 14-Day Free Trial
                                    </button>
                            @endif
                            
                            <button type="button" onclick="subscribeToPlan({{ $plan->id }}, '{{ $plan->slug }}')" class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                                Subscribe Now
                            </button>
                            
                            @if($plan->slug === 'growth')
                                <button type="button" onclick="subscribeWithPromo({{ $plan->id }})" class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-purple-700 transition-colors" id="promo-btn-{{ $plan->id }}" style="display: none;">
                                    Get Founding Partner Price ($399/mo)
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No subscription plans available at the moment. Please check back later.</p>
        </div>
        @endif

        <!-- Founding Partner Offer -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-8 text-center" id="promo-section">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Founding Partner Offer</h3>
            <p class="text-lg text-gray-700 mb-6">
                Be among the first 10 providers and get the Growth plan for just <span class="font-bold text-purple-600">$399/month</span> for 12 months!
            </p>
            <div class="text-sm text-gray-600 mb-4">
                <p id="promo-status">Checking availability...</p>
            </div>
            <div class="text-xs text-gray-500" id="promo-countdown"></div>
        </div>
    </div>
</div>

<script>
// Prevent auto-triggering of modals from session messages
document.addEventListener('DOMContentLoaded', function() {
    // Don't auto-show modals based on session messages
    // Session messages are already displayed as alerts above
    if (window.modalManager) {
        // Explicitly don't trigger modals
        console.log('Modal manager found, but not auto-triggering modals');
    }
    
    // Check promo availability
    checkPromoAvailability();
});

const planPrices = {
    @if(isset($plans) && $plans && $plans->count() > 0)
    @foreach($plans as $plan)
    {{ $plan->id }}: {
        monthly: {{ $plan->monthly_price }},
        yearly: {{ $plan->yearly_price }},
        savings: {{ $plan->yearly_savings }},
        slug: '{{ $plan->slug }}'
    }@if(!$loop->last),@endif
    @endforeach
    @endif
};

const billingPeriods = {}; // Track billing period for each plan

function setBillingPeriod(planId, period) {
    const monthlyBtn = document.getElementById('billing-monthly-' + planId);
    const yearlyBtn = document.getElementById('billing-yearly-' + planId);
    const priceEl = document.getElementById('price-' + planId);
    const periodEl = document.getElementById('period-' + planId);
    const savingsEl = document.getElementById('savings-' + planId);
    
    if (!planPrices[planId]) return;
    
    // Store billing period
    billingPeriods[planId] = period;
    
    // Remove active class from both buttons
    monthlyBtn.classList.remove('active');
    yearlyBtn.classList.remove('active');
    
    // Add active class to selected button
    if (period === 'monthly') {
        monthlyBtn.classList.add('active');
        priceEl.textContent = '$' + planPrices[planId].monthly.toLocaleString();
        periodEl.textContent = '/month';
        savingsEl.style.display = 'none';
    } else {
        yearlyBtn.classList.add('active');
        priceEl.textContent = '$' + planPrices[planId].yearly.toLocaleString();
        periodEl.textContent = '/year';
        savingsEl.style.display = 'block';
        const savingsAmountEl = document.getElementById('savings-amount-' + planId);
        if (savingsAmountEl) {
            savingsAmountEl.textContent = 'Save $' + planPrices[planId].savings.toLocaleString();
        }
    }
}

// Initialize billing periods
@if(isset($plans) && $plans && $plans->count() > 0)
@foreach($plans as $plan)
billingPeriods[{{ $plan->id }}] = 'monthly';
@endforeach
@endif

async function subscribeToPlan(planId, planSlug) {
    const billingPeriod = billingPeriods[planId] || 'monthly';
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = 'Redirecting to checkout...';
    
    try {
        const response = await fetch('{{ route("subscription.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                plan_id: planId,
                billing_period: billingPeriod,
                use_promo: false
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'Network error occurred' }));
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.checkout_url) {
            // Immediately redirect to Stripe Checkout (hosted page)
            window.location.href = data.checkout_url;
        } else {
            alert(data.error || 'An error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred: ' + (error.message || 'Please try again.'));
        button.disabled = false;
        button.textContent = originalText;
    }
}

async function subscribeWithPromo(planId) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = 'Redirecting to checkout...';
    
    try {
        const response = await fetch('{{ route("subscription.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                plan_id: planId,
                billing_period: 'monthly',
                use_promo: true
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'Network error occurred' }));
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.checkout_url) {
            // Immediately redirect to Stripe Checkout (hosted page)
            window.location.href = data.checkout_url;
        } else {
            alert(data.error || 'An error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred: ' + (error.message || 'Please try again.'));
        button.disabled = false;
        button.textContent = originalText;
    }
}

async function startTrial(planId) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = 'Starting Trial...';
    
    try {
        const response = await fetch('{{ route("subscription.trial") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                plan_id: planId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Trial started successfully!');
            window.location.reload();
        } else {
            alert(data.error || 'An error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        button.disabled = false;
        button.textContent = originalText;
    }
}

async function checkPromoAvailability() {
    try {
        const response = await fetch('{{ route("subscription.promo.check") }}');
        const data = await response.json();
        
        const promoStatus = document.getElementById('promo-status');
        const promoCountdown = document.getElementById('promo-countdown');
        
        if (data.available) {
            promoStatus.textContent = `${data.remaining} spots remaining!`;
            promoCountdown.textContent = `Only ${data.remaining} of ${data.total} founding partner spots left`;
            
            // Show promo buttons for Growth plan
            @if(isset($plans) && $plans && $plans->count() > 0)
            @foreach($plans as $plan)
            @if($plan->slug === 'growth')
            const promoBtn = document.getElementById('promo-btn-{{ $plan->id }}');
            if (promoBtn) {
                promoBtn.style.display = 'block';
            }
            @endif
            @endforeach
            @endif
        } else {
            promoStatus.textContent = 'All founding partner spots have been claimed.';
            promoCountdown.textContent = '';
        }
    } catch (error) {
        console.error('Error checking promo availability:', error);
        document.getElementById('promo-status').textContent = 'Unable to check availability.';
    }
}
</script>


<style>
.billing-toggle {
    transition: all 0.2s;
    color: #6b7280;
}
.billing-toggle.active {
    background-color: white;
    color: #111827;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.billing-option {
    transition: all 0.2s;
}
</style>
@endsection

