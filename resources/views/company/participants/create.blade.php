@extends('company.provider-db') 


@section('main-content') {{-- Start the main-content section --}}
<div class="min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8 flex-col md:flex-row">
            <div class="w-full md:w-80 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-4 border-b">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Participant Profile {{ $participant->first_name ?? '' }} {{ $participant->last_name ?? '' }}</h2> {{-- Display participant name --}}

                            {{-- Profile Completion Progress --}}
                            @isset($profileCompletionPercentage)
                                <span class="text-sm font-medium text-blue-600">
                                    Profile Completion: {{ $profileCompletionPercentage }}%
                                </span>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-2">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $profileCompletionPercentage }}%"></div>
                                </div>
                            @endisset
                        </div>
                    </div>
                    <nav class="p-2" id="provider-participant-nav">
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.basic-details', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.basic-details' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.basic-details' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Basic Details</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.basic-details')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border bg-blue-50 text-blue-700 border-blue-200 cursor-default">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 text-blue-600">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">Basic Details</span>
                                    </div>
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.ndis-support-needs', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.ndis-support-needs' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.ndis-support-needs' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">NDIS Details and Support Needs</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.ndis-support-needs')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border text-gray-400 border-transparent cursor-not-allowed opacity-50">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">NDIS Details and Support Needs</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.health-safety', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.health-safety' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.health-safety' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Health and Safety</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.health-safety')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border text-gray-400 border-transparent cursor-not-allowed opacity-50">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Health and Safety</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.living-preferences', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.living-preferences' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.living-preferences' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Living Preferences</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.living-preferences')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border text-gray-400 border-transparent cursor-not-allowed opacity-50">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Living Preferences</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.compatibility-personality', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.compatibility-personality' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.compatibility-personality' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                    <span class="font-medium">Compatibility and Personality</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.compatibility-personality')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border text-gray-400 border-transparent cursor-not-allowed opacity-50">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                    <span class="font-medium">Compatibility and Personality</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($participant->id)
                            <a href="{{ route('provider.participants.profile.availability', $participant->id) }}"
                               class="block w-full text-left px-4 py-3 mb-1 rounded-lg border transition-colors duration-200 group no-underline {{ request()->route()->getName() === 'provider.participants.profile.availability' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5 {{ request()->route()->getName() === 'provider.participants.profile.availability' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Availability</span>
                                </div>
                                @if(request()->route()->getName() === 'provider.participants.profile.availability')
                                    <span class="text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                        @else
                            <div class="block w-full text-left px-4 py-3 mb-1 rounded-lg border text-gray-400 border-transparent cursor-not-allowed opacity-50">
                                <div class="flex items-center space-x-3">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="flex-shrink-0 w-5 h-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Availability</span>
                                </div>
                            </div>
                        @endif
                    </nav>
                </div>
            </div>

            <div class="flex-1">
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">@yield('page_title')</h2>
                        <p class="text-gray-600 text-sm mt-1">@yield('page_description')</p>
                    </div>

                    <div class="p-6">
                        @yield('profile_content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection