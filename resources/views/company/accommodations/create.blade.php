@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">Add New Accommodation</h1>
            <a href="{{ route('provider.accommodations.index') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left mr-2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6" x-data="accommodationForm()">
            <form action="{{ route('provider.accommodations.store') }}" method="POST" enctype="multipart/form-data" id="accommodationForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Accommodation Title</label>
                        <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('title') }}" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Accommodation Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="">Select Type</option>
                            <option value="Supported Independent Living" {{ old('type') == 'Supported Independent Living' ? 'selected' : '' }}>Supported Independent Living</option>
                            <optgroup label="Specialist Disability Accommodation (SDA)">
                                <option value="Improved Livability" {{ old('type') == 'Improved Livability' ? 'selected' : '' }}>Improved Livability</option>
                                <option value="Fully Accessible" {{ old('type') == 'Fully Accessible' ? 'selected' : '' }}>Fully Accessible</option>
                                <option value="High Physical Support" {{ old('type') == 'High Physical Support' ? 'selected' : '' }}>High Physical Support</option>
                                <option value="Robust" {{ old('type') == 'Robust' ? 'selected' : '' }}>Robust</option>
                            </optgroup>
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('address') }}" required>
                        @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state" id="state" @change="fetchSuburbs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="">Select State</option>
                            @foreach($australianStates as $code => $name)
                                <option value="{{ $code }}" {{ old('state') == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                        <input type="text" name="suburb" id="suburb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('suburb') }}" required>
                        @error('suburb')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="post_code" class="block text-sm font-medium text-gray-700">Post Code</label>
                        <input type="text" name="post_code" id="post_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('post_code') }}" required>
                        @error('post_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="num_bedrooms" class="block text-sm font-medium text-gray-700">Number of Bedrooms</label>
                        <input type="number" name="num_bedrooms" id="num_bedrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('num_bedrooms') }}" min="1" required>
                        @error('num_bedrooms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="num_bathrooms" class="block text-sm font-medium text-gray-700">Number of Bathrooms</label>
                        <input type="number" name="num_bathrooms" id="num_bathrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('num_bathrooms') }}" min="1" required>
                        @error('num_bathrooms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="rent_per_week" class="block text-sm font-medium text-gray-700">Rent Per Week (AUD)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-base">$</span>
                            </div>
                            <input type="number" step="0.01" name="rent_per_week" id="rent_per_week" class="block w-full rounded-md border-gray-300 pl-7 pr-2.5 py-2.5 focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base" value="{{ old('rent_per_week') }}" min="0" required>
                        </div>
                        @error('rent_per_week')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="total_vacancies" class="block text-sm font-medium text-gray-700">Total Vacancies</label>
                        <input type="number" name="total_vacancies" id="total_vacancies" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('total_vacancies') }}" min="0" required>
                        @error('total_vacancies')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="current_occupancy" class="block text-sm font-medium text-gray-700">Current Occupancy</label>
                        <input type="number" name="current_occupancy" id="current_occupancy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('current_occupancy', 0) }}" min="0" required>
                        @error('current_occupancy')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Amenities</label>
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($amenitiesOptions as $amenity)
                                <div class="flex items-center">
                                    <input type="checkbox" name="amenities[]" id="amenity_{{ Str::slug($amenity) }}" value="{{ $amenity }}" class="focus:ring-[#cc8e45] h-4 w-4 text-[#33595a] border-gray-300 rounded" {{ in_array($amenity, old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="amenity_{{ Str::slug($amenity) }}" class="ml-2 block text-sm text-gray-900">{{ $amenity }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('amenities')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2 flex items-center">
                        <input type="hidden" name="is_available_for_hm" value="0"> {{-- Hidden field for unchecked checkbox --}}
                        <input type="checkbox" name="is_available_for_hm" id="is_available_for_hm" value="1" class="focus:ring-[#cc8e45] h-4 w-4 text-[#33595a] border-gray-300 rounded" {{ old('is_available_for_hm') ? 'checked' : '' }}>
                        <label for="is_available_for_hm" class="ml-2 block text-sm text-gray-900">Available for Home Modifications (HM)</label>
                        @error('is_available_for_hm')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Photo Upload Section --}}
                    <div class="md:col-span-2">
                        <label for="photos" class="block text-sm font-medium text-gray-700">Photos (Max 10, 1MB each)</label>
                        <input type="file" name="photos[]" id="photos" multiple
                               @change="handlePhotoUpload($event)"
                               accept="image/jpeg,image/png,image/gif"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7ed] file:text-[#33595a] hover:file:bg-[#e1e7dd] cursor-pointer">
                        @error('photos')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @error('photos.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div x-show="photoErrors.length > 0" class="mt-2">
                            <template x-for="error in photoErrors" :key="error">
                                <p class="text-red-500 text-xs mt-1" x-text="error"></p>
                            </template>
                        </div>

                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" x-show="uploadedPhotos.length > 0">
                            <template x-for="(photo, index) in uploadedPhotos" :key="index">
                                <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img :src="photo.preview" alt="Photo preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removePhoto(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-80 hover:opacity-100 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" id="submitBtn" class="bg-[#33595a] text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-[#2c494a] transition duration-300 shadow-md">
                        <span id="submitText">Create Accommodation</span>
                        <span id="submitSpinner" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Success!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="successMessage">Accommodation created successfully!</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeSuccessModal()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Modal --}}
    <div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Error</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="errorMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeErrorModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('accommodationForm', () => ({
                uploadedPhotos: [], // Stores { file: File, preview: URL }
                photoErrors: [],
                maxPhotos: 10,
                maxPhotoSizeKB: 1024, // 1MB

                init() {
                    // No longer need to fetch suburbs since we're using text input
                },

                handlePhotoUpload(event) {
                    this.photoErrors = [];
                    const files = Array.from(event.target.files);

                    if (files.length > this.maxPhotos) {
                        this.photoErrors.push(`You can upload a maximum of ${this.maxPhotos} photos.`);
                        event.target.value = ''; // Clear input
                        return;
                    }

                    // Clear previous photos
                    this.uploadedPhotos = [];
                    
                    files.forEach(file => {
                        if (file.size > this.maxPhotoSizeKB * 1024) { // Convert KB to bytes
                            this.photoErrors.push(`Photo "${file.name}" exceeds the ${this.maxPhotoSizeKB / 1024}MB limit.`);
                        } else if (!file.type.match('image/jpeg|image/png|image/gif')) {
                             this.photoErrors.push(`File "${file.name}" is not a valid image type (JPEG, PNG, GIF).`);
                        } else {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.uploadedPhotos.push({ file: file, preview: e.target.result });
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                },

                removePhoto(index) {
                    this.uploadedPhotos.splice(index, 1);
                    
                    // Update the file input to reflect the removed photo
                    const input = document.getElementById('photos');
                    const dataTransfer = new DataTransfer();
                    this.uploadedPhotos.forEach(item => dataTransfer.items.add(item.file));
                    input.files = dataTransfer.files;
                }
            }));
        });

        // Modal functions
        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // AJAX form submission
        document.getElementById('accommodationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitSpinner.classList.remove('hidden');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal(data.message || 'Accommodation created successfully!');
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '/provider/accommodations';
                    }, 1500);
                } else {
                    showErrorModal(data.message || 'An error occurred while creating the accommodation.');
                }
            })
            .catch(error => {
                showErrorModal('An error occurred while creating the accommodation.');
                console.error('Error:', error);
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitSpinner.classList.add('hidden');
            });
        });

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'successModal') {
                closeSuccessModal();
            }
            if (e.target.id === 'errorModal') {
                closeErrorModal();
            }
        });
    </script>
@endsection