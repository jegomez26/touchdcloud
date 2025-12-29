@extends('company.provider-db')

@section('main-content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-ticket-alt text-[#cc8e45] mr-3"></i>
                Ticket #{{ $ticket->ticket_number }}
            </h1>
            <p class="mt-2 text-[#bcbabb]">{{ $ticket->title }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('provider.support-center.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tickets
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ticket Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Ticket Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ticket Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $ticket->status_color }}">
                        {{ $ticket->status_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $ticket->priority_color }}">
                        {{ $ticket->priority_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $ticket->type_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                        {{ $ticket->category ? $ticket->category->name : 'Uncategorized' }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                    <p class="text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                    <p class="text-sm text-gray-900">{{ $ticket->updated_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $ticket->description }}</p>
                </div>
            </div>
            
            @if($ticket->resolution_notes)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Notes</label>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $ticket->resolution_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Comments</h2>
            
            @if($ticket->comments->count() > 0)
                <div class="space-y-4 mb-6">
                    @foreach($ticket->comments->sortBy('created_at') as $comment)
                    <div class="border-l-4 {{ $comment->is_admin_reply ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50' }} p-4 rounded-r-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">{{ $comment->user->first_name }} {{ $comment->user->last_name }}</span>
                                @if($comment->is_admin_reply)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Admin</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">User</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $comment->comment }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-4xl mb-4"></i>
                    <p>No comments yet. Be the first to add a comment!</p>
                </div>
            @endif
            
            <!-- Add Comment Form -->
            <form method="POST" action="{{ route('provider.support-center.comment', $ticket) }}" class="border-t pt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Comment</label>
                    <textarea name="comment" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]" placeholder="Add your comment here..."></textarea>
                </div>
                <button type="submit" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Add Comment
                </button>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Assigned Admin -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned To</h3>
            @if($ticket->assignedAdmin)
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#cc8e45] rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $ticket->assignedAdmin->first_name }} {{ $ticket->assignedAdmin->last_name }}</p>
                        <p class="text-sm text-gray-500">{{ $ticket->assignedAdmin->email }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-user-slash text-2xl mb-2"></i>
                    <p>Not assigned</p>
                </div>
            @endif
        </div>

        <!-- Ticket Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Statistics</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Comments</span>
                    <span class="font-medium text-gray-900">{{ $ticket->comments->count() }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Days Open</span>
                    <span class="font-medium text-gray-900">{{ $ticket->created_at->diffInDays(now()) }}</span>
                </div>
                
                @if($ticket->resolved_at)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Resolution Time</span>
                    <span class="font-medium text-gray-900">{{ $ticket->created_at->diffInDays($ticket->resolved_at) }} days</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-2">
                <a href="{{ route('provider.support-center.index') }}" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i>
                    All Tickets
                </a>
                
                <button onclick="openNewTicketModal()" class="w-full bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    New Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Ticket Modal (reused from index) -->
<div id="new-ticket-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-2xl w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="new-ticket-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Submit New Ticket</h3>
            <p class="text-gray-600">Describe your issue or request and we'll help you resolve it.</p>
        </div>
        
        <form method="POST" action="{{ route('provider.support-center.create') }}">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]" placeholder="Brief description of your issue">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]" placeholder="Please provide detailed information about your issue..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
                            <option value="">Select Type</option>
                            <option value="bug_report">Bug Report</option>
                            <option value="feature_request">Feature Request</option>
                            <option value="technical_issue">Technical Issue</option>
                            <option value="account_issue">Account Issue</option>
                            <option value="billing_question">Billing Question</option>
                            <option value="general_inquiry">General Inquiry</option>
                            <option value="complaint">Complaint</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                
                @if($categories->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
                        <option value="">Select Category (Optional)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            
            <div class="flex gap-3 justify-end mt-8">
                <button type="button" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-modal" data-modal="new-ticket-modal">
                    Cancel
                </button>
                <button type="submit" class="bg-[#cc8e45] text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition duration-200 font-medium">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>

@include('components.modals')

<script>
function openNewTicketModal() {
    const modal = document.getElementById('new-ticket-modal');
    if (modal) {
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
        
        const modalContent = modal.querySelector('.bg-white');
        if (modalContent) {
            modalContent.classList.remove('-translate-y-4', 'scale-95');
            modalContent.classList.add('translate-y-0', 'scale-100');
        }
        
        // Focus on first input
        const firstInput = modal.querySelector('input[name="title"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeNewTicketModal() {
    const modal = document.getElementById('new-ticket-modal');
    if (modal) {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');
        
        const modalContent = modal.querySelector('.bg-white');
        if (modalContent) {
            modalContent.classList.add('-translate-y-4', 'scale-95');
            modalContent.classList.remove('translate-y-0', 'scale-100');
        }
    }
}

// Close modal when clicking close button or cancel
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('new-ticket-modal');
    if (modal) {
        // Close button
        const closeBtn = modal.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeNewTicketModal);
        }
        
        // Cancel button
        const cancelBtn = modal.querySelector('button[type="button"]');
        if (cancelBtn && cancelBtn.textContent.includes('Cancel')) {
            cancelBtn.addEventListener('click', closeNewTicketModal);
        }
        
        // Close when clicking backdrop
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeNewTicketModal();
            }
        });
        
        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('opacity-0')) {
                closeNewTicketModal();
            }
        });
    }
});
</script>
@endsection

