@extends('layouts.app')

@section('content')

    <section class="relative h-screen flex items-center justify-center text-white overflow-hidden bg-cover bg-center"
        style="background-image: url('images/VecrtorWB.png'); margin-top:-100px"
        data-parallax-speed="0.3">
        {{-- Subtle gradient overlay for aesthetic depth and text readability using the new palette --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#33595a] via-[#3e4732] to-black opacity-0 z-10"></div>

        <div class="container mx-auto px-6 relative z-20 text-center">
            <h1 class="text-4xl sm:text-6xl lg:text-8xl font-extrabold leading-tight mb-6
                       animate-fade-in-up drop-shadow-2xl tracking-tight">
                Your <span class="block mt-4 text-[#cc8e45] transform hover:scale-105 transition-transform duration-300 ease-out">Journey to Independence</span> Starts Here
            </h1>
            <p class="text-lg sm:text-2xl text-[#f8f1e1] mb-12 max-w-5xl mx-auto
                      animate-fade-in-up delay-200 drop-shadow-lg leading-relaxed">
                Touch D Cloud is your trusted partner for NDIS participant accommodation and support coordination. We empower you to live independently and comfortably, <span class="text-[#cc8e45] transition-transform duration-300 ease-out">every step of the way</span>.
            </p>
            <a href="{{ route('listings') }}"
               class="inline-block bg-[#cc8e45] text-white hover:bg-[#a67137] font-extrabold py-4 px-10 rounded-full text-xl sm:text-2xl shadow-2xl
                      transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                      animate-fade-in-up delay-400 border-2 border-[#cc8e45] focus:outline-none focus:ring-4 focus:ring-[#cc8e45]">
                Find Your Perfect Home <span class="ml-2">→</span>
            </a>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-gradient-to-br from-[#f8f1e1] to-[#ffffff]">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold text-[#33595a] mb-16 animate-fade-in-down">
                How Touch D Cloud Empowers You
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-[#cc8e45]
                            group flex flex-col items-center animate-fade-in-left">
                    <div class="bg-[#e1e7dd] rounded-full p-6 mb-8 group-hover:bg-[#bcbabb] transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/2563EB/home-page.png" alt="Home Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-[#3e4732] mb-4 group-hover:text-[#cc8e45] transition-colors duration-300">Curated Accommodation</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Explore a carefully selected range of NDIS-friendly homes tailored to diverse needs and preferences, ensuring comfort and suitability.
                    </p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-[#33595a]
                            group flex flex-col items-center animate-fade-in-up delay-200">
                    <div class="bg-[#e1e7dd] rounded-full p-6 mb-8 group-hover:bg-[#bcbabb] transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/4F46E5/customer-support.png" alt="Support Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-[#3e4732] mb-4 group-hover:text-[#cc8e45] transition-colors duration-300">Expert Support Coordination</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Connect with dedicated support coordinators who provide personalized guidance to navigate your NDIS plan with clarity and confidence.
                    </p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-[#3e4732]
                            group flex flex-col items-center animate-fade-in-right delay-400">
                    <div class="bg-[#e1e7dd] rounded-full p-6 mb-8 group-hover:bg-[#bcbabb] transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/9333EA/medal.png" alt="Independence Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-[#3e4732] mb-4 group-hover:text-[#cc8e45] transition-colors duration-300">Achieve True Independence</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        We empower NDIS participants to live fulfilling, independent lives by providing the right environment and comprehensive assistance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-[#e1e7dd] text-[#33595a]">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold mb-8 animate-fade-in-down">
                Choose the Plan That's Right for You
            </h2>
            <p class="text-xl text-[#3e4732] mb-16 max-w-3xl mx-auto animate-fade-in-down delay-200">
                Tailored support options to match your unique needs and aspirations within the NDIS framework.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16">

                {{-- Package 1: Community Access Plan --}}
                <div class="bg-white rounded-3xl shadow-xl p-8 flex flex-col justify-between
                            transform hover:scale-[1.03] transition-all duration-500 animate-fade-in-left">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 1 --}}
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/011/356/176/small_2x/doctors-and-health-workers-3d-character-illustration-png.png"
                             alt="Community Access Illustration" class="mx-auto mb-6 rounded-lg shadow-md">
                        <h3 class="text-3xl font-extrabold text-[#33595a] mb-4">Community Access Plan</h3>
                        <p class="text-lg text-[#bcbabb] mb-6">Small operations, easy start</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <ul class="text-left text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Basic Accommodation Listings</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Community Event Calendar</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Essential Communication Tools</li>
                        </ul>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#33595a] text-white font-bold py-4 px-6 rounded-full
                                      hover:bg-[#3e4732] transition duration-300 ease-in-out shadow-lg
                                      focus:outline-none focus:ring-4 focus:ring-[#bcbabb]">
                        Get Started
                    </a>
                </div>

                {{-- Package 2: Inclusive Living Plan --}}
                <div class="bg-[#33595a] rounded-3xl shadow-2xl p-8 flex flex-col justify-between border-8 border-[#cc8e45]
                            transform hover:scale-[1.05] transition-all duration-500 animate-fade-in-up delay-200">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 2 --}}
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/011/356/176/small_2x/doctors-and-health-workers-3d-character-illustration-png.png"
                             alt="Inclusive Living Illustration" class="mx-auto mb-6 rounded-lg shadow-md">
                        <h3 class="text-3xl font-extrabold text-[#ffffff] mb-4">Inclusive Living Plan</h3>
                        <p class="text-lg text-[#f8f1e1] mb-6">Medium-sized provider support</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <ul class="text-left text-lg text-[#f8f1e1] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Everything in Community Access</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Advanced Matching Algorithms</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Dedicated Support Coordinator Access</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Enhanced Communication Tools</li>
                        </ul>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-4 px-6 rounded-full
                                      hover:bg-[#a67137] transition duration-300 ease-in-out shadow-lg
                                      focus:outline-none focus:ring-4 focus:ring-[#f8f1e1]">
                        Get Started
                    </a>
                </div>

                {{-- Package 3: Full Support Plan --}}
                <div class="bg-white rounded-3xl shadow-xl p-8 flex flex-col justify-between
                            transform hover:scale-[1.03] transition-all duration-500 animate-fade-in-right">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 3 --}}
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/011/356/176/small_2x/doctors-and-health-workers-3d-character-illustration-png.png"
                             alt="Full Support Illustration" class="mx-auto mb-6 rounded-lg shadow-md">
                        <h3 class="text-3xl font-extrabold text-[#33595a] mb-4">Full Support Plan</h3>
                        <p class="text-lg text-[#bcbabb] mb-6">Advanced matching for large provider</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <ul class="text-left text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Everything in Inclusive Living</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Dedicated Account Manager</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Premium Provider Network Access</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Custom Reporting & Insights</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Priority Support</li>
                        </ul>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#33595a] text-white font-bold py-4 px-6 rounded-full
                                      hover:bg-[#3e4732] transition duration-300 ease-in-out shadow-lg
                                      focus:outline-none focus:ring-4 focus:ring-[#bcbabb]">
                        Get Started
                    </a>
                </div>

            </div> {{-- End grid --}}

            <p class="text-xl text-[#3e4732] mt-16 animate-fade-in-up delay-400">
            </p>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-[#f8f1e1]"> {{-- Updated background color --}}
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="flex justify-center md:justify-start transform hover:scale-102 transition-transform duration-500 animate-fade-in-left">
                <img src="images/hero-bg2.jpg"
                     alt="Connecting people with support"
                     class="rounded-2xl shadow-2xl max-w-full h-auto object-cover border-4 border-[#cc8e45]" style="max-height: 500px;"> {{-- Updated border color --}}
            </div>
            <div class="text-center md:text-left animate-fade-in-right">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-[#33595a] mb-8 leading-tight"> {{-- Updated text color --}}
                    Our Commitment: <span class="text-[#cc8e45]">Accessibility & Empowerment</span> {{-- Updated text color --}}
                </h2>
                <p class="text-xl text-[#3e4732] mb-8 leading-relaxed"> {{-- Updated text color --}}
                    At Touch D Cloud, we are driven by the belief that every individual deserves a supportive and independent living environment. Our platform is meticulously designed for **transparency, ease of access, and genuine care**.
                </p>
                <p class="text-lg text-[#bcbabb] mb-10 leading-relaxed"> {{-- Updated text color --}}
                    We've simplified the journey for NDIS participants and their families, providing a user-friendly and comprehensive resource for finding ideal homes and essential support services.
                </p>
                <a href="{{ route('about') }}"
                   class="inline-block bg-[#cc8e45] hover:bg-[#a67137] text-white font-bold py-4 px-8 rounded-full shadow-lg
                          transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl
                          focus:outline-none focus:ring-4 focus:ring-[#f8f1e1]"> {{-- Updated button colors --}}
                    Discover Our Vision <span class="ml-2">→</span>
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-[#ffffff]"> {{-- Updated background color --}}
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold text-[#33595a] mb-16 animate-fade-in-down"> {{-- Updated text color --}}
                Hear From Our Thriving Community
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-[#f8f1e1] p-10 rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl {{-- Updated background color --}}
                            transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-left">
                    <p class="text-xl text-[#3e4732] mb-8 italic leading-relaxed font-serif"> {{-- Updated text color --}}
                        "Touch D Cloud was a game-changer for me. Finding suitable NDIS accommodation felt overwhelming, but their platform made it incredibly simple and reassuring. The support truly made a difference!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1976&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="Jane Doe Avatar" class="w-20 h-20 rounded-full mr-5 object-cover border-4 border-[#cc8e45] shadow-lg"> {{-- Updated border color --}}
                        <div>
                            <p class="font-extrabold text-[#33595a] text-2xl">Jane Doe</p> {{-- Updated text color --}}
                            <p class="text-md text-[#3e4732]">NDIS Participant</p> {{-- Updated text color --}}
                        </div>
                    </div>
                </div>
                <div class="bg-[#f8f1e1] p-10 rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl {{-- Updated background color --}}
                            transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-right delay-200">
                    <p class="text-xl text-[#3e4732] mb-8 italic leading-relaxed font-serif"> {{-- Updated text color --}}
                        "As a support coordinator, I rely on Touch D Cloud daily. It's an indispensable resource that significantly streamlines the process of connecting participants with the ideal housing and support they need. Highly efficient and reliable!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="John Smith Avatar" class="w-20 h-20 rounded-full mr-5 object-cover border-4 border-[#33595a] shadow-lg"> {{-- Updated border color --}}
                        <div>
                            <p class="font-extrabold text-[#33595a] text-2xl">John Smith</p> {{-- Updated text color --}}
                            <p class="text-md text-[#3e4732]">Support Coordinator</p> {{-- Updated text color --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#33595a] py-20 sm:py-32 text-white text-center"> {{-- Updated background color --}}
        <div class="container mx-auto px-6">
            <h2 class="text-4xl sm:text-5xl font-extrabold mb-8 drop-shadow-lg animate-fade-in-down">
                Ready to Take the Next Step Towards <span class="text-[#cc8e45]">Independence?</span>
            </h2>
            <p class="text-xl text-[#f8f1e1] mb-12 max-w-4xl mx-auto drop-shadow-md leading-relaxed"> {{-- Updated text color --}}
                Join our growing community and experience the simplicity of finding ideal NDIS accommodation and expert support, tailored just for you.
            </p>
            <a href="{{ route('register') }}"
               class="inline-block bg-[#cc8e45] text-white hover:bg-[#a67137] font-extrabold py-5 px-12 rounded-full text-xl sm:text-2xl shadow-2xl
                      transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                      focus:outline-none focus:ring-4 focus:ring-[#f8f1e1]"> {{-- Updated button colors --}}
                Register Your Account Today! <span class="ml-2">→</span>
            </a>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const parallaxSection = document.querySelector('[data-parallax-speed]');
            if (parallaxSection) {
                const speed = parseFloat(parallaxSection.dataset.parallaxSpeed);

                // Initial position to prevent jump on load
                parallaxSection.style.backgroundPositionY = '0px';

                window.addEventListener('scroll', function() {
                    const scrollTop = window.pageYOffset;
                    const yPos = -(scrollTop * speed);
                    parallaxSection.style.backgroundPositionY = yPos + 'px';
                });
            }

            // Simple Scroll Reveal for sections (using Intersection Observer)
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1 // Trigger when 10% of the element is visible
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-active'); // Add a class to trigger CSS animation
                        observer.unobserve(entry.target); // Stop observing once animated
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-fade-in-down, .animate-fade-in-left, .animate-fade-in-right, .animate-fade-in-up').forEach(element => {
                element.classList.add('opacity-0'); // Hide initially
                observer.observe(element);
            });
        });
    </script>

    <style>
        /* Tailwind Animations (You should ideally configure these in tailwind.config.js for production) */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-down.animate-active {
            animation: fadeInDown 1s ease-out forwards;
        }
        .animate-fade-in-up.animate-active {
            animation: fadeInUp 1s ease-out forwards;
        }
        .animate-fade-in-left.animate-active {
            animation: fadeInLeft 1s ease-out forwards;
        }
        .animate-fade-in-right.animate-active {
            animation: fadeInRight 1s ease-out forwards;
        }

        /* Specific delays for hero section elements */
        .animate-fade-in-up.delay-200 { animation-delay: 0.2s; }
        .animate-fade-in-up.delay-400 { animation-delay: 0.4s; }

        /* General animation delays for feature cards etc. */
        .delay-200 { animation-delay: 0.2s; }
        .delay-400 { animation-delay: 0.4s; }
    </style>

@endsection