{{-- resources/views/indiv/main-content.blade.php --}}
@extends('indiv.indiv-db') {{-- Extend the main dashboard layout --}}

@section('main-content') {{-- Match the @yield in indiv-db.blade.php --}}
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Welcome, {{ $user->first_name ?? 'Participant' }}!</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-3">Your Dashboard Overview</h3>
            <p class="text-gray-700">This is your main dashboard content. You can add widgets, summaries, or links to key features here.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-3">Recent Activity</h3>
            <ul class="list-disc list-inside text-gray-700">
                <li>Updated profile on July 19, 2025</li>
                <li>Sent a message to coordinator.</li>
            </ul>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-3">Quick Links</h3>
            <nav class="space-y-2">
                <a href="#" class="text-indigo-600 hover:underline">View Messages</a>
                <a href="#" class="text-indigo-600 hover:underline">Manage Settings</a>
                <a href="#" class="text-indigo-600 hover:underline">Find Housemates</a>
            </nav>
        </div>
    </div>
@endsection