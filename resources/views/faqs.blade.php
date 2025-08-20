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
                <h1 class="text-5xl lg:text-6xl font-extrabold text-primary-dark mb-6 leading-tight">Frequently <span class="text-primary-light">Asked Questions</span></h1>
                <p class="text-lg text-text-dark mb-4 leading-relaxed">
                    <b class="font-semibold text-primary-dark">For Everyone</b> helps NDIS participants connect with others who are looking to share a home. We make it easy to find people with similar goals, interests, and support needs so you can live together in a safe and supportive environment. Our aim is to build a community where participants feel understood and included.
                </p>
                <p class="text-lg text-text-dark leading-relaxed">
                    <b class="font-semibold text-primary-dark">Helping You Find the Right Housemates</b> At <b class="font-semibold text-primary-dark">SIL Match</b>, we believe that every person deserves a safe, comfortable, and empowering place to call home. Our platform is purpose-built to connect NDIS participants with suitable Supported Independent Living (SIL) and Specialist Disability Accommodation (SDA) options across Australia. Whether you're looking for a house that meets your unique accessibility needs or seeking a community that supports your independence, we’re here to help make that journey easier.
                </p>
            </div>

            
        </section>

        <hr class="my-16 border-border-light">

        {{-- For Everyone Section--}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-10">For Everyone</h2>
           
         <div id="accordion-flush" class="accordion-flush w-full max-w-6xl mx-auto border border-gray-200 bg-white rounded-lg" data-accordion="collapse" data-active-classes="bg-gray-100 text-gray-900" data-inactive-classes="text-gray-500">
        <div>
            <h3 id="accordion-flush-heading-1">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 gap-3" data-accordion-target="#accordion-flush-body-1" aria-expanded="true" aria-controls="accordion-flush-body-1">
                    <span>What is SIL Match?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-1" class="block" aria-labelledby="accordion-flush-heading-1">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        SIL Match is an online platform that connects NDIS participants, Support Coordinators, and providers to help create safe, supportive, compatible and long-lasting living arrangements.
                    </p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 id="accordion-flush-heading-2">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-2" aria-expanded="false" aria-controls="accordion-flush-body-2">
                    <span>Who can join SIL Match?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        NDIS participants and their representatives, Support Coordinators, and providers can all join. The sign up process will guide you based on your role.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h3 id="accordion-flush-heading-3">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-3" aria-expanded="false" aria-controls="accordion-flush-body-3">
                    <span>How does SIL Match work?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-3" class="hidden" aria-labelledby="accordion-flush-heading-3">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        You create a profile, share what you are looking for, and then search our listings to find people who match your needs. You can connect and chat before deciding on the next steps.
                    </p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 id="accordion-flush-heading-4">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-4" aria-expanded="false" aria-controls="accordion-flush-body-4">
                    <span>Is my personal information safe?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-4" class="hidden" aria-labelledby="accordion-flush-heading-4">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. Participants choose what information they want to make public. Personal details such as full name, address, and contact information are never shared with anyone until you choose to share them yourself.
                    </p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 id="accordion-flush-heading-5">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-5" aria-expanded="false" aria-controls="accordion-flush-body-5">
                    <span>Does SIL Match provide SIL supports?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-5" class="hidden" aria-labelledby="accordion-flush-heading-5">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        No. SIL Match does not provide Supported Independent Living supports. We are a matching service that helps you find compatible people to live with.
                    </p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 id="accordion-flush-heading-6">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-6" aria-expanded="false" aria-controls="accordion-flush-body-6">
                    <span>Is SIL Match safe to use?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-6" class="hidden" aria-labelledby="accordion-flush-heading-6">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. We verify profiles and provide secure messaging so you can connect with confidence.
                    </p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 id="accordion-flush-heading-7">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-flush-body-7" aria-expanded="false" aria-controls="accordion-flush-body-7">
                    <span>Does it cost anything to join?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-flush-body-7" class="hidden" aria-labelledby="accordion-flush-heading-7">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        It is free for participants and Support Coordinators. Providers have affordable plans with extra features.
                    </p>
                </div>
            </div>
        </div>
            
        </section>

        <hr class="my-16 border-border-light">

        {{-- For Participants Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Participants</h2>
            <div class="flex flex-col items-center gap-10 mt-12">
    <div id="accordion-section-2" class="accordion-flush w-full max-w-6xl mx-auto border border-gray-200 bg-white rounded-lg" data-accordion="collapse" data-active-classes="bg-gray-100 text-gray-900" data-inactive-classes="text-gray-500">

        <div>
            <h3 id="accordion-heading-1">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 gap-3" data-accordion-target="#accordion-body-1" aria-expanded="false" aria-controls="accordion-body-1">
                    <span>Can I join if I already have a home?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-body-1" class="hidden" aria-labelledby="accordion-heading-1">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. You can still use SIL Match to find the right housemate or fill a room.
                    </p>
                </div>
            </div>
        </div>

         <div>
            <h3 id="accordion-heading-2">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-body-2" aria-expanded="false" aria-controls="accordion-body-2">
                    <span>What if I need a housemate with specific skills or experience?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-body-2" class="hidden" aria-labelledby="accordion-heading-2">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        You can include your preferences in your profile so your search results are tailored to your needs.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h3 id="accordion-heading-3">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-body-3" aria-expanded="false" aria-controls="accordion-body-3">
                    <span>How do I know if someone is the right match for me?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-body-3" class="hidden" aria-labelledby="accordion-heading-3">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        You can view their profile, check their details, and message them to learn more before making any decisions.
                    </p>
                </div>
            </div>
        </div>
        </section>
        
        {{-- For Support Coordinators Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Support Coordinators</h2>
            <div class="flex flex-col items-center gap-10 mt-12">
            <div id="accordion-section-2" class="accordion-flush w-full max-w-6xl mx-auto border border-gray-200 bg-white rounded-lg" data-accordion="collapse" data-active-classes="bg-gray-100 text-gray-900" data-inactive-classes="text-gray-500">
            <div>
            <h3 id="accordion-heading-1">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 gap-3" data-accordion-target="#accordion-body-1" aria-expanded="false" aria-controls="accordion-body-1">
                    <span>Can I create profiles for the participants I support?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-body-1" class="hidden" aria-labelledby="accordion-heading-1">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. You can manage multiple participant profiles from your account.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h3 id="accordion-heading-2">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#accordion-body-2" aria-expanded="false" aria-controls="accordion-body-2">
                    <span>How will this save me time?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="accordion-body-2" class="hidden" aria-labelledby="accordion-heading-2">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Instead of calling multiple providers to check availability, you can search our listings in one place and quickly find suitable options.
                    </p>
                </div>
            </div>
        </div>
        </section>

        {{-- For Providers Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Providers</h2>
            <div class="flex flex-col items-center gap-10 mt-12">
            <div id="accordion-section-2" class="accordion-flush w-full max-w-6xl mx-auto border border-gray-200 bg-white rounded-lg" data-accordion="collapse" data-active-classes="bg-gray-100 text-gray-900" data-inactive-classes="text-gray-500">
            <div>
            <h3 id="providers-heading-1">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 gap-3" data-accordion-target="#providers-body-1" aria-expanded="false" aria-controls="providers-body-1">
                    <span>How does SIL Match help me fill vacancies?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="providers-body-1" class="hidden" aria-labelledby="providers-heading-1">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        You can upload depersonalised participant details and connect with other participants, providers, and support coordinators to find suitable matches. Only the Premium membership plan allows you to list properties for public viewing.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h3 id="providers-heading-2">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#providers-body-2" aria-expanded="false" aria-controls="providers-body-2">
                    <span>Can I see participant details before connecting?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="providers-body-2" class="hidden" aria-labelledby="providers-heading-2">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. You will see relevant information to help decide if they could be a good fit before starting a conversation.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h3 id="providers-heading-3">
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-t border-gray-200 gap-3" data-accordion-target="#providers-body-3" aria-expanded="false" aria-controls="providers-body-3">
                    <span>Can I cancel my plan?</span>
                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h3>
            <div id="providers-body-3" class="hidden" aria-labelledby="providers-heading-3">
                <div class="p-5">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">
                        Yes. You can cancel at any time. You will not be billed again after your current billing period ends.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endsection