@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Cancel Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>

            <!-- Cancel Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Cancelled</h1>
            <p class="text-lg text-gray-600 mb-8">
                Your payment was cancelled. No charges have been made to your account.
            </p>

            <!-- Information -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">What happened?</h2>
                <p class="text-gray-700 mb-4">
                    You cancelled the payment process before completing your subscription. Your account remains unchanged, and you can try again at any time.
                </p>
                <p class="text-gray-700">
                    If you experienced any issues during checkout, please contact our support team for assistance.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    Try Again
                </a>
                <a href="{{ route('provider.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

