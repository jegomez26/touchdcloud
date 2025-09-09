@extends('company.provider-db') 

@section('main-content') {{-- Start the main-content section --}}
    <h2 class="font-bold text-3xl text-gray-800 leading-tight mb-8 text-center sm:text-left">
        {{ __('Participant Profile') }}
    </h2>

    <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 lg:p-10 border border-gray-100 transform transition-all duration-300 hover:scale-[1.005]"> {{-- Elevated card styling with subtle hover effect --}}

        {{-- Participant Header Section --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start mb-8 pb-6 border-b border-gray-200">
            @php
                $avatarPath = 'images/general.png'; // Default
                $randomMale = rand(1, 2);
                $randomFemale = rand(1, 2);
                if (isset($participant->gender_identity) && $participant->gender_identity === 'Male') {
                    $avatarPath = 'images/male' . $randomMale . '.png';
                } elseif (isset($participant->gender_identity) && $participant->gender_identity === 'Female') {
                    $avatarPath = 'images/female' . $randomFemale . '.png';
                }
            @endphp
            <img src="{{ asset($avatarPath) }}" alt="{{ $participant->gender_identity ?? 'General' }} Avatar" class="w-28 h-28 sm:w-36 sm:h-36 rounded-full ring-4 ring-blue-300 object-cover mb-6 sm:mb-0 sm:mr-8 shadow-lg transition-transform duration-300 hover:scale-105">

            <div class="text-center sm:text-left flex-grow">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight mb-2">
                    {{ $participant->first_name }} {{ $participant->middle_name }} {{ $participant->last_name }}
                </h1>
                <p class="text-xl text-gray-600 mb-3">
                    <span class="font-semibold">Code:</span> {{ $participant->participant_code_name ?? 'N/A' }}
                </p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-x-6 gap-y-2 text-gray-500 text-base">
                    @if($participant->date_of_birth)
                        <span class="flex items-center"><svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="font-medium">Born:</span> {{ \Carbon\Carbon::parse($participant->date_of_birth)->format('F j, Y') }}</span>
                    @endif
                    @if($participant->gender_identity)
                        <span class="flex items-center"><svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0 3H12m0 0h6m-6 0h-2M5 19V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19H3v-1a2 2 0 012-2h3m6 0v2m0 0h2a2 2 0 002-2v-2m-8 0H5"></path></svg><span class="font-medium">Gender:</span> {{ $participant->gender_identity }}</span>
                    @endif
                    @if($participant->suburb && $participant->state)
                        <span class="flex items-center"><svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="font-medium">Location:</span> {{ $participant->suburb }}, {{ $participant->state }}</span>
                    @endif
                    @if($participant->pronouns)
                        {{-- FIX: No json_decode needed here if pronouns is cast to array --}}
                        <span class="flex items-center"><svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg><span class="font-medium">Pronouns:</span> {{ implode(', ', json_decode($participant->pronouns)) }}</span>
                    @endif
                </div>
            </div>
        </div>


        {{-- Main Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
            <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-blue-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m8-10v4m0 0h-4m4 0h4"></path></svg>Accommodation</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">Current Type:</span> {{ $participant->current_living_situation ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Looking for Home:</span> {{ $participant->move_in_availability ? ($participant->move_in_availability !== 'Just exploring options' ? 'Yes, ' . $participant->move_in_availability : 'Exploring options') : 'N/A' }}</li>
                    <li><span class="font-semibold">Preferred Locations:</span>
                        @if(!empty($participant->preferred_sil_locations))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->preferred_sil_locations && is_array($participant->preferred_sil_locations))
                                    @foreach($participant->preferred_sil_locations as $location)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-200 text-blue-800 shadow-sm">
                                            {{ $location['suburb'] ?? '' }}, {{ $location['state'] ?? '' }}
                                        </span>
                                    @endforeach
                                @else
                                    {{-- This fallback should ideally not be hit if casting is correct --}}
                                    <p>No preferred locations set.</p>
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    <li><span class="font-semibold">Accessibility Needs:</span> {{ $participant->accessibility_needs_in_home ?? 'N/A' }}</li>
                    @if($participant->accessibility_needs_details)
                        <li class="pl-4 text-sm text-gray-600">({{ $participant->accessibility_needs_details }})</li>
                    @endif
                </ul>
            </div>

            <div class="bg-green-50 p-6 rounded-xl border border-green-100 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-green-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.003 12.003 0 002.92 12c0 3.072 1.054 5.856 2.77 8.253A13.93 13.93 0 0012 21c4.636 0 8.529-2.843 10.489-6.814A12.003 12.003 0 0021.08 12c0-2.493-.678-4.819-1.862-6.816z"></path></svg>Disability & Support</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">Primary Disability:</span> {{ $participant->primary_disability ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Secondary Disability:</span> {{ $participant->secondary_disability ?? 'None' }}</li>
                    <li><span class="font-semibold">Daily Living Support Needs:</span>
                        @if(!empty($participant->daily_living_support_needs))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->daily_living_support_needs && is_array($participant->daily_living_support_needs))
                                    @foreach($participant->daily_living_support_needs as $need)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-200 text-green-800 shadow-sm">{{ $need }}</span>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    @if($participant->daily_living_support_needs_other)
                        <li class="pl-4 text-sm text-gray-600">({{ $participant->daily_living_support_needs_other }})</li>
                    @endif
                    <li><span class="font-semibold">Est. SIL Level:</span> {{ $participant->estimated_support_hours_sil_level ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Night Support:</span> {{ $participant->night_support_type ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Behaviour Support Plan:</span> {{ $participant->behaviour_support_plan_status ?? 'N/A' }}</li>
                    @if($participant->behaviours_of_concern_housemates)
                        <li class="pl-4 text-sm text-gray-600">Details: <span class="block whitespace-pre-wrap mt-1">{{ $participant->behaviours_of_concern_housemates }}</span></li>
                    @endif
                </ul>
            </div>

            <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-100 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-yellow-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V9m0 3v2m0 3.929V19M12 5a2 2 0 110-4 2 2 0 010 4zm0 14a2 2 0 110-4 2 2 0 010 4zm-7-2H3v-1a2 2 0 012-2h2c1.333 0 2.333-1 3-3V7c-.667-2-1.667-3-3-3H5a2 2 0 00-2 2v10a2 2 0 002 2zm14 0h2v-1a2 2 0 00-2-2h-2c-1.333 0-2.333-1-3-3V7c.667-2 1.667-3 3-3h2a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>NDIS & Contact</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">SIL Funding Status:</span> {{ $participant->sil_funding_status ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Plan Review Date:</span> {{ $participant->ndis_plan_review_date ? \Carbon\Carbon::parse($participant->ndis_plan_review_date)->format('F j, Y') : 'N/A' }}</li>
                    <li><span class="font-semibold">Plan Manager:</span> {{ $participant->ndis_plan_manager ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Has Support Coordinator:</span> {{ $participant->has_support_coordinator ? 'Yes' : 'No' }}</li>
                    <li><span class="font-semibold">Participant Email:</span> {{ $participant->participant_email ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Participant Phone:</span> {{ $participant->participant_phone ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Best Contact Method:</span> {{ $participant->participant_contact_method ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Address:</span>
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
                    </li>
                </ul>
            </div>

            <div class="bg-purple-50 p-6 rounded-xl border border-purple-100 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-purple-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364zm0 0l7.682 7.682m0 0l7.682-7.682m-7.682 7.682V4"></path></svg>Preferences & Personality</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">Housemate Preferences:</span>
                        @if(!empty($participant->housemate_preferences))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->housemate_preferences && is_array($participant->housemate_preferences))
                                    @foreach($participant->housemate_preferences as $pref)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-200 text-purple-800 shadow-sm">{{ $pref }}</span>
                                    @endforeach
                                @else
                                    {{-- Optional: Fallback for when there are no preferences or data is malformed --}}
                                    <p>No housemate preferences specified.</p>
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    @if($participant->housemate_preferences_other)
                        <li class="pl-4 text-sm text-gray-600">({{ $participant->housemate_preferences_other }})</li>
                    @endif
                    <li><span class="font-semibold">Number of Housemates:</span> {{ $participant->preferred_number_of_housemates ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Pets:</span> {{ $participant->pets_in_home_preference ?? 'N/A' }}
                        @if($participant->own_pet_type)
                            <span class="ml-1 text-sm text-gray-600">({{ $participant->own_pet_type }})</span>
                        @endif
                    </li>
                    <li><span class="font-semibold">Home Environment:</span>
                        @if(!empty($participant->good_home_environment_looks_like))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->good_home_environment_looks_like && is_array($participant->good_home_environment_looks_like))
                                    @foreach($participant->good_home_environment_looks_like as $env)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-200 text-purple-800 shadow-sm">{{ $env }}</span>
                                    @endforeach
                                @else
                                    {{-- Optional: Fallback for when there's no data or it's malformed --}}
                                    <p>No ideal home environment details specified.</p>
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    @if($participant->good_home_environment_looks_like_other)
                        <li class="pl-4 text-sm text-gray-600">({{ $participant->good_home_environment_looks_like_other }})</li>
                    @endif
                    <li><span class="font-semibold">Self Description:</span>
                        @if(!empty($participant->self_description))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->self_description && is_array($participant->self_description))
                                    @foreach($participant->self_description as $desc)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-200 text-purple-800 shadow-sm">{{ $desc }}</span>
                                    @endforeach
                                @else
                                    <p>N/A</p>
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    @if($participant->self_description_other)
                        <li class="pl-4 text-sm text-gray-600">({{ $participant->self_description_other }})</li>
                    @endif
                    <li><span class="font-semibold">Smokes:</span> {{ isset($participant->smokes) ? ($participant->smokes ? 'Yes' : 'No') : 'N/A' }}</li>
                    <li><span class="font-semibold">Deal Breakers:</span> <span class="block whitespace-pre-wrap mt-1">{{ $participant->deal_breakers_housemates ?? 'N/A' }}</span></li>
                    <li><span class="font-semibold">Cultural/Religious Practices:</span> <span class="block whitespace-pre-wrap mt-1">{{ $participant->cultural_religious_practices ?? 'N/A' }}</span></li>
                    <li><span class="font-semibold">Interests/Hobbies:</span> <span class="block whitespace-pre-wrap mt-1">{{ $participant->interests_hobbies ?? 'N/A' }}</span></li>
                </ul>
            </div>

            <div class="bg-red-50 p-6 rounded-xl border border-red-100 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-red-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.003 12.003 0 002.92 12c0 3.072 1.054 5.856 2.77 8.253A13.93 13.93 0 0012 21c4.636 0 8.529-2.843 10.489-6.814A12.003 12.003 0 0021.08 12c0-2.493-.678-4.819-1.862-6.816z"></path></svg>Health Information</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">Medical Conditions:</span> <span class="block whitespace-pre-wrap mt-1">{{ $participant->medical_conditions_relevant ?? 'N/A' }}</span></li>
                    <li><span class="font-semibold">Medication Admin. Help:</span> {{ $participant->medication_administration_help ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Assistive Tech. / Mobility Aids:</span> {{ isset($participant->uses_assistive_technology_mobility_aids) ? ($participant->uses_assistive_technology_mobility_aids ? 'Yes' : 'No') : 'N/A' }}</li>
                    @if($participant->assistive_technology_mobility_aids_list)
                        <li class="pl-4 text-sm text-gray-600">List: <span class="block whitespace-pre-wrap mt-1">{{ $participant->assistive_technology_mobility_aids_list }}</span></li>
                    @endif
                </ul>
            </div>

            <div class="bg-gray-100 p-6 rounded-xl border border-gray-200 shadow-sm transition-transform duration-300 hover:shadow-md">
                <h3 class="font-extrabold text-gray-800 mb-3 text-xl flex items-center"><svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Other Information</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><span class="font-semibold">Aboriginal / Torres Strait Islander:</span> {{ $participant->aboriginal_torres_strait_islander ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Languages Spoken:</span>
                        @if(!empty($participant->languages_spoken))
                            <div class="flex flex-wrap gap-2 mt-1">
                                {{-- FIX: Removed json_decode, using the already casted array --}}
                                @if ($participant->languages_spoken && is_array($participant->languages_spoken))
                                    @foreach($participant->languages_spoken as $lang)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-300 text-gray-800 shadow-sm">{{ $lang }}</span>
                                    @endforeach
                                @else
                                    <p>N/A</p>
                                @endif
                            </div>
                        @else
                            N/A
                        @endif
                    </li>
                    <li><span class="font-semibold">Added By:</span> {{ $participant->addedByUser->name ?? 'N/A' }}</li>
                    <li><span class="font-semibold">Last Updated:</span> {{ $participant->updated_at ? \Carbon\Carbon::parse($participant->updated_at)->format('F j, Y H:i A') : 'N/A' }}</li>
                </ul>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('provider.participants.edit', $participant) }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md w-full sm:w-auto transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Participant
            </a>
            <a href="{{ route('provider.participants.list') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md w-full sm:w-auto transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Participants
            </a>
        </div>
    </div>
@endsection