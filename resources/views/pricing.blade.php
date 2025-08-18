@extends('layouts.app')

@section('content')

    <section class="py-20 sm:py-20 bg-[#e1e7dd] text-[#33595a]">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold mb-8 animate-fade-in-down">
                Finding Your Perfect Fit, Together
            </h2>
            <p class="text-xl text-[#3e4732] mb-16 max-w-3xl mx-auto animate-fade-in-down delay-200">
                We are here to help you navigate the NDIS journey by matching you with the right people to share a home with. Our platform is designed to connect participants with compatible housemates based on lifestyle, support needs, and shared goals. Whether you are a participant looking for a housemate, a support coordinator helping someone find the right match, or a provider connecting people, we have a plan for you.
            </p>


            {{-- Information for Participants and Support Coordinators --}}
            <div class="mb-16">
                <h3 class="text-3xl sm:text-4xl font-extrabold mb-6 text-[#33595a]">For Participants & Support Coordinators: Absolutely Free! 💖</h3>
                <p class="text-lg text-[#3e4732] mb-10 max-w-3xl mx-auto">
                    We believe everyone should have the chance to find the right people to live with, without financial barriers. That is why our platform is completely free for participants, carers and Support Coordinators. Create your profile, explore matches, connect, and start building the right living arrangement for you.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 text-left">
                    <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in-left">
                        <h4 class="text-2xl font-bold mb-4 text-[#cc8e45]">For Participants: Your Journey Starts Here! ✨</h4>
                    <p class="text-xl text-[#3e4732] mb-5 max-w-2xl mx-auto animate-fade-in-down delay-200">
                    Create your free profile, share your lifestyle, support needs, and what you are looking for in a housemate. Get matched with compatible people and start connecting.
                    </p>
                        <ul class="text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>100% Free.</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Unlimited Matches.</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Safe and Private Messaging.</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in-right">
                        <h4 class="text-2xl font-bold mb-4 text-[#cc8e45]">For Support Coordinators: Empowering Your Impact 🤝</h4>
                    <p class="text-xl text-[#3e4732] mb-5 max-w-2xl mx-auto animate-fade-in-down delay-200">
                    Help participants find the right people to live with quickly and easily. Create profiles on their behalf, view matches, and make introductions that lead to supportive living arrangements.
                    </p>
                        <ul class="text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>100% Free.</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Manage multiple participant profiles.</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#33595a] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Save time and reduce mismatches.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Provider Pricing Section --}}
            <h3 class="text-3xl sm:text-4xl font-extrabold mb-6 text-[#33595a] mt-20">For Providers: Build Happy Households 💼</h3>
            <p class="text-xl text-[#3e4732] mb-16 max-w-3xl mx-auto">
                List your current vacancies and connect with participants who will fit in well with your existing households. Use our matching tools to create stable, positive living arrangements.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16">

                {{-- Package 1: Starter Plan --}}
                <div class="bg-white rounded-3xl shadow-xl p-8 flex flex-col justify-between
                             transform hover:scale-[1.03] transition-all duration-500 animate-fade-in-left relative" style="margin-top: 120px; margin-bottom: 20px">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 1 --}}
                        <img src="images/Group01.png" class="absolute -top-5 left-1/2 -translate-x-1/2 -translate-y-1/2 w-60 h-50"> <h3 class="text-4xl font-extrabold text-[#33595a] mb-4" style="margin-top: -5px">Tier 1: Starter Plan</h3>
                        <p class="text-lg text-[#bcbabb] mb-6">Ideal for small providers or testing the waters</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <p class="text-5xl font-extrabold text-[#cc8e45] mb-4">$299<span class="text-xl text-[#3e4732]">/month</span></p>
                        <p class="text-lg text-[#3e4732] mb-6">or <span class="font-bold">$2,988/year</span> (2 months free)</p>
                        <ul class="text-left text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Up to 3 participant profiles</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Basic matching filters</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Email support</li>
                        </ul>
                    </div>
                    <a href="{{route('register.provider.create')}}" class="block w-full text-center bg-[#33595a] text-white font-bold py-4 px-6 rounded-full
                                         hover:bg-[#3e4732] transition duration-300 ease-in-out shadow-lg
                                         focus:outline-none focus:ring-4 focus:ring-[#bcbabb]">
                        Get Started
                    </a>
                </div>

                {{-- Package 2: Growth Plan --}}
                <div class="bg-[#33595a] rounded-3xl shadow-2xl p-8 flex flex-col justify-between border-8 border-[#cc8e45]
                             transform hover:scale-[1.05] transition-all duration-500 animate-fade-in-up delay-200 relative" style="margin-top: 80px; margin-bottom: 20px">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 2 --}}
                        <img src="images/Happy.png" class="absolute -top-12 left-1/2 -translate-x-1/2 -translate-y-1/2 w-60 h-50"> <h3 class="text-4xl font-extrabold text-[#ffffff] mb-4" style="margin-top: 25px">Tier 2: Growth Plan</h3>
                        <p class="text-lg text-[#f8f1e1] mb-6">For providers growing their participant base</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <p class="text-5xl font-extrabold text-[#cc8e45] mb-4">$599<span class="text-xl text-[#f8f1e1]">/month</span></p>
                        <p class="text-lg text-[#f8f1e1] mb-6">or <span class="font-bold">$5,988/year</span> (2 months free)</p>
                        <ul class="text-left text-lg text-[#f8f1e1] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>4–10 participant profiles</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Advanced matching filters</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Phone and email support</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Early access to feature updates</li>
                        </ul>
                    </div>
                    <a href="{{route('register.provider.create')}}" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-4 px-6 rounded-full
                                         hover:bg-[#a67137] transition duration-300 ease-in-out shadow-lg
                                         focus:outline-none focus:ring-4 focus:ring-[#f8f1e1]">
                        Get Started
                    </a>
                </div>

                {{-- Package 3: Premium Plan --}}
                <div class="bg-white rounded-3xl shadow-xl p-8 flex flex-col justify-between
                             transform hover:scale-[1.03] transition-all duration-500 animate-fade-in-right relative" style="margin-top: 120px; margin-bottom: 20px">
                    <div class="mb-8">
                        {{-- Placeholder for Illustration 3 --}}
                        <img src="images/Manny.png" class="absolute -top-15px left-1/2 -translate-x-1/2 -translate-y-1/2 w-60 h-50" style="margin-top: -120px"> <h3 class="text-4xl font-extrabold text-[#33595a] mb-4" style="margin-top: -5px">Tier 3: Premium Plan</h3>
                        <p class="text-lg text-[#bcbabb] mb-6">For large providers needing scale and flexibility</p>
                        <hr class="border-t-2 border-[#f8f1e1] my-6">
                        <p class="text-5xl font-extrabold text-[#cc8e45] mb-4">$799<span class="text-xl text-[#3e4732]">/month</span></p>
                        <p class="text-lg text-[#3e4732] mb-6">or <span class="font-bold">$7,990/year</span> (2 months free)</p>
                        <ul class="text-left text-lg text-[#3e4732] space-y-3">
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>11+ participant profiles</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Unlimited matches and updates</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Dedicated support</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Custom onboarding & feature requests</li>
                            <li class="flex items-center"><svg class="w-6 h-6 mr-3 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Property listings included</li>
                        </ul>
                    </div>
                    <a href="{{route('register.provider.create')}}" class="block w-full text-center bg-[#33595a] text-white font-bold py-4 px-6 rounded-full
                                         hover:bg-[#3e4732] transition duration-300 ease-in-out shadow-lg
                                         focus:outline-none focus:ring-4 focus:ring-[#bcbabb]">
                        Get Started
                    </a>
                </div>

            </div> {{-- End grid --}}

            <div class="mt-16 text-left max-w-3xl mx-auto">
                <h3 class="text-2xl font-bold mb-4 text-[#33595a]">Enhance Your Plan with Add-ons:</h3>
                <ul class="text-lg text-[#3e4732] space-y-2">
                    <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><strong class="font-bold">Featured placement:</strong> Boost your visibility for $99/month.</li>
                    <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-[#cc8e45] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><strong class="font-bold">Profile writing/editing:</strong> Get expert help with your profile for just $50/profile.</li>
                </ul>

                <p class="text-xl text-[#3e4732] mt-8 animate-fade-in-up delay-400">
                    Curious to try us out? Enjoy a <strong class="font-bold">14-day free trial</strong> on our Growth and Premium plans.
                </p>
                <p class="text-xl text-[#3e4732] mt-4 animate-fade-in-up delay-400">
                    <strong class="font-bold">Special Founding Partner Offer:</strong> Be among our first 10 providers and get the Growth plan at an incredible <strong class="text-[#cc8e45]">$399/month</strong> for 12 months! Don't miss this opportunity!
                </p>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const parallaxLayers = document.querySelectorAll('.parallax-layer');
            const heroSection = document.querySelector('section.relative.h-screen');

            // Define the breakpoint for disabling parallax (e.g., 768px for Tailwind's 'md')
            const disableParallaxBreakpoint = 768;

            if (parallaxLayers.length > 0 && heroSection) {
                // Function to update parallax on scroll
                const updateParallax = () => {
                    // Only apply parallax if the screen width is greater than or equal to the breakpoint
                    if (window.innerWidth >= disableParallaxBreakpoint) {
                        const scrollTop = window.pageYOffset;
                        const heroSectionTop = heroSection.getBoundingClientRect().top + scrollTop;

                        parallaxLayers.forEach(layer => {
                            const speed = parseFloat(layer.dataset.parallaxSpeed);
                            const yPos = -((scrollTop - heroSectionTop) * speed);
                            layer.style.backgroundPositionY = yPos + 'px';
                        });
                    } else {
                        // If parallax is disabled, reset background-position-y for static effect
                        parallaxLayers.forEach(layer => {
                            layer.style.backgroundPositionY = 'center'; // Or '0px', depending on desired initial position
                        });
                    }
                };

                // Function to set initial background sizes based on screen width
                const setInitialBackgroundSize = () => {
                    parallaxLayers.forEach(layer => {
                        // On larger screens or when parallax is active, use 'cover' or '100vw auto'
                        if (window.innerWidth >= disableParallaxBreakpoint) {
                            layer.style.backgroundSize = 'cover';
                        } else {
                            // On smaller screens where parallax is disabled, ensure it still covers the section
                            layer.style.backgroundSize = 'cover'; // Keep 'cover' to fill the section
                        }
                    });
                };

                // Initial setup
                setInitialBackgroundSize();
                updateParallax(); // Apply initial parallax or static position

                // Event listeners
                window.addEventListener('scroll', updateParallax); // Always listen, but function checks breakpoint
                window.addEventListener('resize', () => {
                    setInitialBackgroundSize();
                    updateParallax(); // Recalculate parallax/static on resize
                });
            }

            // Simple Scroll Reveal (rest of your existing script)
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
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
            background-repeat: no-repeat;
            background-position: center; /* Ensures it's centered initially */
            background-attachment: scroll; /* Default to scroll for smaller screens, overridden for larger */
            transition: background-size 0.3s ease-out; /* Smooth transition for background-size changes */
            height: 100%;
            width: 100%;
        }

        /* Styles for larger screens (where parallax is active) */
        @media (min-width: 768px) { /* Apply these styles for medium screens and up */
            .parallax-layer {
                background-size: cover; /* Ensure it covers on larger screens */
                background-attachment: fixed; /* This is key for the parallax feel */
            }
        }

        /* Styles for smaller screens (where parallax is disabled) */
        @media (max-width: 767px) {
            .parallax-layer {
                background-size: cover; /* Keep 'cover' to fill the section height on small screens */
                background-attachment: scroll; /* Ensure it scrolls normally with the page */
                background-position: center; /* Center the image without parallax movement */
            }
        }

        /* Tailwind Animations (rest of your existing styles) */
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