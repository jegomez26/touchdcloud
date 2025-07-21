{{-- resources/views/profile/view-participant-profile.blade.php --}}
@extends('indiv.indiv-db')

@section('main-content')
    <div class="max-w-2xl mx-auto p-8 bg-white rounded-xl shadow-lg mt-8 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 text-center text-indigo-700">Your Profile Details ✨</h2>
        <p class="text-gray-700 mb-8 text-center leading-relaxed">
            Here's a summary of your completed profile information.
        </p>

        <div class="space-y-6">
            {{-- Your Personal Details --}}
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Personal Information 👤</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">First Name:</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $user->first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Middle Name:</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $participant->middle_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Last Name:</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $user->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Email:</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            @if ($user->role === 'individual')
                {{-- Participant Details (Conditional for Representative) --}}
                @if ($user->is_representative)
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Represented Participant's Information 👨‍👧‍👦</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">First Name:</label>
                                <p class="mt-1 text-gray-900 font-medium">{{ $participant->first_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Middle Name:</label>
                                <p class="mt-1 text-gray-900 font-medium">{{ $participant->middle_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Last Name:</label>
                                <p class="mt-1 text-gray-900 font-medium">{{ $participant->last_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Common Participant Details --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Additional Participant Details 📝</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Birthday:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->birthday ? $participant->birthday->format('F d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Accommodation Type:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->accommodation_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Disability Type:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->disability_type ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Specific Disability Details:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->specific_disability ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Address Information 🏠</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Street Address:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->street_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Suburb:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->suburb ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">State:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->state ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Post Code:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->post_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Preferences & Contacts 📞</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Looking for Housemate:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->is_looking_hm ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Has Accommodation:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->has_accommodation ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Emergency Contact/Relative Name:</label>
                            <p class="mt-1 text-gray-900 font-medium">{{ $participant->relative_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-center pt-6">
                {{-- You might add an "Edit Profile" button here --}}
                <a href="{{ route('profile.complete.show') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
@endsection