@extends('company.provider-db')

@section('main-content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-headphones text-[#cc8e45] mr-3"></i>
                Support Center
            </h1>
            <p class="mt-2 text-[#bcbabb]">Submit tickets and track your support requests.</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openNewTicketModal()" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                New Ticket
            </button>
        </div>
    </div>
</div>

<!-- Quick Info Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white shadow-lg rounded-xl p-6 text-center border border-[#e1e7dd]">
        <div class="w-16 h-16 bg-[#f8f1e1] rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-ticket-alt text-[#33595a] text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-[#33595a]">{{ $totalTickets }}</h3>
        <p class="text-[#bcbabb]">Total Tickets</p>
    </div>
    
    <div class="bg-white shadow-lg rounded-xl p-6 text-center border border-[#e1e7dd]">
        <div class="w-16 h-16 bg-[#f8f1e1] rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-green-500 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-green-500">{{ $openTickets }}</h3>
        <p class="text-[#bcbabb]">Open</p>
    </div>
    
    <div class="bg-white shadow-lg rounded-xl p-6 text-center border border-[#e1e7dd]">
        <div class="w-16 h-16 bg-[#f8f1e1] rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cogs text-blue-500 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-blue-500">{{ $inProgressTickets }}</h3>
        <p class="text-[#bcbabb]">In Progress</p>
    </div>
    
    <div class="bg-white shadow-lg rounded-xl p-6 text-center border border-[#e1e7dd]">
        <div class="w-16 h-16 bg-[#f8f1e1] rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-green-500">{{ $resolvedTickets }}</h3>
        <p class="text-[#bcbabb]">Resolved</p>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('provider.support-center.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-[#cc8e45]">
                <option value="all" {{ request('priority') === 'all' ? 'selected' : '' }}>All Priority</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center justify-center">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Tickets Table -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">My Support Tickets</h2>
    </div>
    
    @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $ticket->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $ticket->type_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_color }}">
                                {{ $ticket->priority_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->status_color }}">
                                {{ $ticket->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($ticket->assignedAdmin)
                                {{ $ticket->assignedAdmin->first_name }} {{ $ticket->assignedAdmin->last_name }}
                            @else
                                <span class="text-gray-400">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('provider.support-center.view', $ticket) }}" 
                               class="text-[#cc8e45] hover:text-orange-600">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tickets found</h3>
            <p class="text-gray-500 mb-4">You haven't submitted any support tickets yet.</p>
            <button onclick="openNewTicketModal()" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                <i class="fas fa-plus mr-2"></i>
                Submit Your First Ticket
            </button>
        </div>
    @endif
</div>

<!-- New Ticket Modal -->
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
