@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-headphones text-[#cc8e45] mr-3"></i>
                Support Center
            </h1>
            <p class="mt-2 text-[#bcbabb]">Manage user reports, inquiries, and tech support concerns.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Quick Info Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-10">
    <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
        <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-ticket-alt text-custom-dark-teal text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $totalTickets }}</h3>
        <p class="text-custom-dark-olive">Total Tickets</p>
    </div>
    
    <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
        <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-custom-green text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-custom-green">{{ $openTickets }}</h3>
        <p class="text-custom-dark-olive">Open</p>
    </div>
    
    <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
        <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cogs text-custom-ochre text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-custom-ochre">{{ $inProgressTickets }}</h3>
        <p class="text-custom-dark-olive">In Progress</p>
    </div>
    
    <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
        <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-custom-green text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-custom-green">{{ $resolvedTickets }}</h3>
        <p class="text-custom-dark-olive">Resolved</p>
    </div>
    
    <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
        <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-red-500">{{ $urgentTickets }}</h3>
        <p class="text-custom-dark-olive">Urgent</p>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('superadmin.support-center.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
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
            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
                <option value="all" {{ request('priority') === 'all' ? 'selected' : '' }}>All Priority</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                <option value="bug_report" {{ request('type') === 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                <option value="feature_request" {{ request('type') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                <option value="technical_issue" {{ request('type') === 'technical_issue' ? 'selected' : '' }}>Technical Issue</option>
                <option value="account_issue" {{ request('type') === 'account_issue' ? 'selected' : '' }}>Account Issue</option>
                <option value="billing_question" {{ request('type') === 'billing_question' ? 'selected' : '' }}>Billing Question</option>
                <option value="general_inquiry" {{ request('type') === 'general_inquiry' ? 'selected' : '' }}>General Inquiry</option>
                <option value="complaint" {{ request('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full bg-custom-ochre text-white px-4 py-2 rounded-lg hover:bg-custom-ochre-darker transition duration-200 flex items-center justify-center">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Tickets Table -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Support Tickets</h2>
    </div>
    
    @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
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
                            <div class="text-sm text-gray-900">{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('superadmin.support-center.view', $ticket) }}" 
                               class="text-custom-ochre hover:text-custom-ochre-darker">
                                <i class="fas fa-eye"></i> View
                            </a>
                            
                            <!-- Status Update Dropdown -->
                            <div class="relative inline-block text-left">
                                <button onclick="toggleStatusDropdown({{ $ticket->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Status
                                </button>
                                
                                <div id="status-dropdown-{{ $ticket->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                    <form method="POST" action="{{ route('superadmin.support-center.update-status', $ticket) }}" class="p-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                        <textarea name="resolution_notes" placeholder="Resolution notes (optional)" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mt-2" rows="2"></textarea>
                                        <button type="submit" class="w-full mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Assignment Dropdown -->
                            <div class="relative inline-block text-left">
                                <button onclick="toggleAssignDropdown({{ $ticket->id }})" 
                                        class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-user-plus"></i> Assign
                                </button>
                                
                                <div id="assign-dropdown-{{ $ticket->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                    <form method="POST" action="{{ route('superadmin.support-center.assign', $ticket) }}" class="p-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                            <option value="">Select Admin</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->first_name }} {{ $admin->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full mt-2 bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                            Assign
                                        </button>
                                    </form>
                                </div>
                            </div>
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
            <p class="text-gray-500">No support tickets match your current filters.</p>
        </div>
    @endif
</div>

<script>
function toggleStatusDropdown(ticketId) {
    // Close all other dropdowns
    document.querySelectorAll('[id^="status-dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== `status-dropdown-${ticketId}`) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    const dropdown = document.getElementById(`status-dropdown-${ticketId}`);
    dropdown.classList.toggle('hidden');
}

function toggleAssignDropdown(ticketId) {
    // Close all other dropdowns
    document.querySelectorAll('[id^="assign-dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== `assign-dropdown-${ticketId}`) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    const dropdown = document.getElementById(`assign-dropdown-${ticketId}`);
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleStatusDropdown"]') && !event.target.closest('[id^="status-dropdown-"]')) {
        document.querySelectorAll('[id^="status-dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
    
    if (!event.target.closest('[onclick^="toggleAssignDropdown"]') && !event.target.closest('[id^="assign-dropdown-"]')) {
        document.querySelectorAll('[id^="assign-dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});
</script>
@endsection

