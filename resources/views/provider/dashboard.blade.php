@extends('layouts.app')

@section('title', 'Provider Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{
    showPlansModal: false,
    showPaymentModal: false,
    selectedPlan: null,
    billingPeriod: 'monthly',
    loading: false,
    
    submitSubscription() {
        this.loading = true;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('plan_id', this.selectedPlan);
        formData.append('billing_period', this.billingPeriod);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Make API call
        fetch('{{ route("subscription.subscribe") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.loading = false;
            
            if (data.success) {
                this.showPaymentModal = false;
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            this.loading = false;
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    },
    
    startTrial(planId) {
        this.loading = true;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('plan_id', planId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Make API call
        fetch('{{ route("subscription.trial") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.loading = false;
            
            if (data.success) {
                this.showPaymentModal = false;
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            this.loading = false;
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Provider Dashboard</h1>
            <p class="text-gray-600 mt-2">Manage your participants, accommodations, and subscriptions.</p>
        </div>

        <!-- Subscription Status Alert -->
        @php
            $subscriptionService = new \App\Services\SubscriptionService();
            $subscriptionStatus = $subscriptionService::getSubscriptionStatus();
        @endphp

        @if(!$subscriptionStatus['has_subscription'])
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">No Active Subscription</h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        You need an active subscription to access participant matching, messaging, and accommodation features.
                    </p>
                    <div class="mt-3">
                        <button @click="showPlansModal = true" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors">
                            Choose a Plan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @elseif($subscriptionStatus['trial_active'])
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Free Trial Active</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        You have {{ $subscriptionStatus['trial_remaining_days'] }} days remaining in your free trial of {{ $subscriptionStatus['plan_name'] }}.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Participants Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Participants</h3>
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.005v-2.004m0 0v-2.004m0 0V8.995m0 0h.01M12 18.001c-3.14 0-5.7-2.56-5.7-5.7s2.56-5.7 5.7-5.7 5.7 2.56 5.7 5.7-2.56 5.7-5.7 5.7z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Manage participant profiles and matching.</p>
                <div class="space-y-2">
                    <a href="{{ route('provider.participants.list') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700 transition-colors">
                        View Participants
                    </a>
                    @if($subscriptionStatus['has_subscription'])
                        <a href="{{ route('provider.participants.create') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Add Participant
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            Add Participant (Subscription Required)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Accommodations Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Accommodations</h3>
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">List and manage accommodation properties.</p>
                <div class="space-y-2">
                    <a href="{{ route('provider.accommodations.index') }}" class="block w-full bg-green-600 text-white py-2 px-4 rounded-lg text-center hover:bg-green-700 transition-colors">
                        View Accommodations
                    </a>
                    @if($subscriptionStatus['can_access_accommodations'])
                        <a href="{{ route('provider.accommodations.create') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Add Accommodation
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            Add Accommodation (Upgrade Required)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Matching Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Matching</h3>
                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Find matches for your participants.</p>
                <div class="space-y-2">
                    @if($subscriptionStatus['can_access_matching'])
                        <a href="{{ route('provider.participants.matching.index') }}" class="block w-full bg-purple-600 text-white py-2 px-4 rounded-lg text-center hover:bg-purple-700 transition-colors">
                            Start Matching
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            Start Matching (Subscription Required)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Messages Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Messages</h3>
                    <svg class="h-8 w-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Communicate with participants and coordinators.</p>
                <div class="space-y-2">
                    @if($subscriptionStatus['can_access_messaging'])
                        <a href="{{ route('provider.messages.index') }}" class="block w-full bg-orange-600 text-white py-2 px-4 rounded-lg text-center hover:bg-orange-700 transition-colors">
                            View Messages
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            View Messages (Subscription Required)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Subscription Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Subscription</h3>
                    <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Manage your subscription and billing.</p>
                <div class="space-y-2">
                    <a href="{{ route('subscription.manage') }}" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-700 transition-colors">
                        Manage Subscription
                    </a>
                    @if(!$subscriptionStatus['has_subscription'])
                        <button @click="showPlansModal = true" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Choose a Plan
                        </button>
                    @endif
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Profile</h3>
                    <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Update your provider profile information.</p>
                <div class="space-y-2">
                    <a href="{{ route('provider.my-profile.edit') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard -->
        @if($subscriptionStatus['has_subscription'])
        @php
            // Get analytics data
            $participantCount = \App\Models\Participant::where('added_by_user_id', Auth::id())->count();
            $accommodationCount = \App\Models\Property::where('provider_id', Auth::user()->provider->id ?? 0)->count();
            $matchCount = \App\Models\Message::where('sender_id', Auth::id())->count();
            
            // Get participant analytics
            $participantsByState = \App\Models\Participant::where('added_by_user_id', Auth::id())
                ->selectRaw('state, COUNT(*) as count')
                ->groupBy('state')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
                
            $participantsBySuburb = \App\Models\Participant::where('added_by_user_id', Auth::id())
                ->selectRaw('suburb, COUNT(*) as count')
                ->groupBy('suburb')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
                
            $participantsByGender = \App\Models\Participant::where('added_by_user_id', Auth::id())
                ->selectRaw('gender_identity, COUNT(*) as count')
                ->groupBy('gender_identity')
                ->orderBy('count', 'desc')
                ->get();
                
            $participantsByDisability = \App\Models\Participant::where('added_by_user_id', Auth::id())
                ->selectRaw('primary_disability, COUNT(*) as count')
                ->whereNotNull('primary_disability')
                ->groupBy('primary_disability')
                ->orderBy('count', 'desc')
                ->get();
        @endphp

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Participants Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Participants</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $participantCount }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.005v-2.004m0 0v-2.004m0 0V8.995m0 0h.01M12 18.001c-3.14 0-5.7-2.56-5.7-5.7s2.56-5.7 5.7-5.7 5.7 2.56 5.7 5.7-2.56 5.7-5.7 5.7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">Active profiles</span>
                </div>
            </div>

            <!-- Accommodations Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Accommodation Listings</p>
                        <p class="text-3xl font-bold text-green-600">{{ $accommodationCount }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">Available properties</span>
                </div>
            </div>

            <!-- Matches Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Matches</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $matchCount }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">Contacts made</span>
                </div>
            </div>

            <!-- Subscription Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Current Plan</p>
                        <p class="text-lg font-bold text-indigo-600">{{ $subscriptionStatus['plan_name'] ?? 'No Plan' }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">{{ $subscriptionStatus['trial_active'] ? 'Trial Active' : 'Active Subscription' }}</span>
                </div>
            </div>
        </div>

        <!-- Analytics Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Participants by State Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Participants by State (Top 10)</h3>
                <div class="space-y-3">
                    @forelse($participantsByState as $state)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $state->state ?: 'Not Specified' }}</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $participantsByState->max('count') > 0 ? ($state->count / $participantsByState->max('count')) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $state->count }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No participant data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Participants by Suburb Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Participants by Suburb (Top 10)</h3>
                <div class="space-y-3">
                    @forelse($participantsBySuburb as $suburb)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $suburb->suburb ?: 'Not Specified' }}</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $participantsBySuburb->max('count') > 0 ? ($suburb->count / $participantsBySuburb->max('count')) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $suburb->count }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No participant data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Demographics Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gender Distribution -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gender Distribution</h3>
                <div class="space-y-4">
                    @forelse($participantsByGender as $gender)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $gender->gender_identity ?: 'Not Specified' }}</span>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $participantsByGender->sum('count') > 0 ? ($gender->count / $participantsByGender->sum('count')) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $gender->count }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No gender data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Disability Types -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Primary Disability Types</h3>
                <div class="space-y-4">
                    @forelse($participantsByDisability as $disability)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $disability->primary_disability ?: 'Not Specified' }}</span>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $participantsByDisability->sum('count') > 0 ? ($disability->count / $participantsByDisability->sum('count')) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $disability->count }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No disability data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Advanced Analytics with Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gender Distribution Pie Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gender Distribution</h3>
                <div class="relative h-64">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <!-- Disability Types Bar Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Disability Types</h3>
                <div class="relative h-64">
                    <canvas id="disabilityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Geographic Map Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Geographic Distribution</h3>
            <div class="bg-gray-50 rounded-lg p-6 relative" style="z-index: 1;">
                <div id="map" class="h-96 w-full rounded-lg border border-gray-200" style="z-index: 1;"></div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $participantsByState->sum('count') }}</div>
                        <div class="text-sm text-gray-600">Total Participants</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $participantsByState->count() }}</div>
                        <div class="text-sm text-gray-600">States Covered</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $participantsBySuburb->count() }}</div>
                        <div class="text-sm text-gray-600">Suburbs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $participantsBySuburb->sum('count') }}</div>
                        <div class="text-sm text-gray-600">Locations</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                    <div class="p-2 bg-blue-100 rounded-full mr-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.005v-2.004m0 0v-2.004m0 0V8.995m0 0h.01M12 18.001c-3.14 0-5.7-2.56-5.7-5.7s2.56-5.7 5.7-5.7 5.7 2.56 5.7 5.7-2.56 5.7-5.7 5.7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">New participant profile added</p>
                        <p class="text-sm text-gray-600">2 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-green-50 rounded-lg">
                    <div class="p-2 bg-green-100 rounded-full mr-4">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Accommodation listing updated</p>
                        <p class="text-sm text-gray-600">1 day ago</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                    <div class="p-2 bg-purple-100 rounded-full mr-4">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">New match found</p>
                        <p class="text-sm text-gray-600">3 days ago</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Plans Selection Modal -->
    <div x-show="showPlansModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showPlansModal = false">
        
        <div x-show="showPlansModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-2xl shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Choose Your Plan</h2>
                        <p class="text-gray-600 mt-2">Select the perfect plan for your provider needs.</p>
                    </div>
                    <button @click="showPlansModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Plans Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $plans = \App\Models\Plan::active()->ordered()->get();
                    @endphp
                    
                    @foreach($plans as $plan)
                    <div class="relative bg-white rounded-xl shadow-lg border-2 {{ $plan->is_featured ? 'border-blue-500 ring-2 ring-blue-500 ring-opacity-50' : 'border-gray-200' }}">
                        @if($plan->is_featured)
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
                        </div>
                        @endif

                        <div class="p-6">
                            <!-- Plan Header -->
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                <p class="text-gray-600">{{ $plan->description }}</p>
                            </div>

                            <!-- Pricing -->
                            <div class="text-center mb-6">
                                <div class="flex items-baseline justify-center">
                                    <span class="text-5xl font-bold text-gray-900">${{ number_format($plan->monthly_price, 0) }}</span>
                                    <span class="text-xl text-gray-500 ml-1">/month</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    or ${{ number_format($plan->yearly_price, 0) }}/year 
                                    <span class="text-green-600 font-medium">(Save ${{ number_format($plan->yearly_savings, 0) }})</span>
                                </div>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-8">
                                @foreach($plan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                @if(in_array($plan->slug, ['growth', 'premium']))
                                    <button @click="startTrial({{ $plan->id }})" 
                                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                        Start 14-Day Free Trial
                                    </button>
                                @endif
                                
                                <button @click="selectedPlan = {{ $plan->id }}; billingPeriod = 'monthly'; showPaymentModal = true; showPlansModal = false" 
                                        class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                                    Subscribe Now
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Founding Partner Offer -->
                <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Founding Partner Offer</h3>
                    <p class="text-lg text-gray-700 mb-6">
                        Be among the first 10 providers and get the Growth plan for just <span class="font-bold text-purple-600">$399/month</span> for 12 months!
                    </p>
                    <div class="text-sm text-gray-600">
                        <p>Limited time offer • First 10 providers only</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showPaymentModal = false">
        
        <div x-show="showPaymentModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-2xl shadow-xl max-w-2xl w-full">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">Complete Your Subscription</h2>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="p-6">
                <form @submit.prevent="submitSubscription()" x-show="selectedPlan">
                    <!-- Billing Period Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Billing Period</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio" x-model="billingPeriod" value="monthly" class="sr-only">
                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                     :class="billingPeriod === 'monthly' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="text-center">
                                        <div class="font-semibold text-gray-900">Monthly</div>
                                        <div class="text-sm text-gray-600">Billed monthly</div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" x-model="billingPeriod" value="yearly" class="sr-only">
                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                     :class="billingPeriod === 'yearly' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="text-center">
                                        <div class="font-semibold text-gray-900">Yearly</div>
                                        <div class="text-sm text-gray-600">Save 2 months</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                <input type="text" placeholder="1234 5678 9012 3456" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input type="text" placeholder="MM/YY" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" placeholder="123" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" placeholder="123 Main Street" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" placeholder="Sydney" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                    <input type="text" placeholder="2000" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" class="mt-1 mr-3">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-4">
                        <button type="button" @click="showPaymentModal = false" 
                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                                class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">Complete Subscription</span>
                            <span x-show="loading">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Leaflet for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Gender Distribution Pie Chart
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        const genderData = @json($participantsByGender ?? []);
        const genderLabels = genderData.map(item => item.gender_identity || 'Not Specified');
        const genderCounts = genderData.map(item => item.count);
        
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderCounts,
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // Disability Types Bar Chart
    const disabilityCtx = document.getElementById('disabilityChart');
    if (disabilityCtx) {
        const disabilityData = @json($participantsByDisability ?? []);
        const disabilityLabels = disabilityData.map(item => item.primary_disability || 'Not Specified');
        const disabilityCounts = disabilityData.map(item => item.count);
        
        new Chart(disabilityCtx, {
            type: 'bar',
            data: {
                labels: disabilityLabels,
                datasets: [{
                    label: 'Participants',
                    data: disabilityCounts,
                    backgroundColor: '#F59E0B',
                    borderColor: '#D97706',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Initialize Map
    const mapElement = document.getElementById('map');
    if (mapElement) {
        // Initialize map centered on Australia
        const map = L.map('map').setView([-25.2744, 133.7751], 4);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add markers for participant locations
        const participantsByState = @json($participantsByState ?? []);
        const participantsBySuburb = @json($participantsBySuburb ?? []);
        
        // Sample coordinates for Australian states (you would use actual coordinates in production)
        const stateCoordinates = {
            'NSW': [-33.8688, 151.2093],
            'VIC': [-37.8136, 144.9631],
            'QLD': [-27.4698, 153.0251],
            'WA': [-31.9505, 115.8605],
            'SA': [-34.9285, 138.6007],
            'TAS': [-42.8821, 147.3272],
            'NT': [-12.4634, 130.8456],
            'ACT': [-35.2809, 149.1300]
        };
        
        // Add markers for states with participants
        participantsByState.forEach(state => {
            const coords = stateCoordinates[state.state];
            if (coords) {
                L.circleMarker(coords, {
                    radius: Math.max(5, state.count * 2),
                    fillColor: '#3B82F6',
                    color: '#1E40AF',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.7
                }).addTo(map).bindPopup(`
                    <strong>${state.state}</strong><br>
                    Participants: ${state.count}
                `);
            }
        });
        
        // Add a legend
        const legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            const div = L.DomUtil.create('div', 'info legend');
            div.innerHTML = `
                <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <h4 style="margin: 0 0 5px 0; font-size: 14px;">Participants by State</h4>
                    <div style="font-size: 12px;">
                        Circle size represents participant count
                    </div>
                </div>
            `;
            return div;
        };
        legend.addTo(map);
    }
});
</script>
@endsection

