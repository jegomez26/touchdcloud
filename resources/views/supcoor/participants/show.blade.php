@extends('supcoor.sc-db') {{-- Extend your sc-db layout --}}

@section('main-content') {{-- Start the main-content section --}}
    <h2 class="font-bold text-2xl md:text-3xl text-[#33595a] leading-tight mb-6 md:mb-8 text-center sm:text-left">
        {{ __('Participant Profile') }}
    </h2>

    <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 lg:p-10 border border-gray-100"> {{-- Elevated card styling --}}

        {{-- Participant Header Section --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start mb-8 pb-6 border-b border-gray-200">
            @php
                $avatarPath = 'images/general.png'; // Default
                $randomMale = rand(1, 2);
                $randomFemale = rand(1, 2);
                if ($participant->gender === 'Male') {
                    $avatarPath = 'images/male' . $randomMale . '.png';
                } elseif ($participant->gender === 'Female') {
                    $avatarPath = 'images/female' . $randomFemale . '.png';
                }
            @endphp
            <img src="{{ asset($avatarPath) }}" alt="{{ $participant->gender ?? 'General' }} Avatar" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full ring-4 ring-[#cc8e45] object-cover mb-4 sm:mb-0 sm:mr-6 shadow-md">

            <div class="text-center sm:text-left">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-[#33595a] leading-tight mb-1">
                    {{ $participant->first_name }} {{ $participant->middle_name }} {{ $participant->last_name }}
                </h1>
                <p class="text-lg text-gray-700 mb-2">
                    <span class="font-semibold">Code:</span> {{ $participant->participant_code_name ?? 'N/A' }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-1 text-gray-600 text-sm">
                    @if($participant->birthday)
                        <span><span class="font-medium">Birthday:</span> {{ \Carbon\Carbon::parse($participant->birthday)->format('F j, Y') }}</span>
                    @endif
                    @if($participant->gender)
                        <span><span class="font-medium">Gender:</span> {{ $participant->gender }}</span>
                    @endif
                    @if($participant->suburb && $participant->state)
                        <span><span class="font-medium">Location:</span> {{ $participant->suburb }}, {{ $participant->state }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="font-semibold text-[#33595a] mb-1 text-lg">Accommodation</p>
                <p class="text-gray-700">
                    <span class="font-medium">Current Type:</span> {{ $participant->accommodation_type ?? 'N/A' }}
                </p>
                <p class="text-gray-700">
                    <span class="font-medium">Approved NDIS Type:</span> {{ $participant->approved_accommodation_type ?? 'N/A' }}
                </p>
                <p class="text-gray-700">
                    <span class="font-medium">Looking for Home:</span> {{ $participant->is_looking_hm ? 'Yes' : 'No' }}
                </p>
                <p class="text-gray-700">
                    <span class="font-medium">Has Accommodation:</span> {{ $participant->has_accommodation ? 'Yes' : 'No' }}
                </p>
            </div>

            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="font-semibold text-[#33595a] mb-1 text-lg">Disability Information</p>
                <p class="text-gray-700 mb-2">
                    <span class="font-medium">Type(s):</span>
                    @if(!empty($participant->disability_type))
                        @php
                            $disabilities = is_string($participant->disability_type)
                                            ? (json_decode($participant->disability_type) ?? [])
                                            : ($participant->disability_type ?? []);
                        @endphp
                        <div class="flex flex-wrap gap-2 mt-1">
                            @forelse($disabilities as $disability)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#d0dbcc] text-[#3e4732] shadow-sm">
                                    {{ $disability }}
                                </span>
                            @empty
                                <span class="text-gray-500 text-sm">None specified</span>
                            @endforelse
                        </div>
                    @else
                        N/A
                    @endif
                </p>
                <p class="text-gray-700 mt-2">
                    <span class="font-medium">Specific Details:</span>
                    <span class="block whitespace-pre-wrap mt-1">{{ $participant->specific_disability ?? 'N/A' }}</span>
                </p>
                <p class="text-gray-700 mt-2">
                    <span class="font-medium">Behavior of Concern:</span>
                    <span class="block whitespace-pre-wrap mt-1">{{ $participant->behavior_of_concern ?? 'N/A' }}</span>
                </p>
            </div>

            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="font-semibold text-[#33595a] mb-1 text-lg">Funding & Address</p>
                <p class="text-gray-700">
                    <span class="font-medium">Funding (Support Coor.):</span>
                    {{ $participant->funding_amount_support_coor ? '$' . number_format($participant->funding_amount_support_coor, 2) : 'N/A' }}
                </p>
                <p class="text-gray-700">
                    <span class="font-medium">Funding (Accommodation):</span>
                    {{ $participant->funding_amount_accommodation ? '$' . number_format($participant->funding_amount_accommodation, 2) : 'N/A' }}
                </p>
                <p class="text-gray-700 mt-2">
                    <span class="font-medium">Address:</span>
                    <span class="block mt-1">
                        @if($participant->street_address || $participant->suburb || $participant->state || $participant->post_code)
                            {{ $participant->street_address ?? '' }}
                            @if($participant->suburb), {{ $participant->suburb }}@endif
                            @if($participant->state), {{ $participant->state }}@endif
                            @if($participant->post_code) {{ $participant->post_code }}@endif
                        @else
                            N/A
                        @endif
                    </span>
                </p>
            </div>
        </div>

        {{-- Documents Section (New Design) --}}
        @if($participant->health_report_path || $participant->assessment_path || $participant->uploaded_file_path)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-bold text-xl text-[#33595a] mb-4">Uploaded Documents</h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="overflow-x-auto"> {{-- Make table scrollable on small screens --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Document Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if($participant->health_report_path)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Health Report</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ basename($participant->health_report_path) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ Storage::url($participant->health_report_path) }}" target="_blank" class="text-[#cc8e45] hover:text-[#33595a] inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if($participant->assessment_path)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Assessment Report</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ basename($participant->assessment_path) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ Storage::url($participant->assessment_path) }}" target="_blank" class="text-[#cc8e45] hover:text-[#33595a] inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if($participant->uploaded_file_path)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Additional Document</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ basename($participant->uploaded_file_path) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ Storage::url($participant->uploaded_file_path) }}" target="_blank" class="text-[#cc8e45] hover:text-[#33595a] inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if(!$participant->health_report_path && !$participant->assessment_path && !$participant->uploaded_file_path)
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No documents uploaded.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- Action Buttons --}}
        <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('sc.participants.edit', $participant) }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#cc8e45] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md w-full sm:w-auto">
                Edit Participant
            </a>
            <a href="{{ route('sc.participants.list') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md w-full sm:w-auto">
                Back to Participants
            </a>
        </div>
    </div>
@endsection