<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TouchdCloud') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-100" x-cloak>

    {{-- Main Alpine.js data scope --}}
    <div x-data="{
        showLoginModal: false,
        showRegisterRoleModal: false,
        showInitialRegisterModal: false, // New: For basic Fname, Lname, Email, Password
        showCompleteProfileModal: false, // New: For first login profile completion
        currentRegisterRole: '', // To store the selected role (participant, coordinator, provider)
    }" class="min-h-screen flex flex-col">

        {{-- Updated Navbar: Sticky and Wider Spacing --}}
        <nav class="bg-white border-b border-gray-200 shadow-sm p-4 sticky top-0 z-50">
            <div class="container mx-auto flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-700 hover:text-indigo-900 transition duration-300">
                    <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-10 inline-block align-middle mr-3">
                    {{ config('app.name', 'TouchdCloud') }}
                </a>

                <div class="flex items-center space-x-10"> {{-- Increased space-x-6 to space-x-10 for wider spacing --}}
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">Home</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">About Us</a>
                    <a href="{{ route('listings') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">Listings</a>
                    {{-- <a href="{{ route('indiv-db') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">Participant</a> --}}
                    <a href="{{ route('sc-dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">Support Coordinator</a>

                    {{-- AUTHENTICATION LINKS --}}
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md font-medium text-lg transition duration-300">Logout</button>
                        </form>
                        <span class="text-gray-700 font-medium">Welcome, {{ Auth::user()->first_name }}!</span>
                    @else
                        {{-- Login Link that triggers the modal --}}
                        <a @click.prevent="showLoginModal = true" class="text-gray-700 hover:text-indigo-600 font-medium text-lg transition duration-300 cursor-pointer">Login</a>
                        {{-- Register Link that triggers the role selection modal --}}
                        <a @click.prevent="showRegisterRoleModal = true" class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md font-medium text-lg transition duration-300 cursor-pointer">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        {{-- START: Login Modal Structure (Existing) --}}
        <div class="relative z-40">
            <div x-show="showLoginModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showLoginModal"
                    @click.away="showLoginModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative overflow-y-auto max-h-screen">

                    <button @click="showLoginModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Login to Your Account</h2>

                    @include('auth.partials.login-form')

                </div>
            </div>
        </div>
        {{-- END: Login Modal Structure --}}


        {{-- START: Registration Role Selection Modal --}}
        <div class="relative z-40">
            <div x-show="showRegisterRoleModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showRegisterRoleModal"
                    @click.away="showRegisterRoleModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative overflow-y-auto max-h-screen text-center">

                    <button @click="showRegisterRoleModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Are you registering as:</h2>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- New: Participant link now sets role and opens initial registration modal --}}
                        <a @click.prevent="currentRegisterRole = 'participant'; showRegisterRoleModal = false; showInitialRegisterModal = true;"
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300 ease-in-out text-lg cursor-pointer">
                            NDIS Participant
                        </a>
                        <a @click.prevent="currentRegisterRole = 'coordinator'; showRegisterRoleModal = false; showInitialRegisterModal = true;"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300 ease-in-out text-lg cursor-pointer">
                            Support Coordinator
                        </a>
                        <a @click.prevent="currentRegisterRole = 'provider'; showRegisterRoleModal = false; showInitialRegisterModal = true;"
                           class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300 ease-in-out text-lg cursor-pointer">
                            NDIS Service Provider
                        </a>
                    </div>

                </div>
            </div>
        </div>
        {{-- END: Registration Role Selection Modal --}}


        {{-- START: Initial Registration Modal (First Name, Last Name, Email, Password) --}}
        <div class="relative z-40">
            <div x-show="showInitialRegisterModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showInitialRegisterModal"
                    @click.away="showInitialRegisterModal = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative overflow-y-auto max-h-screen">

                    <button @click="showInitialRegisterModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">
                        Register Account
                        <template x-if="currentRegisterRole === 'participant'"><span class="text-xl block">(NDIS Participant)</span></template>
                        <template x-if="currentRegisterRole === 'coordinator'"><span class="text-xl block">(Support Coordinator)</span></template>
                        <template x-if="currentRegisterRole === 'provider'"><span class="text-xl block">(Provider)</span></template>
                    </h2>

                    {{-- CONDITIONAL INCLUSION BASED ON currentRegisterRole --}}
                    <template x-if="currentRegisterRole === 'participant'">
                        @include('auth.register-individual')
                    </template>

                    <template x-if="currentRegisterRole === 'coordinator'">
                        @include('auth.register-coordinator')
                    </template>

                    <template x-if="currentRegisterRole === 'provider'">
                        @include('auth.register-provider')
                    </template>

                </div>
            </div>
        </div>
        {{-- END: Initial Registration Modal --}}


        {{-- START: Complete Profile Modal (Shown on First Login) --}}
        {{-- This modal needs to be conditionally shown based on backend logic. --}}
        {{-- Laravel provides a way to pass data from controller to view, like `session()->has('must_complete_profile')`. --}}
        @if (Auth::check() && !Auth::user()->profile_completed)
        <div class="relative z-40">
            <div x-show="showCompleteProfileModal"
                x-init="showCompleteProfileModal = true" {{-- Immediately show if user needs to complete profile --}}
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">

                <div x-show="showCompleteProfileModal"
                    @click.away="false" {{-- Prevent closing by clicking outside, force completion --}}
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 relative overflow-y-auto max-h-screen">

                    {{-- No close button here to force user to complete profile --}}
                    <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">
                        Complete Your Profile
                    </h2>

                    @include('profile.complete-participant-profile')

                </div>
            </div>
        </div>
        @endif
        {{-- END: Complete Profile Modal --}}


        <footer class="bg-gray-800 text-white p-6">
            <div class="container mx-auto text-center text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'TouchdCloud') }}. All rights reserved.
            </div>
        </footer>
    </div> {{-- Closing div for main x-data --}}
</body>
</html>