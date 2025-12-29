@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 py-12" style="padding-top: 100px;">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 animate-fade-in-down">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 animate-fade-in-down">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 animate-fade-in-down">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Breadcrumb --}}
        <nav class="mb-6 animate-fade-in-down">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-[#cc8e45] transition duration-300">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('listings') }}" class="hover:text-[#cc8e45] transition duration-300">Listings</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-[#33595a] font-medium">{{ $accommodation->title }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2">
                {{-- Image Gallery --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fade-in-up">
                    @if(!empty($accommodation->photos) && count($accommodation->photos) > 0)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $accommodation->photos[0]) }}" 
                                 alt="{{ $accommodation->title }}" 
                                 class="w-full h-96 object-cover cursor-pointer" 
                                 id="main-image"
                                 data-index="0"
                                 data-action="open-modal">
                            
                            {{-- Arrow Navigation --}}
                            @if(count($accommodation->photos) > 1)
                                <button id="prevImageBtn" 
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white hover:bg-opacity-75 rounded-full p-2 transition duration-300 opacity-0 hover:opacity-100 group-hover:opacity-100">
                                    <i class="fas fa-chevron-left text-xl"></i>
                                </button>
                                
                                <button id="nextImageBtn" 
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white hover:bg-opacity-75 rounded-full p-2 transition duration-300 opacity-0 hover:opacity-100 group-hover:opacity-100">
                                    <i class="fas fa-chevron-right text-xl"></i>
                                </button>
                            @endif
                            
                            {{-- Image Navigation Dots --}}
                            @if(count($accommodation->photos) > 1)
                                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                    @foreach($accommodation->photos as $index => $photo)
                                        <button data-image="{{ asset('storage/' . $photo) }}" 
                                                data-index="{{ $index }}"
                                                class="progress-dot w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white bg-opacity-50' }} hover:bg-white transition duration-300"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        {{-- Thumbnail Gallery --}}
                        @if(count($accommodation->photos) > 1)
                            <div class="p-4 grid grid-cols-4 gap-2">
                                @foreach($accommodation->photos as $index => $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         alt="{{ $accommodation->title }} - Image {{ $index + 1 }}" 
                                         class="thumbnail-img w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition duration-300 {{ $index === 0 ? 'ring-2 ring-[#cc8e45]' : '' }}"
                                         data-image="{{ asset('storage/' . $photo) }}"
                                         data-index="{{ $index }}"
                                         data-action="open-modal">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <img src="{{ asset('images/house-1.jpg') }}" 
                             alt="{{ $accommodation->title }}" 
                             class="w-full h-96 object-cover">
                    @endif
                </div>

                {{-- Property Details --}}
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 animate-fade-in-up delay-100">
                    <h1 class="text-4xl font-extrabold text-[#33595a] mb-4">{{ $accommodation->title }}</h1>
                    
                    {{-- Location --}}
                    <div class="flex items-center text-gray-600 mb-6">
                        <i class="fas fa-map-marker-alt mr-2 text-[#cc8e45]"></i>
                        <span class="text-lg">{{ $accommodation->address }}, {{ $accommodation->suburb }}, {{ $accommodation->state }} {{ $accommodation->post_code }}</span>
                    </div>

                    {{-- Property Stats --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="text-center">
                            <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-bed text-[#cc8e45] text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">Bedrooms</p>
                            <p class="text-2xl font-bold text-[#33595a]">{{ $accommodation->num_bedrooms }}</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-shower text-[#cc8e45] text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">Bathrooms</p>
                            <p class="text-2xl font-bold text-[#33595a]">{{ $accommodation->num_bathrooms }}</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-dollar-sign text-[#cc8e45] text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">Rent per Week</p>
                            <p class="text-2xl font-bold text-[#33595a]">${{ number_format($accommodation->rent_per_week) }}</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-users text-[#cc8e45] text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">Occupancy</p>
                            <p class="text-2xl font-bold text-[#33595a]">{{ $accommodation->current_occupancy }}/{{ $accommodation->total_vacancies }}</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#33595a] mb-4">Description</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $accommodation->description }}</p>
                    </div>

                    {{-- Accommodation Type --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#33595a] mb-4">Accommodation Type</h2>
                        <span class="bg-[#e1e7dd] text-[#3e4732] px-4 py-2 rounded-full text-lg font-semibold">{{ $accommodation->type }}</span>
                    </div>

                    {{-- Amenities --}}
                    @if(!empty($accommodation->amenities) && count($accommodation->amenities) > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-[#33595a] mb-4">Amenities & Features</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($accommodation->amenities as $amenity)
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-[#cc8e45] mr-3"></i>
                                        <span class="text-gray-700">{{ $amenity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Provider Information --}}
                <div class="bg-white rounded-xl shadow-lg p-8 animate-fade-in-up delay-200">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-4">Provider Information</h2>
                    <div class="flex items-center">
                        <div class="bg-[#e1e7dd] rounded-full w-16 h-16 flex items-center justify-center mr-4">
                            <i class="fas fa-building text-[#cc8e45] text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#33595a]">{{ $accommodation->provider->business_name ?? 'Provider' }}</h3>
                            <p class="text-gray-600">{{ $accommodation->provider->contact_email ?? 'Contact information available upon request' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Contact Form --}}
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8 sticky top-24 animate-fade-in-up delay-300">
                    <h2 class="text-2xl font-bold text-[#33595a] mb-6">Enquire About This Property</h2>
                    
                    <form action="{{ route('enquiries.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $accommodation->id }}">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#33595a] mb-2">Your Name</label>
                            <input type="text" name="name" id="name" required 
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#33595a] mb-2">Email Address</label>
                            <input type="email" name="email" id="email" required 
                                   value="{{ old('email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#33595a] mb-2">Phone Number (Optional)</label>
                            <input type="tel" name="phone" id="phone" 
                                   value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-[#33595a] mb-2">Message</label>
                            <textarea name="message" id="message" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent @error('message') border-red-500 @enderror"
                                      placeholder="Tell us about your accommodation needs...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out shadow-md">
                            <i class="fas fa-paper-plane mr-2"></i>Send Enquiry
                        </button>
                    </form>
                </div>

                {{-- Quick Info --}}
                <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in-up delay-400">
                    <h3 class="text-xl font-bold text-[#33595a] mb-4">Quick Info</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Property Type:</span>
                            <span class="font-medium text-[#33595a]">{{ $accommodation->type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Available:</span>
                            <span class="font-medium text-green-600">{{ $accommodation->total_vacancies - $accommodation->current_occupancy }} spots</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rent:</span>
                            <span class="font-medium text-[#33595a]">${{ number_format($accommodation->rent_per_week) }}/week</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Location:</span>
                            <span class="font-medium text-[#33595a]">{{ $accommodation->suburb }}, {{ $accommodation->state }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Accommodations --}}
        @if($relatedAccommodations->count() > 0)
            <div class="mt-16 animate-fade-in-up delay-500">
                <h2 class="text-3xl font-bold text-[#33595a] text-center mb-8">Similar Accommodations</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedAccommodations as $related)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                            @if(!empty($related->photos) && count($related->photos) > 0)
                                <img src="{{ asset('storage/' . $related->photos[0]) }}" 
                                     alt="{{ $related->title }}" 
                                     class="w-full h-48 object-cover">
                            @else
                                <img src="{{ asset('images/house-1.jpg') }}" 
                                     alt="{{ $related->title }}" 
                                     class="w-full h-48 object-cover">
                            @endif
                            
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-[#33595a] mb-2">{{ $related->title }}</h3>
                                <p class="text-sm text-gray-500 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $related->suburb }}, {{ $related->state }}
                                </p>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="bg-[#e1e7dd] text-[#3e4732] px-2 py-1 rounded-full text-xs font-semibold">{{ $related->type }}</span>
                                    <span class="text-lg font-bold text-[#cc8e45]">${{ number_format($related->rent_per_week) }}</span>
                                </div>
                                <a href="{{ route('accommodation.show', $related) }}" 
                                   class="block w-full text-center bg-[#cc8e45] text-white font-bold py-2 px-4 rounded-full hover:bg-[#a67137] transition duration-300 ease-in-out text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
        .delay-500 { animation-delay: 0.5s; }

        /* General animation setup for elements that will be observed */
        .animate-fade-in-down, .animate-fade-in-up {
            opacity: 0; /* Start hidden */
        }

        /* Arrow button styling */
        #prevImageBtn, #nextImageBtn {
            transition: all 0.3s ease;
        }

        #prevImageBtn:hover, #nextImageBtn:hover {
            transform: translateY(-50%) scale(1.1);
        }

        /* Progress dots styling */
        .progress-dot {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .progress-dot:hover {
            transform: scale(1.2);
        }
    </style>

    <script>
        // Photo modal variables
        let currentPhotoIndex = 0;
        let photos = [];
        let modalOpen = false;
        let currentImageIndex = 0; // For main image navigation

        // Initialize photos array
        function initializePhotos() {
            photos = [];
            document.querySelectorAll('.thumbnail-img').forEach(img => {
                photos.push(img.getAttribute('data-image'));
            });
        }

        // Main image navigation functions
        function showPreviousImage() {
            if (photos.length <= 1) return;
            currentImageIndex = (currentImageIndex - 1 + photos.length) % photos.length;
            updateMainImage();
        }

        function showNextImage() {
            if (photos.length <= 1) return;
            currentImageIndex = (currentImageIndex + 1) % photos.length;
            updateMainImage();
        }

        function updateMainImage() {
            const mainImage = document.getElementById('main-image');
            const thumbnailImages = document.querySelectorAll('.thumbnail-img');
            
            if (photos[currentImageIndex]) {
                mainImage.src = photos[currentImageIndex];
                mainImage.setAttribute('data-index', currentImageIndex);
                
                // Update thumbnail selection
                thumbnailImages.forEach((img, index) => {
                    img.classList.remove('ring-2', 'ring-[#cc8e45]');
                    if (index === currentImageIndex) {
                        img.classList.add('ring-2', 'ring-[#cc8e45]');
                    }
                });
                
                // Update progress dots
                updateProgressDots();
            }
        }

        function updateProgressDots() {
            document.querySelectorAll('.progress-dot').forEach((dot, index) => {
                dot.classList.remove('bg-white');
                dot.classList.add('bg-white', 'bg-opacity-50');
                
                if (index === currentImageIndex) {
                    dot.classList.remove('bg-opacity-50');
                }
            });
        }

        function goToImage(index) {
            if (index >= 0 && index < photos.length) {
                currentImageIndex = index;
                updateMainImage();
            }
        }

        // Image gallery functionality
        function changeImage(src, thumbnail = null) {
            document.getElementById('main-image').src = src;
            
            // Update thumbnail selection
            if (thumbnail) {
                document.querySelectorAll('.thumbnail-img').forEach(img => {
                    img.classList.remove('ring-2', 'ring-[#cc8e45]');
                });
                thumbnail.classList.add('ring-2', 'ring-[#cc8e45]');
            }
        }

        // Photo modal functions
        function openPhotoModal(index) {
            if (photos.length === 0) return;
            
            currentPhotoIndex = index;
            modalOpen = true;
            
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const photoCounter = document.getElementById('photoCounter');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            modalImage.src = photos[currentPhotoIndex];
            photoCounter.textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
            
            // Show/hide navigation buttons
            prevBtn.style.display = photos.length > 1 ? 'block' : 'none';
            nextBtn.style.display = photos.length > 1 ? 'block' : 'none';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalOpen = false;
            document.body.style.overflow = 'auto'; // Restore scrolling
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

        // Animation observer
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize photos array
            initializePhotos();
            
            // Arrow navigation event listeners
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', showPreviousImage);
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', showNextImage);
            }
            
            // Progress dots event listeners
            document.querySelectorAll('.progress-dot').forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    goToImage(index);
                });
            });

            // Thumbnail gallery event listeners
            document.querySelectorAll('.thumbnail-img').forEach((img, index) => {
                img.addEventListener('click', function() {
                    goToImage(index);
                });
            });

            // Modal open event listeners
            document.querySelectorAll('[data-action="open-modal"]').forEach(element => {
                element.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    openPhotoModal(index);
                });
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (modalOpen) {
                    // Modal keyboard navigation
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
                } else {
                    // Main image keyboard navigation
                    switch(e.key) {
                        case 'ArrowLeft':
                            e.preventDefault();
                            showPreviousImage();
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            showNextImage();
                            break;
                    }
                }
            });

            // Show/hide arrow buttons on hover
            const imageContainer = document.querySelector('.relative');
            if (imageContainer) {
                imageContainer.addEventListener('mouseenter', function() {
                    if (prevBtn) prevBtn.style.opacity = '1';
                    if (nextBtn) nextBtn.style.opacity = '1';
                });
                
                imageContainer.addEventListener('mouseleave', function() {
                    if (prevBtn) prevBtn.style.opacity = '0';
                    if (nextBtn) nextBtn.style.opacity = '0';
                });
            }

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
