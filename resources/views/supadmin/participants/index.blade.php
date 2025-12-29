@extends('supadmin.sa-db')

@section('title', 'Manage Participants - SIL Match Admin')

@section('content')
<div class="container mx-auto px-4 py-8 font-sans">
    <h1 class="text-3xl md:text-4xl font-extrabold text-custom-dark-teal mb-8 border-b-2 border-custom-light-grey-green pb-4">
        <i class="fas fa-users mr-3 text-custom-ochre"></i> Manage Participants
    </h1>

    @if (session('success'))
        <div class="bg-custom-green-light bg-opacity-10 border border-custom-green text-custom-dark-olive px-6 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold text-custom-dark-teal mr-2">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 bg-opacity-20 border border-red-500 text-red-800 px-6 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold text-red-700 mr-2">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Participant Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $totalParticipants }}</h3>
            <p class="text-custom-dark-olive">Total Participants</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-check text-custom-green text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-green">{{ $selfRegisteredCount }}</h3>
            <p class="text-custom-dark-olive">Self-Registered</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $newThisWeekCount }}</h3>
            <p class="text-custom-dark-olive">New This Week</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-map-marker-alt text-custom-ochre text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-ochre">{{ $uniqueSuburbsCount }}</h3>
            <p class="text-custom-dark-olive">Different Suburbs</p>
        </div>
    </div>

    <!-- Participants Table -->
    <div class="bg-custom-white shadow-lg rounded-xl p-6 border border-custom-light-grey-green">
        <h2 class="text-2xl font-bold text-custom-dark-teal mb-6 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-list-alt mr-2 text-custom-ochre"></i> All Participants
        </h2>

        <div class="mb-6">
            <input type="text" id="searchParticipants" placeholder="Search participants by name, email, location, or disability..." 
                   class="w-full px-4 py-2 border border-custom-light-grey-brown rounded-md focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre text-custom-dark-olive placeholder-gray-500 transition-colors duration-200 ease-in-out">
        </div>

        @if($participants->count() > 0)
            <div class="overflow-x-auto relative shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-custom-light-grey-green bg-custom-white">
                    <thead class="bg-custom-light-cream">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Participant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Disability</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Registration</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Added By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-custom-light-grey-green">
                        @foreach($participants as $participant)
                        <tr class="hover:bg-custom-light-cream transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-custom-light-cream rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-custom-dark-teal"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-custom-dark-olive">{{ $participant->first_name }} {{ $participant->last_name }}</div>
                                        <div class="text-sm text-gray-700">
                                            @if($participant->user_id)
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-custom-green bg-opacity-20 text-custom-dark-olive">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Self-Registered
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-custom-light-grey-green bg-opacity-20 text-custom-dark-teal">
                                                    <i class="fas fa-user-plus mr-1"></i>
                                                    @if($participant->addedByUser)
                                                        Added by {{ ucfirst(str_replace('_', ' ', $participant->addedByUser->role)) }}
                                                    @else
                                                        Added by Representative
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($participant->participant_email)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                            {{ $participant->participant_email }}
                                        </div>
                                    @endif
                                    @if($participant->participant_phone)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            {{ $participant->participant_phone }}
                                        </div>
                                    @endif
                                    @if(!$participant->participant_email && !$participant->participant_phone)
                                        <span class="text-gray-400">No contact info</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($participant->suburb && $participant->state)
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                            {{ $participant->suburb }}, {{ $participant->state }}
                                        </div>
                                    @elseif($participant->state)
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                            {{ $participant->state }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($participant->primary_disability)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-wheelchair mr-1"></i>
                                        {{ $participant->primary_disability }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $participant->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($participant->addedByUser)
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            @if($participant->addedByUser->role === 'admin')
                                                <i class="fas fa-crown text-custom-ochre mr-2"></i>
                                            @elseif($participant->addedByUser->role === 'provider')
                                                <i class="fas fa-hospital text-custom-dark-teal mr-2"></i>
                                            @elseif($participant->addedByUser->role === 'support_coordinator')
                                                <i class="fas fa-user-tie text-custom-green mr-2"></i>
                                            @else
                                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                            @endif
                                            {{ $participant->addedByUser->first_name }} {{ $participant->addedByUser->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-700">{{ $participant->addedByUser->email }}</div>
                                        <div class="text-xs text-custom-dark-teal font-medium">{{ ucfirst(str_replace('_', ' ', $participant->addedByUser->role)) }}</div>
                                    </div>
                                @elseif($participant->user)
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-gray-400 mr-2"></i>
                                            Self-registered
                                        </div>
                                        <div class="text-xs text-gray-700">{{ $participant->user->email }}</div>
                                        <div class="text-xs text-custom-dark-teal font-medium">{{ ucfirst($participant->user->role) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Unknown</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $participants->links() }}
        </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-custom-dark-olive mb-2">No participants found</h3>
                <p class="text-custom-dark-olive">There are no participants in the system yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Participant Details Modal -->
<div id="participantDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Participant Details</h3>
                <button onclick="closeParticipantDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="participantDetailsContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchParticipants').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Participant details modal
    function showParticipantDetails(participantId) {
        // This would typically fetch participant details via AJAX
        // For now, we'll show a placeholder
        document.getElementById('participantDetailsContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-user-circle text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">Participant details for ID: ${participantId}</p>
                <p class="text-sm text-gray-400 mt-2">This feature can be enhanced with AJAX loading</p>
            </div>
        `;
        document.getElementById('participantDetailsModal').classList.remove('hidden');
    }

    function closeParticipantDetails() {
        document.getElementById('participantDetailsModal').classList.add('hidden');
    }

    function editParticipant(participantId) {
        // This would typically redirect to edit page or open edit modal
        alert('Edit functionality for participant ID: ' + participantId + ' would be implemented here');
    }

    // Close modal when clicking outside
    document.getElementById('participantDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeParticipantDetails();
        }
    });
</script>
@endpush
