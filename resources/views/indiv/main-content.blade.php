{{-- resources/views/indiv/dashboard.blade.php --}}
@extends('indiv.indiv-db') {{-- Point to your new layout file --}}

@section('title', 'My Dashboard') {{-- Set a specific title for this page --}}

@section('main-content')
    <h2 class="text-2xl font-semibold text-[#3e4732] mb-6">Welcome, {{ Auth::user()->first_name }}!</h2>

    @if (!$basicDetailsComplete)
        {{-- Prompt for incomplete basic details --}}
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
            <p class="font-bold">Important: Your Basic Details are incomplete!</p>
            <p>Please complete your <a href="{{ route('indiv.profile.basic-details') }}" class="font-semibold underline text-yellow-800 hover:text-yellow-900">Basic Details</a> to access the full dashboard and be matched with Support Coordinators.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Profile Completion Progress Card (visible when not completed) --}}
            <div class="bg-white p-6 rounded-lg shadow-md col-span-full md:col-span-1">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Profile Completion</h3>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-[#cc8e45] h-4 rounded-full" style="width: {{ $profileCompletionPercentage }}%"></div>
                </div>
                <p class="text-gray-700 text-sm mb-4">Your profile is {{ $profileCompletionPercentage }}% complete.</p>
                <p class="text-gray-700 mb-4">To find a match with Support Coordinators, you must **complete your profile** first.</p>
                <a href="{{ route('indiv.profile.basic-details') }}" class="mt-4 inline-block text-[#cc8e45] hover:underline font-semibold">Continue Profile &rarr;</a>
            </div>

            {{-- Optional: Show disabled versions of other sections or a message that they are locked --}}
            <div class="bg-white p-6 rounded-lg shadow-md opacity-50 pointer-events-none">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Applying Support Coordinators</h3>
                <p class="text-gray-700">Support Coordinators can apply to assist you once your profile is complete.</p>
                <span class="mt-4 inline-block text-gray-500">Complete profile to view</span>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md opacity-50 pointer-events-none">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Latest Messages</h3>
                <p class="text-gray-700">Your latest messages will appear here.</p>
                <span class="mt-4 inline-block text-gray-500">Complete profile to view</span>
            </div>

        </div>
    @else
        {{-- Display full dashboard content when basic details are complete --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Participant Profile Completion (Even if completed, show a general status) --}}
            <div class="bg-white p-6 rounded-lg shadow-md col-span-full md:col-span-1">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Profile Status</h3>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-[#cc8e45] h-4 rounded-full" style="width: {{ $profileCompletionPercentage }}%"></div>
                </div>
                <p class="text-gray-700 text-sm mb-4">Your profile is {{ $profileCompletionPercentage }}% complete.</p>
                @if ($profileCompletionPercentage < 100)
                    <p class="text-gray-700">Consider completing the remaining sections for better matches!</p>
                    <a href="{{ route('indiv.profile.ndis-support-needs') }}" class="mt-4 inline-block text-[#cc8e45] hover:underline">Continue Profile &rarr;</a>
                @else
                    <p class="text-green-700 font-semibold">Your profile is 100% complete. You're ready to find matches!</p>
                @endif
            </div>

            {{-- Applying Support Coordinators Section --}}
            <div class="bg-white p-6 rounded-lg shadow-md md:col-span-1">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Applying Support Coordinators</h3>
                @if ($applyingCoordinators->isEmpty())
                    <p class="text-gray-700">No Support Coordinators have applied yet. Ensure your profile is fully detailed to attract matches!</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($applyingCoordinators as $application)
                            <li class="border-b pb-2 last:border-b-0 last:pb-0">
                                <p class="font-semibold text-gray-800">{{ $application->supportCoordinator->user->first_name }} {{ $application->supportCoordinator->user->last_name }}</p>
                                <p class="text-sm text-gray-600">Applied: {{ $application->created_at->diffForHumans() }}</p>
                                {{-- Add actions like "View Profile", "Accept", "Reject" --}}
                                <div class="mt-2 flex space-x-2">
                                    <a href="#" class="text-sm text-blue-600 hover:underline">View Profile</a>
                                    <a href="#" class="text-sm text-green-600 hover:underline">Accept</a>
                                    <a href="#" class="text-sm text-red-600 hover:underline">Reject</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a href="#" class="mt-4 inline-block text-[#cc8e45] hover:underline">View All Applications &rarr;</a>
            </div>

            {{-- Latest Messages Preview --}}
            <div class="bg-white p-6 rounded-lg shadow-md md:col-span-1">
                <h3 class="text-xl font-semibold text-[#33595a] mb-2">Latest Messages</h3>
                @if ($latestMessages->isEmpty())
                    <p class="text-gray-700">You have no new messages.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($latestMessages as $message)
                            <li class="border-b pb-2 last:border-b-0 last:pb-0">
                                <p class="font-semibold text-gray-800">From: {{ $message['sender_name'] }}</p>
                                <p class="text-sm text-gray-600 truncate">{{ $message['last_message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $message['message_time']->diffForHumans() }}</p>
                                <a href="{{ route('indiv.messages.show', $message['conversation_id']) }}" class="text-sm text-blue-600 hover:underline">View Conversation</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ route('indiv.messages.inbox') }}" class="mt-4 inline-block text-[#cc8e45] hover:underline">Go to Inbox &rarr;</a>
            </div>

        </div>
    @endif
@endsection