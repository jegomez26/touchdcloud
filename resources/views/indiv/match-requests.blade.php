@extends('indiv.indiv-db')

@section('main-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#3e4732]">Match Requests</h1>
            <p class="text-[#bcbabb] mt-2">View and manage your match requests</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md border border-[#e1e7dd]">
        <div class="border-b border-[#e1e7dd]">
            <nav class="flex -mb-px">
                <button id="pending-tab" class="tab-button active px-6 py-3 text-sm font-medium text-center border-b-2 border-[#33595a] text-[#33595a]">
                    Pending Requests
                    @if($pendingCount > 0)
                        <span class="ml-2 px-2 py-1 text-xs font-semibold bg-[#33595a] text-white rounded-full">{{ $pendingCount }}</span>
                    @endif
                </button>
                <button id="sent-tab" class="tab-button px-6 py-3 text-sm font-medium text-center border-b-2 border-transparent text-[#bcbabb] hover:text-[#3e4732] hover:border-[#bcbabb]">
                    Sent Requests
                </button>
                <button id="accepted-tab" class="tab-button px-6 py-3 text-sm font-medium text-center border-b-2 border-transparent text-[#bcbabb] hover:text-[#3e4732] hover:border-[#bcbabb]">
                    Accepted
                </button>
            </nav>
        </div>

        <!-- Pending Requests Tab Content -->
        <div id="pending-content" class="tab-content p-6">
            @if($pendingRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingRequests as $request)
                        @php
                            $participantCode = $request->senderParticipant->participant_code_name ?? 'N/A';
                            $participantId = $request->senderParticipant->id ?? null;
                        @endphp
                        <div class="bg-[#f8f1e1] rounded-lg border border-[#e1e7dd] p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-[#33595a] rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($participantCode, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-[#3e4732]">{{ $participantCode }}</h3>
                                            <p class="text-sm text-[#bcbabb]">Participant Code</p>
                                        </div>
                                    </div>
                                    @if($request->message)
                                        <p class="text-[#3e4732] mb-4 italic">"{{ $request->message }}"</p>
                                    @endif
                                    <p class="text-xs text-[#bcbabb]">Received {{ $request->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    @if($participantId)
                                        <button onclick="viewParticipantProfile({{ $participantId }})" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                            View Profile
                                        </button>
                                    @endif
                                    <button onclick="showAcceptModal({{ $request->id }}, '{{ $participantCode }}')" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                                        Accept
                                    </button>
                                    <button onclick="showRejectModal({{ $request->id }}, '{{ $participantCode }}')" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-[#e1e7dd] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#3e4732] mb-2">No Pending Requests</h3>
                    <p class="text-[#bcbabb]">You don't have any pending match requests at this time.</p>
                </div>
            @endif
        </div>

        <!-- Sent Requests Tab Content -->
        <div id="sent-content" class="tab-content hidden p-6">
            @if($sentRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($sentRequests as $request)
                        @php
                            $participantCode = $request->receiverParticipant->participant_code_name ?? 'N/A';
                            $participantId = $request->receiverParticipant->id ?? null;
                            $senderParticipant = $request->senderParticipant;
                            $senderParticipantName = $senderParticipant ? trim(($senderParticipant->first_name ?? '') . ' ' . ($senderParticipant->last_name ?? '')) : null;
                        @endphp
                        <div class="bg-[#f8f1e1] rounded-lg border border-[#e1e7dd] p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-[#33595a] rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($participantCode, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-[#3e4732]">{{ $participantCode }}</h3>
                                            @if($senderParticipantName)
                                                <p class="text-sm text-[#cc8e45] font-medium">Match for participant: {{ $senderParticipantName }}</p>
                                            @endif
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($request->status === 'accepted') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($request->message)
                                        <p class="text-[#3e4732] mb-4 italic">"{{ $request->message }}"</p>
                                    @endif
                                    <p class="text-xs text-[#bcbabb]">Sent {{ $request->created_at->diffForHumans() }}</p>
                                </div>
                                @if($participantId)
                                    <div class="ml-4">
                                        <button onclick="viewParticipantProfile({{ $participantId }})" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                            View Profile
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-[#e1e7dd] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#3e4732] mb-2">No Sent Requests</h3>
                    <p class="text-[#bcbabb]">You haven't sent any match requests yet.</p>
                </div>
            @endif
        </div>

        <!-- Accepted Requests Tab Content -->
        <div id="accepted-content" class="tab-content hidden p-6">
            @if($acceptedRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($acceptedRequests as $request)
                        @php
                            // Determine which participant is the "other" participant (not the current user's participant)
                            $currentUser = auth()->user();
                            $currentParticipant = $currentUser->participant;
                            $participantCode = null;
                            $participantId = null;
                            $senderParticipant = $request->senderParticipant;
                            $receiverParticipant = $request->receiverParticipant;
                            $senderParticipantName = $senderParticipant ? trim(($senderParticipant->first_name ?? '') . ' ' . ($senderParticipant->last_name ?? '')) : null;
                            $receiverParticipantName = $receiverParticipant ? trim(($receiverParticipant->first_name ?? '') . ' ' . ($receiverParticipant->last_name ?? '')) : null;
                            $senderParticipantCode = $senderParticipant->participant_code_name ?? null;
                            $receiverParticipantCode = $receiverParticipant->participant_code_name ?? null;
                            
                            if ($currentParticipant) {
                                if ($request->sender_participant_id == $currentParticipant->id) {
                                    // Current user sent the request, show receiver
                                    $participantCode = $receiverParticipantCode ?? 'N/A';
                                    $participantId = $request->receiverParticipant->id ?? null;
                                    $matchForName = $senderParticipantName;
                                } else {
                                    // Current user received the request, show sender
                                    $participantCode = $senderParticipantCode ?? 'N/A';
                                    $participantId = $request->senderParticipant->id ?? null;
                                    $matchForName = $receiverParticipantName;
                                }
                            } else {
                                // Fallback if no current participant
                                $participantCode = $receiverParticipantCode ?? ($senderParticipantCode ?? 'N/A');
                                $participantId = $request->receiverParticipant->id ?? ($request->senderParticipant->id ?? null);
                                $matchForName = $senderParticipantName;
                            }
                        @endphp
                        <div class="bg-[#f8f1e1] rounded-lg border border-[#e1e7dd] p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($participantCode, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-[#3e4732]">{{ $participantCode }}</h3>
                                            @if($matchForName)
                                                <p class="text-sm text-[#cc8e45] font-medium">Match for participant: {{ $matchForName }}</p>
                                            @endif
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Accepted
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-[#bcbabb]">Accepted {{ $request->responded_at ? $request->responded_at->diffForHumans() : $request->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    @if($participantId)
                                        <button onclick="viewParticipantProfile({{ $participantId }})" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                            View Profile
                                        </button>
                                    @endif
                                    <a href="{{ route('indiv.messages.inbox') }}" class="px-4 py-2 bg-[#33595a] text-white rounded-md hover:bg-[#2C494A] transition-colors duration-200 text-sm font-medium">
                                        Start Conversation
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-[#e1e7dd] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#3e4732] mb-2">No Accepted Requests</h3>
                    <p class="text-[#bcbabb]">You don't have any accepted match requests yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Accept Confirmation Modal -->
<div id="accept-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Accept Match Request</h3>
                <button onclick="closeAcceptModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-gray-700 mb-6">Are you sure you want to accept the match request from <strong id="accept-participant-code"></strong>? You will be able to start a conversation once accepted.</p>
            <div class="flex space-x-3">
                <button onclick="closeAcceptModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button id="confirm-accept-btn" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                    Accept
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="reject-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reject Match Request</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-gray-700 mb-6">Are you sure you want to reject the match request from <strong id="reject-participant-code"></strong>? This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button onclick="closeRejectModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button id="confirm-reject-btn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                    Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Participant Profile Modal -->
<div id="participant-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Participant Profile</h3>
                <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="profile-content" class="space-y-4">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#33595a]"></div>
                    <span class="ml-3 text-[#3e4732]">Loading profile...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentRequestId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.id.replace('-tab', '-content');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => {
                t.classList.remove('active', 'border-[#33595a]', 'text-[#33595a]');
                t.classList.add('border-transparent', 'text-[#bcbabb]');
            });
            contents.forEach(c => c.classList.add('hidden'));

            // Add active class to clicked tab
            this.classList.add('active', 'border-[#33595a]', 'text-[#33595a]');
            this.classList.remove('border-transparent', 'text-[#bcbabb]');

            // Show corresponding content
            document.getElementById(targetId).classList.remove('hidden');
        });
    });
});

// Accept Modal Functions
function showAcceptModal(requestId, participantCode) {
    currentRequestId = requestId;
    document.getElementById('accept-participant-code').textContent = participantCode;
    document.getElementById('accept-confirm-modal').classList.remove('hidden');
    
    // Remove previous event listeners
    const confirmBtn = document.getElementById('confirm-accept-btn');
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Add new event listener
    newConfirmBtn.addEventListener('click', function() {
        acceptMatchRequest(requestId);
    });
}

function closeAcceptModal() {
    document.getElementById('accept-confirm-modal').classList.add('hidden');
    currentRequestId = null;
}

// Reject Modal Functions
function showRejectModal(requestId, participantCode) {
    currentRequestId = requestId;
    document.getElementById('reject-participant-code').textContent = participantCode;
    document.getElementById('reject-confirm-modal').classList.remove('hidden');
    
    // Remove previous event listeners
    const confirmBtn = document.getElementById('confirm-reject-btn');
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Add new event listener
    newConfirmBtn.addEventListener('click', function() {
        rejectMatchRequest(requestId);
    });
}

function closeRejectModal() {
    document.getElementById('reject-confirm-modal').classList.add('hidden');
    currentRequestId = null;
}

// Accept/Reject functions
function acceptMatchRequest(requestId) {
    const btn = document.getElementById('confirm-accept-btn');
    btn.disabled = true;
    btn.textContent = 'Accepting...';

    fetch(`/match-requests/${requestId}/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error(text || 'Unexpected non-JSON response');
            }
        });
    })
    .then(data => {
        closeAcceptModal();
        if (data.success) {
            alert('Match request accepted! You can now start a conversation.');
            location.reload();
        } else {
            alert(data.error || 'Failed to accept match request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to accept match request. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Accept';
    });
}

function rejectMatchRequest(requestId) {
    const btn = document.getElementById('confirm-reject-btn');
    btn.disabled = true;
    btn.textContent = 'Rejecting...';

    fetch(`/match-requests/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error(text || 'Unexpected non-JSON response');
            }
        });
    })
    .then(data => {
        closeRejectModal();
        if (data.success) {
            alert('Match request rejected');
            location.reload();
        } else {
            alert(data.error || 'Failed to reject match request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to reject match request. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Reject';
    });
}

// Profile View Functions
function viewParticipantProfile(participantId) {
    const modal = document.getElementById('participant-profile-modal');
    const content = document.getElementById('profile-content');
    
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#33595a]"></div>
            <span class="ml-3 text-[#3e4732]">Loading profile...</span>
        </div>
    `;
    
    fetch(`/participant/participants/${participantId}/details`)
        .then(response => response.json())
        .then(data => {
            const p = data.success ? data.participant : (data.participant || data);
            if (p && p.participant_code_name) {
                content.innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-[#33595a] rounded-full flex items-center justify-center text-white text-xl font-bold">
                                ${p.participant_code_name ? p.participant_code_name.substring(0, 2) : 'PA'}
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-[#3e4732]">${p.participant_code_name || 'No code assigned'}</h4>
                                <p class="text-[#bcbabb]">Participant Code</p>
                            </div>
                        </div>
                        ${p.age ? `
                        <div class="bg-[#f8f1e1] p-4 rounded-lg">
                            <h5 class="font-semibold text-[#3e4732] mb-3">Basic Information</h5>
                            <div class="grid grid-cols-2 gap-3">
                                ${p.age ? `<div><span class="text-sm text-[#bcbabb]">Age:</span><br><span class="font-medium text-[#3e4732]">${p.age} years old</span></div>` : ''}
                                ${(p.gender_identity || p.gender) ? `<div><span class="text-sm text-[#bcbabb]">Gender:</span><br><span class="font-medium text-[#3e4732]">${p.gender_identity || p.gender || 'Not specified'}</span></div>` : ''}
                            </div>
                        </div>
                        ` : ''}
                        ${p.primary_disability ? `
                        <div class="bg-[#f8f1e1] p-4 rounded-lg">
                            <h5 class="font-semibold text-[#3e4732] mb-3">Disability & Support</h5>
                            <div class="grid grid-cols-2 gap-3">
                                ${p.primary_disability ? `<div><span class="text-sm text-[#bcbabb]">Primary:</span><br><span class="font-medium text-[#3e4732]">${p.primary_disability}</span></div>` : ''}
                                ${p.secondary_disability ? `<div><span class="text-sm text-[#bcbabb]">Secondary:</span><br><span class="font-medium text-[#3e4732]">${p.secondary_disability}</span></div>` : ''}
                                ${p.estimated_support_hours_sil_level ? `<div><span class="text-sm text-[#bcbabb]">Support Level:</span><br><span class="font-medium text-[#3e4732]">${p.estimated_support_hours_sil_level}</span></div>` : ''}
                                ${p.night_support_type ? `<div><span class="text-sm text-[#bcbabb]">Night Support:</span><br><span class="font-medium text-[#3e4732]">${p.night_support_type}</span></div>` : ''}
                            </div>
                        </div>
                        ` : ''}
                        ${p.suburb && p.state ? `
                        <div class="bg-[#f8f1e1] p-4 rounded-lg">
                            <h5 class="font-semibold text-[#3e4732] mb-3">Location</h5>
                            <div class="grid grid-cols-2 gap-3">
                                <div><span class="text-sm text-[#bcbabb]">Location:</span><br><span class="font-medium text-[#3e4732]">${p.suburb}, ${p.state}</span></div>
                                ${p.move_in_availability ? `<div><span class="text-sm text-[#bcbabb]">Availability:</span><br><span class="font-medium text-[#3e4732]">${p.move_in_availability}</span></div>` : ''}
                            </div>
                        </div>
                        ` : ''}
                        ${p.interests_hobbies ? `
                        <div class="bg-[#f8f1e1] p-4 rounded-lg">
                            <h5 class="font-semibold text-[#3e4732] mb-3">Interests & Hobbies</h5>
                            <p class="text-[#2C494A] font-medium text-sm leading-relaxed">${p.interests_hobbies}</p>
                        </div>
                        ` : ''}
                        ${p.cultural_religious_practices ? `
                        <div class="bg-[#f8f1e1] p-4 rounded-lg">
                            <h5 class="font-semibold text-[#3e4732] mb-3">Cultural & Religious Practices</h5>
                            <p class="text-[#2C494A] font-medium text-sm leading-relaxed">${p.cultural_religious_practices}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-600">Unable to load participant profile.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600">Error loading participant profile. Please try again.</p>
                </div>
            `;
        });
}

function closeProfileModal() {
    document.getElementById('participant-profile-modal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('accept-confirm-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAcceptModal();
    }
});

document.getElementById('reject-confirm-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

document.getElementById('participant-profile-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProfileModal();
    }
});
</script>
@endpush
@endsection
