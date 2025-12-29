@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">Edit Accommodation: {{ $accommodation->title }}</h1>
            <a href="{{ route('provider.accommodations.index') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left mr-2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
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
                        <input type="text" name="suburb" id="suburb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#cc8e45] focus:ring-[#cc8e45] sm:text-base p-2.5" value="{{ old('suburb', $accommodation->suburb) }}" required>
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
                        <label class="block text-sm font-medium text-gray-700">Photos (Max 10 total, 1MB each)</label>

                        {{-- Display Existing Photos --}}
                        @php
                            $photos = $accommodation->photos ?? [];
                            $uniquePhotos = array_unique($photos);
                        @endphp
                        
                        @if(count($uniquePhotos) > 0)
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach($uniquePhotos as $index => $photoPath)
                                    <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                        <img src="{{ accommodation_image_url($photoPath) }}" alt="Existing Photo" class="w-full h-full object-cover">
                                        <button type="button" onclick="removePhoto({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-80 hover:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Hidden inputs for photos to keep --}}
                        @foreach($uniquePhotos as $photoPath)
                            <input type="hidden" name="photos_to_keep[]" value="{{ $photoPath }}">
                        @endforeach
                        
                        @if(count($uniquePhotos) === 0)
                            <p class="text-sm text-gray-500 mt-2">No photos uploaded yet. Max 10 total.</p>
                        @endif

                        {{-- Input for New Photos --}}
                        <label for="new_photos" class="block text-sm font-medium text-gray-700 mt-4">Upload New Photos</label>
                        <input type="file" name="new_photos[]" id="new_photos" multiple
                               accept="image/jpeg,image/png,image/gif"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f2f7ed] file:text-[#33595a] hover:file:bg-[#e1e7dd] cursor-pointer">
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
        // Message handling functions
        function showMessage(type, message) {
            if (type === 'success') {
                window.modalManager.success(message);
            } else if (type === 'error') {
                window.modalManager.error(message);
            }
        }

        function hideMessage(messageId) {
            // This function is kept for compatibility but now uses the modal manager
            if (messageId === 'success-message') {
                window.modalManager.hide('success-modal');
            } else if (messageId === 'error-message') {
                window.modalManager.hide('error-modal');
            }
        }

        // Check for session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                window.modalManager.success('{{ session('status') }}');
            @endif
            
            @if (session('error'))
                window.modalManager.error('{{ session('error') }}');
            @endif
        });

        function removePhoto(index) {
            // Create a hidden input to mark this photo for removal
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'photos_to_remove[]';
            hiddenInput.value = index;
            document.querySelector('form').appendChild(hiddenInput);
            
            // Hide the photo element
            event.target.closest('.relative').style.display = 'none';
        }
    </script>
@endsection