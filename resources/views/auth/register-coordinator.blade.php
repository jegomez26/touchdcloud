@extends('layouts.app') {{-- or layouts.guest if you made one --}}

@section('content')
<div class="flex flex-col items-center pt-6 sm:pt-0 min-h-screen">
    <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-custom-white shadow-xl overflow-hidden sm:rounded-lg border border-custom-light-grey-green relative">
        <h2 class="text-3xl font-extrabold text-custom-black text-center mb-8">Register as a Support Coordinator</h2>

        <a href="{{ route('home') }}?showRegisterModal=true"
           class="absolute top-3 right-3 text-custom-light-grey-brown hover:text-custom-black text-3xl font-bold"
           title="Back to role selection">
            &times;
        </a>

        <p class="text-center text-lg text-custom-dark-olive">
            This is the registration form for Support Coordinators.
            <br>Content will be added here soon!
        </p>

        <div class="flex items-center justify-center mt-8">
            <a class="underline text-sm text-custom-dark-teal hover:text-custom-ochre rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-ochre" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>
    </div>
</div>
@endsection