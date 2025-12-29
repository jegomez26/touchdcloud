@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-[#33595a]">Welcome, Super Admin! 👋</h1>
    <p class="mt-2 text-[#bcbabb]">Comprehensive system overview and analytics dashboard.</p>
</div>

<!-- Key Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
    {{-- Total Participants --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-users text-[#cc8e45] text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-[#33595a] mb-1">Participants</h3>
        <p class="text-2xl font-bold text-[#cc8e45]">{{ $participantCount }}</p>
    </div>

    {{-- Active Coordinators --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-handshake text-[#33595a] text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-[#33595a] mb-1">Coordinators</h3>
        <p class="text-2xl font-bold text-[#cc8e45]">{{ $coordinatorCount }}</p>
    </div>

    {{-- Active Providers --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-hospital text-[#cc8e45] text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-[#33595a] mb-1">Providers</h3>
        <p class="text-2xl font-bold text-[#cc8e45]">{{ $providerCount }}</p>
    </div>

    
    {{-- Total Accepted Match Requests --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
    <div class="w-16 h-16 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-heart text-[#cc8e45] text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-[#33595a] mb-1">Total Matches</h3>
        <p class="text-2xl font-bold text-[#cc8e45]">{{ $totalAcceptedMatchRequests ?? 0 }}</p>
    </div>

    {{-- Match Rate --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-percentage text-[#33595a] text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-[#33595a] mb-1">Match Rate</h3>
        <p class="text-2xl font-bold text-[#cc8e45]">{{ $matchesData['match_rate'] }}%</p>
    </div>

</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- User Roles Distribution --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h2 class="text-2xl font-bold text-[#33595a] mb-4">User Roles Distribution</h2>
        <canvas id="userRolesChart" class="w-full h-80" style="max-height: 400px;"></canvas>
    </div>

    {{-- Monthly Registrations by Type --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h2 class="text-2xl font-bold text-[#33595a] mb-4">Monthly Registrations by User Type</h2>
        <canvas id="monthlyRegistrationsChart" class="w-full h-80" style="max-height: 400px;"></canvas>
    </div>
</div>

<!-- Top Analytics Tables -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Top 10 Suburbs --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">Top Suburbs</h3>
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
                    @foreach($topSuburbs as $index => $suburb)
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

    {{-- Top 10 States --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">Top States</h3>
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
                    @foreach($topStates as $index => $state)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                            <td class="py-2 font-medium">{{ $state->state }}</td>
                            <td class="py-2 text-right font-semibold text-green-600">{{ $state->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top 10 Disabilities --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">Top Disabilities</h3>
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
                    @foreach($topDisabilities as $index => $disability)
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

<!-- Accepted Match Requests Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Accepted Match Requests by Provider/Company --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">Accepted Match Requests by Provider/Company</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 font-medium text-gray-600">Rank</th>
                        <th class="text-left py-2 font-medium text-gray-600">Provider/Company</th>
                        <th class="text-right py-2 font-medium text-gray-600">Accepted Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acceptedMatchRequestsByProvider as $index => $provider)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                            <td class="py-2 font-medium">{{ $provider['name'] }}</td>
                            <td class="py-2 text-right font-semibold text-blue-600">{{ $provider['count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">No accepted match requests from providers yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Accepted Match Requests by Support Coordinator --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">Accepted Match Requests by Support Coordinator</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 font-medium text-gray-600">Rank</th>
                        <th class="text-left py-2 font-medium text-gray-600">Support Coordinator</th>
                        <th class="text-right py-2 font-medium text-gray-600">Accepted Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acceptedMatchRequestsBySupcoor as $index => $supcoor)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 text-gray-500">#{{ $index + 1 }}</td>
                            <td class="py-2 font-medium">{{ $supcoor['name'] }}</td>
                            <td class="py-2 text-right font-semibold text-green-600">{{ $supcoor['count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">No accepted match requests from support coordinators yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sitewide Map -->
<div class="bg-white rounded-lg shadow-xl p-6 mb-8">
    <h3 class="text-xl font-bold text-[#33595a] mb-4">Sitewide Participant Distribution</h3>
    <p class="text-sm text-gray-600 mb-4">Interactive map showing participant locations across Australia</p>
    <div class="h-96 rounded-lg overflow-hidden relative" style="z-index: 1;">
        <div id="sitewideMap" class="w-full h-full" style="z-index: 1;"></div>
    </div>
</div>

<!-- System Performance Metrics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Performance Metrics --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">System Performance</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Total Users</span>
                <span class="font-semibold text-[#33595a]">{{ $performanceMetrics['total_users'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Active Users</span>
                <span class="font-semibold text-green-600">{{ $performanceMetrics['active_users'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">User Activity Rate</span>
                <span class="font-semibold text-blue-600">{{ $performanceMetrics['user_activity_rate'] }}%</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Total Conversations</span>
                <span class="font-semibold text-purple-600">{{ $performanceMetrics['total_conversations'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Total Messages</span>
                <span class="font-semibold text-orange-600">{{ $performanceMetrics['total_messages'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Avg Messages/Conversation</span>
                <span class="font-semibold text-indigo-600">{{ $performanceMetrics['avg_messages_per_conversation'] }}</span>
            </div>
        </div>
    </div>

    {{-- User Activity Analytics --}}
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h3 class="text-xl font-bold text-[#33595a] mb-4">User Analytics</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">New Registrations Today</span>
                <span class="font-semibold text-green-600">{{ $userActivityData['recent_registrations_today'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">New This Week</span>
                <span class="font-semibold text-blue-600">{{ $userActivityData['recent_registrations_this_week'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">New This Month</span>
                <span class="font-semibold text-purple-600">{{ $userActivityData['recent_registrations_this_month'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Active Users</span>
                <span class="font-semibold text-orange-600">{{ $userActivityData['active_users'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Inactive Users</span>
                <span class="font-semibold text-red-600">{{ $userActivityData['inactive_users'] }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Profile Completion Rate</span>
                <span class="font-semibold text-indigo-600">{{ $userActivityData['profile_completion_rate'] }}%</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-xl p-6">
    <h3 class="text-xl font-bold text-[#33595a] mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <a href="{{ route('superadmin.users.index') }}" class="bg-[#cc8e45] text-white p-4 rounded-lg text-center hover:bg-orange-600 transition duration-200">
            <i class="fas fa-users text-2xl mb-2"></i>
            <p class="font-semibold">Manage Users</p>
        </a>
        <a href="{{ route('superadmin.participants.index') }}" class="bg-[#33595a] text-white p-4 rounded-lg text-center hover:bg-gray-700 transition duration-200">
            <i class="fas fa-user-friends text-2xl mb-2"></i>
            <p class="font-semibold">Manage Participants</p>
        </a>
        <a href="{{ route('superadmin.providers.index') }}" class="bg-[#bcbabb] text-white p-4 rounded-lg text-center hover:bg-gray-500 transition duration-200">
            <i class="fas fa-hospital text-2xl mb-2"></i>
            <p class="font-semibold">Manage Providers</p>
        </a>
        <a href="{{ route('superadmin.support-coordinators.index') }}" class="bg-[#3e4732] text-white p-4 rounded-lg text-center hover:bg-gray-600 transition duration-200">
            <i class="fas fa-handshake text-2xl mb-2"></i>
            <p class="font-semibold">Manage Coordinators</p>
        </a>
        <a href="{{ route('superadmin.logs.index') }}" class="bg-[#f8f1e1] text-[#33595a] p-4 rounded-lg text-center hover:bg-gray-200 transition duration-200">
            <i class="fas fa-file-alt text-2xl mb-2"></i>
            <p class="font-semibold">View Logs</p>
        </a>
        <a href="{{ route('superadmin.backup.index') }}" class="bg-[#e1e7dd] text-[#33595a] p-4 rounded-lg text-center hover:bg-gray-300 transition duration-200">
            <i class="fas fa-download text-2xl mb-2"></i>
            <p class="font-semibold">Backup Data</p>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define a consistent color palette for charts
        const chartColors = {
            darkTeal: 'rgba(51, 89, 90, 0.8)',
            warmOrange: 'rgba(204, 142, 69, 0.8)',
            mediumGrey: 'rgba(188, 186, 187, 0.8)',
            darkOlive: 'rgba(62, 71, 50, 0.8)',
            lightWarmGrey: 'rgba(248, 241, 225, 0.8)',
            coolLightGreyGreen: 'rgba(225, 231, 221, 0.8)',
        };

        // User Roles Pie Chart
        const userRolesData = {
            labels: @json(array_keys($mappedRoles)),
            datasets: [{
                data: @json(array_values($mappedRoles)),
                backgroundColor: [
                    chartColors.darkTeal,
                    chartColors.warmOrange,
                    chartColors.mediumGrey,
                    chartColors.darkOlive,
                    chartColors.coolLightGreyGreen,
                    chartColors.lightWarmGrey,
                ].slice(0, @json(count($mappedRoles))),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 8
            }]
        };

        const userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
        new Chart(userRolesCtx, {
            type: 'pie',
            data: userRolesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { size: 14 },
                            color: '#33595a'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('en-US').format(context.parsed) + ' users';
                                }
                                return label;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            },
        });

        // Monthly Registrations by Type Chart
        const monthlyRegistrationsData = {
            labels: @json(array_keys($monthlyRegistrationsByType)),
            datasets: [
                {
                    label: 'Participants',
                    borderColor: chartColors.warmOrange.replace('0.8', '1'),
                    backgroundColor: chartColors.warmOrange.replace('0.8', '0.2'),
                    data: @json(array_column($monthlyRegistrationsByType, 'participants')),
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: chartColors.warmOrange.replace('0.8', '1'),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Coordinators',
                    borderColor: chartColors.darkTeal.replace('0.8', '1'),
                    backgroundColor: chartColors.darkTeal.replace('0.8', '0.2'),
                    data: @json(array_column($monthlyRegistrationsByType, 'coordinators')),
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: chartColors.darkTeal.replace('0.8', '1'),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Providers',
                    borderColor: chartColors.mediumGrey.replace('0.8', '1'),
                    backgroundColor: chartColors.mediumGrey.replace('0.8', '0.2'),
                    data: @json(array_column($monthlyRegistrationsByType, 'providers')),
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: chartColors.mediumGrey.replace('0.8', '1'),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        };

        const monthlyRegistrationsCtx = document.getElementById('monthlyRegistrationsChart').getContext('2d');
        new Chart(monthlyRegistrationsCtx, {
            type: 'line',
            data: monthlyRegistrationsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 14 },
                            color: '#33595a'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Registrations',
                            color: '#33595a',
                            font: { size: 14, weight: 'bold' }
                        },
                        grid: { color: '#e1e7dd' }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                            color: '#33595a',
                            font: { size: 14, weight: 'bold' }
                        },
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                }
            },
        });

        // Initialize Sitewide Map
        const sitewideMap = L.map('sitewideMap').setView([-25.2744, 133.7751], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(sitewideMap);

        // Add markers for sitewide participants
        const sitewideMapData = @json($sitewideMapData);
        
        sitewideMapData.forEach(function(location) {
            if (location.latitude && location.longitude) {
                // Create custom icon for suburb locations with count
                const suburbIcon = L.divIcon({
                    className: 'suburb-marker',
                    html: `<div class="w-8 h-8 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white font-bold text-sm">${location.participant_count}</div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                L.marker([location.latitude, location.longitude], { icon: suburbIcon })
                    .addTo(sitewideMap)
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
@endpush