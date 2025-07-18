{{-- This file contains ONLY the login form, to be included in the modal --}}

<x-auth-session-status class="mb-6 text-center text-sm" :status="session('status')" />

<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    <div>
        <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700 mb-1" />
        <x-text-input id="email"
                      class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base bg-white text-gray-900 placeholder-gray-400" {{-- ADDED bg-white, text-gray-900, placeholder-gray-400 --}}
                      type="email"
                      name="email"
                      :value="old('email')"
                      required
                      autofocus
                      autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700 mb-1" />

        <x-text-input id="password"
                      class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base bg-white text-gray-900 placeholder-gray-400" {{-- ADDED bg-white, text-gray-900, placeholder-gray-400 --}}
                      type="password"
                      name="password"
                      required
                      autocomplete="current-password" />

        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
    </div>

    <div class="flex items-center justify-between mt-4">
        <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ms-2">{{ __('Remember me') }}</span>
        </label>

        @if (Route::has('password.request'))
            <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium underline transition duration-150 ease-in-out" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        @endif
    </div>

    <div class="flex items-center justify-end">
        <x-primary-button class="w-full justify-center py-2 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-semibold text-base transition ease-in-out duration-150">
            {{ __('Log in') }}
        </x-primary-button>
    </div>
</form>