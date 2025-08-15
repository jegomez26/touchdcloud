@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-0 sm:py-0 md:py-0 lg:py-0 mt-16 md:mt-10 font-sans"> {{-- Increased overall padding, using custom font-sans --}}

        {{-- Hero Section: Image Slider and About Us Text --}}
        <section class="flex flex-col md:flex-row items-center justify-between gap-10 mb-16 lg:mb-24">
            {{-- Changed this div to w-full on small screens, then md:w-1/2 --}}
            <div class="w-full md:w-1/2 p-4">
                {{-- Retained background image on this div, adding a subtle overlay for depth --}}
                <div class="relative w-full h-full bg-no-repeat bg-contain bg-center rounded-2xl shadow-md-light overflow-hidden" style="background-image: url('{{ asset('images/SIL_Match_01.png') }}');">
                    <div class="absolute inset-0 bg-primary-dark opacity-10 rounded-2xl"></div> {{-- Subtle dark overlay for depth --}}
                    <div id="image-slider" class="relative w-full overflow-hidden rounded-2xl" data-carousel="slide" data-carousel-interval="5000">
                        <div class="relative h-72 sm:h-96 md:h-112 lg:h-[500px]"> {{-- Increased responsive height for slider --}}
                            {{-- Image 1 --}}
                            <div class="duration-700 ease-in-out absolute inset-0 transition-transform transform" data-carousel-item="active">
                                <img src="{{ asset('images/OfficeFinal.png') }}" class="block w-full h-full object-right-bottom object-cover" alt="Modern Office Space">
                            </div>
                            {{-- Image 2 --}}
                            <div class="duration-700 ease-in-out absolute inset-0 transition-transform transform" data-carousel-item>
                                <img src="{{ asset('images/BuildFinal.png') }}" class="block w-full h-full object-cover" alt="Construction/Building">
                            </div>
                            {{-- Image 3 --}}
                            <div class="duration-700 ease-in-out absolute inset-0 transition-transform transform" data-carousel-item>
                                <img src="{{ asset('images/FlyFinal.png') }}" class="block w-full h-full object-cover" alt="Aerial View">
                            </div>
                            {{-- Image 4 --}}
                            <div class="duration-700 ease-in-out absolute inset-0 transition-transform transform" data-carousel-item>
                                <img src="{{ asset('images/SeatingFinal.png') }}" class="block w-full h-full object-cover" alt="Comfortable Seating Area">
                            </div>
                            {{-- Image 5 --}}
                            <div class="duration-700 ease-in-out absolute inset-0 transition-transform transform" data-carousel-item>
                                <img src="{{ asset('images/SearchFinal.png') }}" class="block w-full h-full object-cover" alt="People searching online">
                            </div>
                        </div>

                        {{-- Slider controls --}}
                        <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                            <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-custom-white/30 group-hover:bg-custom-white/50 focus:ring-4 focus:ring-custom-white focus:outline-none transition duration-300">
                                <svg class="w-5 h-5 text-custom-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                                </svg>
                            </span>
                        </button>
                        <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                            <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-custom-white/30 group-hover:bg-custom-white/50 focus:ring-4 focus:ring-custom-white focus:outline-none transition duration-300">
                                <svg class="w-5 h-5 text-custom-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 9l4-4-4-4"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-4 text-center md:text-left">
                <h1 class="text-5xl lg:text-6xl font-extrabold text-primary-dark mb-6 leading-tight">About <span class="text-primary-light">SIL Match</span></h1>
                <p class="text-lg text-text-dark mb-4 leading-relaxed">
                    <b class="font-semibold text-primary-dark">SIL Match</b> is dedicated to simplifying the process of finding suitable accommodation and connecting with reliable support coordinators for NDIS participants across Australia. Our platform aims to create a supportive community where participants can thrive.
                </p>
                <p class="text-lg text-text-dark leading-relaxed">
                    <b class="font-semibold text-primary-dark">Empowering NDIS Participants to Find Their Ideal Home.</b> At <b class="font-semibold text-primary-dark">SIL Match</b>, we believe that every person deserves a safe, comfortable, and empowering place to call home. Our platform is purpose-built to connect NDIS participants with suitable Supported Independent Living (SIL) and Specialist Disability Accommodation (SDA) options across Australia. Whether you're looking for a house that meets your unique accessibility needs or seeking a community that supports your independence, we’re here to help make that journey easier.
                </p>
            </div>
        </section>

        <hr class="my-16 border-border-light">

        {{-- Our Mission Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-center text-primary-dark mb-10">Our Mission</h2>
            <p class="text-xl text-text-dark mb-12 leading-relaxed max-w-4xl mx-auto text-center">
                To bridge the gap between NDIS participants and the right living environments—where care, comfort, and community come together. We understand that finding the right accommodation isn’t just about a room, it’s about compatibility, dignity, support, and most importantly, independence. That’s why we’ve created a platform that makes it easy for participants, families, support coordinators, and providers to connect meaningfully.
            </p>

            <div class="grid md:grid-cols-2 gap-10 mt-12">
                <div class="bg-secondary-bg p-8 rounded-2xl shadow-md-light border border-border-light">
                    <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">Why We Started</h3>
                    <p class="text-lg text-text-dark leading-relaxed">
                        Navigating the NDIS system can be overwhelming—especially when it to finding the right SIL or SDA property that matches both your needs and preferences. We started <b class="font-semibold text-primary-dark">SIL Match</b> after seeing too many participants struggle to find a home that felt like theirs.
                        From mismatched accommodations to long waitlists and a lack of transparency, we knew there had to be a better way.
                    </p>
                </div>
                <div class="bg-secondary-bg p-8 rounded-2xl shadow-md-light border border-border-light">
                    <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">So we created a platform that:</h3>
                    <ul class="list-disc list-inside text-lg text-text-dark space-y-3">
                        <li>Highlights verified and curated NDIS-friendly accommodations.</li>
                        <li>Allows participants to match based on their personal preferences.</li>
                        <li>Gives providers a streamlined way to manage and fill their vacancies.</li>
                        <li>Supports families and coordinators in making informed housing decisions.</li>
                    </ul>
                </div>
            </div>
        </section>

        <hr class="my-16 border-border-light">

        {{-- What Makes Us Different Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-center text-primary-dark mb-12">What Makes Us Different</h2>
            <p class="text-xl text-text-dark mb-14 leading-relaxed max-w-4xl mx-auto text-center">
                We’re not just a listing site—we’re a purpose-driven platform with a people-first approach.
            </p>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Participant-Focused Matching
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">Our matching tool goes beyond availability. It considers disability support needs, housemate preferences, location, and lifestyle to ensure long-term compatibility.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Verified & Curated Listings
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">We work with trusted providers to showcase high-quality SIL and SDA accommodations that meet the latest NDIS standards.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Human Support Where It Matters
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">Technology is only part of the solution. Behind our platform is a team of real people ready to guide you—from understanding funding to liaising with providers.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Accessibility by Design
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">We’re committed to ensuring our website is inclusive, usable, and compliant with accessibility standards. Everyone deserves access to information and opportunity.</p>
                </div>
            </div>

            <div class="mt-16">
                <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-8 text-center">Who We Serve</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">NDIS Participants seeking housing options that empower independence</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">Families & Guardians looking for safe, supported environments for loved ones</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">Support Coordinators & Allied Health Professionals needing trusted resources</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">SDA and SIL Providers aiming to fill vacancies with the right participants</p>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center">
                <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">Looking Ahead</h3>
                <p class="text-xl text-text-dark leading-relaxed max-w-4xl mx-auto">
                    As we grow, our vision is to become Australia’s most trusted and participant-centric housing platform in the disability sector. We’re continuously improving our tools, expanding our network of providers, and listening closely to the needs of the NDIS community. We’re just getting started—and we’d love for you to grow with us.
                </p>
            </div>
        </section>

        <p class="text-xl md:text-2xl text-center text-primary-dark font-semibold mt-16 mb-8 py-8 bg-secondary-bg rounded-2xl shadow-md-light border border-border-light">
            We believe in transparency, accessibility, and empowerment, ensuring that every NDIS participant has the resources they need to live independently and comfortably.
        </p>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endsection