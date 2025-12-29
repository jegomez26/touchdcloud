@extends('supadmin.sa-db') {{-- Assuming your superadmin layout is at resources/views/superadmin/sa-db.blade.php --}}

@section('title', 'Manage Support Coordinators - SIL Match Admin')

@section('content')
<div class="container mx-auto px-4 py-8 font-sans"> {{-- Added font-sans for general typography --}}
    <h1 class="text-3xl md:text-4xl font-extrabold text-custom-dark-teal mb-8 border-b-2 border-custom-light-grey-green pb-4">
        <i class="fas fa-users-cog mr-3 text-custom-ochre"></i> Manage Support Coordinators
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
                <i class="fas fa-users-cog text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $totalCoordinators }}</h3>
            <p class="text-custom-dark-olive">Total Coordinators</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-custom-green text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-green">{{ $verifiedCoordinators }}</h3>
            <p class="text-custom-dark-olive">Verified</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hourglass-half text-custom-ochre text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-ochre">{{ $pendingCount }}</h3>
            <p class="text-custom-dark-olive">Pending Approval</p>
        </div>
        
        <div class="bg-custom-white shadow-lg rounded-xl p-6 text-center border border-custom-light-grey-green">
            <div class="w-16 h-16 bg-custom-light-cream rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-day text-custom-dark-teal text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-custom-dark-teal">{{ $newThisWeekCount }}</h3>
            <p class="text-custom-dark-olive">New This Week</p>
        </div>
    </div>

    <div class="bg-custom-white shadow-lg rounded-xl p-6 mb-10 border border-custom-light-grey-green">
        <h2 class="text-2xl font-bold text-custom-dark-teal mb-6 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-hourglass-half mr-2 text-custom-ochre"></i> Pending Approval ({{ $pendingCoordinators->count() }})
        </h2>
        @if ($pendingCoordinators->isEmpty())
            <p class="text-custom-dark-olive text-lg py-4">No support coordinators currently pending approval. All caught up!</p>
        @else
            <div class="overflow-x-auto relative shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-custom-light-grey-green bg-custom-white">
                    <thead class="bg-custom-light-cream">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Company Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">ABN</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Registered On</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-custom-light-grey-green">
                        @foreach ($pendingCoordinators as $coordinator)
                            <tr class="hover:bg-custom-light-cream transition-colors duration-200 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-custom-dark-olive">
                                    {{ $coordinator->first_name }} {{ $coordinator->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $coordinator->user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $coordinator->company_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $coordinator->abn }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                                        @if($coordinator->status === 'pending_verification')
                                            bg-custom-ochre bg-opacity-20 text-custom-ochre
                                        @elseif($coordinator->status === 'verified')
                                            bg-custom-green bg-opacity-20 text-custom-dark-olive
                                        @elseif($coordinator->status === 'rejected')
                                            bg-red-200 text-red-800
                                        @else
                                            bg-gray-200 text-gray-800
                                        @endif
                                    ">
                                        {{ str_replace('_', ' ', $coordinator->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $coordinator->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <form action="{{ route('superadmin.support-coordinators.approve', $coordinator->id) }}" method="POST" class="inline-block mr-2"
                                        onsubmit="return confirm('Are you sure you want to approve {{ $coordinator->first_name }} {{ $coordinator->last_name }}?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="px-4 py-2 rounded-md bg-custom-green text-custom-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-custom-green focus:ring-opacity-50 transition ease-in-out duration-150 text-sm">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                            class="px-4 py-2 rounded-md bg-custom-ochre text-custom-white hover:bg-custom-ochre-darker focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-opacity-50 transition ease-in-out duration-150 text-sm"
                                            onclick="showRejectModal('{{ $coordinator->id }}', '{{ $coordinator->first_name }} {{ $coordinator->last_name }}')">
                                        Reject
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-custom-white shadow-lg rounded-xl p-6 border border-custom-light-grey-green" x-data="{
    search: '',
    sortColumn: 'name',
    sortDirection: 'asc',
    filteredCoordinators: [],
    init() {
        // Assign the globally prepared data directly
        this.filteredCoordinators = initialAllCoordinators;
        this.sortData('name'); // Initial sort
    },
    get sortedCoordinators() {
        let sorted = this.filteredCoordinators;
        if (this.sortColumn) {
            sorted = sorted.sort((a, b) => {
                let valA = a[this.sortColumn] ? (typeof a[this.sortColumn] === 'string' ? a[this.sortColumn].toLowerCase() : a[this.sortColumn]) : '';
                let valB = b[this.sortColumn] ? (typeof b[this.sortColumn] === 'string' ? b[this.sortColumn].toLowerCase() : b[this.sortColumn]) : '';

                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        }
        return sorted;
    },
    get filteredAndSortedCoordinators() {
        return this.sortedCoordinators.filter(coordinator => {
            // Added null check for company_name for robustness
            return coordinator.name.toLowerCase().includes(this.search.toLowerCase()) ||
                   coordinator.email.toLowerCase().includes(this.search.toLowerCase()) ||
                   (coordinator.company_name && coordinator.company_name.toLowerCase().includes(this.search.toLowerCase())) ||
                   coordinator.status.toLowerCase().includes(this.search.toLowerCase());
        });
    },
    sortData(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
    },
    getSortIcon(column) {
        if (this.sortColumn === column) {
            return this.sortDirection === 'asc' ? '▲' : '▼';
        }
        return '';
    }
}">
        <h2 class="text-2xl font-bold text-custom-dark-teal mb-6 pb-3 border-b border-custom-light-grey-green">
            <i class="fas fa-list-alt mr-2 text-custom-ochre"></i> All Support Coordinators
        </h2>

        <div class="mb-6">
            <input type="text" x-model="search" placeholder="Search by name, email, company, or status..."
                   class="w-full px-4 py-2 border border-custom-light-grey-brown rounded-md focus:ring-2 focus:ring-custom-ochre focus:border-custom-ochre text-custom-dark-olive placeholder-gray-500 transition-colors duration-200 ease-in-out">
        </div>

        <div class="overflow-x-auto relative shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-custom-light-grey-green bg-custom-white">
                <thead class="bg-custom-light-cream">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider cursor-pointer" @click="sortData('name')">
                            Name <span x-text="getSortIcon('name')"></span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider cursor-pointer" @click="sortData('email')">
                            Email <span x-text="getSortIcon('email')"></span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider cursor-pointer" @click="sortData('company_name')">
                            Company Name <span x-text="getSortIcon('company_name')"></span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider cursor-pointer" @click="sortData('status')">
                            Status <span x-text="getSortIcon('status')"></span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-custom-dark-teal uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom-light-grey-green">
                    <template x-for="coordinator in filteredAndSortedCoordinators" :key="coordinator.id">
                        <tr class="hover:bg-custom-light-cream transition-colors duration-200 ease-in-out">
                            <td x-text="coordinator.name" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-custom-dark-olive"></td>
                            <td x-text="coordinator.email" class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"></td>
                            <td x-text="coordinator.company_name" class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    :class="{
                                        'bg-custom-ochre bg-opacity-20 text-custom-ochre': coordinator.status === 'pending verification',
                                        'bg-custom-green bg-opacity-20 text-custom-dark-olive': coordinator.status === 'verified',
                                        'bg-red-200 text-red-800': coordinator.status === 'rejected',
                                        'bg-gray-200 text-gray-800': coordinator.status !== 'pending verification' && coordinator.status !== 'verified' && coordinator.status !== 'rejected'
                                    }"
                                    x-text="coordinator.status">
                                </span>
                            </td>
                            <td x-text="coordinator.notes || 'N/A'" class="px-6 py-4 whitespace-normal text-sm text-gray-700"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Activation/Deactivation buttons --}}
                                    <template x-if="coordinator.is_active === true">
                                        <form :action="`/superadmin/support-coordinators/${coordinator.id}/deactivate`" method="POST" class="inline">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="PUT">
                                            <button type="button" 
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                                                    @click="showDelete(`Are you sure you want to deactivate ${coordinator.first_name} ${coordinator.last_name}? This will prevent them from logging in.`, () => this.closest('form').submit())"
                                                    title="Deactivate Account">
                                                Deactivate
                                            </button>
                                        </form>
                                    </template>
                                    <template x-if="coordinator.is_active === false">
                                        <form :action="`/superadmin/support-coordinators/${coordinator.id}/activate`" method="POST" class="inline">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="PUT">
                                            <button type="button" 
                                                    class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 transition-colors"
                                                    @click="showConfirm(`Are you sure you want to activate ${coordinator.first_name} ${coordinator.last_name}? They will be able to log in again.`, () => this.closest('form').submit())"
                                                    title="Activate Account">
                                                Activate
                                            </button>
                                        </form>
                                    </template>
                                    <template x-if="coordinator.is_active === null || coordinator.is_active === undefined">
                                        <span class="bg-gray-400 text-white px-3 py-1 rounded text-xs" title="No user account associated">
                                            No User
                                        </span>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredAndSortedCoordinators.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No matching support coordinators found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 hidden transition-opacity duration-300 ease-in-out">
    <div class="relative bg-custom-white rounded-lg shadow-xl p-8 w-full max-w-md mx-4 transform transition-transform duration-300 ease-in-out scale-95"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <h3 class="text-2xl font-bold text-custom-dark-teal mb-5 pb-3 border-b border-custom-light-grey-green">
            Reject Support Coordinator: <span id="rejectCoordinatorName" class="text-custom-ochre"></span>
        </h3>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            {{-- Hidden input to pass coordinator ID if validation fails on server-side --}}
            <input type="hidden" name="coordinator_id" id="modalCoordinatorId">

            <div class="mb-6">
                <label for="verification_notes" class="block text-sm font-semibold text-custom-dark-olive mb-2">
                    Reason for Rejection (Verification Notes)
                </label>
                <textarea name="verification_notes" id="verification_notes" rows="5"
                          class="mt-1 block w-full rounded-md border-custom-light-grey-brown shadow-sm
                                 focus:border-custom-ochre focus:ring focus:ring-custom-ochre-darker focus:ring-opacity-30
                                 text-custom-dark-teal placeholder-gray-500 bg-custom-light-cream
                                 transition-colors duration-200 ease-in-out p-3"
                          placeholder="e.g., Company is not registered, ABN is invalid, missing documentation, etc." required></textarea>
                @error('verification_notes')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-x-3">
                <button type="button" onclick="hideRejectModal()"
                        class="px-6 py-2 border border-custom-light-grey-brown text-custom-dark-olive rounded-md
                               hover:bg-custom-light-grey-green focus:outline-none focus:ring-2 focus:ring-custom-light-grey-brown
                               transition ease-in-out duration-150 text-base">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-custom-ochre text-custom-white rounded-md hover:bg-custom-ochre-darker
                               focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-opacity-50
                               transition ease-in-out duration-150 text-base">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$initialAllCoordinatorsData = $allCoordinators->map(function($c) {
    return [
        'id' => $c->id,
        'name' => $c->first_name . ' ' . $c->last_name,
        'email' => $c->user->email,
        'company_name' => $c->company_name,
        'status' => str_replace('_', ' ', $c->status),
        'notes' => $c->verification_notes,
        'is_active' => $c->user ? $c->user->is_active : false,
    ];
})->values()->toArray(); // Ensure it's a simple numerically indexed array

$allCoordinatorsDataForJsErrorHandling = $allCoordinators->map(function($c) {
    return [
        'id' => $c->id,
        'name' => $c->first_name . ' ' . $c->last_name,
        'email' => $c->user->email,
        'company_name' => $c->company_name,
        'status' => str_replace('_', ' ', $c->status),
        'notes' => $c->verification_notes,
        'is_active' => $c->user ? $c->user->is_active : false,
    ];
})->keyBy('id')->toArray(); // Key by ID for easy lookup, then convert to array
?>

<script>
    const initialAllCoordinators = JSON.parse('{!! json_encode($initialAllCoordinatorsData) !!}');


    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');
    const rejectCoordinatorName = document.getElementById('rejectCoordinatorName');
    const modalCoordinatorId = document.getElementById('modalCoordinatorId');

    function showRejectModal(coordinatorId, coordinatorName) {
        rejectCoordinatorName.textContent = coordinatorName;
        rejectForm.action = `/superadmin/support-coordinators/${coordinatorId}/reject`;
        modalCoordinatorId.value = coordinatorId;
        rejectModal.classList.remove('hidden');
        rejectModal.style.opacity = '0';
        setTimeout(() => rejectModal.style.opacity = '1', 10);
    }

    function hideRejectModal() {
        rejectModal.style.opacity = '0';
        setTimeout(() => {
            rejectModal.classList.add('hidden');
            document.getElementById('verification_notes').value = '';
            modalCoordinatorId.value = '';
        }, 300);
    }

    window.onclick = function(event) {
        if (event.target == rejectModal) {
            hideRejectModal();
        }
    }

    // Prepare the coordinator data for JavaScript error handling
    const allCoordinatorsDataForJs = {!! json_encode($allCoordinatorsDataForJsErrorHandling) !!};

    @if ($errors->has('verification_notes') && old('coordinator_id'))
        const errorCoordinatorId = "{{ old('coordinator_id') }}";
        const coordinatorName = allCoordinatorsDataForJs[errorCoordinatorId] ?
                                allCoordinatorsDataForJs[errorCoordinatorId].name : 'Unknown';

        document.getElementById('verification_notes').value = "{{ old('verification_notes') }}";
        showRejectModal(errorCoordinatorId, coordinatorName);
    @endif
</script>
@endsection