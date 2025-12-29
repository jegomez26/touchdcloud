@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">My Accommodations</h1>
            @if($canAddAccommodation)
                <a href="{{ route('provider.accommodations.create') }}" class="bg-[#33595a] text-white px-6 py-2 rounded-md hover:bg-[#2c494a] transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Add New Accommodation
                </a>
            @else
                <button disabled class="bg-gray-400 text-gray-600 px-6 py-2 rounded-md cursor-not-allowed flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Accommodation Limit Reached ({{ $currentAccommodationCount }}/{{ $accommodationLimit ?? 'Unlimited' }})
                </button>
            @endif
        </div>

        @if (!$canAddAccommodation)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline">
                        <strong>Accommodation Limit Reached:</strong> You have reached your subscription limit of {{ $accommodationLimit ?? 'unlimited' }} accommodations. 
                        @if($accommodationLimit)
                            You currently have {{ $currentAccommodationCount }} accommodations. 
                        @endif
                        Please upgrade your subscription to add more accommodations.
                    </span>
                </div>
            </div>
        @endif

        {{-- Filter and Search Form --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6" x-data="accommodationFilters()">
            <form action="{{ route('provider.accommodations.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    {{-- Search Input --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" placeholder="Search by title, address, etc."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5"
                               value="{{ request('search') }}">
                    </div>

                    {{-- Type Filter --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5">
                            <option value="">All Types</option>
                            <option value="Supported Independent Living" {{ request('type') == 'Supported Independent Living' ? 'selected' : '' }}>Supported Independent Living</option>
                            <optgroup label="Specialist Disability Accommodation (SDA)">
                                @foreach($accommodationTypes as $typeOption)
                                    @if($typeOption != 'Supported Independent Living') {{-- Avoid duplication --}}
                                        <option value="{{ $typeOption }}" {{ request('type') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5">
                            <option value="">All Statuses</option>
                            @foreach($accommodationStatuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State Filter (with Alpine.js for suburbs) --}}
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state" id="state" @change="fetchSuburbs($event.target.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5">
                            <option value="">All States</option>
                            @foreach($australianStates as $code => $name)
                                <option value="{{ $code }}" {{ request('state') == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Suburb Filter --}}
                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                        <input type="text" name="suburb" id="suburb" placeholder="Enter suburb name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5"
                               value="{{ request('suburb') }}">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-[#33595a] text-white px-6 py-2 rounded-md hover:bg-[#2c494a] transition duration-300 flex items-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search mr-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('provider.accommodations.index') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.76 2.75M3 12v7M3 12h7"/></svg>
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>
        {{-- End Filter and Search Form --}}

        @if ($accommodations->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white shadow-lg rounded-lg p-12 text-center">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Accommodations Found</h3>
                <p class="text-gray-600 mb-6">Get started by adding your first accommodation property.</p>
                @if($canAddAccommodation)
                    <a href="{{ route('provider.accommodations.create') }}" class="inline-flex items-center px-6 py-3 bg-[#33595a] text-white font-medium rounded-lg hover:bg-[#2c494a] transition duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Your First Accommodation
                    </a>
                @else
                    <button disabled class="inline-flex items-center px-6 py-3 bg-gray-400 text-gray-600 font-medium rounded-lg cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Accommodation Limit Reached ({{ $currentAccommodationCount }}/{{ $accommodationLimit ?? 'Unlimited' }})
                    </button>
                @endif
            </div>
        @else
            {{-- Accommodations Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($accommodations as $accommodation)
                    @php
                        $photos = $accommodation->photos ?? []; // Already decoded by model cast
                        $firstPhoto = count($photos) > 0 ? $photos[0] : null;
                    @endphp
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        {{-- Image --}}
                        <div class="h-48 bg-gray-200 relative">
                            @if ($firstPhoto)
                                <img src="{{ accommodation_image_url($firstPhoto) }}" alt="{{ $accommodation->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            {{-- Status Badge --}}
                            <div class="absolute top-3 right-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($accommodation->status == 'available') bg-green-100 text-green-800
                                    @elseif($accommodation->status == 'occupied') bg-red-100 text-red-800
                                    @elseif($accommodation->status == 'draft') bg-gray-100 text-gray-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($accommodation->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $accommodation->title }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $accommodation->type }}</p>
                            <p class="text-sm text-gray-600 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $accommodation->suburb }}, {{ $accommodation->state }}
                            </p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold text-lg text-[#33595a]">${{ number_format($accommodation->rent_per_week, 2) }}</span>
                                    <span class="text-gray-500">/week</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold">{{ $accommodation->total_vacancies - $accommodation->current_occupancy }}</span>
                                    <span class="text-gray-500">/ {{ $accommodation->total_vacancies }} vacancies</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex space-x-2">
                                <a href="{{ route('provider.accommodations.show', $accommodation) }}" class="flex-1 bg-[#33595a] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#2c494a] transition duration-300 text-center">
                                    View Details
                                </a>
                                <a href="{{ route('provider.accommodations.edit', $accommodation) }}" class="flex-1 bg-[#cc8e45] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#a67139] transition duration-300 text-center">
                                    Edit
                                </a>
                                <button onclick="confirmDelete({{ $accommodation->id }}, '{{ addslashes($accommodation->title) }}')" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $accommodations->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Accommodation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete "<span id="accommodationTitle" class="font-semibold"></span>"? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600 transition duration-300">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Success!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="successMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeSuccessModal()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Modal --}}
    <div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Error</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="errorMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeErrorModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js for dynamic suburb fetching --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('accommodationFilters', () => ({
                // No longer needed since we're using text input for suburb
            }));
        });

        // Message handling functions
        function showMessage(type, message) {
            if (type === 'success') {
                window.modalManager.success(message);
            } else if (type === 'error') {
                window.modalManager.error(message);
            }
        }

        function hideMessage(messageId) {
            // This function is kept for compatibility but now uses the modal manager
            if (messageId === 'success-message') {
                window.modalManager.hide('success-modal');
            } else if (messageId === 'error-message') {
                window.modalManager.hide('error-modal');
            }
        }

        // Check for session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                window.modalManager.success('{{ session('status') }}');
            @endif
            
            @if (session('error'))
                window.modalManager.error('{{ session('error') }}');
            @endif
        });

        // Modal functions
        function confirmDelete(accommodationId, accommodationTitle) {
            document.getElementById('accommodationTitle').textContent = accommodationTitle;
            document.getElementById('deleteForm').action = `/provider/accommodations/${accommodationId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // AJAX form submission for delete
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                closeDeleteModal();
                if (data.success) {
                    showMessage('success', data.message || 'Accommodation deleted successfully.');
                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage('error', data.message || 'An error occurred while deleting the accommodation.');
                }
            })
            .catch(error => {
                closeDeleteModal();
                showMessage('error', 'An error occurred while deleting the accommodation.');
                console.error('Error:', error);
            });
        });

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'deleteModal') {
                closeDeleteModal();
            }
            if (e.target.id === 'successModal') {
                closeSuccessModal();
            }
            if (e.target.id === 'errorModal') {
                closeErrorModal();
            }
        });
    </script>
@endsection