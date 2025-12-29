@section('title', 'Forgot Password - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    {{-- Main container for the full-screen layout --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The two-column card layout --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image and Text --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    <img src="{{ asset('images/Happy.png') }}" alt="Password Reset Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Reset Your Password</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        No worries! Enter your email address and we'll send you a secure link to reset your password.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        You'll be back to your account in no time.
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
                        Forgot Your Password?
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        No problem. Just let us know your email address and we will email you a password reset link.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6 text-center text-sm text-custom-ochre" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4 sm:space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-xs sm:text-sm font-semibold text-custom-dark-teal mb-1" />
                        <x-text-input id="email" 
                            class="block w-full px-3 py-2 rounded-md shadow-sm
                                   text-sm sm:text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown
                                   {{ $errors->has('email') ? 'border-custom-ochre' : 'border-custom-light-grey-green' }}
                                   focus:border-custom-ochre focus:ring-custom-ochre transition ease-in-out duration-150"
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-2 text-custom-ochre text-xs sm:text-sm" />
                    </div>

                    <div class="flex items-center justify-end mt-6 sm:mt-8">
                        <a href="{{ route('login') }}" class="underline text-xs sm:text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre mr-4">
                            {{ __('Back to Login') }}
                        </a>
                        <x-primary-button class="py-1.5 px-4 sm:py-2 sm:px-6 rounded-md text-white
                                                 bg-custom-ochre hover:bg-custom-ochre-darker
                                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                 font-semibold text-sm sm:text-base transition ease-in-out duration-150">
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
