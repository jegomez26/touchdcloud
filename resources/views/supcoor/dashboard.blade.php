@extends('supcoor.sc-db') {{-- Extend your support coordinator dashboard layout --}}

@section('main-content')
    <h2 class="font-bold text-2xl md:text-3xl text-[#33595a] leading-tight mb-6 md:mb-8 text-center sm:text-left">
        {{ __('Support Coordinator Dashboard') }}
    </h2>

    {{-- Quick Links Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('sc.participants.list') }}" class="block p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-[#e1e7dd] rounded-full p-3 mr-4">
                    <svg class="w-8 h-8 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3H8a3 3 0 00-3 3v2h5m0-9V4a1 1 0 011-1h2a1 1 0 011 1v6m-5 0h5a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2a2 2 0 012-2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Participants</p>
                    <p class="text-3xl font-bold text-[#33595a]">{{ $totalParticipants }}</p>
                </div>
            </div>
        </a>

        <div class="p-6 bg-white rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-[#d0dbcc] rounded-full p-3 mr-4">
                    <svg class="w-8 h-8 text-[#cc8e45]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v2m7-9v4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Without SIL/SDA Accommodation</p>
                    <p class="text-3xl font-bold text-[#33595a]">{{ $noCurrentSilSdaAccommodation }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-[#f0e4d7] rounded-full p-3 mr-4">
                    <svg class="w-8 h-8 text-[#a3703a]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l7 7 7-7M19 14v6a1 1 0 01-1 1h-3m-6 0v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6m0 0H5m14 0h-2"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Looking for Accommodation</p>
                    <p class="text-3xl font-bold text-[#33595a]">{{ $participantsLookingForAccommodation }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Participants With/Without SIL/SDA Accommodation Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Accommodation Status</h3>
            <canvas id="accommodationChart"></canvas>
        </div>

        {{-- Participants Per State Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Participants Per State</h3>
            <canvas id="stateChart"></canvas>
        </div>

        {{-- Participants Per Primary Disability Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Participants Per Primary Disability</h3>
            <canvas id="primaryDisabilityChart"></canvas>
        </div>

        {{-- Participants Per Age Range Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Participants Per Age Range</h3>
            <canvas id="ageRangeChart"></canvas>
        </div>

        {{-- Participants Per Gender Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Participants Per Gender Identity</h3>
            <canvas id="genderChart"></canvas>
        </div>

        {{-- Participants Per Suburb Chart (consider if many suburbs) --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2"> {{-- Spans two columns on large screens --}}
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Participants Per Suburb (Top 10)</h3>
            <canvas id="suburbChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Ensure you have Chart.js included --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Using CDN for simplicity, adjust if you compile locally --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Data passed from PHP
            const totalParticipants = @json($totalParticipants);
            const noCurrentSilSdaAccommodation = @json($noCurrentSilSdaAccommodation);
            const participantsPerState = @json($participantsPerState);
            const participantsPerSuburb = @json($participantsPerSuburb);
            const participantsPerPrimaryDisability = @json($participantsPerPrimaryDisability);
            const participantsPerAgeRange = @json($participantsPerAgeRange);
            const participantsPerGender = @json($participantsPerGender);

            // --- Chart 1: Accommodation Status (Pie Chart) ---
            const accommodationCtx = document.getElementById('accommodationChart').getContext('2d');
            new Chart(accommodationCtx, {
                type: 'pie',
                data: {
                    labels: ['With SIL/SDA Accommodation', 'Without SIL/SDA Accommodation'],
                    datasets: [{
                        data: [
                            totalParticipants - noCurrentSilSdaAccommodation, // Participants with SIL/SDA accommodation
                            noCurrentSilSdaAccommodation
                        ],
                        backgroundColor: ['#33595a', '#cc8e45'], // Your brand colors
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Participants With and Without SIL/SDA Accommodation'
                        }
                    }
                }
            });

            // --- Chart 2: Participants Per State (Bar Chart) ---
            const stateCtx = document.getElementById('stateChart').getContext('2d');
            new Chart(stateCtx, {
                type: 'bar',
                data: {
                    labels: participantsPerState.map(item => item.state),
                    datasets: [{
                        label: 'Number of Participants',
                        data: participantsPerState.map(item => item.total),
                        backgroundColor: '#8B8171', // A neutral brand color
                        borderColor: '#8B8171',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false // No need for legend if only one dataset
                        },
                        title: {
                            display: false,
                            text: 'Participants Per State'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'State'
                            }
                        }
                    }
                }
            });

            // --- Chart 3: Participants Per Primary Disability (Bar Chart) ---
            const primaryDisabilityCtx = document.getElementById('primaryDisabilityChart').getContext('2d');
            new Chart(primaryDisabilityCtx, {
                type: 'bar',
                data: {
                    labels: participantsPerPrimaryDisability.map(item => item.primary_disability),
                    datasets: [{
                        label: 'Number of Participants',
                        data: participantsPerPrimaryDisability.map(item => item.total),
                        backgroundColor: '#33595a',
                        borderColor: '#33595a',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false,
                            text: 'Participants Per Primary Disability'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Primary Disability Type'
                            }
                        }
                    }
                }
            });

            // --- Chart 4: Participants Per Age Range (Bar Chart) ---
            const ageRangeCtx = document.getElementById('ageRangeChart').getContext('2d');
            new Chart(ageRangeCtx, {
                type: 'bar',
                data: {
                    labels: ['18-25', '26-35', '36-50', '51+', 'Unknown'],
                    datasets: [{
                        label: 'Number of Participants',
                        data: [
                            participantsPerAgeRange.age_18_25,
                            participantsPerAgeRange.age_26_35,
                            participantsPerAgeRange.age_36_50,
                            participantsPerAgeRange.age_51_plus,
                            participantsPerAgeRange.age_unknown,
                        ],
                        backgroundColor: '#cc8e45',
                        borderColor: '#cc8e45',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false,
                            text: 'Participants Per Age Range'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Age Range'
                            }
                        }
                    }
                }
            });

            // --- Chart 5: Participants Per Gender (Doughnut Chart) ---
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: participantsPerGender.map(item => item.gender_identity),
                    datasets: [{
                        data: participantsPerGender.map(item => item.total),
                        backgroundColor: ['#33595a', '#8B8171', '#cc8e45', '#f0e4d7', '#d0dbcc'], // More brand colors
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Participants Per Gender Identity'
                        }
                    }
                }
            });

            // --- Chart 6: Participants Per Suburb (Horizontal Bar Chart for better readability with many labels) ---
            const suburbCtx = document.getElementById('suburbChart').getContext('2d');
            new Chart(suburbCtx, {
                type: 'bar',
                data: {
                    labels: participantsPerSuburb.map(item => item.suburb),
                    datasets: [{
                        label: 'Number of Participants',
                        data: participantsPerSuburb.map(item => item.total),
                        backgroundColor: '#d0dbcc',
                        borderColor: '#d0dbcc',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Makes it a horizontal bar chart
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false,
                            text: 'Participants Per Suburb'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Suburb'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush