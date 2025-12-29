@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans">

        {{-- Hero Section: Enhanced with better styling --}}
        <section class="text-center mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl lg:text-7xl font-extrabold text-[#33595a] mb-6 leading-tight">
                    Frequently Asked <span class="text-[#cc8e45]">Questions</span>
                </h1>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    Find answers to common questions about SIL Match, our services, and how we help NDIS participants find the perfect home-sharing solutions.
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">General Questions</span>
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">For Participants</span>
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">For Coordinators</span>
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">For Providers</span>
                </div>
            </div>
        </section>

        {{-- Quick Search --}}
        <section class="mb-12">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" id="faq-search" placeholder="Search FAQs..." 
                           class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-xl focus:border-[#cc8e45] focus:outline-none shadow-sm">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
            </div>
            </div>
        </section>

        {{-- General Questions Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">General Questions</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Everything you need to know about SIL Match and how we work</p>
            </div>

            <div id="accordion-general" class="w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100" data-accordion="collapse" data-active-classes="bg-gray-50 text-gray-800" data-inactive-classes="text-gray-800">
                {{-- Question 1 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-1" aria-expanded="false" aria-controls="accordion-general-body-1">
                            <span class="text-lg">What is SIL Match and how does it work?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-1" class="hidden" aria-labelledby="accordion-general-heading-1">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">SIL Match is Australia's leading platform connecting NDIS participants with compatible housemates and suitable Supported Independent Living (SIL) arrangements. We use advanced matching algorithms to help you find people who share your lifestyle, interests, and support needs.</p>
                            <p>Our process is simple: create a profile, specify your preferences, browse compatible matches, and connect safely through our secure messaging system before making any living arrangements.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-2" aria-expanded="false" aria-controls="accordion-general-body-2">
                            <span class="text-lg">Who can use SIL Match?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-2" class="hidden" aria-labelledby="accordion-general-heading-2">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">SIL Match is designed for:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li><strong>NDIS Participants</strong> - Looking for compatible housemates and suitable accommodation</li>
                                <li><strong>Support Coordinators</strong> - Helping participants find the right living arrangements</li>
                                <li><strong>SIL/SDA Providers</strong> - Connecting with participants and filling vacancies</li>
                                <li><strong>Families and Guardians</strong> - Supporting loved ones in finding safe, suitable housing</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-3" aria-expanded="false" aria-controls="accordion-general-body-3">
                            <span class="text-lg">Is SIL Match safe and secure?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-3" class="hidden" aria-labelledby="accordion-general-heading-3">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Absolutely. Your safety and privacy are our top priorities:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li><strong>Profile Verification</strong> - All profiles are verified before going live</li>
                                <li><strong>Secure Messaging</strong> - Private communication system with no external access</li>
                                <li><strong>Privacy Controls</strong> - You control what information is shared and when</li>
                                <li><strong>24/7 Support</strong> - Our team is always available to help with any concerns</li>
                                <li><strong>Data Protection</strong> - All personal information is encrypted and securely stored</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-4">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-4" aria-expanded="false" aria-controls="accordion-general-body-4">
                            <span class="text-lg">What's the difference between SIL and SDA?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-4" class="hidden" aria-labelledby="accordion-general-heading-4">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">SIL (Supported Independent Living)</h4>
                                    <p>Personal care and support services to help you live independently in your own home. This includes assistance with daily activities, personal care, and community participation.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">SDA (Specialist Disability Accommodation)</h4>
                                    <p>Specialized housing designed for people with significant functional impairment or very high support needs. SDA properties have specific accessibility features and modifications.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 5 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-5">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-5" aria-expanded="false" aria-controls="accordion-general-body-5">
                            <span class="text-lg">Does SIL Match provide SIL supports?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-5" class="hidden" aria-labelledby="accordion-general-heading-5">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">No, SIL Match is a <strong>matching service only</strong>. We focus exclusively on connecting you with:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li>Compatible housemates who share your lifestyle and support needs</li>
                                <li>Suitable accommodation options that meet your accessibility requirements</li>
                                <li>Verified providers and support coordinators in your area</li>
                            </ul>
                            <p class="mt-4">We don't provide SIL supports directly, but we help you find the right people and places to receive those supports.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 6 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-general-heading-6">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-6" aria-expanded="false" aria-controls="accordion-general-body-6">
                            <span class="text-lg">How much does SIL Match cost?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-6" class="hidden" aria-labelledby="accordion-general-heading-6">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                    <h4 class="font-bold text-[#33595a] mb-2">NDIS Participants</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">FREE</p>
                                    <p class="text-sm">Complete access to all matching features</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                    <h4 class="font-bold text-[#33595a] mb-2">Support Coordinators</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">FREE</p>
                                    <p class="text-sm">Manage multiple participant profiles</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                    <h4 class="font-bold text-[#33595a] mb-2">Providers</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">From $299/mo</p>
                                    <p class="text-sm">Affordable plans with enhanced features</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 7 --}}
                <div>
                    <h3 id="accordion-general-heading-7">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-general-body-7" aria-expanded="false" aria-controls="accordion-general-body-7">
                            <span class="text-lg">How do I get started with SIL Match?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-general-body-7" class="hidden" aria-labelledby="accordion-general-heading-7">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-4">Getting Started is Easy:</h4>
                                    <ol class="list-decimal list-inside space-y-3">
                                        <li><strong>Sign Up</strong> - Choose your role (Participant, Coordinator, or Provider)</li>
                                        <li><strong>Create Profile</strong> - Tell us about your needs, preferences, and goals</li>
                                        <li><strong>Browse Matches</strong> - Explore compatible housemates and accommodation</li>
                                        <li><strong>Connect Safely</strong> - Use our secure messaging to get to know potential matches</li>
                                        <li><strong>Make Decisions</strong> - Choose the right living arrangement for you</li>
                                    </ol>
                                </div>
                                <div class="bg-[#e1e7dd] p-4 rounded-lg">
                                    <h4 class="font-bold text-[#33595a] mb-2">Need Help?</h4>
                                    <p class="mb-3">Our support team is here to guide you through every step.</p>
                                    <a href="{{ route('contact') }}" class="inline-block bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-[#a67137] transition-colors duration-200">
                                        Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- For Participants Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">For NDIS Participants</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Questions specifically about using SIL Match as a participant</p>
            </div>

            <div id="accordion-participants" class="w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100" data-accordion="collapse" data-active-classes="bg-gray-50 text-gray-800" data-inactive-classes="text-gray-800">
                {{-- Question 1 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-participants-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-participants-body-1" aria-expanded="false" aria-controls="accordion-participants-body-1">
                            <span class="text-lg">Can I use SIL Match if I already have a home?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-1" class="hidden" aria-labelledby="accordion-participants-heading-1">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Absolutely! SIL Match is perfect for finding housemates to fill rooms in your existing home. Whether you have:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li>A spare room in your current home</li>
                                <li>A shared house looking for new housemates</li>
                                <li>A group home with vacancies</li>
                                <li>An SDA property needing compatible residents</li>
                            </ul>
                            <p class="mt-4">Our platform helps you find people who will fit well with your lifestyle and support needs.</p>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-participants-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-participants-body-2" aria-expanded="false" aria-controls="accordion-participants-body-2">
                            <span class="text-lg">How do I find housemates with specific support needs?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-2" class="hidden" aria-labelledby="accordion-participants-heading-2">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Our advanced matching system considers multiple factors to find compatible housemates:</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-2">Support Needs Matching:</h4>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>Similar support requirements</li>
                                        <li>Compatible care schedules</li>
                                        <li>Shared support workers</li>
                                        <li>Complementary assistance needs</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-2">Lifestyle Matching:</h4>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>Daily routines and schedules</li>
                                        <li>Interests and hobbies</li>
                                        <li>Social preferences</li>
                                        <li>Household responsibilities</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-participants-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-participants-body-3" aria-expanded="false" aria-controls="accordion-participants-body-3">
                            <span class="text-lg">What information should I include in my profile?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-3" class="hidden" aria-labelledby="accordion-participants-heading-3">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">A complete profile helps us find better matches. Include:</p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Essential Information:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Your interests and hobbies</li>
                                        <li>Daily routine and schedule</li>
                                        <li>Support needs and preferences</li>
                                        <li>Living preferences (quiet, social, etc.)</li>
                                        <li>Household responsibilities you can manage</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Optional Details:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Pet preferences</li>
                                        <li>Transportation needs</li>
                                        <li>Community activities you enjoy</li>
                                        <li>Communication preferences</li>
                                        <li>Goals for independent living</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-participants-heading-4">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-participants-body-4" aria-expanded="false" aria-controls="accordion-participants-body-4">
                            <span class="text-lg">How do I know if someone is the right match for me?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-4" class="hidden" aria-labelledby="accordion-participants-heading-4">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Take your time to get to know potential housemates:</p>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">1. Review Their Profile</h4>
                                    <p class="text-sm">Look for shared interests, compatible routines, and similar support needs.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">2. Use Secure Messaging</h4>
                                    <p class="text-sm">Ask questions about their lifestyle, preferences, and what they're looking for in a housemate.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">3. Consider Compatibility</h4>
                                    <p class="text-sm">Think about whether your daily routines, support needs, and living preferences align.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">4. Take Your Time</h4>
                                    <p class="text-sm">Don't rush into decisions. It's important to feel comfortable and confident about your choice.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 5 --}}
                <div>
                    <h3 id="accordion-participants-heading-5">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-participants-body-5" aria-expanded="false" aria-controls="accordion-participants-body-5">
                            <span class="text-lg">Can my family or support coordinator help me use SIL Match?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-participants-body-5" class="hidden" aria-labelledby="accordion-participants-heading-5">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Yes! SIL Match is designed to support collaborative decision-making:</p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Support Coordinators:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Can create and manage profiles for participants</li>
                                        <li>Help with profile setup and preferences</li>
                                        <li>Assist with matching and communication</li>
                                        <li>Provide guidance throughout the process</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Family Members:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Can help review potential matches</li>
                                        <li>Assist with profile creation</li>
                                        <li>Provide input on compatibility</li>
                                        <li>Support decision-making process</li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mt-4 text-sm bg-[#e1e7dd] p-3 rounded-lg">Remember: The final decision about living arrangements should always be yours, with support from your trusted network.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- For Support Coordinators Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">For Support Coordinators</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">How SIL Match helps you support your participants</p>
            </div>

            <div id="accordion-coordinators" class="w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100" data-accordion="collapse" data-active-classes="bg-gray-50 text-gray-800" data-inactive-classes="text-gray-800">
                {{-- Question 1 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-coordinators-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-coordinators-body-1" aria-expanded="false" aria-controls="accordion-coordinators-body-1">
                            <span class="text-lg">Can I create profiles for the participants I support?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-coordinators-body-1" class="hidden" aria-labelledby="accordion-coordinators-heading-1">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Yes! As a Support Coordinator, you can:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li><strong>Create Multiple Profiles</strong> - Manage profiles for all participants you support</li>
                                <li><strong>Set Preferences</strong> - Define their needs, interests, and living preferences</li>
                                <li><strong>Browse Matches</strong> - Find compatible housemates and accommodation options</li>
                                <li><strong>Facilitate Communication</strong> - Help participants connect safely with potential matches</li>
                                <li><strong>Track Progress</strong> - Monitor matching activities and outcomes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-coordinators-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-coordinators-body-2" aria-expanded="false" aria-controls="accordion-coordinators-body-2">
                            <span class="text-lg">How will SIL Match save me time?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-coordinators-body-2" class="hidden" aria-labelledby="accordion-coordinators-heading-2">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">SIL Match streamlines your workflow by:</p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Efficiency Benefits:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Centralized search across multiple providers</li>
                                        <li>Automated matching based on compatibility</li>
                                        <li>Secure communication platform</li>
                                        <li>Documentation and tracking tools</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Time Savings:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>No more cold-calling providers</li>
                                        <li>Pre-screened compatible matches</li>
                                        <li>Reduced administrative overhead</li>
                                        <li>Faster decision-making process</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div>
                    <h3 id="accordion-coordinators-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-coordinators-body-3" aria-expanded="false" aria-controls="accordion-coordinators-body-3">
                            <span class="text-lg">What information should I include in participant profiles?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-coordinators-body-3" class="hidden" aria-labelledby="accordion-coordinators-heading-3">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Include comprehensive information to ensure better matches:</p>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">Support Needs</h4>
                                    <p class="text-sm">Level of assistance required, support worker preferences, medical needs, and daily care requirements.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">Lifestyle Preferences</h4>
                                    <p class="text-sm">Daily routines, social preferences, interests, hobbies, and communication styles.</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">Living Requirements</h4>
                                    <p class="text-sm">Accessibility needs, location preferences, transport requirements, and accommodation type.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- For Providers Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">For SIL/SDA Providers</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">How SIL Match helps you connect with participants</p>
            </div>

            <div id="accordion-providers" class="w-full max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100" data-accordion="collapse" data-active-classes="bg-gray-50 text-gray-800" data-inactive-classes="text-gray-800">
                {{-- Question 1 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-providers-heading-1">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-providers-body-1" aria-expanded="false" aria-controls="accordion-providers-body-1">
                            <span class="text-lg">How does SIL Match help me fill vacancies?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-1" class="hidden" aria-labelledby="accordion-providers-heading-1">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">SIL Match helps you find compatible participants through:</p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Matching Features:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Upload depersonalized participant details</li>
                                        <li>Connect with compatible participants</li>
                                        <li>List properties for public viewing (Premium)</li>
                                        <li>Receive enquiries from interested parties</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#33595a] mb-3">Network Benefits:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Access to Support Coordinators</li>
                                        <li>Connection with other providers</li>
                                        <li>Verified participant profiles</li>
                                        <li>Secure communication platform</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-providers-heading-2">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-providers-body-2" aria-expanded="false" aria-controls="accordion-providers-body-2">
                            <span class="text-lg">Can I see participant details before connecting?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-2" class="hidden" aria-labelledby="accordion-providers-heading-2">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Yes, you can see relevant information to help determine compatibility:</p>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">Available Information:</h4>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>Support needs and preferences</li>
                                        <li>Lifestyle and interests</li>
                                        <li>Daily routines and schedules</li>
                                        <li>Communication preferences</li>
                                        <li>Living requirements</li>
                                    </ul>
                                </div>
                                <div class="bg-[#e1e7dd] p-4 rounded-lg">
                                    <h4 class="font-bold text-[#33595a] mb-2">Privacy Protected:</h4>
                                    <p class="text-sm">Personal identifying information (name, address, contact details) remains private until you choose to connect directly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="border-b border-gray-100">
                    <h3 id="accordion-providers-heading-3">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-providers-body-3" aria-expanded="false" aria-controls="accordion-providers-body-3">
                            <span class="text-lg">What are the pricing plans for providers?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-3" class="hidden" aria-labelledby="accordion-providers-heading-3">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                    <h4 class="font-bold text-[#33595a] mb-2">Basic Plan</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">$29/mo</p>
                                    <ul class="text-sm space-y-1">
                                        <li>• Upload participant details</li>
                                        <li>• Browse compatible matches</li>
                                        <li>• Secure messaging</li>
                                        <li>• Basic support</li>
                                    </ul>
                                </div>
                                <div class="bg-white p-4 rounded-lg border-2 border-[#cc8e45] text-center relative">
                                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                        <span class="bg-[#cc8e45] text-white px-3 py-1 rounded-full text-xs font-bold">POPULAR</span>
                                    </div>
                                    <h4 class="font-bold text-[#33595a] mb-2">Premium Plan</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">$59/mo</p>
                                    <ul class="text-sm space-y-1">
                                        <li>• Everything in Basic</li>
                                        <li>• List properties publicly</li>
                                        <li>• Priority matching</li>
                                        <li>• Advanced analytics</li>
                                        <li>• Priority support</li>
                                    </ul>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                                    <h4 class="font-bold text-[#33595a] mb-2">Enterprise</h4>
                                    <p class="text-2xl font-bold text-[#cc8e45] mb-2">Custom</p>
                                    <ul class="text-sm space-y-1">
                                        <li>• Everything in Premium</li>
                                        <li>• Multiple properties</li>
                                        <li>• Custom integrations</li>
                                        <li>• Dedicated support</li>
                                        <li>• Training & onboarding</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div>
                    <h3 id="accordion-providers-heading-4">
                        <button type="button" class="flex items-center justify-between w-full p-6 font-semibold text-left text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors duration-200" data-accordion-target="#accordion-providers-body-4" aria-expanded="false" aria-controls="accordion-providers-body-4">
                            <span class="text-lg">Can I cancel my plan anytime?</span>
                            <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h3>
                    <div id="accordion-providers-body-4" class="hidden" aria-labelledby="accordion-providers-heading-4">
                        <div class="p-6 text-gray-600 border-t border-gray-100 bg-gray-50">
                            <p class="mb-4">Yes, you have complete flexibility:</p>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-bold text-[#33595a] mb-2">Cancellation Policy:</h4>
                                    <ul class="list-disc list-inside space-y-2 text-sm">
                                        <li>Cancel anytime with no penalties</li>
                                        <li>Access continues until end of billing period</li>
                                        <li>No long-term contracts required</li>
                                        <li>Easy cancellation through your dashboard</li>
                                    </ul>
                                </div>
                                <div class="bg-[#e1e7dd] p-4 rounded-lg">
                                    <h4 class="font-bold text-[#33595a] mb-2">Need Help?</h4>
                                    <p class="text-sm mb-2">Our support team is here to help with any questions about your plan or billing.</p>
                                    <a href="{{ route('contact') }}" class="inline-block bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-[#a67137] transition-colors duration-200 text-sm">
                                        Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] rounded-2xl p-8 md:p-12 text-center text-white">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Still Have Questions?</h2>
                <p class="text-lg mb-8 opacity-90">Our support team is here to help you get the most out of SIL Match</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="bg-[#cc8e45] hover:bg-[#a67137] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-envelope mr-2"></i>Contact Support
                    </a>
                    <a href="{{ route('pricing') }}" class="bg-transparent border-2 border-white hover:bg-white hover:text-[#33595a] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-dollar-sign mr-2"></i>View Pricing
                    </a>
                </div>
            </div>
        </section>

    </div>

    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <script>
        // FAQ Search Functionality and Accordion Fallback
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faq-search');
            const accordionSections = document.querySelectorAll('[id^="accordion-"]');
            
            // Initialize accordions manually if Flowbite doesn't work
            function initAccordions() {
                accordionSections.forEach(section => {
                    const buttons = section.querySelectorAll('[data-accordion-target]');
                    
                    buttons.forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            
                            const targetId = this.getAttribute('data-accordion-target');
                            const targetElement = document.querySelector(targetId);
                            const icon = this.querySelector('[data-accordion-icon]');
                            
                            if (targetElement) {
                                // Close other accordions in the same section
                                const otherButtons = section.querySelectorAll('[data-accordion-target]');
                                otherButtons.forEach(otherButton => {
                                    if (otherButton !== this) {
                                        const otherTargetId = otherButton.getAttribute('data-accordion-target');
                                        const otherTargetElement = document.querySelector(otherTargetId);
                                        const otherIcon = otherButton.querySelector('[data-accordion-icon]');
                                        
                                        if (otherTargetElement) {
                                            otherTargetElement.classList.add('hidden');
                                            otherButton.setAttribute('aria-expanded', 'false');
                                            if (otherIcon) {
                                                otherIcon.style.transform = 'rotate(0deg)';
                                            }
                                        }
                                    }
                                });
                                
                                // Toggle current accordion
                                if (targetElement.classList.contains('hidden')) {
                                    targetElement.classList.remove('hidden');
                                    this.setAttribute('aria-expanded', 'true');
                                    if (icon) {
                                        icon.style.transform = 'rotate(180deg)';
                                    }
                                } else {
                                    targetElement.classList.add('hidden');
                                    this.setAttribute('aria-expanded', 'false');
                                    if (icon) {
                                        icon.style.transform = 'rotate(0deg)';
                                    }
                                }
                            }
                        });
                    });
                });
            }
            
            // Initialize accordions
            initAccordions();
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                accordionSections.forEach(section => {
                    const questions = section.querySelectorAll('[id$="-heading-"]');
                    let hasVisibleQuestions = false;
                    
                    questions.forEach(question => {
                        const questionText = question.textContent.toLowerCase();
                        const questionElement = question.closest('.border-b, .border-gray-100');
                        
                        if (questionText.includes(searchTerm)) {
                            questionElement.style.display = 'block';
                            hasVisibleQuestions = true;
                        } else {
                            questionElement.style.display = 'none';
                        }
                    });
                    
                    // Show/hide entire section based on visible questions
                    const sectionContainer = section.closest('section');
                    if (hasVisibleQuestions || searchTerm === '') {
                        sectionContainer.style.display = 'block';
                    } else {
                        sectionContainer.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection