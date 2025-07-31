@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">Edit Accommodation: {{ $accommodation->title }}</h1>
            <a href="{{ route('provider.accommodations.list') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left mr-2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6" x-data="accommodationForm()">
            <form action="{{ route('provider.accommodations.update', $accommodation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Accommodation Title</label>
                        <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('title', $accommodation->title) }}" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Accommodation Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="">Select Type</option>
                            <option value="Supported Independent Living" {{ old('type', $accommodation->type) == 'Supported Independent Living' ? 'selected' : '' }}>Supported Independent Living</option>
                            <optgroup label="Specialist Disability Accommodation (SDA)">
                                <option value="Improved Livability" {{ old('type', $accommodation->type) == 'Improved Livability' ? 'selected' : '' }}>Improved Livability</option>
                                <option value="Fully Accessible" {{ old('type', $accommodation->type) == 'Fully Accessible' ? 'selected' : '' }}>Fully Accessible</option>
                                <option value="High Physical Support" {{ old('type', $accommodation->type) == 'High Physical Support' ? 'selected' : '' }}>High Physical Support</option>
                                <option value="Robust" {{ old('type', $accommodation->type) == 'Robust' ? 'selected' : '' }}>Robust</option>
                            </optgroup>
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>{{ old('description', $accommodation->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('address', $accommodation->address) }}" required>
                        @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state" id="state" @change="fetchSuburbs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="">Select State</option>
                            @foreach($australianStates as $code => $name)
                                <option value="{{ $code }}" {{ old('state', $accommodation->state) == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="suburb" class="block text-sm font-medium text-gray-700">Suburb</label>
                        <select name="suburb" id="suburb" x-model="selectedSuburb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required :disabled="suburbs.length === 0">
                            <option value="">Select Suburb</option>
                            <template x-for="suburb in suburbs" :key="suburb">
                                <option :value="suburb" x-text="suburb"></option>
                            </template>
                        </select>
                        @error('suburb')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="post_code" class="block text-sm font-medium text-gray-700">Post Code</label>
                        <input type="text" name="post_code" id="post_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('post_code', $accommodation->post_code) }}" required>
                        @error('post_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="num_bedrooms" class="block text-sm font-medium text-gray-700">Number of Bedrooms</label>
                        <input type="number" name="num_bedrooms" id="num_bedrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('num_bedrooms', $accommodation->num_bedrooms) }}" min="1" required>
                        @error('num_bedrooms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="num_bathrooms" class="block text-sm font-medium text-gray-700">Number of Bathrooms</label>
                        <input type="number" name="num_bathrooms" id="num_bathrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('num_bathrooms', $accommodation->num_bathrooms) }}" min="1" required>
                        @error('num_bathrooms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="rent_per_week" class="block text-sm font-medium text-gray-700">Rent Per Week (AUD)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-base">$</span>
                            </div>
                            <input type="number" step="0.01" name="rent_per_week" id="rent_per_week" class="block w-full rounded-md border-gray-300 pl-7 pr-2.5 py-2.5 focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base" value="{{ old('rent_per_week', $accommodation->rent_per_week) }}" min="0" required>
                        </div>
                        @error('rent_per_week')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" required>
                            <option value="draft" {{ old('status', $accommodation->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="available" {{ old('status', $accommodation->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('status', $accommodation->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="archived" {{ old('status', $accommodation->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="total_vacancies" class="block text-sm font-medium text-gray-700">Total Vacancies</label>
                        <input type="number" name="total_vacancies" id="total_vacancies" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('total_vacancies', $accommodation->total_vacancies) }}" min="0" required>
                        @error('total_vacancies')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="current_occupancy" class="block text-sm font-medium text-gray-700">Current Occupancy</label>
                        <input type="number" name="current_occupancy" id="current_occupancy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('current_occupancy', $accommodation->current_occupancy) }}" min="0" required>
                        @error('current_occupancy')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Amenities</label>
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($amenitiesOptions as $amenity)
                                <div class="flex items-center">
                                    <input type="checkbox" name="amenities[]" id="amenity_{{ Str::slug($amenity) }}" value="{{ $amenity }}" class="focus:ring-[#cc8e45] h-4 w-4 text-[#33595a] border-gray-300 rounded" {{ in_array($amenity, old('amenities', $accommodation->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="amenity_{{ Str::slug($amenity) }}" class="ml-2 block text-sm text-gray-900">{{ $amenity }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('amenities')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2 flex items-center">
                        <input type="hidden" name="is_available_for_hm" value="0"> {{-- Hidden field for unchecked checkbox --}}
                        <input type="checkbox" name="is_available_for_hm" id="is_available_for_hm" value="1" class="focus:ring-[#cc8e45] h-4 w-4 text-[#33595a] border-gray-300 rounded" {{ old('is_available_for_hm', $accommodation->is_available_for_hm) ? 'checked' : '' }}>
                        <label for="is_available_for_hm" class="ml-2 block text-sm text-gray-900">Available for Home Modifications (HM)</label>
                        @error('is_available_for_hm')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Photo Management Section --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Photos (Max 5 total, 1MB each)</label>

                        {{-- Display Existing Photos --}}
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" x-show="existingPhotos.length > 0">
                            <template x-for="(photoPath, index) in existingPhotos" :key="index">
                                <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img :src="'{{ asset('storage') }}/' + photoPath" alt="Existing Photo" class="w-full h-full object-cover">
                                    {{-- Hidden input to tell backend which photos to keep --}}
                                    <input type="hidden" name="photos_to_keep[]" :value="photoPath">
                                    <button type="button" @click="removeExistingPhoto(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-80 hover:opacity-100 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p x-show="existingPhotos.length === 0 && uploadedPhotos.length === 0" class="text-sm text-gray-500 mt-2">No photos uploaded yet. Max 5 total.</p>

                        {{-- Input for New Photos --}}
                        <label for="new_photos" class="block text-sm font-medium text-gray-700 mt-4">Upload New Photos</label>
                        <input type="file" name="new_photos[]" id="new_photos" multiple
                               @change="handleNewPhotoUpload($event)"
                               accept="image/jpeg,image/png,image/gif"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7ed] file:text-[#33595a] hover:file:bg-[#e1e7dd] cursor-pointer"
                               x-bind:disabled="currentTotalPhotos() >= maxPhotos">

                        <p x-show="currentTotalPhotos() >= maxPhotos" class="text-sm text-gray-500 mt-1">Maximum number of photos reached.</p>
                        @error('new_photos')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @error('new_photos.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div x-show="photoErrors.length > 0" class="mt-2">
                            <template x-for="error in photoErrors" :key="error">
                                <p class="text-red-500 text-xs mt-1" x-text="error"></p>
                            </template>
                        </div>

                        {{-- Display Newly Uploaded Photos --}}
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" x-show="uploadedPhotos.length > 0">
                            <template x-for="(photo, index) in uploadedPhotos" :key="'new-photo-' + index">
                                <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img :src="photo.preview" alt="New Photo preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removeNewPhoto(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-80 hover:opacity-100 transition-opacity">
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
                    <button type="submit" class="bg-[#33595a] text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-[#2c494a] transition duration-300 shadow-md">
                        Update Accommodation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('accommodationForm', () => ({
                suburbs: [],
                selectedSuburb: '{{ old('suburb', $accommodation->suburb) }}', // Retain old/current suburb selection
                existingPhotos: @json($accommodation->photos ?? []), // Paths of current photos from DB
                uploadedPhotos: [], // Stores { file: File, preview: URL } for new uploads
                photoErrors: [],
                maxPhotos: 5,
                maxPhotoSizeKB: 1024, // 1MB

                init() {
                    // Fetch suburbs for the initially selected state (current accommodation's state)
                    const initialState = document.getElementById('state').value;
                    if (initialState) {
                        this.fetchSuburbs(initialState, true); // true indicates initial load to set old/current suburb
                    }
                },

                async fetchSuburbs(event, isInitialLoad = false) {
                    let stateCode;
                    if (isInitialLoad) {
                        stateCode = event; // 'event' is actually the state code here
                    } else {
                        stateCode = event.target.value;
                    }

                    this.suburbs = [];
                    // Only reset selectedSuburb if it's not an initial load,
                    // or if the initial selected state has changed or has no matching suburb.
                    if (!isInitialLoad || !this.suburbs.includes(this.selectedSuburb)) {
                        this.selectedSuburb = '';
                    }


                    if (!stateCode) {
                        return;
                    }

                    try {
                        const response = await fetch(`/get-suburbs/${stateCode}`);
                        if (!response.ok) {
                            throw new Error('Failed to fetch suburbs.');
                        }
                        const data = await response.json();
                        this.suburbs = data;

                        // If it's an initial load and old/current suburb exists in new list, set it
                        const oldOrCurrentSuburb = '{{ old('suburb', $accommodation->suburb) }}';
                        if (isInitialLoad && oldOrCurrentSuburb && this.suburbs.includes(oldOrCurrentSuburb)) {
                             this.selectedSuburb = oldOrCurrentSuburb;
                        }
                    } catch (error) {
                        console.error("Error fetching suburbs:", error);
                        // Optionally show a user-friendly error message
                    }
                },

                currentTotalPhotos() {
                    return this.existingPhotos.length + this.uploadedPhotos.length;
                },

                handleNewPhotoUpload(event) {
                    this.photoErrors = [];
                    const files = Array.from(event.target.files);

                    if (this.currentTotalPhotos() + files.length > this.maxPhotos) {
                        this.photoErrors.push(`You can upload a maximum of ${this.maxPhotos - this.existingPhotos.length} new photos, remaining ${this.maxPhotos - this.currentTotalPhotos()} slots.`);
                        event.target.value = ''; // Clear input if too many files selected initially
                        return;
                    }

                    let validFiles = [];
                    files.forEach(file => {
                        if (this.currentTotalPhotos() >= this.maxPhotos) {
                            this.photoErrors.push(`Cannot add more photos. Maximum of ${this.maxPhotos} photos reached.`);
                            return; // Stop processing further files
                        }

                        if (file.size > this.maxPhotoSizeKB * 1024) { // Convert KB to bytes
                            this.photoErrors.push(`Photo "${file.name}" exceeds the ${this.maxPhotoSizeKB / 1024}MB limit.`);
                        } else if (!file.type.match('image/jpeg|image/png|image/gif')) {
                            this.photoErrors.push(`File "${file.name}" is not a valid image type (JPEG, PNG, GIF).`);
                        } else {
                            validFiles.push(file);
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.uploadedPhotos.push({ file: file, preview: e.target.result });
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Update the FileList object for the input field to only include valid files
                    const dataTransfer = new DataTransfer();
                    validFiles.forEach(item => dataTransfer.items.add(item)); // Add actual file objects
                    event.target.files = dataTransfer.files;

                     // Clear the input value if no valid files were added or if max was reached
                    if (validFiles.length === 0 && files.length > 0) {
                         event.target.value = '';
                    }

                    // Re-evaluate errors for exceeding max photos if some were valid but others put it over
                    if (this.currentTotalPhotos() > this.maxPhotos) {
                        this.photoErrors.push(`You can upload a maximum of ${this.maxPhotos} photos total.`);
                    }
                },

                removeExistingPhoto(index) {
                    this.existingPhotos.splice(index, 1);
                    this.photoErrors = this.photoErrors.filter(error => !error.includes('maximum of')); // Clear related errors
                },

                removeNewPhoto(index) {
                    this.uploadedPhotos.splice(index, 1);

                    // Update the FileList object for the new_photos input field after removal
                    const input = document.getElementById('new_photos');
                    const dataTransfer = new DataTransfer();
                    this.uploadedPhotos.forEach(item => dataTransfer.items.add(item.file));
                    input.files = dataTransfer.files;

                    this.photoErrors = this.photoErrors.filter(error => !error.includes('maximum of')); // Clear related errors
                }
            }));
        });
    </script>
@endsection