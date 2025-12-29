@extends('company.provider-db')

@section('main-content')
<div class="container mx-auto p-4 py-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-[#33595a] mb-4">Accommodation Enquiries</h1>
        <p class="text-lg text-gray-600">Manage enquiries for your accommodations</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Enquiries</p>
                    <p class="text-2xl font-bold text-[#33595a]">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3 mr-4">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-[#33595a]">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tended</p>
                    <p class="text-2xl font-bold text-[#33595a]">{{ $stats['tended'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-full p-3 mr-4">
                    <i class="fas fa-archive text-gray-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Closed</p>
                    <p class="text-2xl font-bold text-[#33595a]">{{ $stats['closed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ route('provider.enquiries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-[#33595a] mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="tended" {{ request('status') == 'tended' ? 'selected' : '' }}>Tended</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label for="property_id" class="block text-sm font-medium text-[#33595a] mb-2">Property</label>
                <select name="property_id" id="property_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-[#33595a] mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Search by name, email..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#cc8e45] text-white px-4 py-2 rounded-md hover:bg-[#a67137] transition duration-300">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Enquiries Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enquirer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enquiries as $enquiry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $enquiry->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $enquiry->email }}</div>
                                    @if($enquiry->phone)
                                        <div class="text-sm text-gray-500">{{ $enquiry->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $enquiry->property->title }}</div>
                                <div class="text-sm text-gray-500">{{ $enquiry->property->suburb }}, {{ $enquiry->property->state }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($enquiry->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($enquiry->status === 'tended') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($enquiry->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $enquiry->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-400">{{ $enquiry->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('provider.enquiries.show', $enquiry) }}" 
                                   class="text-[#cc8e45] hover:text-[#a67137] mr-3">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-envelope text-4xl mb-4"></i>
                                    <p class="text-lg">No enquiries found</p>
                                    <p class="text-sm">Enquiries will appear here when people contact you about your accommodations.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($enquiries->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
