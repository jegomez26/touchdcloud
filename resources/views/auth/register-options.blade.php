@extends('layouts.app') {{-- Extend your main layout --}}

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-80px)] py-12 px-4 sm:px-6 lg:px-8">
    <div class="glass-container max-w-4xl w-full p-8 space-y-8 text-center">
        <button onclick="window.location='{{ route('home') }}'" class="absolute top-4 right-4 text-gray-700 hover:text-gray-900 text-4xl font-bold p-2" aria-label="Close">
            &times;
        </button>
        <h2 class="text-3xl font-extrabold text-gray-900">
            Sign Up
        </h2>
        <p class="mt-2 text-lg text-gray-700">
            Before signing up please let us know whether you are a Housing Seeker, Supporter or Housing Provider
            by selecting one of the options below:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
            {{-- Housing Seeker (Participant) --}}
            <div class="flex flex-col items-center p-6 border border-gray-300 rounded-lg shadow-lg bg-white bg-opacity-80 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-indigo-600 mb-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Housing Seeker</h3>
                <p class="text-gray-600 text-center text-sm mb-4 flex-grow">
                    Sign up to create a profile. Enter information about what you are looking for to find the home that is right for you.
                </p>
                <a href="{{ route('register.individual.create') }}" class="mt-auto px-6 py-3 border border-indigo-600 text-indigo-600 rounded-full font-semibold hover:bg-indigo-50 transition duration-300">
                    Seeker Sign Up
                </a>
            </div>

            {{-- Housing Supporter (Support Coordinator) --}}
            <div class="flex flex-col items-center p-6 border border-gray-300 rounded-lg shadow-lg bg-white bg-opacity-80 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-green-600 mb-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20.005v-2.004m0 0v-2.004m0 0V8.995m0 0h.01M12 18.001c-3.14 0-5.7-2.56-5.7-5.7s2.56-5.7 5.7-5.7 5.7 2.56 5.7 5.7-2.56 5.7-5.7 5.7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Housing Supporter</h3>
                <p class="text-gray-600 text-center text-sm mb-4 flex-grow">
                    Sign up to create profiles for the people you support. Enter their needs and preferences and find appropriate housing options.
                </p>
                <a href="{{ route('register.coordinator.create') }}" class="mt-auto px-6 py-3 border border-green-600 text-green-600 rounded-full font-semibold hover:bg-green-50 transition duration-300">
                    Supporter Sign Up
                </a>
            </div>

            {{-- Housing Provider (Provider) --}}
            <div class="flex flex-col items-center p-6 border border-gray-300 rounded-lg shadow-lg bg-white bg-opacity-80 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-blue-600 mb-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 12h.01M12 12h.01M10 16h.01"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Housing Provider</h3>
                <p class="text-gray-600 text-center text-sm mb-4 flex-grow">
                    Sign up as a Provider and subscribe to a plan if you intend to list more than two properties. Receive Housing Seeker enquiries and fill your vacancies.
                </p>
                <a href="{{ route('register.provider.create') }}" class="mt-auto px-6 py-3 border border-blue-600 text-blue-600 rounded-full font-semibold hover:bg-blue-50 transition duration-300">
                    Provider Sign Up
                </a>
            </div>
        </div>
    </div>
</div>
@endsection