@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">My Accommodations</h1>
            <a href="{{ route('provider.accommodations.create') }}" class="bg-[#33595a] text-white px-6 py-2 rounded-md hover:bg-[#2c494a] transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Add New Accommodation
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        {{-- Filter and Search Form --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6" x-data="accommodationFilters()">
            <form action="{{ route('provider.accommodations.list') }}" method="GET">
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

                    {{-- Suburb Filter (populated dynamically by Alpine.js) --}}
                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                        <select name="suburb" id="suburb" x-model="selectedSuburb"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5"
                                :disabled="suburbs.length === 0 && !selectedSuburb"> {{-- Disable if no suburbs and no current selection --}}
                            <option value="">All Suburbs</option>
                            <template x-for="suburb in suburbs" :key="suburb">
                                <option :value="suburb" x-text="suburb"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-[#33595a] text-white px-6 py-2 rounded-md hover:bg-[#2c494a] transition duration-300 flex items-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search mr-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('provider.accommodations.list') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.76 2.75M3 12v7M3 12h7"/></svg>
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>
        {{-- End Filter and Search Form --}}

        @if ($accommodations->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">No Accommodations Found!</strong>
                <span class="block sm:inline">Adjust your filters or add new accommodations.</span>
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#f2f7ed]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rent / Week</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vacancies</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($accommodations as $accommodation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $photos = json_decode($accommodation->photos, true);
                                            $firstPhoto = count($photos) > 0 ? $photos[0] : null;
                                        @endphp
                                        @if ($firstPhoto)
                                            <img src="{{ asset('storage/' . $firstPhoto) }}" alt="{{ $accommodation->title }}" class="w-16 h-16 object-cover rounded-md shadow-sm">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center text-gray-500 text-xs">No Image</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $accommodation->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $accommodation->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $accommodation->suburb }}, {{ $accommodation->state }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($accommodation->rent_per_week, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $accommodation->total_vacancies - $accommodation->current_occupancy }} / {{ $accommodation->total_vacancies }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($accommodation->status == 'available') bg-green-100 text-green-800
                                            @elseif($accommodation->status == 'occupied') bg-red-100 text-red-800
                                            @elseif($accommodation->status == 'draft') bg-gray-100 text-gray-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($accommodation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('provider.accommodations.show', $accommodation) }}" class="text-[#33595a] hover:text-[#2c494a] mr-3">View</a>
                                        <a href="{{ route('provider.accommodations.edit', $accommodation) }}" class="text-[#cc8e45] hover:text-[#a67139] mr-3">Edit</a>
                                        <form action="{{ route('provider.accommodations.destroy', $accommodation) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this accommodation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $accommodations->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Alpine.js for dynamic suburb fetching --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('accommodationFilters', () => ({
                suburbs: [],
                selectedSuburb: '{{ request('suburb') }}', // Pre-select based on current request

                init() {
                    // Fetch suburbs for the initially selected state on page load
                    const initialState = document.getElementById('state').value;
                    if (initialState) {
                        this.fetchSuburbs(initialState, true); // true indicates initial load
                    }
                },

                async fetchSuburbs(stateCode, isInitialLoad = false) {
                    this.suburbs = []; // Clear current suburbs
                    if (!isInitialLoad) {
                        this.selectedSuburb = ''; // Clear selected suburb if state changes
                    }

                    if (!stateCode) {
                        return;
                    }

                    try {
                        const response = await fetch(`/get-suburbs/${stateCode}`);
                        if (!response.ok) {
                            throw new Error('Failed to fetch suburbs.');
                        }
                        const data = await response.json();
                        this.suburbs = data;

                        // On initial load, try to set the selected suburb if it exists in the fetched list
                        if (isInitialLoad && '{{ request('suburb') }}' && this.suburbs.includes('{{ request('suburb') }}')) {
                             this.selectedSuburb = '{{ request('suburb') }}';
                        }
                    } catch (error) {
                        console.error("Error fetching suburbs:", error);
                        // Optionally show a user-friendly error message
                    }
                },
            }));
        });
    </script>
@endsection