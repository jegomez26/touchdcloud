<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TouchdCloud') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-custom-light-cream" x-cloak>

    {{-- Main Alpine.js data scope --}}
    <div x-data="{
        showLoginModal: false,
        // showRegisterRoleModal will be true if the query param is present OR if there were registration errors
        showRegisterRoleModal: {{ request()->query('showRegisterModal') || ($errors->any() && (request()->routeIs('register.individual.create') || request()->routeIs('register.coordinator.create') || request()->routeIs('register.provider.create'))) ? 'true' : 'false' }},
        showCompleteProfileModal: false,
    }" class="min-h-screen flex flex-col">

        {{-- Navbar: bg-white remains, text colors adjusted --}}
        <nav class="p-4 sticky top-5 z-50 rounded-full max-w-35 border border-solid shadow-lg" style="backdrop-filter: blur(5px); padding:10px; margin-left:50px; margin-right:50px; background-color: rgba(255, 255, 255, 0.2); border-color: rgba(206, 206, 206, 0.5);">
            <div class="container mx-auto flex justify-between items-center opacity-100">
                <a href="{{ route('home') }}" class="text-3xl font-extrabold text-custom-dark-teal hover:text-custom-dark-olive transition duration-300">
                    <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-10 inline-block align-middle mr-3">
                    {{ config('app.name', 'TouchdCloud') }}
                </a>

                <div class="flex items-center space-x-10">
                    <a href="{{ route('home') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Home</a>
                    <a href="{{ route('about') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">About Us</a>
                    <a href="{{ route('listings') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Listings</a>
                    <a href="{{ route('company-dashboard') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Company</a>
                    <a href="{{ route('sc-dashboard') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Support Coordinator</a>

                    {{-- AUTHENTICATION LINKS --}}
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md font-medium text-lg transition duration-300">Logout</button>
                        </form>
                        <span class="text-custom-dark-teal font-medium">Welcome, {{ Auth::user()->first_name }}!</span>
                    @else
                        {{-- Login Link --}}
                        <a href="{{ route('login') }}" class="text-custom-dark-teal hover:text-custom-ochre font-medium text-lg transition duration-300">Login</a>
                        {{-- Register Link - now opens modal or redirects to appropriate registration page on validation error --}}
                        <a @click.prevent="showRegisterRoleModal = true" class=" text-white bg-custom-ochre hover:bg-custom-ochre-darker px-4 py-2 rounded-full font-medium text-lg transition duration-300 cursor-pointer">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            {{-- This is where your individual registration pages will be injected --}}
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
                class="fixed inset-0 bg-custom-dark-teal bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showRegisterRoleModal"
                    @click.away="showRegisterRoleModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-custom-white rounded-lg shadow-xl max-w-4xl w-full p-8 relative overflow-y-auto max-h-[90vh] text-center">

                    <button @click="showRegisterRoleModal = false" class="absolute top-3 right-3 text-custom-light-grey-brown hover:text-custom-black text-3xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-3xl font-extrabold text-custom-black mb-6">
                        Sign Up
                    </h2>
                    <p class="mt-2 text-lg text-custom-dark-teal mb-8">
                        Before signing up please let us know whether you are an NDIS Participant, Support Coordinator or NDIS Accommodation Provider
                        by selecting one of the options below:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
                        {{-- NDIS Participant --}}
                        {{-- Now linking directly to the registration route --}}
                        <a href="{{ route('register.individual.create') }}"
                           class="flex flex-col items-center p-6 border border-custom-light-grey-green rounded-lg shadow-lg bg-custom-light-cream hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="text-custom-dark-teal mb-4">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-custom-dark-teal mb-2">NDIS Participant</h3>
                            <p class="text-custom-dark-olive text-center text-sm mb-4 flex-grow">
                                Sign up to create a profile. Enter information about what you are looking for to find the home that is right for you.
                            </p>
                            <span class="mt-auto px-6 py-3 border border-custom-dark-teal text-custom-dark-teal rounded-full font-semibold hover:bg-custom-dark-teal-darker hover:text-white transition duration-300">
                                Participant Sign Up
                            </span>
                        </a>

                        {{-- Support Coordinator --}}
                        <a href="{{ route('register.coordinator.create') }}"
                           class="flex flex-col items-center p-6 border border-custom-light-grey-green rounded-lg shadow-lg bg-custom-light-cream hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="text-custom-dark-teal mb-4">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.005v-2.004m0 0v-2.004m0 0V8.995m0 0h.01M12 18.001c-3.14 0-5.7-2.56-5.7-5.7s2.56-5.7 5.7-5.7 5.7 2.56 5.7 5.7-2.56 5.7-5.7 5.7z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-custom-dark-teal mb-2">Support Coordinator</h3>
                            <p class="text-custom-dark-olive text-center text-sm mb-4 flex-grow">
                                Sign up to create profiles for the people you support. Enter their needs and preferences and find appropriate housing options.
                            </p>
                            <span class="mt-auto px-6 py-3 border border-custom-dark-teal text-custom-dark-teal rounded-full font-semibold hover:bg-custom-dark-teal-darker hover:text-white transition duration-300">
                                Coordinator Sign Up
                            </span>
                        </a>

                        {{-- NDIS Accommodation Provider --}}
                        <a href="{{ route('register.provider.create') }}"
                           class="flex flex-col items-center p-6 border border-custom-light-grey-green rounded-lg shadow-lg bg-custom-light-cream hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="text-custom-dark-teal mb-4">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 12h.01M12 12h.01M10 16h.01"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-custom-dark-teal mb-2">NDIS Accommodation Provider</h3>
                            <p class="text-custom-dark-olive text-center text-sm mb-4 flex-grow">
                                Sign up as a Provider and subscribe to a plan if you intend to list more than two properties. Receive Housing Seeker enquiries and fill your vacancies.
                            </p>
                            <span class="mt-auto px-6 py-3 border border-custom-dark-teal text-custom-dark-teal rounded-full font-semibold hover:bg-custom-dark-teal-darker hover:text-white transition duration-300">
                                Provider Sign Up
                            </span>
                        </a>
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
                    class="bg-custom-white rounded-lg shadow-xl max-w-2xl w-full p-6 relative overflow-y-auto max-h-screen">

                    <h2 class="text-3xl font-bold text-custom-black text-center mb-6">
                        Complete Your Profile
                    </h2>
                    @include('profile.complete-participant-profile')

                </div>
            </div>
        </div>
        @endif
        {{-- END: Complete Profile Modal --}}


        {{-- Footer - using a dark color from the palette --}}
        <footer class="bg-custom-dark-olive text-white p-6">
            <div class="container mx-auto text-center text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'TouchdCloud') }}. All rights reserved.
            </div>
        </footer>
    </div> {{-- Closing div for main x-data --}}
</body>
</html>