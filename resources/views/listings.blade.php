@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">Available Listings</h1>
        <p class="text-lg text-gray-600 text-center">
            Browse through our range of NDIS-approved accommodations.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Cozy Apartment in Sydney</h2>
                <p class="text-gray-600 mb-1">Type: Apartment</p>
                <p class="text-gray-600 mb-1">Bedrooms: 2 | Bathrooms: 1</p>
                <p class="text-green-600 font-bold text-xl mb-4">$450 / week</p>
                <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">View Details</a>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Spacious House in Melbourne</h2>
                <p class="text-gray-600 mb-1">Type: House</p>
                <p class="text-gray-600 mb-1">Bedrooms: 3 | Bathrooms: 2</p>
                <p class="text-green-600 font-bold text-xl mb-4">$600 / week</p>
                <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">View Details</a>
            </div>
            </div>
    </div>
@endsection