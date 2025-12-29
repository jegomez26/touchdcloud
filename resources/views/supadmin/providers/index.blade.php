@extends('supadmin.sa-db')

@section('title', 'Manage Providers - SIL Match Admin')

@section('content')
<div class="container mx-auto px-4 py-8 font-sans">
    <h1 class="text-3xl md:text-4xl font-extrabold text-custom-dark-teal mb-8 border-b-2 border-custom-light-grey-green pb-4">
        <i class="fas fa-hospital mr-3 text-custom-ochre"></i> Manage Providers
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

    <!-- Provider Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hospital text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $totalProviders }}</h3>
            <p class="text-custom-dark-olive">Total Providers</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-custom-green text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-green">{{ $activeSubscriptionsCount }}</h3>
            <p class="text-custom-dark-olive">Active Subscriptions</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-custom-ochre text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-ochre">{{ $expiredSubscriptionsCount }}</h3>
            <p class="text-custom-dark-olive">Expired Subscriptions</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-day text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $newThisWeekCount }}</h3>
            <p class="text-custom-dark-olive">New This Week</p>
        </div>
    </div>

    <!-- Providers Table -->
    <div class="bg-custom-white shadow-lg rounded-xl p-6 border border-custom-light-grey-green">
        <h2 class="text-2xl font-bold text-custom-dark-teal mb-6 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-list-alt mr-2 text-custom-ochre"></i> All Providers
        </h2>

        <div class="mb-6">
            <input type="text" id="searchProviders" placeholder="Search providers by name, business, email, or status..." 
                   class="w-full px-4 py-2 border border-custom-light-grey-brown rounded-md focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre text-custom-dark-olive placeholder-gray-500 transition-colors duration-200 ease-in-out">
        </div>

        @if($providers->count() > 0)
            <div class="overflow-x-auto relative shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-custom-light-grey-green bg-custom-white">
                    <thead class="bg-custom-light-cream">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Provider</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Business Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Subscription</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Plan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Joined</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-custom-light-grey-green">
                        @foreach($providers as $provider)
                        <tr class="hover:bg-custom-light-cream transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-custom-light-cream rounded-full flex items-center justify-center">
                                        <i class="fas fa-hospital text-custom-dark-teal"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-custom-dark-olive">{{ $provider->organisation_name }}</div>
                                        <div class="text-sm text-gray-700">{{ $provider->user->first_name }} {{ $provider->user->last_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($provider->email_address)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                            {{ $provider->email_address }}
                                        </div>
                                    @endif
                                    @if($provider->phone_number)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            {{ $provider->phone_number }}
                                        </div>
                                    @endif
                                    @if($provider->office_address)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                            {{ Str::limit($provider->office_address, 30) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($provider->subscriptions->count() > 0)
                                    @php $latestSubscription = $provider->subscriptions->sortByDesc('created_at')->first(); @endphp
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                            {{ $latestSubscription->created_at->format('M d, Y') }}
                                        </div>
                                        @if($latestSubscription->ends_at)
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                                Expires: {{ $latestSubscription->ends_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No subscription</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($provider->subscriptions->count() > 0)
                                    @php $latestSubscription = $provider->subscriptions->sortByDesc('created_at')->first(); @endphp
                                    @if($latestSubscription->plan)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-crown mr-1"></i>
                                            {{ $latestSubscription->plan->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-crown mr-1"></i>
                                            {{ $latestSubscription->plan_name ?? 'Unknown Plan' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm">No plan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($provider->subscriptions->count() > 0)
                                    @php $latestSubscription = $provider->subscriptions->sortByDesc('created_at')->first(); @endphp
                                    @if($latestSubscription->stripe_status === 'active' || $latestSubscription->paypal_status === 'active')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-custom-green bg-opacity-20 text-custom-dark-olive">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @elseif($latestSubscription->stripe_status === 'expired' || $latestSubscription->paypal_status === 'expired')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Expired
                                        </span>
                                    @elseif($latestSubscription->stripe_status === 'cancelled' || $latestSubscription->paypal_status === 'cancelled')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                            <i class="fas fa-ban mr-1"></i>
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-custom-ochre bg-opacity-20 text-custom-ochre">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ ucfirst($latestSubscription->stripe_status ?? $latestSubscription->paypal_status ?? 'Unknown') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                        <i class="fas fa-minus mr-1"></i>
                                        No Subscription
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Activation/Deactivation buttons --}}
                                    @if($provider->user && $provider->user->is_active)
                                        <form action="{{ route('superadmin.providers.deactivate', $provider) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    class="px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition ease-in-out duration-150 text-sm"
                                                    onclick="showDelete('Are you sure you want to deactivate {{ $provider->organisation_name }}? This will prevent them from logging in.', () => this.closest('form').submit())"
                                                    title="Deactivate Account">
                                                Deactivate
                                            </button>
                                        </form>
                                    @elseif($provider->user && !$provider->user->is_active)
                                        <form action="{{ route('superadmin.providers.activate', $provider) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    class="px-4 py-2 rounded-md bg-custom-green text-custom-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-custom-green focus:ring-opacity-50 transition ease-in-out duration-150 text-sm"
                                                    onclick="showConfirm('Are you sure you want to activate {{ $provider->organisation_name }}? They will be able to log in again.', () => this.closest('form').submit())"
                                                    title="Activate Account">
                                                Activate
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800" title="No user account associated">
                                            No User
                                        </span>
                                    @endif
                                    
                                    @if($provider->subscription && $provider->subscription->status === 'active')
                                        <form action="{{ route('superadmin.providers.reject', $provider) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    onclick="showDelete('Are you sure you want to reject {{ $provider->business_name }}? This will revoke their access.', () => this.closest('form').submit())"
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @elseif($provider->subscription && $provider->subscription->status === 'expired')
                                        <form action="{{ route('superadmin.providers.approve', $provider) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                    onclick="showConfirm('Are you sure you want to approve {{ $provider->business_name }}? This will grant them access.', () => this.closest('form').submit())"
                                                    title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
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
            {{ $providers->links() }}
        </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-hospital text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-custom-dark-olive mb-2">No providers found</h3>
                <p class="text-custom-dark-olive">There are no providers in the system yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Provider Details Modal -->
<div id="providerDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Provider Details</h3>
                <button onclick="closeProviderDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="providerDetailsContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchProviders').addEventListener('input', function(e) {
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

    // Provider details modal
    function showProviderDetails(providerId) {
        // This would typically fetch provider details via AJAX
        // For now, we'll show a placeholder
        document.getElementById('providerDetailsContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-hospital text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">Provider details for ID: ${providerId}</p>
                <p class="text-sm text-gray-400 mt-2">This feature can be enhanced with AJAX loading</p>
            </div>
        `;
        document.getElementById('providerDetailsModal').classList.remove('hidden');
    }

    function closeProviderDetails() {
        document.getElementById('providerDetailsModal').classList.add('hidden');
    }

    function editProvider(providerId) {
        // This would typically redirect to edit page or open edit modal
        alert('Edit functionality for provider ID: ' + providerId + ' would be implemented here');
    }

    // Close modal when clicking outside
    document.getElementById('providerDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProviderDetails();
        }
    });
</script>
@endpush
