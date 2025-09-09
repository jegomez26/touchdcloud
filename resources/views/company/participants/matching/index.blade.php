@extends('company.provider-db')

@section('main-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#3e4732]">Match Participants</h1>
            <p class="text-[#bcbabb] mt-2">Find compatible housemates for your participants</p>
        </div>
    </div>

    <!-- Participants Grid -->
    @if($participants->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($participants as $participant)
                <div class="bg-white rounded-lg shadow-md p-6 border border-[#e1e7dd] hover:shadow-lg transition-shadow duration-200">
                    <!-- Participant Avatar -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-[#33595a] rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($participant->first_name, 0, 1)) }}{{ strtoupper(substr($participant->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[#3e4732]">
                                {{ $participant->first_name }} {{ $participant->last_name }}
                            </h3>
                            <p class="text-sm text-[#bcbabb]">
                                {{ $participant->participant_code_name ?? 'No code assigned' }}
                            </p>
                        </div>
                    </div>

                    <!-- Participant Details -->
                    <div class="space-y-3 mb-4">
                        @if($participant->age)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-3 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-medium text-[#3e4732]">{{ $participant->age }} years old</span>
                            </div>
                        @endif

                        @if($participant->primary_disability)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-3 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium text-[#3e4732]">{{ $participant->primary_disability }}</span>
                            </div>
                        @endif

                        @if($participant->state)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-3 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-medium text-[#3e4732]">{{ $participant->suburb }}, {{ $participant->state }}</span>
                            </div>
                        @endif

                        @if($participant->estimated_support_hours_sil_level)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-3 text-[#33595a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-[#3e4732]">{{ $participant->estimated_support_hours_sil_level }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div class="pt-4 border-t border-[#e1e7dd]">
                        <a href="{{ route('provider.participants.matching.show', $participant) }}" 
                           class="w-full bg-[#33595a] text-white px-4 py-2 rounded-md hover:bg-[#2C494A] transition-colors duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Find Matches</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $participants->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-[#e1e7dd] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-[#bcbabb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-[#3e4732] mb-2">No Participants Found</h3>
            <p class="text-[#bcbabb] mb-6">You haven't added any participants yet. Add some participants to start matching them.</p>
            <a href="{{ route('provider.participants.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#33595a] text-white rounded-md hover:bg-[#2C494A] transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Participant
            </a>
        </div>
    @endif
</div>
@endsection
