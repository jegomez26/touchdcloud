{{-- resources/views/company/provider-db.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIL Match - Provider Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #F9FAFB;
            color: #374151;
        }
        /* Custom styles for active sidebar link */
        .sidebar-link.active {
            background-color: #2C494A; /* primary-dark */
            color: #ffffff; /* custom-white */
            font-weight: 600;
            
            /* New: Extend active color to the right edge */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            padding-right: 1.5rem; /* Remove right padding for active state */
            /* Crucial change: Negative right margin to fill the parent's padding */
            margin-right: 0; 
        }
        .sidebar-link {
            transition: all 0.25s ease-in-out;
            padding-left: 2rem; /* Consistent padding */
            padding-right: 1.5rem;
            border-radius: 0.75rem; /* More rounded */
            display: flex; /* Make it a flex container to align icon and text */
            align-items: center;
            position: relative; /* For potential future hover effects like underlines */
            overflow: hidden;
        }
        .sidebar-link:not(.active):hover {
            background-color: #E5E7EB; /* border-light */
            color: #374151; /* text-dark */
            transform: translateY(-1px); /* Subtle lift on hover */
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
        }

        .sidebar-link svg {
            color: inherit;
            transition: color 0.25s ease-in-out, transform 0.25s ease-in-out; /* Add transform transition */
        }

        .sidebar-link:hover svg {
            transform: translateX(3px); /* Subtle icon slide on hover */
        }

        /* Adjust scrollbar for main content */
        main.overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }
        main.overflow-y-auto::-webkit-scrollbar-track {
            background: #e1e7dd; /* Matching light green-gray */
            border-radius: 10px;
        }
        main.overflow-y-auto::-webkit-scrollbar-thumb {
            background: #bcbabb; /* Matching gray */
            border-radius: 10px;
        }
        main.overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #cc8e45; /* Matching accent brown */
        }

        /* Chat bubble styles (existing) */
        .chat-bubble {
            max-width: 75%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            word-wrap: break-word;
        }
        .chat-bubble.incoming {
            background-color: #e1e7dd; /* green-gray */
            align-self: flex-start;
            border-bottom-left-radius: 0.25rem; /* Smaller radius for corner */
        }
        .chat-bubble.outgoing {
            background-color: #33595a; /* dark teal */
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0.25rem; /* Smaller radius for corner */
        }
        .chat-message-time {
            font-size: 0.75rem; /* text-xs */
            color: #bcbabb; /* gray-500 equivalent */
            margin-top: 0.25rem;
        }
        .chat-message-sender {
            font-size: 0.875rem; /* text-sm */
            font-weight: 500; /* font-medium */
            margin-bottom: 0.25rem;
        }
        .chat-bubble.outgoing .chat-message-sender,
        .chat-bubble.outgoing .chat-message-time {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Custom scrollbar for sidebar nav in mobile if it needs to scroll */
        #sidebar-nav-container::-webkit-scrollbar {
            width: 8px;
        }
        #sidebar-nav-container::-webkit-scrollbar-track {
            background: #f2f7ed; /* Lighter green-gray for sidebar track */
            border-radius: 10px;
        }
        #sidebar-nav-container::-webkit-scrollbar-thumb {
            background: #d8d8d8; /* Slightly darker gray for sidebar thumb */
            border-radius: 10px;
        }
        #sidebar-nav-container::-webkit-scrollbar-thumb:hover {
            background: #bcbabb; /* Matching gray on hover */
        }

        /* Calendar container */
        .flatpickr-calendar {
            background-color: #ffffff; /* White background */
            border-radius: 0.75rem; /* Rounded corners (matches your input fields) */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Tailwind shadow-lg */
            border: 1px solid #e1e7dd; /* Subtle border */
            font-family: 'Inter', sans-serif; /* Use your main font */
            color: #3e4732; /* Darker green-gray for text */
            padding: 1rem; /* Add some padding around the calendar */
        }

        /* Header (month/year) */
        .flatpickr-months .flatpickr-month {
            color: #33595a; /* Dark teal for month/year text */
            font-weight: 600; /* Semi-bold */
        }

        /* Arrows for month/year navigation */
        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: #3e4732; /* Darker green-gray for arrows */
            fill: #3e4732; /* Ensure SVG fills are also colored */
            opacity: 0.8; /* Slightly less opaque */
            transition: opacity 0.2s ease-in-out;
        }
        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            opacity: 1;
            color: #cc8e45; /* Accent brown on hover */
            fill: #cc8e45;
        }

        /* Weekdays row (Mon, Tue, Wed...) */
        .flatpickr-weekdays {
            margin-top: 0.5rem;
        }
        .flatpickr-weekday {
            color: #bcbabb; /* Light gray for weekdays */
            font-weight: 500; /* Medium weight */
            font-size: 0.875rem; /* text-sm */
        }

        /* Day cells (numbers) */
        .flatpickr-day {
            color: #3e4732; /* Default day color */
            font-size: 0.95rem; /* Slightly larger for readability */
            font-weight: 500;
            line-height: 2.25rem; /* Adjust height for consistent circles/squares */
            height: 2.25rem;
            width: 2.25rem;
            margin: 0.125rem; /* Small gap between days */
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-radius 0.2s ease-in-out;
        }

        /* Hover state for days */
        .flatpickr-day:hover {
            background-color: #f2f7ed; /* Lighter green-gray on hover */
            border-radius: 0.5rem; /* Slightly rounded on hover */
        }

        /* Selected day */
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange, /* For range selection start */
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange, /* For range selection end */
        .flatpickr-day.endRange:hover {
            background-color: #33595a; /* Dark teal for selected date */
            color: #ffffff; /* White text for selected date */
            border-color: #33595a; /* Match background */
            border-radius: 0.5rem; /* Rounded for selected */
        }

        /* Today's date */
        .flatpickr-day.today {
            border-color: #cc8e45; /* Accent brown border for today */
            color: #cc8e45; /* Accent brown text for today */
            font-weight: 600; /* Semi-bold for today */
        }
        .flatpickr-day.today:hover {
            background-color: #fcefdc; /* Lighter accent brown on hover for today */
        }
        .flatpickr-day.today.selected {
            background-color: #33595a; /* Dark teal if today is selected */
            color: #ffffff;
            border-color: #33595a;
        }

        /* Disabled days (outside current month, before maxDate, etc.) */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #ccc; /* Lighter gray for disabled days */
            opacity: 0.7;
            background-color: transparent;
            cursor: not-allowed;
        }

        /* Input field styling (from your complete-participant-profile.blade.php) */
        input[type="text"].rounded-md {
            /* Ensure your input field still looks good */
            /* These styles are already present on your input field */
            /* .mt-1.block.w-full.rounded-md.border-gray-300.shadow-sm.focus:border-indigo-500.focus:ring-indigo-500.sm:text-base.p-2.5.transition.ease-in-out.duration-150 */
        }

        /* For the clear/today button, if you add one */
        .flatpickr-clear, .flatpickr-today-button {
            color: #cc8e45; /* Accent brown */
            font-weight: 600;
            transition: color 0.2s ease-in-out;
        }
        .flatpickr-clear:hover, .flatpickr-today-button:hover {
            color: #a67139; /* Darker accent brown */
        }

        /* Ensure calendar positioning is correct, especially when opened from an input */
        .flatpickr-calendar.arrow:before,
        .flatpickr-calendar.arrow:after {
            border-top-color: #e1e7dd; /* Match border */
        }
        .flatpickr-calendar.arrow.top:after {
            border-top-color: #ffffff; /* Match background */
        }

        /* Fix for cursor on datepicker icon */
        /* This applies to any input that becomes a flatpickr instance */
        .flatpickr-input[readonly] {
            cursor: pointer;
        }

        /* Adjusting for the 'Inter' font, if needed for subtle adjustments */
        .flatpickr-calendar .numInputWrapper span {
            font-family: 'Inter', sans-serif;
        }
        .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthOption {
            font-family: 'Inter', sans-serif;
        }
        .flatpickr-calendar .flatpickr-current-month .numInput {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            /* Existing styles */
            display: flex; /* Ensure the anchor tag takes full width */
            align-items: center; /* Vertically align icon and text */
            width: 100%; /* Important for full row selection */
            padding-top: 0.75rem; /* Adjust padding as needed for the entire clickable area */
            padding-bottom: 0.75rem;
            padding-left: 2rem; /* Consistent left padding */
            padding-right: 1.5rem; /* Default right padding */
        }
        .sidebar-link.active {
            padding-right: 1.5rem; /* Remove right padding for active state */
            /* Ensure the background extends by adjusting width to fill space */
            position: relative;
            z-index: 10; /* Make sure it's above other elements if necessary */
            /* Updated for the right edge fix */
            margin-right: 0; /* THIS IS THE KEY CHANGE */
            
        }
        .sidebar-link span {
            /* If you have text inside a span, ensure it doesn't break the layout */
            display: inline-block;
            vertical-align: middle;
        }

        #sidebar {
            padding-left: 1.5rem;
            padding-right: 0;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#f8f1e1] text-[#000000] flex flex-col">

    <header class="bg-[#ffffff] shadow-md p-4 flex items-center justify-between z-30 sticky top-0 w-full h-16">
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-[#3e4732]">Provider Dashboard</h1>
            <button id="mobile-menu-button" class="text-[#bcbabb] focus:outline-none p-2 rounded-md hover:bg-[#f8f1e1]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-[#33595a] hover:text-[#3e4732] transition duration-300">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }}" class="h-10 inline-block align-middle mr-3">
            </a>
            <div class="flex items-center space-x-4 relative">
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-[#bcbabb] focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[#bcbabb]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <button class="text-[#bcbabb] hover:text-[#000000] focus:outline-none p-2 rounded-md hover:bg-[#f8f1e1] transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-[#bcbabb] hover:text-[#000000] focus:outline-none p-2 rounded-md hover:bg-[#f8f1e1] transition-colors duration-200">
                        {{-- Generate initials for the avatar --}}
                        @php
                            $firstName = Auth::user()->first_name ?? '';
                            $lastName = Auth::user()->last_name ?? '';
                            $initials = '';
                            if (!empty($firstName)) {
                                $initials .= strtoupper(substr($firstName, 0, 1));
                            }
                            if (!empty($lastName)) {
                                $initials .= strtoupper(substr($lastName, 0, 1));
                            }
                            // Default to 'U' if no initials can be formed
                            if (empty($initials)) {
                                $initials = 'U';
                            }

                            // Define background and text colors for the avatar based on your scheme
                            $bgColor = '33595a'; // dark teal for background
                            $textColor = 'ffffff'; // white for text
                        @endphp
                        <img src="https://placehold.co/32x32/{{ $bgColor }}/{{ $textColor }}?text={{ $initials }}" alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-[#3e4732]">
                        {{-- Display the logged-in user's name --}}
                        <span class="font-medium text-[#3e4732] hidden sm:inline">
                            {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-[#ffffff] rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </button>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-[#bcbabb]">
                        <button data-action="logout" class="block px-4 py-2 text-sm text-[#cc8e45] hover:bg-[#f8f1e1] w-full text-left transition-colors duration-150 rounded-md" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out inline-block mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
                        </button>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main content wrapper. This will now contain both sidebar and main content --}}
    <div class="flex flex-1">
        {{-- Fixed Sidebar for Desktop --}}
        <aside id="sidebar" class="hidden md:flex flex-col fixed top-16 left-0 h-[calc(100vh-64px)] w-72 bg-[#ffffff] text-[#000000] p-6 space-y-6 border-r border-[#e1e7dd] z-20">
            <div class="flex items-center justify-center mb-8">
                {{-- No close button needed for desktop fixed sidebar --}}
            </div>
            {{-- Navigation menu: this itself should not scroll --}}
            <nav class="space-y-1 overflow-y-auto pr-2" id="sidebar-nav-container">
                <p class="text-xs font-semibold text-[#bcbabb] uppercase mb-2 px-4">Menu</p>
                <a href="{{ route('provider.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                <a href="{{ route('provider.participants.list') }}" data-section="my-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> My Participants
                </a>
                <a href="{{ route('provider.accommodations.index') }}" data-section="my-accommodations" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> My Accommodations
                </a>
                <a href="{{ route('provider.participants.matching.index') }}" data-section="match-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-pull-request-draft"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M18 9v6"/><path d="M12 15V9"/><path d="M6 9v4"/><path d="M12 6h-.01"/><path d="M18 6h-.01"/></svg> Match Participants
                </a>
                <a href="{{ route('provider.messages.index') }}" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </a>
            </nav>
        </aside>

        {{-- Mobile Sidebar (Original, still present for smaller screens) --}}
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 bg-[#ffffff] text-[#000000] w-64 p-6 space-y-6 transform -translate-x-full md:hidden transition-transform duration-300 ease-in-out z-40 border-r border-[#e1e7dd] flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold text-[#3e4732]">Menu</h2>
                <button id="close-sidebar-button" class="text-[#bcbabb] focus:outline-none p-2 rounded-md hover:bg-[#e1e7dd]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1 overflow-y-auto pr-2" id="mobile-sidebar-nav-container">
                <p class="text-xs font-semibold text-[#bcbabb] uppercase mb-2 px-4">Menu</p>
                <a href="{{ route('provider.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                <a href="#" data-section="my-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> My Participants
                </a>
                <a href="{{ route('provider.accommodations.index') }}" data-section="my-accommodations" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> My Accommodations
                </a>
                <a href="{{ route('provider.participants.matching.index') }}" data-section="match-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-pull-request-draft"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M18 9v6"/><path d="M12 15V9"/><path d="M6 9v4"/><path d="M12 6h-.01"/><path d="M18 6h-.01"/></svg> Match Participants
                </a>
                <a href="#" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>  Messages
                </a>
            </nav>
        </aside>

        @if (session('success'))
        <div id="success-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out">
            <div class="bg-custom-white rounded-lg shadow-2xl p-8 max-w-sm w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-3 right-3 text-text-light hover:text-text-dark focus:outline-none close-overlay">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-custom-green mb-4">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-text-dark mb-2">Success!</h3>
                    <p class="text-text-light text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="error-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out">
            <div class="bg-custom-white rounded-lg shadow-2xl p-8 max-w-sm w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-3 right-3 text-text-light hover:text-text-dark focus:outline-none close-overlay">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-[#ef4444] mb-4">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2L8 6m0 0l-2 2m2 2l-2 2m8-8l2 2m0 0l2 2m-2-2l-2 2m2-2l2-2M6 8l2-2m0 0l2 2M6 8l-2 2" />
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-text-dark mb-2">Oops!</h3>
                    <p class="text-text-light text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

        <main class="flex-1 p-4 md:p-8 overflow-y-auto md:ml-72 pt-20 md:pt-8">
            <div class="max-w-full mx-auto">
                @yield('main-content')
            </div>
        </main>
    </div>

    {{-- Flatpickr JS (Moved here to ensure it loads before your inline script) --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Using a single variable for the mobile sidebar for clarity
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeSidebarButton = document.getElementById('close-sidebar-button');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileDropdownActions = profileDropdown ? profileDropdown.querySelectorAll('button[data-action]') : [];

            // Mobile menu toggle (targets the specific mobile sidebar)
            mobileMenuButton.addEventListener('click', function() {
                if (mobileSidebar) {
                    mobileSidebar.classList.remove('-translate-x-full');
                }
            });

            if (closeSidebarButton) {
                closeSidebarButton.addEventListener('click', function() {
                    if (mobileSidebar) {
                        mobileSidebar.classList.add('-translate-x-full');
                    }
                });
            }

            // Profile dropdown toggle
            if (profileMenuButton) {
                profileMenuButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (profileMenuButton && profileDropdown && !profileMenuButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            document.querySelectorAll('[data-action="logout"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('logout-form').submit();
                });
            });

            // Update profile dropdown actions to use the new dashboard route
            profileDropdownActions.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    profileDropdown.classList.add('hidden');

                    if (action === 'profile') {
                        window.location.href = '{{ route('provider.dashboard') }}'; // Direct to provider dashboard for profile
                    } else if (action === 'settings') {
                        console.log('Navigating to settings...'); // You can change this to a proper route later
                    } else if (action === 'logout') {
                        document.getElementById('logout-form').submit();
                    }
                });
            });

            // Function to set the active sidebar link
            function setActiveSidebarLink() {
                sidebarLinks.forEach(item => item.classList.remove('active')); // Remove active from all first
                const currentPath = window.location.pathname;

                // Loop through links to find a match
                sidebarLinks.forEach(link => {
                    const linkHref = new URL(link.href).pathname; // Get pathname from href to ignore host/query params

                    
                });

                // If no specific link matches, default to dashboard if on the root of provider path
                if (!document.querySelector('.sidebar-link.active') && (currentPath === '/provider' || currentPath === '{{ route('provider.dashboard', [], false) }}')) {
                    const dashboardLink = document.querySelector('.sidebar-link[data-section="dashboard"]');
                    if (dashboardLink) {
                        dashboardLink.classList.add('active');
                    }
                }
            }

            // Call the function on DOMContentLoaded to set the initial active link
            setActiveSidebarLink();

            // Add event listeners for clicks on sidebar links (optional, for immediate visual feedback before full page load)
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    // Immediately apply active class for visual feedback
                    sidebarLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    // If on mobile, close sidebar after clicking a link
                    if (window.innerWidth < 768 && mobileSidebar) { // 768px is md breakpoint
                        mobileSidebar.classList.add('-translate-x-full');
                    }
                });
            });

            // --- Overlay Handling ---
            /**
             * Shows an overlay by removing 'hidden', 'opacity-0', 'pointer-events-none'
             * and updating transform classes.
             * @param {HTMLElement} overlayElement - The overlay element to show.
             */
            function showOverlay(overlayElement) {
                if (overlayElement) {
                    overlayElement.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
                    overlayElement.classList.add('active'); // Keeping 'active' for consistency with original script, though it wasn't used for transitions here.
                    // Small delay to allow 'hidden' to be removed before transition
                    setTimeout(() => {
                        overlayElement.querySelector('div').classList.remove('-translate-y-4', 'scale-95');
                        overlayElement.querySelector('div').classList.add('translate-y-0', 'scale-100'); // Assuming these are the "visible" states
                    }, 10);
                }
            }

            /**
             * Hides an overlay by adding 'opacity-0', 'pointer-events-none' and
             * updating transform classes, then adding 'hidden' after transition.
             * @param {HTMLElement} overlayElement - The overlay element to hide.
             */
            function hideOverlay(overlayElement) {
                if (overlayElement) {
                    overlayElement.classList.remove('active'); // Remove active state
                    overlayElement.classList.add('opacity-0', 'pointer-events-none');
                    overlayElement.querySelector('div').classList.remove('translate-y-0', 'scale-100');
                    overlayElement.querySelector('div').classList.add('-translate-y-4', 'scale-95');

                    overlayElement.addEventListener('transitionend', function handler() {
                        overlayElement.classList.add('hidden');
                        overlayElement.removeEventListener('transitionend', handler);
                    }, {
                        once: true
                    });
                }
            }

            // Close overlays when close button is clicked
            document.querySelectorAll('.close-overlay').forEach(button => {
                button.addEventListener('click', function() {
                    const overlay = this.closest('[id$="-overlay"]');
                    hideOverlay(overlay);
                });
            });

            // Get success and error overlay elements
            const successOverlay = document.getElementById('success-overlay');
            const errorOverlay = document.getElementById('error-overlay');

            // Show overlays if session messages exist (Blade syntax)
            if (successOverlay && '{{ session("success") }}') {
                showOverlay(successOverlay);
            }
            if (errorOverlay && '{{ session("error") }}') {
                showOverlay(errorOverlay);
            }

            // Close by clicking outside overlay content
            if (successOverlay) {
                successOverlay.addEventListener('click', function(event) {
                    if (event.target === successOverlay) {
                        hideOverlay(successOverlay);
                    }
                });
            }
            if (errorOverlay) {
                errorOverlay.addEventListener('click', function(event) {
                    if (event.target === errorOverlay) {
                        hideOverlay(errorOverlay);
                    }
                });
            }
            // --- End Overlay Handling ---


            // --- Flatpickr Initialization ---
            // Initialize all inputs with the 'flatpickr-input' class
            flatpickr("#date_of_birth-input", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            });
            // --- End Flatpickr Initialization ---
        });
    </script>
</body>
</html>