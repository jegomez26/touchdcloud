@extends('supcoor.sc-db') {{-- Extend your sc-db layout --}}

@section('main-content') {{-- Start the main-content section --}}
    <h2 class="font-semibold text-2xl md:text-3xl text-[#33595a] leading-tight mb-6 md:mb-8">
        {{ __('Unassigned Participants') }}
    </h2>

    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8" x-data="participantModal()"> {{-- Responsive padding: smaller on mobile, larger on desktop --}}
        {{-- Filter Participants Section (using the new design) --}}
        <div class="mb-6 md:mb-8 bg-gray-50 p-4 sm:p-6 rounded-lg shadow-inner border border-gray-100"
             x-data="{
                 openFilters: false,
                 selectedAccommodationType: {{ json_encode(request('accommodation_type', '')) }},
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
            <form action="{{ route('sc.supcoor.unassigned_participants') }}" method="GET" class="space-y-4 sm:space-y-5">
                <div class="flex flex-col md:flex-row items-end gap-3 md:gap-4">
                    <div class="flex-grow w-full">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Participants</label>
                        <input type="text" name="search" id="search" placeholder="Disability, location..."
                               value="{{ request('search') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 placeholder-gray-400 p-2.5 text-base">
                    </div>
                    <div class="w-full md:w-auto">
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
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 border-t border-gray-200 pt-5 mt-5">

                    {{-- Accommodation Type Filter --}}
                    <div>
                        <label for="accommodation_type" class="block text-sm font-medium text-gray-700 mb-1">Accommodation Type</label>
                        <select id="accommodation_type" name="accommodation_type"
                                x-model="selectedAccommodationType"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] bg-white text-gray-800 p-2.5 text-base">
                            <option value="">All Accommodation Types</option>
                            @foreach($accommodationTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

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
                            @if(request('state') && !empty($suburbsForFilter))
                                @foreach($suburbsForFilter as $suburb)
                                    <option value="{{ $suburb }}" {{ request('suburb') == $suburb ? 'selected' : '' }}>{{ $suburb }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-span-full flex flex-col sm:flex-row justify-end gap-3 pt-2 sm:pt-0">
                        <a href="{{ route('sc.supcoor.unassigned_participants') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-wider hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            Clear Filters
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-[#cc8e45] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        {{-- End Filter Participants Section --}}

        @if ($participants->isEmpty())
            <div class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg border border-gray-200 flex flex-col items-center justify-center p-4 sm:p-6">
                <img src="{{ asset('images/empty-participant.svg') }}" alt="No participants illustration" class="mx-auto max-w-xs h-32 sm:h-48 mb-4 sm:mb-6 opacity-80">
                <p class="text-lg sm:text-xl text-gray-600 mb-2 sm:mb-4 font-medium">No unassigned participants found matching your criteria.</p>
                <p class="text-gray-500 text-sm sm:text-base max-w-lg">Try adjusting your search terms or clearing them.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($participants as $participant)
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-100 flex items-start text-left transform transition-transform duration-200 hover:scale-[1.02] hover:shadow-lg">
                        {{-- Profile Avatar on the left --}}
                        <div class="flex-shrink-0 mr-4 mt-1"> {{-- Added mt-1 to slightly align with text --}}
                            @if($participant->profile_avatar_url)
                                <img src="{{ asset('storage/' . $participant->profile_avatar_url) }}" alt="Participant Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-3 border-[#cc8e45] shadow-md">
                            @else
                                @php
                                    $avatarPath = 'images/general.png'; // Default
                                    $randomMale = rand(1, 2);
                                    $randomFemale = rand(1, 2);
                                    if ($participant->gender === 'Male') {
                                        $avatarPath = 'images/male' . $randomMale . '.png';
                                    } elseif ($participant->gender === 'Female') {
                                        $avatarPath = 'images/female' . $randomFemale . '.png';
                                    }
                                @endphp
                                <img src="{{ asset($avatarPath) }}" alt="{{ $participant->gender ?? 'General' }} Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-3 border-[#cc8e45] shadow-md">
                            @endif
                        </div>

                        <div class="flex-grow flex flex-col justify-start">
                            {{-- Participant Code Name as header --}}
                            <h3 class="text-xl font-bold text-[#33595a] mb-1 leading-tight">
                                {{ $participant->participant_code_name ?? 'N/A' }}
                            </h3>

                            {{-- Age --}}
                            <p class="text-gray-700 text-sm mb-1">
                                <strong class="font-semibold">Age:</strong> {{ $participant->age ? $participant->age . ' years old' : 'N/A' }}
                            </p>

                            {{-- Location --}}
                            <p class="text-gray-700 text-sm mb-2">
                                <strong class="font-semibold">Location:</strong>
                                @if($participant->suburb && $participant->state)
                                    {{ $participant->suburb }}, {{ $participant->state }}
                                @elseif($participant->suburb)
                                    {{ $participant->suburb }}
                                @elseif($participant->state)
                                    {{ $participant->state }}
                                @else
                                    N/A
                                @endif
                            </p>

                            {{-- Looking for Housemate --}}
                            @if($participant->is_looking_hm)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#cc8e45] text-white mb-2 shadow-sm">
                                    Looking for Housemate
                                </span>
                            @endif

                            {{-- Accommodation Type Chips (Assumed 'accommodation_type' from model is what they are looking for) --}}
                            @if($participant->accommodation_type)
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <strong class="font-semibold text-[#33595a] text-sm">Accommodation Needed:</strong>
                                    @php
                                        // If accommodation_type is a single string but you want it as a chip
                                        $accommodationDisplay = $participant->accommodation_type;
                                        // If it's a JSON array and cast to array in model:
                                        // $accommodationTypes = is_array($participant->accommodation_type) ? $participant->accommodation_type : [$participant->accommodation_type];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#d0dbcc] text-[#3e4732] border border-[#d0dbcc] shadow-sm">
                                        {{ $accommodationDisplay }}
                                    </span>
                                </div>
                            @endif


                            {{-- Disability Type Chips --}}
                            @if($participant->disability_type && is_array($participant->disability_type) && count($participant->disability_type) > 0)
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <strong class="font-semibold text-[#33595a] text-sm">Disability:</strong>
                                    @foreach($participant->disability_type as $type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#d0dbcc] text-[#3e4732] border border-[#d0dbcc] shadow-sm">
                                            {{ $type }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @if($participant->specific_disability)
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <strong class="font-semibold text-[#33595a] text-sm">Specific Disability:</strong>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#d0dbcc] text-[#3e4732] border border-[#d0dbcc] shadow-sm">
                                        {{ $participant->specific_disability }}
                                    </span>
                                </div>
                            @endif

                            {{-- Message Button (Triggers Modal) --}}
                            <button @click="openModal({{ $participant->id }}, @js($participant->participant_code_name ?? 'Participant'))"
                                    class="w-full mt-auto px-6 py-3 bg-[#33595a] text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#33595a] focus:ring-offset-2 transition ease-in-out duration-200 transform hover:scale-105">
                                Message Participant
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 sm:mt-10">
                {{ $participants->appends(request()->query())->links() }} {{-- Pagination Links --}}
            </div>
        @endif

        {{-- Message Modal (within the main x-data scope) --}}
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal()">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                     role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#d0dbcc] sm:mx-0 sm:h-10 sm:w-10">
                                {{-- Icon --}}
                                <svg class="h-6 w-6 text-[#33595a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-[#33595a]" id="modal-headline">
                                    Send Message to <span class="font-bold text-[#cc8e45]" x-text="participantCodeName"></span>
                                </h3>
                                <div class="mt-4">
                                    <form @submit.prevent="sendMessage">
                                        <div class="mb-4">
                                            <label for="message_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                            <input type="text" x-model="messageSubject" id="message_subject" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-sm p-2.5">
                                        </div>
                                        <div class="mb-4">
                                            <label for="message_body" class="block text-sm font-medium text-gray-700">Message</label>
                                            <textarea x-model="messageBody" id="message_body" rows="5" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-sm p-2.5"></textarea>
                                        </div>
                                        <template x-if="errorMessage">
                                            <p class="text-red-600 text-sm mb-4 font-medium" x-text="errorMessage"></p>
                                        </template>
                                        <template x-if="successMessage">
                                            <p class="text-[#33595a] text-sm mb-4 font-medium" x-text="successMessage"></p>
                                        </template>

                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg -mx-4 -mb-4 sm:-mx-6 sm:-mb-4">
                                            <button type="submit"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#cc8e45] text-base font-medium text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition ease-in-out duration-150">
                                                Send Message
                                            </button>
                                            <button type="button" @click="closeModal()"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition ease-in-out duration-150">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function participantModal() {
        return {
            open: false,
            participantId: null,
            participantCodeName: '', // This will hold the "Participant" generic name
            messageSubject: '',
            messageBody: '',
            errorMessage: '',
            successMessage: '',

            openModal(id, name) {
                this.participantId = id;
                this.participantCodeName = name; // Will be 'Participant' as passed
                this.messageSubject = '';
                this.messageBody = '';
                this.errorMessage = '';
                this.successMessage = '';
                this.open = true;
            },
            closeModal() {
                this.open = false;
                this.participantId = null;
                this.participantCodeName = '';
                this.messageSubject = '';
                this.messageBody = '';
                this.errorMessage = '';
                this.successMessage = '';
            },
            sendMessage() {
                this.errorMessage = '';
                this.successMessage = '';

                fetch('/coordinator/send-message/' + this.participantId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message_subject: this.messageSubject,
                        message_body: this.messageBody
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    this.successMessage = data.message || 'Message sent successfully!';
                    this.messageSubject = '';
                    this.messageBody = '';
                    setTimeout(() => {
                        this.closeModal();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    this.errorMessage = error.message || 'Failed to send message. Please try again.';
                    if (error.errors) {
                        let errors = Object.values(error.errors).flat();
                        this.errorMessage = errors.join('\n');
                    }
                });
            }
        };
    }

    // This function will be called by Alpine.js for filtering suburbs
    function loadSuburbsForFilter(selectedState, initialSuburb = null) {
        const suburbSelect = document.getElementById('suburb');
        suburbSelect.innerHTML = '<option value="">Loading Suburbs...</option>';
        suburbSelect.disabled = true;

        if (selectedState) {
            fetch(`/get-suburbs/${selectedState}`)
                .then(response => {
                    if (!response.ok) {
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
{{-- Ensure Alpine.js is included, usually in your main layout --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush