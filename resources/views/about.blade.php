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
                    <b class="font-semibold text-primary-dark">SIL Match</b> helps NDIS participants connect with others who are looking to share a home. We make it easy to find people with similar goals, interests, and support needs so you can live together in a safe and supportive environment. Our aim is to build a community where participants feel understood and included.
                </p>
                <p class="text-lg text-text-dark leading-relaxed">
                    <b class="font-semibold text-primary-dark">Helping You Find the Right Housemates</b> At <b class="font-semibold text-primary-dark">SIL Match</b>, we believe that every person deserves a safe, comfortable, and empowering place to call home. Our platform is purpose-built to connect NDIS participants with suitable Supported Independent Living (SIL) and Specialist Disability Accommodation (SDA) options across Australia. Whether you're looking for a house that meets your unique accessibility needs or seeking a community that supports your independence, we’re here to help make that journey easier.
                </p>
            </div>

            
        </section>

        <hr class="my-16 border-border-light">

        {{-- Our Mission Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-center text-primary-dark mb-10">Our Mission</h2>
            <p class="text-xl text-text-dark mb-12 leading-relaxed max-w-4xl mx-auto text-center">
                To bridge the gap between NDIS participants and the right people to live with, where friendship, shared goals, and mutual support can grow. We understand that finding a good living arrangement is not just about a room, it is about living with people who understand you, respect your needs, and help you feel at home. That is why we created a platform that makes it simple for participants, families, and support coordinators to connect with others who are a great match.
            </p>

            <div class="grid md:grid-cols-2 gap-10 mt-12">
                <div class="bg-secondary-bg p-8 rounded-2xl shadow-md-light border border-border-light">
                    <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">Why We Started</h3>
                    <p class="text-lg text-text-dark leading-relaxed">
                        Navigating the NDIS system can feel overwhelming, especially when you are looking for people to share a home with who suit your lifestyle and support needs. Too often, participants end up living with people who are not a good fit, which can make home life stressful. We created SIL Match after seeing how much easier life can be when the right people live together, building supportive and happy households.
                    </p>
                </div>
                <div class="bg-secondary-bg p-8 rounded-2xl shadow-md-light border border-border-light">
                    <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">So we created a platform that:</h3>
                    <ul class="list-disc list-inside text-lg text-text-dark space-y-3">
                        <li>Helps participants match with others based on personality, lifestyle, and support needs.</li>
                        <li>Gives families and coordinators a safe, easy way to help participants find compatible housemates.</li>
                        <li>Builds stronger and happier living environments by starting with the right connections</li>
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
                       People Over Property
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">Our priority is helping NDIS participants find housemates they connect with, not just rooms to rent.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Personalised Matching
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">We look at lifestyle, routines, and support needs to help you find people who will make home life easier and more enjoyable.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Safe and Supportive Connections
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">Every profile is verified, and participants can connect in a secure space before deciding to live together.</p>
                </div>
                <div class="bg-custom-white p-7 rounded-2xl shadow-md-light hover:shadow-xl transition-shadow duration-300 border border-border-light flex flex-col items-center text-center">
                    <div class="mb-4 text-custom-green">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-light mb-3">
                        Inclusive and Accessible
                    </h3>
                    <p class="text-base text-text-dark leading-relaxed">Our platform is designed so everyone can use it, including people with different accessibility needs.</p>
                </div>
            </div>

            <div class="mt-16">
                <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-8 text-center">Who We Serve</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">NDIS Participants looking for compatible housemates who can share a supportive and comfortable home life.</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">Families and Guardians wanting to help loved ones find safe and positive living arrangements with the right people.</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">Support Coordinators and Allied Health Professionals who need a trusted way to connect participants with suitable housemates.</p>
                    </div>
                    <div class="bg-custom-light-grey-green p-6 rounded-xl text-center shadow-sm border border-border-light hover:shadow-md transition-shadow duration-300">
                        <p class="text-lg font-medium text-primary-dark leading-relaxed">SDA and SIL Providers who want to match participants in a way that creates harmonious and lasting living arrangements</p>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center">
                <h3 class="text-3xl lg:text-4xl font-bold text-primary-dark mb-6">Looking Ahead</h3>
                <p class="text-xl text-text-dark leading-relaxed max-w-4xl mx-auto">
                    Our vision is to be Australia’s most trusted platform for matching NDIS participants with compatible housemates. We are continually improving our tools, expanding our community, and listening to the needs of the NDIS sector. We are just getting started and we invite you to grow with us.
                </p>
            </div>
        </section>

        <p class="text-xl md:text-2xl text-center text-primary-dark font-semibold mt-16 mb-8 py-8 bg-secondary-bg rounded-2xl shadow-md-light border border-border-light">
            We believe in transparency, accessibility, and empowerment, ensuring that every NDIS participant has the resources they need to live independently and comfortably.
        </p>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endsection