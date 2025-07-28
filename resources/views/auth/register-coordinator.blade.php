@section('title', 'Register as Support Coordinator - ' . config('app.name', 'TouchdCloud'))

<x-guest-layout>
    {{-- Main container for the full-screen layout.
         Uses `h-screen` (100% viewport height) on medium+ screens to ensure it fills the available screen
         and prevents overall page scrolling from its content.
         `flex`, `items-center`, `justify-center` will perfectly center the card within this height.
         `p-4 sm:p-6 lg:p-8` provides universal padding. --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The actual two-column card.
             `md:h-full` makes the card fill the height of its parent (the `h-screen` outer container)
             minus the padding.
             **UPDATED:** `md:max-h-[85vh]` reduces the max height of the entire card on larger screens.
             `overflow-hidden` ensures nothing escapes this boundary and causes unwanted page scroll. --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image/Illustration and Text --}}
            {{-- Hidden on small screens, flex column on medium and up.
                 `h-full` is crucial here to make it expand to the height of its parent (the card container). --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                {{-- Dynamic background image for the scene --}}
                <div class="absolute inset-0 bg-cover bg-center" >
                    {{-- Overlay to darken the image slightly and ensure text readability --}}
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    {{-- The "Happy.png" image for the prominent illustration --}}
                    <img src="{{ asset('images/Happy.png') }}" alt="Support Coordinator Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">

                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Welcome, Support Coordinators!</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        Unlock a seamless way to manage your NDIS participants, streamline your administrative tasks, and connect with a supportive community. Your journey to more effective coordination begins here.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        Join us and make a real difference, one participant at a time.
                    </p>
                </div>
            </div>

            {{-- Right Column: Form --}}
            {{-- Takes full width on small screens, half width on medium and up.
                 `h-full` makes it fill the available height of its parent.
                 `overflow-y-auto` then ensures that *only this column* scrolls if its content
                 exceeds the available height. --}}
            <div class="w-full md:w-2/3 bg-white p-6 sm:p-8 relative flex flex-col md:h-full overflow-y-auto">
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-20 sm:h-24 w-auto mb-3 sm:mb-4">
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal text-center">
                        Register as a Support Coordinator
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Create your Support Coordinator account to get started.
                    </p>
                </div>

                <form method="POST" action="{{ route('register.coordinator.store') }}"
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
                    class="space-y-4 sm:space-y-6"
                    x-data="{
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

                    {{-- Hidden role input for 'coordinator' --}}
                    <input type="hidden" name="role" value="coordinator">

                    {{-- Support Coordinator Details --}}
                    <h3 class="text-lg sm:text-xl font-extrabold text-custom-dark-teal border-b border-custom-light-grey-green pb-2 sm:pb-3 mb-3 sm:mb-4">Your Details (Support Coordinator)</h3>

                    {{-- First Name & Last Name (On one line) --}}
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="flex-1">
                            <x-input-label for="first_name" :value="__('First Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="first_name" id="first_name"
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                                text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                {{ $errors->has('first_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('first_name')" required autocomplete="given-name" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>

                        <div class="flex-1">
                            <x-input-label for="last_name" :value="__('Last Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="last_name" id="last_name"
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                                text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                {{ $errors->has('last_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('last_name')" required autocomplete="family-name" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>
                    </div>

                    {{-- Affiliated Company Name & ABN (On one line) --}}
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="flex-1">
                            <x-input-label for="company_name" :value="__('Affiliated Company Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="company_name" id="company_name"
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                                text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                {{ $errors->has('company_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('company_name')" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>

                        <div class="flex-1">
                            <x-input-label for="abn" :value="__('ABN (Australian Business Number)')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="abn" id="abn"
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                                text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                {{ $errors->has('abn') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        :value="old('abn')" required />
                            <x-input-error :messages="$errors->get('abn')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                        <x-text-input id="email"
                                        class="block w-full px-3 py-2 rounded-md shadow-sm
                                            text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                            {{ $errors->has('email') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                        type="email"
                                        name="email"
                                        :value="old('email')"
                                        required
                                        autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    {{-- Password Field with Toggle and Live Validation (as per reference) --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                        <div class="relative">
                            <input id="password"
                                x-ref="passwordInput"
                                :type="passwordFieldType"
                                name="password"
                                x-model="password"
                                @input="validatePassword(password)"
                                required
                                autocomplete="new-password"
                                class="block w-full px-3 py-2 rounded-md shadow-sm
                                        text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown pr-10
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
                        <x-input-error :messages="$errors->get('password')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />

                        {{-- Password Criteria List --}}
                        <ul class="text-xs sm:text-sm mt-1 sm:mt-2 space-y-0.5 sm:space-y-1">
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
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />

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
                                class="block w-full px-3 py-2 rounded-md shadow-sm
                                        text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown pr-10 border"
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
                        <p x-show="passwordConfirmationTouched && !passwordsMatch && password.length > 0" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm">
                            Passwords do not match.
                        </p>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    <div class="block mt-4 sm:mt-6">
                        <label for="terms_and_privacy" class="inline-flex items-center text-custom-dark-olive cursor-pointer">
                            <input id="terms_and_privacy" type="checkbox"
                                class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre
                                        {{ $errors->has('terms_and_privacy') ? 'border-custom-ochre' : '' }}"
                                name="terms_and_privacy" {{ old('terms_and_privacy') ? 'checked' : '' }} required>
                            <span class="ml-2 text-xs sm:text-sm">
                                I agree to the <a href="{{ route('terms.show') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Terms of Service</a> and <a href="{{ route('policy.show') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Privacy Policy</a>.
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