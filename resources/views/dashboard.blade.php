@extends('indiv.indiv-db') 

@section('content')
    <div id="dashboard-section" class="p-6 bg-white rounded-xl shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome, John Smith!</h2>
        <p class="text-gray-600">This is your personalized dashboard overview.</p>
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 text-blue-800">
            <p class="font-semibold">Quick Updates:</p>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>Your next meeting with Sarah Coordinator is on 2024-07-25.</li>
                <li>New accommodation options available in your area.</li>
                <li>Review your latest support plan updates.</li>
            </ul>
        </div>
    </div>
@endsection