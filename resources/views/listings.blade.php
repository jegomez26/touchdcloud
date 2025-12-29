@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 py-12" style="padding-top: 100px;">
        <h1 class="text-5xl font-extrabold text-center text-[#33595a] mb-6 animate-fade-in-down">Discover Your Next Home</h1>
        <p class="text-xl text-[#3e4732] text-center mb-12 max-w-3xl mx-auto animate-fade-in-down delay-200">
            Browse through our curated selection of NDIS-approved accommodations, designed with comfort and accessibility in mind.
        </p>

        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 animate-fade-in-up delay-300">
            <form method="GET" action="{{ route('listings') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-[#33595a] mb-2">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search by title, location..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                    </div>

                    {{-- Type Filter --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-[#33595a] mb-2">Accommodation Type</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                            <option value="">All Types</option>
                            @foreach($accommodationTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State Filter --}}
                    <div>
                        <label for="state" class="block text-sm font-medium text-[#33595a] mb-2">State</label>
                        <select name="state" id="state" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                            <option value="">All States</option>
                            @foreach($australianStates as $code => $name)
                                <option value="{{ $code }}" {{ request('state') == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bedrooms Filter --}}
                    <div>
                        <label for="bedrooms" class="block text-sm font-medium text-[#33595a] mb-2">Min Bedrooms</label>
                        <select name="bedrooms" id="bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                            <option value="">Any</option>
                            <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                            <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                            <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5+</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-center space-x-4">
                    <button type="submit" class="bg-[#cc8e45] text-white px-6 py-2 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="{{ route('listings') }}" class="bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-gray-600 transition duration-300 ease-in-out shadow-md">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </a>
                </div>
            </form>
        </div>

        {{-- Results Count --}}
        @if($accommodations->count() > 0)
            <div class="text-center mb-6">
                <p class="text-lg text-[#3e4732]">
                    Showing {{ $accommodations->firstItem() }} to {{ $accommodations->lastItem() }} of {{ $accommodations->total() }} accommodations
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-8">
            @forelse($accommodations as $index => $accommodation)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up {{ $index > 0 ? 'delay-' . ($index * 100) : '' }}">
                    {{-- Accommodation Image --}}
                    @if(!empty($accommodation->photos) && count($accommodation->photos) > 0)
                        <img src="{{ asset('storage/' . $accommodation->photos[0]) }}" 
                             alt="{{ $accommodation->title }}" 
                             class="w-full h-48 object-cover cursor-pointer"
                             data-accommodation-id="{{ $accommodation->id }}"
                             data-index="0"
                             data-action="open-modal">
                    @else
                        <img src="{{ asset('images/house-' . (($index % 8) + 1) . '.jpg') }}" 
                             alt="{{ $accommodation->title }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-[#33595a] mb-2">{{ $accommodation->title }}</h2>
                        <p class="text-md text-gray-500 mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>{{ $accommodation->suburb }}, {{ $accommodation->state }}
                        </p>
                        
                        {{-- Accommodation Type Badge --}}
                        <div class="flex items-center text-gray-700 mb-4 text-lg">
                            <span class="bg-[#e1e7dd] text-[#3e4732] px-3 py-1 rounded-full text-sm font-semibold mr-2">{{ $accommodation->type }}</span>
                        </div>
                        
                        {{-- Property Details --}}
                        <div class="flex justify-around items-center text-gray-700 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-bed text-[#cc8e45] mr-2 text-xl"></i>
                                <span class="text-lg">{{ $accommodation->num_bedrooms }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-shower text-[#cc8e45] mr-2 text-xl"></i>
                                <span class="text-lg">{{ $accommodation->num_bathrooms }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign text-[#cc8e45] mr-2 text-xl"></i>
                                <span class="text-lg">${{ number_format($accommodation->rent_per_week) }}</span>
                            </div>
                        </div>
                        
                        {{-- Availability Info --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-users mr-2"></i>
                                {{ $accommodation->current_occupancy }}/{{ $accommodation->total_vacancies }} occupied
                            </p>
                        </div>
                        
                        {{-- Enquire Button --}}
                        <a href="{{ route('accommodation.show', $accommodation) }}" 
                           class="block w-full text-center bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500 text-xl mb-4">
                        <i class="fas fa-home text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-[#33595a] mb-2">No Accommodations Found</h3>
                        <p class="text-lg">We couldn't find any accommodations matching your criteria.</p>
                        <p class="text-md mt-2">Try adjusting your search filters or check back later for new listings.</p>
                    </div>
                    <a href="{{ route('listings') }}" class="inline-block bg-[#cc8e45] text-white px-6 py-3 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">
                        View All Accommodations
                    </a>
                </div>
            @endforelse
        </div> {{-- End grid --}}

        {{-- Pagination --}}
        @if($accommodations->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $accommodations->links() }}
            </div>
        @endif

        <p class="text-xl text-[#3e4732] mt-16 text-center animate-fade-in-up delay-400">
            Can't find what you're looking for? <a href="#" class="text-[#cc8e45] font-bold hover:underline">Contact us</a> and we'll help you find your ideal match.
        </p>
    </div>

    {{-- Photo Modal --}}
    <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center">
        <div class="relative max-w-7xl max-h-full w-full h-full flex items-center justify-center p-4">
            {{-- Close Button --}}
            <button onclick="closePhotoModal()" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 text-4xl font-bold z-10">
                <i class="fas fa-times"></i>
            </button>
            
            {{-- Previous Button --}}
            <button onclick="previousPhoto()" 
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 text-4xl z-10"
                    id="prevBtn">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            {{-- Next Button --}}
            <button onclick="nextPhoto()" 
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 text-4xl z-10"
                    id="nextBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            {{-- Main Image --}}
            <img id="modalImage" 
                 src="" 
                 alt="" 
                 class="max-w-full max-h-full object-contain">
            
            {{-- Photo Counter --}}
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-lg">
                <span id="photoCounter">1 / 1</span>
            </div>
        </div>
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
        // Photo modal variables
        let currentPhotoIndex = 0;
        let photos = [];
        let modalOpen = false;

        // Photo modal functions
        function openPhotoModal(accommodationId, photoIndex) {
            // Get photos for this accommodation
            const accommodationImages = document.querySelectorAll(`[data-accommodation-id="${accommodationId}"]`);
            if (accommodationImages.length === 0) return;
            
            // For listings page, we only show the first photo in modal
            // Users can click "View Details" to see full gallery
            photos = [accommodationImages[0].src];
            currentPhotoIndex = 0;
            modalOpen = true;
            
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const photoCounter = document.getElementById('photoCounter');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            modalImage.src = photos[currentPhotoIndex];
            photoCounter.textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
            
            // Hide navigation buttons for single photo
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalOpen = false;
            document.body.style.overflow = 'auto';
        }

        function nextPhoto() {
            if (photos.length <= 1) return;
            currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
            updateModalImage();
        }

        function previousPhoto() {
            if (photos.length <= 1) return;
            currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
            updateModalImage();
        }

        function updateModalImage() {
            const modalImage = document.getElementById('modalImage');
            const photoCounter = document.getElementById('photoCounter');
            
            modalImage.src = photos[currentPhotoIndex];
            photoCounter.textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Modal open event listeners
            document.querySelectorAll('[data-action="open-modal"]').forEach(element => {
                element.addEventListener('click', function() {
                    const accommodationId = this.getAttribute('data-accommodation-id');
                    const index = parseInt(this.getAttribute('data-index'));
                    openPhotoModal(accommodationId, index);
                });
            });
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (!modalOpen) return;
                
                switch(e.key) {
                    case 'Escape':
                        closePhotoModal();
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        previousPhoto();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        nextPhoto();
                        break;
                }
            });

            // Click outside modal to close
            document.getElementById('photoModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closePhotoModal();
                }
            });

            // Animation observer
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

            document.querySelectorAll('.animate-fade-in-down, .animate-fade-in-up').forEach(element => {
                observer.observe(element);
            });
        });
    </script>
@endsection