@extends('supadmin.sa-db') {{-- Extends your main super admin layout --}}

@section('content')
{{-- Alpine.js data for modals (now driven by Alpine.data('mainData', ...) in script) --}}
<div x-data="mainData()" @keydown.escape="closeModals()">

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-[#33595a]">Manage NDIS Businesses</h1>
        <p class="mt-2 text-[#bcbabb]">Oversee and manage registered NDIS businesses in the system.</p>
    </div>

    <div class="bg-white rounded-lg shadow-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            {{-- Filter/Search --}}
            <div class="w-full md:w-1/2 mb-4 md:mb-0">
                <form action="{{ route('superadmin.ndis-businesses.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Search businesses..."
                            value="{{ request('search') }}"
                            class="flex-grow border border-[#e1e7dd] rounded-md px-4 py-2 mr-2 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent text-[#3e4732]">
                    <button type="submit" class="bg-[#cc8e45] text-white px-4 py-2 rounded-md hover:bg-orange-600 transition duration-200 shadow-md">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('superadmin.ndis-businesses.index') }}" class="ml-2 px-3 py-2 text-[#33595a] hover:text-[#000000] transition duration-200" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Add New Business Button --}}
            {{-- Changed @click to use openAddModal() --}}
            <button @click="openAddModal()" class="bg-[#33595a] text-white px-6 py-2 rounded-md hover:bg-[#3e4732] transition duration-200 shadow-md flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Business
            </button>

        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input:</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- NDIS Businesses Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-[#e1e7dd] rounded-lg">
                <thead>
                    <tr class="bg-[#f8f1e1] text-[#33595a] text-left">
                        <th class="py-3 px-4 uppercase font-semibold text-sm rounded-tl-lg">Business Name</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm">ABN</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm">Services Offered</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center rounded-tr-lg">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ndisBusinesses as $business)
                        <tr class="border-b border-[#e1e7dd] last:border-b-0 hover:bg-[#f8f1e1]/50 transition duration-150">
                            <td class="py-3 px-4 text-[#3e4732]">{{ $business->business_name }}</td>
                            <td class="py-3 px-4 text-[#3e4732]">{{ $business->abn }}</td>
                            @php
                                $services = is_array($business->services_offered)
                                    ? implode(', ', $business->services_offered)
                                    : $business->services_offered;
                            @endphp

                            <td class="py-3 px-4 text-[#3e4732]">
                                {{ Str::limit($services, 50, '...') }}
                            </td>
                            <td class="py-3 px-4 flex justify-center items-center space-x-2">
                                {{-- Changed @click to use openEditModal(business) --}}
                                <button @click="openEditModal({{ $business->toJson() }})"
                                    class="bg-[#cc8e45] text-white px-3 py-1 rounded-md hover:bg-orange-600 transition duration-200 text-sm shadow-md flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button @click="showDeleteModal = true; deletingBusinessId = {{ $business->id }};"
                                        class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-200 text-sm shadow-md flex items-center">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-4 text-center text-[#bcbabb]">No NDIS businesses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $ndisBusinesses->links() }}
        </div>
    </div>

    {{-- Define service options once --}}
    @php
        $serviceOptions = [
            "Support Coordination",
            "Specialist Disability Accommodation (SDA)",
            "Supported Independent Living (SIL)",
            "Assistance with Daily Living",
            "Community Participation",
            "Therapeutic Supports",
            "Plan Management",
            "Assistive Technology",
            "Household Tasks",
            "Travel and Transport Assistance",
            "Employment Support",
            "Early Childhood Supports"
        ];
    @endphp

    {{-- Add New Business Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div @click.away="closeModals()" class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-md relative animate-fade-in-up">
            <button @click="closeModals()" class="absolute top-4 right-4 text-[#bcbabb] hover:text-[#33595a] text-2xl font-bold">&times;</button>
            <h3 class="text-2xl font-bold text-[#33595a] mb-6">Add New NDIS Business</h3>
            <form action="{{ route('superadmin.ndis-businesses.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="new_business_name" class="block text-[#3e4732] text-sm font-semibold mb-2">Business Name</label>
                    <input type="text" id="new_business_name" name="business_name" x-model="newBusiness.business_name"
                           class="w-full px-4 py-2 border border-[#e1e7dd] rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] text-[#33595a]" required>
                </div>
                <div class="mb-4">
                    <label for="new_abn" class="block text-[#3e4732] text-sm font-semibold mb-2">ABN</label>
                    <input type="text" id="new_abn" name="abn" x-model="newBusiness.abn"
                           class="w-full px-4 py-2 border border-[#e1e7dd] rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] text-[#33595a]" required>
                </div>
                <div class="mb-4">
                    <label for="new_services_offered" class="block text-[#3e4732] text-sm font-semibold mb-2">Services Offered</label>
                    <select name="services_offered[]" id="new_services_offered" multiple
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select">
                        @foreach($serviceOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeModals()" class="px-5 py-2 bg-[#bcbabb] text-white rounded-md hover:bg-[#a09e9e] transition duration-200">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#cc8e45] text-white rounded-md hover:bg-orange-600 transition duration-200">Add Business</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Business Modal --}}
    <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div @click.away="closeModals()" class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-md relative animate-fade-in-up">
            <button @click="closeModals()" class="absolute top-4 right-4 text-[#bcbabb] hover:text-[#33595a] text-2xl font-bold">&times;</button>
            <h3 class="text-2xl font-bold text-[#33595a] mb-6">Edit NDIS Business</h3>
            {{-- ADDED x-if="editingBusiness" here --}}
            <template x-if="editingBusiness">
                <form x-bind:action="'{{ url('superadmin/ndis-businesses') }}/' + editingBusiness.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="edit_business_name" class="block text-[#3e4732] text-sm font-semibold mb-2">Business Name</label>
                        <input type="text" id="edit_business_name" name="business_name" x-model="editingBusiness.business_name"
                               class="w-full px-4 py-2 border border-[#e1e7dd] rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] text-[#33595a]" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_abn" class="block text-[#3e4732] text-sm font-semibold mb-2">ABN</label>
                        <input type="text" id="edit_abn" name="abn" x-model="editingBusiness.abn"
                               class="w-full px-4 py-2 border border-[#e1e7dd] rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] text-[#33595a]" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_services_offered" class="block text-[#3e4732] text-sm font-semibold mb-2">Services Offered</label>
                        <select name="services_offered[]" id="edit_services_offered" multiple
    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            x-init="
                                editChoicesInstance = new Choices($el, {
                                    removeItemButton: true,
                                    placeholderValue: 'Edit services offered',
                                    searchEnabled: true,
                                    itemSelectText: '',
                                });
                                // Set initial choices once the instance is created and data is available
                                // This is critical for initial load
                                if (editingBusiness && editingBusiness.services_offered) {
                                    editChoicesInstance.setChoiceByValue(editingBusiness.services_offered);
                                }
                            "
                            x-effect="
                                // React to changes in editingBusiness.services_offered
                                // This handles cases where data might update *after* x-init, or if modal is re-opened
                                if (editChoicesInstance && editingBusiness && editingBusiness.services_offered && JSON.stringify(editChoicesInstance.getValue(true)) !== JSON.stringify(editingBusiness.services_offered)) {
                                    editChoicesInstance.clearStore(); // Clear existing selections
                                    editChoicesInstance.setChoiceByValue(editingBusiness.services_offered);
                                }
                            "
                        >
                            @foreach($serviceOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeModals()" class="px-5 py-2 bg-[#bcbabb] text-white rounded-md hover:bg-[#a09e9e] transition duration-200">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-[#cc8e45] text-white rounded-md hover:bg-orange-600 transition duration-200">Update Business</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div @click.away="closeModals()" class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-sm relative animate-fade-in-up">
            <button @click="closeModals()" class="absolute top-4 right-4 text-[#bcbabb] hover:text-[#33595a] text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-bold text-[#33595a] mb-4">Confirm Deletion</h3>
            <p class="text-[#3e4732] mb-6">Are you sure you want to delete this NDIS business? This action cannot be undone.</p>
            <form x-bind:action="'{{ url('superadmin/ndis-businesses') }}/' + deletingBusinessId" method="POST" class="flex justify-end space-x-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="closeModals()" class="px-5 py-2 bg-[#bcbabb] text-white rounded-md hover:bg-[#a09e9e] transition duration-200">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
    {{-- Choices.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
@endpush

@push('scripts')
{{-- Include Alpine.js if not already included in your sa-db layout --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mainData', () => ({
            showAddModal: false,
            showEditModal: false,
            showDeleteModal: false,
            _editingBusiness: null, // Private backing property
            deletingBusinessId: null,
            newBusiness: { business_name: '', abn: '', services_offered: [], is_verified: false },

            addChoicesInstance: null,
            editChoicesInstance: null,

            // Setter for editingBusiness to ensure services_offered is always an array
            set editingBusiness(value) {
                if (value) {
                    let services = value.services_offered;

                    if (typeof services === 'string' && services.trim() !== '') {
                        try {
                            // Attempt to parse as JSON first (Laravel often stores arrays as JSON)
                            const parsed = JSON.parse(services);
                            if (Array.isArray(parsed)) {
                                services = parsed;
                            } else {
                                // If JSON parsing results in non-array, or if it's not JSON,
                                // fallback to comma-separated string
                                services = services.split(',').map(s => s.trim());
                            }
                        } catch (e) {
                            // If JSON.parse throws an error (e.g., invalid JSON),
                            // fallback to comma-separated string
                            services = services.split(',').map(s => s.trim());
                        }
                    } else if (services === null || services === undefined || !Array.isArray(services)) {
                        // Ensure it's an array even if null, undefined, or unexpected type
                        services = [];
                    }

                    // Assign the cleaned services_offered back to the value
                    this._editingBusiness = { ...value, services_offered: services };
                } else {
                    this._editingBusiness = null;
                }
            },
            get editingBusiness() {
                return this._editingBusiness;
            },

            // Methods to open modals
            openAddModal() {
                this.showAddModal = true;
            },

            openEditModal(business) {
                // When you call openEditModal, the setter for editingBusiness will clean the data
                this.editingBusiness = business;
                this.showEditModal = true;
            },

            // Method to close modals
            closeModals() {
                this.showAddModal = false;
                this.showEditModal = false;
                this.showDeleteModal = false;

                this.$nextTick(() => {
                    this.editingBusiness = null; // This will trigger x-if to remove the form
                });

                // Reset newBusiness and clear Choices.js instance for add modal if it exists
                this.newBusiness = { business_name: '', abn: '', services_offered: [], is_verified: false };
                if (this.addChoicesInstance) {
                    this.addChoicesInstance.clearStore();
                    // choices.js `setChoiceByValue` can take an empty array to clear selections
                    this.addChoicesInstance.setChoiceByValue([]);
                }
                // editChoicesInstance is handled by x-if destroying/recreating
            }
        }));
    });
</script>

<style>
    /* Your custom transition for modals */
    .animate-fade-in-up {
        animation: fadeInScaleUp 0.3s ease-out forwards;
    }

    @keyframes fadeInScaleUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
@endpush