@extends('supcoor.participants.create')

@section('page_title', 'Availability & Next Steps')
@section('page_description', 'Details regarding availability for the participant.')

@section('profile_content')
<div class="p-6 bg-white rounded-lg shadow-md max-w-4xl mx-auto">
    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    @if ($errors->any() && !session('status'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif
    <form action="{{ route('sc.participants.profile.availability.update', $participant->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="space-y-6">
            {{-- When would you like to move into new accommodation? --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">When would you like to move into new accommodation?</label>
                @php
                    $moveInOptions = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];
                    $currentMoveIn = old('move_in_availability', $participant->move_in_availability ?? '');
                @endphp
                <div class="mt-1 space-y-2">
                    @foreach ($moveInOptions as $option)
                        <div class="flex items-center">
                            <input type="radio" name="move_in_availability" value="{{ $option }}" id="move_in_{{ Str::slug($option) }}"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                {{ ($currentMoveIn === $option) ? 'checked' : '' }}>
                            <label for="move_in_{{ Str::slug($option) }}" class="ml-2 text-sm font-medium text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('move_in_availability')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current living situation --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current living situation:</label>
                @php
                    $currentLivingOptions = ['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'];
                    $currentLivingSituation = old('current_living_situation', $participant->current_living_situation ?? '');
                @endphp
                <div class="mt-1 space-y-2">
                    @foreach ($currentLivingOptions as $option)
                        <div class="flex items-center">
                            <input type="radio" name="current_living_situation" value="{{ $option }}" id="living_situation_{{ Str::slug($option) }}"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                {{ ($currentLivingSituation === $option) ? 'checked' : '' }}>
                            <label for="living_situation_{{ Str::slug($option) }}" class="ml-2 text-sm font-medium text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('current_living_situation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="current_living_situation_other_container" class="mt-4 {{ (old('current_living_situation', $participant->current_living_situation ?? '') !== 'Other') ? 'hidden' : '' }}">
                <label for="current_living_situation_other" class="block text-sm font-medium text-gray-700">Other Current Living Situation (please specify):</label>
                <textarea name="current_living_situation_other" id="current_living_situation_other" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Supported independent living, hospital">{{ old('current_living_situation_other', $participant->current_living_situation_other ?? '') }}</textarea>
                @error('current_living_situation_other')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Would you like to be contacted if a suitable match is found? --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Would the participant like to be contacted if a suitable match is found?</label>
                <div class="mt-1 flex space-x-4">
                    <div class="flex items-center">
                        <input type="radio" name="contact_for_suitable_match" value="1" id="contact_match_yes"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                            {{ (old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) === 1 || old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) === true) ? 'checked' : '' }}>
                        <label for="contact_match_yes" class="ml-2 text-sm font-medium text-gray-700">Yes</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="contact_for_suitable_match" value="0" id="contact_match_no"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                            {{ (old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) === 0 || old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) === false) ? 'checked' : '' }}>
                        <label for="contact_match_no" class="ml-2 text-sm font-medium text-gray-700">No</label>
                    </div>
                </div>
                @error('contact_for_suitable_match')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="preferred_contact_method_match_container" class="mt-4 {{ (old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) !== 1 && old('contact_for_suitable_match', $participant->contact_for_suitable_match ?? null) !== true) ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred contact method (if contact is permitted):</label>
                @php
                    $contactMethodOptions = ['Phone', 'Email', 'Via support coordinator', 'Other'];
                    $currentContactMethod = old('preferred_contact_method_match', $participant->preferred_contact_method_match ?? '');
                @endphp
                <div class="mt-1 space-y-2">
                    @foreach ($contactMethodOptions as $option)
                        <div class="flex items-center">
                            <input type="radio" name="preferred_contact_method_match" value="{{ $option }}" id="contact_method_{{ Str::slug($option) }}"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                {{ ($currentContactMethod === $option) ? 'checked' : '' }}>
                            <label for="contact_method_{{ Str::slug($option) }}" class="ml-2 text-sm font-medium text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('preferred_contact_method_match')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="preferred_contact_method_match_other_container" class="mt-4 {{ (old('preferred_contact_method_match', $participant->preferred_contact_method_match ?? '') !== 'Other') ? 'hidden' : '' }}">
                <label for="preferred_contact_method_match_other" class="block text-sm font-medium text-gray-700">Other Preferred Contact Method (please specify):</label>
                <textarea name="preferred_contact_method_match_other" id="preferred_contact_method_match_other" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Via text message, through a specific family member">{{ old('preferred_contact_method_match_other', $participant->preferred_contact_method_match_other ?? '') }}</textarea>
                @error('preferred_contact_method_match_other')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle 'Other' textarea visibility for Current Living Situation
        const currentLivingSituationRadios = document.querySelectorAll('input[name="current_living_situation"]');
        const currentLivingSituationOtherContainer = document.getElementById('current_living_situation_other_container');
        const currentLivingSituationOtherTextarea = document.getElementById('current_living_situation_other');

        function toggleCurrentLivingSituationOther() {
            const selectedValue = document.querySelector('input[name="current_living_situation"]:checked')?.value;
            if (selectedValue === 'Other') {
                currentLivingSituationOtherContainer.classList.remove('hidden');
            } else {
                currentLivingSituationOtherContainer.classList.add('hidden');
                currentLivingSituationOtherTextarea.value = ''; // Clear the textarea if hidden
            }
        }

        currentLivingSituationRadios.forEach(radio => {
            radio.addEventListener('change', toggleCurrentLivingSituationOther);
        });
        // Initial check on page load
        toggleCurrentLivingSituationOther();

        // Toggle visibility of Preferred Contact Method section based on 'Contact for Suitable Match'
        const contactMatchRadios = document.querySelectorAll('input[name="contact_for_suitable_match"]');
        const preferredContactMethodMatchContainer = document.getElementById('preferred_contact_method_match_container');
        const preferredContactMethodMatchRadios = document.querySelectorAll('input[name="preferred_contact_method_match"]');
        const preferredContactMethodMatchOtherContainer = document.getElementById('preferred_contact_method_match_other_container');
        const preferredContactMethodMatchOtherTextarea = document.getElementById('preferred_contact_method_match_other');

        function togglePreferredContactMethodSection() {
            const contactAllowed = document.getElementById('contact_match_yes').checked;
            if (contactAllowed) {
                preferredContactMethodMatchContainer.classList.remove('hidden');
            } else {
                preferredContactMethodMatchContainer.classList.add('hidden');
                // Clear selected radio and any 'Other' text when hidden
                preferredContactMethodMatchRadios.forEach(radio => radio.checked = false);
                preferredContactMethodMatchOtherContainer.classList.add('hidden');
                preferredContactMethodMatchOtherTextarea.value = '';
            }
        }

        function togglePreferredContactMethodOther() {
            const selectedValue = document.querySelector('input[name="preferred_contact_method_match"]:checked')?.value;
            if (selectedValue === 'Other') {
                preferredContactMethodMatchOtherContainer.classList.remove('hidden');
            } else {
                preferredContactMethodMatchOtherContainer.classList.add('hidden');
                preferredContactMethodMatchOtherTextarea.value = ''; // Clear the textarea if hidden
            }
        }

        contactMatchRadios.forEach(radio => {
            radio.addEventListener('change', togglePreferredContactMethodSection);
        });
        preferredContactMethodMatchRadios.forEach(radio => {
            radio.addEventListener('change', togglePreferredContactMethodOther);
        });

        // Initial checks on page load
        togglePreferredContactMethodSection();
        togglePreferredContactMethodOther(); // Ensure 'Other' for contact method is also correct on load
    });
</script>
@endpush
@endsection