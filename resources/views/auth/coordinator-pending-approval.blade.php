<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Account Pending Approval</h2>
            <p class="text-gray-600 mb-6">
                Thank you for registering as a Support Coordinator.
                Your account is currently under review by our administrators.
                You will receive an email notification once your account has been approved.
            </p>
            <p class="text-sm text-gray-500">
                You can try logging in later to check your status.
            </p>
            <div class="mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-primary-button>
                        {{ __('Log Out') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>