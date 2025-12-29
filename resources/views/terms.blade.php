@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans">

    {{-- Hero Section --}}
    <section class="text-center mb-16 lg:mb-24">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl lg:text-7xl font-extrabold text-[#33595a] mb-6 leading-tight">
                Terms of <span class="text-[#cc8e45]">Service</span>
            </h1>
            <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                Please read these terms carefully before using SIL Match. By accessing our platform, you agree to be bound by these terms and conditions.
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
                    <h2 class="text-3xl font-bold text-[#33595a]">Acceptance of Terms</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-4">
                        By accessing or using the SIL Match website and services, you agree to be bound by these Terms of Service ("Terms"). If you do not agree to all the terms and conditions of this agreement, then you may not access the website or use any services.
                    </p>
                    <div class="bg-[#e1e7dd] p-4 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Important:</strong> These terms constitute a legally binding agreement between you and SIL Match.
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
                    <h2 class="text-3xl font-bold text-[#33595a]">Services Provided</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        SIL Match provides a comprehensive platform for NDIS Participants, Support Coordinators, and Accommodation Providers to connect and facilitate housing solutions.
                    </p>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <div class="text-center mb-4">
                                <i class="fas fa-user text-[#cc8e45] text-3xl mb-2"></i>
                                <h3 class="font-bold text-[#33595a]">For Participants</h3>
                            </div>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li>• Profile creation and management</li>
                                <li>• Browse compatible housemates</li>
                                <li>• Search accommodation listings</li>
                                <li>• Secure messaging system</li>
                            </ul>
                        </div>
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <div class="text-center mb-4">
                                <i class="fas fa-hands-helping text-[#cc8e45] text-3xl mb-2"></i>
                                <h3 class="font-bold text-[#33595a]">For Coordinators</h3>
                            </div>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li>• Manage participant profiles</li>
                                <li>• Connect with providers</li>
                                <li>• Facilitate matches</li>
                                <li>• Track outcomes</li>
                            </ul>
                        </div>
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <div class="text-center mb-4">
                                <i class="fas fa-building text-[#cc8e45] text-3xl mb-2"></i>
                                <h3 class="font-bold text-[#33595a]">For Providers</h3>
                            </div>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li>• List properties (Premium)</li>
                                <li>• Connect with participants</li>
                                <li>• Manage enquiries</li>
                                <li>• Access analytics</li>
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
                    <h2 class="text-3xl font-bold text-[#33595a]">User Responsibilities</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        Users are responsible for maintaining the confidentiality of their account and password and for restricting access to their computer. You agree to accept responsibility for all activities that occur under your account or password.
                    </p>
                    <div class="space-y-4">
                        <div class="bg-[#e1e7dd] p-4 rounded-lg">
                            <h4 class="font-bold text-[#33595a] mb-2">Account Security</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Keep your login credentials secure and confidential</li>
                                <li>• Notify us immediately of any unauthorized access</li>
                                <li>• Use strong, unique passwords</li>
                                <li>• Log out when using shared devices</li>
                            </ul>
                        </div>
                        <div class="bg-[#e1e7dd] p-4 rounded-lg">
                            <h4 class="font-bold text-[#33595a] mb-2">Accurate Information</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Provide accurate and up-to-date information</li>
                                <li>• Update your profile when circumstances change</li>
                                <li>• Verify information before sharing</li>
                                <li>• Report any errors or discrepancies</li>
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
                    <h2 class="text-3xl font-bold text-[#33595a]">Content and Conduct</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        You agree not to post, transmit, or otherwise make available any content that is unlawful, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, libelous, invasive of another's privacy, hateful, or racially, ethnically or otherwise objectionable.
                    </p>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                            <h4 class="font-bold text-red-800 mb-3">
                                <i class="fas fa-times-circle mr-2"></i>
                                Prohibited Content
                            </h4>
                            <ul class="text-sm text-red-700 space-y-2">
                                <li>• Discriminatory or offensive language</li>
                                <li>• False or misleading information</li>
                                <li>• Spam or unsolicited communications</li>
                                <li>• Copyrighted material without permission</li>
                                <li>• Personal information of others</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                            <h4 class="font-bold text-green-800 mb-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                Appropriate Content
                            </h4>
                            <ul class="text-sm text-green-700 space-y-2">
                                <li>• Respectful and professional communication</li>
                                <li>• Accurate and helpful information</li>
                                <li>• Constructive feedback and support</li>
                                <li>• Relevant and meaningful content</li>
                                <li>• Privacy-conscious sharing</li>
                            </ul>
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
                    <h2 class="text-3xl font-bold text-[#33595a]">Disclaimers and Limitation of Liability</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        The services are provided "as is" without warranty of any kind. In no event shall SIL Match be liable for any direct, indirect, incidental, special, consequential, or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data, or other intangible losses.
                    </p>
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-yellow-800 mb-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Important Limitations
                        </h4>
                        <ul class="text-sm text-yellow-700 space-y-2">
                            <li>• We are a matching service only, not a provider of SIL or SDA services</li>
                            <li>• We do not guarantee successful matches or outcomes</li>
                            <li>• Users are responsible for their own safety and due diligence</li>
                            <li>• We are not liable for disputes between users</li>
                            <li>• Service availability is not guaranteed</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Section 6 --}}
            <section class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="bg-[#cc8e45] rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-xl">6</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#33595a]">Changes to Terms</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        We reserve the right to modify these Terms at any time. We will notify you of any changes by posting the new Terms on this page and updating the "Last Updated" date.
                    </p>
                    <div class="bg-[#e1e7dd] p-4 rounded-lg">
                        <h4 class="font-bold text-[#33595a] mb-2">Notification Process</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Changes will be posted on this page</li>
                            <li>• Email notifications for significant changes</li>
                            <li>• Continued use constitutes acceptance</li>
                            <li>• Previous versions available upon request</li>
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
                    <h2 class="text-3xl font-bold text-[#33595a]">Contact Information</h2>
                </div>
                <div class="pl-16">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        If you have any questions about these Terms, please contact us using the information below.
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
            <a href="{{ route('policy') }}" class="bg-transparent border-2 border-[#cc8e45] hover:bg-[#cc8e45] hover:text-white text-[#cc8e45] px-8 py-3 rounded-lg font-semibold transition-colors duration-200 text-center">
                <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
            </a>
            <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 text-center">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
        </a>
    </div>
    </div>

</div>
@endsection