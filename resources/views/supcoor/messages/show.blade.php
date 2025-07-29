@extends('supcoor.sc-db')

@section('main-content')
    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8 flex flex-col h-full max-h-[calc(100vh-160px)]">
        <div class="flex items-center mb-6 border-b pb-4">
            <a href="{{ route('sc.messages.index') }}" class="text-[#cc8e45] hover:underline mr-4 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Inbox
            </a>
            <h3 class="text-xl md:text-2xl font-semibold text-[#33595a] flex-1">
                Conversation with {{ $conversation->participant->participant_code_name ?? 'Unknown Participant' }}
            </h3>
        </div>

        {{-- Message Display Area --}}
        <div class="flex-1 overflow-y-auto pr-4 mb-6 custom-scrollbar" style="max-height: calc(100vh - 350px);"> {{-- Adjust max-height as needed --}}
            @foreach ($messages as $message)
                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }} mb-4">
                    <div class="max-w-xs sm:max-w-md lg:max-w-lg p-3 rounded-lg relative
                        {{ $message->sender_id === Auth::id() ? 'bg-[#33595a] text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                        <p class="text-sm">
                            @if ($conversation->type === 'sc_to_participant')
                                <span class="font-bold">
                                    {{ $message->sender_id === Auth::id() ? 'You' : ($message->sender->name ?? 'Participant') }}:
                                </span>
                            @endif
                            {{ $message->content }}
                        </p>
                        <span class="block text-xs mt-1 {{ $message->sender_id === Auth::id() ? 'text-gray-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('M d, H:i A') }}
                            @if ($message->sender_id === Auth::id() && $message->read_at)
                                <span class="ml-1 text-green-300" title="Read"><svg class="inline-block w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Message Input Area --}}
        <div x-data="messageSender()" class="mt-auto pt-4 border-t border-gray-200" x-init="conversationId = {{ $conversation->id }}">
            <form @submit.prevent="sendMessage" class="flex flex-col sm:flex-row gap-3">
                <textarea x-model="messageContent"
                          placeholder="Type your message..."
                          rows="3"
                          required
                          @keydown.enter.prevent="sendMessage" {{-- Send on Enter, prevent new line --}}
                          class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] p-2.5 text-base resize-none"></textarea>
                <button type="submit"
                        :disabled="loading"
                        class="px-6 py-3 bg-[#cc8e45] text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-200 flex-shrink-0 flex items-center justify-center">
                    <span x-show="!loading">Send</span>
                    <span x-show="loading">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <template x-if="errorMessage">
                <p class="text-red-600 text-sm mt-2 font-medium" x-text="errorMessage"></p>
            </template>
            <template x-if="successMessage">
                <p class="text-[#33595a] text-sm mt-2 font-medium" x-text="successMessage"></p>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function messageSender() {
        return {
            conversationId: null,
            messageContent: '',
            loading: false,
            errorMessage: '',
            successMessage: '',

            sendMessage() {
                this.errorMessage = '';
                this.successMessage = '';
                this.loading = true;

                fetch(`/sc/messages/${this.conversationId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        content: this.messageContent
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    this.successMessage = data.message;
                    this.messageContent = ''; // Clear input
                    this.loading = false;
                    // Dynamically append the new message to the display
                    const messageContainer = document.querySelector('.custom-scrollbar');
                    const newMessageDiv = document.createElement('div');
                    newMessageDiv.className = 'flex justify-end mb-4'; // Always sent by current user
                    newMessageDiv.innerHTML = `
                        <div class="max-w-xs sm:max-w-md lg:max-w-lg p-3 rounded-lg relative bg-[#33595a] text-white rounded-br-none">
                            <p class="text-sm">
                                <span class="font-bold">You:</span> ${data.data.content}
                            </p>
                            <span class="block text-xs mt-1 text-gray-200">
                                ${new Date(data.data.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true })}
                            </span>
                        </div>
                    `;
                    messageContainer.appendChild(newMessageDiv);
                    messageContainer.scrollTop = messageContainer.scrollHeight; // Scroll to bottom

                    setTimeout(() => {
                        this.successMessage = ''; // Clear success message after a few seconds
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    this.errorMessage = error.message || 'Failed to send message. Please try again.';
                    if (error.errors) {
                        let errors = Object.values(error.errors).flat();
                        this.errorMessage = errors.join('\n');
                    }
                    this.loading = false;
                    setTimeout(() => {
                        this.errorMessage = ''; // Clear error message after a few seconds
                    }, 5000);
                });
            }
        };
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush