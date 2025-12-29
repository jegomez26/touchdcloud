@extends('company.provider-db') 

@section('main-content') {{-- Start the main-content section --}}
<div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8">
    
    
    
    <!-- Provider Dashboard Content -->
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </h1>
        <div class="flex items-center mb-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                @if(Auth::user()->role === 'coordinator')
                    Support Coordinator
                @elseif(Auth::user()->role === 'provider')
                    NDIS Provider
                @elseif(Auth::user()->role === 'participant')
                    Participant
                @else
                    {{ ucfirst(Auth::user()->role) }}
                @endif
            </span>
        </div>
        <p class="text-gray-600 mt-2">Manage your participants, accommodations, and subscriptions.</p>
    </div>

    <!-- Subscription Status Alert -->
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
                           @if(isset($subscriptionStatus['trial_ends_at']))
                               <br><strong>Trial ends on:</strong> {{ \Carbon\Carbon::parse($subscriptionStatus['trial_ends_at'])->format('F j, Y \a\t g:i A') }}
                           @endif
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
                    @if($canAddParticipant)
                        <a href="{{ route('provider.participants.create') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Add Participant
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-400 text-gray-600 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Limit Reached ({{ $currentParticipantCount }}/{{ $participantLimit ?? 'Unlimited' }})
                            </span>
                        </button>
                    @endif
                @else
                    <button @click="showPlansModal = true" class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center hover:bg-gray-400 transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Add Participant (Subscription Required)
                        </span>
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
                    @if($canAddAccommodation)
                        <a href="{{ route('provider.accommodations.create') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Add Accommodation
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-400 text-gray-600 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Limit Reached ({{ $currentAccommodationCount }}/{{ $accommodationLimit ?? 'Unlimited' }})
                            </span>
                        </button>
                    @endif
                @else
                    <button @click="showPlansModal = true" class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center hover:bg-gray-400 transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Add Accommodation (Upgrade Required)
                        </span>
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
                    <button @click="showPlansModal = true" class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center hover:bg-gray-400 transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Start Matching (Subscription Required)
                        </span>
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
                    <button @click="showPlansModal = true" class="block w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-center hover:bg-gray-400 transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            View Messages (Subscription Required)
                        </span>
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
                <button @click="showSubscriptionManageModal = true" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-700 transition-colors">
                    Manage Subscription
                </button>
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
                        <a href="{{ route('provider.profile.edit') }}" class="block w-full bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            Edit Profile
                        </a>
                    </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Analytics Dashboard</h2>
        
        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            

            <!-- Disability Types Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Participants by Disability Type</h3>
                <div class="h-64">
                    <canvas id="disabilityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Participants by Location</h3>
            <div class="h-96 relative" style="z-index: 1;">
                <div id="map" class="w-full h-full rounded-lg" style="z-index: 1;"></div>
            </div>
        </div>
    </div>
</div>
@endsection {{-- End the main-content section --}}
