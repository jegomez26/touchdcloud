@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 py-12" style="padding-top: 100px;">
        <h1 class="text-5xl font-extrabold text-center text-[#33595a] mb-6 animate-fade-in-down">Discover Your Next Home</h1>
        <p class="text-xl text-[#3e4732] text-center mb-12 max-w-3xl mx-auto animate-fade-in-down delay-200">
            Browse through our curated selection of NDIS-approved accommodations, designed with comfort and accessibility in mind.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-8">

            {{-- Listing 1 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up">
                <img src="images/house-1.jpg" alt="Accommodation Image 1" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Modern Home Oasis</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Richmond, Victoria</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">High Physical Support</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">3</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 2 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-100">
                <img src="images/house-2.jpg" alt="Accommodation Image 2" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Serene Suburban Dwelling</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Toowoomba, Queensland</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Improved Liveability</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">4</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">3</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 3 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-200">
                <img src="images/house-3.jpg" alt="Accommodation Image 3" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">City View Apartment</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Parramatta, New South Wales</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Fully Accessible</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 4 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-300">
                <img src="images/house-4.jpg" alt="Accommodation Image 4" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Coastal Retreat Villa</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Fremantle, Western Australia</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Robust</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">3</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 5 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up">
                <img src="images/house-5.jpg" alt="Accommodation Image 5" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Spacious Family Home</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Adelaide, South Australia</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Improved Liveability</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">5</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">3</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 6 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-100">
                <img src="images/house-6.jpg" alt="Accommodation Image 6" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Quiet Countryside Cottage</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Launceston, Tasmania</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Basic Accessible</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 7 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-200">
                <img src="images/house-7.jpg" alt="Accommodation Image 7" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Urban Loft Living</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Brisbane, Queensland</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">High Physical Support</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">1</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">0</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

            {{-- Listing 8 --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-300">
                <img src="images/house-8.jpg" alt="Accommodation Image 8" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-2">Peaceful Garden Home</h2>
                    <p class="text-md text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Canberra, ACT</p>
                    <div class="flex items-center text-gray-700 mb-4 text-lg">
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">Robust</span>
                    </div>
                    <div class="flex justify-around items-center text-gray-700 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">3</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-[#cc8e45] mr-2 text-xl"></i>
                            <span class="text-lg">2</span>
                        </div>
                    </div>
                    <a href="#" class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">Enquire Now</a>
                </div>
            </div>

        </div> {{-- End grid --}}

        <p class="text-xl text-[#3e4732] mt-16 text-center animate-fade-in-up delay-400">
            Can't find what you're looking for? <a href="#" class="text-[#cc8e45] font-bold hover:underline">Contact us</a> and we'll help you find your ideal match.
        </p>
    </div>

    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Tailwind Animations (rest of your existing styles, ensure these are present or link your main CSS) */
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

        .animate-fade-in-down.animate-active {
            animation: fadeInDown 1s ease-out forwards;
        }
        .animate-fade-in-up.animate-active {
            animation: fadeInUp 1s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* General animation setup for elements that will be observed */
        .animate-fade-in-down, .animate-fade-in-up {
            opacity: 0; /* Start hidden */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1 // Trigger when 10% of the element is visible
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-active');
                        observer.unobserve(entry.target); // Stop observing once animated
                    }
                });
            }, observerOptions);

            // Observe elements with animation classes
            document.querySelectorAll('.animate-fade-in-down, .animate-fade-in-up').forEach(element => {
                observer.observe(element);
            });
        });
    </script>
@endsection