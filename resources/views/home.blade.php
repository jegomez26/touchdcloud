@extends('layouts.app')

@section('content')

    <section class="hero-section relative h-screen flex items-center justify-center text-white overflow-hidden bg-cover bg-center"
        style="margin-top:-100px; padding-top: 150px; ">
        {{-- Parallax Layers - Only enabled on larger screens --}}
        {{-- The z-index values create an explicit stacking order from back to front. --}}
        <div class="absolute inset-0 parallax-layer hidden md:block" style="background-repeat: no-repeat; background-image: url('images/Sunset.png'); background-size: 100vw auto; z-index: 10;" data-parallax-speed="1"></div>
        <div class="absolute inset-0 parallax-layer hidden md:block" style="background-repeat: no-repeat; background-image: url('images/Greens.png'); background-size: 100vw auto; z-index: 11;" data-parallax-speed="-1.3"></div>
        <div class="absolute inset-0 parallax-layer hidden md:block" style="background-repeat: no-repeat; background-image: url('images/BackHouse.png'); background-size: 100vw auto; z-index: 12;" data-parallax-speed="-1"></div>

        {{-- Static background layers for mobile --}}
        <div class="absolute inset-0 md:hidden bg-cover bg-center" style="background-image: url('images/Sunset.png'); z-index: 10;"></div>
        <div class="absolute inset-0 md:hidden bg-cover bg-center" style="background-image: url('images/Greens.png'); z-index: 11;"></div>
        <div class="absolute inset-0 md:hidden bg-cover bg-center" style="background-image: url('images/BackHouse.png'); z-index: 12;"></div>

        {{-- FIXED: These layers are positioned behind the text with lower z-index --}}
        <div class="absolute inset-0 parallax-layer hidden md:block" style="background-repeat: no-repeat; background-image: url('images/FrontHouse.png'); background-size: 100vw auto; z-index: 20;" data-parallax-speed="-0.5"></div>
        <div class="absolute inset-0 parallax-layer hidden md:block" style="background-repeat: no-repeat; background-image: url('images/Boy-Girl.png'); background-size: 100vw auto; z-index: 25;" data-parallax-speed="0" data-parallax-x-speed="-1"></div>
        
        {{-- FIXED: Static foreground layers for mobile with proper z-index --}}
        <div class="absolute inset-0 md:hidden bg-cover bg-center" style="background-image: url('images/FrontHouse.png'); z-index: 20;"></div>
        <div class="absolute inset-0 md:hidden bg-cover bg-center" style="background-image: url('images/Boy-Girl.png'); z-index: 25;"></div>
        
        {{-- FIXED: Text container with proper positioning and z-index to stay above images --}}
        <div class="container mx-auto px-3 xs:px-4 sm:px-6 relative z-30 text-left 
                    transform -translate-y-16 xs:-translate-y-20 sm:-translate-y-24 md:-translate-y-32 lg:-translate-y-40 xl:-translate-y-48 2xl:-translate-y-56">
            <h1 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-extrabold leading-tight
                         animate-fade-in-up tracking-tight drop-shadow-lg">
                <span class="text-[#cc8e45] transform hover:scale-105 transition-transform duration-300 ease-out">Journey to Independence</span>
            </h1>
            <h2 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-extrabold leading-tight mb-1 xs:mb-2 sm:mb-3 md:mb-4 lg:mb-5
                         animate-fade-in-up tracking-tight drop-shadow-lg">
                Starts Here
            </h2>
        </div>

        {{-- FIXED: Transparent box with proper positioning to stay within hero bounds --}}
        <div class="absolute inset-0 flex items-end justify-center md:justify-end z-30 px-3 xs:px-4 sm:px-6 md:px-8 lg:px-10 xl:px-20 pt-8 xs:pt-10 sm:pt-12 md:pt-16 lg:pt-20 xl:pt-24 pb-2 xs:pb-3 sm:pb-4 md:pb-6 lg:pb-8 xl:pb-10">
            <div class="bg-white/10 backdrop-blur-sm p-3 xs:p-4 sm:p-5 md:p-6 lg:p-8 relative z-30 rounded-xl border border-white/20 max-w-xs xs:max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl w-full
                         animate-fade-in-up delay-200 drop-shadow-lg">
                <p class="text-xs xs:text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl text-[#000000] leading-relaxed mb-3 xs:mb-4 sm:mb-5 md:mb-6">
                    SIL Match helps NDIS participants find the right people to live with. We connect you with housemates who understand your needs, share your goals, and help create a supportive and comfortable home life.
                </p>
                <a href="{{ route('listings') }}"
                   class="inline-block bg-[#cc8e45] text-white hover:bg-[#a67137] font-extrabold py-2 xs:py-3 sm:py-3 md:py-4 lg:py-4 xl:py-4 px-4 xs:px-6 sm:px-6 md:px-8 lg:px-10 xl:px-10 rounded-full text-xs xs:text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl shadow-2xl
                                 transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                                 animate-fade-in-up delay-400 border-2 border-[#cc8e45] focus:outline-none focus:ring-4 focus:ring-[#cc8e45] w-full sm:w-auto text-center">
                    Find Your Perfect Match <span class="ml-1 xs:ml-2">→</span>
                </a>
            </div>
        </div>
    </section>
    
    <section class="relative py-8 xs:py-10 sm:py-12 md:py-16 lg:py-20 xl:py-24 2xl:py-32 bg-gradient-to-br from-[#cc8e45] to-[#ffffff] overflow-hidden">
        {{-- HouseSmall.png: Behind cards, aligned to bottom-left, moves left-to-right on scroll --}}
        {{-- IMPROVED: Better responsive height and positioning --}}
        <div class="absolute bottom-0 left-0 right-0 h-[400px] xs:h-[500px] sm:h-[600px] md:h-[700px] lg:h-[800px] xl:h-[900px] parallax-layer hidden lg:block" 
            style="background-image: url('images/HouseSmall.png'); background-repeat: no-repeat; background-size: auto 100%; background-position: bottom left; z-index: 10;" 
            data-parallax-speed="0" data-parallax-x-speed="1">
        </div>
        
        {{-- PicnicFun.png: On top of cards, fixed at bottom-center --}}
        {{-- IMPROVED: Better responsive sizing and positioning --}}
        <img src="images/picnicfun.png" alt="picnicfun"
            class="absolute bottom-0 left-1/2 transform -translate-x-1/2 hidden lg:block object-contain z-40
                    w-40 h-40 xl:w-60 xl:h-60 2xl:w-80 2xl:h-80 translate-y-8 xl:translate-y-12 2xl:translate-y-14"> 
        
        <div class="container mx-auto px-3 xs:px-4 sm:px-6 md:px-8 lg:px-10 text-center relative z-10">
            <h2 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-[#ffffff] mb-6 xs:mb-8 sm:mb-10 md:mb-12 lg:mb-14 xl:mb-16 animate-fade-in-down">
                How SIL Match Empowers You
            </h2>
            <div class="grid grid-cols-1 xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 xs:gap-5 sm:gap-6 md:gap-8 lg:gap-10">
                <div class="backdrop-blur-md bg-white/10 border border-white/20 p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-2xl xs:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
            transform hover:-translate-y-2 xs:hover:-translate-y-3 sm:hover:-translate-y-4
            group flex flex-col items-center animate-fade-in-up delay-200">
                    <div class="absolute inset-0 w-full h-full object-cover opacity-10 rounded-2xl xs:rounded-3xl z-10"></div>
                    <h3 class="text-lg xs:text-xl sm:text-xl md:text-2xl lg:text-3xl font-bold text-[#3e4732] mb-2 xs:mb-3 sm:mb-3 md:mb-4 group-hover:text-[#cc8e45] z-20 transition-colors duration-300">Finding the Right Housemates</h3>
                    <p class="text-xs xs:text-sm sm:text-sm md:text-base lg:text-lg text-white-300 leading-relaxed z-20">
                        Our matching system connects NDIS participants with people who are a good fit to live with. We look at lifestyle, routines, location, and support needs so you can share a home with the right people. 
                    </p>
                </div>
                <div class="backdrop-blur-md bg-white/10 border border-white/20 p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-2xl xs:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                    transform hover:-translate-y-2 xs:hover:-translate-y-3 sm:hover:-translate-y-4
                    group flex flex-col items-center animate-fade-in-up delay-200">
                    <div class="absolute inset-0 w-full h-full object-cover opacity-40 rounded-2xl xs:rounded-3xl z-10"></div>
                    <h3 class="text-lg xs:text-xl sm:text-xl md:text-2xl lg:text-3xl font-bold text-[#3e4732] mb-2 xs:mb-3 sm:mb-3 md:mb-4 group-hover:text-[#cc8e45] z-20 transition-colors duration-300">Matches Made for You</h3>
                    <p class="text-xs xs:text-sm sm:text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed z-20">
                        See profiles of participants who share your interests and understand your needs. Every match is based on what matters most to you, helping you build a positive and supportive home life.
                    </p>
                </div>
                <div class="backdrop-blur-md bg-white/10 border border-white/20 p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-2xl xs:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                    transform hover:-translate-y-2 xs:hover:-translate-y-3 sm:hover:-translate-y-4
                    group flex flex-col items-center animate-fade-in-up delay-200">
                    <h3 class="text-lg xs:text-xl sm:text-xl md:text-2xl lg:text-3xl font-bold text-[#3e4732] mb-2 xs:mb-3 sm:mb-3 md:mb-4 group-hover:text-[#cc8e45] transition-colors duration-300">Support to Make it Happen</h3>
                    <p class="text-xs xs:text-sm sm:text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed">
                        We make it easy for you, your family, or your support coordinator to manage your profile and connect with potential housemates. You can message safely and decide who is the best fit.
                    </p>
                </div>
                <div class="backdrop-blur-md bg-white/10 border border-white/20 p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-2xl xs:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500
                    transform hover:-translate-y-2 xs:hover:-translate-y-3 sm:hover:-translate-y-4
                    group flex flex-col items-center animate-fade-in-up delay-200">
                    <h3 class="text-lg xs:text-xl sm:text-xl md:text-2xl lg:text-3xl font-bold text-[#3e4732] mb-2 xs:mb-3 sm:mb-3 md:mb-4 group-hover:text-[#cc8e45] transition-colors duration-300">Live Well Together</h3>
                    <p class="text-xs xs:text-sm sm:text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed">
                        When you live with the right people, it is easier to feel at home, grow your skills, and enjoy your independence. We are here to help you make that happen.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8 xs:py-10 sm:py-12 md:py-16 lg:py-20 xl:py-24 2xl:py-32 bg-[#f8f1e1]">
        <div class="container mx-auto px-3 xs:px-4 sm:px-6 md:px-8 lg:px-10 grid grid-cols-1 md:grid-cols-2 gap-6 xs:gap-8 sm:gap-10 md:gap-12 lg:gap-14 xl:gap-16 items-center">
            <div class="flex justify-center md:justify-start transform hover:scale-102 transition-transform duration-500 animate-fade-in-left order-2 md:order-1">
                <img src="images/hero-bg2.jpg"
                     alt="Connecting people with support"
                     class="rounded-xl xs:rounded-2xl shadow-2xl max-w-full h-auto object-cover border-2 xs:border-4 border-[#cc8e45] w-full max-h-48 xs:max-h-56 sm:max-h-64 md:max-h-72 lg:max-h-80 xl:max-h-96 2xl:max-h-[500px]">
            </div>
            <div class="text-center md:text-left animate-fade-in-right order-1 md:order-2">
                <h2 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-[#33595a] mb-4 xs:mb-5 sm:mb-6 md:mb-7 lg:mb-8 leading-tight">
                    Our Commitment: <span class="text-[#cc8e45]">Accessibility & Empowerment</span>
                </h2>
                <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-xl xl:text-xl text-[#3e4732] mb-4 xs:mb-5 sm:mb-6 md:mb-7 lg:mb-8 leading-relaxed">
                    At SIL Match, we believe everyone should have the chance to live with people who make them feel supported and included. Our platform is designed so NDIS participants can easily find and connect with housemates who understand their needs and respect their independence.
                </p>
                <p class="text-xs xs:text-sm sm:text-base md:text-lg lg:text-lg xl:text-lg text-[#3e4732] mb-6 xs:mb-7 sm:mb-8 md:mb-9 lg:mb-10 leading-relaxed">
                    We make sure our service is easy to use, safe, and welcoming for everyone, no matter their accessibility needs. Our goal is to help create living arrangements where people feel at home and can build a positive life together.
                </p>
                <a href="{{ route('about') }}"
                   class="inline-block bg-[#cc8e45] hover:bg-[#a67137] text-white font-bold py-2 xs:py-3 sm:py-3 md:py-4 lg:py-4 px-4 xs:px-6 sm:px-6 md:px-8 lg:px-8 rounded-full shadow-lg
                                 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl
                                 focus:outline-none focus:ring-4 focus:ring-[#f8f1e1] text-xs xs:text-sm sm:text-base md:text-base">
                    Discover Our Vision <span class="ml-1 xs:ml-2">→</span>
                </a>
            </div>
        </div>
    </section>

    <section class="py-8 xs:py-10 sm:py-12 md:py-16 lg:py-20 xl:py-24 2xl:py-32 bg-[#ffffff]">
        <div class="container mx-auto px-3 xs:px-4 sm:px-6 md:px-8 lg:px-10 text-center">
            <h2 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-[#33595a] mb-6 xs:mb-8 sm:mb-10 md:mb-12 lg:mb-14 xl:mb-16 animate-fade-in-down">
                Hear How SIL Match Will Help You
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 xs:gap-8 sm:gap-10 md:gap-12">
                <div class="bg-[#f8f1e1] p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-xl xs:rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl
                                 transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-left">
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-xl text-[#3e4732] mb-4 xs:mb-5 sm:mb-6 md:mb-7 lg:mb-8 italic leading-relaxed font-serif">
                        "SIL Match was a game-changer for me. Finding suitable NDIS accommodation felt overwhelming, but their platform made it incredibly simple and reassuring. The support truly made a difference!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1976&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="Jane Doe Avatar" class="w-12 xs:w-14 sm:w-16 md:w-18 lg:w-20 xl:w-20 h-12 xs:h-14 sm:h-16 md:h-18 lg:h-20 xl:h-20 rounded-full mr-3 xs:mr-4 sm:mr-4 md:mr-5 object-cover border-2 xs:border-3 sm:border-4 border-[#cc8e45] shadow-lg">
                        <div>
                            <p class="font-extrabold text-[#33595a] text-base xs:text-lg sm:text-xl md:text-xl lg:text-2xl">Jane Doe</p>
                            <p class="text-xs xs:text-sm sm:text-sm md:text-base text-[#3e4732]">NDIS Participant</p>
                        </div>
                    </div>
                </div>
                <div class="bg-[#f8f1e1] p-4 xs:p-5 sm:p-6 md:p-7 lg:p-8 xl:p-10 rounded-xl xs:rounded-2xl shadow-xl flex flex-col items-center hover:shadow-2xl
                                 transition-all duration-500 transform hover:scale-[1.02] animate-fade-in-right delay-200">
                    <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-xl text-[#3e4732] mb-4 xs:mb-5 sm:mb-6 md:mb-7 lg:mb-8 italic leading-relaxed font-serif">
                        "As a support coordinator, I rely on SIL Match daily. It's an indispensable resource that significantly streamlines the process of connecting participants with the ideal housing and support they need. Highly efficient and reliable!"
                    </p>
                    <div class="flex items-center mt-auto">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                             alt="John Smith Avatar" class="w-12 xs:w-14 sm:w-16 md:w-18 lg:w-20 xl:w-20 h-12 xs:h-14 sm:h-16 md:h-18 lg:h-20 xl:h-20 rounded-full mr-3 xs:mr-4 sm:mr-4 md:mr-5 object-cover border-2 xs:border-3 sm:border-4 border-[#33595a] shadow-lg">
                        <div>
                            <p class="font-extrabold text-[#33595a] text-base xs:text-lg sm:text-xl md:text-xl lg:text-2xl">John Smith</p>
                            <p class="text-xs xs:text-sm sm:text-sm md:text-base text-[#3e4732]">Support Coordinator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#33595a] py-8 xs:py-10 sm:py-12 md:py-16 lg:py-20 xl:py-24 2xl:py-32 text-white text-center">
        <div class="container mx-auto px-3 xs:px-4 sm:px-6 md:px-8 lg:px-10">
            <h2 class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold mb-4 xs:mb-5 sm:mb-6 md:mb-7 lg:mb-8 drop-shadow-lg animate-fade-in-down">
                Ready to <span class="text-[#cc8e45]">Connect?</span>
            </h2>
            <p class="text-sm xs:text-base sm:text-lg md:text-xl lg:text-xl text-[#f8f1e1] mb-6 xs:mb-7 sm:mb-8 md:mb-9 lg:mb-10 xl:mb-12 max-w-4xl mx-auto drop-shadow-md leading-relaxed">
                Join SIL Match and become part of a growing community where participants, support coordinators, and providers can find the right people to create supportive, positive living arrangements. Whether you want to meet compatible housemates, help someone find the right match, or fill a vacancy, we make it simple and secure.
            </p>
            <a @click.prevent="showRegisterRoleModal = true"
               class="inline-block bg-[#cc8e45] text-white hover:bg-[#a67137] font-extrabold py-3 xs:py-3 sm:py-4 md:py-4 lg:py-5 xl:py-5 px-6 xs:px-7 sm:px-8 md:px-10 lg:px-12 xl:px-12 rounded-full text-sm xs:text-base sm:text-lg md:text-xl lg:text-xl xl:text-2xl shadow-2xl
                                 transition duration-400 ease-in-out transform hover:scale-105 hover:shadow-3xl
                                 focus:outline-none focus:ring-4 focus:ring-[#f8f1e1] w-full sm:w-auto">
                Start Matching Today!
            </a>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we should enable parallax (only on medium screens and up)
        const shouldEnableParallax = window.innerWidth >= 768; // md breakpoint
        
        if (shouldEnableParallax) {
            // Find all parallax layers
            const parallaxLayers = document.querySelectorAll('.parallax-layer');
            const heroSection = document.querySelector('section.relative.h-screen');

            if (parallaxLayers.length > 0 && heroSection) {
                window.addEventListener('scroll', function() {
                    const scrollTop = window.pageYOffset;
                    const heroSectionTop = heroSection.getBoundingClientRect().top + scrollTop;

                    parallaxLayers.forEach(layer => {
                        const speed = parseFloat(layer.dataset.parallaxSpeed);
                        const xSpeed = parseFloat(layer.dataset.parallaxXSpeed);

                        // Handle vertical movement for all layers with data-parallax-speed
                        const yPos = -((scrollTop - heroSectionTop) * speed);
                        layer.style.backgroundPositionY = yPos + 'px';

                        // Handle horizontal movement for layers with data-parallax-x-speed
                        if (!isNaN(xSpeed)) {
                            const xOffset = scrollTop * xSpeed;
                            layer.style.backgroundPositionX = `${xOffset}px`;
                        }
                    });
                });

                // Set initial background positions to prevent a jump on load
                parallaxLayers.forEach(layer => {
                    layer.style.backgroundPositionY = '0px';
                    layer.style.backgroundPositionX = '0px';
                });
            }
        }

        // Disable parallax on window resize if screen becomes too small
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (window.innerWidth < 768) {
                    // Remove parallax listeners and reset positions on small screens
                    const parallaxLayers = document.querySelectorAll('.parallax-layer');
                    parallaxLayers.forEach(layer => {
                        layer.style.backgroundPositionY = '0px';
                        layer.style.backgroundPositionX = '0px';
                    });
                }
            }, 250);
        });

        // Simple Scroll Reveal for sections (using Intersection Observer)
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1 // Trigger when 10% of the element is visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-fade-in-down, .animate-fade-in-left, .animate-fade-in-right, .animate-fade-in-up').forEach(element => {
            element.classList.add('opacity-0');
            observer.observe(element);
        });
    });
    </script>

    <style>
        .parallax-layer {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Extra small screens (xs breakpoint) */
        @media (max-width: 475px) {
            .text-xs { font-size: 0.75rem; }
            .text-2xl { font-size: 1.5rem; }
            .text-3xl { font-size: 1.875rem; }
            .text-4xl { font-size: 2.25rem; }
            .text-5xl { font-size: 3rem; }
            .text-6xl { font-size: 3.75rem; }
            .text-7xl { font-size: 4.5rem; }
            .text-8xl { font-size: 6rem; }
        }

        /* Very small screens (below 320px) */
        @media (max-width: 320px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .text-xs { font-size: 0.625rem; }
            .text-sm { font-size: 0.75rem; }
            .text-base { font-size: 0.875rem; }
            .text-lg { font-size: 1rem; }
            .text-xl { font-size: 1.125rem; }
            .text-2xl { font-size: 1.25rem; }
            .text-3xl { font-size: 1.5rem; }
            .text-4xl { font-size: 1.875rem; }
            .text-5xl { font-size: 2.25rem; }
            .text-6xl { font-size: 2.5rem; }
            .text-7xl { font-size: 3rem; }
            .text-8xl { font-size: 3.5rem; }
        }

        /* Tailwind Animations */
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

        /* Responsive improvements for mobile and tablet */
        @media (max-width: 768px) {
            /* Ensure text is readable on mobile */
            .drop-shadow-2xl {
                filter: drop-shadow(0 25px 25px rgb(0 0 0 / 0.8));
            }
            
            /* Better spacing on mobile */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Improve button touch targets */
            a, button {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Better spacing for small screens */
            .space-y-2 > * + * {
                margin-top: 0.5rem;
            }
            
            .space-y-4 > * + * {
                margin-top: 1rem;
            }
        }

        /* Performance optimization for mobile */
        @media (max-width: 767px) {
            .parallax-layer {
                transform: none !important;
                background-attachment: scroll !important;
            }
            
            /* Reduce animation complexity on mobile */
            .animate-fade-in-up,
            .animate-fade-in-down,
            .animate-fade-in-left,
            .animate-fade-in-right {
                animation-duration: 0.6s;
            }
        }

        /* Hero section specific fixes */
        .hero-section {
            position: relative;
            overflow: hidden;
        }

        /* Ensure text stays within hero bounds */
        @media (max-width: 640px) {
            .hero-section .container {
                max-width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Adjust text positioning for very small screens */
            .hero-section .transform {
                transform: translateY(-2rem) !important;
            }
        }

        /* Ensure proper text visibility on all screens */
        .hero-section h1,
        .hero-section h2 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            position: relative;
            z-index: 30;
        }

        /* Large screen optimizations */
        @media (min-width: 1920px) {
            .container {
                max-width: 1600px;
            }
            
            /* Ensure text doesn't get too large on very large screens */
            .text-8xl { font-size: 6rem; }
            .text-9xl { font-size: 7rem; }
            .text-11xl { font-size: 8rem; }
        }
    </style>

@endsection