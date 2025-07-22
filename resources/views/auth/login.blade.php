@section('title', 'Login - ' . config('app.name', 'TouchdCloud'))

{{-- The x-guest-layout component provides the full page background and centering --}}
<x-guest-layout>
    {{-- The login card container:
         - w-full ensures it uses available width up to its max-width.
         - max-w-md constrains its maximum width to Tailwind's 'medium' breakpoint (28rem or 448px by default).
         - The other classes apply background, rounded corners, shadow, and border. --}}
    <div class="w-full max-w-md bg-custom-white rounded-lg shadow-xl p-8 sm:p-10 border border-custom-light-grey-green">

        <div class="flex flex-col items-center justify-center mb-8">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-24 w-auto mb-4">
            </a>
            <h2 class="text-3xl font-extrabold text-custom-dark-teal text-center">
                Welcome Back!
            </h2>
            <p class="mt-2 text-custom-dark-olive text-center">
                Log in to access your account.
            </p>
        </div>

        <x-auth-session-status class="mb-6 text-center text-sm text-custom-ochre" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                <x-text-input id="email"
                                class="block w-full px-4 py-2 border border-custom-light-grey-green rounded-md shadow-sm
                                        focus:ring-custom-ochre focus:border-custom-ochre
                                        text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-custom-dark-teal mb-1" />
                <x-text-input id="password"
                                class="block w-full px-4 py-2 border border-custom-light-grey-green rounded-md shadow-sm
                                        focus:ring-custom-ochre focus:border-custom-ochre
                                        text-base bg-custom-white text-custom-dark-teal placeholder-custom-light-grey-brown"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-custom-ochre text-sm" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="inline-flex items-center text-sm text-custom-dark-olive">
                    <input id="remember_me" type="checkbox"
                            class="rounded border-custom-light-grey-brown text-custom-ochre shadow-sm focus:ring-custom-ochre"
                            name="remember">
                    <span class="ms-2">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-custom-dark-teal hover:text-custom-ochre font-medium underline transition duration-150 ease-in-out" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button class="w-full justify-center py-2 px-4 rounded-md text-white
                                            bg-custom-ochre hover:bg-custom-ochre-darker
                                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                            font-semibold text-base transition ease-in-out duration-150">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>

            {{-- Link to Register if not already logged in --}}
            @if (!Auth::check())
            <div class="text-center mt-6">
                <p class="text-custom-dark-olive text-sm">
                    Don't have an account?
                    {{-- Changed href to home route with a query parameter --}}
                    <a href="{{ route('home', ['showRegisterModal' => true]) }}" class="font-medium text-custom-dark-teal hover:text-custom-ochre underline">
                        Register here
                    </a>
                </p>
            </div>
            @endif
        </form>
    </div>
</x-guest-layout>