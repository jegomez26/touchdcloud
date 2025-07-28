{{-- resources/views/supadmin/sa-db.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Updated title to reflect Super Admin --}}
    <title>Touch D Cloud - Super Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f8f1e1; /* Light cream background */
        }
        /* Custom styles for active sidebar link */
        .sidebar-link.active {
            background-color: #e1e7dd; /* Light green-gray background */
            color: #3e4732; /* Darker green-gray text */
            font-weight: 600; /* Semi-bold */
            box-shadow: none; /* Remove shadow for cleaner look */
            transform: none; /* Remove transform */
        }
        .sidebar-link {
            transition: all 0.2s ease-in-out;
            padding-left: 1.5rem; /* Consistent padding */
            padding-right: 1.5rem;
        }
        .sidebar-link:not(.active):hover {
            background-color: #f2f7ed; /* Lighter hover for non-active */
            color: #3e4732; /* Darker green-gray on hover */
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
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#f8f1e1] text-[#000000] flex flex-col">

    <header class="bg-[#ffffff] shadow-md p-4 flex items-center justify-between z-30 sticky top-0 w-full h-16"> {{-- Added h-16 (64px) for header height --}}
        <div class="flex items-center md:hidden w-full justify-between">
            {{-- Updated header for mobile --}}
            <h1 class="text-xl font-bold text-[#3e4732]">Super Admin Dashboard</h1>
            <button id="mobile-menu-button" class="text-[#bcbabb] focus:outline-none p-2 rounded-md hover:bg-[#f8f1e1]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('superadmin.dashboard') }}" class="text-3xl font-extrabold text-[#33595a] hover:text-[#3e4732] transition duration-300">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }}" class="h-10 inline-block align-middle mr-3">
                {{ config('app.name', 'TouchdCloud') }}
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
        <aside id="sidebar" class="hidden md:flex flex-col fixed top-16 left-0 h-[calc(100vh-64px)] w-60 bg-[#ffffff] text-[#000000] p-6 space-y-6 border-r border-[#e1e7dd] z-20">
            <div class="flex items-center justify-center mb-8">
                {{-- No close button needed for desktop fixed sidebar --}}
            </div>
            {{-- Navigation menu: this itself should not scroll --}}
            <nav class="space-y-1 overflow-y-auto pr-2" id="sidebar-nav-container"> {{-- Added overflow-y-auto and pr-2 here for explicit sidebar nav scrolling if needed --}}
                <p class="text-xs font-semibold text-[#bcbabb] uppercase mb-2 px-4">Menu</p>
                {{-- Dashboard link --}}
                <a href="{{ route('superadmin.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                {{-- Participants link --}}
                <a href="" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Participants
                </a>
                {{-- Support Coordinators link --}}
                <a href="{{ route('superadmin.support-coordinators.index') }}" data-section="support-coordinators" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-handshake mr-3"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.5.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.3-.6h4.4c1.1 0 2.1-.4 2.8-1.2L21 11"/><path d="M19 12v6a2 2 0 0 1-2 2h-4"/></svg> Support Coordinators
                </a>
                {{-- Providers link --}}
                <a href="" data-section="providers" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck mr-3"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M10 18H7"/><path d="M19 18h-6"/><path d="M17 21a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/><path d="M7 21a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/><path d="M14 6h7l-3 5H6"/></svg> Providers
                </a>
                {{-- NDIS Businesses link --}}
                <a href="{{ route('superadmin.ndis-businesses.index') }}" data-section="ndis-businesses" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building mr-3"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg> NDIS Businesses
                </a>
                {{-- Messages link --}}
                <a href="" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
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
                {{-- Dashboard link for mobile --}}
                <a href="{{ route('superadmin.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                {{-- Participants link for mobile --}}
                <a href="" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Participants
                </a>
                {{-- Support Coordinators link for mobile --}}
                <a href="" data-section="support-coordinators" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-handshake mr-3"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.5.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.3-.6h4.4c1.1 0 2.1-.4 2.8-1.2L21 11"/><path d="M19 12v6a2 2 0 0 1-2 2h-4"/></svg> Support Coordinators
                </a>
                {{-- Providers link for mobile --}}
                <a href="" data-section="providers" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck mr-3"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M10 18H7"/><path d="M19 18h-6"/><path d="M17 21a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/><path d="M7 21a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/><path d="M14 6h7l-3 5H6"/></svg> Providers
                </a>
                {{-- NDIS Businesses link for mobile --}}
                <a href="" data-section="ndis-businesses" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building mr-3"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg> NDIS Businesses
                </a>
                {{-- Messages link for mobile --}}
                <a href="" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </a>
            </nav>
        </aside>

        {{-- ... existing layout elements like navbars, sidebars etc. ... --}}

    @if (session('success'))
        <div id="success-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div class="bg-white rounded-lg shadow-2xl p-8 max-w-sm w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none close-overlay">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-green-500 mb-4">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                    <p class="text-gray-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="error-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div class="bg-white rounded-lg shadow-2xl p-8 max-w-sm w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none close-overlay">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-red-500 mb-4">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2L8 6m0 0l-2 2m2 2l-2 2m8-8l2 2m0 0l2 2m-2-2l-2 2m2-2l2-2m-2 2L6 8M6 8l2-2m0 0l2 2M6 8l-2 2" />
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Oops!</h3>
                    <p class="text-gray-700 text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif


        <main class="flex-1 p-4 md:p-8 overflow-y-auto md:ml-60 pt-20 md:pt-8"> {{-- Adjusted padding-top for header clearance --}}
            <div class="max-w-full mx-auto">
                {{-- This is where the dynamic content will be injected --}}
                @yield('content')

            </div>
        </main>
    </div>

    {{-- Flatpickr JS (Moved here to ensure it loads before your inline script) --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Function to set the active sidebar link
            function setActiveSidebarLink() {
                const path = window.location.pathname;
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                    const section = link.getAttribute('data-section');
                    // Simple check: if the path contains the section name
                    if (section && path.includes(section)) {
                        link.classList.add('active');
                    }
                });
            }

            // Call on load
            setActiveSidebarLink();

            // Profile dropdown toggle
            if (profileMenuButton) {
                profileMenuButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });
            }

            // Close profile dropdown if clicked outside
            window.addEventListener('click', function(event) {
                if (profileMenuButton && !profileMenuButton.contains(event.target) && profileDropdown && !profileDropdown.contains(event.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // Handle session messages (success/error overlays)
            const successOverlay = document.getElementById('success-overlay');
            const errorOverlay = document.getElementById('error-overlay');

            if (successOverlay) {
                // Show with transition
                setTimeout(() => {
                    successOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    successOverlay.querySelector('div').classList.remove('-translate-y-4', 'scale-95');
                }, 100); // Small delay to ensure CSS transition applies

                // Hide on close button click
                successOverlay.querySelectorAll('.close-overlay').forEach(button => {
                    button.addEventListener('click', () => {
                        successOverlay.classList.add('opacity-0', 'pointer-events-none');
                        successOverlay.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                    });
                });

                // Hide after 5 seconds
                setTimeout(() => {
                    successOverlay.classList.add('opacity-0', 'pointer-events-none');
                    successOverlay.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                }, 5000);
            }

            if (errorOverlay) {
                // Show with transition
                setTimeout(() => {
                    errorOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    errorOverlay.querySelector('div').classList.remove('-translate-y-4', 'scale-95');
                }, 100); // Small delay to ensure CSS transition applies

                // Hide on close button click
                errorOverlay.querySelectorAll('.close-overlay').forEach(button => {
                    button.addEventListener('click', () => {
                        errorOverlay.classList.add('opacity-0', 'pointer-events-none');
                        errorOverlay.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                    });
                });

                // Hide after 5 seconds
                setTimeout(() => {
                    errorOverlay.classList.add('opacity-0', 'pointer-events-none');
                    errorOverlay.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                }, 5000);
            }
        });
    </script>
</body>
</html>
```