{{-- resources/views/profile/complete-participant-profile.blade.php --}}
@extends('indiv.indiv-db')

@section('main-content')
    <div class="max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-lg mt-8 border border-gray-200"
         x-data="participantProfileForm({
             isRepresentative: {{ $user->is_representative ? 'true' : 'false' }},
             initialErrors: @json($errors->messages()),
             initialParticipantContactMethod: '{{ old('participant_contact_method', $participant->participant_contact_method ?? '') }}',
             initialGenderIdentity: '{{ old('gender_identity', $participant->gender_identity ?? '') }}',
             initialHasSupportCoordinator: '{{ old('has_support_coordinator', $participant->has_support_coordinator ?? '') }}',
             initialUsesAssistiveTechnology: '{{ old('uses_assistive_technology_mobility_aids', $participant->uses_assistive_technology_mobility_aids ?? '') }}',
             initialBehaviourSupportPlanStatus: '{{ old('behaviour_support_plan_status', $participant->behaviour_support_plan_status ?? '') }}',
             initialAccessibilityNeedsInHome: '{{ old('accessibility_needs_in_home', $participant->accessibility_needs_in_home ?? '') }}',
             initialPetsInHomePreference: '{{ old('pets_in_home_preference', $participant->pets_in_home_preference ?? '') }}',
             initialCurrentLivingSituation: '{{ old('current_living_situation', $participant->current_living_situation ?? '') }}',
             initialContactForSuitableMatch: '{{ old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? '') }}',
             initialPreferredSilLocations: @json(old('preferred_sil_locations', ($participant && $participant->preferred_sil_locations) ? (is_string($participant->preferred_sil_locations) ? json_decode($participant->preferred_sil_locations, true) : $participant->preferred_sil_locations) : [])),
             initialAddressSuburb: '{{ old('suburb', $participant->suburb ?? '') }}', // Renamed to avoid conflict
             initialAddressPostCode: '{{ old('post_code', $participant->post_code ?? '') }}', // Renamed
             initialAddressState: '{{ old('state', $participant->state ?? '') }}' // Added for address state
         })"
    >
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 text-center">Complete Participant Profile 📝</h2>
        <p class="text-gray-700 mb-8 text-center leading-relaxed">
            @if ($user->is_representative)
                Please provide the details for the individual you are representing.
            @else
                Please provide your additional details.
            @endif
            This information helps us connect you with the right opportunities.
        </p>

        {{-- Laravel Validation Errors (Visible if any errors exist after server-side validation) --}}
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

            {{-- Hidden field for participant_code_name (system generated) --}}
            <input type="hidden" name="participant_code_name" value="{{ $user->participant_code_name ?? '' }}">
            {{-- Hidden field for user_id (if self-registered) --}}
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            {{-- Hidden field for added_by_user_id --}}
            <input type="hidden" name="added_by_user_id" value="{{ $user->id }}">

            {{-- Step 1: About You / The Participant (Section 0 from Schema) --}}
            <div x-show="currentStep === 1" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 1: About You / The Participant 👤</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" required
                               value="{{ old('first_name', $participant->first_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Participant's first name">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name"
                               value="{{ old('middle_name', $participant->middle_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Participant's middle name (optional)">
                        @error('middle_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" required
                               value="{{ old('last_name', $participant->last_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Participant's last name">
                        @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="participant_email" class="block text-sm font-medium text-gray-700 mb-1">Participant's Email</label>
                        <input type="email" name="participant_email" id="participant_email"
                               value="{{ old('participant_email', $participant->participant_email ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Participant's email address">
                        @error('participant_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="participant_phone" class="block text-sm font-medium text-gray-700 mb-1">Participant's Phone</label>
                        <input type="text" name="participant_phone" id="participant_phone"
                               value="{{ old('participant_phone', $participant->participant_phone ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Participant's phone number">
                        @error('participant_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Contact Method for Participant</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="participant_contact_method" value="Phone" class="form-radio h-4 w-4 text-indigo-600" x-model="participantContactMethod">
                            <span class="ml-2 text-gray-700">Phone</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="participant_contact_method" value="Email" class="form-radio h-4 w-4 text-indigo-600" x-model="participantContactMethod">
                            <span class="ml-2 text-gray-700">Email</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="participant_contact_method" value="Either" class="form-radio h-4 w-4 text-indigo-600" x-model="participantContactMethod">
                            <span class="ml-2 text-gray-700">Either</span>
                        </label>
                    </div>
                    @error('participant_contact_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_participant_best_contact" id="is_participant_best_contact" value="1" {{ old('is_participant_best_contact', $participant->is_participant_best_contact ?? false) ? 'checked' : '' }}
                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="is_participant_best_contact" class="ml-2 block text-sm font-medium text-gray-700 select-none">Is the participant the best person to contact directly?</label>
                </div>
                @error('is_participant_best_contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <button type="button" @click="goToNextStep()"
                        class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                    Next: Demographics & Identity
                </button>
            </div>

            {{-- Step 2: Demographics & Identity (Section 1 from Schema) --}}
            <div x-show="currentStep === 2" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 2: Demographics & Identity 🌏</h3>

                {{-- Birthday Datepicker --}}
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="date_of_birth" id="date_of_birth" required
                               value="{{ old('date_of_birth', ($participant && $participant->date_of_birth) ? $participant->date_of_birth->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 pr-10 flatpickr-input"
                               placeholder="Select date of birth">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender Identity</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender_identity" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" x-model="genderIdentity">
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div x-show="genderIdentity === 'Other'" class="mt-2">
                        <input type="text" name="gender_identity_other"
                               value="{{ old('gender_identity_other', $participant->gender_identity_other ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Please specify your gender identity">
                    </div>
                    @error('gender_identity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('gender_identity_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="pronouns" class="block text-sm font-medium text-gray-700 mb-1">Pronouns</label>
                    <select name="pronouns[]" id="pronouns" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Select one or more pronouns">
                        @php
                            $pronounOptions = ['He / Him', 'She / Her', 'They / Them', 'Ze / Zir', 'Other'];
                            $currentPronouns = ($participant && $participant->pronouns) ? (is_string($participant->pronouns) ? json_decode($participant->pronouns, true) : $participant->pronouns) : [];
                            $selectedPronouns = old('pronouns', $currentPronouns);
                            $selectedPronouns = is_array($selectedPronouns) ? $selectedPronouns : [];
                        @endphp
                        @foreach($pronounOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedPronouns) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('pronouns') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2" x-show="document.getElementById('pronouns').value.includes('Other')">
                        <input type="text" name="pronouns_other"
                               value="{{ old('pronouns_other', $participant->pronouns_other ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Please specify your pronouns">
                    </div>
                    @error('pronouns_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="languages_spoken" class="block text-sm font-medium text-gray-700 mb-1">Languages Spoken (including English)</label>
                    <select name="languages_spoken[]" id="languages_spoken" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Type or select languages (e.g., English, Mandarin, Auslan)">
                        @php
                            $languageOptions = [
                                'English', 'Mandarin', 'Cantonese', 'Vietnamese', 'Arabic', 'Greek',
                                'Italian', 'Spanish', 'Filipino', 'Hindi', 'Punjabi', 'Auslan', 'Other'
                            ];
                            $currentLanguages = ($participant && $participant->languages_spoken) ? (is_string($participant->languages_spoken) ? json_decode($participant->languages_spoken, true) : $participant->languages_spoken) : [];
                            $selectedLanguages = old('languages_spoken', $currentLanguages);
                            $selectedLanguages = is_array($selectedLanguages) ? $selectedLanguages : [];
                        @endphp
                        @foreach($languageOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedLanguages) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('languages_spoken') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aboriginal or Torres Strait Islander?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="aboriginal_torres_strait_islander" value="Yes" class="form-radio h-4 w-4 text-indigo-600" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'Yes' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="aboriginal_torres_strait_islander" value="No" class="form-radio h-4 w-4 text-indigo-600" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'No' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="aboriginal_torres_strait_islander" value="Prefer not to say" class="form-radio h-4 w-4 text-indigo-600" {{ old('aboriginal_torres_strait_islander', $participant->aboriginal_torres_strait_islander ?? '') == 'Prefer not to say' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Prefer not to say</span>
                        </label>
                    </div>
                    @error('aboriginal_torres_strait_islander') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: NDIS Details
                    </button>
                </div>
            </div>

            {{-- Step 3: NDIS Details (Section 2 from Schema) --}}
            <div x-show="currentStep === 3" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 3: NDIS Details 📋</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Has SIL funding in their NDIS plan?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sil_funding_status" value="Yes" class="form-radio h-4 w-4 text-indigo-600" {{ old('sil_funding_status', $participant->sil_funding_status ?? '') == 'Yes' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sil_funding_status" value="No" class="form-radio h-4 w-4 text-indigo-600" {{ old('sil_funding_status', $participant->sil_funding_status ?? '') == 'No' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sil_funding_status" value="Not sure" class="form-radio h-4 w-4 text-indigo-600" {{ old('sil_funding_status', $participant->sil_funding_status ?? '') == 'Not sure' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Not sure</span>
                        </label>
                    </div>
                    @error('sil_funding_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="ndis_plan_review_date" class="block text-sm font-medium text-gray-700 mb-1">NDIS Plan Review Date</label>
                    <div class="relative">
                        <input type="text" name="ndis_plan_review_date" id="ndis_plan_review_date"
                               value="{{ old('ndis_plan_review_date', ($participant && $participant->ndis_plan_review_date) ? $participant->ndis_plan_review_date->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 pr-10 flatpickr-input"
                               placeholder="Select NDIS plan review date">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @error('ndis_plan_review_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NDIS Plan Manager</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="ndis_plan_manager" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('ndis_plan_manager', $participant->ndis_plan_manager ?? '') == $option ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('ndis_plan_manager') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Has a support coordinator?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="has_support_coordinator" value="1" class="form-radio h-4 w-4 text-indigo-600" x-model="hasSupportCoordinator">
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="has_support_coordinator" value="0" class="form-radio h-4 w-4 text-indigo-600" x-model="hasSupportCoordinator">
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                    </div>
                    @error('has_support_coordinator') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: Support Needs
                    </button>
                </div>
            </div>

            {{-- Step 4: Support Needs (Section 3 from Schema) --}}
            <div x-show="currentStep === 4" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 4: Support Needs 💪</h3>

                <div>
                    <label for="daily_living_support_needs" class="block text-sm font-medium text-gray-700 mb-1">Daily Living Support Needs</label>
                    <select name="daily_living_support_needs[]" id="daily_living_support_needs" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Select daily living support needs">
                        @php
                            $dailyLivingOptions = [
                                'Personal care', 'Medication management', 'Meal preparation',
                                'Shopping', 'Household tasks', 'Community access', 'Managing finances', 'Transport',
                                'Communication', 'Behaviour support', 'Therapy support', 'Employment support', 'Education support', 'Other'
                            ];
                            $currentDailyNeeds = ($participant && $participant->daily_living_support_needs) ? (is_string($participant->daily_living_support_needs) ? json_decode($participant->daily_living_support_needs, true) : $participant->daily_living_support_needs) : [];
                            $selectedDailyNeeds = old('daily_living_support_needs', $currentDailyNeeds);
                            $selectedDailyNeeds = is_array($selectedDailyNeeds) ? $selectedDailyNeeds : [];
                        @endphp
                        @foreach($dailyLivingOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedDailyNeeds) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('daily_living_support_needs') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2" x-show="document.getElementById('daily_living_support_needs').value.includes('Other')">
                        <textarea name="daily_living_support_needs_other" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                  placeholder="Please specify other daily living support needs">{{ old('daily_living_support_needs_other', $participant->daily_living_support_needs_other ?? '') }}</textarea>
                    </div>
                    @error('daily_living_support_needs_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="primary_disability" class="block text-sm font-medium text-gray-700 mb-1">Primary Disability</label>
                        <input type="text" name="primary_disability" id="primary_disability"
                               value="{{ old('primary_disability', $participant->primary_disability ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="e.g., Intellectual Disability">
                        @error('primary_disability') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="secondary_disability" class="block text-sm font-medium text-gray-700 mb-1">Secondary Disability (Optional)</label>
                        <input type="text" name="secondary_disability" id="secondary_disability"
                               value="{{ old('secondary_disability', $participant->secondary_disability ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="e.g., Physical Disability">
                        @error('secondary_disability') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="estimated_support_hours_sil_level" class="block text-sm font-medium text-gray-700 mb-1">Estimated Support Hours / SIL Level</label>
                        <input type="text" name="estimated_support_hours_sil_level" id="estimated_support_hours_sil_level"
                               value="{{ old('estimated_support_hours_sil_level', $participant->estimated_support_hours_sil_level ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="e.g., 1:3, 1:2, 24/7">
                        @error('estimated_support_hours_sil_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Night Support Type</label>
                        <div class="mt-1 flex flex-wrap gap-4">
                            @foreach(['Active overnight', 'Sleepover', 'None'] as $option)
                                <label class="inline-flex items-center">
                                    <input type="radio" name="night_support_type" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('night_support_type', $participant->night_support_type ?? '') == $option ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('night_support_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uses Assistive Technology / Mobility Aids?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="uses_assistive_technology_mobility_aids" value="1" class="form-radio h-4 w-4 text-indigo-600" x-model="usesAssistiveTechnology">
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="uses_assistive_technology_mobility_aids" value="0" class="form-radio h-4 w-4 text-indigo-600" x-model="usesAssistiveTechnology">
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                    </div>
                    @error('uses_assistive_technology_mobility_aids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="usesAssistiveTechnology === '1'" class="mt-2">
                    <label for="assistive_technology_mobility_aids_list" class="block text-sm font-medium text-gray-700 mb-1">List Assistive Technology / Mobility Aids</label>
                    <textarea name="assistive_technology_mobility_aids_list" id="assistive_technology_mobility_aids_list" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="e.g., Wheelchair, Hearing aids, Communication device">{{ old('assistive_technology_mobility_aids_list', $participant->assistive_technology_mobility_aids_list ?? '') }}</textarea>
                    @error('assistive_technology_mobility_aids_list') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: Health & Safety
                    </button>
                </div>
            </div>

            {{-- Step 5: Health & Safety (Section 4 from Schema) --}}
            <div x-show="currentStep === 5" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 5: Health & Safety 🚑</h3>

                <div>
                    <label for="medical_conditions_relevant" class="block text-sm font-medium text-gray-700 mb-1">Relevant Medical Conditions</label>
                    <textarea name="medical_conditions_relevant" id="medical_conditions_relevant" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="List any medical conditions relevant to living arrangements or support needs.">{{ old('medical_conditions_relevant', $participant->medical_conditions_relevant ?? '') }}</textarea>
                    @error('medical_conditions_relevant') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Requires help with medication administration?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Yes', 'No', 'Sometimes'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_administration_help" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('medication_administration_help', $participant->medication_administration_help ?? '') == $option ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('medication_administration_help') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Has a Behaviour Support Plan (BSP)?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Yes', 'No', 'In development'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="behaviour_support_plan_status" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" x-model="behaviourSupportPlanStatus">
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('behaviour_support_plan_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="behaviourSupportPlanStatus === 'Yes'" class="mt-2">
                    <label for="behaviours_of_concern_housemates" class="block text-sm font-medium text-gray-700 mb-1">Behaviours of Concern relevant to housemates</label>
                    <textarea name="behaviours_of_concern_housemates" id="behaviours_of_concern_housemates" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="Describe any behaviours that housemates should be aware of and how they are managed (as per BSP).">{{ old('behaviours_of_concern_housemates', $participant->behaviours_of_concern_housemates ?? '') }}</textarea>
                    @error('behaviours_of_concern_housemates') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: Living Preferences
                    </button>
                </div>
            </div>

            {{-- Step 6: Living Preferences (Section 5 from Schema) --}}
            <div x-show="currentStep === 6" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 6: Living Preferences 🏠</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred SIL Locations</label>
                    <div id="preferred-sil-locations-container" class="space-y-4">
                        <template x-for="(location, index) in preferredSilLocations" :key="index">
                            <div class="flex items-end gap-4">
                                <div class="flex-1">
                                    <label :for="'preferred_state_' + index" class="sr-only">State</label>
                                    <select :name="'preferred_sil_locations[' + index + '][state]'"
                                            :id="'preferred_state_' + index"
                                            x-model="location.state"
                                            @change="updateSuburbsForSil(index, $event.target.value)"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                                        @foreach(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'] as $stateAbbr)
                                            <option value="{{ $stateAbbr }}">{{ $stateAbbr }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="errors['preferred_sil_locations.' + index + '.state']">
                                        <p class="text-red-500 text-xs mt-1" x-text="errors['preferred_sil_locations.' + index + '.state'][0]"></p>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <label :for="'preferred_suburb_' + index" class="sr-only">Suburb</label>
                                    <select :name="'preferred_sil_locations[' + index + '][suburb]'"
                                            :id="'preferred_suburb_' + index"
                                            x-model="location.suburb"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                            :disabled="!location.state">
                                        <option value="" disabled>Select Suburb</option>
                                        <template x-for="suburbOption in location.suburbOptions" :key="suburbOption">
                                            <option :value="suburbOption" x-text="suburbOption"></option>
                                        </template>
                                    </select>
                                    <template x-if="errors['preferred_sil_locations.' + index + '.suburb']">
                                        <p class="text-red-500 text-xs mt-1" x-text="errors['preferred_sil_locations.' + index + '.suburb'][0]"></p>
                                    </template>
                                </div>
                                <button type="button" @click="removePreferredSilLocation(index)" x-show="preferredSilLocations.length > 1"
                                        class="p-2.5 text-red-600 hover:text-red-800 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addPreferredSilLocation()"
                            class="mt-4 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition duration-150 ease-in-out text-sm">
                        Add Another Location
                    </button>
                    @error('preferred_sil_locations') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('preferred_sil_locations.*.state') <p class="text-red-500 text-xs mt-1">Please select a state for all preferred locations.</p> @enderror
                    @error('preferred_sil_locations.*.suburb') <p class="text-red-500 text-xs mt-1">Please select a suburb for all preferred locations.</p> @enderror
                </div>


                <div>
                    <label for="housemate_preferences" class="block text-sm font-medium text-gray-700 mb-1">Housemate Preferences</label>
                    <select name="housemate_preferences[]" id="housemate_preferences" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Select housemate preferences">
                        @php
                            $housemateOptions = [
                                'Male', 'Female', 'Mixed', 'Older', 'Younger', 'Similar age', 'Quiet', 'Social',
                                'Works/Studies', 'Hobbies/Interests (specify below)', 'Other'
                            ];
                            $currentHousematePrefs = ($participant && $participant->housemate_preferences) ? (is_string($participant->housemate_preferences) ? json_decode($participant->housemate_preferences, true) : $participant->housemate_preferences) : [];
                            $selectedHousematePrefs = old('housemate_preferences', $currentHousematePrefs);
                            $selectedHousematePrefs = is_array($selectedHousematePrefs) ? $selectedHousematePrefs : [];
                        @endphp
                        @foreach($housemateOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedHousematePrefs) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('housemate_preferences') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2" x-show="document.getElementById('housemate_preferences').value.includes('Other') || document.getElementById('housemate_preferences').value.includes('Hobbies/Interests (specify below)')">
                        <textarea name="housemate_preferences_other" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                  placeholder="Please specify other housemate preferences or interests.">{{ old('housemate_preferences_other', $participant->housemate_preferences_other ?? '') }}</textarea>
                    </div>
                    @error('housemate_preferences_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Number of Housemates</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['1', '2', '3+', 'No preference'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="preferred_number_of_housemates" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('preferred_number_of_housemates', $participant->preferred_number_of_housemates ?? '') == $option ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('preferred_number_of_housemates') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Accessibility Needs in Home</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Fully accessible', 'Some modifications required', 'No specific needs'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="accessibility_needs_in_home" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" x-model="accessibilityNeedsInHome">
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('accessibility_needs_in_home') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="accessibilityNeedsInHome === 'Fully accessible' || accessibilityNeedsInHome === 'Some modifications required'" class="mt-2">
                    <label for="accessibility_needs_details" class="block text-sm font-medium text-gray-700 mb-1">Details of Accessibility Needs</label>
                    <textarea name="accessibility_needs_details" id="accessibility_needs_details" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="Describe ramps, wider doorways, bathroom modifications, etc.">{{ old('accessibility_needs_details', $participant->accessibility_needs_details ?? '') }}</textarea>
                    @error('accessibility_needs_details') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pets in Home Preference</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Have pets', 'Can live with pets', 'Do not want to live with pets'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="pets_in_home_preference" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" x-model="petsInHomePreference">
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('pets_in_home_preference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="petsInHomePreference === 'Have pets'" class="mt-2">
                    <label for="own_pet_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Pet(s)</label>
                    <input type="text" name="own_pet_type" id="own_pet_type"
                           value="{{ old('own_pet_type', $participant->own_pet_type ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                           placeholder="e.g., Dog, Cat, Fish">
                    @error('own_pet_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="good_home_environment_looks_like" class="block text-sm font-medium text-gray-700 mb-1">A good home environment looks like...</label>
                    <select name="good_home_environment_looks_like[]" id="good_home_environment_looks_like" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Select characteristics of an ideal home environment">
                        @php
                            $homeEnvironmentOptions = [
                                'Quiet', 'Social', 'Clean', 'Organized', 'Relaxed', 'Busy',
                                'Good natural light', 'Access to outdoor space', 'Close to public transport',
                                'Near shops/amenities', 'Structured routine', 'Flexible routine', 'Other'
                            ];
                            $currentHomeEnv = ($participant && $participant->good_home_environment_looks_like) ? (is_string($participant->good_home_environment_looks_like) ? json_decode($participant->good_home_environment_looks_like, true) : $participant->good_home_environment_looks_like) : [];
                            $selectedHomeEnv = old('good_home_environment_looks_like', $currentHomeEnv);
                            $selectedHomeEnv = is_array($selectedHomeEnv) ? $selectedHomeEnv : [];
                        @endphp
                        @foreach($homeEnvironmentOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedHomeEnv) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('good_home_environment_looks_like') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2" x-show="document.getElementById('good_home_environment_looks_like').value.includes('Other')">
                        <textarea name="good_home_environment_looks_like_other" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                  placeholder="Please specify other preferred home environment characteristics.">{{ old('good_home_environment_looks_like_other', $participant->good_home_environment_looks_like_other ?? '') }}</textarea>
                    </div>
                    @error('good_home_environment_looks_like_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: Compatibility & Personality
                    </button>
                </div>
            </div>

            {{-- Step 7: Compatibility & Personality (Section 6 from Schema) --}}
            <div x-show="currentStep === 7" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 7: Compatibility & Personality ✨</h3>

                <div>
                    <label for="self_description" class="block text-sm font-medium text-gray-700 mb-1">Describe Yourself (select all that apply)</label>
                    <select name="self_description[]" id="self_description" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150 choices-js-select"
                            data-placeholder="Select words that describe you">
                        @php
                            $selfDescriptionOptions = [
                                'Quiet', 'Social', 'Independent', 'Outgoing', 'Introverted',
                                'Organized', 'Relaxed', 'Creative', 'Active', 'Calm', 'Energetic', 'Other'
                            ];
                            $currentSelfDesc = ($participant && $participant->self_description) ? (is_string($participant->self_description) ? json_decode($participant->self_description, true) : $participant->self_description) : [];
                            $selectedSelfDesc = old('self_description', $currentSelfDesc);
                            $selectedSelfDesc = is_array($selectedSelfDesc) ? $selectedSelfDesc : [];
                        @endphp
                        @foreach($selfDescriptionOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selectedSelfDesc) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('self_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2" x-show="document.getElementById('self_description').value.includes('Other')">
                        <textarea name="self_description_other" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                  placeholder="Please specify other ways to describe yourself.">{{ old('self_description_other', $participant->self_description_other ?? '') }}</textarea>
                    </div>
                    @error('self_description_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Do you smoke?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="smokes" value="1" class="form-radio h-4 w-4 text-indigo-600" {{ old('smokes', $participant->smokes ?? '') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="smokes" value="0" class="form-radio h-4 w-4 text-indigo-600" {{ old('smokes', $participant->smokes ?? '') == '0' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                    </div>
                    @error('smokes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="deal_breakers_housemates" class="block text-sm font-medium text-gray-700 mb-1">Deal-breakers for Housemates</label>
                    <textarea name="deal_breakers_housemates" id="deal_breakers_housemates" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="Are there any specific preferences or deal-breakers you have regarding housemates?">{{ old('deal_breakers_housemates', $participant->deal_breakers_housemates ?? '') }}</textarea>
                    @error('deal_breakers_housemates') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cultural_religious_practices" class="block text-sm font-medium text-gray-700 mb-1">Cultural or Religious Practices</label>
                    <textarea name="cultural_religious_practices" id="cultural_religious_practices" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="Any cultural or religious practices important to your living arrangements or support?">{{ old('cultural_religious_practices', $participant->cultural_religious_practices ?? '') }}</textarea>
                    @error('cultural_religious_practices') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="interests_hobbies" class="block text-sm font-medium text-gray-700 mb-1">Interests & Hobbies</label>
                    <textarea name="interests_hobbies" id="interests_hobbies" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                              placeholder="What are your interests and hobbies? (e.g., music, sports, arts, reading)">{{ old('interests_hobbies', $participant->interests_hobbies ?? '') }}</textarea>
                    @error('interests_hobbies') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="button" @click="goToNextStep()"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Next: Availability & Contact
                    </button>
                </div>
            </div>

            {{-- Step 8: Availability & Contact (Section 7 + Address from Schema) --}}
            <div x-show="currentStep === 8" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Step 8: Availability & Contact 🗓️</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Move-in Availability</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="move_in_availability" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('move_in_availability', $participant->move_in_availability ?? '') == $option ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('move_in_availability') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Living Situation</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="current_living_situation" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" x-model="currentLivingSituation">
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div x-show="currentLivingSituation === 'Other'" class="mt-2">
                        <textarea name="current_living_situation_other" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                  placeholder="Please describe your current living situation.">{{ old('current_living_situation_other', $participant->current_living_situation_other ?? '') }}</textarea>
                    </div>
                    @error('current_living_situation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('current_living_situation_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Address Details (Moved here as it's often the last piece of general info) --}}
                <h3 class="text-xl font-semibold text-gray-800 pt-4 pb-2 border-t mt-6">Address Details 🏠</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="street_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                        <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $participant->street_address ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                placeholder="Street number and name">
                        @error('street_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select name="state" id="state"
                                x-model="addressState" @change="loadAddressSuburbs($event.target.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                            @foreach(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'] as $stateAbbr)
                                <option value="{{ $stateAbbr }}">{{ $stateAbbr }}</option>
                            @endforeach
                        </select>
                        @error('state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700 mb-1">Suburb</label>
                        <select name="suburb" id="suburb"
                                x-model="addressSuburb"
                                :disabled="!addressState"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150">
                            <option value="" disabled>Select Suburb</option>
                            <template x-for="suburbOption in addressSuburbOptions" :key="suburbOption">
                                <option :value="suburbOption" x-text="suburbOption"></option>
                            </template>
                        </select>
                        @error('suburb') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="post_code" class="block text-sm font-medium text-gray-700 mb-1">Post Code</label>
                        <input type="text" name="post_code" id="post_code" x-model="addressPostCode"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 cursor-not-allowed"
                                placeholder="e.g., 1234">
                        @error('post_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Contact for Suitable Match --}}
                <h3 class="text-xl font-semibold text-gray-800 pt-4 pb-2 border-t mt-6">Contact for Match 📞</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Should we contact you when a suitable match becomes available?</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="contact_for_suitable_match" value="1" class="form-radio h-4 w-4 text-indigo-600" x-model="contactForSuitableMatch">
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="contact_for_suitable_match" value="0" class="form-radio h-4 w-4 text-indigo-600" x-model="contactForSuitableMatch">
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                    </div>
                    @error('contact_for_suitable_match') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="contactForSuitableMatch === '1'" class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Contact Method for Match Updates</label>
                    <div class="mt-1 flex flex-wrap gap-4">
                        @foreach(['Phone', 'Email', 'Via support coordinator', 'Other'] as $option)
                            <label class="inline-flex items-center">
                                <input type="radio" name="preferred_contact_method_match" value="{{ $option }}" class="form-radio h-4 w-4 text-indigo-600" {{ old('preferred_contact_method_match', $participant->preferred_contact_method_match ?? '') == $option ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-2" x-show="document.querySelector('input[name=\'preferred_contact_method_match\']:checked')?.value === 'Other'">
                        <input type="text" name="preferred_contact_method_match_other"
                               value="{{ old('preferred_contact_method_match_other', $participant->preferred_contact_method_match_other ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                               placeholder="Please specify other contact method.">
                    </div>
                    @error('preferred_contact_method_match') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('preferred_contact_method_match_other') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>


                {{-- Emergency Contact Details --}}
                <h3 class="text-xl font-semibold text-gray-800 pt-4 pb-2 border-t mt-6">Emergency Contact Details 🚨</h3>

                <template x-if="isRepresentative">
                    <div class="space-y-6">
                        <div class="mb-4">
                            <label for="representative_name_display" class="block text-sm font-medium text-gray-700 mb-1">Representative Name</label>
                            <input type="text" id="representative_name_display"
                                   value="{{ $user->first_name . ' ' . $user->last_name }}" readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 cursor-not-allowed">
                            <p class="mt-2 text-sm text-gray-600">This field is automatically filled with your name as you are completing the profile on behalf of someone else.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="relationship_to_participant" class="block text-sm font-medium text-gray-700 mb-1">Your Relationship to Participant <span class="text-red-500">*</span></label>
                                <input type="text" name="relationship_to_participant" id="relationship_to_participant" required
                                       value="{{ old('relationship_to_participant', $participant->relationship_to_participant ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                       placeholder="e.g., Parent, Guardian, Support Coordinator">
                                @error('relationship_to_participant') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="representative_phone" class="block text-sm font-medium text-gray-700 mb-1">Your Phone Number</label>
                                <input type="text" name="representative_phone" id="representative_phone"
                                       value="{{ old('representative_phone', $participant->representative_phone ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                       placeholder="Your phone number">
                                @error('representative_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="representative_email" class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                            <input type="email" name="representative_email" id="representative_email"
                                   value="{{ old('representative_email', $user->email) }}" readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 cursor-not-allowed">
                        </div>
                    </div>
                </template>

                <template x-if="!isRepresentative">
                    <div class="space-y-6">
                        <div class="mb-4">
                            <label for="relative_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                            <input type="text" name="relative_name" id="relative_name"
                                   value="{{ old('relative_name', $participant->relative_name ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                   placeholder="Name of emergency contact">
                            @error('relative_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="relative_relationship" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Relationship to You</label>
                                <input type="text" name="relative_relationship" id="relative_relationship"
                                       value="{{ old('relative_relationship', $participant->relative_relationship ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                       placeholder="e.g., Parent, Guardian, Sibling">
                                @error('relative_relationship') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="relative_phone" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                                <input type="text" name="relative_phone" id="relative_phone"
                                       value="{{ old('relative_phone', $participant->relative_phone ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                       placeholder="Emergency contact phone">
                                @error('relative_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="relative_email" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Email</label>
                            <input type="email" name="relative_email" id="relative_email"
                                   value="{{ old('relative_email', $participant->relative_email ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-2.5 transition ease-in-out duration-150"
                                   placeholder="Emergency contact email">
                            @error('relative_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </template>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="goToPreviousStep()"
                            class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Previous
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-lg">
                        Save Profile
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- Choices.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    @endpush

    @push('scripts')
    {{-- Alpine.js (Must be loaded before your custom scripts that use it) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- Choices.js JS --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('participantProfileForm', (initialState) => ({
                currentStep: 1,
                isRepresentative: initialState.isRepresentative,
                errors: initialState.initialErrors,
                participantContactMethod: initialState.initialParticipantContactMethod,
                genderIdentity: initialState.initialGenderIdentity,
                hasSupportCoordinator: initialState.initialHasSupportCoordinator,
                usesAssistiveTechnology: initialState.initialUsesAssistiveTechnology,
                behaviourSupportPlanStatus: initialState.initialBehaviourSupportPlanStatus,
                accessibilityNeedsInHome: initialState.initialAccessibilityNeedsInHome,
                petsInHomePreference: initialState.initialPetsInHomePreference,
                currentLivingSituation: initialState.initialCurrentLivingSituation,
                contactForSuitableMatch: initialState.initialContactForSuitableMatch,

                // State for Address Details (Suburb/Postcode)
                addressState: initialState.initialAddressState,
                addressSuburb: initialState.initialAddressSuburb,
                addressPostCode: initialState.initialAddressPostCode,
                addressSuburbOptions: [],
                cachedSuburbs: {}, // To cache suburb options for each state

                // State for Preferred SIL Locations
                preferredSilLocations: [], // Array of { state: '', suburb: '', suburbOptions: [] }

                getChoicesInstance(element) {
                    if (!element) return null;
                    // Check if Choices.js instance already exists on the element
                    if (element.choices) {
                        return element.choices;
                    }
                    // If not, create and store it
                    const choices = new Choices(element, {
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: element.getAttribute('data-placeholder') || 'Select an option', // Changed default placeholder
                        searchEnabled: true,
                        renderChoiceLimit: -1
                    });
                    element.choices = choices; // Store the instance directly on the element
                    return choices;
                },

                init() {
                    this.setInitialStepFromErrors();
                    this.$nextTick(() => {
                        this.initializeFlatpickr();

                        // Initialize Address Details Suburb/Postcode
                        const mainSuburbSelect = document.getElementById('suburb');
                        if (this.addressState) {
                            this.loadAddressSuburbs(this.addressState, this.addressSuburb);
                        } else {
                            // Ensure main suburb dropdown is styled even if no state is pre-selected
                            this.getChoicesInstance(mainSuburbSelect);
                        }

                        // Initialize Preferred SIL Locations
                        if (initialState.initialPreferredSilLocations && initialState.initialPreferredSilLocations.length > 0) {
                            this.preferredSilLocations = initialState.initialPreferredSilLocations.map(loc => ({
                                state: loc.state,
                                suburb: loc.suburb,
                                suburbOptions: [] // Will be populated by updateSuburbsForSil
                            }));
                            this.preferredSilLocations.forEach((loc, index) => {
                                // Ensure state dropdown for existing locations are styled on load
                                const stateSelect = document.getElementById(`preferred_state_${index}`);
                                this.getChoicesInstance(stateSelect); // Initialize the state dropdown

                                if (loc.state) {
                                    this.updateSuburbsForSil(index, loc.state, loc.suburb);
                                } else {
                                    // Ensure suburb dropdown for existing locations are styled even if no state is pre-selected
                                    const suburbSelect = document.getElementById(`preferred_suburb_${index}`);
                                    this.getChoicesInstance(suburbSelect);
                                }
                            });
                        } else {
                            this.addPreferredSilLocation(); // Start with one empty set of dropdowns
                        }

                        // Initialize Choices.js instances for any other static selects that are not handled dynamically
                        // This selector targets elements with 'choices-js-select' but excludes all state/suburb dropdowns
                        document.querySelectorAll('.choices-js-select:not([id="suburb"]):not([id^="preferred_state_"]):not([id^="preferred_suburb_"])').forEach(element => {
                            this.getChoicesInstance(element);
                        });
                    });
                },

                setInitialStepFromErrors() {
                    if (Object.keys(this.errors).length > 0) {
                        const firstErrorField = Object.keys(this.errors)[0];
                        if (['first_name', 'last_name', 'middle_name', 'participant_email', 'participant_phone', 'participant_contact_method', 'is_participant_best_contact'].includes(firstErrorField)) {
                            this.currentStep = 1;
                        } else if (['date_of_birth', 'gender_identity', 'pronouns', 'languages_spoken', 'aboriginal_torres_strait_islander'].includes(firstErrorField)) {
                            this.currentStep = 2;
                        } else if (['sil_funding_status', 'ndis_plan_review_date', 'ndis_plan_manager', 'has_support_coordinator'].includes(firstErrorField)) {
                            this.currentStep = 3;
                        } else if (['daily_living_support_needs', 'primary_disability', 'secondary_disability', 'estimated_support_hours_sil_level', 'night_support_type', 'uses_assistive_technology_mobility_aids'].includes(firstErrorField)) {
                            this.currentStep = 4;
                        } else if (['medical_conditions_relevant', 'medication_administration_help', 'behaviour_support_plan_status', 'behaviours_of_concern_housemates'].includes(firstErrorField)) {
                            this.currentStep = 5;
                        } else if (['preferred_sil_locations', 'housemate_preferences', 'preferred_number_of_housemates', 'accessibility_needs_in_home', 'pets_in_home_preference', 'good_home_environment_looks_like'].some(field => firstErrorField.startsWith(field))) {
                            // Check for preferred_sil_locations.0.state, etc.
                            this.currentStep = 6;
                        } else if (['self_description', 'smokes', 'deal_breakers_housemates', 'cultural_religious_practices', 'interests_hobbies'].includes(firstErrorField)) {
                            this.currentStep = 7;
                        } else if (['move_in_availability', 'current_living_situation', 'contact_for_suitable_match', 'preferred_contact_method_match', 'street_address', 'suburb', 'state', 'post_code', 'relative_name', 'relationship_to_participant', 'relative_phone', 'relative_email', 'representative_phone'].includes(firstErrorField)) {
                            this.currentStep = 8;
                        }
                    }
                },

                // The `initializeChoices()` function is now largely redundant because `getChoicesInstance` handles it,
                // but I've kept it with a specific exclusion for elements already handled by the state/suburb logic.
                // Consider removing this entirely and ensuring all selects are initialized via getChoicesInstance where needed.
                initializeChoices() {
                    document.querySelectorAll('.choices-js-select:not([id="suburb"]):not([id^="preferred_state_"]):not([id^="preferred_suburb_"])').forEach(element => {
                        this.getChoicesInstance(element);
                    });
                },

                initializeFlatpickr() {
                    flatpickr("#date_of_birth", {
                        dateFormat: "Y-m-d",
                        maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
                    });
                    flatpickr("#ndis_plan_review_date", {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                    });
                },

                // *** Address Details Suburb/Postcode Logic ***
                async loadAddressSuburbs(state, selectedSuburb = null) {
                    // Clear previous state and suburb
                    this.addressSuburbOptions = [];
                    this.addressSuburb = '';

                    const selectElement = document.getElementById('suburb');

                    // Destroy Choices.js if it exists on this element
                    if (selectElement && selectElement.choices) {
                        selectElement.choices.destroy();
                        selectElement.choices = null;
                    }

                    if (!state) {
                        // If no state selected, ensure Choices.js is re-initialized on an empty select
                        this.$nextTick(() => { // Use $nextTick to ensure DOM is ready for re-init
                            this.getChoicesInstance(selectElement);
                        });
                        return;
                    }

                    // Check cache first
                    if (this.cachedSuburbs[state]) {
                        this.addressSuburbOptions = this.cachedSuburbs[state];
                        // Re-initialize Choices.js AFTER Alpine has updated the DOM with new options
                        this.$nextTick(() => {
                            this.updateChoicesInstance(selectElement, this.addressSuburbOptions, selectedSuburb);
                        });
                        return;
                    }

                    // If not in cache, fetch
                    try {
                        const response = await fetch(`/get-suburbs/${state}`);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const rawData = await response.json();
                        this.addressSuburbOptions = rawData;
                        this.cachedSuburbs[state] = rawData;

                        // Re-initialize Choices.js AFTER Alpine has updated the DOM with new options
                        this.$nextTick(() => {
                            this.updateChoicesInstance(selectElement, this.addressSuburbOptions, selectedSuburb);
                        });

                    } catch (error) {
                        console.error('Error fetching address suburbs:', error);
                        alert('Could not load suburbs for the selected state. Please try again.');
                    }
                },

                async updateSuburbsForSil(index, state, selectedSuburb = null) {
                    // Clear current suburb and its options first
                    this.preferredSilLocations[index].suburb = '';
                    this.preferredSilLocations[index].suburbOptions = [];

                    const selectElement = document.getElementById(`preferred_suburb_${index}`);

                    // Destroy the existing Choices.js instance to re-initialize
                    if (selectElement && selectElement.choices) {
                        selectElement.choices.destroy();
                        selectElement.choices = null;
                    }

                    if (!state) {
                        // Re-initialize Choices.js even if state is empty to reset it
                        this.$nextTick(() => {
                            this.getChoicesInstance(selectElement);
                        });
                        return;
                    }

                    if (this.cachedSuburbs[state]) {
                        this.preferredSilLocations[index].suburbOptions = this.cachedSuburbs[state];
                        this.$nextTick(() => {
                            this.updateChoicesInstance(selectElement, this.preferredSilLocations[index].suburbOptions, selectedSuburb);
                        });
                        return;
                    }

                    try {
                        const response = await fetch(`/get-suburbs/${state}`);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const rawData = await response.json();
                        this.preferredSilLocations[index].suburbOptions = rawData; // Assign the raw array of strings
                        this.cachedSuburbs[state] = rawData; // Cache it

                        // After updating the options, re-initialize Choices.js
                        this.$nextTick(() => { // Ensure DOM is updated before Choices.js tries to initialize
                            this.updateChoicesInstance(selectElement, this.preferredSilLocations[index].suburbOptions, selectedSuburb);
                        });
                    } catch (error) {
                        console.error('Error fetching SIL suburbs:', error);
                        alert(`Could not load suburbs for state ${state}. Please try again.`);
                    }
                },

                updateChoicesInstance(selectElement, optionsArray, selectedValue = null) {
                    if (!selectElement) return;

                    // Prepare options for Choices.js (objects with value and label)
                    const choicesOptions = optionsArray.map(suburb => ({
                        value: suburb,
                        label: suburb
                    }));

                    // Get or create Choices.js instance using the helper
                    const choices = this.getChoicesInstance(selectElement);

                    // Clear existing options and set new ones via Choices.js API
                    choices.clearChoices();
                    choices.setChoices(choicesOptions, 'value', 'label', true);


                    // If a suburb was previously selected, try to re-select it
                    if (selectedValue && optionsArray.includes(selectedValue)) {
                        choices.setChoiceByValue(selectedValue);
                    } else {
                        // If no valid selected value, ensure the model is cleared
                        const modelProperty = selectElement.id === 'suburb' ? 'addressSuburb' :
                                            (selectElement.id.startsWith('preferred_suburb_') ? `preferredSilLocations[${selectElement.id.split('_')[2]}].suburb` : null);
                        if (modelProperty) {
                            // This uses a dynamic path to set the Alpine data model
                            const pathParts = modelProperty.split(/[\[\].]/).filter(Boolean);
                            let target = this;
                            for (let i = 0; i < pathParts.length - 1; i++) {
                                if (target === undefined || target[pathParts[i]] === undefined) {
                                    target = undefined;
                                    break;
                                }
                                target = target[pathParts[i]];
                            }
                            if (target !== undefined) {
                                target[pathParts[pathParts.length - 1]] = '';
                            }
                        }
                    }
                },

                addPreferredSilLocation() {
                    this.preferredSilLocations.push({ state: '', suburb: '', suburbOptions: [] });
                    // We need to wait for Alpine to add the new elements to the DOM
                    this.$nextTick(() => {
                        const lastIndex = this.preferredSilLocations.length - 1;
                        const stateSelect = document.getElementById(`preferred_state_${lastIndex}`);
                        const suburbSelect = document.getElementById(`preferred_suburb_${lastIndex}`);

                        // Initialize Choices.js for the newly added state dropdown
                        this.getChoicesInstance(stateSelect);
                        // Initialize Choices.js for the newly added suburb dropdown
                        this.getChoicesInstance(suburbSelect);
                    });
                },

                removePreferredSilLocation(index) {
                    // Before removing, destroy the Choices.js instances for this row
                    const stateSelect = document.getElementById(`preferred_state_${index}`);
                    const suburbSelect = document.getElementById(`preferred_suburb_${index}`);

                    if (stateSelect && stateSelect.choices) {
                        stateSelect.choices.destroy();
                        stateSelect.choices = null;
                    }
                    if (suburbSelect && suburbSelect.choices) {
                        suburbSelect.choices.destroy();
                        suburbSelect.choices = null;
                    }

                    this.preferredSilLocations.splice(index, 1);
                    // Ensure at least one location always exists if the array becomes empty
                    if (this.preferredSilLocations.length === 0) {
                        this.addPreferredSilLocation();
                    }
                },

                validateCurrentStep() {
                    let isValid = true;
                    const currentStepElement = document.querySelector(`div[x-show="currentStep === ${this.currentStep}"]`);
                    if (!currentStepElement) return true;

                    // Clear previous client-side errors for this step
                    currentStepElement.querySelectorAll('.text-red-500.text-xs.mt-1').forEach(el => el.remove());
                    currentStepElement.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

                    const requiredInputs = currentStepElement.querySelectorAll('[required]');
                    requiredInputs.forEach(input => {
                        let value = input.value.trim();
                        // Special handling for choices.js multi-selects
                        if (input.classList.contains('choices-js-select') && input.multiple) {
                            const choicesInstance = Choices.getInstance(input);
                            if (choicesInstance && choicesInstance.getValue(true).length === 0) {
                                value = ''; // Treat as empty if no options selected
                            }
                        }

                        // Skip validation for preferred_sil_locations' selects here, they're handled separately
                        if (input.name.startsWith('preferred_sil_locations[')) {
                            return;
                        }

                        if (!value) {
                            isValid = false;
                            input.classList.add('border-red-500');
                            const errorP = document.createElement('p');
                            errorP.id = input.id + '-error';
                            errorP.classList.add('text-red-500', 'text-xs', 'mt-1');
                            errorP.textContent = 'This field is required.';
                            input.parentNode.appendChild(errorP);
                        } else {
                            input.classList.remove('border-red-500');
                            const errorP = document.getElementById(input.id + '-error');
                            if (errorP) errorP.remove();
                        }
                    });

                    // Specific validation for radio groups
                    const radioGroups = [
                        { name: 'participant_contact_method', step: 1 },
                        { name: 'gender_identity', step: 2 },
                        { name: 'aboriginal_torres_strait_islander', step: 2 },
                        { name: 'sil_funding_status', step: 3 },
                        { name: 'ndis_plan_manager', step: 3 },
                        { name: 'has_support_coordinator', step: 3 },
                        { name: 'night_support_type', step: 4 },
                        { name: 'uses_assistive_technology_mobility_aids', step: 4 },
                        { name: 'medication_administration_help', step: 5 },
                        { name: 'behaviour_support_plan_status', step: 5 },
                        { name: 'preferred_number_of_housemates', step: 6 },
                        { name: 'accessibility_needs_in_home', step: 6 },
                        { name: 'pets_in_home_preference', step: 6 },
                        { name: 'smokes', step: 7 },
                        { name: 'move_in_availability', step: 8 },
                        { name: 'current_living_situation', step: 8 },
                        { name: 'contact_for_suitable_match', step: 8 },
                        { name: 'preferred_contact_method_match', step: 8 }, // Conditional if contactForSuitableMatch is '1'
                    ];

                    radioGroups.forEach(group => {
                        const groupContainer = currentStepElement.querySelector(`input[name="${group.name}"]`)?.closest('div');
                        if (groupContainer && window.getComputedStyle(groupContainer).display !== 'none') {
                            const radioButtons = currentStepElement.querySelectorAll(`input[type="radio"][name="${group.name}"]`);
                            let isRadioSelected = false;
                            radioButtons.forEach(radio => {
                                if (radio.checked) {
                                    isRadioSelected = true;
                                }
                            });

                            if (group.name === 'preferred_contact_method_match' && this.contactForSuitableMatch !== '1') {
                                isRadioSelected = true;
                            }

                            if (!isRadioSelected) {
                                isValid = false;
                                const firstRadioParent = radioButtons[0]?.closest('div');
                                if (firstRadioParent && !document.getElementById(`${group.name}-error`)) {
                                    const errorP = document.createElement('p');
                                    errorP.id = `${group.name}-error`;
                                    errorP.classList.add('text-red-500', 'text-xs', 'mt-1');
                                    errorP.textContent = 'Please select an option.';
                                    firstRadioParent.appendChild(errorP);
                                }
                            } else {
                                const errorP = document.getElementById(`${group.name}-error`);
                                if (errorP) errorP.remove();
                            }
                        }
                    });

                    // Custom validation for Preferred SIL Locations (Step 6)
                    if (this.currentStep === 6) {
                        this.preferredSilLocations.forEach((location, index) => {
                            // Clear previous errors for this specific set of dropdowns
                            const stateSelect = currentStepElement.querySelector(`#preferred_state_${index}`);
                            const suburbSelect = currentStepElement.querySelector(`#preferred_suburb_${index}`);

                            // Remove existing borders and errors before re-evaluating
                            if (stateSelect) {
                                stateSelect.classList.remove('border-red-500');
                                let errorP = document.getElementById(`preferred_sil_locations.${index}.state-error`);
                                if (errorP) errorP.remove();
                            }
                            if (suburbSelect) {
                                suburbSelect.classList.remove('border-red-500');
                                let errorP = document.getElementById(`preferred_sil_locations.${index}.suburb-error`);
                                if (errorP) errorP.remove();
                            }

                            if (!location.state) {
                                isValid = false;
                                if (stateSelect) {
                                    stateSelect.classList.add('border-red-500');
                                    const errorP = document.createElement('p');
                                    errorP.id = `preferred_sil_locations.${index}.state-error`;
                                    errorP.classList.add('text-red-500', 'text-xs', 'mt-1');
                                    errorP.textContent = 'Please select a state.';
                                    stateSelect.parentNode.appendChild(errorP);
                                }
                            }
                            if (!location.suburb) {
                                isValid = false;
                                if (suburbSelect) {
                                    suburbSelect.classList.add('border-red-500');
                                    const errorP = document.createElement('p');
                                    errorP.id = `preferred_sil_locations.${index}.suburb-error`;
                                    errorP.classList.add('text-red-500', 'text-xs', 'mt-1');
                                    errorP.textContent = 'Please select a suburb.';
                                    suburbSelect.parentNode.appendChild(errorP);
                                }
                            }
                        });
                    }

                    // Conditional required field: relationship_to_participant for representatives
                    if (this.currentStep === 8 && this.isRepresentative) {
                        const relationshipInput = document.getElementById('relationship_to_participant');
                        if (relationshipInput && !relationshipInput.value.trim()) {
                            isValid = false;
                            relationshipInput.classList.add('border-red-500');
                            if (!document.getElementById('relationship_to_participant-error')) {
                                const errorP = document.createElement('p');
                                errorP.id = 'relationship_to_participant-error';
                                errorP.classList.add('text-red-500', 'text-xs', 'mt-1');
                                errorP.textContent = 'This field is required.';
                                relationshipInput.parentNode.appendChild(errorP);
                            }
                        } else if (relationshipInput) {
                            relationshipInput.classList.remove('border-red-500');
                            const errorP = document.getElementById('relationship_to_participant-error');
                            if (errorP) errorP.remove();
                        }
                    }

                    return isValid;
                },

                goToNextStep() {
                    if (this.validateCurrentStep()) {
                        this.currentStep++;
                        this.$nextTick(() => {
                            this.initializeChoices(); // Re-initialize general choices.js elements
                            this.initializeFlatpickr();

                            // Re-initialize specific elements if they become visible on this step
                            if (this.currentStep === 8) { // Assuming address details are on step 8
                                this.loadAddressSuburbs(this.addressState, this.addressSuburb);
                                // Also ensure the main state dropdown is styled if it's new on this step
                                const mainStateSelect = document.getElementById('state'); // Assuming an ID for main state dropdown
                                if (mainStateSelect && !mainStateSelect.choices) {
                                    this.getChoicesInstance(mainStateSelect);
                                }
                            }
                            if (this.currentStep === 6) { // Assuming preferred SIL locations are on step 6
                                this.preferredSilLocations.forEach((loc, index) => {
                                    // Re-initialize state dropdown for existing entries
                                    const stateSelect = document.getElementById(`preferred_state_${index}`);
                                    if (stateSelect && !stateSelect.choices) {
                                        this.getChoicesInstance(stateSelect);
                                    }
                                    // Only load suburbs if a state is selected and suburbs aren't already loaded
                                    if (loc.state && loc.suburbOptions.length === 0) {
                                        this.updateSuburbsForSil(index, loc.state, loc.suburb);
                                    } else {
                                        // If no state or suburbs already loaded, ensure suburb dropdown is styled
                                        const suburbSelect = document.getElementById(`preferred_suburb_${index}`);
                                        if (suburbSelect && !suburbSelect.choices) {
                                            this.getChoicesInstance(suburbSelect);
                                        }
                                    }
                                });
                            }
                        });
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                goToPreviousStep() {
                    this.currentStep--;
                    this.$nextTick(() => {
                        this.initializeChoices(); // Re-initialize general choices.js elements
                        this.initializeFlatpickr();

                        // Re-initialize specific elements if they become visible on this step
                        if (this.currentStep === 8) { // Assuming address details are on step 8
                            this.loadAddressSuburbs(this.addressState, this.addressSuburb);
                            // Also ensure the main state dropdown is styled if it's new on this step
                            const mainStateSelect = document.getElementById('state'); // Assuming an ID for main state dropdown
                            if (mainStateSelect && !mainStateSelect.choices) {
                                this.getChoicesInstance(mainStateSelect);
                            }
                        }
                        if (this.currentStep === 6) { // Assuming preferred SIL locations are on step 6
                            this.preferredSilLocations.forEach((loc, index) => {
                                // Re-initialize state dropdown for existing entries
                                const stateSelect = document.getElementById(`preferred_state_${index}`);
                                if (stateSelect && !stateSelect.choices) {
                                    this.getChoicesInstance(stateSelect);
                                }
                                // Only load suburbs if a state is selected and suburbs aren't already loaded
                                if (loc.state && loc.suburbOptions.length === 0) {
                                    this.updateSuburbsForSil(index, loc.state, loc.suburb);
                                } else {
                                    // If no state or suburbs already loaded, ensure suburb dropdown is styled
                                    const suburbSelect = document.getElementById(`preferred_suburb_${index}`);
                                    if (suburbSelect && !suburbSelect.choices) {
                                        this.getChoicesInstance(suburbSelect);
                                    }
                                }
                            });
                        }
                    });
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
            }));
        });
    </script>
    @endpush
@endsection