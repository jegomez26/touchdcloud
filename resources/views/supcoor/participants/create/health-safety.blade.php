@extends('supcoor.participants.create')

@section('page_title', 'Health and Safety')
@section('page_description', 'Details regarding health and safety for the participant.')

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
    <form action="{{ route('sc.participants.profile.health-safety.update', $participant->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="space-y-6">
            <div>
                <label for="medical_conditions_relevant" class="block text-sm font-medium text-gray-700">Medical conditions relevant to support providers:</label>
                <textarea name="medical_conditions_relevant" id="medical_conditions_relevant" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Epilepsy, Diabetes, Allergies, etc.">{{ old('medical_conditions_relevant', $participant->medical_conditions_relevant ?? '') }}</textarea>
                @error('medical_conditions_relevant')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="medication_administration_help" class="block text-sm font-medium text-gray-700">Does the participant need help with medication administration?</label>
                <select name="medication_administration_help" id="medication_administration_help" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" {{ old('medication_administration_help', $participant->medication_administration_help ?? '') === '' ? 'selected' : '' }}>Select an option</option>
                    @php
                        $medicationHelpOptions = ['Yes', 'No', 'Sometimes'];
                    @endphp
                    @foreach ($medicationHelpOptions as $option)
                        <option value="{{ $option }}" {{ old('medication_administration_help', $participant->medication_administration_help ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('medication_administration_help')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="behaviour_support_plan_status" class="block text-sm font-medium text-gray-700">Does the participant have a behaviour support plan?</label>
                <select name="behaviour_support_plan_status" id="behaviour_support_plan_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled selected>Select an option</option>
                    @php
                        $behaviourPlanOptions = ['Yes', 'No', 'In development'];
                    @endphp
                    @foreach ($behaviourPlanOptions as $option)
                        <option value="{{ $option }}" {{ old('behaviour_support_plan_status', $participant->behaviour_support_plan_status ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('behaviour_support_plan_status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Conditional field for 'behaviours_of_concern_housemates' --}}
            <div id="behaviours_of_concern_container" class="mt-4 {{ (old('behaviour_support_plan_status', $participant->behaviour_support_plan_status ?? '') !== 'Yes') ? 'hidden' : '' }}">
                <label for="behaviours_of_concern_housemates" class="block text-sm font-medium text-gray-700">If yes, are there any behaviours of concern potential housemates should know about?</label>
                <textarea name="behaviours_of_concern_housemates" id="behaviours_of_concern_housemates" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Please describe any relevant behaviours.">{{ old('behaviours_of_concern_housemates', $participant->behaviours_of_concern_housemates ?? '') }}</textarea>
                @error('behaviours_of_concern_housemates')
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
    document.addEventListener('DOMContentLoaded', function() {
        const behaviourSupportPlanSelect = document.getElementById('behaviour_support_plan_status');
        const behavioursOfConcernContainer = document.getElementById('behaviours_of_concern_container');
        const behavioursOfConcernTextarea = document.getElementById('behaviours_of_concern_housemates');

        function toggleBehavioursOfConcern() {
            if (behaviourSupportPlanSelect.value === 'Yes') {
                behavioursOfConcernContainer.classList.remove('hidden');
            } else {
                behavioursOfConcernContainer.classList.add('hidden');
                // Clear the content if the textarea is hidden
                behavioursOfConcernTextarea.value = '';
            }
        }

        // Set initial state
        toggleBehavioursOfConcern();

        // Add event listener for changes
        behaviourSupportPlanSelect.addEventListener('change', toggleBehavioursOfConcern);
    });
</script>
@endpush

@endsection