@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 md:py-12 lg:py-16 font-sans">

        {{-- Hero Section --}}
        <section class="text-center mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl lg:text-7xl font-extrabold text-[#33595a] mb-6 leading-tight">
                    Contact <span class="text-[#cc8e45]">Support</span>
                </h1>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    Get in touch with our support team. We're here to help you with any questions or concerns.
                </p>
            </div>
        </section>

        {{-- Contact Form --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    
                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Please correct the following errors:</strong>
                            </div>
                            <ul class="list-disc list-inside ml-4">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#33595a] mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter your full name">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#33595a] mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('email') border-red-500 @enderror"
                                   placeholder="Enter your email address">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- User Type --}}
                        <div>
                            <label for="user_type" class="block text-sm font-medium text-[#33595a] mb-2">
                                I am a...
                            </label>
                            <select name="user_type" 
                                    id="user_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('user_type') border-red-500 @enderror">
                                <option value="">Select your role</option>
                                <option value="participant" {{ old('user_type') == 'participant' ? 'selected' : '' }}>NDIS Participant</option>
                                <option value="coordinator" {{ old('user_type') == 'coordinator' ? 'selected' : '' }}>Support Coordinator</option>
                                <option value="provider" {{ old('user_type') == 'provider' ? 'selected' : '' }}>SIL/SDA Provider</option>
                                <option value="other" {{ old('user_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('user_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-[#33595a] mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   value="{{ old('subject') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('subject') border-red-500 @enderror"
                                   placeholder="What is this about?">
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="message" class="block text-sm font-medium text-[#33595a] mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message" 
                                      rows="6"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('message') border-red-500 @enderror"
                                      placeholder="Please describe your question or concern in detail...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full bg-[#cc8e45] hover:bg-[#a67137] text-white font-bold py-4 px-6 rounded-lg transition-colors duration-200 shadow-md">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- Contact Information --}}
        <section class="mb-16 lg:mb-24">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl lg:text-4xl font-bold text-center text-[#33595a] mb-12">Other Ways to Reach Us</h2>
                
                <div class="grid md:grid-cols-3 gap-8">
                    {{-- Email --}}
                    <div class="text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-2">Email Support</h3>
                        <p class="text-gray-600 mb-2">Send us an email anytime</p>
                        <a href="mailto:support@silmatch.com.au" class="text-[#cc8e45] hover:underline font-medium">
                            support@silmatch.com.au
                        </a>
                    </div>

                    {{-- Response Time --}}
                    <div class="text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-2">Response Time</h3>
                        <p class="text-gray-600 mb-2">We typically respond within</p>
                        <p class="text-[#cc8e45] font-bold text-lg">24 hours</p>
                    </div>

                    {{-- Business Hours --}}
                    <div class="text-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar text-[#33595a] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-2">Business Hours</h3>
                        <p class="text-gray-600 mb-2">Monday to Friday</p>
                        <p class="text-[#cc8e45] font-bold text-lg">9 AM - 5 PM AEST</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ Link --}}
        <section class="text-center">
            <div class="bg-gradient-to-r from-[#33595a] to-[#3e4732] rounded-2xl p-8 md:p-12 text-white">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Need Quick Answers?</h2>
                <p class="text-lg mb-8 opacity-90">Check our comprehensive FAQ section for instant answers to common questions</p>
                <a href="{{ route('faqs') }}" class="bg-[#cc8e45] hover:bg-[#a67137] text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200">
                    <i class="fas fa-question-circle mr-2"></i>View FAQs
                </a>
            </div>
        </section>

    </div>

    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
