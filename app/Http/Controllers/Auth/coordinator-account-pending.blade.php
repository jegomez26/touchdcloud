@section('title', 'Account Pending Approval - ' . config('app.name', 'SIL Match'))

<x-guest-layout>
    <div class="w-full max-w-md bg-custom-white rounded-lg shadow-xl p-8 sm:p-10 border border-custom-light-grey-green text-center">
        <div class="flex flex-col items-center justify-center mb-6">
            <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-20 w-auto mb-4">
            <h2 class="text-2xl font-extrabold text-custom-dark-teal">
                Account Pending Approval
            </h2>
        </div>

        <p class="mt-4 text-custom-dark-olive leading-relaxed">
            Thank you for registering as a Support Coordinator with SIL Match!
        </p>
        <p class="mt-2 text-custom-dark-olive leading-relaxed">
            Your account has been successfully created, and a verification email has been sent to your inbox. Please verify your email address.
        </p>
        <p class="mt-2 text-custom-dark-olive leading-relaxed font-semibold">
            Once your email is verified, our administration team will review your application. You will receive another notification once your account has been activated.
        </p>

        <div class="mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-md shadow-sm text-white bg-custom-ochre hover:bg-custom-ochre-darker focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre transition ease-in-out duration-150">
                Go to Login Page
            </a>
        </div>
    </div>
</x-guest-layout>