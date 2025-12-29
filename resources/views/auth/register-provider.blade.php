@section('title', 'Register as Provider - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    {{-- Main container for the full-screen layout --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The two-column card layout --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image and Text --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/provider_illustration_background.jpg') }}');">
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    <img src="{{ asset('images/Happy.png') }}" alt="Provider Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Partner with Us!</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        Connect with participants, Support Coordinators, and other providers to create stable and positive living arrangements. Your journey to filling vacancies with the right people starts here.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        Together, let's build a stronger, more accessible support community.
                    </p>
                </div>
            </div>

            {{-- Right Column: Form --}}
            <div class="w-full md:w-2/3 bg-white p-6 sm:p-8 relative flex flex-col md:h-full overflow-y-auto">
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 sm:h-24 w-auto mb-3 sm:mb-4">
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal text-center">
                        Register as a Provider
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Tell us about your organisation to get started.
                    </p>
                </div>

                <form method="POST" action="{{ route('register.provider.store') }}" class="space-y-4 sm:space-y-6" x-data="{
                    // Alpine.js data for password visibility
                    password: '',
                    passwordConfirmation: '',
                    passwordFieldType: 'password',
                    confirmPasswordFieldType: 'password',
                    passwordsMatch: false,

                    // Alpine.js data for dynamic suburbs
                    states: {
                        'ACT': ['Canberra', 'Belconnen', 'Gungahlin', 'Tuggeranong', 'Woden Valley','Queanbeyan', 'Googong', 'Jerrabomberra'],
                        'NSW': ['Sydney', 'Parramatta', 'Newcastle', 'Wollongong', 'Central Coast', 'Blacktown', 'Liverpool', 'Penrith', 'Bondi', 'Manly'],
                        'NT': ['Darwin', 'Palmerston', 'Alice Springs', 'Katherine', 'Howard Springs'],
                        'QLD': ['Brisbane', 'Gold Coast', 'Sunshine Coast', 'Cairns', 'Townsville', 'Toowoomba', 'Logan', 'Ipswich'],
                        'SA': ['Adelaide', 'Mawson Lakes', 'Glenelg', 'Port Adelaide', 'Noarlunga Centre'],
                        'TAS': ['Hobart', 'Launceston', 'Devonport', 'Burnie', 'Kingston'],
                        'VIC': ['Melbourne', 'Geelong', 'Ballarat', 'Bendigo', 'Frankston', 'Dandenong', 'Footscray', 'Richmond'],
                        'WA': ['Perth', 'Fremantle', 'Rockingham', 'Mandurah', 'Joondalup', 'Bunbury', 'Albany']
                    },
                    selectedState: '{{ old('office_state') }}',
                    selectedSuburb: '{{ old('office_suburb') }}',

                    // Alpine.js functions
                    init() {
                        this.$watch('password + passwordConfirmation', () => {
                            this.passwordsMatch = this.password === this.passwordConfirmation && this.passwordConfirmation !== '';
                        });
                        this.$watch('selectedState', () => {
                            this.selectedSuburb = ''; // Reset suburb when state changes
                        });
                    },
                    getSuburbs() {
                        return this.states[this.selectedState] || [];
                    }
                }">
                    @csrf
                    <input type="hidden" name="role" value="provider">

                    {{-- SECTION 1: Organisation Details --}}
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-2 sm:pb-3 mb-3 sm:mb-4">
                            Organisation Details
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="organisation_name" :value="__('Organisation Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                <x-text-input type="text" name="organisation_name" id="organisation_name" 
                                    class="block w-full px-3 py-2 rounded-md shadow-sm
                                           text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                           {{ $errors->has('organisation_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                    :value="old('organisation_name')" required autocomplete="organization" />
                                <x-input-error :messages="$errors->get('organisation_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="abn" :value="__('ABN (Australian Business Number)')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="abn" id="abn" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('abn') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('abn')" required autocomplete="off" />
                                    <x-input-error :messages="$errors->get('abn')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                                <div>
                                    <x-input-label for="ndis_registration_number" :value="__('NDIS Registration Number (Optional)')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="ndis_registration_number" id="ndis_registration_number" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('ndis_registration_number') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('ndis_registration_number')" autocomplete="off" />
                                    <x-input-error :messages="$errors->get('ndis_registration_number')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                            </div>

                            {{-- Provider Type Checkboxes --}}
                            <div>
                                <x-input-label :value="__('Are you a:')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-2" />
                                <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="provider_types" value="SIL Provider" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" >
                                        <span class="ml-2 text-sm text-custom-dark-olive">SIL Provider</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="provider_types" value="SDA Provider" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" >
                                        <span class="ml-2 text-sm text-custom-dark-olive">SDA Provider</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="provider_types" value="Both" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" >
                                        <span class="ml-2 text-sm text-custom-dark-olive">Both</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('provider_types')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>

                            <hr class="my-4 border-custom-light-grey-green">

                            <div class="space-y-4">
                                <h4 class="text-md sm:text-lg font-bold text-custom-dark-teal">Operational Details</h4>
                                <div>
                                    <x-input-label for="office_address" :value="__('Office Address')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="office_address" id="office_address" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('office_address') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('office_address')" autocomplete="street-address" />
                                    <x-input-error :messages="$errors->get('office_address')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="office_state" :value="__('State')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                        <select name="office_state" id="office_state" x-model="selectedState"
                                            class="block w-full px-3 py-2 rounded-md shadow-sm
                                                   text-sm sm:text-base bg-custom-white text-custom-dark-teal
                                                   {{ $errors->has('office_state') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}
                                                   focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150">
                                            <option value="" selected disabled>Select State</option>
                                            @foreach(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'] as $stateAbbr)
                                                <option value="{{ $stateAbbr }}">{{ $stateAbbr }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('office_state')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                    </div>
                                    <div>
                                        <x-input-label for="office_suburb" :value="__('Suburb')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                        <select name="office_suburb" id="office_suburb" x-model="selectedSuburb"
                                            class="block w-full px-3 py-2 rounded-md shadow-sm
                                                   text-sm sm:text-base bg-custom-white text-custom-dark-teal
                                                   {{ $errors->has('office_suburb') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}
                                                   focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150"
                                            x-bind:disabled="!selectedState">
                                            <option value="" selected disabled>Select Suburb</option>
                                            <template x-if="selectedState">
                                                <template x-for="suburb in getSuburbs()" :key="suburb">
                                                    <option :value="suburb" x-text="suburb" :selected="selectedSuburb === suburb"></option>
                                                </template>
                                            </template>
                                        </select>
                                        <x-input-error :messages="$errors->get('office_suburb')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="office_post_code" :value="__('Post Code')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="office_post_code" id="office_post_code" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('office_post_code') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('office_post_code')" autocomplete="postal-code" />
                                    <x-input-error :messages="$errors->get('office_post_code')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                            </div>

                            {{-- States Operated In Checkboxes --}}
                            <div class="mt-4">
                                <x-input-label :value="__('Which states do you operate in?')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-2" />
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @php
                                        $states = ['VIC', 'NSW', 'QLD', 'SA', 'WA', 'TAS', 'ACT', 'NT'];
                                    @endphp
                                    @foreach($states as $state)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="states_operated_in[]" value="{{ $state }}" class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ in_array($state, old('states_operated_in', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-custom-dark-olive">{{ $state }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('states_operated_in')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>
                        </div>
                    </div>
                    {{-- SECTION 2: Account & Representative Details --}}
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-2 sm:pb-3 mb-3 sm:mb-4">
                            Account & Representative Details
                        </h3>
                        <p class="text-sm text-custom-dark-olive mb-4">
                            The company representative's email will be used for your account login.
                        </p>
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                                <div class="flex-1">
                                    <x-input-label for="main_contact_name" :value="__('Representative First Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="main_contact_name" id="main_contact_name" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('main_contact_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('main_contact_name')" required autocomplete="name" />
                                    <x-input-error :messages="$errors->get('main_contact_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                                <div class="flex-1">
                                    <x-input-label for="main_contact_last_name" :value="__('Representative Last Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="main_contact_last_name" id="main_contact_last_name" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('main_contact_last_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('main_contact_last_name')" required autocomplete="name" />
                                    <x-input-error :messages="$errors->get('main_contact_last_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                                <div class="flex-1">
                                    <x-input-label for="main_contact_role_title" :value="__('Role/Title')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="main_contact_role_title" id="main_contact_role_title" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('main_contact_role_title') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('main_contact_role_title')" autocomplete="organization-title" />
                                    <x-input-error :messages="$errors->get('main_contact_role_title')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    {{-- This email will be used for the account --}}
                                    <x-text-input type="email" name="email" id="email" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('email') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('email')" required autocomplete="email" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                                <div>
                                    <x-input-label for="phone_number" :value="__('Phone Number')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                    <x-text-input type="text" name="phone_number" id="phone_number" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('phone_number') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('phone_number')" required autocomplete="tel" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="website" :value="__('Website (Optional)')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                <x-text-input type="url" name="website" id="website" 
                                    class="block w-full px-3 py-2 rounded-md shadow-sm
                                           text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                           {{ $errors->has('website') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                    :value="old('website')" autocomplete="url" />
                                <x-input-error :messages="$errors->get('website')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>

                            {{-- Password --}}
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                <div class="relative">
                                    <input id="password" x-ref="passwordInput" :type="passwordFieldType" name="password" x-model="password" required autocomplete="new-password" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm pr-10
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               {{ $errors->has('password') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}
                                               focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150" />
                                    <button type="button" @click="passwordFieldType = (passwordFieldType === 'password' ? 'text' : 'password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-custom-dark-teal focus:outline-none">
                                        <svg x-show="passwordFieldType === 'password'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.025m3.758-1.332A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.268 4.706M9.543 12.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" /></svg>
                                        <svg x-show="passwordFieldType === 'text'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                                <div class="relative">
                                    <input id="password_confirmation" x-ref="passwordConfirmationInput" :type="confirmPasswordFieldType" name="password_confirmation" x-model="passwordConfirmation" required autocomplete="new-password" 
                                        class="block w-full px-3 py-2 rounded-md shadow-sm pr-10
                                               text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                               focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150" 
                                        :class="{
                                            'border-green-500': passwordsMatch && passwordConfirmation.length > 0,
                                            'border-custom-ochre': !passwordsMatch && passwordConfirmation.length > 0,
                                        }" />
                                    <button type="button" @click="confirmPasswordFieldType = (confirmPasswordFieldType === 'password' ? 'text' : 'password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-custom-dark-teal focus:outline-none">
                                        <svg x-show="confirmPasswordFieldType === 'password'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.025m3.758-1.332A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.268 4.706M9.543 12.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" /></svg>
                                        <svg x-show="confirmPasswordFieldType === 'text'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </div>
                                <p x-show="!passwordsMatch && passwordConfirmation.length > 0" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm">Passwords do not match.</p>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Services Provided --}}
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-2 sm:pb-3 mb-3 sm:mb-4 mt-6">
                            Services Provided
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label :value="__('What types of support do you offer in SIL homes?')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-2" />
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @php
                                        $silSupportTypes = ['24/7 support', 'Active overnight', 'Sleepover only', 'Drop-in support', 'Behaviour support', 'High-intensity supports', 'Other'];
                                    @endphp
                                    @foreach($silSupportTypes as $type)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="sil_support_types[]" value="{{ $type }}" class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ in_array($type, old('sil_support_types', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-custom-dark-olive">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('sil_support_types')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>

                            <div>
                                <x-input-label :value="__('Do you have a clinical team involved in support planning?')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-2" />
                                <div class="flex flex-wrap gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="clinical_team_involvement" value="Yes" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ old('clinical_team_involvement') == 'Yes' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-custom-dark-olive">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="clinical_team_involvement" value="No" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ old('clinical_team_involvement') == 'No' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-custom-dark-olive">No</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="clinical_team_involvement" value="In partnership with external providers" class="rounded-full border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ old('clinical_team_involvement') == 'In partnership with external providers' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-custom-dark-olive">In partnership with external providers</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('clinical_team_involvement')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-custom-light-grey-green">

                    {{-- Terms Checkbox & Buttons --}}
                    <div class="block mt-4 sm:mt-6">
                        <label for="terms_and_privacy" class="inline-flex items-center text-custom-dark-olive cursor-pointer">
                            <input id="terms_and_privacy" type="checkbox" name="terms_and_privacy" required class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre" {{ old('terms_and_privacy') ? 'checked' : '' }}>
                            <span class="ml-2 text-xs sm:text-sm">
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Terms of Service</a> and <a href="{{ route('policy') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Privacy Policy</a>.
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms_and_privacy')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    <div class="flex items-center justify-end mt-6 sm:mt-8">
                        <a class="underline text-xs sm:text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>
                        <x-primary-button class="ms-3 sm:ms-4 py-1.5 px-4 sm:py-2 sm:px-6 rounded-md text-white
                                                 bg-custom-ochre hover:bg-custom-ochre-darker
                                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                 font-semibold text-sm sm:text-base transition ease-in-out duration-150">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>