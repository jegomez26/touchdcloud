@extends('layouts.app')

@section('title', 'Payment')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Plan Summary -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Complete Your Subscription</h1>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $plan->description }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600">${{ number_format($price, 2) }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($billingPeriod) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="payment-form">
                @csrf
                <input type="hidden" id="plan-id" value="{{ $plan->id }}">
                <input type="hidden" id="billing-period" value="{{ $billingPeriod }}">
                
                <!-- Stripe Elements Container -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                    <div id="card-element" class="p-4 border border-gray-300 rounded-lg form-control">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                </div>

                <!-- Billing Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name on Card</label>
                            <input type="text" id="cardholder-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="John Doe" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="billing-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ auth()->user()->email }}" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-button" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span id="button-text">Subscribe Now</span>
                    <span id="spinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/clover/stripe.js"></script>
<script>
const stripe = Stripe('{{ env('STRIPE_KEY') }}');
let elements;
let cardElement;
let paymentIntentClientSecret;

// Initialize payment form
document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Create payment intent
        const response = await fetch('{{ route("subscription.create-payment-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                plan_id: document.getElementById('plan-id').value,
                billing_period: document.getElementById('billing-period').value,
                use_promo: false
            })
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.error || 'Failed to initialize payment');
            return;
        }

        paymentIntentClientSecret = data.client_secret;

        // Initialize Stripe Elements
        elements = stripe.elements();
        cardElement = elements.create('card');
        // cardElement = elements.create('card', {
        //     style: {
        //         base: {
        //             fontSize: '16px',
        //             color: '#424770',
        //             '::placeholder': {
        //                 color: '#aab7c4',
        //             },
        //         },
        //         invalid: {
        //             color: '#9e2146',
        //         },
        //     },
        // });

        cardElement.mount('#card-element');

        // Handle real-time validation errors
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', handleSubmit);
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to initialize payment form. Please refresh the page.');
    }
});

async function handleSubmit(event) {
    event.preventDefault();

    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    // Disable submit button
    submitButton.disabled = true;
    buttonText.textContent = 'Processing...';
    spinner.classList.remove('hidden');

    const cardholderName = document.getElementById('cardholder-name').value;

    try {
        // Confirm payment with Stripe
        const {error, paymentIntent} = await stripe.confirmCardPayment(
            paymentIntentClientSecret,
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName,
                        email: document.getElementById('billing-email').value,
                    },
                },
            }
        );

        if (error) {
            // Show error to customer
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitButton.disabled = false;
            buttonText.textContent = 'Subscribe Now';
            spinner.classList.add('hidden');
        } else {
            // Payment succeeded - confirm subscription
            await confirmSubscription(paymentIntent.id);
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
        submitButton.disabled = false;
        buttonText.textContent = 'Subscribe Now';
        spinner.classList.add('hidden');
    }
}

async function confirmSubscription(paymentIntentId) {
    try {
        const response = await fetch('{{ route("subscription.confirm-subscription") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntentId,
                plan_id: document.getElementById('plan-id').value,
                billing_period: document.getElementById('billing-period').value,
            })
        });

        const data = await response.json();

        if (data.success) {
            // Redirect to success page
            window.location.href = '{{ route("subscription.checkout.success") }}?subscription_id=' + data.subscription.id + '&status=success';
        } else {
            alert(data.error || 'Failed to create subscription. Please contact support.');
            window.location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please contact support.');
        window.location.reload();
    }
}
</script>
@endsection

