@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans"> {{-- Adjusted padding for better spacing --}}

        {{-- Hero Section: Revamped for modern look --}}
        <section class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-16 mb-16 lg:mb-24">
            <div class="w-full md:w-1/2 p-4 text-center md:text-left">
                <h1 class="text-5xl lg:text-6xl font-extrabold text-primary-dark mb-6 leading-tight">Frequently <span class="text-primary-light">Asked Questions</span></h1>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    At <b>SIL Match</b>, we're dedicated to helping NDIS participants find the perfect home-sharing solutions. We connect you with compatible housemates and suitable Supported Independent Living (SIL) and Specialist Disability Accommodation (SDA) options across Australia.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Our goal is to create a community where everyone feels <b>understood</b>, <b>included</b>, and <b>empowered</b> to live independently in a safe and supportive environment.
                </p>
            </div>
            <div class="w-full md:w-1/2 flex justify-center items-center">
                <img src="{{ asset('images/flyfinal.png') }}" class="block w-full h-full object-cover" alt="Comfortable Seating Area">
            </div>
        </section>

        {{-- Frequently Asked Questions Sections --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-10">General Questions</h2>

            <div id="accordion-general" class="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden" data-accordion="collapse" data-active-classes="bg-blue-50 text-blue-800" data-inactive-classes="text-gray-700">
                {{-- Question 1 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-1" aria-expanded="false" aria-controls="accordion-general-body-1">
                            <span>What is SIL Match?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-1" class="hidden" aria-labelledby="accordion-general-heading-1">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>SIL Match is an online platform that connects NDIS participants, Support Coordinators, and providers to create compatible and lasting Supported Independent Living (SIL) arrangements.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-2" aria-expanded="false" aria-controls="accordion-general-body-2">
                            <span>Who can use SIL Match?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-2" class="hidden" aria-labelledby="accordion-general-heading-2">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>NDIS participants and their representatives, Support Coordinators, and providers are all welcome to join. Our sign-up process is tailored to your specific role.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-3" aria-expanded="false" aria-controls="accordion-general-body-3">
                            <span>How does SIL Match work?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-3" class="hidden" aria-labelledby="accordion-general-heading-3">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>You create a personalized profile, specify your needs and preferences, and then browse listings to find compatible matches. You can connect and chat securely before making any decisions.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-4">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-4" aria-expanded="false" aria-controls="accordion-general-body-4">
                            <span>Is my personal information secure?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-4" class="hidden" aria-labelledby="accordion-general-heading-4">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Absolutely. You control what information is public on your profile. Your full name, address, and contact details are kept private until you choose to share them directly with a match.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 5 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-5">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-5" aria-expanded="false" aria-controls="accordion-general-body-5">
                            <span>Does SIL Match provide SIL supports?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-5" class="hidden" aria-labelledby="accordion-general-heading-5">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>No, SIL Match is a dedicated **matching service**, not a SIL support provider. We focus solely on connecting you with compatible individuals and suitable housing options.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 6 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-general-heading-6">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-6" aria-expanded="false" aria-controls="accordion-general-body-6">
                            <span>Is SIL Match safe to use?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-6" class="hidden" aria-labelledby="accordion-general-heading-6">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Yes. We prioritize your safety with profile verification and a secure messaging system, allowing you to connect with confidence.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 7 --}}
                <div>
                    <h3 id="accordion-general-heading-7">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-general-body-7" aria-expanded="false" aria-controls="accordion-general-body-7">
                            <span>Is there a cost to join?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-7" class="hidden" aria-labelledby="accordion-general-heading-7">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>It's completely free for NDIS participants and Support Coordinators. Providers have access to affordable plans with enhanced features.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- For Participants Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Participants</h2>
            <div id="accordion-participants" class="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden" data-accordion="collapse" data-active-classes="bg-blue-50 text-blue-800" data-inactive-classes="text-gray-700">
                {{-- Question 1 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-participants-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-participants-body-1" aria-expanded="false" aria-controls="accordion-participants-body-1">
                            <span>Can I use SIL Match if I already have a home?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-1" class="hidden" aria-labelledby="accordion-participants-heading-1">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Yes! Our platform is also ideal for finding the right housemate to fill a room in your existing home.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-participants-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-participants-body-2" aria-expanded="false" aria-controls="accordion-participants-body-2">
                            <span>What if I need a housemate with specific support needs or interests?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-2" class="hidden" aria-labelledby="accordion-participants-heading-2">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>You can detail your preferences in your profile, allowing our matching system to show you results that are precisely tailored to your requirements.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div>
                    <h3 id="accordion-participants-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-participants-body-3" aria-expanded="false" aria-controls="accordion-participants-body-3">
                            <span>How do I know if someone is the right match for me?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-3" class="hidden" aria-labelledby="accordion-participants-heading-3">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>You can view detailed profiles, review their information, and use our secure messaging to communicate directly before deciding if they're a good fit.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- For Support Coordinators Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Support Coordinators</h2>
            <div id="accordion-coordinators" class="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden" data-accordion="collapse" data-active-classes="bg-blue-50 text-blue-800" data-inactive-classes="text-gray-700">
                {{-- Question 1 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-coordinators-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-coordinators-body-1" aria-expanded="false" aria-controls="accordion-coordinators-body-1">
                            <span>Can I create profiles for the participants I support?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-coordinators-body-1" class="hidden" aria-labelledby="accordion-coordinators-heading-1">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Yes, you can easily manage multiple participant profiles directly from your Support Coordinator account.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div>
                    <h3 id="accordion-coordinators-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-coordinators-body-2" aria-expanded="false" aria-controls="accordion-coordinators-body-2">
                            <span>How will SIL Match save me time?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-coordinators-body-2" class="hidden" aria-labelledby="accordion-coordinators-heading-2">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Instead of contacting numerous providers individually, you can efficiently search our comprehensive listings in one place, significantly streamlining your search for suitable options.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- For Providers Section --}}
        <section class="mb-16 lg:mb-24">
            <h2 class="text-4xl lg:text-6xl font-extrabold text-center text-primary-dark mb-12">For Providers</h2>
            <div id="accordion-providers" class="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden" data-accordion="collapse" data-active-classes="bg-blue-50 text-blue-800" data-inactive-classes="text-gray-700">
                {{-- Question 1 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-providers-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-providers-body-1" aria-expanded="false" aria-controls="accordion-providers-body-1">
                            <span>How does SIL Match help me fill vacancies?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-1" class="hidden" aria-labelledby="accordion-providers-heading-1">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>You can upload depersonalized participant details and connect with participants, other providers, and Support Coordinators to find ideal matches for your vacancies. Listing properties for public viewing is available with our Premium membership plan.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-200">
                    <h3 id="accordion-providers-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-providers-body-2" aria-expanded="false" aria-controls="accordion-providers-body-2">
                            <span>Can I see participant details before connecting?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-2" class="hidden" aria-labelledby="accordion-providers-heading-2">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Yes. You'll see relevant, non-identifying information to help you determine if a participant might be a good fit before initiating a conversation.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div>
                    <h3 id="accordion-providers-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-5 font-semibold text-left text-gray-700 hover:bg-gray-50 focus:outline-none" data-accordion-target="#accordion-providers-body-3" aria-expanded="false" aria-controls="accordion-providers-body-3">
                            <span>Can I cancel my plan?</span>
                            <svg data-accordion-icon class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-3" class="hidden" aria-labelledby="accordion-providers-heading-3">
                        <div class="p-5 text-gray-600 border-t border-gray-200">
                            <p>Certainly. You can cancel your plan at any time. Your billing will simply not renew after your current billing period ends.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endsection