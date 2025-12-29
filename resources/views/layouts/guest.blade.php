<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/blue_logo.png') }}">

    <title>@yield('title', config('app.name', 'SIL Match'))</title> {{-- Added @yield('title') --}}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Ensure Alpine.js is included here (typically via app.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    {{-- This div is the outer container from your login form --}}
    <div class="min-h-screen flex items-center justify-center bg-custom-light-cream p-4 sm:p-6">
        {{ $slot }} {{-- This is where your page content will be injected --}}
    </div>
    {{-- Scripts pushed from child views will appear here --}}
    @stack('scripts')
</body>
</html>