@extends('layouts.app')

@section('content')

    <section class="relative h-screen flex items-center justify-center text-white overflow-hidden bg-cover bg-center"
        style="background-image: url('images/hero-bg2.jpg');"
        data-parallax-speed="0.3">
        {{-- Subtle gradient overlay for aesthetic depth and text readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 opacity-80 z-10"></div>

        <div class="container mx-auto px-6 relative z-20 text-center">
            <h1 class="text-4xl sm:text-6xl lg:text-8xl font-extrabold leading-tight mb-6
                       animate-fade-in-up drop-shadow-2xl tracking-tight">
                Your <span class="block mt-4 text-blue-300 transform hover:scale-105 transition-transform duration-300 ease-out">Journey to Independence</span> Starts Here
            </h1>
            <p class="text-lg sm:text-2xl text-blue-100 mb-12 max-w-5xl mx-auto
                      animate-fade-in-up delay-200 drop-shadow-lg leading-relaxed">
                Touch D Cloud is your trusted partner for NDIS participant accommodation and support coordination. We empower you to live independently and comfortably, <span class="text-blue-300">every step of the way</span>.
            </p>
            <a href="{{ route('listings') }}"
               class="inline-block bg-white text-blue-800 hover:bg-gray-100 font-extrabold py-4 px-10 rounded-full text-xl sm:text-2xl shadow-2xl
                      transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                      animate-fade-in-up delay-400 border-2 border-white focus:outline-none focus:ring-4 focus:ring-blue-300">
                Find Your Perfect Home <span class="ml-2">→</span>
            </a>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-16 animate-fade-in-down">
                How Touch D Cloud Empowers You
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-blue-600
                            group flex flex-col items-center animate-fade-in-left">
                    <div class="bg-blue-100 rounded-full p-6 mb-8 group-hover:bg-blue-200 transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/2563EB/home-page.png" alt="Home Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 group-hover:text-blue-700 transition-colors duration-300">Curated Accommodation</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Explore a carefully selected range of NDIS-friendly homes tailored to diverse needs and preferences, ensuring comfort and suitability.
                    </p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-indigo-600
                            group flex flex-col items-center animate-fade-in-up delay-200">
                    <div class="bg-indigo-100 rounded-full p-6 mb-8 group-hover:bg-indigo-200 transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/4F46E5/customer-support.png" alt="Support Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 group-hover:text-indigo-700 transition-colors duration-300">Expert Support Coordination</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Connect with dedicated support coordinators who provide personalized guidance to navigate your NDIS plan with clarity and confidence.
                    </p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                            transform hover:-translate-y-4 border-b-8 border-purple-600
                            group flex flex-col items-center animate-fade-in-right delay-400">
                    <div class="bg-purple-100 rounded-full p-6 mb-8 group-hover:bg-purple-200 transition-colors duration-300">
                        <img src="https://img.icons8.com/ios-filled/120/9333EA/medal.png" alt="Independence Icon"
                             class="w-28 h-28 object-contain filter drop-shadow-md group-hover:drop-shadow-lg transition-all duration-300">
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 group-hover:text-purple-700 transition-colors duration-300">Achieve True Independence</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        We empower NDIS participants to live fulfilling, independent lives by providing the right environment and comprehensive assistance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-gray-100">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="flex justify-center md:justify-start transform hover:scale-102 transition-transform duration-500 animate-fade-in-left">
                <img src="images/hero-bg2.jpg"
                     alt="Connecting people with support"
                     class="rounded-2xl shadow-2xl max-w-full h-auto object-cover border-4 border-blue-300" style="max-height: 500px;">
            </div>
            <div class="text-center md:text-left animate-fade-in-right">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-8 leading-tight">
                    Our Commitment: <span class="text-blue-700">Accessibility & Empowerment</span>
                </h2>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    At Touch D Cloud, we are driven by the belief that every individual deserves a supportive and independent living environment. Our platform is meticulously designed for **transparency, ease of access, and genuine care**.
                </p>
                <p class="text-lg text-gray-600 mb-10 leading-relaxed">
                    We've simplified the journey for NDIS participants and their families, providing a user-friendly and comprehensive resource for finding ideal homes and essential support services.
                </p>
                <a href="{{ route('about') }}"
                   class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 px-8 rounded-full shadow-lg
                          transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl
                          focus:outline-none focus:ring-4 focus:ring-blue-300">
                    Discover Our Vision <span class="ml-2">→</span>
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-32 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-16 animate-fade-in-down">
                Hear From Our Thriving Community
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-gray-50 p-10 rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl
                            transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-left">
                    <p class="text-xl text-gray-700 mb-8 italic leading-relaxed font-serif">
                        "Touch D Cloud was a game-changer for me. Finding suitable NDIS accommodation felt overwhelming, but their platform made it incredibly simple and reassuring. The support truly made a difference!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1976&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="Jane Doe Avatar" class="w-20 h-20 rounded-full mr-5 object-cover border-4 border-blue-400 shadow-lg">
                        <div>
                            <p class="font-extrabold text-gray-900 text-2xl">Jane Doe</p>
                            <p class="text-md text-gray-600">NDIS Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-10 rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl
                            transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-right delay-200">
                    <p class="text-xl text-gray-700 mb-8 italic leading-relaxed font-serif">
                        "As a support coordinator, I rely on Touch D Cloud daily. It's an indispensable resource that significantly streamlines the process of connecting participants with the ideal housing and support they need. Highly efficient and reliable!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="John Smith Avatar" class="w-20 h-20 rounded-full mr-5 object-cover border-4 border-indigo-400 shadow-lg">
                        <div>
                            <p class="font-extrabold text-gray-900 text-2xl">John Smith</p>
                            <p class="text-md text-gray-600">Support Coordinator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-blue-800 py-20 sm:py-32 text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl sm:text-5xl font-extrabold mb-8 drop-shadow-lg animate-fade-in-down">
                Ready to Take the Next Step Towards <span class="text-blue-300">Independence?</span>
            </h2>
            <p class="text-xl text-blue-100 mb-12 max-w-4xl mx-auto drop-shadow-md leading-relaxed">
                Join our growing community and experience the simplicity of finding ideal NDIS accommodation and expert support, tailored just for you.
            </p>
            <a href="{{ route('register') }}"
               class="inline-block bg-white text-blue-800 hover:bg-gray-100 font-extrabold py-5 px-12 rounded-full text-xl sm:text-2xl shadow-2xl
                      transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                      focus:outline-none focus:ring-4 focus:ring-blue-300">
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