@extends('company.provider-db') 


@section('main-content') {{-- Start the main-content section --}}
    {{-- The new div to control form width --}}
    <div class="flex justify-center"> {{-- Centers the content horizontally --}}
        <div class="w-full lg:w-3/4 xl:w-2/3 2xl:w-1/2"> {{-- Sets max width for the form container --}}
            <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="font-semibold text-xl text-[#33595a] leading-tight mb-6">
                    {{ __('Edit Participant: ') }} {{ $participant->first_name }} {{ $participant->last_name }}
                </h2>

                <form method="POST" action="{{ route('provider.participants.update', $participant) }}"
                    enctype="multipart/form-data" {{-- Added for file uploads --}}
                    x-data="{
                        selectedDisabilities: {{ json_encode(old('disability_type', $participant->disability_type ?? [])) }},
                        init() {
                            // Initialize Choices.js for disability_type
                            const disabilityTypeSelect = document.getElementById('disability_type');
                            this.choicesInstance = new Choices(disabilityTypeSelect, {
                                removeItemButton: true,
                                placeholder: true,
                                placeholderValue: 'Select one or more disability types',
                                itemSelectText: '',
                            });

                            // Update Alpine.js variable when Choices.js selection changes
                            disabilityTypeSelect.addEventListener('change', () => {
                                this.selectedDisabilities = this.choicesInstance.getValue(true);
                            });

                            // Initialize Flatpickr for Birthday
                            flatpickr('#birthday', {
                                dateFormat: 'Y-m-d',
                                maxDate: 'today'
                            });
                        }
                    }">
                    @csrf
                    @method('PUT')

                    <h3 class="text-xl font-bold text-[#33595a] mb-4 border-b pb-2 border-[#e1e7dd]">Core Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4"> {{-- Combined first, middle, last name into one row --}}
                        <div>
                            <x-input-label for="first_name" :value="__('First Name') . ' *'" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $participant->first_name)" required autofocus placeholder="e.g., John" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name', $participant->middle_name)" placeholder="e.g., David" />
                            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Last Name') . ' *'" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $participant->last_name)" required placeholder="e.g., Doe" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="birthday" :value="__('Birthday')" />
                            <div class="relative mt-1">
                                <x-text-input id="birthday" class="block w-full pr-10 flatpickr-input" type="text" name="birthday" :value="old('birthday', $participant->birthday ? \Carbon\Carbon::parse($participant->birthday)->format('Y-m-d') : '')" placeholder="YYYY-MM-DD" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#cc8e45]">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="gender" :value="__('Gender')" />
                            <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $participant->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $participant->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Non-binary" {{ old('gender', $participant->gender) == 'Non-binary' ? 'selected' : '' }}>Non-binary</option>
                                <option value="Prefer not to say" {{ old('gender', $participant->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-[#33595a] my-4 border-b pb-2 border-[#e1e7dd]">Disability & Accommodation</h3>

                    <div class="mb-4">
                        <x-input-label for="disability_type" :value="__('Disability Type')" />
                        {{-- Choices.js will be initialized on this select --}}
                        <select id="disability_type" name="disability_type[]" multiple class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]">
                            @foreach($disabilityTypes as $type)
                                <option value="{{ $type }}" {{ in_array($type, (array)old('disability_type', $participant->disability_type ?? [])) ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('disability_type')" class="mt-2" />
                        <x-input-error :messages="$errors->get('disability_type.*')" class="mt-2" />
                    </div>

                    

                    <div class="mb-4">
                        <x-input-label for="accommodation_type" :value="__('Current or Looking for Accommodation Type')" />
                        <select id="accommodation_type" name="accommodation_type" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]">
                            <option value="">Select Accommodation Type</option>
                            <option value="SIL" {{ old('accommodation_type', $participant->accommodation_type) == 'SIL' ? 'selected' : '' }}>SIL (Supported Independent Living)</option>
                            <optgroup label="SDA (Specialist Disability Accommodation)">
                                <option value="Improved Livability" {{ old('accommodation_type', $participant->accommodation_type) == 'Improved Livability' ? 'selected' : '' }}>Improved Livability</option>
                                <option value="Fully Accessible" {{ old('accommodation_type', $participant->accommodation_type) == 'Fully Accessible' ? 'selected' : '' }}>Fully Accessible</option>
                                <option value="High Physical Support" {{ old('accommodation_type', $participant->accommodation_type) == 'High Physical Support' ? 'selected' : '' }}>High Physical Support</option>
                                <option value="Robust" {{ old('accommodation_type', $participant->accommodation_type) == 'Robust' ? 'selected' : '' }}>Robust</option>
                            </optgroup>
                            {{-- Add other general accommodation types here if applicable, for example: --}}
                            {{-- <option value="Private Rental" {{ old('accommodation_type', $participant->accommodation_type) == 'Private Rental' ? 'selected' : '' }}>Private Rental</option> --}}
                            {{-- <option value="Living with Family" {{ old('accommodation_type', $participant->accommodation_type) == 'Living with Family' ? 'selected' : '' }}>Living with Family</option> --}}
                            {{-- <option value="Shared Accommodation" {{ old('accommodation_type', $participant->accommodation_type) == 'Shared Accommodation' ? 'selected' : '' }}>Shared Accommodation</option> --}}
                            {{-- <option value="Group Home" {{ old('accommodation_type', $participant->accommodation_type) == 'Group Home' ? 'selected' : '' }}>Group Home</option> --}}
                            {{-- <option value="Boarding House" {{ old('accommodation_type', $participant->accommodation_type) == 'Boarding House' ? 'selected' : '' }}>Boarding House</option> --}}
                            {{-- <option value="Homeless/Unstable" {{ old('accommodation_type', $participant->accommodation_type) == 'Homeless/Unstable' ? 'selected' : '' }}>Homeless/Unstable</option> --}}
                        </select>
                        <x-input-error :messages="$errors->get('accommodation_type')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="approved_accommodation_type" :value="__('Approved NDIS Accommodation Type')" />
                        <select id="approved_accommodation_type" name="approved_accommodation_type" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]">
                            <option value="">None</option>
                            <option value="SDA" {{ old('approved_accommodation_type', $participant->approved_accommodation_type) == 'SDA' ? 'selected' : '' }}>SDA (Specialist Disability Accommodation)</option>
                            <option value="SIL" {{ old('approved_accommodation_type', $participant->approved_accommodation_type) == 'SIL' ? 'selected' : '' }}>SIL (Supported Independent Living)</option>
                        </select>
                        <x-input-error :messages="$errors->get('approved_accommodation_type')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="behavior_of_concern" :value="__('Behavior of Concern')" />
                        <textarea id="behavior_of_concern" name="behavior_of_concern" rows="3" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]" placeholder="Describe any behaviors of concern.">{{ old('behavior_of_concern', $participant->behavior_of_concern) }}</textarea>
                        <x-input-error :messages="$errors->get('behavior_of_concern')" class="mt-2" />
                    </div>

                    <h3 class="text-xl font-bold text-[#33595a] my-4 border-b pb-2 border-[#e1e7dd]">Address Details</h3>

                    <div class="mb-4">
                        <x-input-label for="street_address" :value="__('Street Address') . ' *'" />
                        <x-text-input id="street_address" class="block mt-1 w-full" type="text" name="street_address" :value="old('street_address', $participant->street_address)" required placeholder="e.g., 123 Main St" />
                        <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <x-input-label for="state" :value="__('State') . ' *'" />
                            <select id="state" name="state" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]" required>
                                <option value="">Select State</option>
                                <option value="ACT" {{ old('state', $participant->state) == 'ACT' ? 'selected' : '' }}>ACT</option>
                                <option value="NSW" {{ old('state', $participant->state) == 'NSW' ? 'selected' : '' }}>NSW</option>
                                <option value="NT" {{ old('state', $participant->state) == 'NT' ? 'selected' : '' }}>NT</option>
                                <option value="QLD" {{ old('state', $participant->state) == 'QLD' ? 'selected' : '' }}>QLD</option>
                                <option value="SA" {{ old('state', $participant->state) == 'SA' ? 'selected' : '' }}>SA</option>
                                <option value="TAS" {{ old('state', $participant->state) == 'TAS' ? 'selected' : '' }}>TAS</option>
                                <option value="VIC" {{ old('state', $participant->state) == 'VIC' ? 'selected' : '' }}>VIC</option>
                                <option value="WA" {{ old('state', $participant->state) == 'WA' ? 'selected' : '' }}>WA</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="suburb" :value="__('Suburb') . ' *'" />
                            <input type="text" id="suburb" name="suburb" value="{{ old('suburb', $participant->suburb) }}" 
                                   class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]" 
                                   placeholder="Enter suburb name" required>
                            <x-input-error :messages="$errors->get('suburb')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="post_code" :value="__('Post Code') . ' *'" />
                            <x-text-input id="post_code" class="block mt-1 w-full" type="text" name="post_code" :value="old('post_code', $participant->post_code)" required placeholder="e.g., 2000" />
                            <x-input-error :messages="$errors->get('post_code')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-[#33595a] my-4 border-b pb-2 border-[#e1e7dd]">Funding & Status</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_looking_hm" name="is_looking_hm" value="1" class="rounded border-gray-300 text-[#cc8e45] shadow-sm focus:ring-[#cc8e45]" {{ old('is_looking_hm', $participant->is_looking_hm) ? 'checked' : '' }}>
                            <x-input-label for="is_looking_hm" class="ml-2" :value="__('Is looking for a Housemate?')" />
                            <x-input-error :messages="$errors->get('is_looking_hm')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="has_accommodation" name="has_accommodation" value="1" class="rounded border-gray-300 text-[#cc8e45] shadow-sm focus:ring-[#cc8e45]" {{ old('has_accommodation', $participant->has_accommodation) ? 'checked' : '' }}>
                            <x-input-label for="has_accommodation" class="ml-2" :value="__('Currently has Accommodation?')" />
                            <x-input-error :messages="$errors->get('has_accommodation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="funding_amount_support_coor" :value="__('Funding for Support Coordination (AUD)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">AUD</span>
                                <x-text-input id="funding_amount_support_coor" class="block w-full pl-12 pr-3" type="number" step="0.01" name="funding_amount_support_coor" :value="old('funding_amount_support_coor', $participant->funding_amount_support_coor)" placeholder="e.g., 5000.00" />
                            </div>
                            <x-input-error :messages="$errors->get('funding_amount_support_coor')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="funding_amount_accommodation" :value="__('Funding for Accommodation (AUD)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">AUD</span>
                                <x-text-input id="funding_amount_accommodation" class="block w-full pl-12 pr-3" type="number" step="0.01" name="funding_amount_accommodation" :value="old('funding_amount_accommodation', $participant->funding_amount_accommodation)" placeholder="e.g., 10000.00" />
                            </div>
                            <x-input-error :messages="$errors->get('funding_amount_accommodation')" class="mt-2" />
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-[#33595a] my-4 border-b pb-2 border-[#e1e7dd]">Health Report / Assessment</h3>

                    <div class="mb-4">
                        <x-input-label for="health_report_file" :value="__('Upload Health Report / Assessment File')" />
                        <input id="health_report_file" type="file" name="health_report_file" class="block w-full text-sm text-gray-500 mt-1
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-[#e1e7dd] file:text-[#33595a]
                            hover:file:bg-[#d0dbcc]" />
                        <p class="text-sm text-gray-500 mt-1">Max 2MB. Allowed types: PDF, DOC, DOCX, JPG, JPEG, PNG.</p>
                        @if ($participant->health_report_path)
                            <p class="text-sm text-gray-600 mt-2">
                                Current file: <a href="{{ Storage::url($participant->health_report_path) }}" target="_blank" class="text-[#cc8e45] hover:underline">{{ basename($participant->health_report_path) }}</a>
                                <span class="text-xs text-gray-500">(Leave blank to keep current, upload new to replace)</span>
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('health_report_file')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="health_report_text" :value="__('Alternative: Health Report / Assessment Details')" />
                        <textarea id="health_report_text" name="health_report_text" rows="5" class="block mt-1 w-full border-gray-300 focus:border-[#cc8e45] focus:ring-[#cc8e45] rounded-md shadow-sm bg-[#ffffff] text-[#3e4732]" placeholder="Provide details if no file is uploaded.">{{ old('health_report_text', $participant->health_report_text) }}</textarea><x-input-error :messages="$errors->get('health_report_text')" class="mt-2" />
                        @if ($errors->has('health_report_path') || $errors->has('health_report_text'))
                            <p class="text-sm text-red-600 mt-2">
                                @error('health_report_path') {{ $message }} @enderror
                                @error('health_report_text') {{ $message }} @enderror
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('provider.participants.list') }}" class="inline-flex items-center px-4 py-2 bg-[#8B8171] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#8B8171] focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-[#cc8e45] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Update Participant') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection {{-- End the main-content section --}}

@push('scripts')
{{-- Flatpickr (Datepicker) CSS and JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Choices.js CSS and JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Suburb is now a free text field, no JavaScript needed
    });
</script>
@endpush