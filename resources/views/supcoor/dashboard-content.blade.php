@extends('supcoor.sc-db')

@section('main-content')
<div class="max-w-full mx-auto">
    <!-- Welcome Section -->
    <div class="p-6 bg-white rounded-xl shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
            Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        </h2>
        <p class="text-gray-600">This is your support coordinator dashboard with comprehensive analytics</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalParticipants ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">No Current SIL/SDA</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $noCurrentSilSdaAccommodation ?? 0 }}</p>
                </div>
            </div>
        </div> -->

        <!-- <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Looking for Accommodation</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $participantsLookingForAccommodation ?? 0 }}</p>
                </div>
            </div>
        </div> -->

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
                <canvas id="participantsByStateChart"></canvas>
            </div>
        </div>

        <!-- Participants by Primary Disability Chart -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Participants by Primary Disability</h3>
            <div class="h-64">
                <canvas id="participantsByDisabilityChart"></canvas>
            </div>
        </div>

        <!-- Participants by Age Range Chart -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Participants by Age Range</h3>
            <div class="h-64">
                <canvas id="participantsByAgeChart"></canvas>
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

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($recentActivities ?? [] as $activity)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="p-2 rounded-full 
                        @if($activity['type'] === 'participant') bg-blue-100
                        @elseif($activity['type'] === 'participant_update') bg-green-100
                        @elseif($activity['type'] === 'message') bg-purple-100
                        @else bg-gray-100 @endif">
                        <svg class="w-4 h-4 
                            @if($activity['type'] === 'participant') text-blue-600
                            @elseif($activity['type'] === 'participant_update') text-green-600
                            @elseif($activity['type'] === 'message') text-purple-600
                            @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($activity['type'] === 'participant')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            @elseif($activity['type'] === 'participant_update')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            @elseif($activity['type'] === 'message')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
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
    const stateCtx = document.getElementById('participantsByStateChart').getContext('2d');
    new Chart(stateCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($participantsPerState->pluck('state')) !!},
            datasets: [{
                label: 'Participants',
                data: {!! json_encode($participantsPerState->pluck('total')) !!},
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
                    beginAtZero: true
                }
            }
        }
    });

    // Participants by Disability Chart
    const disabilityCtx = document.getElementById('participantsByDisabilityChart').getContext('2d');
    new Chart(disabilityCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($participantsPerPrimaryDisability->pluck('primary_disability')) !!},
            datasets: [{
                data: {!! json_encode($participantsPerPrimaryDisability->pluck('total')) !!},
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
                    position: 'bottom'
                }
            }
        }
    });

    // Participants by Age Chart
    const ageCtx = document.getElementById('participantsByAgeChart').getContext('2d');
    new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($participantsPerAgeRange->pluck('age_range')) !!},
            datasets: [{
                label: 'Participants',
                data: {!! json_encode($participantsPerAgeRange->pluck('total')) !!},
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
                    beginAtZero: true
                }
            }
        }
    });

    

    // Initialize Your Participants Map
    const yourParticipantsMap = L.map('participantsMap').setView([-25.2744, 133.7751], 4); // Australia center

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(yourParticipantsMap);

    // Add markers for your participants by suburb
    const suburbData = {!! json_encode($participantsPerSuburb) !!};
    
    suburbData.forEach(function(item) {
        if (item.latitude && item.longitude) {
            L.marker([item.latitude, item.longitude])
                .addTo(yourParticipantsMap)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-semibold">${item.suburb}</h4>
                        <p class="text-sm text-gray-600">Your Participants: ${item.total}</p>
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
