{{-- resources/views/indiv/messages/index.blade.php --}}
@extends('indiv.indiv-db')
@section('main-content')
    <script>
        window.App = window.App || {};
        window.App.user = {
            id: {{ Auth::id() }}
            // You might add more user data here if needed, e.g., name: "{{ Auth::user()->name }}"
        };
        console.log('Current authenticated user ID:', window.App.user.id);
    </script>
    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-xl shadow-md-light min-h-[calc(100vh-var(--header-height, 100px))] flex flex-col"> {{-- Adjusted padding for small screens, min-height --}}
        <h2 class="text-2xl sm:text-3xl font-extrabold text-primary-dark mb-6 sm:mb-8 tracking-tight">Your Inbox</h2> {{-- Smaller on small screens --}}

        @if($conversations->isEmpty())
            <p class="text-text-light text-base sm:text-lg py-6 sm:py-10 text-center">You have no messages yet. Start a new conversation!</p>
        @else
            <div class="flex flex-col md:flex-row gap-4 md:gap-8 flex-1"> {{-- flex-1 to allow children to grow --}}
                {{-- Conversation List (Sidebar) --}}
                <div id="conversation-sidebar"
                     class="w-full md:w-1/3 bg-secondary-bg rounded-xl p-4 md:p-5 overflow-y-auto border border-border-light shadow-sm flex-shrink-0
                             {{ $initialConversationId ? 'hidden md:block' : 'block' }}"> {{-- Hide on mobile if initial conv is set, show otherwise --}}

                    <h3 class="font-bold text-lg sm:text-xl text-primary-dark mb-4 md:mb-5 border-b pb-3 border-border-light">Conversations</h3>
                    <ul id="conversation-list">
                        @foreach($conversations as $conversation)
                            <li class="mb-2 sm:mb-3 last:mb-0">
                                <a href="#" data-conversation-id="{{ $conversation->id }}"
                                   class="conversation-item flex items-center p-3 sm:p-4 rounded-lg hover:bg-gray-100 transition-all duration-200 ease-in-out
                                           {{ $initialConversationId == $conversation->id ? 'bg-primary-light text-white shadow-sm' : 'bg-white border border-border-light' }}">
                                    <img src="{{ $conversation->supportCoordinator->user->profile_avatar_url ? asset('storage/' . $conversation->supportCoordinator->user->profile_avatar_url) : asset('images/general.png') }}"
                                            alt="Coordinator Avatar" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full mr-3 sm:mr-4 object-cover border-2 border-white shadow-sm">
                                    <div class="flex-1 min-w-0"> {{-- Add min-w-0 to allow text truncation --}}
                                        <p class="text-sm sm:text-base font-semibold {{ $initialConversationId == $conversation->id ? 'text-white' : 'text-text-dark' }} leading-tight">
                                            {{ $conversation->supportCoordinator->user->name ?? 'Support Coordinator' }}
                                        </p>
                                        @if($conversation->messages->isNotEmpty())
                                            <p class="text-xs sm:text-sm {{ $initialConversationId == $conversation->id ? 'text-gray-200' : 'text-text-light' }} truncate w-full mt-1">
                                                {{ $conversation->messages->first()->content }}
                                            </p>
                                        @else
                                            <p class="text-xs sm:text-sm {{ $initialConversationId == $conversation->id ? 'text-gray-200' : 'text-text-light' }} italic">No messages yet.</p>
                                        @endif
                                    </div>
                                    @if($conversation->messages->isNotEmpty() && $conversation->messages->first()->read_at === null && $conversation->messages->first()->receiver_id === Auth::id())
                                        <span class="ml-2 sm:ml-4 flex-shrink-0 bg-accent-yellow text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full shadow-sm">New</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    {{-- Pagination Links --}}
                    <div class="mt-4 sm:mt-6 border-t pt-3 sm:pt-4 border-border-light">
                        {{ $conversations->links('vendor.pagination.tailwind') }}
                    </div>
                </div>

                {{-- Message Display Area and Input --}}
                <div id="message-view-area"
                     class="w-full md:w-2/3 bg-white rounded-xl border border-border-light flex flex-col shadow-md-light h-[calc(85vh-var(--header-height,100px))]
                             {{ $initialConversationId ? 'block' : 'hidden md:block' }}"> {{-- Show if initial conv is set, else hide on mobile --}}

                    {{-- Back button for mobile --}}
                    <div class="md:hidden p-3 border-b border-border-light bg-secondary-bg">
                        <button id="back-to-conversations" class="text-primary-dark hover:text-primary-light flex items-center font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Conversations
                        </button>
                    </div>

                    {{-- This div will be populated by JS --}}
                    <div id="message-content-area" class="flex-1 overflow-y-auto flex flex-col custom-scrollbar">
                        {{-- Default content or initial loading state --}}
                        <div class="flex-1 flex items-center justify-center text-text-light text-base sm:text-lg p-4"> {{-- Smaller on small screens --}}
                            @if($initialConversationId)
                                Loading conversation...
                            @else
                                Select a conversation to view messages.
                            @endif
                        </div>
                    </div>

                    {{-- MESSAGE INPUT FORM - KEPT SEPARATE, ALWAYS PRESENT IN THE DOM --}}
                    <div id="message-input-form-container" class="border-t border-border-light p-3 sm:p-4 bg-secondary-bg {{ !$initialConversationId ? 'hidden' : '' }}">
                        <form id="reply-form" class="flex items-end space-x-2 sm:space-x-3">
                            @csrf
                            <input type="hidden" name="conversation_id" id="current-conversation-id" value="{{ $initialConversationId }}">
                            <div class="flex-1">
                                <textarea name="content" id="message-content" rows="1" class="w-full rounded-lg border-border-light shadow-sm focus:ring-primary-light focus:border-primary-light resize-none overflow-hidden p-2.5 sm:p-3.5 text-text-dark placeholder-text-light" placeholder="Type your message..." style="min-height: 48px;"></textarea>
                            </div>
                            <button type="submit" class="bg-primary-dark text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-primary-light transition duration-200 ease-in-out font-semibold text-sm sm:text-base shadow-sm">Send</button>
                        </form>
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
    </style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const conversationSidebar = document.getElementById('conversation-sidebar');
        const messageViewArea = document.getElementById('message-view-area');
        const messageContentArea = document.getElementById('message-content-area');
        const conversationList = document.getElementById('conversation-list');
        const replyForm = document.getElementById('reply-form');
        const messageContentInput = document.getElementById('message-content');
        const currentConversationIdInput = document.getElementById('current-conversation-id');
        const backToConversationsButton = document.getElementById('back-to-conversations');

        let currentConversationId = {{ $initialConversationId ?? 'null' }};
        const messageInputFormContainer = document.getElementById('message-input-form-container');

        // Function to manage visibility on mobile
        function showConversationList() {
            conversationSidebar.classList.remove('hidden');
            messageViewArea.classList.add('hidden');
            // Hide the input form if no conversation is actively loaded (e.g., after returning to list)
            if (messageInputFormContainer) {
                messageInputFormContainer.classList.add('hidden');
            }
        }

        function showMessageView() {
            conversationSidebar.classList.add('hidden');
            messageViewArea.classList.remove('hidden');
            // Show the input form once a conversation is selected
            if (messageInputFormContainer) {
                messageInputFormContainer.classList.remove('hidden');
            }
        }

        // Initial state on load: if a conversation ID is present, show message view on mobile
        // but only if the screen is small (i.e., md breakpoint is not active)
        const isMobile = window.innerWidth < 768; // Tailwind's 'md' breakpoint is 768px by default
        if (currentConversationId && isMobile) {
            showMessageView();
        } else if (!currentConversationId && isMobile) {
            showConversationList(); // Ensure list is shown if no initial conv on mobile
        }
        // For desktop, leave the `md:block` classes to handle visibility

        // Add event listener for the "Back to Conversations" button
        if (backToConversationsButton) {
            backToConversationsButton.addEventListener('click', function() {
                showConversationList();
            });
        }

        function renderMessage(messageData, isSender, coordinatorNameForDisplay) {
            const senderName = isSender ? 'You' : coordinatorNameForDisplay;
            const readStatus = messageData.read_at ? '(Read)' : '';
            const messageClass = isSender ? 'outgoing' : 'incoming';
            const textAlignment = isSender ? 'text-right' : 'text-left';

            return `
                <div class="flex ${isSender ? 'justify-end' : 'justify-start'}">
                    <div class="chat-bubble ${messageClass}">
                        <p class="chat-message-sender ${textAlignment}">${senderName}</p>
                        <p>${messageData.content}</p>
                        <p class="chat-message-time ${textAlignment}">${messageData.created_at} ${readStatus}</p>
                    </div>
                </div>
            `;
        }

        // Function to fetch and display messages for a conversation
        function loadConversationMessages(conversationId) {
            currentConversationId = conversationId;
            if (currentConversationIdInput) {
                currentConversationIdInput.value = conversationId;
            }

            if (messageContentArea) {
                messageContentArea.innerHTML = `
                    <div class="flex-1 flex items-center justify-center text-text-light text-lg">
                        Loading conversation...
                    </div>
                `;
            }

            // Always show the message view when a conversation is loaded
            showMessageView();

            fetch(`/participant/messages/${conversationId}`)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Unauthorized to view this conversation.');
                        }
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (messageContentArea) {
                        // Store coordinator name for dynamic message rendering
                        const coordinatorName = data.coordinator_name;

                        messageContentArea.innerHTML = `
                            <div class="p-3 sm:p-4 border-b border-border-light bg-secondary-bg flex items-center shadow-sm">
                                <img src="${data.coordinator_avatar}" alt="Coordinator Avatar" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full mr-3 sm:mr-4 object-cover border-2 border-white shadow-sm">
                                <div>
                                    <h4 class="text-lg sm:text-xl font-bold text-primary-dark">
                                        ${coordinatorName}
                                    </h4>
                                    <p class="text-xs sm:text-sm text-text-light mt-0.5">Support Coordinator</p>
                                </div>
                            </div>
                            <div id="messages-container" class="flex-1 overflow-y-auto p-4 sm:p-5 flex flex-col space-y-3 sm:space-y-4 bg-gray-50">
                                ${data.messages.map(message => renderMessage(message, message.is_sender, coordinatorName)).join('')}
                            </div>
                        `;

                        const messagesContainer = document.getElementById('messages-container');
                        if (messagesContainer) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    }

                    // ... (existing active class update for conversation items) ...
                    document.querySelectorAll('.conversation-item').forEach(item => {
                        item.classList.remove('bg-primary-light', 'text-white', 'shadow-sm', 'font-semibold');
                        item.classList.add('bg-white', 'border', 'border-border-light'); // Reset to default

                        // Remove text-white/gray-200 from child elements for non-active
                        item.querySelector('p.font-semibold').classList.remove('text-white');
                        item.querySelector('p.font-semibold').classList.add('text-text-dark');
                        const lastMessageP = item.querySelector('p.text-xs.sm\\:text-sm');
                        if (lastMessageP) {
                             lastMessageP.classList.remove('text-gray-200');
                             lastMessageP.classList.add('text-text-light');
                        }


                        if (item.dataset.conversationId == conversationId) {
                             item.classList.remove('bg-white', 'border', 'border-border-light'); // Remove default
                             item.classList.add('bg-primary-light', 'text-white', 'shadow-sm', 'font-semibold');

                             // Apply text-white/gray-200 to child elements for active
                             item.querySelector('p.font-semibold').classList.remove('text-text-dark');
                             item.querySelector('p.font-semibold').classList.add('text-white');
                             if (lastMessageP) {
                                 lastMessageP.classList.remove('text-text-light');
                                 lastMessageP.classList.add('text-gray-200');
                             }
                        }
                    });

                })
                .catch(error => {
                    console.error('Error loading conversation:', error);
                    if (messageContentArea) {
                        messageContentArea.innerHTML = `<div class="flex-1 flex items-center justify-center text-red-500 text-lg">Error loading conversation: ${error.message}</div>`;
                    }
                });
        }

        // Event listener for clicking on conversation items
        if (conversationList) {
            conversationList.addEventListener('click', function(event) {
                const conversationItem = event.target.closest('.conversation-item');
                if (conversationItem) {
                    event.preventDefault(); // Prevent default link behavior
                    const conversationId = conversationItem.dataset.conversationId;
                    loadConversationMessages(conversationId);
                }
            });
        }

        // Event listener for sending a reply
        if (replyForm && messageContentInput && currentConversationIdInput) {
            replyForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const content = messageContentInput.value.trim();
                if (!content) return;

                const conversationId = currentConversationIdInput.value;
                if (!conversationId) {
                    alert('Please select a conversation first.');
                    return;
                }

                // Temporarily disable the send button
                const sendButton = replyForm.querySelector('button[type="submit"]');
                sendButton.disabled = true;
                sendButton.textContent = 'Sending...';

                fetch(`/participant/messages/${conversationId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(response => {
                    sendButton.disabled = false; // Re-enable button
                    sendButton.textContent = 'Send';
                    if (!response.ok) {
                        throw new Error('Failed to send message.');
                    }
                    return response.json();
                })
                .then(data => {
                    // This message was sent by the current user, so append it to the UI immediately.
                    const messagesContainer = document.getElementById('messages-container');
                    if (messagesContainer) {
                        // Assuming your `data.data` from the controller `reply` method
                        // has the same structure as what `renderMessage` expects.
                        messagesContainer.insertAdjacentHTML('beforeend', renderMessage(data.data, true, null)); // Pass true for isSender
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    messageContentInput.value = '';
                    messageContentInput.style.height = 'auto'; // Reset textarea height

                    // Update the last message preview in the conversation list sidebar
                    const currentConvListItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (currentConvListItem) {
                        const previewElement = currentConvListItem.querySelector('p.text-xs.sm\\:text-sm');
                        if (previewElement) {
                            previewElement.textContent = data.data.content;
                        }
                        // Also, ensure "New" tag is removed if it was there and it's your own message
                        const newTag = currentConvListItem.querySelector('.bg-accent-yellow');
                        if (newTag) {
                            newTag.remove();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('Failed to send message. Please try again.');
                    sendButton.disabled = false;
                    sendButton.textContent = 'Send';
                });
            });
        }

        // Initial load (after DOMContentLoaded)
        if (currentConversationId && !isMobile) {
            // Only load conversation on desktop if initially set
            loadConversationMessages(currentConversationId);
        } else if (currentConversationId && isMobile) {
            // On mobile, if initial conv is set, ensure it's loaded and view is shown
            loadConversationMessages(currentConversationId);
            showMessageView();
        }


        // Dynamic active state for sidebar links based on current URL (unchanged)
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref && currentPath.startsWith(linkHref) && linkHref !== '#') {
                document.querySelectorAll('.sidebar-link').forEach(item => item.classList.remove('active'));
                link.classList.add('active');
            }
        });

        // Adjust textarea height automatically (unchanged)
        if (messageContentInput) {
            messageContentInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // Handle window resize to adjust visibility if breakpoint changes
        window.addEventListener('resize', function() {
            const newIsMobile = window.innerWidth < 768; // Re-evaluate 'md' breakpoint
            if (!newIsMobile) { // If transitioning to desktop
                conversationSidebar.classList.remove('hidden'); // Always show sidebar
                messageViewArea.classList.remove('hidden'); // Always show message area
            } else { // If transitioning to mobile
                if (currentConversationId) {
                    showMessageView(); // If a conversation is active, show it
                } else {
                    showConversationList(); // Otherwise, show the list
                }
            }
        });
    });
</script>
@endpush