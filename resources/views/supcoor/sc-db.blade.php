{{-- resources/views/supcoor/sc-db.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/blue_logo.png') }}">

    <title>SIL Match - Support Coordinator Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- FontAwesome CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <h1 class="text-xl font-bold text-[#3e4732]">Support Coordinator Dashboard</h1>
            <button id="mobile-menu-button" class="text-[#bcbabb] focus:outline-none p-2 rounded-md hover:bg-[#f8f1e1]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-[#33595a] hover:text-[#3e4732] transition duration-300">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }}" class="h-10 inline-block align-middle mr-3">
            </a>
            <div class="flex items-center space-x-4 relative">
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
                        <div class="flex flex-col">
                            <span class="font-medium text-[#3e4732] hidden sm:inline">
                                {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}
                            </span>
                            <span class="text-xs text-[#6b7280] hidden sm:inline">
                                @if(Auth::user()->role === 'coordinator')
                                    Support Coordinator
                                @elseif(Auth::user()->role === 'provider')
                                    NDIS Provider
                                @elseif(Auth::user()->role === 'participant')
                                    Participant
                                @else
                                    {{ ucfirst(Auth::user()->role) }}
                                @endif
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-[#ffffff] rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
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
                <a href="{{ route('sc.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('sc.participants.list') }}" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-user-check mr-3"></i> Participants
                </a>
                <a href="{{ route('sc.participants.matching.index') }}" data-section="participants-matching" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-heart mr-3"></i> Match Participants
                </a>
                
                <a href="{{route('sc.messages.index')}}" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-comments mr-3"></i> Messages
                </a>
                <a href="{{ route('sc.match-requests.index') }}" data-section="match-requests" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-handshake mr-3"></i> Match Requests
                </a>
                <a href="{{ route('sc.support-center.index') }}" data-section="support-center" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-headphones mr-3"></i> Support Center
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
                <a href="{{ route('sc.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('sc.participants.list') }}" data-section="participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-user-check mr-3"></i> Participants
                </a>
                <a href="{{ route('sc.participants.matching.index') }}" data-section="participants-matching" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-heart mr-3"></i> Match Participants
                </a>
                
                <a href="{{route('sc.messages.index')}}" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-comments mr-3"></i> Messages
                </a>
                <a href="{{ route('sc.match-requests.index') }}" data-section="match-requests" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-handshake mr-3"></i> Match Requests
                </a>
                <a href="{{ route('sc.support-center.index') }}" data-section="support-center" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-headphones mr-3"></i> Support Center
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8 overflow-y-auto md:ml-72 pt-20 md:pt-8">
            <div class="max-w-full mx-auto">
                @yield('main-content')
            </div>
        </main>
    </div>

    {{-- Match Request Modal --}}
    <div id="match-request-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Send Match Request</h3>
                        <button id="close-match-request-modal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Send a match request to:</p>
                        <p id="match-request-participant-name" class="font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="match-request-message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message (Optional)
                        </label>
                        <textarea 
                            id="match-request-message" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#33595a] focus:border-transparent"
                            placeholder="Add a personal message to your match request..."
                        ></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button 
                            id="cancel-match-request" 
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200"
                        >
                            Cancel
                        </button>
                        <button 
                            id="send-match-request" 
                            class="flex-1 px-4 py-2 bg-[#33595a] text-white rounded-md hover:bg-[#2C494A] transition-colors duration-200"
                        >
                            Send Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Request Already Sent Modal --}}
    <div id="request-already-sent-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md mx-4 rounded-lg shadow-xl border border-[#e1e7dd]">
            <div class="px-6 py-4 border-b border-[#e1e7dd] flex items-center justify-between">
                <h3 class="text-lg font-semibold text-[#3e4732]">Match Request Already Sent</h3>
                <button id="close-already-sent-modal" class="text-[#bcbabb] hover:text-[#3e4732]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[#3e4732] font-medium">A match request has already been sent to <span id="already-sent-participant-name" class="font-semibold"></span>.</p>
                        <p class="text-xs text-[#bcbabb] mt-1">Please wait for them to accept your request.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-[#e1e7dd] flex items-center justify-end">
                <button id="close-already-sent-modal-btn" class="px-4 py-2 bg-[#33595a] text-white rounded-md hover:bg-[#2C494A] transition-colors duration-200">
                    OK
                </button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Success!</h3>
                    <p id="success-message" class="text-sm text-gray-600 mb-4"></p>
                    <button 
                        id="close-success-modal" 
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Modal --}}
    <div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Error</h3>
                    <p id="error-message" class="text-sm text-gray-600 mb-4"></p>
                    <button 
                        id="close-error-modal" 
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
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
                        window.location.href = '{{ route('sc.dashboard') }}'; // Direct to dashboard for profile
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

                    if (currentPath === linkHref) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('sc.participants.list', [], false) }}' && currentPath.startsWith('{{ route('sc.participants.list', [], false) }}') && !currentPath.includes('/participants-matching')) {
                        // Special handling for /participants routes (e.g., /participants/123) but exclude matching routes
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('sc.participants.matching.index', [], false) }}' && currentPath.startsWith('{{ route('sc.participants.matching.index', [], false) }}')) {
                        // Special handling for /participants-matching routes
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('sc.dashboard', [], false) }}' && currentPath.includes('/dashboard')) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('sc.unassigned_participants', [], false) }}' && currentPath.includes('/unassigned-participants')) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('sc.messages.index', [], false) }}' && currentPath.includes('/messages')) {
                        link.classList.add('active');
                    }
                });

                // If no specific link matches, default to dashboard if on the root of sc path
                if (!document.querySelector('.sidebar-link.active') && (currentPath === '/supcoor' || currentPath === '{{ route('sc.dashboard', [], false) }}')) {
                    const dashboardLink = document.querySelector('.sidebar-link[data-section="dashboard"]');
                    if (dashboardLink) {
                        dashboardLink.classList.add('active');
                    }
                }
            }

            // Call the function on DOMContentLoaded to set the initial active link
            setActiveSidebarLink();

            // Add event listeners for clicks on sidebar links (optional, for immediate visual feedback before full page load)
            // Note: Full page reload will re-run setActiveSidebarLink anyway.
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    // Prevent default only if you were doing a SPA-like behavior.
                    // Since you're doing full page reloads with href, let the default behavior happen.
                    // This listener is mostly for the mobile sidebar closing and immediate active state toggle.
                    
                    // Immediately apply active class for visual feedback
                    sidebarLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    // If on mobile, close sidebar after clicking a link
                    if (window.innerWidth < 768 && mobileSidebar) { // 768px is md breakpoint
                        mobileSidebar.classList.add('-translate-x-full');
                    }
                });
            });


            // --- Flatpickr Initialization ---
            // Initialize all inputs with the 'flatpickr-input' class
            flatpickr("#date_of_birth-input", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            });
            // --- End Flatpickr Initialization ---
        });

        // Match request functions
        window.requestMatch = function(participantId) {
            // Store the participant ID for later use
            window.currentParticipantId = participantId;
            
            // Update the modal with participant info
            const participantNameElement = document.getElementById('match-request-participant-name');
            const messageElement = document.getElementById('match-request-message');
            const modalElement = document.getElementById('match-request-modal');
            
            if (!participantNameElement) {
                console.error('match-request-participant-name element not found');
                return;
            }
            if (!messageElement) {
                console.error('match-request-message element not found');
                return;
            }
            if (!modalElement) {
                console.error('match-request-modal element not found');
                return;
            }
            
            // Try to get participant code from the match element
            const matchElement = document.querySelector(`[data-participant-id="${participantId}"]`);
            const participantCode = matchElement ? matchElement.getAttribute('data-participant-code') : null;
            
            participantNameElement.textContent = participantCode || `Participant ${participantId}`;
            messageElement.value = '';
            
            // Show the modal
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
        };

        // Modal event listeners
        const closeMatchRequestModal = document.getElementById('close-match-request-modal');
        const cancelMatchRequest = document.getElementById('cancel-match-request');
        const matchRequestModal = document.getElementById('match-request-modal');
        
        if (closeMatchRequestModal) {
            closeMatchRequestModal.addEventListener('click', function() {
                if (matchRequestModal) {
                    matchRequestModal.classList.add('hidden');
                    matchRequestModal.classList.remove('flex');
                }
            });
        }

        if (cancelMatchRequest) {
            cancelMatchRequest.addEventListener('click', function() {
                if (matchRequestModal) {
                    matchRequestModal.classList.add('hidden');
                    matchRequestModal.classList.remove('flex');
                }
            });
        }

        document.getElementById('send-match-request').addEventListener('click', function() {
            const message = document.getElementById('match-request-message').value;
            const participantId = window.currentParticipantId;
            const senderParticipantId = window.matchingContextParticipantId || null;
            
            // Disable the send button to prevent double-clicks
            const sendBtn = document.getElementById('send-match-request');
            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';
            
            // Make API call to send match request
            const requestBody = {
                participant_id: participantId,
                message: message
            };
            
            // Include sender_participant_id if available (from matching context)
            if (senderParticipantId) {
                requestBody.sender_participant_id = senderParticipantId;
            }
            
            fetch('/match-requests/send-for-participant', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestBody)
            })
            .then(response => {
                // Always try to parse JSON, even if response is not ok
                return parseJsonResponse(response).then(data => {
                    // If response is not ok, throw error
                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to send match request');
                    }
                    return data;
                });
            })
            .then(data => {
                // Hide the match request modal
                const modal = document.getElementById('match-request-modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                
                // Check for success - handle both 'success' property and direct success responses
                if (data.success === true || (data.message && !data.error)) {
                    showSuccessModal('Match request sent successfully!');
                    updateButtonState(participantId, 'pending');
                } else {
                    showErrorModal(data.error || data.message || 'Failed to send match request');
                }
            })
            .catch(error => {
                console.error('Error sending match request:', error);
                const modal = document.getElementById('match-request-modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                showErrorModal(error.message || 'Failed to send match request. Please try again.');
            })
            .finally(() => {
                // Re-enable the send button
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send Request';
            });
        });

        window.acceptMatchRequest = function(requestId) {
            fetch(`/match-requests/${requestId}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(parseJsonResponse)
            .then(data => {
                if (data.success) {
                    showSuccessModal('Match request accepted! You can now start a conversation.');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showErrorModal(data.error || 'Failed to accept match request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Failed to accept match request. Please try again.');
            });
        };

        window.rejectMatchRequest = function(requestId) {
            fetch(`/match-requests/${requestId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(parseJsonResponse)
            .then(data => {
                if (data.success) {
                    showSuccessModal('Match request rejected');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showErrorModal(data.error || 'Failed to reject match request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Failed to reject match request. Please try again.');
            });
        };

        function updateButtonState(participantId, state) {
            // Find the button by looking for the checkMatchRequestStatus onclick attribute
            const button = document.querySelector(`button[onclick*="checkMatchRequestStatus(${participantId}"]`);
            if (!button) {
                // Fallback: look for any button containing the participant ID
                const buttons = document.querySelectorAll('button');
                const targetButton = Array.from(buttons).find(btn => 
                    btn.onclick && btn.onclick.toString().includes(participantId)
                );
                if (targetButton) {
                    updateButtonContent(targetButton, state, participantId);
                }
                return;
            }
            updateButtonContent(button, state, participantId);
        }
        
        function updateButtonContent(button, state, participantId) {
            switch(state) {
                case 'pending':
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Request Sent</span>
                    `;
                    button.disabled = true;
                    button.classList.remove('bg-[#33595a]', 'hover:bg-[#2C494A]');
                    button.classList.add('bg-yellow-600', 'cursor-not-allowed');
                    button.onclick = null; // Remove onclick handler
                    break;
                case 'accepted':
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Start Conversation</span>
                    `;
                    button.classList.remove('bg-[#33595a]', 'hover:bg-[#2C494A]');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                    button.onclick = () => openSendModal(participantId);
                    break;
                case 'rejected':
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Request Rejected</span>
                    `;
                    button.disabled = true;
                    button.classList.remove('bg-[#33595a]', 'hover:bg-[#2C494A]');
                    button.classList.add('bg-red-600', 'cursor-not-allowed');
                    button.onclick = null; // Remove onclick handler
                    break;
            }
        }

        function getUserIdFromParticipantId(participantId) {
            // This is a placeholder - you'd need to implement proper mapping
            // For demo purposes, return a dummy ID
            return 1;
        }

        function startChat(participantId) {
            // Redirect to chat or open chat modal
            alert('Starting chat with participant ' + participantId);
        }

        // Ensure we can safely parse JSON responses even when the backend sends HTML (e.g., 419/302)
        function parseJsonResponse(response) {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                return response.json();
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error(text || 'Unexpected non-JSON response');
                }
            });
        }

        // Modal helper functions
        function showSuccessModal(message) {
            document.getElementById('success-message').textContent = message;
            document.getElementById('success-modal').classList.remove('hidden');
        }

        function showErrorModal(message) {
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-modal').classList.remove('hidden');
        }

        // Success modal event listeners
        document.getElementById('close-success-modal').addEventListener('click', function() {
            document.getElementById('success-modal').classList.add('hidden');
        });

        // Error modal event listeners
        document.getElementById('close-error-modal').addEventListener('click', function() {
            document.getElementById('error-modal').classList.add('hidden');
        });

        // Close modals when clicking outside
        document.getElementById('match-request-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('success-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('error-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
        
        // Check match request status dynamically
        window.checkMatchRequestStatus = function(participantId, buttonElement) {
            // Show loading state
            const originalText = buttonElement.innerHTML;
            buttonElement.disabled = true;
            buttonElement.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2 inline-block"></div>
                Checking...
            `;
            
            // Get the current participant ID (the one we're matching FOR)
            const senderParticipantId = window.matchingContextParticipantId || null;
            
            if (!senderParticipantId) {
                console.error('No sender participant ID found');
                buttonElement.disabled = false;
                buttonElement.innerHTML = originalText;
                return;
            }
            
            // Use the new endpoint to check match request status between two participants
            fetch('/match-requests/check-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    sender_participant_id: senderParticipantId,
                    receiver_participant_id: participantId
                })
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(text || 'Unexpected non-JSON response');
                    }
                });
            })
            .then(data => {
                if (data.success && data.exists) {
                    if (data.status === 'pending') {
                        // Show modal that request is already sent
                        showRequestAlreadySentModal(participantId);
                        updateButtonForMatchRequestStatus(buttonElement, 'pending', participantId);
                    } else if (data.status === 'accepted') {
                        // Show "See Conversation" button
                        updateButtonForMatchRequestStatus(buttonElement, 'accepted', participantId, data.conversation_id);
                    } else {
                        updateButtonForMatchRequestStatus(buttonElement, data.status, participantId);
                    }
                } else {
                    // No existing request, proceed with sending new request
                    requestMatch(participantId);
                }
            })
            .catch(error => {
                console.error('Error checking match request status:', error);
                // On error, proceed with sending new request
                requestMatch(participantId);
            })
            .finally(() => {
                // Reset button if no action was taken
                if (buttonElement.disabled && buttonElement.innerHTML.includes('Checking...')) {
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = originalText;
                }
            });
        };
        
        // Function to show "request already sent" modal
        function showRequestAlreadySentModal(participantId) {
            const modal = document.getElementById('request-already-sent-modal');
            if (modal) {
                // Get participant code if available
                const matchElement = document.querySelector(`[data-participant-id="${participantId}"]`);
                const participantCode = matchElement ? matchElement.getAttribute('data-participant-code') : `Participant ${participantId}`;
                
                const nameElement = document.getElementById('already-sent-participant-name');
                if (nameElement) {
                    nameElement.textContent = participantCode;
                }
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        
        function updateButtonForMatchRequestStatus(buttonElement, status, participantId, conversationId = null) {
            buttonElement.disabled = false;
            
            switch(status) {
                case 'pending':
                    buttonElement.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Request Sent</span>
                    `;
                    buttonElement.className = buttonElement.className.replace('bg-[#33595a] hover:bg-[#2C494A]', 'bg-yellow-600 cursor-not-allowed');
                    buttonElement.disabled = true;
                    buttonElement.onclick = null; // Remove onclick handler
                    break;
                case 'accepted':
                    if (conversationId) {
                        // Show "See Conversation" button with link
                        buttonElement.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>See Conversation</span>
                        `;
                        buttonElement.className = buttonElement.className.replace('bg-[#33595a] hover:bg-[#2C494A]', 'bg-green-600 hover:bg-green-700');
                        buttonElement.onclick = () => {
                            window.location.href = `/sc/messages/${conversationId}`;
                        };
                    } else {
                        // No conversation ID, show "Start Conversation"
                        buttonElement.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Start Conversation</span>
                        `;
                        buttonElement.className = buttonElement.className.replace('bg-[#33595a] hover:bg-[#2C494A]', 'bg-green-600 hover:bg-green-700');
                        buttonElement.onclick = () => {
                            if (typeof openSendModal === 'function') {
                                openSendModal(participantId);
                            }
                        };
                    }
                    break;
                case 'rejected':
                    buttonElement.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Request Rejected</span>
                    `;
                    buttonElement.className = buttonElement.className.replace('bg-[#33595a] hover:bg-[#2C494A]', 'bg-red-600 cursor-not-allowed');
                    buttonElement.disabled = true;
                    buttonElement.onclick = null; // Remove onclick handler
                    break;
            }
        }
        
        // Request Already Sent Modal event listeners
        const closeAlreadySentModal = document.getElementById('close-already-sent-modal');
        const closeAlreadySentModalBtn = document.getElementById('close-already-sent-modal-btn');
        const alreadySentModal = document.getElementById('request-already-sent-modal');

        if (closeAlreadySentModal) {
            closeAlreadySentModal.addEventListener('click', function() {
                if (alreadySentModal) {
                    alreadySentModal.classList.add('hidden');
                    alreadySentModal.classList.remove('flex');
                }
            });
        }

        if (closeAlreadySentModalBtn) {
            closeAlreadySentModalBtn.addEventListener('click', function() {
                if (alreadySentModal) {
                    alreadySentModal.classList.add('hidden');
                    alreadySentModal.classList.remove('flex');
                }
            });
        }

        if (alreadySentModal) {
            alreadySentModal.addEventListener('click', function(e) {
                if (e.target === alreadySentModal) {
                    alreadySentModal.classList.add('hidden');
                    alreadySentModal.classList.remove('flex');
                }
            });
        }
        
        // Open send message modal for starting conversations
        window.openSendModal = function(participantId) {
            // For now, redirect to the matching view where the modal exists
            // In a full implementation, you might want to create the modal in the dashboard
            window.location.href = `/sc/participants-matching/${participantId}`;
        };
    </script>
</body>
</html>