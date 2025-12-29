@section('title', 'Verify Email - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    {{-- Main container for the full-screen layout --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The two-column card layout --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image and Text --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    <img src="{{ asset('images/Happy.png') }}" alt="Email Verification Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Almost There!</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        We've sent you a verification link. Check your email and click the link to complete your account setup.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        Your account will be ready in just a moment.
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
                        Verify Your Email Address
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Thanks for signing up! Before getting started, please verify your email address.
                    </p>
                </div>

                <div class="mb-6 text-sm text-custom-dark-olive leading-relaxed text-center">
                    {{ __('We\'ve sent a verification link to your email address. Please check your inbox and click the link to verify your account.') }}
                    <br><br>
                    {{ __('If you didn\'t receive the email, we can send you another one.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 font-medium text-sm text-green-600 p-4 bg-green-50 rounded-lg border border-green-200 text-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-6 space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-primary-button class="w-full justify-center py-2 px-4 rounded-md text-white
                                                 bg-custom-ochre hover:bg-custom-ochre-darker
                                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                 font-semibold text-base transition ease-in-out duration-150">
                            <i class="fas fa-envelope mr-2"></i>
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full justify-center py-2 px-4 rounded-md
                                                   text-sm text-custom-dark-teal hover:text-custom-ochre underline
                                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                   font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>