@extends('layouts.app')

@section('content')
    {{-- This ensures the layout is consistent even if accessed directly --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Login</h2>
            @include('auth.partials.login-form')
        </div>
    </div>
@endsection