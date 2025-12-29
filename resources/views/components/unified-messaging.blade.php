{{-- Unified Messaging Component --}}
@props([
    'conversations' => collect(),
    'initialConversationId' => null,
    'userType' => 'provider', // provider, coordinator, participant
    'routePrefix' => 'provider', // provider, sc, participant
    'emptyMessage' => 'No conversations yet'
])

<div class="h-[calc(100vh-180px)] grid grid-cols-1 lg:grid-cols-3 gap-4" x-data="unifiedMessaging()" x-init="init()">
    {{-- Left Panel: Conversations List --}}
    <div class="bg-white rounded-lg border border-[#e1e7dd] overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-[#e1e7dd] text-[#3e4732] font-semibold">
            <div class="flex items-center justify-between">
                <span>Messages</span>
                <span class="text-xs text-[#bcbabb]">{{ $conversations->count() }} conversations</span>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto divide-y">
            @forelse($conversations as $conversation)
                @php 
                    $lastMessage = $conversation->messages->first();
                    // Only count unread messages that were sent TO the current user (not by them)
                    $unreadCount = $conversation->messages()
                        ->where('receiver_id', auth()->id())
                        ->where('sender_id', '!=', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                    
                    // Determine sender info (the other person in the conversation)
                    $senderCode = '';
                    $senderRole = '';
                    
                    if ($userType === 'provider') {
                        // For providers, show their own participant name and matched participant code
                        if ($conversation->initiator_user_id === auth()->id()) {
                            // Provider initiated: show their participant name and matched participant code
                            $matchingParticipantName = trim(($conversation->initiatorParticipant?->first_name ?? '') . ' ' . ($conversation->initiatorParticipant?->last_name ?? ''));
                            $senderCode = 'Match for participant: ' . $matchingParticipantName;
                            $matchedParticipantCode = $conversation->recipientParticipant?->participant_code_name ?? 'PA-' . $conversation->recipientParticipant?->id;
                            $senderRole = 'Matched participant: ' . $matchedParticipantCode;
                        } else {
                            // Support coordinator initiated: show only matched participant code (not SC's participant name)
                            $matchedParticipantCode = $conversation->recipientParticipant?->participant_code_name ?? 'PA-' . $conversation->recipientParticipant?->id;
                            $senderCode = 'Match for participant: [Participant]';
                            $senderRole = 'Matched participant: ' . $matchedParticipantCode;
                        }
                    } elseif ($userType === 'coordinator') {
                        // For coordinators, show their own participant name and matched participant code
                        if ($conversation->initiator_user_id === auth()->id()) {
                            // Support coordinator initiated: show their participant name and matched participant code
                            $matchingParticipantName = trim(($conversation->initiatorParticipant?->first_name ?? '') . ' ' . ($conversation->initiatorParticipant?->last_name ?? ''));
                            $senderCode = 'Match for participant: ' . $matchingParticipantName;
                            $matchedParticipantCode = $conversation->recipientParticipant?->participant_code_name ?? 'PA-' . $conversation->recipientParticipant?->id;
                            $senderRole = 'Matched participant: ' . $matchedParticipantCode;
                        } else {
                            // Provider initiated: show support coordinator's participant name and provider's participant code
                            $matchingParticipantName = trim(($conversation->recipientParticipant?->first_name ?? '') . ' ' . ($conversation->recipientParticipant?->last_name ?? ''));
                            $senderCode = 'Match for participant: ' . $matchingParticipantName;
                            $matchedParticipantCode = $conversation->initiatorParticipant?->participant_code_name ?? 'PA-' . $conversation->initiatorParticipant?->id;
                            $senderRole = 'Matched participant: ' . $matchedParticipantCode;
                        }
                    } elseif ($userType === 'participant') {
                        // For participants, show sender code based on conversation type
                        if ($conversation->type === 'provider_to_sc') {
                            // Provider initiated conversation
                            $senderCode = $conversation->provider?->provider_code_name ?? 
                                        'PR-' . $conversation->provider?->id;
                            $senderRole = 'NDIS Provider';
                        } elseif ($conversation->type === 'provider_to_participant') {
                            // Provider initiated conversation directly to participant
                            $senderCode = $conversation->provider?->provider_code_name ?? 
                                        'PR-' . $conversation->provider?->id;
                            $senderRole = 'NDIS Provider';
                        } elseif ($conversation->type === 'participant_to_participant') {
                            // Participant to participant conversation
                            $senderCode = $conversation->senderParticipant?->participant_code_name ?? 
                                        'PA-' . $conversation->senderParticipant?->id;
                            $senderRole = 'Participant';
                        } else {
                            // Support coordinator initiated conversation
                            $senderCode = $conversation->supportCoordinator?->sup_coor_code_name ?? 
                                        'SC-' . $conversation->supportCoordinator?->id;
                            $senderRole = 'Support Coordinator';
                        }
                    }
                @endphp
                
                <button 
                    data-conv-id="{{ $conversation->id }}" 
                    class="w-full text-left px-4 py-3 hover:bg-[#f8f1e1] focus:bg-[#f8f1e1] transition-colors duration-200 {{ $initialConversationId == $conversation->id ? 'bg-[#f8f1e1] border-l-4 border-[#cc8e45]' : '' }}"
                    @click="selectConversation({{ $conversation->id }})"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-[#3e4732] text-sm truncate">{{ $senderCode }}</div>
                            <div class="text-xs text-[#cc8e45] font-medium mb-1">{{ $senderRole }}</div>
                            @if($lastMessage)
                                <div class="text-xs text-[#bcbabb] truncate">{{ Str::limit($lastMessage->content, 50) }}</div>
                            @else
                                <div class="text-xs text-[#bcbabb] italic">No messages yet</div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end ml-2">
                            @if($unreadCount > 0)
                                <span class="bg-[#cc8e45] text-white text-xs font-bold px-2 py-1 rounded-full mb-1">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                            <span class="text-xs text-[#bcbabb]">
                                {{ $conversation->last_message_at?->diffForHumans() ?? 'No activity' }}
                            </span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-6 text-center text-[#bcbabb]">
                    <svg class="w-12 h-12 mx-auto mb-3 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p>{{ $emptyMessage }}</p>
                </div>
            @endforelse
        </div>
        
        @if($conversations->hasPages())
            <div class="px-4 py-2 border-t border-[#e1e7dd]">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>

    {{-- Right Panel: Conversation View --}}
    <div class="lg:col-span-2 bg-white rounded-lg border border-[#e1e7dd] overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-[#e1e7dd] bg-[#f8f1e1]" x-show="selectedConversationId">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-[#cc8e45] rounded-full flex items-center justify-center text-white font-semibold mr-3">
                        <span x-text="senderCode.charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <div class="font-bold text-[#3e4732] text-lg" x-text="senderCode"></div>
                        <div class="text-sm text-[#cc8e45] font-medium" x-text="senderRole"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button 
                        x-show="currentParticipantId"
                        @click="viewParticipantProfile(currentParticipantId)"
                        class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>View Profile</span>
                    </button>
                    <div class="text-xs text-[#bcbabb]" x-text="lastMessageTime"></div>
                </div>
            </div>
        </div>
        
        {{-- Empty State --}}
        <div x-show="!selectedConversationId" class="flex-1 flex items-center justify-center text-[#bcbabb]">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-lg font-medium">Select a conversation</p>
                <p class="text-sm">Choose a conversation from the left panel to start messaging</p>
            </div>
        </div>

        {{-- Messages Container --}}
        <div 
            x-show="selectedConversationId" 
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 scroll-smooth relative"
            x-ref="messagesContainer"
            style="max-height: calc(100vh - 300px);"
            @scroll="handleScroll"
        >
            {{-- Scroll to Top Indicator --}}
            <div 
                x-show="showScrollToTop" 
                class="fixed top-4 right-4 z-10 bg-[#cc8e45] text-white p-2 rounded-full shadow-lg cursor-pointer hover:bg-[#b87a3e] transition-colors"
                @click="scrollToTop"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
            </div>
            <template x-for="message in messages" :key="message.id">
                <div :class="message.is_sender ? 'flex justify-end' : 'flex justify-start'">
                    <div 
                        :class="message.is_sender ? 'bg-[#cc8e45] text-white rounded-br-sm' : 'bg-white text-[#3e4732] rounded-bl-sm'"
                        class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg shadow-sm border"
                    >
                        <div class="text-sm leading-relaxed" x-text="message.content"></div>
                        <div 
                            :class="message.is_sender ? 'text-gray-200' : 'text-[#bcbabb]'"
                            class="text-xs mt-2 flex items-center justify-between"
                        >
                            <span x-text="message.time"></span>
                            <div class="flex items-center space-x-1">
                                <span x-show="message.is_sender && message.read_at" class="flex items-center">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1">Read</span>
                                </span>
                                <span x-show="message.is_sender && !message.read_at" class="text-gray-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            {{-- Loading State --}}
            <div x-show="loading" class="flex justify-center py-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#cc8e45]"></div>
            </div>
            
            {{-- Empty Messages State --}}
            <div x-show="!loading && messages.length === 0 && selectedConversationId" class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-600 mb-2">No messages yet</h3>
                <p class="text-sm text-gray-500">Start the conversation by sending a message below</p>
            </div>
        </div>

        {{-- Message Input --}}
        <div 
            x-show="selectedConversationId" 
            class="px-4 py-3 border-t border-[#e1e7dd] bg-white"
        >
            <form @submit.prevent="sendMessage" class="flex items-end space-x-2">
                <textarea 
                    x-model="messageContent"
                    @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                    rows="2"
                    placeholder="Type a message... (Shift+Enter for new line)"
                    class="flex-1 border border-[#e1e7dd] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] resize-none"
                    :disabled="sending"
                ></textarea>
                <button 
                    type="submit"
                    :disabled="sending || !messageContent.trim()"
                    class="px-4 py-2 rounded-md bg-[#cc8e45] text-white hover:bg-[#b87a3a] disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                >
                    <span x-show="!sending">Send</span>
                    <span x-show="sending">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
            
            {{-- Error/Success Messages --}}
            <div x-show="errorMessage" class="mt-2 text-sm text-red-600" x-text="errorMessage"></div>
            <div x-show="successMessage" class="mt-2 text-sm text-green-600" x-text="successMessage"></div>
        </div>
    </div>
</div>

{{-- Participant Profile Modal --}}
<div 
    x-show="showProfileModal" 
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    @click.self="showProfileModal = false"
    style="display: none;"
>
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-[#e1e7dd] px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-[#3e4732]">Participant Profile</h3>
            <button @click="showProfileModal = false" class="text-[#bcbabb] hover:text-[#3e4732]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <div x-show="loadingProfile" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#33595a] mx-auto"></div>
                <p class="mt-4 text-[#3e4732]">Loading profile...</p>
            </div>
            
            <div x-show="!loadingProfile && profileData" class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-[#33595a] rounded-full flex items-center justify-center text-white text-xl font-bold">
                        <span x-text="profileData.participant_code_name ? profileData.participant_code_name.substring(0, 2) : 'PA'"></span>
                    </div>
                    <div>
                        <h4 class="text-xl font-semibold text-[#3e4732]" x-text="profileData.participant_code_name || 'No code assigned'"></h4>
                        <p class="text-[#bcbabb]">Participant Code</p>
                    </div>
                </div>
                
                <template x-if="profileData.age">
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h5 class="font-semibold text-[#3e4732] mb-3">Basic Information</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <div x-show="profileData.age">
                                <span class="text-sm text-[#bcbabb]">Age:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.age + ' years old'"></span>
                            </div>
                            <div x-show="profileData.gender_identity || profileData.gender">
                                <span class="text-sm text-[#bcbabb]">Gender:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.gender_identity || profileData.gender || 'Not specified'"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template x-if="profileData.primary_disability">
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h5 class="font-semibold text-[#3e4732] mb-3">Disability & Support</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <div x-show="profileData.primary_disability">
                                <span class="text-sm text-[#bcbabb]">Primary:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.primary_disability"></span>
                            </div>
                            <div x-show="profileData.secondary_disability">
                                <span class="text-sm text-[#bcbabb]">Secondary:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.secondary_disability"></span>
                            </div>
                            <div x-show="profileData.estimated_support_hours_sil_level">
                                <span class="text-sm text-[#bcbabb]">Support Level:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.estimated_support_hours_sil_level"></span>
                            </div>
                            <div x-show="profileData.night_support_type">
                                <span class="text-sm text-[#bcbabb]">Night Support:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.night_support_type"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template x-if="profileData.suburb || profileData.state">
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h5 class="font-semibold text-[#3e4732] mb-3">Location</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <div x-show="profileData.suburb && profileData.state">
                                <span class="text-sm text-[#bcbabb]">Location:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.suburb + ', ' + profileData.state"></span>
                            </div>
                            <div x-show="profileData.move_in_availability">
                                <span class="text-sm text-[#bcbabb]">Availability:</span><br>
                                <span class="font-medium text-[#3e4732]" x-text="profileData.move_in_availability"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template x-if="profileData.interests_hobbies">
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h5 class="font-semibold text-[#3e4732] mb-3">Interests & Hobbies</h5>
                        <p class="text-[#2C494A] font-medium text-sm leading-relaxed" x-text="profileData.interests_hobbies"></p>
                    </div>
                </template>
                
                <template x-if="profileData.cultural_religious_practices">
                    <div class="bg-[#f8f1e1] p-4 rounded-lg">
                        <h5 class="font-semibold text-[#3e4732] mb-3">Cultural & Religious Practices</h5>
                        <p class="text-[#2C494A] font-medium text-sm leading-relaxed" x-text="profileData.cultural_religious_practices"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function unifiedMessaging() {
    return {
        selectedConversationId: {{ $initialConversationId ?? 'null' }},
        conversations: @json($conversations->toArray()),
        messages: [],
        messageContent: '',
        loading: false,
        sending: false,
        errorMessage: '',
        successMessage: '',
        senderCode: '',
        senderRole: '',
        lastMessageTime: '',
        userType: '{{ $userType }}',
        routePrefix: '{{ $routePrefix }}',
        showScrollToTop: false,
        currentParticipantId: null,
        showProfileModal: false,
        profileData: null,
        loadingProfile: false,

        init() {
            if (this.selectedConversationId) {
                this.loadConversation(this.selectedConversationId);
            }
        },

        async selectConversation(conversationId) {
            this.selectedConversationId = conversationId;
            this.messages = [];
            this.messageContent = '';
            this.errorMessage = '';
            this.successMessage = '';
            
            // Update active state
            document.querySelectorAll('[data-conv-id]').forEach(btn => {
                btn.classList.remove('bg-[#f8f1e1]', 'border-l-4', 'border-[#cc8e45]');
            });
            
            const activeBtn = document.querySelector(`[data-conv-id="${conversationId}"]`);
            if (activeBtn) {
                activeBtn.classList.add('bg-[#f8f1e1]', 'border-l-4', 'border-[#cc8e45]');
            }
            
            await this.loadConversation(conversationId);
        },

        async loadConversation(conversationId) {
            this.loading = true;
            
            try {
                const response = await fetch(`/${this.routePrefix}/messages/${conversationId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load conversation');
                }
                
                const data = await response.json();
                
                // Debug logging
                console.log('Conversation data:', data);
                console.log('Support coordinator code:', data.conversation?.support_coordinator_code);
                
                // Set sender info based on user type - show specific match context
                if (this.userType === 'provider') {
                    // For providers, show their own participant name and matched participant code
                    if (data.conversation?.initiator_user_id === {{ auth()->id() }}) {
                        // Provider initiated: show their participant name and matched participant code
                        const matchingParticipantName = data.conversation?.initiator_participant_name || 
                                                      'Participant ' + (data.conversation?.initiator_participant_id || '');
                        this.senderCode = 'Match for participant: ' + matchingParticipantName;
                        const matchedParticipantCode = data.conversation?.recipient_participant_code || 
                                                     data.conversation?.recipient_participant_code_name || 
                                                     'PA-' + (data.conversation?.recipient_participant_id || '');
                        this.senderRole = 'Matched participant: ' + matchedParticipantCode;
                    } else {
                        // Support coordinator initiated: show only matched participant code (not SC's participant name)
                        const matchedParticipantCode = data.conversation?.recipient_participant_code || 
                                                     data.conversation?.recipient_participant_code_name || 
                                                     'PA-' + (data.conversation?.recipient_participant_id || '');
                        this.senderCode = 'Match for participant: [Participant]';
                        this.senderRole = 'Matched participant: ' + matchedParticipantCode;
                    }
                } else if (this.userType === 'coordinator') {
                    // For coordinators, show their own participant name and matched participant code
                    if (data.conversation?.initiator_user_id === {{ auth()->id() }}) {
                        // Support coordinator initiated: show their participant name and matched participant code
                        const matchingParticipantName = data.conversation?.initiator_participant_name || 
                                                      'Participant ' + (data.conversation?.initiator_participant_id || '');
                        this.senderCode = 'Match for participant: ' + matchingParticipantName;
                        const matchedParticipantCode = data.conversation?.recipient_participant_code || 
                                                     data.conversation?.recipient_participant_code_name || 
                                                     'PA-' + (data.conversation?.recipient_participant_id || '');
                        this.senderRole = 'Matched participant: ' + matchedParticipantCode;
                    } else {
                        // Provider initiated: show support coordinator's participant name and provider's participant code
                        const matchingParticipantName = data.conversation?.recipient_participant_name || 
                                                      'Participant ' + (data.conversation?.recipient_participant_id || '');
                        this.senderCode = 'Match for participant: ' + matchingParticipantName;
                        const matchedParticipantCode = data.conversation?.initiator_participant_code || 
                                                     data.conversation?.initiator_participant_code_name || 
                                                     'PA-' + (data.conversation?.initiator_participant_id || '');
                        this.senderRole = 'Matched participant: ' + matchedParticipantCode;
                    }
                } else if (this.userType === 'participant') {
                    // For participants, show sender code based on conversation type
                    if (data.conversation?.type === 'provider_to_sc') {
                        // Provider initiated conversation
                        this.senderCode = data.conversation?.provider_code || 
                                        data.provider_code_name || 
                                        'PR-' + (data.conversation?.provider_id || '');
                        this.senderRole = 'NDIS Provider';
                    } else if (data.conversation?.type === 'provider_to_participant') {
                        // Provider initiated conversation directly to participant
                        this.senderCode = data.conversation?.provider_code || 
                                        data.provider_code_name || 
                                        'PR-' + (data.conversation?.provider_id || '');
                        this.senderRole = 'NDIS Provider';
                    } else if (data.conversation?.type === 'participant_to_participant') {
                        // Participant to participant conversation
                        this.senderCode = data.conversation?.sender_participant_code || 
                                        data.sender_participant_code_name || 
                                        'PA-' + (data.conversation?.sender_participant_id || '');
                        this.senderRole = 'Participant';
                    } else {
                        // Support coordinator initiated conversation
                        this.senderCode = data.conversation?.support_coordinator_code || 
                                        'SC-' + (data.conversation?.support_coordinator_id || '');
                        this.senderRole = 'Support Coordinator';
                    }
                }
                
                if (data.conversation?.last_message_at) {
                    const lastMsgDate = new Date(data.conversation.last_message_at);
                    this.lastMessageTime = isNaN(lastMsgDate.getTime()) ? '' : lastMsgDate.toLocaleString();
                } else {
                    this.lastMessageTime = '';
                }
                
                // Set current participant ID for profile viewing
                if (this.userType === 'participant') {
                    // For participants, show the other participant's profile
                    if (data.conversation?.type === 'participant_to_participant') {
                        // Find the other participant ID
                        const currentUserId = {{ auth()->id() }};
                        if (data.conversation?.initiator_user_id === currentUserId) {
                            this.currentParticipantId = data.conversation?.recipient_participant_id;
                        } else {
                            this.currentParticipantId = data.conversation?.initiator_participant_id;
                        }
                    } else {
                        // For SC/Provider conversations, show the participant's own profile or the matched participant
                        this.currentParticipantId = data.conversation?.participant_id || 
                                                    data.conversation?.recipient_participant_id || 
                                                    data.conversation?.initiator_participant_id;
                    }
                } else {
                    // For SC/Provider, show the matched participant's profile
                    this.currentParticipantId = data.conversation?.recipient_participant_id || 
                                              data.conversation?.participant_id ||
                                              data.conversation?.initiator_participant_id;
                }
                
                // Process messages
                this.messages = (data.messages || []).map(msg => ({
                    id: msg.id,
                    content: msg.content,
                    is_sender: msg.is_sender || msg.sender_id === {{ auth()->id() }},
                    time: this.formatTime(msg.created_at),
                    read_at: msg.read_at
                }));
                
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
                
            } catch (error) {
                console.error('Error loading conversation:', error);
                this.errorMessage = 'Failed to load conversation';
            } finally {
                this.loading = false;
            }
        },

        async sendMessage() {
            // Prevent duplicate submissions
            if (!this.messageContent.trim() || this.sending) {
                return;
            }
            
            // Set sending flag immediately to prevent race conditions
            this.sending = true;
            const messageToSend = this.messageContent.trim();
            this.messageContent = ''; // Clear input immediately to prevent double submission
            this.errorMessage = '';
            this.successMessage = '';
            
            try {
                const response = await fetch(`/${this.routePrefix}/messages/${this.selectedConversationId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: messageToSend })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to send message');
                }
                
                const data = await response.json();
                
                // Check if message was already added (prevent duplicates)
                const messageExists = this.messages.some(msg => msg.id === data.data.id);
                if (!messageExists) {
                    // Add message to UI
                    this.messages.push({
                        id: data.data.id,
                        content: data.data.content,
                        is_sender: true,
                        time: this.formatTime(data.data.created_at),
                        read_at: data.data.read_at
                    });
                }
                
                this.successMessage = data.message || 'Message sent';
                
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
                
                // Update conversation list preview
                this.updateConversationPreview(data.data.content);
                
                setTimeout(() => {
                    this.successMessage = '';
                }, 3000);
                
            } catch (error) {
                console.error('Error sending message:', error);
                // Restore message content on error
                this.messageContent = messageToSend;
                this.errorMessage = error.message || 'Failed to send message';
                setTimeout(() => {
                    this.errorMessage = '';
                }, 5000);
            } finally {
                this.sending = false;
            }
        },

        updateConversationPreview(content) {
            const activeBtn = document.querySelector(`[data-conv-id="${this.selectedConversationId}"]`);
            if (activeBtn) {
                const previewElement = activeBtn.querySelector('.text-xs.text-\\[\\#bcbabb\\].truncate');
                if (previewElement) {
                    previewElement.textContent = content.length > 50 ? content.substring(0, 50) + '...' : content;
                }
                
                // Remove unread badge when current user sends a message
                // (since they won't have unread messages from themselves)
                const badge = activeBtn.querySelector('.bg-\\[\\#cc8e45\\]');
                if (badge) {
                    badge.remove();
                }
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    // Smooth scroll to bottom
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        },

        scrollToTop() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        },

        handleScroll() {
            const container = this.$refs.messagesContainer;
            if (container) {
                // Show scroll to top button when scrolled down more than 200px
                this.showScrollToTop = container.scrollTop > 200;
            }
        },

        formatTime(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return '';
            }
            
            const now = new Date();
            const diffInHours = (now - date) / (1000 * 60 * 60);
            
            if (diffInHours < 24) {
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
            }
        },

        async viewParticipantProfile(participantId) {
            if (!participantId) return;
            
            this.loadingProfile = true;
            this.showProfileModal = true;
            this.profileData = null;
            
            try {
                let url = '';
                if (this.userType === 'participant') {
                    url = `/participant/participants/${participantId}/details`;
                } else if (this.userType === 'coordinator') {
                    url = `/sc/participants/${participantId}/details`;
                } else if (this.userType === 'provider') {
                    url = `/provider/participants/${participantId}/details`;
                }
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load participant profile');
                }
                
                const data = await response.json();
                const p = data.success ? data.participant : (data.participant || data);
                
                if (p && p.participant_code_name) {
                    this.profileData = p;
                } else {
                    throw new Error('Participant data not found');
                }
            } catch (error) {
                console.error('Error loading participant profile:', error);
                this.errorMessage = 'Failed to load participant profile';
                this.showProfileModal = false;
            } finally {
                this.loadingProfile = false;
            }
        }
    }
}
</script>
@endpush

<style>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #999;
}
</style>
