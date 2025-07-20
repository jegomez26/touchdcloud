<form method="POST" action="{{ route('register') }}">
    @csrf

    {{-- Hidden input for role, as this form is specifically for 'participant' --}}
    <input type="hidden" name="role" value="participant">

    {{-- Alpine data for this specific form to manage internal state --}}
    <div x-data="{ registrationType: 'participant' }">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Who are you registering?</label>
            <div class="mt-1 flex space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="registration_type" value="participant" class="form-radio" x-model="registrationType">
                    <span class="ml-2">I am the Participant</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="registration_type" value="representative" class="form-radio" x-model="registrationType">
                    <span class="ml-2">I am registering for a Participant</span>
                </label>
            </div>
        </div>

        {{-- Representative Fields - controlled by Alpine's x-show --}}
        <div x-show="registrationType === 'representative'" class="space-y-4"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <h3 class="text-lg font-semibold mt-6 mb-4">Your Details (Representative)</h3>
            <div>
                <label for="representative_first_name" class="block text-sm font-medium text-gray-700">Your First Name</label>
                {{-- Only add required if the field is visible/active --}}
                <input type="text" name="representative_first_name" id="representative_first_name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                       x-bind:required="registrationType === 'representative'">
                @error('representative_first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="representative_last_name" class="block text-sm font-medium text-gray-700">Your Last Name</label>
                <input type="text" name="representative_last_name" id="representative_last_name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                       x-bind:required="registrationType === 'representative'">
                @error('representative_last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="relationship_to_participant" class="block text-sm font-medium text-gray-700">Relationship to Participant</label>
                <select name="relationship_to_participant" id="relationship_to_participant"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        x-bind:required="registrationType === 'representative'">
                    <option value="">Select Relationship</option>
                    <option value="Parent">Parent</option>
                    <option value="Guardian">Guardian</option>
                    <option value="Support Coordinator">Support Coordinator</option>
                    <option value="Family Member">Family Member</option>
                    <option value="Other">Other</option>
                </select>
                @error('relationship_to_participant') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <h3 class="text-lg font-semibold mt-6 mb-4">Participant's Details</h3>
        </div>

        {{-- Participant/User Details --}}
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">
                <span x-text="registrationType === 'representative' ? 'Participant\'s First Name' : 'Your First Name'"></span>
            </label>
            <input type="text" name="first_name" id="first_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">
                <span x-text="registrationType === 'representative' ? 'Participant\'s Last Name' : 'Your Last Name'"></span>
            </label>
            <input type="text" name="last_name" id="last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required autocomplete="new-password">
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required autocomplete="new-password">
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Register') }}
            </button>
        </div>
    </div> {{-- End of x-data scope for this form --}}
</form>