@extends('company.provider-db')

@section('title', 'Manage Subscription')

@section('main-content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Subscription Management</h1>
            <p class="text-gray-600 mt-2">Manage your subscription and view usage statistics.</p>
        </div>

        @if($subscription)
        <!-- Current Subscription -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $subscription->plan_name }}</h2>
                    <p class="text-gray-600">{{ $subscription->billing_period_formatted }} billing</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-900">${{ number_format($subscription->price, 0) }}</div>
                    <div class="text-gray-600">per {{ $subscription->billing_period }}</div>
                </div>
            </div>

            <!-- Trial Status -->
            @if($subscription->inTrial())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800">Free Trial Active</h3>
                        <p class="text-sm text-blue-700">
                            {{ $subscription->trial_remaining_days }} days remaining in your free trial.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Usage Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600">Participant Profiles</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $participantCount }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                @if($subscription->participant_profile_limit)
                                    of {{ $subscription->participant_profile_limit }}
                                @else
                                    Unlimited
                                @endif
                            </div>
                            @if($subscription->participant_profile_limit && $participantCount >= $subscription->participant_profile_limit)
                                <div class="text-red-600 text-sm font-medium">Limit Reached</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600">Accommodation Listings</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $accommodationCount }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                @if($subscription->accommodation_listing_limit)
                                    of {{ $subscription->accommodation_listing_limit }}
                                @else
                                    Unlimited
                                @endif
                            </div>
                            @if($subscription->accommodation_listing_limit && $accommodationCount >= $subscription->accommodation_listing_limit)
                                <div class="text-red-600 text-sm font-medium">Limit Reached</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Features -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Plan Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($subscription->plan)
                        @foreach($subscription->plan->features as $feature)
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('subscription.plans') }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg font-medium text-center hover:bg-blue-700 transition-colors">
                    Change Plan
                </a>
                <form method="POST" action="{{ route('subscription.cancel') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-red-700 transition-colors" 
                            onclick="return confirm('Are you sure you want to cancel your subscription?')">
                        Cancel Subscription
                    </button>
                </form>
            </div>
        </div>
        @else
        <!-- No Subscription -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">No Active Subscription</h2>
            <p class="text-gray-600 mb-6">
                You don't have an active subscription. Subscribe to a plan to access all features.
            </p>
            <a href="{{ route('subscription.plans') }}" class="bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                Choose a Plan
            </a>
        </div>
        @endif

        <!-- Usage Guidelines -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-yellow-800 mb-3">Usage Guidelines</h3>
            <ul class="text-yellow-700 space-y-2">
                <li>• Participant profiles are refreshed monthly based on your subscription date</li>
                <li>• You can delete and add new participant profiles within your limit</li>
                <li>• Accommodation listings are only available with Growth and Premium plans</li>
                <li>• Messaging features require an active subscription</li>
            </ul>
        </div>
    </div>
</div>
@endsection

