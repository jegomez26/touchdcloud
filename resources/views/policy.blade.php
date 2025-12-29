@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans">

    {{-- Hero Section --}}
    <section class="text-center mb-16 lg:mb-24">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl lg:text-7xl font-extrabold text-[#33595a] mb-6 leading-tight">
                Privacy <span class="text-[#cc8e45]">Policy</span>
            </h1>
            <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                Your privacy is important to us. This Privacy Policy explains how SIL Match collects, uses, discloses, and safeguards your information.
            </p>
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">Last Updated: {{ date('F j, Y') }}</span>
                <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full font-medium">Effective Date: {{ date('F j, Y') }}</span>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 border border-gray-100">
            
            {{-- Section 1 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">1</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Introduction</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-4">
                        Your privacy is important to us. This Privacy Policy explains how SIL Match collects, uses, discloses, and safeguards your information when you visit our website <a href="http://www.silmatch.com.au" class="underline text-[#cc8e45] hover:text-[#a67137]">www.silmatch.com.au</a>, including any other media form, media channel, mobile website, or mobile application related or connected thereto (collectively, the "Site"). Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the Site.
                    </p>
                    <div class="bg-[#e1e7dd] p-4 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <strong>Your Rights:</strong> You have the right to know how your personal information is collected, used, and protected.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Section 2 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">2</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Information We Collect</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We may collect personal information that you voluntarily provide to us when you register on the Site, express an interest in obtaining information about us or our products and services, when you participate in activities on the Site, or otherwise when you contact us.
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        The personal information that we collect depends on the context of your interactions with us and the Site, the choices you make and the products and features you use. The personal information we collect may include the following:
                    </p>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-[#33595a] mb-4">
                                <i class="fas fa-user mr-2"></i>
                                Personal Information
                            </h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li>• Names and contact details</li>
                                <li>• Email addresses</li>
                                <li>• Phone numbers</li>
                                <li>• Contact preferences</li>
                                <li>• Profile information</li>
                            </ul>
                        </div>
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-[#33595a] mb-4">
                                <i class="fas fa-home mr-2"></i>
                                Service-Related Information
                            </h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li>• NDIS participant needs</li>
                                <li>• Accommodation preferences</li>
                                <li>• Support requirements</li>
                                <li>• Provider details</li>
                                <li>• Communication history</li>
        </ul>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 3 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">3</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">How We Use Your Information</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We use personal information collected via our Site for a variety of business purposes described below. We process your personal information for these purposes in reliance on our legitimate business interests, in order to enter into or perform a contract with you, with your consent, and/or for compliance with our legal obligations.
                    </p>
                    <div class="space-y-4">
                        <div class="bg-[#e1e7dd] p-4 rounded-lg">
                            <h4 class="font-bold text-[#33595a] mb-2">Primary Uses</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• To facilitate account creation and logon process</li>
                                <li>• To enable user-to-user communications with your consent</li>
                                <li>• To respond to your inquiries and offer support</li>
                                <li>• To fulfill and manage your orders related to the Site</li>
                            </ul>
                        </div>
                        <div class="bg-[#e1e7dd] p-4 rounded-lg">
                            <h4 class="font-bold text-[#33595a] mb-2">Additional Uses</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• To post testimonials with your consent</li>
                                <li>• To send you marketing and promotional communications</li>
                                <li>• To improve our services and user experience</li>
                                <li>• To comply with legal obligations</li>
        </ul>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 4 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">4</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Disclosure of Your Information</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We may share information we have collected about you in certain situations. Your information may be disclosed as follows:
                    </p>
                    <div class="space-y-4">
                        <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                            <h4 class="font-bold text-red-800 mb-3">
                                <i class="fas fa-gavel mr-2"></i>
                                By Law or to Protect Rights
                            </h4>
                            <p class="text-sm text-red-700">
                                If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, or safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.
                            </p>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-blue-800 mb-3">
                                <i class="fas fa-handshake mr-2"></i>
                                Third-Party Service Providers
                            </h4>
                            <p class="text-sm text-blue-700">
                                We may share your information with third parties that perform services for us or on our behalf, including data analysis, email delivery, hosting services, customer service, and marketing assistance.
                            </p>
                        </div>
                        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                            <h4 class="font-bold text-yellow-800 mb-3">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Business Transfers
                            </h4>
                            <p class="text-sm text-yellow-700">
                                We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 5 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">5</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Security of Your Information</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.
                    </p>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 text-center">
                            <i class="fas fa-lock text-[#cc8e45] text-3xl mb-4"></i>
                            <h4 class="font-bold text-[#33595a] mb-2">Administrative Security</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Access controls</li>
                                <li>• Staff training</li>
                                <li>• Policy enforcement</li>
                            </ul>
                        </div>
                        <div class="bg-white p-6 rounded-lg border border-gray-200 text-center">
                            <i class="fas fa-shield-alt text-[#cc8e45] text-3xl mb-4"></i>
                            <h4 class="font-bold text-[#33595a] mb-2">Technical Security</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Data encryption</li>
                                <li>• Secure servers</li>
                                <li>• Regular updates</li>
                            </ul>
                        </div>
                        <div class="bg-white p-6 rounded-lg border border-gray-200 text-center">
                            <i class="fas fa-server text-[#cc8e45] text-3xl mb-4"></i>
                            <h4 class="font-bold text-[#33595a] mb-2">Physical Security</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Secure facilities</li>
                                <li>• Access monitoring</li>
                                <li>• Backup systems</li>
        </ul>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 6 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">6</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Policy for Children</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We do not knowingly solicit information from or market to children under the age of 13. If you become aware of any data we have collected from children under age 13, please contact us using the contact information provided below.
                    </p>
                    <div class="bg-[#e1e7dd] p-4 rounded-lg">
                        <h4 class="font-bold text-[#33595a] mb-2">Child Protection</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• We do not knowingly collect data from children under 13</li>
                            <li>• Parents can request deletion of their child's data</li>
                            <li>• We comply with COPPA and other child protection laws</li>
                            <li>• Report any concerns immediately</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Section 7 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">7</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Changes to This Privacy Policy</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We may update this Privacy Policy from time to time. The updated version will be indicated by an updated "Revised" date and the updated version will be effective as soon as it is accessible. We encourage you to review this privacy policy frequently to be informed of how we are protecting your information.
                    </p>
                    <div class="bg-[#e1e7dd] p-4 rounded-lg">
                        <h4 class="font-bold text-[#33595a] mb-2">Notification Process</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Changes will be posted on this page</li>
                            <li>• Email notifications for significant changes</li>
                            <li>• Updated "Last Modified" date</li>
                            <li>• Previous versions available upon request</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Section 8 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">8</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Contact Us</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        If you have questions or comments about this Privacy Policy, please contact us using the information below.
                    </p>
                    <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] p-6 rounded-lg text-white">
                        <h4 class="font-bold text-xl mb-4">Get in Touch</h4>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="mb-2">
                                    <i class="fas fa-envelope mr-2"></i>
                                    <strong>Email:</strong> support@silmatch.com.au
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-clock mr-2"></i>
                                    <strong>Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM AEST
                                </p>
                            </div>
                            <div>
                                <p class="mb-2">
                                    <i class="fas fa-phone mr-2"></i>
                                    <strong>Phone:</strong> Available through contact form
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-reply mr-2"></i>
                                    <strong>Response Time:</strong> Within 24 hours
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="max-w-4xl mx-auto mt-12">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-[#cc8e45] hover:bg-[#a67137] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 text-center">
                <i class="fas fa-envelope mr-2"></i>Contact Support
            </a>
            <a href="{{ route('terms') }}" class="bg-transparent border-2 border-[#cc8e45] hover:bg-[#cc8e45] hover:text-white text-[#cc8e45] px-8 py-3 rounded-lg font-semibold transition-colors duration-200 text-center">
                <i class="fas fa-file-contract mr-2"></i>Terms of Service
            </a>
            <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 text-center">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
        </a>
    </div>
    </div>

</div>
@endsection