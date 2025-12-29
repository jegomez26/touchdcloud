@extends('supadmin.sa-db')

@section('title', 'Manage Admins - SIL Match Admin')

@section('content')
<div class="container mx-auto px-4 py-8 font-sans">
    <h1 class="text-3xl md:text-4xl font-extrabold text-custom-dark-teal mb-8 border-b-2 border-custom-light-grey-green pb-4">
        <i class="fas fa-crown mr-3 text-custom-ochre"></i> Manage Admins
    </h1>

    @if (session('success'))
        <div class="bg-custom-green-light bg-opacity-10 border border-custom-green text-custom-dark-olive px-6 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold text-custom-dark-teal mr-2">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 bg-opacity-20 border border-red-500 text-red-800 px-6 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold text-red-700 mr-2">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Quick Info Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-crown text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $totalAdmins }}</h3>
            <p class="text-custom-dark-olive">Total Admins</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-check text-custom-green text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-green">{{ $activeAdmins }}</h3>
            <p class="text-custom-dark-olive">Active Admins</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-times text-custom-ochre text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-ochre">{{ $inactiveAdmins }}</h3>
            <p class="text-custom-dark-olive">Inactive Admins</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-day text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $newThisWeekCount }}</h3>
            <p class="text-custom-dark-olive">New This Week</p>
        </div>
    </div>

    <!-- Add New Admin Button -->
    <div class="mb-6">
        <button onclick="showAddAdminModal()" 
                class="bg-custom-ochre text-custom-white px-6 py-3 rounded-lg hover:bg-custom-ochre-darker focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-opacity-50 transition ease-in-out duration-150 font-medium">
            <i class="fas fa-plus mr-2"></i> Add New Admin
        </button>
    </div>

    <!-- Admins Table -->
    <div class="bg-custom-white shadow-lg rounded-xl p-6 border border-custom-light-grey-green">
        <h2 class="text-2xl font-bold text-custom-dark-teal mb-6 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-list-alt mr-2 text-custom-ochre"></i> All Admins
        </h2>

        <div class="mb-6">
            <input type="text" id="searchAdmins" placeholder="Search admins by name, email, or role..." 
                   class="w-full px-4 py-2 border border-custom-light-grey-brown rounded-md focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre text-custom-dark-olive placeholder-gray-500 transition-colors duration-200 ease-in-out">
        </div>

        @if($admins->count() > 0)
            <div class="overflow-x-auto relative shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-custom-light-grey-green bg-custom-white">
                    <thead class="bg-custom-light-cream">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Admin</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Privileges</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-custom-light-grey-green">
                        @foreach($admins as $admin)
                        <tr class="hover:bg-custom-light-cream transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-custom-light-cream rounded-full flex items-center justify-center">
                                        <i class="fas fa-crown text-custom-dark-teal"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-custom-dark-olive">{{ $admin->first_name }} {{ $admin->last_name }}</div>
                                        <div class="text-sm text-gray-700">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ $admin->email }}
                                    </div>
                                    @if($admin->email_verified_at)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            <span class="text-xs text-green-600">Verified</span>
                                        </div>
                                    @else
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                            <span class="text-xs text-yellow-600">Unverified</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($admin->role === 'super_admin')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-crown mr-1"></i>
                                        Super Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($admin->privileges && count($admin->privileges) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($admin->privileges as $privilege)
                                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-medium rounded bg-gray-100 text-gray-800">
                                                {{ ucfirst(str_replace('_', ' ', $privilege)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No privileges</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($admin->is_active)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-custom-green bg-opacity-20 text-custom-dark-olive">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $admin->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Activation/Deactivation buttons --}}
                                    @if($admin->id !== auth()->id())
                                        @if($admin->is_active)
                                            <form action="{{ route('superadmin.admins.deactivate', $admin) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" 
                                                        class="px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition ease-in-out duration-150 text-sm"
                                                        onclick="showDelete('Are you sure you want to deactivate {{ $admin->first_name }} {{ $admin->last_name }}? This will prevent them from logging in.', () => this.closest('form').submit())"
                                                        title="Deactivate Account">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('superadmin.admins.activate', $admin) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" 
                                                        class="px-4 py-2 rounded-md bg-custom-green text-custom-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-custom-green focus:ring-opacity-50 transition ease-in-out duration-150 text-sm"
                                                        onclick="showConfirm('Are you sure you want to activate {{ $admin->first_name }} {{ $admin->last_name }}? They will be able to log in again.', () => this.closest('form').submit())"
                                                        title="Activate Account">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800" title="Cannot modify your own account">
                                            Current User
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $admins->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-crown text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-custom-dark-olive mb-2">No admins found</h3>
                <p class="text-custom-dark-olive">There are no admins in the system yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 hidden transition-opacity duration-300 ease-in-out">
    <div class="relative bg-custom-white rounded-lg shadow-xl p-8 w-full max-w-2xl mx-4 transform transition-transform duration-300 ease-in-out scale-95"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <h3 class="text-2xl font-bold text-custom-dark-teal mb-5 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-plus mr-2 text-custom-ochre"></i> Add New Admin
        </h3>
        <form id="addAdminForm" method="POST" action="{{ route('superadmin.admins.create') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                           class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                                  focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                                  text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                                  transition-colors duration-200 ease-in-out p-3">
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                           class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                                  focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                                  text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                                  transition-colors duration-200 ease-in-out p-3">
                </div>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required
                       class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                              focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                              text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                              transition-colors duration-200 ease-in-out p-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="password" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required minlength="8"
                           class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                                  focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                                  text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                                  transition-colors duration-200 ease-in-out p-3">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                           class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                                  focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                                  text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                                  transition-colors duration-200 ease-in-out p-3">
                </div>
            </div>

            <div class="mb-6">
                <label for="role" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role" id="role" required
                        class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                               focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                               text-custom-dark-teal bg-custom-light-cream
                               transition-colors duration-200 ease-in-out p-3">
                    <option value="">Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-custom-dark-olive mb-3">
                    Privileges
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_users" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Users</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_providers" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Providers</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_participants" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Participants</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_support_coordinators" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Support Coordinators</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_admins" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Admins</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="view_logs" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">View Logs</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="privileges[]" value="manage_backups" class="rounded border-custom-light-grey-brown text-custom-ochre focus:ring-custom-ochre focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-custom-dark-olive">Manage Backups</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-x-3">
                <button type="button" onclick="hideAddAdminModal()"
                        class="px-6 py-2 border border-custom-light-grey-brown text-custom-dark-olive rounded-md
                               hover:bg-custom-light-grey-green focus:outline-none focus:ring-2 focus:ring-custom-light-grey-brown
                               transition ease-in-out duration-150 text-base">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-custom-ochre text-custom-white rounded-md hover:bg-custom-ochre-darker
                               focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-opacity-50
                               transition ease-in-out duration-150 text-base">
                    <i class="fas fa-plus mr-2"></i> Create Admin
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchAdmins').addEventListener('input', function(e) {
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

    // Add Admin Modal functions
    function showAddAdminModal() {
        const modal = document.getElementById('addAdminModal');
        modal.classList.remove('hidden');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.opacity = '1', 10);
    }

    function hideAddAdminModal() {
        const modal = document.getElementById('addAdminModal');
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('addAdminForm').reset();
        }, 300);
    }

    // Close modal when clicking outside
    document.getElementById('addAdminModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideAddAdminModal();
        }
    });

    // Password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (password !== confirmation) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endpush
