@extends('company.participants.create')

@section('page_title', 'Living Preferences')
@section('page_description', 'Details regarding living preferences for the participant.')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endpush

@section('profile_content')
<div class="p-6 bg-white rounded-lg shadow-md max-w-4xl mx-auto">
    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    @if ($errors->any() && !session('status'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif
    <form action="{{ route('provider.participants.profile.living-preferences.update', $participant->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred SIL Location(s):</label>
                <div id="sil_locations_container" class="space-y-4">
                    @php
                        $currentPreferredSilLocations = old('preferred_sil_locations', $participant->preferred_sil_locations ?? []);
                        if (is_string($currentPreferredSilLocations)) {
                            $currentPreferredSilLocations = json_decode($currentPreferredSilLocations, true) ?? [];
                        }
                        // Ensure it's an array of objects/arrays with 'state' and 'suburb' keys
                        $currentPreferredSilLocations = collect($currentPreferredSilLocations)->map(function($location) {
                            if (is_string($location) && str_contains($location, ' ')) {
                                $parts = explode(' ', $location);
                                $state = array_pop($parts); // Last part is state
                                $suburb = implode(' ', $parts); // Remaining parts are suburb
                                return ['state' => $state, 'suburb' => $suburb];
                            }
                            // Ensure $location is an array and has expected keys, default to empty string if not
                            return [
                                'state' => $location['state'] ?? '',
                                'suburb' => $location['suburb'] ?? ''
                            ];
                        })->all();

                        // If no locations saved, add one empty entry for initial display
                        if (empty($currentPreferredSilLocations)) {
                            $currentPreferredSilLocations = [['state' => '', 'suburb' => '']];
                        }

                        $states = ['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA']; // Define Australian states
                    @endphp

                    @foreach ($currentPreferredSilLocations as $index => $location)
                        <div class="flex flex-col md:flex-row gap-4 preferred-location-row items-end" data-index="{{ $index }}">
                            <div class="flex-1">
                                <label for="state_{{ $index }}" class="block text-sm font-medium text-gray-700">State</label>
                                <select name="preferred_sil_locations[{{ $index }}][state]" id="state_{{ $index }}"
                                    class="state-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state }}" {{ (old("preferred_sil_locations.$index.state", $location['state'] ?? '') == $state) ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                                @error("preferred_sil_locations.$index.state")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex-1">
                                <label for="suburb_{{ $index }}" class="block text-sm font-medium text-gray-700">Suburb</label>
                                <input type="text" name="preferred_sil_locations[{{ $index }}][suburb]" id="suburb_{{ $index }}" 
                                       value="{{ old("preferred_sil_locations.$index.suburb", $location['suburb'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Enter suburb name">
                                @error("preferred_sil_locations.$index.suburb")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @if ($index > 0 || count($currentPreferredSilLocations) > 1) {{-- Show remove button if not the first row or if there are multiple rows --}}
                                <button type="button" class="remove-location-btn inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Remove
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add_location_btn" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Add Another Location
                </button>
                @error('preferred_sil_locations')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Housemate preferences:</label>
                @php
                    $housemateOptions = [
                        'Male housemates',
                        'Female housemates',
                        'Mixed gender',
                        'Similar age group',
                        'Cultural/religious compatibility',
                        'No strong preference',
                        'Other'
                    ];
                    $currentHousematePreferences = old('housemate_preferences', $participant->housemate_preferences ?? []);
                    if (is_string($currentHousematePreferences)) {
                        $currentHousematePreferences = json_decode($currentHousematePreferences, true) ?? [];
                    }
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="housemate_preferences_checkboxes_container"> {{-- Changed ID for clarity --}}
                    @foreach ($housemateOptions as $option)
                        <div class="flex items-center">
                            <input type="checkbox" name="housemate_preferences[]" value="{{ $option }}" id="housemate_preference_{{ Str::slug($option) }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                {{ in_array($option, $currentHousematePreferences) ? 'checked' : '' }}>
                            <label for="housemate_preference_{{ Str::slug($option) }}" class="ml-2 text-sm text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('housemate_preferences')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="housemate_preferences_other_container" class="mt-4 {{ (!in_array('Other', $currentHousematePreferences)) ? 'hidden' : '' }}">
                <label for="housemate_preferences_other" class="block text-sm font-medium text-gray-700">Other Housemate Preferences (please specify):</label>
                <textarea name="housemate_preferences_other" id="housemate_preferences_other" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Prefers quiet housemates">{{ old('housemate_preferences_other', $participant->housemate_preferences_other ?? '') }}</textarea>
                @error('housemate_preferences_other')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="preferred_number_of_housemates" class="block text-sm font-medium text-gray-700">Preferred number of housemates:</label>
                <select name="preferred_number_of_housemates" id="preferred_number_of_housemates" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled selected>Select an option</option>
                    @php
                        $numHousematesOptions = ['1', '2', '3+', 'No preference'];
                    @endphp
                    @foreach ($numHousematesOptions as $option)
                        <option value="{{ $option }}" {{ old('preferred_number_of_housemates', $participant->preferred_number_of_housemates ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('preferred_number_of_housemates')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="accessibility_needs_in_home" class="block text-sm font-medium text-gray-700">Accessibility needs in the home:</label>
                <select name="accessibility_needs_in_home" id="accessibility_needs_in_home" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled selected>Select an option</option>
                    @php
                        $accessibilityOptions = ['Fully accessible', 'Some modifications required', 'No specific needs'];
                    @endphp
                    @foreach ($accessibilityOptions as $option)
                        <option value="{{ $option }}" {{ old('accessibility_needs_in_home', $participant->accessibility_needs_in_home ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('accessibility_needs_in_home')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="accessibility_needs_details_container" class="mt-4 {{ (old('accessibility_needs_in_home', $participant->accessibility_needs_in_home ?? '') === 'No specific needs' || old('accessibility_needs_in_home', $participant->accessibility_needs_in_home ?? '') === '') ? 'hidden' : '' }}">
                <label for="accessibility_needs_details" class="block text-sm font-medium text-gray-700">Details (e.g., wheelchair ramps, wider doorways, grab bars):</label>
                <textarea name="accessibility_needs_details" id="accessibility_needs_details" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Provide details about required modifications or features.">{{ old('accessibility_needs_details', $participant->accessibility_needs_details ?? '') }}</textarea>
                @error('accessibility_needs_details')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pets_in_home_preference" class="block text-sm font-medium text-gray-700">Pets in the home:</label>
                <select name="pets_in_home_preference" id="pets_in_home_preference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled selected>Select an option</option>
                    @php
                        $petsPreferenceOptions = ['Have pets', 'Can live with pets', 'Do not want to live with pets'];
                    @endphp
                    @foreach ($petsPreferenceOptions as $option)
                        <option value="{{ $option }}" {{ old('pets_in_home_preference', $participant->pets_in_home_preference ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('pets_in_home_preference')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="own_pet_type_container" class="mt-4 {{ (old('pets_in_home_preference', $participant->pets_in_home_preference ?? '') !== 'Have pets') ? 'hidden' : '' }}">
                <label for="own_pet_type" class="block text-sm font-medium text-gray-700">Type of pet you own:</label>
                <input type="text" name="own_pet_type" id="own_pet_type"
                    value="{{ old('own_pet_type', $participant->own_pet_type ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Dog (Labrador), Cat, Bird">
                @error('own_pet_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">What does a good home environment look like for you?</label>
                @php
                    $homeEnvironmentOptions = [
                        'Quiet and low-stimulus',
                        'Social and interactive',
                        'Structured and routine-based',
                        'Independent and private',
                        'Other'
                    ];
                    $currentHomeEnvironment = old('good_home_environment_looks_like', $participant->good_home_environment_looks_like ?? []);
                    if (is_string($currentHomeEnvironment)) {
                        $currentHomeEnvironment = json_decode($currentHomeEnvironment, true) ?? [];
                    }
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="good_home_environment_checkboxes_container"> {{-- Changed ID for clarity --}}
                    @foreach ($homeEnvironmentOptions as $option)
                        <div class="flex items-center">
                            <input type="checkbox" name="good_home_environment_looks_like[]" value="{{ $option }}" id="home_environment_{{ Str::slug($option) }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                {{ in_array($option, $currentHomeEnvironment) ? 'checked' : '' }}>
                            <label for="home_environment_{{ Str::slug($option) }}" class="ml-2 text-sm text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('good_home_environment_looks_like')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="good_home_environment_looks_like_other_container" class="mt-4 {{ (!in_array('Other', $currentHomeEnvironment)) ? 'hidden' : '' }}">
                <label for="good_home_environment_looks_like_other" class="block text-sm font-medium text-gray-700">Other Good Home Environment (please specify):</label>
                <textarea name="good_home_environment_looks_like_other" id="good_home_environment_looks_like_other" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Needs plenty of natural light, close to public transport">{{ old('good_home_environment_looks_like_other', $participant->good_home_environment_looks_like_other ?? '') }}</textarea>
                @error('good_home_environment_looks_like_other')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-500">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize locationIndex based on the count of existing preferred locations.
        // If there are no existing locations, it starts at -1 so the first added row is index 0.
        let locationIndex = {{ count($currentPreferredSilLocations) > 0 ? count($currentPreferredSilLocations) - 1 : -1 }};
        const silLocationsContainer = document.getElementById('sil_locations_container');
        const addLocationBtn = document.getElementById('add_location_btn');

        // Suburbs are now free text fields, no JavaScript needed for suburb handling

        // Add new location row
        addLocationBtn.addEventListener('click', function () {
            locationIndex++;
            const newRow = document.createElement('div');
            newRow.classList.add('flex', 'flex-col', 'md:flex-row', 'gap-4', 'preferred-location-row', 'items-end');
            newRow.setAttribute('data-index', locationIndex);
            newRow.innerHTML = `
                <div class="flex-1">
                    <label for="state_${locationIndex}" class="block text-sm font-medium text-gray-700">State</label>
                    <select name="preferred_sil_locations[${locationIndex}][state]" id="state_${locationIndex}"
                        class="state-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select State</option>
                        @foreach ($states as $state)
                            <option value="{{ $state }}">{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="suburb_${locationIndex}" class="block text-sm font-medium text-gray-700">Suburb</label>
                    <input type="text" name="preferred_sil_locations[${locationIndex}][suburb]" id="suburb_${locationIndex}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Enter suburb name">
                </div>
                <button type="button" class="remove-location-btn inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Remove
                </button>
            `;
            silLocationsContainer.appendChild(newRow);

            // No suburb handling needed since it's now a free text field
        });

        // Remove location row
        silLocationsContainer.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-location-btn')) {
                const rowToRemove = event.target.closest('.preferred-location-row');
                // No suburb handling needed since it's now a free text field
                rowToRemove.remove();
                // Re-index remaining rows if necessary (important for form submission)
                document.querySelectorAll('.preferred-location-row').forEach((row, i) => {
                    row.setAttribute('data-index', i);
                    row.querySelectorAll('[name^="preferred_sil_locations"]').forEach(input => {
                        const name = input.getAttribute('name');
                        // Use a regular expression to correctly replace the index in the name
                        input.setAttribute('name', name.replace(/\[\d+\]/, `[${i}]`));
                        input.setAttribute('id', input.id.replace(/\d+/, i));
                    });
                    row.querySelectorAll('label').forEach(label => {
                        const htmlFor = label.getAttribute('for');
                        label.setAttribute('for', htmlFor.replace(/\d+/, i));
                    });
                });
                locationIndex = document.querySelectorAll('.preferred-location-row').length - 1;
            }
        });

        // Housemate preferences "Other" checkbox
        const housematePreferencesCheckboxes = document.querySelectorAll('#housemate_preferences_checkboxes_container input[type="checkbox"]');
        const housematePreferencesOtherContainer = document.getElementById('housemate_preferences_other_container');
        const housematePreferencesOtherTextarea = document.getElementById('housemate_preferences_other');

        housematePreferencesCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.value === 'Other') {
                    if (this.checked) {
                        housematePreferencesOtherContainer.classList.remove('hidden');
                    } else {
                        housematePreferencesOtherContainer.classList.add('hidden');
                        housematePreferencesOtherTextarea.value = ''; // Clear textarea if "Other" is unchecked
                    }
                }
            });
        });

        // Accessibility needs in the home dropdown
        const accessibilityNeedsSelect = document.getElementById('accessibility_needs_in_home');
        const accessibilityNeedsDetailsContainer = document.getElementById('accessibility_needs_details_container');
        const accessibilityNeedsDetailsTextarea = document.getElementById('accessibility_needs_details');

        accessibilityNeedsSelect.addEventListener('change', function() {
            if (this.value === 'No specific needs' || this.value === '') {
                accessibilityNeedsDetailsContainer.classList.add('hidden');
                accessibilityNeedsDetailsTextarea.value = ''; // Clear textarea if "No specific needs" or no selection
            } else {
                accessibilityNeedsDetailsContainer.classList.remove('hidden');
            }
        });

        // Pets in the home preference dropdown
        const petsInHomePreferenceSelect = document.getElementById('pets_in_home_preference');
        const ownPetTypeContainer = document.getElementById('own_pet_type_container');
        const ownPetTypeInput = document.getElementById('own_pet_type');

        petsInHomePreferenceSelect.addEventListener('change', function() {
            if (this.value === 'Have pets') {
                ownPetTypeContainer.classList.remove('hidden');
            } else {
                ownPetTypeContainer.classList.add('hidden');
                ownPetTypeInput.value = ''; // Clear input if not "Have pets"
            }
        });

        // Good home environment "Other" checkbox
        const goodHomeEnvironmentCheckboxes = document.querySelectorAll('#good_home_environment_checkboxes_container input[type="checkbox"]');
        const goodHomeEnvironmentOtherContainer = document.getElementById('good_home_environment_looks_like_other_container');
        const goodHomeEnvironmentOtherTextarea = document.getElementById('good_home_environment_looks_like_other');

        goodHomeEnvironmentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.value === 'Other') {
                    if (this.checked) {
                        goodHomeEnvironmentOtherContainer.classList.remove('hidden');
                    } else {
                        goodHomeEnvironmentOtherContainer.classList.add('hidden');
                        goodHomeEnvironmentOtherTextarea.value = ''; // Clear textarea if "Other" is unchecked
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection