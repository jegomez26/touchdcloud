@extends('supcoor.sc-db')

@section('main-content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('sc.participants.matching.index') }}" 
               class="p-2 text-[#bcbabb] hover:text-[#3e4732] hover:bg-[#e1e7dd] rounded-md transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-[#3e4732]">Find Matches</h1>
                <p class="text-[#bcbabb] mt-2">Find compatible housemates for your participants</p>
            </div>
        </div>
    </div>

    <!-- Two Panel Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        <!-- Left Panel - Participant Information -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-[#e1e7dd] flex flex-col">
            <h2 class="text-xl font-semibold text-[#3e4732] mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Participant Information
            </h2>

            <!-- Participant Avatar and Basic Info -->
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-20 h-20 bg-[#33595a] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($participant->first_name, 0, 1)) }}{{ strtoupper(substr($participant->last_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-[#3e4732]">
                        {{ $participant->first_name }} {{ $participant->last_name }}
                    </h3>
                    <p class="text-[#bcbabb]">{{ $participant->participant_code_name ?? 'No code assigned' }}</p>
                </div>
            </div>

            <!-- Participant Details -->
            <div class="space-y-4">
                <!-- Basic Information -->
                <div class="bg-[#f8f1e1] p-4 rounded-lg">
                    <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Basic Information</h4>
                    <div class="space-y-3">
                        @if($participant->age)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Age:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->age }} years old</span>
                            </div>
                        @endif

                        @if($participant->gender_identity)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Gender:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->gender_identity }}</span>
                            </div>
                        @endif

                        @if($participant->date_of_birth)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">DOB:</span>
                                <span class="text-[#2C494A] font-medium">{{ \Carbon\Carbon::parse($participant->date_of_birth)->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Disability & Support Information -->
                <div class="bg-[#f8f1e1] p-4 rounded-lg">
                    <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Disability & Support</h4>
                    <div class="space-y-3">
                        @if($participant->primary_disability)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Primary:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->primary_disability }}</span>
                            </div>
                        @endif

                        @if($participant->secondary_disability)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Secondary:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->secondary_disability }}</span>
                            </div>
                        @endif

                        @if($participant->estimated_support_hours_sil_level)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Support Level:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->estimated_support_hours_sil_level }}</span>
                            </div>
                        @endif

                        @if($participant->night_support_type)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Night Support:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->night_support_type }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Location & Contact -->
                <div class="bg-[#f8f1e1] p-4 rounded-lg">
                    <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Location & Contact</h4>
                    <div class="space-y-3">
                        @if($participant->state)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Location:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->suburb }}, {{ $participant->state }}</span>
                            </div>
                        @endif

                        @if($participant->participant_phone)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Phone:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->participant_phone }}</span>
                            </div>
                        @endif

                        @if($participant->participant_email)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Email:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->participant_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preferences -->
                <div class="bg-[#f8f1e1] p-4 rounded-lg">
                    <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Preferences</h4>
                    <div class="space-y-3">
                        @if(isset($participant->smokes))
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Smoking:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->smokes ? 'Yes' : 'No' }}</span>
                            </div>
                        @endif

                        @if($participant->preferred_number_of_housemates)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Housemates:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->preferred_number_of_housemates }}</span>
                            </div>
                        @endif

                        @if($participant->move_in_availability)
                            <div class="flex items-center">
                                <span class="font-medium text-[#3e4732] w-28 text-sm">Availability:</span>
                                <span class="text-[#2C494A] font-medium">{{ $participant->move_in_availability }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Interests & Hobbies -->
                @if($participant->interests_hobbies)
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Interests & Hobbies</h4>
                        <p class="text-[#2C494A] font-medium text-sm leading-relaxed">{{ $participant->interests_hobbies }}</p>
                    </div>
                @endif

                <!-- Cultural & Religious Practices -->
                @if($participant->cultural_religious_practices)
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h4 class="font-semibold text-[#3e4732] mb-3 text-base">Cultural & Religious Practices</h4>
                        <p class="text-[#2C494A] font-medium text-sm leading-relaxed">{{ $participant->cultural_religious_practices }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel - Find Matches -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-[#e1e7dd] flex flex-col">
            <h2 class="text-xl font-semibold text-[#3e4732] mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Find Compatible Matches
            </h2>

            <p class="text-[#bcbabb] mb-6">Click the button below to find participants who might be compatible housemates for {{ $participant->first_name }}.</p>

            <button id="find-matches-btn" 
                    class="w-full bg-[#33595a] text-white px-6 py-3 rounded-md hover:bg-[#2C494A] transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Find Matches</span>
            </button>

            <!-- Loading State -->
            <div id="loading-state" class="hidden text-center py-8 flex-1 flex items-center justify-center">
                <div class="inline-flex flex-col items-center space-y-4">
                    <div class="relative">
                        <div class="w-12 h-12 border-4 border-[#e1e7dd] border-t-[#33595a] rounded-full animate-spin"></div>
                    </div>
                    <div class="text-[#3e4732] font-medium">Finding compatible matches...</div>
                    <div class="text-[#bcbabb] text-sm">This may take a few moments</div>
                </div>
            </div>

            <!-- Matches Results -->
            <div id="matches-results" class="hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-[#3e4732]">Potential Matches</h3>
                    <div id="match-stats" class="text-sm text-[#bcbabb]">
                        <!-- Match statistics will be shown here -->
                    </div>
                </div>
                <div id="matches-list" class="space-y-4 h-[500px] overflow-y-auto">
                    <!-- Matches will be populated here -->
                </div>
            </div>

            <!-- No Matches State -->
            <div id="no-matches-state" class="hidden text-center py-8 flex-1 flex items-center justify-center">
                <div class="inline-flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-[#e1e7dd] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0118 12a8 8 0 10-8 8 7.962 7.962 0 01-2.291-.5"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#3e4732] mb-2">No Matches Found</h3>
                    <p class="text-[#bcbabb]">No compatible participants found at this time. Try again later or adjust the search criteria.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="view-details-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-4xl mx-4 rounded-lg shadow-xl border border-[#e1e7dd] max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-[#e1e7dd] flex items-center justify-between">
                <h3 class="text-xl font-semibold text-[#3e4732]">Participant Details</h3>
                <button id="view-details-modal-close" class="text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="view-details-content" class="p-6">
                <!-- Content will be loaded here -->
            </div>
            
            <div class="px-6 py-4 border-t border-[#e1e7dd] flex justify-end space-x-3">
                <button id="view-details-modal-close-btn" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.matchingContextParticipantId = {{ $participant->id }};

document.addEventListener('DOMContentLoaded', function() {
    const findMatchesBtn = document.getElementById('find-matches-btn');
    const loadingState = document.getElementById('loading-state');
    const matchesResults = document.getElementById('matches-results');
    const noMatchesState = document.getElementById('no-matches-state');
    const matchesList = document.getElementById('matches-list');

    findMatchesBtn.addEventListener('click', function() {
        // Show loading state
        findMatchesBtn.disabled = true;
        loadingState.classList.remove('hidden');
        matchesResults.classList.add('hidden');
        noMatchesState.classList.add('hidden');

        // Get the current participant ID from the URL
        const currentParticipantId = {{ $participant->id }};
        
        // Make AJAX request with the current participant ID
        fetch(`/sc/participants-matching/${currentParticipantId}/find-matches`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Hide loading state
                loadingState.classList.add('hidden');
                findMatchesBtn.disabled = false;

                if (data.error) {
                    alert(data.error);
                    return;
                }

                if (data.matches && data.matches.length > 0) {
                    // Display matches with cumulative information
                    displayMatches(data.matches, data.total_matches, data.new_matches_found);
                    matchesResults.classList.remove('hidden');
                } else {
                    // Show no matches state
                    noMatchesState.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error finding matches:', error);
                loadingState.classList.add('hidden');
                findMatchesBtn.disabled = false;
                alert('Error finding matches. Please try again.');
            });
    });

    function displayMatches(matches, totalMatches, newMatchesFound) {
        matchesList.innerHTML = '';
        
        // Update match statistics
        const matchStats = document.getElementById('match-stats');
        if (totalMatches > 0) {
            matchStats.innerHTML = `
                <span class="bg-[#e1e7dd] text-[#3e4732] px-2 py-1 rounded-full text-xs">
                    ${totalMatches} total matches
                </span>
                ${newMatchesFound > 0 ? `
                    <span class="bg-[#cc8e45] text-white px-2 py-1 rounded-full text-xs ml-2">
                        +${newMatchesFound} new
                    </span>
                ` : ''}
            `;
        } else {
            matchStats.innerHTML = '';
        }
        
        matches.forEach(match => {
            const matchElement = createMatchElement(match);
            matchesList.appendChild(matchElement);
        });
    }

    function createMatchElement(match) {
        const participant = match.participant;
        const score = match.score;
        const factors = match.compatibility_factors || [];
        const existingConversation = match.conversation_id ? { id: match.conversation_id } : null;
        const matchRequestStatus = match.match_request_status || null;
        const status = match.status || 'active';

        const matchDiv = document.createElement('div');
        matchDiv.className = 'bg-[#f8f1e1] rounded-lg p-4 border border-[#e1e7dd] hover:shadow-md transition-shadow duration-200';
        
        // Determine button content based on existing conversation and match request status
        let buttonContent = '';
        if (existingConversation) {
            // Conversation exists - show "See Conversation"
            buttonContent = `
                <button class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 flex items-center justify-center space-x-2 text-sm font-medium" onclick="viewConversation(${existingConversation.id})">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>See Conversation</span>
                </button>
            `;
        } else if (matchRequestStatus === 'accepted') {
            // Match request accepted but no conversation yet - show "Start Conversation"
            buttonContent = `
                <button class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 flex items-center justify-center space-x-2 text-sm font-medium" onclick="openSendModal(${participant.id})">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Start Conversation</span>
                </button>
            `;
        } else if (matchRequestStatus === 'pending') {
            // Match request pending - show "Request Sent"
            buttonContent = `
                <button class="flex-1 bg-yellow-600 text-white px-4 py-2 rounded-md cursor-not-allowed flex items-center justify-center space-x-2 text-sm font-medium" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Request Sent</span>
                </button>
            `;
        } else {
            // No existing conversation or match request - check match request status dynamically
            buttonContent = `
                <button class="flex-1 bg-[#33595a] text-white px-4 py-2 rounded-md hover:bg-[#2C494A] transition-colors duration-200 flex items-center justify-center space-x-2 text-sm font-medium" onclick="checkMatchRequestStatus(${participant.id}, this)" data-participant-id="${participant.id}" data-participant-code="${participant.participant_code_name || ''}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Request Match</span>
                </button>
            `;
        }
        
        matchDiv.innerHTML = `
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-14 h-14 bg-[#33595a] rounded-full flex items-center justify-center text-white font-bold text-lg">
                        ${participant.participant_code_name ? participant.participant_code_name.charAt(0) + participant.participant_code_name.charAt(1) : '??'}
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h4 class="font-semibold text-[#3e4732] text-lg" data-participant-id="${participant.id}" data-participant-code="${participant.participant_code_name || ''}">${participant.participant_code_name || 'No code assigned'}</h4>
                            ${status === 'active' ? '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Active</span>' : ''}
                            ${status === 'contacted' ? '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">Contacted</span>' : ''}
                            ${status === 'interested' ? '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">Interested</span>' : ''}
                            ${status === 'not_interested' ? '<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">Not Interested</span>' : ''}
                            ${status === 'matched' ? '<span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-medium">Matched</span>' : ''}
                            ${existingConversation ? '<span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full font-medium">Messaged</span>' : ''}
                        </div>
                        <p class="text-sm text-[#bcbabb]">Participant Code</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-[#33595a]">${score}%</div>
                    <div class="text-xs text-[#bcbabb] font-medium">Compatibility</div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                ${participant.age ? `<div class="bg-white p-2 rounded"><div class="text-xs text-[#bcbabb] font-medium">Age</div><div class="text-sm font-semibold text-[#3e4732]">${participant.age} years</div></div>` : ''}
                ${participant.primary_disability ? `<div class="bg-white p-2 rounded"><div class="text-xs text-[#bcbabb] font-medium">Disability</div><div class="text-sm font-semibold text-[#3e4732]">${participant.primary_disability}</div></div>` : ''}
                ${participant.state ? `<div class="bg-white p-2 rounded"><div class="text-xs text-[#bcbabb] font-medium">Location</div><div class="text-sm font-semibold text-[#3e4732]">${participant.suburb || ''}${participant.suburb && participant.state ? ', ' : ''}${participant.state || ''}</div></div>` : ''}
                ${participant.estimated_support_hours_sil_level ? `<div class="bg-white p-2 rounded"><div class="text-xs text-[#bcbabb] font-medium">Support Level</div><div class="text-sm font-semibold text-[#3e4732]">${participant.estimated_support_hours_sil_level}</div></div>` : ''}
            </div>
            
            ${factors.length > 0 ? `
                <div class="mb-4">
                    <h5 class="text-sm font-medium text-[#3e4732] mb-2">Why this match works:</h5>
                    <div class="flex flex-wrap gap-1">
                        ${factors.map(factor => `<span class="inline-block bg-[#33595a] text-white text-xs px-3 py-1 rounded-full font-medium">${factor}</span>`).join('')}
                    </div>
                </div>
            ` : ''}
            
            <div class="flex space-x-2 pt-3 border-t border-[#e1e7dd]">
                ${buttonContent}
                <button class="px-4 py-2 border border-[#33595a] text-[#33595a] rounded-md hover:bg-[#33595a] hover:text-white transition-colors duration-200 text-sm font-medium" onclick="viewDetails(${participant.id})">
                    View Details
                </button>
            </div>
        `;
        
        return matchDiv;
    }

    // View Details Modal helpers
    const viewDetailsModal = document.getElementById('view-details-modal');
    const viewDetailsModalClose = document.getElementById('view-details-modal-close');
    const viewDetailsModalCloseBtn = document.getElementById('view-details-modal-close-btn');

    function closeViewDetailsModal() {
        viewDetailsModal.classList.add('hidden');
        viewDetailsModal.classList.remove('flex');
    }

    viewDetailsModalClose.addEventListener('click', closeViewDetailsModal);
    viewDetailsModalCloseBtn.addEventListener('click', closeViewDetailsModal);
    
    viewDetailsModal.addEventListener('click', function(e) {
        if (e.target === viewDetailsModal) {
            closeViewDetailsModal();
        }
    });

    function viewDetails(participantId) {
        const modal = document.getElementById('view-details-modal');
        const content = document.getElementById('view-details-content');
        
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="w-12 h-12 border-4 border-[#e1e7dd] border-t-[#33595a] rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-[#3e4732] font-medium">Loading participant details...</p>
            </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        fetch(`/sc/participants/${participantId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-[#3e4732] mb-2">Error</h3>
                            <p class="text-[#bcbabb]">${data.error}</p>
                        </div>
                    `;
                    return;
                }
                
                const p = data.participant || data;
                content.innerHTML = `
                    <div class="space-y-6">
                        <div class="text-center border-b border-[#e1e7dd] pb-6">
                            <div class="w-20 h-20 bg-[#33595a] rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                                ${p.participant_code_name ? p.participant_code_name.charAt(0) + p.participant_code_name.charAt(1) : '??'}
                            </div>
                            <h2 class="text-2xl font-semibold text-[#3e4732] mb-2">${p.participant_code_name || 'No code assigned'}</h2>
                            <p class="text-[#bcbabb]">Participant Code</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-[#f8f1e1] p-4 rounded-lg">
                                <h3 class="font-semibold text-[#3e4732] mb-3 text-base">Basic Information</h3>
                                <div class="space-y-3">
                                    ${p.age ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Age:</span><span class="text-[#2C494A] font-medium">${p.age} years</span></div>` : ''}
                                    ${p.gender_identity ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Gender:</span><span class="text-[#2C494A] font-medium">${p.gender_identity}</span></div>` : ''}
                                    ${p.primary_disability ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Disability:</span><span class="text-[#2C494A] font-medium">${p.primary_disability}</span></div>` : ''}
                                    ${p.secondary_disability ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Secondary:</span><span class="text-[#2C494A] font-medium">${p.secondary_disability}</span></div>` : ''}
                                </div>
                            </div>
                            
                            <div class="bg-[#f8f1e1] p-4 rounded-lg">
                                <h3 class="font-semibold text-[#3e4732] mb-3 text-base">Support & Location</h3>
                                <div class="space-y-3">
                                    ${p.estimated_support_hours_sil_level ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Support:</span><span class="text-[#2C494A] font-medium">${p.estimated_support_hours_sil_level}</span></div>` : ''}
                                    ${p.night_support_type ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Night:</span><span class="text-[#2C494A] font-medium">${p.night_support_type}</span></div>` : ''}
                                    ${p.state ? `<div class="flex items-center"><span class="font-medium text-[#3e4732] w-24 text-sm">Location:</span><span class="text-[#2C494A] font-medium">${p.suburb || ''}${p.suburb && p.state ? ', ' : ''}${p.state || ''}</span></div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error fetching participant details:', error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[#3e4732] mb-2">Error</h3>
                        <p class="text-[#bcbabb]">Failed to load participant details. Please try again.</p>
                    </div>
                `;
            });
    }

    function viewConversation(conversationId) {
        window.location.href = `/sc/messages/${conversationId}`;
    }

    // Expose functions to global scope
    window.viewDetails = viewDetails;
    window.viewConversation = viewConversation;
    
    // The checkMatchRequestStatus and requestMatch functions are already available
    // from sc-db.blade.php parent layout
});
</script>
@endpush
@endsection

