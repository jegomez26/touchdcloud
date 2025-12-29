@extends('company.provider-db')

@section('main-content')
<div class="container mx-auto p-4 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('provider.dashboard') }}" class="hover:text-[#cc8e45] transition duration-300">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('provider.enquiries.index') }}" class="hover:text-[#cc8e45] transition duration-300">Enquiries</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-[#33595a] font-medium">Enquiry Details</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            {{-- Enquiry Details --}}
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-[#33595a]">Enquiry Details</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($enquiry->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($enquiry->status === 'tended') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($enquiry->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-[#33595a] mb-2">Enquirer Information</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Name:</span> {{ $enquiry->name }}</p>
                            <p><span class="font-medium">Email:</span> 
                                <a href="mailto:{{ $enquiry->email }}" class="text-[#cc8e45] hover:underline">
                                    {{ $enquiry->email }}
                                </a>
                            </p>
                            @if($enquiry->phone)
                                <p><span class="font-medium">Phone:</span> 
                                    <a href="tel:{{ $enquiry->phone }}" class="text-[#cc8e45] hover:underline">
                                        {{ $enquiry->phone }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-[#33595a] mb-2">Property Information</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Property:</span> {{ $enquiry->property->title }}</p>
                            <p><span class="font-medium">Location:</span> {{ $enquiry->property->suburb }}, {{ $enquiry->property->state }}</p>
                            <p><span class="font-medium">Type:</span> {{ $enquiry->property->type }}</p>
                            <p><span class="font-medium">Rent:</span> ${{ number_format($enquiry->property->rent_per_week) }}/week</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-[#33595a] mb-2">Message</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $enquiry->message }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-[#33595a] mb-2">Enquiry Date</h3>
                        <p class="text-gray-700">{{ $enquiry->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($enquiry->tended_at)
                        <div>
                            <h3 class="text-lg font-semibold text-[#33595a] mb-2">Tended Date</h3>
                            <p class="text-gray-700">{{ $enquiry->tended_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Property Preview --}}
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-[#33595a] mb-6">Property Preview</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!empty($enquiry->property->photos) && count($enquiry->property->photos) > 0)
                        <img src="{{ asset('storage/' . $enquiry->property->photos[0]) }}" 
                             alt="{{ $enquiry->property->title }}" 
                             class="w-full h-48 object-cover rounded-lg">
                    @else
                        <img src="{{ asset('images/house-1.jpg') }}" 
                             alt="{{ $enquiry->property->title }}" 
                             class="w-full h-48 object-cover rounded-lg">
                    @endif
                    
                    <div>
                        <h3 class="text-xl font-bold text-[#33595a] mb-2">{{ $enquiry->property->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ $enquiry->property->description }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="bg-[#e1e7dd] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-bed text-[#cc8e45]"></i>
                                </div>
                                <p class="text-sm text-gray-600">Bedrooms</p>
                                <p class="font-bold text-[#33595a]">{{ $enquiry->property->num_bedrooms }}</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-[#e1e7dd] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-shower text-[#cc8e45]"></i>
                                </div>
                                <p class="text-sm text-gray-600">Bathrooms</p>
                                <p class="font-bold text-[#33595a]">{{ $enquiry->property->num_bathrooms }}</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('accommodation.show', $enquiry->property) }}" 
                           class="inline-block bg-[#cc8e45] text-white px-4 py-2 rounded-md hover:bg-[#a67137] transition duration-300">
                            <i class="fas fa-external-link-alt mr-2"></i>View Property
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Status Update Form --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 sticky top-24">
                <h2 class="text-xl font-bold text-[#33595a] mb-6">Update Status</h2>
                
                <form action="{{ route('provider.enquiries.update', $enquiry) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-[#33595a] mb-2">Status</label>
                        <select name="status" id="status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                            <option value="pending" {{ $enquiry->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="tended" {{ $enquiry->status === 'tended' ? 'selected' : '' }}>Tended</option>
                            <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="provider_notes" class="block text-sm font-medium text-[#33595a] mb-2">Notes</label>
                        <textarea name="provider_notes" id="provider_notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent"
                                  placeholder="Add any notes about this enquiry...">{{ $enquiry->provider_notes }}</textarea>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-[#cc8e45] text-white font-bold py-3 px-6 rounded-md hover:bg-[#a67137] transition duration-300">
                        <i class="fas fa-save mr-2"></i>Update Enquiry
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection
