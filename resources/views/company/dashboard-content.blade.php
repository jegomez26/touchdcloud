@extends('company.provider-db')

@section('main-content')
<div class="max-w-full mx-auto">
    <!-- Welcome Section -->
    <div class="p-6 bg-white rounded-xl shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
            Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </h2>
        <p class="text-gray-600">This is your provider dashboard with comprehensive analytics</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Participants</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $currentParticipantCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Accommodations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $currentAccommodationCount ?? 0 }}</p>
                </div>
            </div>
        </div>


        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
            <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Matches</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $acceptedMatchRequests ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Participants by State Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Participants by State</h3>
                <div class="h-64">
                    @if($participantsByState->count() > 0)
                        <canvas id="participantsByStateChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p>No state data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participants by Primary Disability Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Participants by Primary Disability</h3>
                <div class="h-64">
                    @if($participantsByDisability->count() > 0)
                        <canvas id="participantsByDisabilityChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p>No disability data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participants by Age Range Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Participants by Age Range</h3>
                <div class="h-64">
                    @if($participantsByAgeRange->count() > 0)
                        <canvas id="participantsByAgeChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p>No age data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    <!-- System-wide Analytics Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">System-wide Analytics</h2>
        
        <!-- Top Analytics Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Top 10 Suburbs -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Suburbs</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">Rank</th>
                                <th class="text-left py-2 font-medium text-gray-600">Suburb</th>
                                <th class="text-right py-2 font-medium text-gray-600">Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($systemWideAnalytics['topSuburbs'] as $index => $suburb)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                                    <td class="py-2 font-medium">{{ $suburb->suburb }}</td>
                                    <td class="py-2 text-right font-semibold text-blue-600">{{ $suburb->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top 10 States -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top States</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">Rank</th>
                                <th class="text-left py-2 font-medium text-gray-600">State</th>
                                <th class="text-right py-2 font-medium text-gray-600">Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($systemWideAnalytics['topStates'] as $index => $state)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                                    <td class="py-2 font-medium">{{ $state['state'] }}</td>
                                    <td class="py-2 text-right font-semibold text-green-600">{{ $state['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top 10 Disabilities -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Disabilities</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">Rank</th>
                                <th class="text-left py-2 font-medium text-gray-600">Disability</th>
                                <th class="text-right py-2 font-medium text-gray-600">Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($systemWideAnalytics['topDisabilities'] as $index => $disability)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                                    <td class="py-2 font-medium">{{ $disability->primary_disability }}</td>
                                    <td class="py-2 text-right font-semibold text-purple-600">{{ $disability->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Participant Map -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">All Participants Map</h3>
            <p class="text-sm text-gray-600 mb-4">Individual participant locations across the system</p>
            <div class="h-96 rounded-lg overflow-hidden relative" style="z-index: 1;">
                <div id="allParticipantsMap" class="w-full h-full" style="z-index: 1;"></div>
            </div>
        </div>
    </div>

    <!-- Your Participants Map Section -->
    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Participants by Suburbs</h3>
        <div class="h-96 rounded-lg overflow-hidden relative" style="z-index: 1;">
            <div id="participantsMap" class="w-full h-full" style="z-index: 1;"></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Add Participant Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 ml-3">Add Participant</h3>
            </div>
            <p class="text-gray-600 mb-4">Add a new participant to your profile</p>
            @if($canAddParticipant)
                <a href="{{ route('provider.participants.create') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700 transition-colors">
                    Add Participant
                </a>
            @else
                <button disabled class="block w-full bg-gray-400 text-gray-600 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                    Limit Reached ({{ $currentParticipantCount }}/{{ $participantLimit ?? 'Unlimited' }})
                </button>
            @endif
        </div>

        <!-- Add Accommodation Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 ml-3">Add Accommodation</h3>
            </div>
            <p class="text-gray-600 mb-4">List a new accommodation property</p>
            @if($canAddAccommodation)
                <a href="{{ route('provider.accommodations.create') }}" class="block w-full bg-green-600 text-white py-2 px-4 rounded-lg text-center hover:bg-green-700 transition-colors">
                    Add Accommodation
                </a>
            @else
                <button disabled class="block w-full bg-gray-400 text-gray-600 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                    Limit Reached ({{ $currentAccommodationCount }}/{{ $accommodationLimit ?? 'Unlimited' }})
                </button>
            @endif
        </div>

        <!-- View Messages Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 ml-3">Messages</h3>
            </div>
            <p class="text-gray-600 mb-4">View and respond to messages</p>
            <a href="{{ route('provider.messages.index') }}" class="block w-full bg-purple-600 text-white py-2 px-4 rounded-lg text-center hover:bg-purple-700 transition-colors">
                View Messages
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-xl shadow-lg relative">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <div class="space-y-4 max-h-96 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 relative">
            @forelse($recentActivities ?? [] as $activity)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="p-2 rounded-full 
                        @if($activity['type'] === 'participant') bg-blue-100
                        @elseif($activity['type'] === 'accommodation') bg-green-100
                        @elseif($activity['type'] === 'accommodation_update') bg-yellow-100
                        @elseif($activity['type'] === 'enquiry') bg-purple-100
                        @elseif($activity['type'] === 'enquiry_update') bg-indigo-100
                        @elseif($activity['type'] === 'message_sent') bg-teal-100
                        @elseif($activity['type'] === 'message_received') bg-cyan-100
                        @elseif($activity['type'] === 'match_found') bg-pink-100
                        @elseif($activity['type'] === 'support_ticket') bg-red-100
                        @elseif($activity['type'] === 'support_update') bg-orange-100
                        @elseif($activity['type'] === 'profile_update') bg-gray-100
                        @else bg-gray-100 @endif">
                        <i class="fas w-4 h-4 
                            @if($activity['type'] === 'participant') text-blue-600 fa-user-plus
                            @elseif($activity['type'] === 'accommodation') text-green-600 fa-home
                            @elseif($activity['type'] === 'accommodation_update') text-yellow-600 fa-edit
                            @elseif($activity['type'] === 'enquiry') text-purple-600 fa-envelope
                            @elseif($activity['type'] === 'enquiry_update') text-indigo-600 fa-envelope-open
                            @elseif($activity['type'] === 'message_sent') text-teal-600 fa-paper-plane
                            @elseif($activity['type'] === 'message_received') text-cyan-600 fa-inbox
                            @elseif($activity['type'] === 'match_found') text-pink-600 fa-heart
                            @elseif($activity['type'] === 'support_ticket') text-red-600 fa-ticket-alt
                            @elseif($activity['type'] === 'support_update') text-orange-600 fa-tools
                            @elseif($activity['type'] === 'profile_update') text-gray-600 fa-user-edit
                            @else text-gray-600 fa-clock @endif"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p>No recent activity</p>
                </div>
            @endforelse
        </div>
        <!-- Scroll indicator -->
        <div class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Leaflet for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart colors
    const colors = {
        primary: '#3B82F6',
        secondary: '#10B981',
        accent: '#F59E0B',
        danger: '#EF4444',
        purple: '#8B5CF6',
        pink: '#EC4899',
        indigo: '#6366F1',
        teal: '#14B8A6'
    };

    // Participants by State Chart
    const stateChartElement = document.getElementById('participantsByStateChart');
    if (stateChartElement) {
        const stateCtx = stateChartElement.getContext('2d');
        new Chart(stateCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($participantsByState->pluck('state')) !!},
            datasets: [{
                label: 'Participants',
                data: {!! json_encode($participantsByState->pluck('count')) !!},
                backgroundColor: colors.primary,
                borderColor: colors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' participant' + (context.parsed.y !== 1 ? 's' : '');
                        }
                    }
                }
            }
        }
        });
    }

    // Participants by Disability Chart
    const disabilityChartElement = document.getElementById('participantsByDisabilityChart');
    if (disabilityChartElement) {
        const disabilityCtx = disabilityChartElement.getContext('2d');
        new Chart(disabilityCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($participantsByDisability->pluck('primary_disability')) !!},
            datasets: [{
                data: {!! json_encode($participantsByDisability->pluck('count')) !!},
                backgroundColor: [
                    colors.primary,
                    colors.secondary,
                    colors.accent,
                    colors.danger,
                    colors.purple,
                    colors.pink,
                    colors.indigo,
                    colors.teal
                ]
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            return label + ': ' + value + ' participant' + (value !== 1 ? 's' : '');
                        }
                    }
                }
            }
        }
        });
    }

    // Participants by Age Chart
    const ageChartElement = document.getElementById('participantsByAgeChart');
    if (ageChartElement) {
        const ageCtx = ageChartElement.getContext('2d');
        new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($participantsByAgeRange->pluck('age_range')) !!},
            datasets: [{
                label: 'Participants',
                data: {!! json_encode($participantsByAgeRange->pluck('count')) !!},
                backgroundColor: colors.secondary,
                borderColor: colors.secondary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' participant' + (context.parsed.y !== 1 ? 's' : '');
                        }
                    }
                }
            }
        }
        });
    }

    

    // Initialize Your Participants Map
    const yourParticipantsMap = L.map('participantsMap').setView([-25.2744, 133.7751], 4); // Australia center

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(yourParticipantsMap);

    // Add markers for your participants by suburb
    const suburbData = {!! json_encode($participantsBySuburb) !!};
    
    suburbData.forEach(function(item) {
        if (item.latitude && item.longitude) {
            L.marker([item.latitude, item.longitude])
                .addTo(yourParticipantsMap)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-semibold">${item.suburb}</h4>
                        <p class="text-sm text-gray-600">Your Participants: ${item.count}</p>
                    </div>
                `);
        }
    });

    // Initialize All Participants Map
    const allParticipantsMap = L.map('allParticipantsMap').setView([-25.2744, 133.7751], 4); // Australia center

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(allParticipantsMap);

    // Add markers for all participants in the system
    const allParticipantsData = {!! json_encode($systemWideAnalytics['allParticipants']) !!};
    
    allParticipantsData.forEach(function(location) {
        if (location.latitude && location.longitude) {
            // Create custom icon for suburb locations with count
            const suburbIcon = L.divIcon({
                className: 'suburb-marker',
                html: `<div class="w-8 h-8 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white font-bold text-sm">${location.participant_count}</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            L.marker([location.latitude, location.longitude], { icon: suburbIcon })
                .addTo(allParticipantsMap)
                .bindPopup(`
                    <div class="p-3">
                        <h4 class="font-semibold text-lg">${location.location}</h4>
                        <p class="text-sm text-gray-600 mb-2">Participants: <span class="font-semibold text-blue-600">${location.participant_count}</span></p>
                        <p class="text-sm text-gray-600">Location: ${location.suburb || 'Unknown'}, ${location.state || 'Unknown'}</p>
                        ${location.disabilities && location.disabilities.length > 0 ? `<p class="text-sm text-gray-600 mt-1">Disabilities: ${location.disabilities.join(', ')}</p>` : ''}
                        ${location.genders && location.genders.length > 0 ? `<p class="text-sm text-gray-600">Genders: ${location.genders.join(', ')}</p>` : ''}
                    </div>
                `);
        }
    });
});
</script>
@endsection
