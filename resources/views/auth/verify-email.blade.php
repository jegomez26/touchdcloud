@section('title', 'Verify Email - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    {{-- The verification card container --}}
    <div class="w-full max-w-md bg-custom-white rounded-lg shadow-xl p-8 sm:p-10 border border-custom-light-grey-green text-center relative">

        <div class="flex flex-col items-center justify-center mb-6">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 w-auto mb-4">
            </a>
            <h2 class="text-2xl font-extrabold text-custom-dark-teal">
                Verify Your Email Address
            </h2>
        </div>

        <div class="mb-6 text-sm text-custom-dark-olive leading-relaxed">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
            <br>
            {{ __('If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 font-medium text-sm text-green-600 p-3 bg-green-50 rounded-md border border-green-200">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-6 space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="w-full justify-center py-2 px-4 rounded-md text-white
                                         bg-custom-ochre hover:bg-custom-ochre-darker
                                         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                         font-semibold text-base transition ease-in-out duration-150">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full justify-center py-2 px-4 rounded-md
                                               text-sm text-custom-dark-teal hover:text-custom-ochre underline
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre
                                               font-medium transition duration-150 ease-in-out">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>