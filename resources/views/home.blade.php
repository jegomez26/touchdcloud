@extends('layouts.app')

@section('content')

    <section class="relative bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-24 sm:py-32 overflow-hidden">
        <div class="absolute inset-0 z-0 opacity-10">
            <img src="{{ asset('images/hero-bg2.jpg')}}" alt="Background Pattern" class="w-full h-full object-cover">
        </div>
        <div class="container mx-auto px-6 relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 animate-fade-in-up">
                Welcome to <span class="block mt-2 text-indigo-200">Touch D Cloud</span>
            </h1>
            <p class="text-lg sm:text-xl text-indigo-100 mb-10 max-w-3xl mx-auto animate-fade-in-up delay-200">
                Your trusted platform for NDIS participant accommodation and support coordination. We connect you with the right resources to live independently and comfortably.
            </p>
            <a href="{{ route('listings') }}" class="inline-block bg-white text-indigo-700 hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg sm:text-xl shadow-xl transition duration-300 ease-in-out transform hover:scale-105 animate-fade-in-up delay-400">
                Find Your Accommodation Today!
            </a>
        </div>
    </section>

    <section class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-12">How Touch D Cloud Helps You</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-gray-50 p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="https://cdn-icons-png.flaticon.com/512/2225/2225617.png" alt="Icon" class="mx-auto mb-6 w-24 h-24 object-contain">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Discover Tailored Accommodation</h3>
                    <p class="text-gray-600">Browse a curated selection of NDIS-friendly homes, designed to meet diverse needs and preferences.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="https://static.vecteezy.com/system/resources/previews/016/132/724/non_2x/helpdesk-icon-in-flat-style-headphone-illustration-on-white-isolated-background-chat-operator-business-concept-vector.jpg" alt="Icon" class="mx-auto mb-6 w-24 h-24 object-contain">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Seamless Support Coordination</h3>
                    <p class="text-gray-600">Connect with experienced support coordinators who can guide you through the NDIS process effortlessly.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRWZJXxPv8CIL5csW3qFYRvAyfQUjFY4m1Lrw&s" alt="Icon" class="mx-auto mb-6 w-24 h-24 object-contain">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Achieve Independent Living</h3>
                    <p class="text-gray-600">Empowering NDIS participants to live fulfilling lives with the right support and environment.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24 bg-gray-100">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="text-center md:text-left">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-6">Our Commitment to You</h2>
                <p class="text-lg text-gray-700 mb-6">
                    At Touch D Cloud, we understand the importance of finding the perfect place to call home and getting the right support. Our platform is built on principles of accessibility, transparency, and empowerment.
                </p>
                <p class="text-md text-gray-600">
                    We're dedicated to simplifying the search for NDIS accommodation and support services, providing a user-friendly experience that puts your needs first.
                </p>
                <a href="{{ route('about') }}" class="inline-block mt-8 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out">
                    Learn More About Us
                </a>
            </div>
            <div class="flex justify-center">
                <img src="https://via.placeholder.com/600x400/6366F1/FFFFFF?text=Our+Mission" alt="Our Mission Image" class="rounded-lg shadow-xl max-w-full h-auto">
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-12">What Our Users Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="bg-gray-50 p-8 rounded-lg shadow-md">
                    <p class="text-lg text-gray-700 mb-6 italic">"Touch D Cloud made finding NDIS accommodation so much easier. The process was smooth, and the support was invaluable!"</p>
                    <div class="flex items-center justify-center">
                        <img src="https://via.placeholder.com/60/9CA3AF/FFFFFF?text=User1" alt="User Avatar" class="w-16 h-16 rounded-full mr-4 object-cover">
                        <div>
                            <p class="font-semibold text-gray-800">Jane Doe</p>
                            <p class="text-sm text-gray-500">NDIS Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-md">
                    <p class="text-lg text-gray-700 mb-6 italic">"As a support coordinator, Touch D Cloud has become my go-to resource for connecting participants with suitable housing options. Highly recommended!"</p>
                    <div class="flex items-center justify-center">
                        <img src="https://via.placeholder.com/60/9CA3AF/FFFFFF?text=User2" alt="User Avatar" class="w-16 h-16 rounded-full mr-4 object-cover">
                        <div>
                            <p class="font-semibold text-gray-800">John Smith</p>
                            <p class="text-sm text-gray-500">Support Coordinator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-indigo-700 py-16 sm:py-24 text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Ready to Find Your Ideal Home?</h2>
            <p class="text-lg text-indigo-100 mb-10 max-w-2xl mx-auto">
                Join hundreds of NDIS participants and support coordinators who trust Touch D Cloud for their accommodation needs.
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-700 hover:bg-gray-100 font-bold py-4 px-10 rounded-full text-xl shadow-xl transition duration-300 ease-in-out transform hover:scale-105">
                Get Started Today!
            </a>
        </div>
    </section>

@endsection