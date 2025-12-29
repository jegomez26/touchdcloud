@extends('company.provider-db')

@section('main-content')
<div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8">
    
    
    <!-- Profile Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Provider Profile</h1>
        <p class="text-gray-600 mt-2">Manage your provider profile information and branding.</p>
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Profile Form -->
    <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data" x-data="profileData">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Profile Picture & Logo -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile & Logo</h3>
                    
                    <!-- Current Logo Display -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <div class="w-32 h-32 mx-auto bg-gray-100 rounded-lg overflow-hidden border-4 border-gray-200">
                                <!-- Dynamic logo display -->
                                <img x-show="logoPreview" :src="logoPreview" alt="Provider Logo" class="w-full h-full object-cover">
                                <img x-show="!logoPreview && currentLogo" :src="currentLogo" alt="Provider Logo" class="w-full h-full object-cover">
                                <div x-show="!logoPreview && !currentLogo" class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" @click.stop="openFileDialog()" class="absolute -bottom-2 -right-2 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Click to upload logo</p>
                        <p class="text-xs text-gray-500">Recommended: 300x300px, PNG/JPG</p>
                    </div>

                    <!-- Logo Upload Input -->
                    <input type="file" x-ref="logoInput" name="logo" accept="image/*" class="hidden" @change="handleLogoUpload($event)">

                    <!-- Logo Upload Status -->
                    <div x-show="logoUploading" class="text-center">
                        <div class="inline-flex items-center text-blue-600">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Provider Information</h3>
                    
                    <div class="space-y-6">
                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                            <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $provider->business_name ?? $provider->organisation_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter your business name" required>
                            @error('business_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                            <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $provider->contact_person ?? $provider->main_contact_name ?? $user->first_name . ' ' . $user->last_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter contact person name" required>
                            @error('contact_person')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $provider->email ?? $provider->email_address ?? $user->email) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter email address" required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $provider->phone ?? $provider->phone_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter phone number" required>
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                      placeholder="Enter full address" required>{{ old('address', $provider->address ?? $provider->office_address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $provider->city ?? $provider->office_suburb) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter city" required>
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                            <select id="state" name="state" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select State</option>
                                <option value="NSW" {{ old('state', $provider->state ?? $provider->office_state) == 'NSW' ? 'selected' : '' }}>New South Wales</option>
                                <option value="VIC" {{ old('state', $provider->state ?? $provider->office_state) == 'VIC' ? 'selected' : '' }}>Victoria</option>
                                <option value="QLD" {{ old('state', $provider->state ?? $provider->office_state) == 'QLD' ? 'selected' : '' }}>Queensland</option>
                                <option value="WA" {{ old('state', $provider->state ?? $provider->office_state) == 'WA' ? 'selected' : '' }}>Western Australia</option>
                                <option value="SA" {{ old('state', $provider->state ?? $provider->office_state) == 'SA' ? 'selected' : '' }}>South Australia</option>
                                <option value="TAS" {{ old('state', $provider->state ?? $provider->office_state) == 'TAS' ? 'selected' : '' }}>Tasmania</option>
                                <option value="ACT" {{ old('state', $provider->state ?? $provider->office_state) == 'ACT' ? 'selected' : '' }}>Australian Capital Territory</option>
                                <option value="NT" {{ old('state', $provider->state ?? $provider->office_state) == 'NT' ? 'selected' : '' }}>Northern Territory</option>
                            </select>
                            @error('state')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $provider->postal_code ?? $provider->office_post_code) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Enter postal code" required>
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website (Optional)</label>
                            <input type="url" id="website" name="website" value="{{ old('website', $provider->website) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="https://example.com">
                            @error('website')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                      placeholder="Describe your business and services">{{ old('description', $provider->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 pt-6 border-t border-gray-200 mt-8">
                        <a href="{{ route('provider.dashboard') }}" 
                           class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-400 transition-colors text-center">
                            Cancel
                        </a>
                        <button type="submit" :disabled="logoUploading"
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!logoUploading">Update Profile</span>
                            <span x-show="logoUploading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileData', () => ({
        logoPreview: null,
        logoUploading: false,
        currentLogo: @json($provider->logo ? asset('storage/' . $provider->logo) : null),
        
        openFileDialog() {
            this.$refs.logoInput.click();
        },
        
        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    this.$refs.logoInput.value = ''; // Clear the input
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB.');
                    this.$refs.logoInput.value = ''; // Clear the input
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }));
});
</script>
@endsection
