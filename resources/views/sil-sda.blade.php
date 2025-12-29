@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans">

        {{-- Hero Section --}}
        <section class="text-center mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl lg:text-7xl font-extrabold text-[#33595a] mb-6 leading-tight">
                    Understanding <span class="text-[#cc8e45]">SIL & SDA</span>
                </h1>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    Learn about Supported Independent Living (SIL) and Specialist Disability Accommodation (SDA) - two important NDIS funding types that help people with disability live more independently.
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">NDIS Information</span>
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">Funding Types</span>
                    <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">Easy Read Available</span>
                </div>
            </div>
        </section>

        {{-- Quick Navigation --}}
        <section class="mb-12">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-4 text-center">Quick Navigation</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="#what-is-sil" class="bg-[#e1e7dd] hover:bg-[#cc8e45] hover:text-white text-[#3e4732] px-4 py-3 rounded-lg text-center font-medium transition-colors duration-200">
                            What is SIL?
                        </a>
                        <a href="#what-is-sda" class="bg-[#e1e7dd] hover:bg-[#cc8e45] hover:text-white text-[#3e4732] px-4 py-3 rounded-lg text-center font-medium transition-colors duration-200">
                            What is SDA?
                        </a>
                        <a href="#differences" class="bg-[#e1e7dd] hover:bg-[#cc8e45] hover:text-white text-[#3e4732] px-4 py-3 rounded-lg text-center font-medium transition-colors duration-200">
                            Key Differences
                        </a>
                        <a href="#easy-read" class="bg-[#e1e7dd] hover:bg-[#cc8e45] hover:text-white text-[#3e4732] px-4 py-3 rounded-lg text-center font-medium transition-colors duration-200">
                            Easy Read Version
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- What is SIL Section --}}
        <section id="what-is-sil" class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">What is Supported Independent Living (SIL)?</h2>
                    <p class="text-lg text-gray-600">Understanding SIL funding and how it helps with daily support</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="prose prose-lg max-w-none">
                        <p class="text-lg text-gray-700 leading-relaxed mb-6">
                            <strong class="text-[#33595a]">Supported Independent Living, or SIL</strong>, is funding that helps people with disability receive the daily support they need to live more independently. It is designed for people who need regular help throughout the day, and possibly overnight, with tasks like cooking, cleaning, personal care, or following daily routines.
                        </p>

                        <div class="bg-[#e1e7dd] p-6 rounded-lg mb-6">
                            <h3 class="text-xl font-bold text-[#33595a] mb-3">Key Points About SIL:</h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                <li>SIL is about the <strong>support services provided</strong>, not the rent or the cost of the home</li>
                                <li>Many people receiving SIL live in <strong>shared homes</strong> with other NDIS participants</li>
                                <li>SIL can also be provided to people who <strong>live on their own</strong></li>
                                <li>If SIL is not the right fit, there are <strong>other types of home and living supports</strong> that may be more suitable</li>
                            </ul>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-[#33595a] mb-3">What SIL Covers:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                    <li>Cooking and meal preparation</li>
                                    <li>Cleaning and household tasks</li>
                                    <li>Personal care assistance</li>
                                    <li>Following daily routines</li>
                                    <li>Overnight support if needed</li>
                                </ul>
                            </div>
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-[#33595a] mb-3">What SIL Doesn't Cover:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                    <li>Rent or mortgage payments</li>
                                    <li>Utilities and bills</li>
                                    <li>Food and groceries</li>
                                    <li>Personal expenses</li>
                                    <li>The cost of the home itself</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- What is SDA Section --}}
        <section id="what-is-sda" class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">What is Specialist Disability Accommodation (SDA)?</h2>
                    <p class="text-lg text-gray-600">Understanding SDA funding and accessible housing</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="prose prose-lg max-w-none">
                        <p class="text-lg text-gray-700 leading-relaxed mb-6">
                            <strong class="text-[#33595a]">Specialist Disability Accommodation, or SDA</strong>, is housing that has been specially designed or modified for people who have very high support needs or significant functional limitations. SDA properties include accessibility features and layouts that make everyday living easier and safer, and that allow support workers to provide help more effectively.
                        </p>

                        <div class="bg-[#e1e7dd] p-6 rounded-lg mb-6">
                            <h3 class="text-xl font-bold text-[#33595a] mb-3">Key Points About SDA:</h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                <li>SDA funding covers the <strong>home itself</strong>, not the support services you receive inside it</li>
                                <li>People still need to pay <strong>rent and other everyday living costs</strong> such as utilities, food, and personal expenses</li>
                                <li>SDA properties have <strong>accessibility features</strong> designed for people with high support needs</li>
                                <li>The layout allows <strong>support workers to provide help more effectively</strong></li>
                            </ul>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-[#33595a] mb-3">SDA Features May Include:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                    <li>Wheelchair accessible entrances</li>
                                    <li>Wide doorways and hallways</li>
                                    <li>Accessible bathrooms</li>
                                    <li>Specialized lighting</li>
                                    <li>Emergency response systems</li>
                                    <li>Hoist systems</li>
                                </ul>
                            </div>
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-[#33595a] mb-3">What SDA Doesn't Cover:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                    <li>Support services (that's SIL)</li>
                                    <li>Rent payments</li>
                                    <li>Utilities and bills</li>
                                    <li>Food and groceries</li>
                                    <li>Personal expenses</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Differences Section --}}
        <section id="differences" class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">The Difference Between SIL and SDA</h2>
                    <p class="text-lg text-gray-600">Understanding how SIL and SDA work together but are funded separately</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 leading-relaxed mb-6">
                            SIL and SDA often work together, but they are funded separately. Understanding the difference is important for getting the right support.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300 rounded-lg">
                            <thead>
                                <tr class="bg-[#e1e7dd]">
                                    <th class="border border-gray-300 px-6 py-4 text-left font-bold text-[#33595a]">Type</th>
                                    <th class="border border-gray-300 px-6 py-4 text-left font-bold text-[#33595a]">What It Covers</th>
                                    <th class="border border-gray-300 px-6 py-4 text-left font-bold text-[#33595a]">Who It Helps</th>
                                    <th class="border border-gray-300 px-6 py-4 text-left font-bold text-[#33595a]">How It's Funded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-6 py-4 font-bold text-[#cc8e45]">SIL</td>
                                    <td class="border border-gray-300 px-6 py-4">Daily supports to help with independent living</td>
                                    <td class="border border-gray-300 px-6 py-4">People who need ongoing support every day</td>
                                    <td class="border border-gray-300 px-6 py-4">Core Supports budget in your NDIS plan</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-6 py-4 font-bold text-[#cc8e45]">SDA</td>
                                    <td class="border border-gray-300 px-6 py-4">Accessible and purpose-built housing</td>
                                    <td class="border border-gray-300 px-6 py-4">People with very high support needs</td>
                                    <td class="border border-gray-300 px-6 py-4">Capital Supports budget in your NDIS plan</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 bg-gradient-to-r from-[#33595a] to-[#3e4732] rounded-lg p-6 text-white text-center">
                        <h3 class="text-2xl font-bold mb-2">In Short:</h3>
                        <p class="text-lg">
                            <strong>SIL funds the support</strong> • <strong>SDA funds the home</strong>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- How to Get Funding Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">How to Get SIL or SDA Funding</h2>
                    <p class="text-lg text-gray-600">Steps to include SIL or SDA in your NDIS plan</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <div class="text-center mb-4">
                            <div class="bg-[#cc8e45] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <span class="text-white font-bold text-2xl">1</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#33595a]">Include it in your NDIS goals</h3>
                        </div>
                        <p class="text-gray-700 text-center">
                            Explain to your Planner, Local Area Coordinator or Support Coordinator how SIL or SDA will help you work towards your goals.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <div class="text-center mb-4">
                            <div class="bg-[#cc8e45] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <span class="text-white font-bold text-2xl">2</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#33595a]">Gather supporting evidence</h3>
                        </div>
                        <p class="text-gray-700 text-center">
                            Ask Allied Health professionals or specialists to write reports that show why you need this type of support or housing.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <div class="text-center mb-4">
                            <div class="bg-[#cc8e45] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <span class="text-white font-bold text-2xl">3</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#33595a]">Discuss your needs</h3>
                        </div>
                        <p class="text-gray-700 text-center">
                            Bring your evidence to your planning meeting so the NDIS can assess your eligibility.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Check Your Plan Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">How to Check if You Already Have SIL or SDA Funding</h2>
                    <p class="text-lg text-gray-600">Understanding your current NDIS plan</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-xl font-bold text-[#33595a] mb-4">Look at your NDIS plan:</h3>
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-bold text-[#33595a] mb-2">Core Supports section</h4>
                                    <p class="text-gray-700">Will show SIL funding</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h4 class="font-bold text-[#33595a] mb-2">Capital Supports section</h4>
                                    <p class="text-gray-700">Will show SDA funding</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-xl font-bold text-[#33595a] mb-4">Need Help Understanding?</h3>
                            <p class="text-gray-700 mb-4">If you are unsure, speak with:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 mb-4">
                                <li>Your Support Coordinator</li>
                                <li>Your Local Area Coordinator</li>
                                <li>Call the NDIS on <strong>1800 800 110</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Who Can Help Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">Who Can Help You Apply</h2>
                    <p class="text-lg text-gray-600">Professional support for your application</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-friends text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-3">Support Coordinators</h3>
                        <p class="text-gray-700">Can guide you through the application process and help with the paperwork.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-md text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-3">Allied Health Professionals</h3>
                        <p class="text-gray-700">Can provide the assessments needed for your funding request.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-3">The NDIS</h3>
                        <p class="text-gray-700">Can explain eligibility criteria and funding options in more detail.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Key Things to Remember --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] rounded-2xl p-8 text-white">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6 text-center">Key Things to Remember</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <ul class="list-disc list-inside space-y-3 text-lg">
                                <li>You can have SIL, SDA, both, or neither in your plan</li>
                                <li>Having SDA funding does not automatically mean you will get SIL funding, and vice versa</li>
                                <li>SIL Match does not provide SIL or SDA services; we are a matching service that helps people find compatible housemates and create positive living arrangements</li>
                            </ul>
                        </div>
                        <div class="bg-white bg-opacity-20 p-6 rounded-lg">
                            <h3 class="text-xl font-bold mb-3">About SIL Match</h3>
                            <p class="text-lg">
                                We help you find the right people to live with, creating supportive and compatible living arrangements.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Easy Read Section --}}
        <section id="easy-read" class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#33595a] mb-4">Easy Read: What is SIL and SDA?</h2>
                    <p class="text-lg text-gray-600">A simplified version with clear, easy-to-understand language</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="space-y-8">
                        {{-- What is SIL Easy Read --}}
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-2xl font-bold text-[#33595a] mb-4">What is SIL?</h3>
                            <ul class="list-disc list-inside space-y-2 text-lg text-gray-700">
                                <li>SIL means <strong>Supported Independent Living</strong></li>
                                <li>It is money from the NDIS to help you with daily tasks</li>
                                <li>You might get help with cooking, cleaning, getting dressed, or following your daily routine</li>
                                <li>SIL is for people who need help every day, and sometimes at night</li>
                                <li>SIL pays for support, not rent or bills</li>
                            </ul>
                        </div>

                        {{-- What is SDA Easy Read --}}
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-2xl font-bold text-[#33595a] mb-4">What is SDA?</h3>
                            <ul class="list-disc list-inside space-y-2 text-lg text-gray-700">
                                <li>SDA means <strong>Specialist Disability Accommodation</strong></li>
                                <li>It is housing that is built or changed to suit people with high support needs</li>
                                <li>SDA homes might have ramps, wide doorways, or special bathrooms</li>
                                <li>SDA pays for the home, not for the support you get inside</li>
                                <li>You still pay rent, food, and other everyday costs</li>
                            </ul>
                        </div>

                        {{-- SIL vs SDA Easy Read --}}
                        <div class="bg-gradient-to-r from-[#cc8e45] to-[#a67137] p-6 rounded-lg text-white">
                            <h3 class="text-2xl font-bold mb-4">SIL vs SDA</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xl font-bold mb-2">SIL = the help you get</p>
                                    <p class="text-lg">Support with daily tasks</p>
                                </div>
                                <div>
                                    <p class="text-xl font-bold mb-2">SDA = the home you live in</p>
                                    <p class="text-lg">Accessible housing</p>
                                </div>
                            </div>
                            <p class="text-lg mt-4 text-center">
                                You can have SIL, SDA, both, or neither in your plan
                            </p>
                        </div>

                        {{-- How to Get Funding Easy Read --}}
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-2xl font-bold text-[#33595a] mb-4">How to Get SIL or SDA in Your Plan</h3>
                            <ol class="list-decimal list-inside space-y-2 text-lg text-gray-700">
                                <li>Tell your NDIS planner or support coordinator why you need it</li>
                                <li>Give reports from doctors or therapists to show your needs</li>
                                <li>Talk about SIL or SDA when you make or review your NDIS plan</li>
                            </ol>
                        </div>

                        {{-- How to Check Easy Read --}}
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-2xl font-bold text-[#33595a] mb-4">How to Check if You Already Have It</h3>
                            <p class="text-lg text-gray-700 mb-4">Look at your NDIS plan:</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg">
                                    <p class="font-bold text-[#33595a]">Core Supports section = SIL</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <p class="font-bold text-[#33595a]">Capital Supports section = SDA</p>
                                </div>
                            </div>
                            <p class="text-lg text-gray-700 mt-4">
                                If you are not sure, ask your support coordinator or call the NDIS on <strong>1800 800 110</strong>
                            </p>
                        </div>

                        {{-- Who Can Help Easy Read --}}
                        <div class="bg-[#e1e7dd] p-6 rounded-lg">
                            <h3 class="text-2xl font-bold text-[#33595a] mb-4">Who Can Help You</h3>
                            <ul class="list-disc list-inside space-y-2 text-lg text-gray-700">
                                <li><strong>Support Coordinators</strong> can help you apply</li>
                                <li><strong>Therapists and Doctors</strong> can write reports to support your request</li>
                                <li><strong>The NDIS</strong> can explain the rules and options</li>
                            </ul>
                        </div>

                        {{-- Remember Easy Read --}}
                        <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] p-6 rounded-lg text-white">
                            <h3 class="text-2xl font-bold mb-4">Remember</h3>
                            <ul class="list-disc list-inside space-y-2 text-lg">
                                <li>SIL Match does not give SIL or SDA services</li>
                                <li>We help you find the right people to live with</li>
                                <li>You choose what information you want to share</li>
                                <li>Your personal details stay private until you choose to share them</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section class="mb-16 lg:mb-24">
            <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] rounded-2xl p-8 md:p-12 text-center text-white">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Need More Help?</h2>
                <p class="text-lg mb-8 opacity-90">Our support team is here to help you understand SIL and SDA</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" class="bg-[#cc8e45] hover:bg-[#a67137] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-envelope mr-2"></i>Contact Support
                    </a>
                    <a href="{{ route('faqs') }}" class="bg-transparent border-2 border-white hover:bg-white hover:text-[#33595a] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-question-circle mr-2"></i>View FAQs
                    </a>
                </div>
            </div>
        </section>

    </div>

    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script>
        // Smooth scrolling for navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('a[href^="#"]');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
@endsection
