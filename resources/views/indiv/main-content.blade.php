{{-- resources/views/indiv/dashboard.blade.php --}}
@extends('indiv.indiv-db') {{-- Point to your new layout file --}}

@section('title', 'My Dashboard') {{-- Set a specific title for this page --}}

@section('main-content')
    <h2 class="text-2xl font-semibold text-[#3e4732] mb-6">Welcome, {{ Auth::user()->first_name }}!</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Example Dashboard Card 1 --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-[#33595a] mb-2">My Plans</h3>
            <p class="text-gray-700">View your current NDIS plans and allocated funds.</p>
            <a href="#" class="mt-4 inline-block text-[#cc8e45] hover:underline">Go to Plans &rarr;</a>
        </div>

        {{-- Example Dashboard Card 2 --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-[#33595a] mb-2">My Providers</h3>
            <p class="text-gray-700">Discover and manage your chosen service providers.</p>
            <a href="#" class="mt-4 inline-block text-[#cc8e45] hover:underline">Find Providers &rarr;</a>
        </div>

        {{-- Example Dashboard Card 3 (could be for messages) --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-[#33595a] mb-2">Messages</h3>
            <p class="text-gray-700">Check your inbox for new messages and conversations.</p>
            <a href="{{ route('indiv.messages.inbox') }}" class="mt-4 inline-block text-[#cc8e45] hover:underline">View Messages &rarr;</a>
        </div>

        {{-- Add more dashboard content here --}}
    </div>
@endsection