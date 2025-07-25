{{-- resources/views/profile/complete-participant-profile.blade.php --}}
@extends('indiv.indiv-db')

@section('main-content')
    <div class="max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-lg mt-8 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 text-center">Complete Participant Profile 📝</h2>
        <p class="text-gray-700 mb-8 text-center leading-relaxed">
            @if ($user->is_representative)
                Please provide the details for the individual you are representing.
            @else
                Please provide your additional details.
            @endif
            This information helps us connect you with the right opportunities.
        </p>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                <strong class="font-bold">Oops! There were some errors.</strong>
                <span class="block sm:inline mt-1 sm:mt-0">Please correct the following:</span>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.complete.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- SECTION: Participant's Full Name --}}
            <h3 class="text-xl font-semibold text-gray-800 pb-2">Participant's Full Name 👤</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="participant_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="participant_first_name" id="participant_first_name"
                           value="{{ old('participant_first_name', $participant->first_name ?? '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="Participant's first name">
                    @error('participant_first_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="participant_middle_name" id="participant_middle_name"
                           value="{{ old('participant_middle_name', $participant->middle_name ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="Participant's middle name (optional)">
                    @error('participant_middle_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="participant_last_name" id="participant_last_name"
                           value="{{ old('participant_last_name', $participant->last_name ?? '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="Participant's last name">
                    @error('participant_last_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- All other participant details fields --}}
            <div class="mb-4">
                <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">Birthday <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="birthday" id="birthday"
                           value="{{ old('birthday', ($participant && $participant->birthday) ? $participant->birthday->format('Y-m-d') : '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 pr-10 flatpickr-input"
                           placeholder="Select date of birth">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @error('birthday')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            {{-- End of Birthday Datepicker --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="disability_type" class="block text-sm font-medium text-gray-700 mb-1">Disability Type(s)</label>
                    <select name="disability_type[]" id="disability_type" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select">
                        @php
                            $disabilityOptions = [
                                'Intellectual disability',
                                'Autism Spectrum Disorder',
                                'Cerebral palsy',
                                'Genetic/chromosomal syndromes (Angelman, Rett, etc.)',
                                'Spinal cord/brain injuries (paraplegia, quadriplegia, tetraplegia, hemiplegia)',
                                'Permanent blindness',
                                'Hearing impairment',
                                'Deaf-blindness',
                                'Physical disabilities (MS, muscular dystrophy, etc.)',
                                'Neurological conditions (stroke, brain injury)',
                                'Psychosocial (mental health) disabilities',
                                'Developmental delays (children)',
                                'Communication disorders',
                                'Specific learning disorders & ADHD',
                                'Other sensory disabilities',
                            ];

                            // Get previously selected values from old input or participant data
                            $currentDisabilities = ($participant && $participant->disability_type) ? (is_string($participant->disability_type) ? json_decode($participant->disability_type, true) : $participant->disability_type) : [];
                            $selectedDisabilities = old('disability_type', $currentDisabilities);
                            $selectedDisabilities = is_array($selectedDisabilities) ? $selectedDisabilities : [];
                        @endphp
                        @foreach($disabilityOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedDisabilities) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('disability_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="accommodation_type" class="block text-sm font-medium text-gray-700 mb-1">Accommodation Type</label>
                    <select name="accommodation_type" id="accommodation_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                        <option value="" selected disabled>Select Accommodation Type</option>
                        @foreach(['Specialist Disability Accommodation (SDA)', 'Supported Independent Living (SIL)', 'Community Participation'] as $accommType)
                            <option value="{{ $accommType }}" {{ old('accommodation_type', $participant->accommodation_type ?? '') == $accommType ? 'selected' : '' }}>
                                {{ $accommType }}
                            </option>
                        @endforeach
                    </select>
                    @error('accommodation_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="specific_disability" class="block text-sm font-medium text-gray-700 mb-1">Specific Disability Details</label>
                <textarea name="specific_disability" id="specific_disability" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                          placeholder="Provide specific details about the disability and any support needs.">{{ old('specific_disability', $participant->specific_disability ?? '') }}</textarea>
                @error('specific_disability')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <h3 class="text-xl font-semibold text-gray-800 pt-4 pb-2 border-t mt-6">Address Details 🏠</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="street_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $participant->street_address ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="Street number and name">
                    @error('street_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Swapped Suburb and State, now State comes first --}}
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select name="state" id="state"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                        <option value="" selected disabled>Select State</option>
                        @foreach(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'] as $stateAbbr)
                            <option value="{{ $stateAbbr }}" {{ old('state', $participant->state ?? '') == $stateAbbr ? 'selected' : '' }}>
                                {{ $stateAbbr }}
                            </option>
                        @endforeach
                    </select>
                    @error('state')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="suburb" class="block text-sm font-medium text-gray-700 mb-1">Suburb</label>
                    <select name="suburb" id="suburb"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                        <option value="" selected disabled>Select Suburb</option>
                        {{-- Suburbs will be populated via JavaScript --}}
                        @if (old('suburb', $participant->suburb ?? ''))
                            {{-- If there's an old value or existing participant suburb, display it --}}
                            <option value="{{ old('suburb', $participant->suburb) }}" selected>
                                {{ old('suburb', $participant->suburb) }}
                            </option>
                        @endif
                    </select>
                    @error('suburb')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="post_code" class="block text-sm font-medium text-gray-700 mb-1">Post Code</label>
                    <input type="text" name="post_code" id="post_code" value="{{ old('post_code', $participant->post_code ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="e.g., 1234">
                    @error('post_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4 border-t mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_looking_hm" id="is_looking_hm" value="1" {{ old('is_looking_hm', $participant->is_looking_hm ?? false) ? 'checked' : '' }}
                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="is_looking_hm" class="ml-2 block text-sm font-medium text-gray-700 select-none">Is looking for a housemate?</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="has_accommodation" id="has_accommodation" value="1" {{ old('has_accommodation', $participant->has_accommodation ?? false) ? 'checked' : '' }}
                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="has_accommodation" class="ml-2 block text-sm font-medium text-gray-700 select-none">Has existing accommodation?</label>
                </div>
            </div>

            <div class="mt-6">
                <label for="relative_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact / Relative Name</label>
                <input type="text" name="relative_name" id="relative_name"
                       value="{{ old('relative_name', $participant->relative_name ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                       placeholder="Name of emergency contact"
                       {{ $user->is_representative ? 'readonly' : '' }}>
                @if ($user->is_representative)
                    <p class="mt-2 text-sm text-gray-600">This field is automatically filled with your name as you are completing the profile on behalf of someone else.</p>
                @endif
                @error('relative_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="relationship_to_participant" class="block text-sm font-medium text-gray-700 mb-1">Relationship to Participant</label>
                    <input type="text" name="relationship_to_participant" id="relationship_to_participant"
                           value="{{ old('relationship_to_participant', $participant->relative_relationship ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="e.g., Parent, Guardian, Sibling"
                           {{ $user->is_representative ? 'readonly' : '' }}>
                    @error('relationship_to_participant')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="relative_phone" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact / Relative Phone</label>
                    <input type="text" name="relative_phone" id="relative_phone"
                           value="{{ old('relative_phone', $participant->relative_phone ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="Emergency contact phone"
                           {{ $user->is_representative ? 'readonly' : '' }}>
                    @error('relative_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="relative_email" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact / Relative Email</label>
                <input type="email" name="relative_email" id="relative_email"
                       value="{{ old('relative_email', $participant->relative_email ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                       placeholder="Emergency contact email"
                       {{ $user->is_representative ? 'readonly' : '' }}>
                @error('relative_email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Participant Code Name (Display Only) --}}
            <div class="mt-6" hidden>
                <label class="block text-sm font-medium text-gray-700 mb-1">Participant Code Name</label>
                <div class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 p-2.5 text-gray-900 sm:text-base">
                    {{ $user->participant_code_name ?? 'Not Available Yet' }}
                </div>
                <p class="mt-2 text-sm text-gray-600">This is your unique participant code name, automatically generated by the system.</p>
            </div>


            <div class="pt-6">
                <button type="submit"
                        class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    {{-- Choices.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatpickr initialization
            flatpickr(".flatpickr-input", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)), // Ensures 18+ years old
            });

            // Choices.js Initialization for Disability Type
            const disabilitySelect = document.getElementById('disability_type');
            if (disabilitySelect) {
                new Choices(disabilitySelect, {
                    removeItemButton: true, // Allows removal of selected items
                    placeholder: true,
                    placeholderValue: 'Select one or more disability types',
                    searchEnabled: true, // Enable search functionality
                    itemSelectText: 'Press to select', // Text shown on hover
                    noResultsText: 'No results found',
                    noChoicesText: 'No more options to choose',
                });
            }

            const stateSelect = document.getElementById('state');
            const suburbSelect = document.getElementById('suburb');
            // Store the initial value if any (for existing data or old input after validation failure)
            const initialSuburbValue = "{{ old('suburb', $participant->suburb ?? '') }}";

            // Function to load suburbs
            function loadSuburbs(state, selectedSuburb = null) {
                // Clear existing options, but keep "Select Suburb"
                suburbSelect.innerHTML = '<option value="">Select Suburb</option>';
                suburbSelect.disabled = true; // Disable until loaded

                if (!state) {
                    return; // No state selected, do nothing
                }

                fetch(`/get-suburbs/${state}`) // Use your actual route
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(suburbs => {
                        suburbs.forEach(suburb => {
                            const option = document.createElement('option');
                            option.value = suburb;
                            option.textContent = suburb;
                            if (selectedSuburb && suburb === selectedSuburb) {
                                option.selected = true;
                            }
                            suburbSelect.appendChild(option);
                        });
                        suburbSelect.disabled = false; // Re-enable after loading
                    })
                    .catch(error => {
                        console.error('Error fetching suburbs:', error);
                        suburbSelect.disabled = false; // Ensure it's re-enabled even on error
                        alert('Could not load suburbs for the selected state. Please try again.');
                    });
            }

            // Event listener for state change
            stateSelect.addEventListener('change', function() {
                loadSuburbs(this.value); // Load suburbs for the newly selected state
            });

            // Initial load for suburbs if a state is already selected (e.g., from old() or existing data)
            if (stateSelect.value) {
                loadSuburbs(stateSelect.value, initialSuburbValue);
            }
        });
    </script>
    @endpush
@endsection