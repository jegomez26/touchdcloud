@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-users text-[#cc8e45] mr-3"></i>
                Manage Users
            </h1>
            <p class="mt-2 text-[#bcbabb]">Manage user accounts, activation status, and permissions.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-check text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-green-600">{{ $users->where('is_active', true)->count() }}</h3>
        <p class="text-gray-600">Active Users</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-times text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-red-600">{{ $users->where('is_active', false)->count() }}</h3>
        <p class="text-gray-600">Inactive Users</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-blue-600">{{ $users->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
        <p class="text-gray-600">New This Week</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-purple-600">{{ $users->where('profile_completed', true)->count() }}</h3>
        <p class="text-gray-600">Completed Profiles</p>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-xl p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-[#33595a]">All Users</h2>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="searchUsers" placeholder="Search users..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-[#f8f1e1] rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-[#cc8e45]"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                                    @elseif($user->role === 'coordinator') bg-blue-100 text-blue-800
                                    @elseif($user->role === 'provider') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="fas 
                                        @if($user->role === 'admin') fa-crown
                                        @elseif($user->role === 'coordinator') fa-handshake
                                        @elseif($user->role === 'provider') fa-hospital
                                        @else fa-user
                                        @endif mr-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->profile_completed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Complete
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($user->is_active)
                                        <form action="{{ route('superadmin.users.deactivate', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('superadmin.users.activate', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to activate this user?')">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                            onclick="showUserDetails({{ $user->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
            <p class="text-gray-500">There are no users in the system yet.</p>
        </div>
    @endif
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Details</h3>
                <button onclick="closeUserDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="userDetailsContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchUsers').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // User details modal
    function showUserDetails(userId) {
        // This would typically fetch user details via AJAX
        // For now, we'll show a placeholder
        document.getElementById('userDetailsContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-user-circle text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">User details for ID: ${userId}</p>
                <p class="text-sm text-gray-400 mt-2">This feature can be enhanced with AJAX loading</p>
            </div>
        `;
        document.getElementById('userDetailsModal').classList.remove('hidden');
    }

    function closeUserDetails() {
        document.getElementById('userDetailsModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('userDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUserDetails();
        }
    });
</script>
@endpush
