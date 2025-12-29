@section('title', 'Account Pending Approval - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    {{-- Main container for the full-screen layout --}}
    <div class="relative h-auto md:h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- The two-column card layout --}}
        <div class="relative w-full md:flex rounded-lg shadow-xl md:h-full md:max-h-[85vh] overflow-hidden" style="max-width: 1200px;">

            {{-- Left Column: Image and Text --}}
            <div class="hidden md:flex flex-col w-full md:w-1/2 bg-[#2D4A80] p-6 lg:p-10 items-center justify-center text-white text-center h-full">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full">
                    <img src="{{ asset('images/Happy.png') }}" alt="Pending Approval Illustration" class="max-w-[150px] sm:max-w-xs h-auto object-contain mb-6 sm:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-4 drop-shadow-lg">Under Review</h3>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 drop-shadow">
                        Thank you for registering as a Support Coordinator. Your account is currently being reviewed by our administrators.
                    </p>
                    <p class="text-xs sm:text-sm lg:text-md font-semibold drop-shadow">
                        We'll notify you as soon as your account is approved.
                    </p>
                </div>
            </div>

            {{-- Right Column: Content --}}
            <div class="w-full md:w-2/3 bg-white p-6 sm:p-8 relative flex flex-col md:h-full overflow-y-auto">
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 sm:h-24 w-auto mb-3 sm:mb-4">
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal text-center">
                        Account Pending Approval
                    </h2>
                    <p class="mt-1 sm:mt-2 text-custom-dark-olive text-center text-sm sm:text-base">
                        Your Support Coordinator account is currently under review.
                    </p>
                </div>

                <div class="text-center mb-8">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-clock text-yellow-600 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">Review in Progress</h3>
                        <p class="text-yellow-700 text-sm leading-relaxed">
                            Thank you for registering as a Support Coordinator. Your account is currently under review by our administrators. 
                            You will receive an email notification once your account has been approved.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-800 mb-3">What's Next?</h3>
                        <ul class="text-blue-700 text-sm text-left space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mr-2 mt-1"></i>
                                <span>We'll review your registration details and credentials</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-envelope text-blue-500 mr-2 mt-1"></i>
                                <span>You'll receive an email notification when approved</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-sign-in-alt text-blue-500 mr-2 mt-1"></i>
                                <span>You can then log in and start using the platform</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="text-center mb-4">
                        <p class="text-sm text-custom-dark-olive">
                            You can try logging in later to check your status.
                        </p>
                    </div>
                    
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-primary-button class="w-full justify-center py-2 px-4 rounded-md text-white
                                                     bg-custom-ochre hover:bg-custom-ochre-darker
                                                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                     font-semibold text-base transition ease-in-out duration-150">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </x-primary-button>
                        </form>
                        
                        <a href="{{ route('login') }}" class="w-full justify-center py-2 px-4 rounded-md text-center block
                                                               text-sm text-custom-dark-teal hover:text-custom-ochre underline
                                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                                               font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Try Logging In Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>