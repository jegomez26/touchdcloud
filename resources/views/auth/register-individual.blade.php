@section('title', 'Register - ' . config('app.name', 'TouchdCloud')) {{-- Sets the page title --}}

<x-guest-layout>
    {{-- The registration card container (matching the login form's card structure) --}}
    <div class="w-full max-w-2xl bg-custom-white rounded-lg shadow-xl p-8 sm:p-10 border border-custom-light-grey-green relative">
        {{-- Adjusted max-w-md to max-w-2xl to give more space for the form elements --}}

        <div class="flex flex-col items-center justify-center mb-8">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-24 w-auto mb-4">
            </a>
            <h2 class="text-3xl font-extrabold text-custom-dark-teal text-center">
                Join TouchdCloud!
            </h2>
            <p class="mt-2 text-custom-dark-olive text-center">
                Create your account to get started.
            </p>
        </div>

        <a href="{{ route('home') }}?showRegisterModal=true"
           class="absolute top-3 right-3 text-custom-light-grey-brown hover:text-custom-black text-3xl font-bold"
           title="Back to role selection">
            &times;
        </a>

        <form method="POST" action="{{ route('register') }}"
            x-ref="form"
            @submit.prevent="
                passwordConfirmationTouched = true;
                if (allPasswordCriteriaMet && passwordsMatch) {
                    $refs.form.submit();
                } else {
                    if (!passwordsMatch) {
                        $nextTick(() => $refs.passwordConfirmationInput.focus());
                    } else {
                        $nextTick(() => $refs.passwordInput.focus());
                    }
                }
            "
            class="space-y-6"
            x-data="{
                registrationType: '{{ old('registration_type', 'participant') }}',
                passwordFieldType: 'password',
                confirmPasswordFieldType: 'password',
                password: '',
                passwordConfirmation: '',
                passwordConfirmationTouched: false,
                hasMinLength: false,
                hasUpperCase: false,
                hasLowerCase: false,
                hasDigit: false,
                hasSpecialChar: false,

                validatePassword(pw) {
                    this.hasMinLength = pw.length >= 8;
                    this.hasUpperCase = /[A-Z]/.test(pw);
                    this.hasLowerCase = /[a-z]/.test(pw);
                    this.hasDigit = /\d/.test(pw);
                    this.hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':`~\\|,.<>\/?]/.test(pw);
                },
                handlePasswordConfirmationBlur() {
                    this.passwordConfirmationTouched = true;
                },
                get allPasswordCriteriaMet() {
                    return this.hasMinLength && this.hasUpperCase && this.hasLowerCase && this.hasDigit && this.hasSpecialChar;
                },
                get passwordsMatch() {
                    return this.password === this.passwordConfirmation && this.passwordConfirmation !== '';
                }
            }"
        >
            @csrf

            <input type="hidden" name="role" value="participant">

            {{-- "Who are you registering?" section (Toggle Button) --}}
            <div class="mb-6">
                <label class="block text-base font-semibold text-custom-dark-teal mb-2">Who are you registering?</label>
                <div class="relative flex w-full bg-custom-light-cream rounded-full p-1 border border-custom-light-grey-green">
                    <div class="absolute inset-0 z-0 bg-custom-ochre rounded-full shadow-md transition-all duration-300 ease-in-out"
                         :class="registrationType === 'participant' ? 'transform translate-x-0 w-1/2' : 'transform translate-x-full w-1/2'">
                    </div>

                    <button type="button"
                            @click="registrationType = 'participant'"
                            class="relative z-10 w-1/2 px-4 py-2 text-center rounded-full transition-colors duration-300 ease-in-out"
                            :class="registrationType === 'participant' ? 'text-white font-bold' : 'text-custom-dark-teal hover:text-custom-ochre-darker'">
                        I am the Participant
                    </button>
                    <button type="button"
                            @click="registrationType = 'representative'"
                            class="relative z-10 w-1/2 px-4 py-2 text-center rounded-full transition-colors duration-300 ease-in-out"
                            :class="registrationType === 'representative' ? 'text-white font-bold' : 'text-custom-dark-teal hover:text-custom-ochre-darker'">
                        I am registering for a Participant
                    </button>

                    <input type="hidden" name="registration_type" x-model="registrationType">
                </div>
                @error('registration_type')
                    <span class="text-custom-ochre text-sm mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Representative Fields - controlled by Alpine's x-show --}}
            <div x-show="registrationType === 'representative'" class="space-y-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4">

                <h3 class="text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-3 mb-4">Your Details (Representative)</h3>

                <div>
                    <x-input-label for="representative_first_name" :value="__('Your First Name')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                    <x-text-input type="text" name="representative_first_name" id="representative_first_name"
                                  class="block w-full px-4 py-2 rounded-md shadow-sm
                                         text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                         {{ $errors->has('representative_first_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                  :value="old('representative_first_name')"
                                  x-bind:required="registrationType === 'representative'" />
                    <x-input-error :messages="$errors->get('representative_first_name')" class="mt-2 text-custom-ochre text-sm" />
                </div>

                <div>
                    <x-input-label for="representative_last_name" :value="__('Your Last Name')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                    <x-text-input type="text" name="representative_last_name" id="representative_last_name"
                                  class="block w-full px-4 py-2 rounded-md shadow-sm
                                         text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                         {{ $errors->has('representative_last_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                  :value="old('representative_last_name')"
                                  x-bind:required="registrationType === 'representative'" />
                    <x-input-error :messages="$errors->get('representative_last_name')" class="mt-2 text-custom-ochre text-sm" />
                </div>

                <div>
                    <x-input-label for="relationship_to_participant" :value="__('Relationship to Participant')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                    <select name="relationship_to_participant" id="relationship_to_participant"
                            class="block w-full px-4 py-2 border rounded-md shadow-sm
                                   text-base bg-custom-white text-custom-dark-teal
                                   {{ $errors->has('relationship_to_participant') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                            x-bind:required="registrationType === 'representative'">
                        <option value="">Select Relationship</option>
                        <option value="Parent" {{ old('relationship_to_participant') == 'Parent' ? 'selected' : '' }}>Parent</option>
                        <option value="Guardian" {{ old('relationship_to_participant') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                        <option value="Support Coordinator" {{ old('relationship_to_participant') == 'Support Coordinator' ? 'selected' : '' }}>Support Coordinator</option>
                        <option value="Family Member" {{ old('relationship_to_participant') == 'Family Member' ? 'selected' : '' }}>Family Member</option>
                        <option value="Other" {{ old('relationship_to_participant') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('relationship_to_participant')" class="mt-2 text-custom-ochre text-sm" />
                </div>

                <h3 class="text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-3 mt-6 mb-4">Participant's Details</h3>
            </div>

            {{-- Participant/User Details --}}
            <div>
                <x-input-label for="first_name" class="text-sm font-semibold text-custom-dark-teal mb-1">
                    <span x-text="registrationType === 'representative' ? 'Participant\'s First Name' : 'Your First Name'"></span>
                </x-input-label>
                <x-text-input type="text" name="first_name" id="first_name"
                              class="block w-full px-4 py-2 rounded-md shadow-sm
                                     text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                     {{ $errors->has('first_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                              :value="old('first_name')" required autocomplete="first_name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div>
                <x-input-label for="last_name" class="text-sm font-semibold text-custom-dark-teal mb-1">
                    <span x-text="registrationType === 'representative' ? 'Participant\'s Last Name' : 'Your Last Name'"></span>
                </x-input-label>
                <x-text-input type="text" name="last_name" id="last_name"
                              class="block w-full px-4 py-2 rounded-md shadow-sm
                                     text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                     {{ $errors->has('last_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                  :value="old('last_name')" required autocomplete="last_name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                <x-text-input id="email"
                                  class="block w-full px-4 py-2 rounded-md shadow-sm
                                         text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                         {{ $errors->has('email') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                  type="email"
                                  name="email"
                                  :value="old('email')"
                                  required
                                  autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            {{-- Password Field with Toggle and Live Validation --}}
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                <div class="relative">
                    <input id="password"
                        x-ref="passwordInput"
                        :type="passwordFieldType"
                        name="password"
                        x-model="password"
                        @input="validatePassword(password)"
                        required
                        autocomplete="new-password"
                        class="block w-full px-4 py-2 rounded-md shadow-sm
                                text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown pr-10
                                border"
                        :class="{
                            'border-green-500 ring-green-300': allPasswordCriteriaMet && password.length > 0,
                            'border-custom-ochre ring-custom-ochre': password.length > 0 && !allPasswordCriteriaMet && passwordConfirmationTouched
                        }"
                    />
                    <button type="button"
                            @click="passwordFieldType = (passwordFieldType === 'password' ? 'text' : 'password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-custom-dark-teal focus:outline-none">
                        <svg x-show="passwordFieldType === 'password'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.025m3.758-1.332A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.268 4.706M9.543 12.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" />
                        </svg>
                        <svg x-show="passwordFieldType === 'text'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-custom-ochre text-sm" />

                {{-- Password Criteria List --}}
                <ul class="text-sm mt-2 space-y-1">
                    <li :class="hasMinLength ? 'text-green-600 font-semibold' : 'text-custom-ochre'">
                        <span x-html="hasMinLength ? '&#10003;' : '&#10006;'"></span> At least 8 characters
                    </li>
                    <li :class="hasUpperCase ? 'text-green-600 font-semibold' : 'text-custom-ochre'">
                        <span x-html="hasUpperCase ? '&#10003;' : '&#10006;'"></span> At least one uppercase letter (A-Z)
                    </li>
                    <li :class="hasLowerCase ? 'text-green-600 font-semibold' : 'text-custom-ochre'">
                        <span x-html="hasLowerCase ? '&#10003;' : '&#10006;'"></span> At least one lowercase letter (a-z)
                    </li>
                    <li :class="hasDigit ? 'text-green-600 font-semibold' : 'text-custom-ochre'">
                        <span x-html="hasDigit ? '&#10003;' : '&#10006;'"></span> At least one digit (0-9)
                    </li>
                    <li :class="hasSpecialChar ? 'text-green-600 font-semibold' : 'text-custom-ochre'">
                        <span x-html="hasSpecialChar ? '&#10003;' : '&#10006;'"></span> At least one special character (!@#$...)
                    </li>
                </ul>
            </div>

            {{-- Confirm Password Field --}}
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-custom-dark-teal mb-1" />

                <div class="relative">
                    <input id="password_confirmation"
                        x-ref="passwordConfirmationInput"
                        :type="confirmPasswordFieldType"
                        name="password_confirmation"
                        x-model="passwordConfirmation"
                        required
                        autocomplete="new-password"
                        @input="passwordsMatch"
                        @blur="handlePasswordConfirmationBlur()"
                        class="block w-full px-4 py-2 rounded-md shadow-sm
                                text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown pr-10 border"
                        :class="{
                            'border-green-500 ring-green-300': passwordsMatch && passwordConfirmation.length > 0,
                            'border-custom-ochre ring-custom-ochre': passwordConfirmationTouched && !passwordsMatch
                        }"
                    />

                    {{-- Eye Icon Toggle Button for Confirm Password --}}
                    <button type="button"
                            @click="confirmPasswordFieldType = (confirmPasswordFieldType === 'password' ? 'text' : 'password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-custom-dark-teal focus:outline-none">
                        <svg x-show="confirmPasswordFieldType === 'password'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.025m3.758-1.332A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.268 4.706M9.543 12.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" />
                        </svg>
                        <svg x-show="confirmPasswordFieldType === 'text'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                {{-- Custom Mismatch Error Message --}}
                <p x-show="passwordConfirmationTouched && !passwordsMatch && password.length > 0" class="mt-2 text-custom-ochre text-sm">
                    Passwords do not match.
                </p>

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div class="block mt-6">
                <label for="terms_and_privacy" class="inline-flex items-center text-custom-dark-olive cursor-pointer">
                    <input id="terms_and_privacy" type="checkbox"
                           class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre
                                  {{ $errors->has('terms_and_privacy') ? 'border-custom-ochre' : '' }}"
                           name="terms_and_privacy" {{ old('terms_and_privacy') ? 'checked' : '' }} required>
                    <span class="ml-2 text-sm">
                        I agree to the <a href="{{ route('terms.show') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Terms of Service</a> and <a href="{{ route('policy.show') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Privacy Policy</a>.
                    </span>
                </label>
                <x-input-error :messages="$errors->get('terms_and_privacy')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div class="flex items-center justify-end mt-8">
                <a class="underline text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4 py-2 px-6 rounded-md text-white
                                         bg-custom-ochre hover:bg-custom-ochre-darker
                                         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                         font-semibold text-base transition ease-in-out duration-150">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>