@extends('profile.complete-participant-profile')

@section('page_title', 'NDIS & Support Needs')
@section('page_description', 'Details regarding NDIS plan and required support.')

@section('profile_content')
<div class="p-6 bg-white rounded-lg shadow-md max-w-4xl mx-auto">
    <!-- Success Message -->
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <!-- General Error Message (for non-field-specific errors) -->
        @if ($errors->any() && !session('status'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display='none';">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.847l-2.651 3.002a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-3.002a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif
    {{-- Wrap the content in a form --}}
    <form action="{{ route('indiv.profile.ndis-support-needs.update', $participant->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="space-y-6">
            <div class="space-y-6">

                <div>
                    <label for="sil_funding_status" class="block text-sm font-medium text-gray-700">Do you currently have SIL funding in your NDIS plan?</label>
                    <select name="sil_funding_status" id="sil_funding_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled selected>Select an option</option>
                        @php
                            $silFundingOptions = ['Yes', 'No', 'Not sure'];
                        @endphp
                        @foreach ($silFundingOptions as $option)
                            <option value="{{ $option }}" {{ old('sil_funding_status', $participant->sil_funding_status ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ndis_plan_review_date" class="block text-sm font-medium text-gray-700">NDIS plan review date (if known)</label>
                    <input type="text" name="ndis_plan_review_date" id="ndis_plan_review_date"
                        value="{{ old('ndis_plan_review_date', $participant->ndis_plan_review_date ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 datepicker"
                        placeholder="YYYY-MM-DD">
                </div>

                <div>
                    <label for="ndis_plan_manager" class="block text-sm font-medium text-gray-700">Who manages your NDIS plan?</label>
                    <select name="ndis_plan_manager" id="ndis_plan_manager" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled selected>Select an option</option>
                        @php
                            $planManagerOptions = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
                        @endphp
                        @foreach ($planManagerOptions as $option)
                            <option value="{{ $option }}" {{ old('ndis_plan_manager', $participant->ndis_plan_manager ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="has_support_coordinator" class="block text-sm font-medium text-gray-700">Do you have a support coordinator?</label>
                    <select name="has_support_coordinator" id="has_support_coordinator" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select an option</option>
                        {{-- Values are 1 (Yes), 0 (No), and empty string (null for 'Not sure') --}}
                        <option value="1" {{ old('has_support_coordinator', $participant->has_support_coordinator ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('has_support_coordinator', $participant->has_support_coordinator ?? '') == 0 ? 'selected' : '' }}>No</option>
                        <option value="" {{ old('has_support_coordinator', $participant->has_support_coordinator ?? '') == null ? 'selected' : '' }}>Not sure</option>
                    </select>
                </div>
            </div>

            <hr class="my-8">

            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800">Support Needs</h2>

                <div>
                    <label for="daily_living_support_needs" class="block text-sm font-medium text-gray-700">What type of support do you require for daily living?</label>
                    <select name="daily_living_support_needs[]" id="daily_living_support_needs" multiple
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @php
                            $dailyLivingNeedsOptions = [
                                'Personal care (showering, dressing)',
                                'Medication management',
                                'Meal preparation',
                                'Assistance with mobility',
                                'Overnight support',
                                'Community access',
                                'Behavior support',
                                'Household tasks (e.g. cleaning, laundry)',
                                'Other'
                            ];
                            // Ensure $currentDailyLivingNeeds is an array for in_array check
                            $currentDailyLivingNeeds = old('daily_living_support_needs', $participant->daily_living_support_needs ?? []);
                            // If it's a string (e.g., from DB without array cast), decode it
                            if (is_string($currentDailyLivingNeeds)) {
                                $currentDailyLivingNeeds = json_decode($currentDailyLivingNeeds, true) ?? [];
                            }
                        @endphp
                        @foreach ($dailyLivingNeedsOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $currentDailyLivingNeeds) ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="daily_living_support_needs_other_textarea_container" class="mt-4 {{ !in_array('Other', $currentDailyLivingNeeds) ? 'hidden' : '' }}">
                    <label for="daily_living_support_needs_other" class="block text-sm font-medium text-gray-700">Other Daily Living Support Needs (please specify):</label>
                    <textarea name="daily_living_support_needs_other" id="daily_living_support_needs_other" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., Assistance with budgeting, social activities, etc.">{{ old('daily_living_support_needs_other', $participant->daily_living_support_needs_other ?? '') }}</textarea>
                </div>

                <div>
                    <label for="primary_disability" class="block text-sm font-medium text-gray-700">Primary disability/disabilities</label>
                    <input type="text" name="primary_disability" id="primary_disability"
                        value="{{ old('primary_disability', $participant->primary_disability ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., Intellectual Disability, Autism Spectrum Disorder">
                </div>

                <div>
                    <label for="secondary_disability" class="block text-sm font-medium text-gray-700">Secondary disability/disabilities</label>
                    <input type="text" name="secondary_disability" id="secondary_disability"
                        value="{{ old('secondary_disability', $participant->secondary_disability ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., Mobility Impairment, Mental Health Condition (Optional)">
                </div>

                <div>
                    <label for="estimated_support_hours_sil_level" class="block text-sm font-medium text-gray-700">Estimated hours of support per day or SIL level funded in plan (e.g. 1:3, 1:2 etc)</label>
                    <input type="text" name="estimated_support_hours_sil_level" id="estimated_support_hours_sil_level"
                        value="{{ old('estimated_support_hours_sil_level', $participant->estimated_support_hours_sil_level ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., 1:3, 1:2, or 6 hours/day">
                </div>

                <div>
                    <label for="night_support_type" class="block text-sm font-medium text-gray-700">Do you require night support?</label>
                    <select name="night_support_type" id="night_support_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled selected>Select an option</option>
                        @php
                            $nightSupportOptions = ['Active overnight', 'Sleepover', 'None'];
                        @endphp
                        @foreach ($nightSupportOptions as $option)
                            <option value="{{ $option }}" {{ old('night_support_type', $participant->night_support_type ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="uses_assistive_technology_mobility_aids" class="block text-sm font-medium text-gray-700">Do you use assistive technology or mobility aids?</label>
                    <select name="uses_assistive_technology_mobility_aids" id="uses_assistive_technology_mobility_aids" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" {{ old('uses_assistive_technology_mobility_aids', $participant->uses_assistive_technology_mobility_aids ?? '') === '' ? 'selected' : '' }}>Select an option</option>
                        {{-- Values are 1 (Yes), 0 (No) --}}
                        <option value="1" {{ old('uses_assistive_technology_mobility_aids', $participant->uses_assistive_technology_mobility_aids ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('uses_assistive_technology_mobility_aids', $participant->uses_assistive_technology_mobility_aids ?? '') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div id="assistive_technology_list_fields" class="mt-4 {{ !($participant->uses_assistive_technology_mobility_aids ?? false) ? 'hidden' : '' }}">
                    <label for="assistive_technology_mobility_aids_list" class="block text-sm font-medium text-gray-700">If yes, list:</label>
                    <textarea name="assistive_technology_mobility_aids_list" id="assistive_technology_mobility_aids_list" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., Wheelchair, Hearing Aids, Communication Device">{{ old('assistive_technology_mobility_aids_list', $participant->assistive_technology_mobility_aids_list ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Flatpickr for the date picker
                flatpickr("#ndis_plan_review_date", {
                    dateFormat: "Y-m-d", // Matches the database format
                    allowInput: true, // Allows manual input as well
                });

                // Initialize Choices.js for daily_living_support_needs
                const dailyLivingNeedsSelect = document.getElementById('daily_living_support_needs');
                const dailyLivingNeedsChoices = new Choices(dailyLivingNeedsSelect, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Select all that apply',
                });

                const otherOptionValue = 'Other';
                const dailyLivingOtherTextareaContainer = document.getElementById('daily_living_support_needs_other_textarea_container');

                function toggleDailyLivingOtherTextarea() {
                    const selectedValues = dailyLivingNeedsChoices.getValue(true);
                    if (selectedValues.includes(otherOptionValue)) {
                        dailyLivingOtherTextareaContainer.classList.remove('hidden');
                    } else {
                        dailyLivingOtherTextareaContainer.classList.add('hidden');
                        // Clear the content if the textarea is hidden
                        dailyLivingOtherTextareaContainer.querySelector('textarea').value = '';
                    }
                }

                toggleDailyLivingOtherTextarea();
                dailyLivingNeedsSelect.addEventListener('change', toggleDailyLivingOtherTextarea);


                // Toggle for Assistive Technology List (Dropdown)
                const usesAssistiveTechSelect = document.getElementById('uses_assistive_technology_mobility_aids');
                const assistiveTechListFields = document.getElementById('assistive_technology_list_fields');

                function toggleAssistiveTechList() {
                    // Check if the selected value is '1' (which is 'Yes')
                    if (usesAssistiveTechSelect.value === '1') {
                        assistiveTechListFields.classList.remove('hidden');
                    } else {
                        assistiveTechListFields.classList.add('hidden');
                        // Clear the content if the textarea is hidden
                        assistiveTechListFields.querySelector('textarea').value = '';
                    }
                }

                toggleAssistiveTechList();
                usesAssistiveTechSelect.addEventListener('change', toggleAssistiveTechList);
            });
        </script>
    @endpush
@endsection