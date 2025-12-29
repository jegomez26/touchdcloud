@extends('company.provider-db')

@section('main-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-[#3e4732]">Accommodation Details: {{ $accommodation->title }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('provider.accommodations.index') }}" class="bg-[#bcbabb] text-white px-6 py-2 rounded-md hover:bg-[#a09d9b] transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left mr-2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Back to List
                </a>
                <a href="{{ route('provider.accommodations.edit', $accommodation) }}" class="bg-[#cc8e45] text-white px-6 py-2 rounded-md hover:bg-[#a67139] transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-edit-3 mr-2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    Edit Accommodation
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-[#3e4732] mb-4">{{ $accommodation->title }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p class="font-medium">Type:</p>
                    <p>{{ $accommodation->type }}</p>
                </div>
                <div>
                    <p class="font-medium">Status:</p>
                    <p>
                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full
                            @if($accommodation->status == 'available') bg-green-100 text-green-800
                            @elseif($accommodation->status == 'occupied') bg-red-100 text-red-800
                            @elseif($accommodation->status == 'draft') bg-gray-100 text-gray-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($accommodation->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="font-medium">Address:</p>
                    <p>{{ $accommodation->address }}, {{ $accommodation->suburb }}, {{ $accommodation->state }} {{ $accommodation->post_code }}</p>
                </div>
                <div>
                    <p class="font-medium">Rent Per Week:</p>
                    <p>${{ number_format($accommodation->rent_per_week, 2) }}</p>
                </div>
                <div>
                    <p class="font-medium">Bedrooms:</p>
                    <p>{{ $accommodation->num_bedrooms }}</p>
                </div>
                <div>
                    <p class="font-medium">Bathrooms:</p>
                    <p>{{ $accommodation->num_bathrooms }}</p>
                </div>
                <div>
                    <p class="font-medium">Vacancies:</p>
                    <p>{{ $accommodation->total_vacancies - $accommodation->current_occupancy }} out of {{ $accommodation->total_vacancies }}</p>
                </div>
                <div>
                    <p class="font-medium">Available for Home Modifications:</p>
                    <p>{{ $accommodation->is_available_for_hm ? 'Yes' : 'No' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="font-medium text-gray-700">Description:</p>
                <p class="text-gray-900 leading-relaxed">{{ $accommodation->description }}</p>
            </div>

            <div class="mt-6">
                <p class="font-medium text-gray-700 mb-2">Amenities:</p>
                @if($accommodation->amenities && count($accommodation->amenities) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($accommodation->amenities as $amenity)
                            <span class="bg-[#f2f7ed] text-[#3e4732] px-3 py-1 rounded-full text-sm font-medium">{{ $amenity }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No amenities listed.</p>
                @endif
            </div>

            <div class="mt-6">
                <p class="font-medium text-gray-700 mb-2">Photos:</p>
                @if($accommodation->photos && count($accommodation->photos) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($accommodation->photos as $photoPath)
                            {{-- Adjust asset path based on where you store photos (e.g., storage/app/public/accommodations) --}}
                            <img src="{{ accommodation_image_url($photoPath) }}" alt="{{ $accommodation->title }} Photo" class="w-full h-32 object-cover rounded-lg shadow-md">
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No photos uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Check for session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                window.modalManager.success('{{ session('status') }}');
            @endif
            
            @if (session('error'))
                window.modalManager.error('{{ session('error') }}');
            @endif
        });
    </script>
@endsection