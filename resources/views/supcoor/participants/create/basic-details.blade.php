@extends('supcoor.participants.create')

@section('page_title', 'Basic Details')
@section('page_description', 'Personal information and contact details')

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

        <form method="POST" action="{{ $participant->exists ? route('sc.participants.profile.basic-details.update', $participant->id) : route('sc.participants.store') }}">
            @csrf
            @if($participant->exists)
                @method('PUT') {{-- Only include for existing participants (updates) --}}
            @endif
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $participant->first_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name (Optional)</label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $participant->middle_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('middle_name') border-red-500 @enderror">
                        @error('middle_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $participant->last_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="participant_email" class="block text-sm font-medium text-gray-700">Participant Email</label>
                        <input type="email" name="participant_email" id="participant_email" value="{{ old('participant_email', $participant->participant_email ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('participant_email') border-red-500 @enderror">
                        @error('participant_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="participant_phone" class="block text-sm font-medium text-gray-700">Participant Phone</label>
                        <input type="tel" name="participant_phone" id="participant_phone" value="{{ old('participant_phone', $participant->participant_phone ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('participant_phone') border-red-500 @enderror">
                        @error('participant_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="participant_contact_method" class="block text-sm font-medium text-gray-700">Preferred Contact Method (Optional)</label>
                    <select name="participant_contact_method" id="participant_contact_method"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('participant_contact_method') border-red-500 @enderror">
                        <option value="">Select an option</option>
                        <option value="Phone" {{ old('participant_contact_method', $participant->participant_contact_method ?? '') == 'Phone' ? 'selected' : '' }}>Phone</option>
                        <option value="Email" {{ old('participant_contact_method', $participant->participant_contact_method ?? '') == 'Email' ? 'selected' : '' }}>Email</option>
                        <option value="Either" {{ old('participant_contact_method', $participant->participant_contact_method ?? '') == 'Either' ? 'selected' : '' }}>Either</option>
                    </select>
                    @error('participant_contact_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_participant_best_contact" name="is_participant_best_contact" type="checkbox" value="1"
                                {{ old('is_participant_best_contact', $participant->is_participant_best_contact ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_participant_best_contact" class="font-medium text-gray-700">Is the participant the best person to contact?</label>
                    </div>
                    @error('is_participant_best_contact')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="contact_person_fields" class="space-y-6 mt-8">
                <hr class="my-8">
                <h2 class="text-2xl font-semibold text-gray-800">Contact Person Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_full_name" class="block text-sm font-medium text-gray-700">Contact Full Name</label>
                        <input type="text" name="contact_full_name" id="contact_full_name" value="{{ old('contact_full_name', $participant->participantContact->full_name ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_full_name') border-red-500 @enderror">
                        @error('contact_full_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_relationship" class="block text-sm font-medium text-gray-700">Relationship to Participant</label>
                        <select name="contact_relationship" id="contact_relationship" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_relationship') border-red-500 @enderror">
                            <option value="">Select an option</option>
                            @php
                                $relationships = ['Family member', 'Carer', 'Public Guardian', 'Support Worker', 'Other'];
                            @endphp
                            @foreach ($relationships as $relationship)
                                <option value="{{ $relationship }}" {{ old('contact_relationship', $participant->participantContact->relationship_to_participant ?? '') == $relationship ? 'selected' : '' }}>{{ $relationship }}</option>
                            @endforeach
                        </select>
                        @error('contact_relationship')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_organisation" class="block text-sm font-medium text-gray-700">Organisation (Optional)</label>
                        <input type="text" name="contact_organisation" id="contact_organisation" value="{{ old('contact_organisation', $participant->participantContact->organisation ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_organisation') border-red-500 @enderror">
                        @error('contact_organisation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                        <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $participant->participantContact->phone_number ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_phone') border-red-500 @enderror">
                        @error('contact_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Email Address (Optional)</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $participant->participantContact->email_address ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_email') border-red-500 @enderror">
                        @error('contact_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_method" class="block text-sm font-medium text-gray-700">Preferred Method of Contact (Optional)</label>
                        <select name="contact_method" id="contact_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_method') border-red-500 @enderror">
                            <option value="">Select an option</option>
                            @php
                                $contactMethods = ['Phone', 'Email', 'Either'];
                            @endphp
                            @foreach ($contactMethods as $method)
                                <option value="{{ $method }}" {{ old('contact_method', $participant->participantContact->preferred_method_of_contact ?? '') == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('contact_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1">
                        <label for="contact_consent" class="block text-sm font-medium text-gray-700">Consent to speak on behalf</label>
                        <select name="contact_consent" id="contact_consent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_consent') border-red-500 @enderror">
                            <option value="">Select an option</option>
                            @php
                                $consents = ['Yes', 'No', 'Consent pending or unsure'];
                            @endphp
                            @foreach ($consents as $consent)
                                <option value="{{ $consent }}" {{ old('contact_consent', $participant->participantContact->consent_to_speak_on_behalf ?? '') == $consent ? 'selected' : '' }}>{{ $consent }}</option>
                            @endforeach
                        </select>
                        @error('contact_consent')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <hr class="my-8">

            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Address Details</h2>
                <div>
                    <label for="street_address" class="block text-sm font-medium text-gray-700">Street Address</label>
                    <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $participant->street_address ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('street_address') border-red-500 @enderror">
                    @error('street_address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state" id="state"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state') border-red-500 @enderror">
                            <option value="">Select a State</option>
                            @php
                                $states = ['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA']; // Australian states/territories
                            @endphp
                            @foreach ($states as $state)
                                <option value="{{ $state }}" {{ old('state', $participant->state ?? '') == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                        @error('state')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                        <select name="suburb" id="suburb"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('suburb') border-red-500 @enderror">
                            <option value="">Select a Suburb</option>
                            {{-- Suburbs will be loaded dynamically via JavaScript --}}
                            @if(old('state', $participant->state ?? ''))
                                {{-- If there's an old state or participant state, try to pre-fill the suburb --}}
                                @php
                                    // This assumes you have a way to get suburbs for the old/current state on initial load
                                    // For simplicity in this Blade, we'll just put the current selected suburb if it exists
                                @endphp
                                @if($participant->suburb)
                                    <option value="{{ $participant->suburb }}" selected>{{ $participant->suburb }}</option>
                                @endif
                            @endif
                        </select>
                        @error('suburb')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="post_code" class="block text-sm font-medium text-gray-700">Postcode</label>
                    <input type="text" name="post_code" id="post_code" value="{{ old('post_code', $participant->post_code ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('postcode') border-red-500 @enderror">
                    @error('post_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="my-8">

            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800">Basic Demographics</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="text" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $participant->date_of_birth ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="gender_identity" class="block text-sm font-medium text-gray-700">Gender Identity</label>
                        <select name="gender_identity" id="gender_identity"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('gender_identity') border-red-500 @enderror">
                            <option value="">Select an option</option>
                            <option value="Female" {{ old('gender_identity', $participant->gender_identity ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Male" {{ old('gender_identity', $participant->gender_identity ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Non-binary" {{ old('gender_identity', $participant->gender_identity ?? '') == 'Non-binary' ? 'selected' : '' }}>Non-binary</option>
                            <option value="Prefer not to say" {{ old('gender_identity', $participant->gender_identity ?? '') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            <option value="Other" {{ old('gender_identity', $participant->gender_identity ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender_identity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="gender_identity_other_field" class="{{ old('gender_identity', $participant->gender_identity ?? '') == 'Other' ? '' : 'hidden' }}">
                    <label for="gender_identity_other" class="block text-sm font-medium text-gray-700">Please specify your gender identity</label>
                    <input type="text" name="gender_identity_other" id="gender_identity_other" value="{{ old('gender_identity_other', $participant->gender_identity_other ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="pronouns_choices" class="block text-sm font-medium text-gray-700">Pronouns</label>
                    <select name="pronouns[]" id="pronouns_choices" multiple>
                        @php
                            $pronouns = ['She / Her', 'He / Him', 'They / Them', 'She / They', 'He / They', 'Ze / Hir', 'Prefer not to say', 'Other'];
                            $selectedPronouns = old('pronouns', $participant->pronouns ?? []);
                            if(is_string($selectedPronouns)) {
                                $selectedPronouns = json_decode($selectedPronouns, true) ?? [];
                            }
                        @endphp
                        @foreach($pronouns as $pronoun)
                            <option value="{{ $pronoun }}" {{ in_array($pronoun, $selectedPronouns) ? 'selected' : '' }}>{{ $pronoun }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm text-gray-500">You can select multiple pronouns.</p>
                    <div id="pronouns_other_field" class="mt-2 hidden">
                        <label for="pronouns_other_text" class="block text-sm font-medium text-gray-700">Please specify your pronouns</label>
                        <input type="text" name="pronouns_other_text" id="pronouns_other_text" value="{{ old('pronouns_other_text', $participant->pronouns_other_text ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('pronouns')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="languages_spoken_choices" class="block text-sm font-medium text-gray-700">Languages Spoken</label>
                    <select name="languages_spoken[]" id="languages_spoken_choices" multiple>
                        @php
                            $selectedLanguages = old('languages_spoken', $participant->languages_spoken ?? []);
                            if(is_string($selectedLanguages)) {
                                $selectedLanguages = json_decode($selectedLanguages, true) ?? [];
                            }
                            $australianLanguages = [
                                'English',
                                'Auslan (Australian Sign Language)',
                                'Italian',
                                'Greek',
                                'Maltese',
                                'German',
                                'Mandarin',
                                'Cantonese',
                                'Vietnamese',
                                'Arabic',
                                'Filipino/Tagalog',
                                'Hindi',
                                'Pitjantjatjara',
                                'Warlpiri',
                                'Arrernte',
                                'Kriol',
                            ];
                        @endphp
                        
                        @foreach ($australianLanguages as $language)
                            <option value="{{ $language }}" {{ in_array($language, $selectedLanguages) ? 'selected' : '' }}>{{ $language }}</option>
                        @endforeach

                        @if(!empty($selectedLanguages))
                            @foreach($selectedLanguages as $language)
                                @if(!in_array($language, $australianLanguages) && $language !== 'Other')
                                    <option value="{{ $language }}" selected>{{ $language }}</option>
                                @endif
                            @endforeach
                        @endif

                        <option value="Other">Other</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">Start typing to add languages or select from the list. You can add "Other" to specify a language not listed.</p>
                    <div id="languages_other_field" class="mt-2 hidden">
                        <label for="languages_other_text" class="block text-sm font-medium text-gray-700">Please specify the language</label>
                        <input type="text" name="languages_other_text" id="languages_other_text" value="{{ old('languages_other_text', $participant->languages_other_text ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('languages_spoken')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="aboriginal_torres_strait_islander" class="block text-sm font-medium text-gray-700">Aboriginal / Torres Strait Islander</label>
                    <select name="aboriginal_torres_strait_islander" id="aboriginal_torres_strait_islander"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('aboriginal_torres_strait_islander') border-red-500 @enderror">
                        <option value="">Select an option</option>
                        <option value="Yes" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'No' ? 'selected' : '' }}>No</option>
                        <option value="Prefer not to say" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                    @error('aboriginal_torres_strait_islander')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ----- Gender Identity "Other" field toggle -----
            const genderSelect = document.getElementById('gender_identity');
            const genderOtherField = document.getElementById('gender_identity_other_field');
            
            function toggleGenderOther() {
                if (genderSelect.value === 'Other') {
                    genderOtherField.classList.remove('hidden');
                } else {
                    genderOtherField.classList.add('hidden');
                }
            }

            // Initial check on page load
            toggleGenderOther();

            // Add event listener for changes
            genderSelect.addEventListener('change', toggleGenderOther);


            // ----- Contact Person Fields toggle -----
            const contactCheckbox = document.getElementById('is_participant_best_contact');
            const contactFieldsContainer = document.getElementById('contact_person_fields');

            function toggleContactFields() {
                // If the checkbox is unchecked, show the contact person fields
                if (contactCheckbox.checked) {
                    contactFieldsContainer.classList.add('hidden');
                } else {
                    contactFieldsContainer.classList.remove('hidden');
                }
            }

            // Initial check on page load
            toggleContactFields();

            // Add event listener for changes
            contactCheckbox.addEventListener('change', toggleContactFields);


            // ----- flatpickr initialization -----
            flatpickr("#date_of_birth", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            });


            // ----- Choices.js for Pronouns and Languages -----
            
            // Pronouns
            const pronounsElement = document.getElementById('pronouns_choices');
            const pronounsOtherField = document.getElementById('pronouns_other_field');
            const pronounsChoices = new Choices(pronounsElement, {
                removeItemButton: true,
                shouldSort: false,
                searchEnabled: false,
            });

            // Function to check if 'Other' is selected and toggle the input field
            function togglePronounsOther() {
                const selectedValues = pronounsChoices.getValue(true);
                if (selectedValues.includes('Other')) {
                    pronounsOtherField.classList.remove('hidden');
                } else {
                    pronounsOtherField.classList.add('hidden');
                }
            }

            // Initial check on page load for pronouns
            togglePronounsOther();
            
            // Add event listener for changes in choices.js
            pronounsElement.addEventListener('change', togglePronounsOther);


            // Languages Spoken
            const languagesElement = document.getElementById('languages_spoken_choices');
            const languagesOtherField = document.getElementById('languages_other_field');
            const languagesChoices = new Choices(languagesElement, {
                removeItemButton: true,
                shouldSort: false,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'Select or type languages',
                duplicateItemsAllowed: false,
                allowHTML: true,
                // The following options enable free-form tagging
                // We use this because the list of languages is not provided
                addItemText: (value) => {
                    return `Press Enter to add <b>"${value}"</b>`;
                },
            });

            // Function to check if 'Other' is selected and toggle the input field
            function toggleLanguagesOther() {
                const selectedValues = languagesChoices.getValue(true);
                if (selectedValues.includes('Other')) {
                    languagesOtherField.classList.remove('hidden');
                } else {
                    languagesOtherField.classList.add('hidden');
                }
            }

            // Initial check on page load for languages
            toggleLanguagesOther();

            // Add event listener for changes in choices.js
            languagesElement.addEventListener('change', toggleLanguagesOther);

            // ----- Dynamic Suburb Loading -----
            const stateSelect = document.getElementById('state');
            const suburbSelect = document.getElementById('suburb');
            const currentSuburb = "{{ old('suburb', $participant->suburb ?? '') }}"; // Capture the old/current suburb for pre-selection

            async function loadSuburbs(state) {
                suburbSelect.innerHTML = '<option value="">Loading suburbs...</option>';
                suburbSelect.disabled = true;

                if (!state) {
                    suburbSelect.innerHTML = '<option value="">Select a State first</option>';
                    return;
                }

                try {
                    const response = await fetch(`/get-suburbs/${state}`);
                    const suburbs = await response.json();

                    suburbSelect.innerHTML = '<option value="">Select a Suburb</option>';
                    suburbs.forEach(suburb => {
                        const option = document.createElement('option');
                        option.value = suburb;
                        option.textContent = suburb;
                        if (suburb === currentSuburb) {
                            option.selected = true;
                        }
                        suburbSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching suburbs:', error);
                    suburbSelect.innerHTML = '<option value="">Error loading suburbs</option>';
                } finally {
                    suburbSelect.disabled = false;
                }
            }

            // Initial load of suburbs if a state is already selected (e.g., on form errors or edit)
            if (stateSelect.value) {
                loadSuburbs(stateSelect.value);
            }

            // Event listener for state change
            stateSelect.addEventListener('change', (event) => {
                loadSuburbs(event.target.value);
            });
        });
    </script>
@endsection