@extends('company.provider-db')

@section('main-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('provider.participants.matching.index') }}" 
               class="p-2 text-[#bcbabb] hover:text-[#3e4732] hover:bg-[#e1e7dd] rounded-md transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-[#3e4732]">Find Matches for {{ $participant->first_name }}</h1>
                <p class="text-[#bcbabb] mt-2">Find compatible housemates for {{ $participant->first_name }} {{ $participant->last_name }}</p>
            </div>
        </div>
    </div>

    <!-- Two Panel Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Panel - Participant Information -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-[#e1e7dd]">
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
        <div class="bg-white rounded-lg shadow-md p-6 border border-[#e1e7dd]">
            <h2 class="text-xl font-semibold text-[#3e4732] mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Find Compatible Matches
            </h2>

            <p class="text-[#bcbabb] mb-6">Click the button below to find participants who might be compatible housemates for {{ $participant->first_name }}.</p>

            <button id="find-matches-btn" 
                    class="w-full bg-[#33595a] text-white px-6 py-3 rounded-md hover:bg-[#2C494A] transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Find Matches</span>
            </button>

            <!-- Loading State -->
            <div id="loading-state" class="hidden mt-6 text-center py-8">
                <div class="inline-flex flex-col items-center space-y-4">
                    <div class="relative">
                        <div class="w-12 h-12 border-4 border-[#e1e7dd] border-t-[#33595a] rounded-full animate-spin"></div>
                    </div>
                    <div class="text-[#3e4732] font-medium">Finding compatible matches...</div>
                    <div class="text-[#bcbabb] text-sm">This may take a few moments</div>
                </div>
            </div>

            <!-- Matches Results -->
            <div id="matches-results" class="hidden mt-6">
                <h3 class="text-lg font-semibold text-[#3e4732] mb-4">Potential Matches</h3>
                <div id="matches-list" class="space-y-4 max-h-96 overflow-y-auto">
                    <!-- Matches will be populated here -->
                </div>
            </div>

            <!-- No Matches State -->
            <div id="no-matches-state" class="hidden mt-6 text-center py-8">
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

    <!-- Send Message Modal -->
    <div id="send-message-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg mx-4 rounded-lg shadow-xl border border-[#e1e7dd]">
            <div class="px-6 py-4 border-b border-[#e1e7dd] flex items-center justify-between">
                <h3 class="text-lg font-semibold text-[#3e4732]">Send Message</h3>
                <button id="send-modal-close" class="text-[#bcbabb] hover:text-[#3e4732]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-[#6b7280]">Your message will be sent to the owner of this participant. You will see only the participant code.</p>
                <textarea id="send-message-text" class="w-full border border-[#e1e7dd] rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-[#cc8e45]" rows="5" placeholder="Type your message..."></textarea>
                <div id="send-message-error" class="hidden text-sm text-red-600"></div>
            </div>
            <div class="px-6 py-4 border-t border-[#e1e7dd] flex items-center justify-end space-x-2">
                <button id="send-modal-cancel" class="px-4 py-2 rounded-md border border-[#e1e7dd] text-[#3e4732] hover:bg-[#f8f1e1]">Cancel</button>
                <button id="send-modal-submit" class="px-4 py-2 rounded-md bg-[#33595a] text-white hover:bg-[#2C494A] disabled:opacity-50">Send</button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
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

        // Make AJAX request
        fetch(`{{ route('provider.participants.matching.find', $participant) }}`)
            .then(response => response.json())
            .then(data => {
                // Hide loading state
                loadingState.classList.add('hidden');
                findMatchesBtn.disabled = false;

                if (data.matches && data.matches.length > 0) {
                    // Display matches
                    displayMatches(data.matches);
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

    function displayMatches(matches) {
        matchesList.innerHTML = '';
        
        matches.forEach(match => {
            const matchElement = createMatchElement(match);
            matchesList.appendChild(matchElement);
        });
    }

    let currentTargetParticipantId = null;

    function createMatchElement(match) {
        const participant = match.participant;
        const score = match.score;
        const factors = match.compatibility_factors;

        const matchDiv = document.createElement('div');
        matchDiv.className = 'bg-[#f8f1e1] rounded-lg p-4 border border-[#e1e7dd] hover:shadow-md transition-shadow duration-200';
        
        matchDiv.innerHTML = `
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-14 h-14 bg-[#33595a] rounded-full flex items-center justify-center text-white font-bold text-lg">
                        ${participant.participant_code_name ? participant.participant_code_name.charAt(0) + participant.participant_code_name.charAt(1) : '??'}
                    </div>
                    <div>
                        <h4 class="font-semibold text-[#3e4732] text-lg">${participant.participant_code_name || 'No code assigned'}</h4>
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
                <button class="flex-1 bg-[#33595a] text-white px-4 py-2 rounded-md hover:bg-[#2C494A] transition-colors duration-200 flex items-center justify-center space-x-2 text-sm font-medium" onclick="openSendModal(${participant.id})">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Send Message</span>
                </button>
                <button class="px-4 py-2 border border-[#33595a] text-[#33595a] rounded-md hover:bg-[#33595a] hover:text-white transition-colors duration-200 text-sm font-medium" onclick="viewDetails(${participant.id})">
                    View Details
                </button>
            </div>
        `;
        
        return matchDiv;
    }

    // Modal helpers
    const sendModal = document.getElementById('send-message-modal');
    const sendModalClose = document.getElementById('send-modal-close');
    const sendModalCancel = document.getElementById('send-modal-cancel');
    const sendModalSubmit = document.getElementById('send-modal-submit');
    const sendMessageText = document.getElementById('send-message-text');
    const sendMessageError = document.getElementById('send-message-error');

    function openSendModal(participantId) {
        currentTargetParticipantId = participantId;
        sendMessageText.value = '';
        sendMessageError.classList.add('hidden');
        sendModal.classList.remove('hidden');
        sendModal.classList.add('flex');
        sendMessageText.focus();
    }

    function closeSendModal() {
        sendModal.classList.add('hidden');
        sendModal.classList.remove('flex');
        currentTargetParticipantId = null;
    }

    function submitSendMessage() {
        const content = sendMessageText.value.trim();
        if (!content) {
            sendMessageError.textContent = 'Please enter a message.';
            sendMessageError.classList.remove('hidden');
            return;
        }
        if (!currentTargetParticipantId) {
            return;
        }

        sendModalSubmit.disabled = true;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/provider/participants/${currentTargetParticipantId}/messages/send-to-owner`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content })
        })
        .then(async (res) => {
            if (!res.ok) {
                const data = await res.json().catch(() => ({ message: 'Failed to send message' }));
                throw new Error(data.message || 'Failed to send message');
            }
            return res.json();
        })
        .then(() => {
            closeSendModal();
            alert('Message sent successfully.');
        })
        .catch((err) => {
            sendMessageError.textContent = err.message || 'Error sending message';
            sendMessageError.classList.remove('hidden');
        })
        .finally(() => {
            sendModalSubmit.disabled = false;
        });
    }

    // Wire modal controls
    sendModalClose.addEventListener('click', closeSendModal);
    sendModalCancel.addEventListener('click', closeSendModal);
    sendModal.addEventListener('click', (e) => {
        if (e.target === sendModal) closeSendModal();
    });
    sendModalSubmit.addEventListener('click', submitSendMessage);

    function viewDetails(participantId) {
        // TODO: Implement view details functionality
        alert('View details functionality will be implemented here. Participant ID: ' + participantId);
    }
    // Expose functions to global scope for inline onclick handlers
    window.openSendModal = openSendModal;
    window.viewDetails = viewDetails;
});
</script>
@endpush
@endsection
