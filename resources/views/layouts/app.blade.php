<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIL Match') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/blue_logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Remove this entire style block after migrating custom colors to tailwind.config.js and adding custom scrollbar css to app.css --}}
    <style>
        /* Custom colors - ideally defined in tailwind.config.js */
        :root {
            --color-custom-light-cream: #f8f1e1;
            --color-custom-dark-teal: #33595a;
            --color-custom-dark-olive: #3e4732;
            --color-custom-ochre: #cc8e45;
            --color-custom-ochre-darker: #a67137;
            --color-custom-white: #ffffff;
            --color-custom-light-grey-brown: #bcbabb;
            --color-custom-light-grey-green: #dbe4d5;
            --color-custom-black: #000000; /* Added for contrast */
        }
        .bg-custom-light-cream { background-color: var(--color-custom-light-cream); }
        .text-custom-dark-teal { color: var(--color-custom-dark-teal); }
        .hover\:text-custom-dark-olive:hover { color: var(--color-custom-dark-olive); }
        .hover\:text-custom-ochre:hover { color: var(--color-custom-ochre); }
        .bg-custom-ochre { background-color: var(--color-custom-ochre); }
        .hover\:bg-custom-ochre-darker:hover { background-color: var(--color-custom-ochre-darker); }
        .bg-custom-dark-olive { background-color: var(--color-custom-dark-olive); }
        .bg-custom-white { background-color: var(--color-custom-white); }
        .text-custom-light-grey-brown { color: var(--color-custom-light-grey-brown); }
        .text-custom-black { color: var(--color-custom-black); } /* Adjusted for visibility */
        .border-custom-light-grey-green { border-color: var(--color-custom-light-grey-green); }
        .text-custom-dark-olive { color: var(--color-custom-dark-olive); }
        .border-custom-dark-teal { border-color: var(--color-custom-dark-teal); }
        .hover\:bg-custom-dark-teal-darker:hover { background-color: color-mix(in srgb, var(--color-custom-dark-teal) 80%, black); } /* Example darker shade */
        .shadow-lg-custom { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }

        /* General styles for x-cloak (Alpine.js) */
        /* [x-cloak] { display: none !important; } */

        /* Ensure smooth height transitions for mobile menu */
        .h-0 {
            transition: height 0.3s ease-out;
            overflow: hidden;
        }
        .h-auto {
            transition: height 0.3s ease-out;
        }

        /* Fix map z-index issues - ensure maps stay below header */
        .leaflet-container {
            z-index: 1 !important;
        }
        .leaflet-control-container {
            z-index: 2 !important;
        }
        .leaflet-popup {
            z-index: 3 !important;
        }
    </style>
</head>
<body class="antialiased bg-custom-light-cream">

    {{-- Main Alpine.js data scope --}}
    <div x-data="{
        showLoginModal: false,
        showRegisterRoleModal: {{ request()->query('showRegisterModal') || ($errors->any() && (request()->routeIs('register.individual.create') || request()->routeIs('register.coordinator.create') || request()->routeIs('register.provider.create'))) ? 'true' : 'false' }},
        showCompleteProfileModal: false,
        isMobileMenuOpen: false, // New state for mobile menu
    }" class="min-h-screen flex flex-col">


        {{-- Navbar --}}
        <nav class="p-4 sticky top-0 z-50
                    md:top-0 md:rounded-none md:max-w-none md:px-0 md:py-4 md:w-full
                    shadow-lg-custom"
             style="backdrop-filter: blur(5px); background-color: rgba(255, 255, 255, 0.2); border-color: rgba(206, 206, 206, 0.5); border-width: 1px; border-style: solid;">
            <div class="container mx-auto flex items-center justify-between opacity-100">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="text-2xl sm:text-3xl font-extrabold text-custom-dark-teal hover:text-custom-dark-olive transition duration-300 flex items-center">
                    <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SILMatch') }} Logo" class="h-8 sm:h-10 inline-block align-middle mr-2">
                    
                </a>

                {{-- Hamburger Menu Button (Mobile) --}}
                <div class="md:hidden">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-custom-dark-teal hover:text-custom-ochre focus:outline-none">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path x-show="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Desktop Navigation Links --}}
                <div class="hidden md:flex items-center space-x-6 lg:space-x-10">
                    <a href="{{ route('home') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Home</a>
                    <a href="{{ route('about') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">About Us</a>
                    <a href="{{ route('pricing') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Pricing</a>
                    <a href="{{ route('listings') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Listings</a>
                    <a href="{{ route('faqs') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">FAQs</a>
                    <a href="{{ route('sil-sda') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">SIL & SDA</a>
                    <a href="{{ route('contact') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Contact</a>
                    
                    {{-- AUTHENTICATION LINKS (Desktop) --}}
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 lg:px-4 lg:py-2 rounded-md font-medium text-base lg:text-lg transition duration-300">Logout</button>
                        </form>
                        <span class="text-custom-dark-teal font-medium text-base lg:text-lg">Welcome, {{ Auth::user()->first_name }}!</span>
                    @else
                        {{-- Login Link --}}
                        <a href="{{ route('login') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-base lg:text-lg transition duration-300">Login</a>
                        {{-- Register Link - now opens modal or redirects to appropriate registration page on validation error --}}
                        <a @click.prevent="showRegisterRoleModal = true" class="text-white bg-custom-ochre hover:bg-custom-ochre-darker px-3 py-1 lg:px-4 lg:py-2 rounded-full font-medium text-base lg:text-lg transition duration-300 cursor-pointer">Register</a>
                    @endauth
                </div>
            </div>

            {{-- Mobile Navigation Menu --}}
            <div x-show="isMobileMenuOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="md:hidden mt-4 space-y-2 text-center"
                 @click.away="isMobileMenuOpen = false"
                 x-bind:class="isMobileMenuOpen ? 'h-auto' : 'h-0'">
                <a href="{{ route('home') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Home</a>
                <a href="{{ route('about') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">About Us</a>
                <a href="{{ route('pricing') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Pricing</a>
                <a href="{{ route('listings') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Listings</a>
                <a href="{{ route('faqs') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">FAQs</a>
                <a href="{{ route('sil-sda') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">SIL & SDA</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Contact</a>

                {{-- AUTHENTICATION LINKS (Mobile) --}}
                @auth
                    <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Logout</button>
                    </form>
                    <span class="block px-4 py-2 text-custom-dark-teal font-medium text-lg">Welcome, {{ Auth::user()->first_name }}!</span>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300" @click="isMobileMenuOpen = false">Login</a>
                    <a @click.prevent="showRegisterRoleModal = true; isMobileMenuOpen = false" class="block text-white bg-custom-ochre hover:bg-custom-ochre-darker px-4 py-2 rounded-full font-medium text-lg transition duration-300 cursor-pointer mt-2 mx-auto max-w-xs">Register</a>
                @endauth
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        {{-- START: Registration Role Selection Modal --}}
        <div class="relative z-40">
            <div x-show="showRegisterRoleModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">

                <div x-show="showRegisterRoleModal"
                    @click.away="showRegisterRoleModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="bg-white rounded-2xl shadow-2xl max-w-sm sm:max-w-md md:max-w-4xl lg:max-w-6xl w-full p-6 sm:p-8 lg:p-12 relative overflow-y-auto max-h-[95vh] text-center">

                    {{-- Close Button --}}
                    <button @click="showRegisterRoleModal = false" 
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors duration-200 z-10">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Header --}}
                    <div class="mb-8">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4 sm:mb-6">
                            Sign Up
                        </h2>
                        <p class="text-lg sm:text-xl text-gray-600 mb-6 sm:mb-8 max-w-3xl mx-auto leading-relaxed">
                            Before signing up, please choose your role so we can set up your account in the best way for you. Select one of the options below:
                        </p>
                    </div>

                    {{-- Role Selection Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 lg:gap-10">
                        {{-- NDIS Participant --}}
                        <a href="{{ route('register.participant.create') }}"
                           class="group flex flex-col items-center p-6 sm:p-8 border-2 border-gray-200 rounded-2xl shadow-lg bg-gradient-to-br from-white to-gray-50 hover:shadow-2xl hover:border-[#cc8e45] transition-all duration-300 transform hover:-translate-y-2 hover:scale-105">
                            <div class="bg-gradient-to-br from-[#cc8e45] to-[#a67137] rounded-full w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-user text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#33595a] mb-3 group-hover:text-[#cc8e45] transition-colors duration-300">
                                NDIS Participant
                            </h3>
                            <p class="text-gray-600 text-center text-sm sm:text-base mb-6 flex-grow leading-relaxed">
                               Create your profile and share what you are looking for in a housemate or living arrangement. Search listings, connect with others, and find people who are the right fit for you.
                            </p>
                            <div class="mt-auto w-full">
                                <span class="inline-flex items-center justify-center w-full px-6 py-3 bg-[#cc8e45] text-white rounded-xl font-semibold hover:bg-[#a67137] transition-colors duration-300 text-sm sm:text-base shadow-md group-hover:shadow-lg">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Participant Sign Up
                                </span>
                            </div>
                        </a>

                        {{-- Support Coordinator --}}
                        <a href="{{ route('register.coordinator.create') }}"
                           class="group flex flex-col items-center p-6 sm:p-8 border-2 border-gray-200 rounded-2xl shadow-lg bg-gradient-to-br from-white to-gray-50 hover:shadow-2xl hover:border-[#cc8e45] transition-all duration-300 transform hover:-translate-y-2 hover:scale-105">
                            <div class="bg-gradient-to-br from-[#cc8e45] to-[#a67137] rounded-full w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-hands-helping text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#33595a] mb-3 group-hover:text-[#cc8e45] transition-colors duration-300">
                                Support Coordinator
                            </h3>
                            <p class="text-gray-600 text-center text-sm sm:text-base mb-6 flex-grow leading-relaxed">
                                Create and manage profiles for the people you support. Search listings, connect with participants, providers, and other coordinators, and help match people with the right living arrangements.
                            </p>
                            <div class="mt-auto w-full">
                                <span class="inline-flex items-center justify-center w-full px-6 py-3 bg-[#cc8e45] text-white rounded-xl font-semibold hover:bg-[#a67137] transition-colors duration-300 text-sm sm:text-base shadow-md group-hover:shadow-lg">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Coordinator Sign Up
                                </span>
                            </div>
                        </a>

                        {{-- NDIS Support and Accommodation Provider --}}
                        <a href="{{ route('register.provider.create') }}"
                           class="group flex flex-col items-center p-6 sm:p-8 border-2 border-gray-200 rounded-2xl shadow-lg bg-gradient-to-br from-white to-gray-50 hover:shadow-2xl hover:border-[#cc8e45] transition-all duration-300 transform hover:-translate-y-2 hover:scale-105">
                            <div class="bg-gradient-to-br from-[#cc8e45] to-[#a67137] rounded-full w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-building text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#33595a] mb-3 group-hover:text-[#cc8e45] transition-colors duration-300">
                                NDIS Support & Accommodation Provider
                            </h3>
                            <p class="text-gray-600 text-center text-sm sm:text-base mb-6 flex-grow leading-relaxed">
                                Create a provider account to connect with participants, Support Coordinators, and other providers. Upload depersonalised participant details to find matches, and if you have a Premium plan, list your available properties for public viewing.
                            </p>
                            <div class="mt-auto w-full">
                                <span class="inline-flex items-center justify-center w-full px-6 py-3 bg-[#cc8e45] text-white rounded-xl font-semibold hover:bg-[#a67137] transition-colors duration-300 text-sm sm:text-base shadow-md group-hover:shadow-lg">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Provider Sign Up
                                </span>
                            </div>
                        </a>
                    </div>

                    {{-- Footer Note --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Your information is secure and will only be used to create your account
                        </p>
                    </div>

                </div>
            </div>
        </div>
        {{-- END: Registration Role Selection Modal --}}


        {{-- START: Complete Profile Modal (Shown on First Login) --}}
        @if (Auth::check() && !Auth::user()->profile_completed)
        <div class="relative z-40">
            <div x-show="showCompleteProfileModal"
                x-init="showCompleteProfileModal = true"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-custom-dark-teal bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showCompleteProfileModal"
                    @click.away="false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-custom-white rounded-lg shadow-xl max-w-lg sm:max-w-xl md:max-w-2xl w-full p-6 relative overflow-y-auto max-h-screen">

                    <h2 class="text-2xl sm:text-3xl font-bold text-custom-black text-center mb-6">
                        Complete Your Profile
                    </h2>
                    @include('profile.complete-participant-profile', [
                        'user' => Auth::user(),
                        'participant' => Auth::user()->participant // Assuming the participant relationship exists
                    ])

                </div>
            </div>
        </div>
        @endif
        {{-- END: Complete Profile Modal --}}


        {{-- Footer - using a dark color from the palette --}}
        <footer class="bg-custom-light-cream text-custom-dark-teal p-6 mt-auto">
            <div class="container mx-auto text-center text-sm ">
                &copy; {{ date('Y') }} {{ config('app.name', 'SIL Match') }}. All rights reserved.
            </div>
        </footer>
    </div> {{-- Closing div for main x-data --}}
</body>
</html>