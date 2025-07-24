@extends('supadmin.sa-db') {{-- Corrected: Extends your main super admin layout --}}

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-[#33595a]">Welcome, Super Admin! 👋</h1>
    <p class="mt-2 text-[#bcbabb]">This is your central dashboard for system overview and management.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    {{-- Card 1: Total Participants --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-users text-[#cc8e45] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Total Participants</h2>
        <p class="text-[#bcbabb] text-sm mb-4">A comprehensive look at all registered participants.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Active participants: <span class="font-semibold ml-auto">{{ $participantCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            View Participants
        </button>
    </div>

    {{-- Card 2: Active Support Coordinators --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-handshake text-[#33595a] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Active Coordinators</h2>
        <p class="text-[#bcbabb] text-sm mb-4">Overview of active system coordinators.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Active coordinators: <span class="font-semibold ml-auto">{{ $coordinatorCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            Manage Coordinators
        </button>
    </div>

    {{-- Card 3: Active Providers --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-hospital text-[#cc8e45] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Active Providers</h2>
        <p class="text-[#bcbabb] text-sm mb-4">Insights into active healthcare and service providers.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Active providers: <span class="font-semibold ml-auto">{{ $providerCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            View Providers
        </button>
    </div>

    {{-- Card 4: Total NDIS Businesses --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-building text-[#33595a] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Total NDIS Businesses</h2>
        <p class="text-[#bcbabb] text-sm mb-4">Detailed records of all NDIS registered businesses.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Registered businesses: <span class="font-semibold ml-auto">{{ $ndisBusinessCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            View NDIS Businesses
        </button>
    </div>

    {{-- Card 5: Total Super Admins --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-crown text-[#cc8e45] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Total Super Admins</h2>
        <p class="text-[#bcbabb] text-sm mb-4">Access to all super administrator accounts.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Active admins: <span class="font-semibold ml-auto">{{ $superAdminCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            Manage Super Admins
        </button>
    </div>

    {{-- Card 6: Inactive Users --}}
    <div class="bg-white rounded-xl shadow-xl p-6 border border-[#e1e7dd] flex flex-col items-center text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer">
        <div class="w-24 h-24 rounded-full bg-[#f8f1e1] flex items-center justify-center mb-4 shadow-inner">
            <i class="fas fa-user-slash text-[#33595a] text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#33595a] mb-1">Inactive Users</h2>
        <p class="text-[#bcbabb] text-sm mb-4">List of all inactive user accounts in the system.</p>
        <div class="w-full border-t border-[#e1e7dd] pt-4 mb-4">
            <ul class="text-left text-[#3e4732] space-y-2">
                <li class="flex items-center"><i class="fas fa-check-circle text-[#cc8e45] mr-2"></i> Total inactive: <span class="font-semibold ml-auto">{{ $inactiveUserCount }}</span></li>
            </ul>
        </div>
        <button class="w-full bg-[#cc8e45] text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
            Review Inactive Users
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-xl p-6">
        <h2 class="text-2xl font-bold text-[#33595a] mb-4">User Roles Distribution</h2>
        <canvas id="userRolesChart" class="w-full h-80" style="max-height: 400px;"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow-xl p-6">
        <h2 class="text-2xl font-bold text-[#33595a] mb-4">Monthly Registrations</h2>
        {{-- ADDED: max-height style here --}}
        <canvas id="monthlyRegistrationsChart" class="w-full h-80" style="max-height: 400px;"></canvas>
    </div>
</div>

<div class="bg-white rounded-lg shadow-xl p-6 mt-6">
    <h2 class="text-2xl font-bold text-[#33595a] mb-4 flex items-center">
        <i class="fas fa-history text-[#bcbabb] mr-3"></i> Recent Activity
    </h2>
    <div x-data="{ activities: @json($recentActivities ?? []), showAll: false }">
        <template x-for="(activity, index) in (showAll ? activities : activities.slice(0, 5))" :key="index">
            <div class="flex items-start mb-4 p-4 border-b border-[#e1e7dd] last:border-b-0">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#f8f1e1] flex items-center justify-center text-[#cc8e45] text-lg">
                    <i :class="activity.icon"></i>
                </div>
                <div class="ml-4 flex-grow">
                    <p class="text-[#33595a] font-medium" x-text="activity.title"></p>
                    <p class="text-sm text-[#bcbabb]" x-text="activity.description"></p>
                    <p class="text-xs text-[#e1e7dd] mt-1" x-text="activity.time"></p>
                </div>
            </div>
        </template>
        <button x-show="activities.length > 5 && !showAll" @click="showAll = true" class="mt-4 px-4 py-2 bg-[#cc8e45] text-white rounded-md hover:bg-orange-600 transition duration-200">
            View All Activity
        </button>
        <button x-show="activities.length > 5 && showAll" @click="showAll = false" class="mt-4 px-4 py-2 bg-[#e1e7dd] text-[#33595a] rounded-md hover:bg-[#bcbabb] transition duration-200">
            Show Less
        </button>
    </div>
</div>

<div class="bg-white rounded-lg shadow-xl p-6 mt-6" x-data="{ open: false }">
    <button @click="open = !open" class="text-[#33595a] hover:text-[#000000] font-semibold focus:outline-none flex items-center">
        <i class="fas fa-lightbulb text-[#cc8e45] mr-2"></i>
        <span x-show="!open">Show Quick Tips</span>
        <span x-show="open">Hide Quick Tips</span>
        <i class="ml-auto fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </button>
    <div x-show="open" x-transition.origin.top class="mt-4 p-4 bg-[#f8f1e1] border border-[#e1e7dd] rounded-md">
        <ul class="list-disc pl-5 text-[#3e4732] space-y-2">
            <li><strong>Tip:</strong> Regularly check the "System Logs" for errors and performance issues.</li>
            <li><strong>Tip:</strong> Utilize "User Management" to control user access and activation status.</li>
            <li><strong>Tip:</strong> Schedule routine data backups to prevent data loss.</li>
            <li><strong>Tip:</strong> Monitor new registrations through the "Monthly Registrations" graph.</li>
        </ul>
    </div>
</div>

@endsection

@push('scripts')
{{-- IMPORTANT: Ensure Chart.js is loaded BEFORE your custom script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define a consistent color palette for charts using your scheme
        const chartColors = {
            darkTeal: 'rgba(51, 89, 90, 0.8)', // #33595a
            warmOrange: 'rgba(204, 142, 69, 0.8)', // #cc8e45
            mediumGrey: 'rgba(188, 186, 187, 0.8)', // #bcbabb
            darkOlive: 'rgba(62, 71, 50, 0.8)', // #3e4732
            lightWarmGrey: 'rgba(248, 241, 225, 0.8)', // #f8f1e1
            coolLightGreyGreen: 'rgba(225, 231, 221, 0.8)', // #e1e7dd
        };

        // Data for User Roles Pie Chart
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
                borderColor: '#ffffff', // White
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
                            font: {
                                size: 14
                            },
                            color: '#33595a' // Dark Teal for legend text
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
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

        // Data for Monthly Registrations Line Chart
        const monthlyRegistrationsData = {
            labels: @json(array_keys($monthlyRegistrations)),
            datasets: [{
                label: 'New Registrations',
                borderColor: chartColors.warmOrange.replace('0.8', '1'), // Solid orange for line
                backgroundColor: chartColors.warmOrange.replace('0.8', '0.2'), // Light orange for fill
                data: @json(array_values($monthlyRegistrations)),
                tension: 0.4,
                fill: true,
                pointBackgroundColor: chartColors.warmOrange.replace('0.8', '1'),
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
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
                            font: {
                                size: 14
                            },
                            color: '#33595a' // Dark Teal for legend text
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Registrations',
                            color: '#33595a', // Dark Teal
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: '#e1e7dd' // Cool Light Grey/Green for grid lines
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                            color: '#33595a', // Dark Teal
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                }
            },
        });
    });
</script>
@endpush