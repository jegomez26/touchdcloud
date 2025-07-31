@extends('supcoor.sc-db') {{-- Extend your sc-db layout --}}

@section('main-content') {{-- Start the main-content section --}}
    <h2 class="font-semibold text-2xl md:text-3xl text-[#33595a] leading-tight mb-6 md:mb-8">
        {{ __('My Participants') }}
    </h2>

    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8"> {{-- Responsive padding: smaller on mobile, larger on desktop --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4 md:gap-6">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-[#33595a] text-center md:text-left w-full md:w-auto">Managed Participants</h3>
            <a href="{{ route('sc.participants.create') }}" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 sm:px-6 sm:py-3 bg-[#cc8e45] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                <svg class="w-4 h-4 mr-2 -ml-1 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add New Participant
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search and Filter Form --}}
        <div class="mb-6 md:mb-8 bg-gray-50 p-4 sm:p-6 rounded-lg shadow-inner border border-gray-100"
             x-data="{
                openFilters: false,
                selectedDisabilityType: {{ json_encode(request('disability_type', '')) }},
                selectedState: {{ json_encode(request('state', '')) }},
                currentSuburb: {{ json_encode(request('suburb', '')) }}
             }"
             x-init="
                if (selectedState) {
                    loadSuburbsForFilter(selectedState, currentSuburb);
                } else {
                    document.getElementById('suburb').disabled = true;
                }
             ">
            <form action="{{ route('sc.participants.list') }}" method="GET" class="space-y-4 sm:space-y-5">
                <div class="flex flex-col md:flex-row items-end gap-3 md:gap-4"> {{-- Adjusted gaps for smaller screens --}}
                    <div class="flex-grow w-full"> {{-- Ensure input takes full width on small screens --}}
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Participants</label>
                        <input type="text" name="search" id="search" placeholder="Name or specific disability..."
                               value="{{ request('search') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 placeholder-gray-400 p-2.5 text-base">
                    </div>
                    <div class="w-full md:w-auto"> {{-- Button takes full width on small screens --}}
                        <button type="submit" class="w-full inline-flex justify-center items-center px-5 py-2.5 sm:px-6 sm:py-2.5 bg-[#33595a] border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-wider hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#33595a] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Search
                        </button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="openFilters = !openFilters" class="text-[#cc8e45] hover:underline flex items-center text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01.293.707V19a1 1 0 01-1 1H4a1 1 0 01-1-1v-2.586a1 1 0 01-.293-.707V4z"></path></svg>
                        <span x-text="openFilters ? 'Hide Advanced Filters' : 'Show Advanced Filters'"></span>
                        <svg class="ml-1 w-4 h-4 transform transition-transform duration-200" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>

                <div x-show="openFilters" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 border-t border-gray-200 pt-5 mt-5"> {{-- Adjusted grid for sm screens --}}

                    {{-- Disability Type Filter --}}
                    <div>
                        <label for="disability_type" class="block text-sm font-medium text-gray-700 mb-1">Disability Type</label>
                        <select id="disability_type" name="disability_type"
                                x-model="selectedDisabilityType"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 p-2.5 text-base">
                            <option value="">All Disability Types</option>
                            @foreach($disabilityTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State Filter --}}
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select id="state" name="state"
                                x-model="selectedState"
                                @change="loadSuburbsForFilter(selectedState, currentSuburb)"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 p-2.5 text-base">
                            <option value="">All States</option>
                            <option value="ACT">ACT</option>
                            <option value="NSW">NSW</option>
                            <option value="NT">NT</option>
                            <option value="QLD">QLD</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="VIC">VIC</option>
                            <option value="WA">WA</option>
                        </select>
                    </div>

                    {{-- Suburb Filter (Dependent on State) --}}
                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700 mb-1">Suburb</label>
                        <select id="suburb" name="suburb"
                                x-model="currentSuburb"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 p-2.5 text-base"
                                :disabled="!selectedState">
                            <option value="">All Suburbs</option>
                            {{-- Options will be loaded dynamically by Alpine.js/JavaScript --}}
                            {{-- Pre-populate if state and suburb already selected on page load --}}
                            @if(request('state') && !empty($suburbsForFilter))
                                @foreach($suburbsForFilter as $suburb)
                                    <option value="{{ $suburb }}" {{ request('suburb') == $suburb ? 'selected' : '' }}>{{ $suburb }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-span-full flex flex-col sm:flex-row justify-end gap-3 pt-2 sm:pt-0"> {{-- Buttons stack on mobile, side-by-side on sm+ --}}
                        <a href="{{ route('sc.participants.list') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-wider hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            Clear Filters
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-[#cc8e45] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        {{-- End Search and Filter Form --}}


        @if ($participants->isEmpty())
            <div class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg border border-gray-200 flex flex-col items-center justify-center p-4 sm:p-6"> {{-- Responsive padding for empty state --}}
                <img src="{{ asset('images/empty-participant.svg') }}" alt="No participants illustration" class="mx-auto max-w-xs h-32 sm:h-48 mb-4 sm:mb-6 opacity-80"> {{-- Responsive image size --}}
                <p class="text-lg sm:text-xl text-gray-600 mb-2 sm:mb-4 font-medium">No participants found matching your criteria.</p>
                <p class="text-gray-500 text-sm sm:text-base max-w-lg">Try adjusting your search terms or filters, or add a new participant to get started!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"> {{-- Adjusted gaps for smaller screens --}}
                @foreach ($participants as $participant)
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-100 flex flex-col transform transition-transform duration-200 hover:scale-[1.02] hover:shadow-lg"> {{-- Responsive padding --}}
                        <div class="flex flex-col sm:flex-row items-center sm:items-start mb-4 sm:mb-5 text-center sm:text-left"> {{-- Stack on mobile, side-by-side on sm+ --}}
                            {{-- Profile Avatar based on Gender --}}
                            @php
                                $avatarPath = 'images/general.png'; // Default
                                $randomMale = rand(1, 2);
                                $randomFemale = rand(1, 2);
                                // Ensure the gender values match what's stored in your database (e.g., "Male", "Female")
                                if ($participant->gender === 'Male') {
                                    $avatarPath = 'images/male' . $randomMale . '.png';
                                } elseif ($participant->gender === 'Female') {
                                    $avatarPath = 'images/female' . $randomFemale . '.png';
                                }
                            @endphp
                            <img src="{{ asset($avatarPath) }}" alt="{{ $participant->gender ?? 'General' }} Avatar" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto sm:mr-5 object-cover border-4 border-[#cc8e45] shadow-sm mb-3 sm:mb-0"> {{-- Responsive size and margin --}}

                            <div class="w-full">
                                <h4 class="text-xl sm:text-2xl font-bold text-[#33595a] leading-tight">{{ $participant->first_name }} {{ $participant->last_name }}</h4>
                                @if($participant->participant_code_name)
                                    <p class="text-gray-600 text-sm mt-1">Code: <span class="font-semibold">{{ $participant->participant_code_name }}</span></p>
                                @endif
                                @if($participant->birthday)
                                    <p class="text-gray-600 text-sm">Age: <span class="font-medium">{{ \Carbon\Carbon::parse($participant->birthday)->age }}</span></p>
                                @endif
                                @if($participant->suburb && $participant->state)
                                    <p class="text-gray-600 text-sm">Location: <span class="font-medium">{{ $participant->suburb }}, {{ $participant->state }}</span></p>
                                @endif
                            </div>
                        </div>

                        {{-- Accommodation Type Chip --}}
                        @if($participant->accommodation_type)
                            <div class="mb-3 sm:mb-4"> {{-- Responsive margin --}}
                                <span class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-semibold bg-[#33595a] text-white shadow-sm"> {{-- Responsive padding and text size --}}
                                    <svg class="w-3 h-3 mr-1 sm:w-4 sm:h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    Accommodation Needed: {{ $participant->accommodation_type }}
                                </span>
                            </div>
                        @endif

                        {{-- Disability Type Chips (if any) --}}
                        @if(!empty($participant->disability_type))
                            <div class="mb-3 sm:mb-4"> {{-- Responsive margin --}}
                                <p class="text-gray-700 text-sm font-medium mb-1.5 sm:mb-2">Disabilities:</p>
                                <div class="flex flex-wrap gap-1.5 sm:gap-2"> {{-- Responsive gap --}}
                                    @php
                                        // Ensure disability_type is treated as an array
                                        $disabilities = is_string($participant->disability_type)
                                                        ? (json_decode($participant->disability_type) ?? [])
                                                        : ($participant->disability_type ?? []);
                                    @endphp
                                    @forelse($disabilities as $disability)
                                        <span class="inline-flex items-center px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-medium bg-[#d0dbcc] text-[#3e4732] shadow-sm"> {{-- Responsive padding and text size --}}
                                            {{ $disability }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 text-xs sm:text-sm">None specified</span>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <div class="mt-auto flex flex-wrap gap-2 sm:gap-3 pt-4 sm:pt-5 border-t border-gray-200"> {{-- Responsive gap and padding --}}
                            <a href="{{ route('sc.participants.show', $participant) }}" class="flex-grow sm:flex-none inline-flex justify-center items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-[#33595a] text-white text-xs font-semibold rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                                View Profile
                            </a>
                            <a href="{{ route('sc.participants.edit', $participant) }}" class="flex-grow sm:flex-none inline-flex justify-center items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-[#cc8e45] text-white text-xs font-semibold rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                                Edit
                            </a>
                            <button
                                type="button"
                                class="flex-grow sm:flex-none inline-flex justify-center items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition-colors duration-200 shadow-sm"
                                x-data="{}"
                                x-on:click.prevent="
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'You will not be able to revert this!',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#cc8e45',
                                        cancelButtonColor: '#33595a',
                                        confirmButtonText: 'Yes, delete it!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.getElementById('delete-participant-{{ $participant->id }}').submit();
                                        }
                                    })
                                "
                            >
                                Delete
                            </button>
                            <form id="delete-participant-{{ $participant->id }}" action="{{ route('sc.participants.destroy', $participant) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 sm:mt-10"> {{-- Responsive margin top --}}
                {{ $participants->appends(request()->query())->links() }} {{-- Keep pagination filters --}}
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // This function will be called by Alpine.js
            function loadSuburbsForFilter(selectedState, initialSuburb = null) {
                const suburbSelect = document.getElementById('suburb');
                suburbSelect.innerHTML = '<option value="">Loading Suburbs...</option>';
                suburbSelect.disabled = true; // Disable until loaded

                if (selectedState) {
                    fetch(`/get-suburbs/${selectedState}`)
                        .then(response => {
                            if (!response.ok) {
                                // Improved error handling for fetch
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Message: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            let options = '<option value="">All Suburbs</option>';
                            if (data.length > 0) {
                                data.forEach(suburb => {
                                    options += `<option value="${suburb}" ${suburb === initialSuburb ? 'selected' : ''}>${suburb}</option>`;
                                });
                            } else {
                                options += '<option value="">No suburbs found</option>';
                            }
                            suburbSelect.innerHTML = options;
                            suburbSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching suburbs:', error);
                            suburbSelect.innerHTML = '<option value="">Error loading suburbs</option>';
                            suburbSelect.disabled = false;
                        });
                } else {
                    suburbSelect.innerHTML = '<option value="">All Suburbs</option>';
                    suburbSelect.disabled = true;
                }
            }
        </script>
    @endpush
@endsection