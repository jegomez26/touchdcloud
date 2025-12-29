@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-ticket-alt text-[#cc8e45] mr-3"></i>
                Ticket {{ $ticket->ticket_number }}
            </h1>
            <p class="mt-2 text-[#bcbabb]">{{ $ticket->title }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.support-center.index') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Support Center
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ticket Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ticket Details</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->title }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg">
                        {{ $ticket->description }}
                    </div>
                </div>
                
                @if($ticket->resolution_notes)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Resolution Notes</label>
                    <div class="mt-1 text-sm text-gray-900 bg-green-50 p-4 rounded-lg border border-green-200">
                        {{ $ticket->resolution_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Status and Assignment Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Update Status -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Update Status</h3>
                    <form method="POST" action="{{ route('superadmin.support-center.update-status', $ticket) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-3">
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            
                            <textarea name="resolution_notes" placeholder="Resolution notes (optional)" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre" rows="3"></textarea>
                            
                            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Assign Ticket -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Assign Ticket</h3>
                    <form method="POST" action="{{ route('superadmin.support-center.assign', $ticket) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-3">
                            <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre">
                                <option value="">Select Admin</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                        {{ $admin->first_name }} {{ $admin->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                                Assign Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ticket Information Sidebar -->
    <div class="space-y-6">
        <!-- Ticket Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ticket Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $ticket->ticket_number }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $ticket->type_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_color }}">
                        {{ $ticket->priority_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->status_color }}">
                        {{ $ticket->status_name }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y g:i A') }}</p>
                </div>
                
                @if($ticket->resolved_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Resolved</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->resolved_at->format('M d, Y g:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- User Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                        {{ ucfirst(str_replace('_', ' ', $ticket->user->role)) }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $ticket->user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Assignment Information -->
        @if($ticket->assignedAdmin)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned To</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Admin Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->assignedAdmin->first_name }} {{ $ticket->assignedAdmin->last_name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $ticket->assignedAdmin->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst(str_replace('_', ' ', $ticket->assignedAdmin->role)) }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

