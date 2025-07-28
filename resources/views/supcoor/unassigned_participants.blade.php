@extends('supcoor.sc-db') {{-- Assuming you have a main layout --}}

@section('main-content')
<div class="container mx-auto px-4 py-8" x-data="participantModal()">
    <h1 class="text-4xl font-extrabold text-custom-dark-olive mb-8 text-center sm:text-left">Unassigned Participants</h1>

    <div class="bg-custom-white shadow-xl rounded-xl p-6 mb-10 border border-custom-light-grey-green">
        <h2 class="text-xl font-semibold text-custom-dark-teal mb-5">Filter Participants</h2>
        <form action="{{ route('sc.supcoor.unassigned_participants') }}" method="GET" class="space-y-6 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-end">
            <div>
                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                <select name="state" id="state" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre text-base p-3">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ (old('state', $filters['state'] ?? '') == $state) ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                <select name="suburb" id="suburb" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre text-base p-3">
                    <option value="">All Suburbs</option>
                    @foreach($suburbs as $suburb)
                        <option value="{{ $suburb }}" {{ (old('suburb', $filters['suburb'] ?? '') == $suburb) ? 'selected' : '' }}>
                            {{ $suburb }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="accommodation_type" class="block text-sm font-medium text-gray-700">Accommodation Type</label>
                <select name="accommodation_type" id="accommodation_type" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre text-base p-3">
                    <option value="">All Accommodation Types</option>
                    @foreach($accommodationTypes as $type)
                        <option value="{{ $type }}" {{ (old('accommodation_type', $filters['accommodation_type'] ?? '') == $type) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="disability_type" class="block text-sm font-medium text-gray-700">Disability Type</label>
                <select name="disability_type" id="disability_type" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre text-base p-3">
                    <option value="">All Disability Types</option>
                    @foreach($disabilityTypes as $type)
                        <option value="{{ $type }}" {{ (old('disability_type', $filters['disability_type'] ?? '') == $type) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-1">
                <label for="search" class="block text-sm font-medium text-gray-700">Search (Code Name, Disability)</label>
                <input type="text" name="search" id="search" placeholder="e.g. D001, Autism"
                       value="{{ old('search', $filters['search'] ?? '') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre text-base p-3">
            </div>

            <div class="flex flex-col sm:flex-row gap-4 md:col-span-2 lg:col-span-1 xl:col-span-1">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-custom-ochre text-custom-white font-semibold rounded-lg shadow-md hover:bg-custom-ochre-darker focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-offset-2 transition ease-in-out duration-150">
                    Apply Filters
                </button>
                <a href="{{ route('sc.supcoor.unassigned_participants') }}" class="w-full sm:w-auto px-6 py-3 bg-custom-light-grey-green text-custom-dark-olive rounded-lg hover:bg-custom-light-grey-brown focus:outline-none focus:ring-2 focus:ring-custom-light-grey-brown focus:ring-offset-2 transition ease-in-out duration-150 text-center shadow-md">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    @if($participants->isEmpty())
        <div class="bg-custom-white shadow-md rounded-lg p-8 text-center text-gray-500 border border-custom-light-grey-green">
            <p class="text-xl">No unassigned participants found matching your criteria.</p>
            <p class="mt-2 text-md">Try adjusting your filters or clearing them.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($participants as $participant)
                <div class="bg-custom-white shadow-xl rounded-lg p-6 flex flex-col items-center text-center border border-custom-light-grey-green transition transform hover:scale-105 hover:shadow-2xl duration-300 ease-in-out">
                    {{-- Profile Avatar --}}
                    <div class="mb-5">
                        @if($participant->profile_avatar_url)
                            <img src="{{ asset('storage/' . $participant->profile_avatar_url) }}" alt="{{ $participant->code_name }}" class="w-28 h-28 rounded-full object-cover border-4 border-custom-ochre shadow-lg">
                        @else
                            {{-- Default avatar using general.png --}}
                            <img src="{{ asset('images/general.png') }}" alt="Default Avatar" class="w-28 h-28 rounded-full object-cover border-4 border-custom-ochre shadow-lg">
                        @endif
                    </div>

                    {{-- Participant Code Name (always try to display this if available) --}}
                    @if($participant->code_name)
                        <div class="mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-custom-dark-teal text-custom-white shadow-md">
                                {{ $participant->code_name }}
                            </span>
                        </div>
                    @endif

                    <div class="text-gray-700 text-sm space-y-2 mb-6">
                        @if($participant->accommodation_needed)
                            <p class="text-base"><strong class="font-semibold text-custom-dark-teal">Accommodation Needed:</strong> {{ implode(', ', (array) $participant->accommodation_needed) }}</p>
                        @endif

                        {{-- Disability Type as Chips (handling comma-separated string) --}}
                        @if($participant->disability_type)
                            <p class="text-base flex flex-wrap justify-center items-center gap-2">
                                <strong class="font-semibold text-custom-dark-teal">Disability:</strong>
                                @php
                                    // If disability_type is a string, split it by comma and trim each part
                                    $disabilityTypes = is_string($participant->disability_type) ? array_map('trim', explode(',', $participant->disability_type)) : (array) $participant->disability_type;
                                @endphp
                                @foreach($disabilityTypes as $type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-custom-light-grey-green text-custom-dark-olive border border-custom-light-grey-brown shadow-sm">
                                        {{ $type }}
                                    </span>
                                @endforeach
                            </p>
                        @endif

                        @if($participant->age)
                            <p class="text-base"><strong class="font-semibold text-custom-dark-teal">Age:</strong> {{ $participant->age }}</p>
                        @endif

                        {{-- Location as a Chip --}}
                        @if($participant->suburb || $participant->state)
                            <p class="text-base flex flex-wrap justify-center items-center gap-2">
                                <strong class="font-semibold text-custom-dark-teal">Location:</strong>
                                @if($participant->suburb)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-custom-light-grey-green text-custom-dark-olive border border-custom-light-grey-brown shadow-sm">
                                        {{ $participant->suburb }}
                                    </span>
                                @endif
                                @if($participant->state)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-custom-light-grey-green text-custom-dark-olive border border-custom-light-grey-brown shadow-sm">
                                        {{ $participant->state }}
                                    </span>
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Message Button (Triggers Modal) --}}
                    <button @click="openModal({{ $participant->id }}, @js($participant->code_name))"
                            class="w-full mt-auto px-6 py-3 bg-custom-green text-custom-white font-semibold rounded-lg shadow-md hover:bg-custom-green-light focus:outline-none focus:ring-2 focus:ring-custom-green-light focus:ring-offset-2 transition ease-in-out duration-200 transform hover:scale-105">
                        Message Participant
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $participants->links() }} {{-- Pagination Links --}}
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

            {{-- This is to trick the browser into centering the modal contents. --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal panel --}}
            <div class="inline-block align-bottom bg-custom-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-custom-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-custom-light-grey-green sm:mx-0 sm:h-10 sm:w-10">
                            {{-- Icon --}}
                            <svg class="h-6 w-6 text-custom-dark-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-custom-dark-olive" id="modal-headline">
                                Send Message to <span class="font-bold text-custom-ochre" x-text="participantCodeName"></span>
                            </h3>
                            <div class="mt-4">
                                <form @submit.prevent="sendMessage">
                                    <div class="mb-4">
                                        <label for="message_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                        <input type="text" x-model="messageSubject" id="message_subject" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre sm:text-sm p-2.5">
                                    </div>
                                    <div class="mb-4">
                                        <label for="message_body" class="block text-sm font-medium text-gray-700">Message</label>
                                        <textarea x-model="messageBody" id="message_body" rows="5" required
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-custom-ochre focus:ring-custom-ochre sm:text-sm p-2.5"></textarea>
                                    </div>
                                    <template x-if="errorMessage">
                                        <p class="text-red-600 text-sm mb-4 font-medium" x-text="errorMessage"></p>
                                    </template>
                                    <template x-if="successMessage">
                                        <p class="text-custom-green text-sm mb-4 font-medium" x-text="successMessage"></p>
                                    </template>

                                    <div class="bg-custom-light-cream px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg -mx-4 -mb-4 sm:-mx-6 sm:-mb-4">
                                        <button type="submit"
                                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-custom-ochre text-base font-medium text-custom-white hover:bg-custom-ochre-darker focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition ease-in-out duration-150">
                                            Send Message
                                        </button>
                                        <button type="button" @click="closeModal()"
                                                class="mt-3 w-full inline-flex justify-center rounded-md border border-custom-light-grey-brown shadow-sm px-4 py-2 bg-custom-white text-base font-medium text-custom-dark-olive hover:bg-custom-light-grey-green focus:outline-none focus:ring-2 focus:ring-custom-light-grey-brown focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition ease-in-out duration-150">
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
            participantCodeName: '',
            messageSubject: '',
            messageBody: '',
            errorMessage: '',
            successMessage: '',

            openModal(id, name) {
                this.participantId = id;
                this.participantCodeName = name;
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
</script>
{{-- Ensure Alpine.js is included, usually in your main layout --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush