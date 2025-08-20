@extends('supcoor.participants.create')

@section('page_title', 'Compatibility & Personality')
@section('page_description', 'Details regarding compatibility and personality for the participant.')

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
    <form action="{{ route('sc.participants.profile.compatibility-personality.update', $participant->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="space-y-6">
            {{-- How would you describe yourself? --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">How would you describe yourself?</label>
                @php
                    $selfDescriptionOptions = [
                        'Quiet',
                        'Social',
                        'Routine-focused',
                        'Independent',
                        'Likes group activities',
                        'Needs help building friendships',
                        'Enjoys hobbies or creative outlets',
                        'Other'
                    ];
                    $currentSelfDescription = old('self_description', $participant->self_description ?? []);
                    if (is_string($currentSelfDescription)) {
                        $currentSelfDescription = json_decode($currentSelfDescription, true) ?? [];
                    }
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="self_description_checkboxes_container">
                    @foreach ($selfDescriptionOptions as $option)
                        <div class="flex items-center">
                            <input type="checkbox" name="self_description[]" value="{{ $option }}" id="self_description_{{ Str::slug($option) }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                {{ in_array($option, $currentSelfDescription) ? 'checked' : '' }}>
                            <label for="self_description_{{ Str::slug($option) }}" class="ml-2 text-sm text-gray-700">{{ $option }}</label>
                        </div>
                    @endforeach
                </div>
                @error('self_description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="self_description_other_container" class="mt-4 {{ (!in_array('Other', $currentSelfDescription)) ? 'hidden' : '' }}">
                <label for="self_description_other" class="block text-sm font-medium text-gray-700">Other Self-Description (please specify):</label>
                <textarea name="self_description_other" id="self_description_other" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Prefers quiet housemates">{{ old('self_description_other', $participant->self_description_other ?? '') }}</textarea>
                @error('self_description_other')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Do you smoke? --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Do you smoke?</label>
                <div class="mt-1 flex space-x-4">
                    <div class="flex items-center">
                        <input type="radio" name="smokes" value="1" id="smokes_yes"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                            {{ (old('smokes', $participant->smokes ?? null) === 1 || old('smokes', $participant->smokes ?? null) === true) ? 'checked' : '' }}>
                        <label for="smokes_yes" class="ml-2 text-sm font-medium text-gray-700">Yes</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="smokes" value="0" id="smokes_no"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                            {{ (old('smokes', $participant->smokes ?? null) === 0 || old('smokes', $participant->smokes ?? null) === false) ? 'checked' : '' }}>
                        <label for="smokes_no" class="ml-2 text-sm font-medium text-gray-700">No</label>
                    </div>
                </div>
                @error('smokes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Any deal-breakers for housemates? --}}
            <div>
                <label for="deal_breakers_housemates" class="block text-sm font-medium text-gray-700">Any deal-breakers for housemates? (e.g. noise levels, smoking, alcohol, pets):</label>
                <textarea name="deal_breakers_housemates" id="deal_breakers_housemates" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., No excessive noise after 10 PM, no smoking indoors">{{ old('deal_breakers_housemates', $participant->deal_breakers_housemates ?? '') }}</textarea>
                @error('deal_breakers_housemates')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cultural or religious practices you'd like respected in the home: --}}
            <div>
                <label for="cultural_religious_practices" class="block text-sm font-medium text-gray-700">Cultural or religious practices you'd like respected in the home:</label>
                <textarea name="cultural_religious_practices" id="cultural_religious_practices" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Quiet prayer times, no pork in the kitchen">{{ old('cultural_religious_practices', $participant->cultural_religious_practices ?? '') }}</textarea>
                @error('cultural_religious_practices')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- What are you interests and hobbies --}}
            <div>
                <label for="interests_hobbies" class="block text-sm font-medium text-gray-700">What are your interests and hobbies? (feel free to list as many as you would like listed on your profile):</label>
                <textarea name="interests_hobbies" id="interests_hobbies" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Reading, gaming, painting, gardening, listening to music">{{ old('interests_hobbies', $participant->interests_hobbies ?? '') }}</textarea>
                @error('interests_hobbies')
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
        // Toggle 'Other' textarea visibility for Self-Description
        const selfDescriptionOtherCheckbox = document.getElementById('self_description_other');
        const selfDescriptionOtherContainer = document.getElementById('self_description_other_container');

        if (selfDescriptionOtherCheckbox) {
            selfDescriptionOtherCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    selfDescriptionOtherContainer.classList.remove('hidden');
                } else {
                    selfDescriptionOtherContainer.classList.add('hidden');
                    selfDescriptionOtherContainer.querySelector('textarea').value = ''; // Clear the textarea if hidden
                }
            });
        }
    });
</script>
@endpush
@endsection