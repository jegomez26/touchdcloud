@extends('supcoor.sc-db')

@section('main-content')
    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-xl shadow-md-light min-h-[calc(100vh-var(--header-height, 100px))] flex flex-col">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-primary-dark mb-6 sm:mb-8 tracking-tight">Your Inbox</h2>

        @if ($conversations->isEmpty())
            <p class="text-text-light text-base sm:text-lg py-6 sm:py-10 text-center">
                You have no messages yet. Start by connecting with participants from the
                <a href="{{ route('sc.supcoor.unassigned_participants') }}" class="text-primary-light hover:underline font-semibold">Unassigned Participants</a> list.
            </p>
        @else
            <div class="flex flex-col md:flex-row gap-4 md:gap-8 flex-1" x-data="conversationPanel()">
                {{-- Conversation List (Sidebar) --}}
                <div id="conversation-sidebar"
                     class="w-full md:w-1/3 bg-secondary-bg rounded-xl p-4 md:p-5 overflow-y-auto border border-border-light shadow-sm flex-shrink-0"
                     x-show="showList"> {{-- Alpine controls visibility --}}

                    <h3 class="font-bold text-lg sm:text-xl text-primary-dark mb-4 md:mb-5 border-b pb-3 border-border-light">Conversations</h3>
                    <ul id="conversation-list">
                        @foreach ($conversations as $conversation)
                            <li class="mb-2 sm:mb-3 last:mb-0">
                                <a href="#" data-conversation-id="{{ $conversation->id }}"
                                   class="conversation-item flex items-center p-3 sm:p-4 rounded-lg hover:bg-gray-100 transition-all duration-200 ease-in-out
                                        {{ $initialConversationId == $conversation->id ? 'bg-primary-light text-white shadow-sm' : 'bg-white border border-border-light' }}"
                                   @click.prevent="selectConversation({{ $conversation->id }})">
                                    <div class="flex-shrink-0 relative">
                                        @php
                                            $avatarPath = 'images/general.png';
                                            if ($conversation->participant->profile_avatar_url) {
                                                $avatarPath = 'storage/' . $conversation->participant->profile_avatar_url;
                                            } elseif ($conversation->participant->gender === 'Male') {
                                                $avatarPath = 'images/male' . rand(1, 2) . '.png';
                                            } elseif ($conversation->participant->gender === 'Female') {
                                                $avatarPath = 'images/female' . rand(1, 2) . '.png';
                                            }
                                        @endphp
                                        <img src="{{ asset($avatarPath) }}" alt="Participant Avatar"
                                             class="w-10 h-10 sm:w-12 sm:h-12 rounded-full mr-3 sm:mr-4 object-cover border-2 border-white shadow-sm">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm sm:text-base font-semibold {{ $initialConversationId == $conversation->id ? 'text-white' : 'text-text-dark' }} leading-tight">
                                            {{ $conversation->participant->participant_code_name ?? 'Unknown Participant' }}
                                        </p>
                                        @if ($conversation->messages->isNotEmpty())
                                            <p class="text-xs sm:text-sm {{ $initialConversationId == $conversation->id ? 'text-gray-200' : 'text-text-light' }} truncate w-full mt-1">
                                                {{ $conversation->messages->first()->content }}
                                            </p>
                                        @else
                                            <p class="text-xs sm:text-sm {{ $initialConversationId == $conversation->id ? 'text-gray-200' : 'text-text-light' }} italic">No messages yet.</p>
                                        @endif
                                    </div>
                                    @php
                                        $unreadCount = $conversation->messages()->where('receiver_id', Auth::id())->whereNull('read_at')->count();
                                    @endphp
                                    @if ($unreadCount > 0)
                                        <span class="ml-2 sm:ml-4 flex-shrink-0 bg-accent-yellow text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full shadow-sm">
                                            {{ $unreadCount }} New
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Message Display Area and Input --}}
                <div id="message-view-area"
                     class="w-full md:w-2/3 bg-white rounded-xl border border-border-light flex flex-col shadow-md-light h-[calc(85vh-var(--header-height,100px))]"
                     x-show="showMessage">

                    {{-- Back button for mobile --}}
                    <div class="md:hidden p-3 border-b border-border-light bg-secondary-bg" x-show="showMessage && selectedConversationId">
                        <button id="back-to-conversations" class="text-primary-dark hover:text-primary-light flex items-center font-semibold"
                                @click="showConversationList()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Conversations
                        </button>
                    </div>

                    {{-- Fixed Header --}}
                    <div x-show="selectedConversationId && !loading"
                         class="p-3 sm:p-4 border-b border-border-light bg-secondary-bg flex items-center shadow-sm flex-shrink-0 sticky top-0 z-10">
                        <img :src="participantAvatarUrl" alt="Participant Avatar"
                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-full mr-3 sm:mr-4 object-cover border-2 border-white shadow-sm">
                        <div>
                            <h4 class="text-lg sm:text-xl font-bold text-primary-dark" x-text="selectedParticipantCodeName"></h4>
                            <p class="text-xs sm:text-sm text-text-light mt-0.5">Participant</p>
                        </div>
                    </div>

                    {{-- Scrollable Message List --}}
                    <template x-if="selectedConversationId && !loading">
                        <div id="messages-container"
                            class="flex-1 overflow-y-auto p-4 sm:p-5 flex flex-col space-y-3 sm:space-y-4 bg-gray-50 custom-scrollbar">
                            <template x-for="message in messages" :key="message.id">
                                <div :class="message.is_sender ? 'justify-end' : 'justify-start'" class="flex">
                                    <div :class="message.is_sender ? 'outgoing' : 'incoming'" class="chat-bubble">
                                        <p class="chat-message-sender"
                                        :class="message.is_sender ? 'text-right' : 'text-left'"
                                        x-text="message.is_sender ? 'You' : message.sender_name"></p>
                                        <p x-text="message.content"></p>
                                        <p class="chat-message-time"
                                        :class="message.is_sender ? 'text-right' : 'text-left'"
                                        x-text="message.created_at_formatted + (message.read_at ? ' (Read)' : '')"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Fixed Input Area --}}
                    <div id="message-input-form-container"
                        class="border-t border-border-light p-3 sm:p-4 bg-secondary-bg flex-shrink-0 sticky bottom-0 z-10"
                        x-show="selectedConversationId">
                        <form @submit.prevent="sendMessage" class="flex items-end space-x-2 sm:space-x-3">
                            <div class="flex-1">
                                <textarea x-model="messageContent"
                                        @keydown.enter.prevent="sendMessage"
                                        rows="1"
                                        placeholder="Type your message..."
                                        class="w-full rounded-lg border-border-light shadow-sm focus:ring-primary-light focus:border-primary-light resize-none overflow-hidden p-2.5 sm:p-3.5 text-text-dark placeholder-text-light"
                                        style="min-height: 48px;"></textarea>
                            </div>
                            <button type="submit"
                                        :disabled="loading"
                                        class="bg-primary-dark text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-primary-light transition duration-200 ease-in-out font-semibold text-sm sm:text-base shadow-sm">
                                <span x-show="!loading">Send</span>
                                <span x-show="loading">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                        <template x-if="errorMessage">
                            <p class="text-red-500 text-sm mt-2 text-center" x-text="errorMessage"></p>
                        </template>
                        <template x-if="successMessage">
                            <p class="text-green-500 text-sm mt-2 text-center" x-text="successMessage"></p>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Custom chat bubble styles --}}
    <style>
        .chat-bubble {
            max-width: 75%; /* Limit bubble width */
            padding: 0.75rem 1rem; /* Adjusted padding for smaller screens */
            border-radius: 1.25rem; /* More rounded bubbles */
            line-height: 1.5; /* Better line spacing */
            word-wrap: break-word; /* Ensure long words break */
        }

        /* Adjust chat bubble padding for larger screens */
        @media (min-width: 640px) { /* Tailwind's 'sm' breakpoint */
            .chat-bubble {
                padding: 1rem 1.25rem;
            }
        }

        .chat-bubble.incoming {
            background-color: theme('colors.chat-incoming'); /* Use new color variable */
            color: theme('colors.text-dark');
            border-bottom-left-radius: 0.375rem; /* Slight corner adjustment for "tail" */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* Subtle shadow */
        }

        .chat-bubble.outgoing {
            background-color: theme('colors.primary-light'); /* Use new color variable */
            color: white;
            border-bottom-right-radius: 0.375rem; /* Slight corner adjustment for "tail" */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* Subtle shadow */
        }

        .chat-message-sender {
            font-size: 0.75rem; /* Smaller sender name on mobile */
            font-weight: 600; /* Semi-bold */
            margin-bottom: 0.25rem; /* Small space below sender name */
            opacity: 0.8; /* Slightly faded */
        }

        @media (min-width: 640px) { /* Tailwind's 'sm' breakpoint */
            .chat-message-sender {
                font-size: 0.8rem;
            }
        }

        .chat-bubble.outgoing .chat-message-sender {
            color: rgba(255, 255, 255, 0.8); /* Lighter for outgoing */
        }
        .chat-bubble.incoming .chat-message-sender {
            color: theme('colors.primary-dark'); /* Distinct color for incoming sender */
        }

        .chat-message-time {
            font-size: 0.65rem; /* Smaller time stamp on mobile */
            color: theme('colors.text-light'); /* Lighter color */
            margin-top: 0.5rem; /* Space above time */
            opacity: 0.7; /* More faded */
        }

        @media (min-width: 640px) { /* Tailwind's 'sm' breakpoint */
            .chat-message-time {
                font-size: 0.7rem;
            }
        }
        .chat-bubble.outgoing .chat-message-time {
            color: rgba(255, 255, 255, 0.7); /* Lighter for outgoing */
        }

        /* Custom Scrollbar from your original code */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection

@push('scripts')
<script>
    window.currentRouteName = "{{ Route::currentRouteName() }}";
</script>
<script>
    function conversationPanel() {
        return {
            selectedConversationId: null,
            selectedParticipantCodeName: 'No Conversation Selected',
            messages: [],
            messageContent: '',
            loading: false,
            errorMessage: '',
            successMessage: '',
            participantAvatarUrl: '{{ asset("images/general.png") }}', // Default avatar
            currentUser: @json(Auth::user()), // Pass authenticated user data to Alpine.js

            // Mobile visibility states
            showList: true, // Controls sidebar visibility on mobile
            showMessage: false, // Controls message view visibility on mobile

            init() {
                this.selectedConversationId = {{ $initialConversationId ?? 'null' }}; // Set initial conversation ID

                const isMobile = window.innerWidth < 768; // Tailwind's 'md' breakpoint

                if (isMobile) {
                    if (this.selectedConversationId) { // If there's an initial conversation on mobile
                        this.showConversationView(); // Show message view
                        this.selectConversation(this.selectedConversationId); // Load the conversation
                    } else { // No initial conversation on mobile
                        this.showConversationList(); // Show the list
                    }
                } else { // Desktop view
                    this.showList = true;
                    this.showMessage = true;
                    if (this.selectedConversationId) {
                        this.selectConversation(this.selectedConversationId);
                    }
                }

                // Add event listener for textarea auto-resize
                const textarea = this.$el.querySelector('textarea[x-model="messageContent"]');
                if (textarea) {
                    textarea.addEventListener('input', this.autoResizeTextarea);
                }

                // Handle window resize to adjust visibility if breakpoint changes
                window.addEventListener('resize', () => {
                    const newIsMobile = window.innerWidth < 768;
                    if (!newIsMobile) { // Transitioning to desktop
                        this.showList = true;
                        this.showMessage = true;
                    } else { // Transitioning to mobile
                        if (this.selectedConversationId) {
                            this.showConversationView();
                        } else {
                            this.showConversationList();
                        }
                    }
                });
            },

            showConversationList() {
                this.showList = true;
                this.showMessage = false;
                this.selectedConversationId = null; // Important: Clear selected ID when returning to list
            },

            showConversationView() {
                this.showList = false;
                this.showMessage = true;
            },

            async selectConversation(conversationId) {
                this.selectedConversationId = conversationId;
                this.messages = []; // Clear previous messages
                this.messageContent = '';
                this.errorMessage = '';
                this.successMessage = '';
                this.loading = true;

                // Update active class for conversation list items
                document.querySelectorAll('.conversation-item').forEach(item => {
                    // Remove current active styles
                    const participantNameElement = item.querySelector('.text-sm.sm\\:text-base.font-semibold');
                    const lastMessageElement = item.querySelector('.text-xs.sm\\:text-sm.truncate');

                    item.classList.remove('bg-primary-light', 'text-white', 'shadow-sm', 'font-semibold');
                    participantNameElement?.classList.remove('text-white');
                    lastMessageElement?.classList.remove('text-gray-200');

                    // Add default styles
                    item.classList.add('bg-white', 'border', 'border-border-light');
                    participantNameElement?.classList.add('text-text-dark');
                    lastMessageElement?.classList.add('text-text-light');

                    if (item.dataset.conversationId == conversationId) {
                        // Add active styles
                        item.classList.add('bg-primary-light', 'text-white', 'shadow-sm', 'font-semibold');
                        participantNameElement?.classList.add('text-white');
                        lastMessageElement?.classList.add('text-gray-200');

                        // Remove default styles
                        item.classList.remove('bg-white', 'border', 'border-border-light');
                        participantNameElement?.classList.remove('text-text-dark');
                        lastMessageElement?.classList.remove('text-text-light');
                    }
                });


                // Show message view immediately on mobile when a conversation is selected
                if (window.innerWidth < 768) {
                    this.showConversationView();
                }

                try {
                    // Fetch messages from your controller's show method
                    const response = await fetch(`/sc/messages/${conversationId}`);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to load conversation.');
                    }
                    const data = await response.json();

                    this.messages = data.messages.map(msg => ({
                        id: msg.id,
                        content: msg.content,
                        created_at_formatted: this.formatDate(msg.created_at),
                        is_sender: msg.is_sender ,
                        sender_id: msg.sender_id, // Important for differentiating messages
                        sender_name: msg.sender_id === this.currentUser.id ? 'You' : 'Participant',
                        read_at: msg.read_at,
                    }));
                    this.selectedParticipantCodeName = data.participant_code_name;
                    this.participantAvatarUrl = data.participant_avatar;

                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });

                } catch (error) {
                    console.error('Error loading conversation:', error);
                    this.errorMessage = error.message;
                    this.selectedParticipantCodeName = 'Error Loading';
                    this.participantAvatarUrl = '{{ asset("images/general.png") }}'; // Fallback
                } finally {
                    this.loading = false;
                }
            },

            async sendMessage() {
                if (!this.messageContent.trim()) {
                    this.errorMessage = 'Message cannot be empty.';
                    setTimeout(() => { this.errorMessage = ''; }, 3000);
                    return;
                }
                this.errorMessage = '';
                this.successMessage = '';
                this.loading = true;

                try {
                    const response = await fetch(`/sc/messages/${this.selectedConversationId}/reply`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            content: this.messageContent
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to send message.');
                    }

                    const data = await response.json();
                    this.messages.push({
                        id: data.data.id,
                        content: data.data.content,
                        created_at_formatted: this.formatDate(data.data.created_at),
                        is_sender: true, // It's always the current user sending
                        sender_id: this.currentUser.id, // Use current user's ID
                        sender_name: 'You', // Display 'You' for own messages
                        read_at: data.data.read_at,
                    });
                    this.messageContent = '';
                    this.successMessage = data.message;

                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });

                    setTimeout(() => {
                        this.successMessage = '';
                    }, 2000);

                    // Update the last message preview in the sidebar dynamically
                    const currentConvListItem = document.querySelector(`.conversation-item[data-conversation-id="${this.selectedConversationId}"]`);
                    if (currentConvListItem) {
                        const previewElement = currentConvListItem.querySelector('.text-xs.sm\\:text-sm.truncate');
                        if (previewElement) {
                            previewElement.textContent = data.data.content;
                        }
                        // Remove the "New" badge for this conversation when the current user sends a message
                        const newBadge = currentConvListItem.querySelector('.bg-accent-yellow');
                        if (newBadge) {
                            newBadge.remove();
                        }
                    }

                } catch (error) {
                    console.error('Error sending message:', error);
                    this.errorMessage = error.message || 'Failed to send message. Please try again.';
                    if (error.errors) {
                        let errors = Object.values(error.errors).flat();
                        this.errorMessage = errors.join('\n');
                    }
                    setTimeout(() => { this.errorMessage = ''; }, 5000);
                } finally {
                    this.loading = false;
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const messageArea = document.getElementById('messages-container');
                    if (messageArea) {
                        messageArea.scrollTop = messageArea.scrollHeight;
                    }
                });
            },

            formatDate(dateString) {
                const options = { hour: 'numeric', minute: 'numeric', month: 'short', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            },

            autoResizeTextarea(event) {
                const textarea = event.target;
                textarea.style.height = 'auto'; // Reset height to recalculate
                textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
            }
        }
    }
</script>
@endpush