{{-- resources/views/supadmin/sa-db.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Updated title to reflect Super Admin --}}
    <link rel="icon" type="image/png" href="{{ asset('images/blue_logo.png') }}">
    <title>Touch D Cloud - Super Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- FontAwesome CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            background: #E5E7EB; /* Matching light gray */
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
            background-color: #E5E7EB; /* light gray */
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
            border: 1px solid #E5E7EB; /* Subtle border */
            font-family: 'Inter', sans-serif; /* Use your main font */
            color: #374151; /* Dark gray for text */
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
            border: 1px solid #E5E7EB;
            padding: 0.5rem 0.75rem;
        }
            /* .mt-1.block.w-full.rounded-md.border-gray-300.shadow-sm.focus:border-indigo-500.focus:ring-indigo-500.sm:text-base.p-2.5.transition.ease-in-out.duration-150 */

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
            border-top-color: #E5E7EB; /* Match border */
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

        /* Custom Color Classes */
        .text-custom-dark-teal { color: #33595a; }
        .text-custom-dark-olive { color: #3e4732; }
        .text-custom-ochre { color: #cc8e45; }
        .text-custom-green { color: #4ade80; }
        .text-custom-white { color: #ffffff; }
        
        .bg-custom-white { background-color: #ffffff; }
        .bg-custom-light-cream { background-color: #f8f1e1; }
        .bg-custom-light-grey-green { background-color: #E5E7EB; }
        .bg-custom-light-grey-brown { background-color: #bcbabb; }
        .bg-custom-green { background-color: #4ade80; }
        .bg-custom-green-light { background-color: #dcfce7; }
        
        .border-custom-light-grey-green { border-color: #E5E7EB; }
        .border-custom-light-grey-brown { border-color: #bcbabb; }
        
        .focus\:ring-custom-ochre:focus { --tw-ring-color: #cc8e45; }
        .focus\:border-custom-ochre:focus { border-color: #cc8e45; }
        .hover\:bg-custom-ochre-darker:hover { background-color: #a67139; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F9FAFB] text-[#000000] flex flex-col">

    <header class="bg-[#ffffff] shadow-md p-4 flex items-center justify-between z-30 sticky top-0 w-full h-16"> {{-- Added h-16 (64px) for header height --}}
        <div class="flex items-center md:hidden w-full justify-between">
            {{-- Updated header for mobile --}}
            <h1 class="text-xl font-bold text-[#374151]">Super Admin Dashboard</h1>
            <button id="mobile-menu-button" class="text-[#6B7280] focus:outline-none p-2 rounded-md hover:bg-[#F9FAFB]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('superadmin.dashboard') }}" class="text-3xl font-extrabold text-[#33595a] hover:text-[#374151] transition duration-300">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }}" class="h-10 inline-block align-middle mr-3">
                
            </a>
            <div class="flex items-center space-x-4 relative">
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-[#bcbabb] focus:outline-none focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[#6B7280]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <button class="text-[#6B7280] hover:text-[#000000] focus:outline-none p-2 rounded-md hover:bg-[#F9FAFB] transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-[#6B7280] hover:text-[#000000] focus:outline-none p-2 rounded-md hover:bg-[#F9FAFB] transition-colors duration-200">
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
                        <span class="font-medium text-[#374151] hidden sm:inline">
                            {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-[#ffffff] rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        
                        <button data-action="logout" class="block px-4 py-2 text-sm text-[#cc8e45] hover:bg-[#F9FAFB] w-full text-left transition-colors duration-150 rounded-md" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
        <aside id="sidebar" class="hidden md:flex flex-col fixed top-16 left-0 h-[calc(100vh-64px)] w-60 bg-[#ffffff] text-[#000000] p-6 space-y-6 border-r border-[#E5E7EB] z-20">
            <div class="flex items-center justify-center mb-8">
                {{-- No close button needed for desktop fixed sidebar --}}
            </div>
            {{-- Navigation menu: this itself should not scroll --}}
            <nav class="space-y-1 overflow-y-auto pr-2" id="sidebar-nav-container"> {{-- Added overflow-y-auto and pr-2 here for explicit sidebar nav scrolling if needed --}}
                <p class="text-xs font-semibold text-[#6B7280] uppercase mb-2 px-4">Menu</p>
                {{-- Dashboard link --}}
                <a href="{{ route('superadmin.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                {{-- Participants link --}}
                @if(auth()->user()->hasPrivilege('manage_participants'))
                <a href="{{ route('superadmin.participants.index') }}" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-users mr-3"></i> Participants
                </a>
                @endif
                {{-- Support Coordinators link --}}
                @if(auth()->user()->hasPrivilege('manage_support_coordinators'))
                <a href="{{ route('superadmin.support-coordinators.index') }}" data-section="support-coordinators" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-hands-helping mr-3"></i> Support Coordinators
                </a>
                @endif
                {{-- Admins link --}}
                @if(auth()->user()->hasPrivilege('manage_admins'))
                <a href="{{ route('superadmin.admins.index') }}" data-section="admins" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-crown mr-3"></i> Manage Admins
                </a>
                @endif
                {{-- Providers link --}}
                @if(auth()->user()->hasPrivilege('manage_providers'))
                <a href="{{ route('superadmin.providers.index') }}" data-section="providers" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-building mr-3"></i> Providers
                </a>
                @endif
                {{-- Support Center link --}}
                <a href="{{ route('superadmin.support-center.index') }}" data-section="support-center" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-headphones mr-3"></i> Support Center
                </a>
            </nav>
        </aside>

        {{-- Mobile Sidebar (Original, still present for smaller screens) --}}
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 bg-[#ffffff] text-[#000000] w-64 p-6 space-y-6 transform -translate-x-full md:hidden transition-transform duration-300 ease-in-out z-40 border-r border-[#E5E7EB] flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold text-[#374151]">Menu</h2>
                <button id="close-sidebar-button" class="text-[#6B7280] focus:outline-none p-2 rounded-md hover:bg-[#E5E7EB]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1 overflow-y-auto pr-2" id="mobile-sidebar-nav-container">
                <p class="text-xs font-semibold text-[#6B7280] uppercase mb-2 px-4">Menu</p>
                {{-- Dashboard link for mobile --}}
                <a href="{{ route('superadmin.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                {{-- Participants link for mobile --}}
                @if(auth()->user()->hasPrivilege('manage_participants'))
                <a href="{{ route('superadmin.participants.index') }}" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-users mr-3"></i> Participants
                </a>
                @endif
                {{-- Support Coordinators link for mobile --}}
                @if(auth()->user()->hasPrivilege('manage_support_coordinators'))
                <a href="{{ route('superadmin.support-coordinators.index') }}" data-section="support-coordinators" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-hands-helping mr-3"></i> Support Coordinators
                </a>
                @endif
                {{-- Admins link for mobile --}}
                @if(auth()->user()->hasPrivilege('manage_admins'))
                <a href="{{ route('superadmin.admins.index') }}" data-section="admins" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-crown mr-3"></i> Manage Admins
                </a>
                @endif
                {{-- Providers link for mobile --}}
                @if(auth()->user()->hasPrivilege('manage_providers'))
                <a href="{{ route('superadmin.providers.index') }}" data-section="providers" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-building mr-3"></i> Providers
                </a>
                @endif
                {{-- Support Center link for mobile --}}
                <a href="{{ route('superadmin.support-center.index') }}" data-section="support-center" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#374151] hover:bg-[#E5E7EB] hover:text-[#374151]">
                    <i class="fas fa-headphones mr-3"></i> Support Center
                </a>
            </nav>
        </aside>

        {{-- ... existing layout elements like navbars, sidebars etc. ... --}}

    {{-- Include Enhanced Modal Components --}}
    @include('components.modals')

    {{-- Session-based Success Modal --}}
    @if (session('success'))
        <div id="session-success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-session-modal" data-modal="session-success-modal">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-green-500 mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Success!</h3>
                    <p class="text-gray-700 text-lg mb-6">{{ session('success') }}</p>
                    <button class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-200 font-medium close-session-modal" data-modal="session-success-modal">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Session-based Error Modal --}}
    @if (session('error'))
        @php
            $errorMessage = session('error');
            $isUnauthorized = str_contains($errorMessage, 'permission') || str_contains($errorMessage, 'privilege');
        @endphp
        
        @if($isUnauthorized)
            {{-- Show Unauthorized Modal for permission errors --}}
            <div id="session-unauthorized-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
                <div class="bg-white rounded-xl shadow-2xl p-8 max-w-lg w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                    <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-session-modal" data-modal="session-unauthorized-modal">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="text-center">
                        <div class="flex items-center justify-center text-amber-500 mb-6">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Access Denied</h3>
                        <p class="text-gray-700 text-lg mb-4">{{ $errorMessage }}</p>
                        <p class="text-gray-600 text-sm mb-6">Please contact your administrator if you believe this is an error.</p>
                        <div class="flex gap-3 justify-center">
                            <button class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-session-modal" data-modal="session-unauthorized-modal">
                                Close
                            </button>
                            <button class="bg-amber-500 text-white px-6 py-3 rounded-lg hover:bg-amber-600 transition duration-200 font-medium" onclick="window.location.href='{{ route('superadmin.dashboard') }}'">
                                Go to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Show Regular Error Modal for other errors --}}
            <div id="session-error-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
                <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                    <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-session-modal" data-modal="session-error-modal">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="text-center">
                        <div class="flex items-center justify-center text-red-500 mb-6">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Error!</h3>
                        <p class="text-gray-700 text-lg mb-6">{{ $errorMessage }}</p>
                        <button class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition duration-200 font-medium close-session-modal" data-modal="session-error-modal">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Session-based Warning Modal --}}
    @if (session('warning'))
        <div id="session-warning-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-session-modal" data-modal="session-warning-modal">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-yellow-500 mb-6">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Warning!</h3>
                    <p class="text-gray-700 text-lg mb-6">{{ session('warning') }}</p>
                    <button class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-200 font-medium close-session-modal" data-modal="session-warning-modal">
                        Understood
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Session-based Info Modal --}}
    @if (session('info'))
        <div id="session-info-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-session-modal" data-modal="session-info-modal">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <div class="flex items-center justify-center text-blue-500 mb-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Information</h3>
                    <p class="text-gray-700 text-lg mb-6">{{ session('info') }}</p>
                    <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium close-session-modal" data-modal="session-info-modal">
                        Got it
                    </button>
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
    
    {{-- FontAwesome Fallback Script --}}
    <script>
        // Check if FontAwesome loaded properly
        document.addEventListener('DOMContentLoaded', function() {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-check';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const computedStyle = window.getComputedStyle(testIcon);
            const fontFamily = computedStyle.getPropertyValue('font-family');
            
            if (!fontFamily.includes('Font Awesome') && !fontFamily.includes('FontAwesome')) {
                console.warn('FontAwesome not loaded properly, icons may not display correctly');
                // Add fallback styling
                const style = document.createElement('style');
                style.textContent = `
                    .fas:before {
                        content: '●';
                        font-family: Arial, sans-serif;
                        font-weight: bold;
                    }
                    .fa-users:before { content: '👥'; }
                    .fa-handshake:before { content: '🤝'; }
                    .fa-hospital:before { content: '🏥'; }
                    .fa-heart:before { content: '❤️'; }
                    .fa-percentage:before { content: '%'; }
                    .fa-user:before { content: '👤'; }
                    .fa-user-check:before { content: '✅'; }
                    .fa-user-plus:before { content: '➕'; }
                    .fa-map-marker-alt:before { content: '📍'; }
                    .fa-list-alt:before { content: '📋'; }
                    .fa-envelope:before { content: '✉️'; }
                    .fa-phone:before { content: '📞'; }
                    .fa-calendar:before { content: '📅'; }
                    .fa-clock:before { content: '🕐'; }
                    .fa-crown:before { content: '👑'; }
                    .fa-check-circle:before { content: '✅'; }
                    .fa-times-circle:before { content: '❌'; }
                    .fa-ban:before { content: '🚫'; }
                    .fa-minus:before { content: '➖'; }
                    .fa-wheelchair:before { content: '♿'; }
                    .fa-user-tie:before { content: '👔'; }
                    .fa-times:before { content: '✖️'; }
                    .fa-hourglass-half:before { content: '⏳'; }
                    .fa-users-cog:before { content: '👥⚙️'; }
                `;
                document.head.appendChild(style);
            }
            
            document.body.removeChild(testIcon);
        });
    </script>

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

            // Handle unauthorized access responses
            // Intercept fetch requests to handle unauthorized responses
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 403) {
                            return response.json().then(data => {
                                if (data.error === 'unauthorized') {
                                    showUnauthorized(data.message);
                                    throw new Error('Unauthorized access');
                                }
                                throw new Error(data.message || 'Access denied');
                            });
                        }
                        return response;
                    });
            };

            // Handle XMLHttpRequest responses
            const originalXHROpen = XMLHttpRequest.prototype.open;
            const originalXHRSend = XMLHttpRequest.prototype.send;
            
            XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
                this._url = url;
                return originalXHROpen.apply(this, arguments);
            };
            
            XMLHttpRequest.prototype.send = function(data) {
                this.addEventListener('readystatechange', function() {
                    if (this.readyState === 4 && this.status === 403) {
                        try {
                            const response = JSON.parse(this.responseText);
                            if (response.error === 'unauthorized') {
                                showUnauthorized(response.message);
                            }
                        } catch (e) {
                            // If response is not JSON, show generic unauthorized message
                            showUnauthorized('You don\'t have permission to perform this action.');
                        }
                    }
                });
                return originalXHRSend.apply(this, arguments);
            };

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

            // Handle session messages (enhanced modals)
            const sessionModals = [
                'session-success-modal',
                'session-error-modal', 
                'session-warning-modal',
                'session-info-modal',
                'session-unauthorized-modal'
            ];

            sessionModals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                // Show with transition
                setTimeout(() => {
                        modal.classList.remove('opacity-0', 'pointer-events-none');
                        modal.querySelector('div').classList.remove('-translate-y-4', 'scale-95');
                    }, 100);

                // Hide on close button click
                    modal.querySelectorAll('.close-session-modal').forEach(button => {
                    button.addEventListener('click', () => {
                            modal.classList.add('opacity-0', 'pointer-events-none');
                            modal.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                        });
                    });

                    // Auto-hide after 5 seconds
                setTimeout(() => {
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        modal.querySelector('div').classList.add('-translate-y-4', 'scale-95');
                }, 5000);
            }
            });
        });
    </script>
</body>
</html>
```