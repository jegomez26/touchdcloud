@section('title', 'Login - ' . config('app.name', 'SIL Match'))

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
             `md:max-h-[85vh]` reduces the max height of the entire card on larger screens.
             `overflow-hidden` ensures nothing escapes this boundary and causes unwanted page scroll. --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image/Illustration and Text (Hidden on small screens) --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                {{-- Dynamic background image for the scene --}}
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login_illustration_background.png') }}');">
                    {{-- Overlay to darken the image slightly and ensure text readability --}}
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    {{-- The "Happy.png" image for the prominent illustration --}}
                    <img src="{{ asset('images/Happy.png') }}" alt="Login Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">

                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Welcome Back!</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        Continue your search, check messages, and manage your matches all in one place.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        Your seamless experience awaits.
                    </p>
                </div>
            </div>

            {{-- Right Column: Login Form --}}
            <div class="w-full md:w-2/3 bg-white p-6 sm:p-8 relative flex flex-col md:h-full overflow-y-auto">
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 sm:h-24 w-auto mb-3 sm:mb-4">
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal text-center">
                        Login to Your Account
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Access your personalized dashboard.
                    </p>
                </div>

                <x-auth-session-status class="mb-6 text-center text-sm text-custom-ochre" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6"
                    x-data="{
                        passwordFieldType: 'password',
                    }"
                >
                    @csrf

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
                                        autofocus
                                        autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    {{-- Password Field with Toggle --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                        <div class="relative">
                            <input id="password"
                                :type="passwordFieldType"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="block w-full px-3 py-2 rounded-md shadow-sm
                                        text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown pr-10
                                        {{ $errors->has('password') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}
                                        focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150"
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
                    </div>

                    <div class="flex items-center justify-between mt-4 sm:mt-6">
                        <label for="remember_me" class="inline-flex items-center text-sm text-custom-dark-olive cursor-pointer">
                            <input id="remember_me" type="checkbox"
                                   class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre"
                                   name="remember">
                            <span class="ms-2">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="underline text-xs sm:text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-6 sm:mt-8">
                        <x-primary-button class="w-full justify-center py-1.5 px-4 sm:py-2 sm:px-6 rounded-md text-white
                                                bg-custom-ochre hover:bg-custom-ochre-darker
                                                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                font-semibold text-sm sm:text-base transition ease-in-out duration-150">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>

                    {{-- Link to Register if not already logged in --}}
                    @if (!Auth::check())
                    <div class="text-center mt-4 sm:mt-6">
                        <p class="text-custom-dark-olive text-sm sm:text-base">
                            Don't have an account?
                            {{-- Changed href to home route with a query parameter --}}
                            <a href="{{ route('home', ['showRegisterModal' => true]) }}" class="font-medium text-custom-dark-teal hover:text-custom-ochre underline">
                                Register here
                            </a>
                        </p>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>