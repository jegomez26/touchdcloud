@section('title', 'Register - ' . config('app.name', 'SIL Match')) {{-- Sets the page title --}}

<x-guest-layout>
    {{-- Main container for the full-screen layout.
        Uses `h-auto md:h-screen` to fill viewport height on medium+ screens and center content.
        `flex`, `items-center`, `justify-center` will perfectly center the card within this height.
        `p-4 sm:p-6 lg:p-8` provides universal padding. --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The actual two-column card.
            `md:flex` enables the two-column layout on medium+ screens.
            `rounded-lg shadow-xl` for card styling.
            `md:h-full md:max-h-[85vh]` controls its height, making it fill most of the screen vertically.
            `overflow-hidden` ensures content stays within the card bounds. --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image/Illustration and Text (Hidden on small screens) --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                {{-- Dynamic background image for the scene --}}
                <!-- <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/register_illustration_background.png') }}');"> -->
                    {{-- Overlay to darken the image slightly and ensure text readability --}}
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                <!-- </div> -->

                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    {{-- The "Happy.png" image for the prominent illustration --}}
                    <img src="{{ asset('images/Happy.png') }}" alt="Registration Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">

                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Welcome, Participants!</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        Find people who are a good fit to share a home with. Create your profile, search listings, and connect with others who understand your needs and goals. Your journey to a supportive and comfortable living arrangement starts here.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        Your pathway to a more organized and connected experience begins here.
                    </p>
                </div>
            </div>

            {{-- Right Column: Registration Form --}}
            <div class="w-full md:w-2/3 bg-white p-6 sm:p-8 relative flex flex-col md:h-full overflow-y-auto">
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 sm:h-24 w-auto mb-3 sm:mb-4">
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal text-center">
                        Create Your Account
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Let's get you set up! Are you registering as a participant or a representative?
                    </p>
                </div>

                {{-- Close Button for returning to home/role selection --}}
                <a href="{{ route('home') }}?showRegisterModal=true"
                   class="absolute top-3 right-3 text-custom-light-grey-brown hover:text-custom-black text-3xl font-bold"
                   title="Back to role selection">
                    &times;
                </a>

                <form method="POST" action="{{ route('register.participant.create') }}"
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
                        selectedRole: 'participant', // Default to participant
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

                    {{-- Hidden input for role --}}
                    <input type="hidden" name="role" x-model="selectedRole">

                    {{-- New Full-Width Toggle Switch for Participant/Representative --}}
                    <div class="mb-6">
                        <div class="w-full h-12 flex rounded-full bg-gray-200 p-1 relative transition-colors duration-300">
                            {{-- Sliding background for the active choice --}}
                            <div class="absolute top-1 bottom-1 w-1/2 rounded-full bg-white shadow transition-transform duration-300 ease-in-out"
                                :class="selectedRole === 'representative' ? 'translate-x-full' : ''">
                            </div>

                            {{-- Participant Label/Button --}}
                            <button type="button" @click="selectedRole = 'participant'"
                                class="flex-1 z-10 rounded-full flex items-center justify-center font-semibold text-sm transition-colors duration-300 ease-in-out"
                                :class="selectedRole === 'participant' ? 'text-custom-dark-teal' : 'text-gray-500'">
                                Participant
                            </button>

                            {{-- Representative Label/Button --}}
                            <button type="button" @click="selectedRole = 'representative'"
                                class="flex-1 z-10 rounded-full flex items-center justify-center font-semibold text-sm transition-colors duration-300 ease-in-out"
                                :class="selectedRole === 'representative' ? 'text-custom-dark-teal' : 'text-gray-500'">
                                Representative
                            </button>
                        </div>
                        <p class="mt-4 text-sm sm:text-base text-custom-dark-olive text-center">
                            <span x-show="selectedRole === 'participant'">You'll create an account for yourself, the participant.</span>
                            <span x-show="selectedRole === 'representative'">You'll create an account for yourself, the representative, and link it to a participant later.</span>
                        </p>
                    </div>
                    {{-- End New Toggle Switch --}}

                    <p class="text-sm sm:text-base text-custom-dark-olive mb-4">
                        Please provide your details below to create your user account.
                    </p>

                    {{-- First Name and Last Name on one line --}}
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="flex-1">
                            <x-input-label for="first_name" :value="__('Your First Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="first_name" id="first_name"
                                          class="block w-full px-3 py-2 rounded-md shadow-sm
                                                 text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                 {{ $errors->has('first_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                          :value="old('first_name')" required autocomplete="first_name" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>

                        <div class="flex-1">
                            <x-input-label for="last_name" :value="__('Your Last Name')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                            <x-text-input type="text" name="last_name" id="last_name"
                                          class="block w-full px-3 py-2 rounded-md shadow-sm
                                                 text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                                 {{ $errors->has('last_name') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                          :value="old('last_name')" required autocomplete="last_name" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Your Email Address')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                        <x-text-input id="email"
                                      class="block w-full px-3 py-2 rounded-md shadow-sm
                                             text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                             {{ $errors->has('email') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required
                                      autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    {{-- Password Field with Toggle and Live Validation --}}
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
                                    'border-custom-ochre ring-custom-ochre': password.length > 0 && !allPasswordCriteriaMet
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
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Terms of Service</a> and <a href="{{ route('policy') }}" target="_blank" class="underline text-custom-dark-teal hover:text-custom-ochre">Privacy Policy</a>.
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms_and_privacy')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    <div class="flex items-center justify-end mt-6 sm:mt-8">
                        <a class="underline text-xs sm:text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ochre" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <x-primary-button class="ms-4 py-1.5 px-4 sm:py-2 sm:px-6 rounded-md text-white
                                                 bg-custom-ochre hover:bg-custom-ochre-darker
                                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ochre
                                                 font-semibold text-sm sm:text-base transition ease-in-out duration-150">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
