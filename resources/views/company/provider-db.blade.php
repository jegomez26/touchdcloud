{{-- resources/views/company/provider-db.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/blue_logo.png') }}">
    <script src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

    <title>SIL Match - Provider Dashboard</title>
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
        
        /* Custom scrollbar styles for recent activity */
        .scrollbar-thin {
            scrollbar-width: thin;
        }
        
        .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
            background-color: #D1D5DB;
            border-radius: 0.5rem;
        }
        
        .scrollbar-track-gray-100::-webkit-scrollbar-track {
            background-color: #F3F4F6;
            border-radius: 0.5rem;
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background-color: #9CA3AF;
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
            <a href="{{ route('provider.dashboard') }}" class="text-3xl font-extrabold text-[#33595a] hover:text-[#3e4732] transition duration-300">
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
                        <button data-action="billing" class="block px-4 py-2 text-sm text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-receipt inline-block mr-2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"/><path d="M14 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg> Billing & Renewals
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

    {{-- Trial Period Notification Banner --}}
    <div x-data="trialNotificationData()" x-show="showTrialNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-full"
         class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 shadow-lg">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-medium" x-text="trialMessage"></p>
                    <p class="text-sm opacity-90" x-text="trialSubMessage"></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="convertTrial" 
                        class="bg-white text-blue-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 transition-colors"
                        x-show="trialActive">
                    Convert to Paid
                </button>
                <button @click="showTrialNotification = false" 
                        class="text-white hover:text-gray-200 transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

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
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('provider.participants.list') }}" data-section="my-participants" @click="checkSubscriptionAndNavigate('participants')" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-users mr-3"></i> My Participants
                </a>
                <a href="{{ route('provider.accommodations.index') }}" data-section="my-accommodations" @click="checkSubscriptionAndNavigate('accommodations')" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-home mr-3"></i> My Accommodations
                </a>
                <a href="{{ route('provider.enquiries.index') }}" data-section="enquiries" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-envelope mr-3"></i> Enquiries
                </a>
                <a href="{{ route('provider.participants.matching.index') }}" data-section="match-participants" @click="checkSubscriptionAndNavigate('matching')" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-link mr-3"></i> Match Participants
                </a>
                <a href="{{ route('provider.messages.index') }}" data-section="messages" @click="checkSubscriptionAndNavigate('messages')" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-comments mr-3"></i> Messages
                </a>
                <a href="{{ route('provider.match-requests.index') }}" data-section="match-requests" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-handshake mr-3"></i> Match Requests
                </a>
                <a href="{{ route('provider.support-center.index') }}" data-section="support-center" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
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
                <a href="{{ route('provider.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="#" data-section="my-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-users mr-3"></i> My Participants
                </a>
                <a href="{{ route('provider.accommodations.index') }}" data-section="my-accommodations" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-home mr-3"></i> My Accommodations
                </a>
                <a href="{{ route('provider.enquiries.index') }}" data-section="enquiries" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-envelope mr-3"></i> Enquiries
                </a>
                <a href="{{ route('provider.participants.matching.index') }}" data-section="match-participants" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-link mr-3"></i> Match Participants
                </a>
                <a href="#" data-section="messages" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>  Messages
                </a>
                <a href="{{ route('provider.match-requests.index') }}" data-section="match-requests" class="sidebar-link flex items-center w-full py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[#3e4732] hover:bg-[#e1e7dd] hover:text-[#33595a]">
                    <i class="fas fa-handshake mr-3"></i> Match Requests
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
                <!-- Provider Dashboard Content with Alpine.js -->
                <div class="min-h-screen" x-data="subscriptionData">
                    <!-- Main Content Area -->
                    @yield('main-content')
                    
                    <!-- Plans Selection Modal -->
                    <div x-show="showPlansModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="showPlansModal = false">
                        
                        <div x-show="showPlansModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                            
                            <!-- Modal Header -->
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-3xl font-bold text-gray-900">Choose Your Plan</h2>
                                        <p class="text-gray-600 mt-2">Select the perfect plan for your provider needs.</p>
                                    </div>
                                    <button @click="showPlansModal = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Trial Information Banner -->
                            <div x-show="highlightTrialInfo && trialInfo" 
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="mx-6 mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-lg font-semibold text-blue-900">Complete Your Subscription</h3>
                                        <div class="mt-2 text-sm text-blue-800">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="bg-white bg-opacity-50 rounded-lg p-3">
                                                    <div class="font-medium text-blue-900">Trial Start</div>
                                                    <div class="text-blue-700" x-text="trialInfo ? new Date(trialInfo.trial_start_date).toLocaleDateString() : ''"></div>
                                                </div>
                                                <div class="bg-white bg-opacity-50 rounded-lg p-3">
                                                    <div class="font-medium text-blue-900">Trial End</div>
                                                    <div class="text-blue-700" x-text="trialInfo ? new Date(trialInfo.trial_ends_at).toLocaleDateString() : ''"></div>
                                                </div>
                                                <div class="bg-white bg-opacity-50 rounded-lg p-3">
                                                    <div class="font-medium text-blue-900">Charging Starts</div>
                                                    <div class="text-blue-700" x-text="trialInfo ? new Date(trialInfo.charging_starts_at).toLocaleDateString() : ''"></div>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-blue-800">
                                                <strong>Note:</strong> You'll have <span x-text="trialInfo ? trialInfo.trial_days : ''"></span> days to explore all features. After the trial period, you'll be charged <span x-text="billingPeriod === 'monthly' ? (trialInfo ? '$' + trialInfo.monthly_price + '/month' : '') : (trialInfo ? '$' + trialInfo.yearly_price + '/year' : '')"></span>.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Plans Grid -->
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    @php
                                        $plans = \App\Models\Plan::active()->ordered()->get();
                                    @endphp
                                    
                                    @foreach($plans as $plan)
                                    <div class="relative bg-white rounded-xl shadow-lg border-2 {{ $plan->is_featured ? 'border-blue-500 ring-2 ring-blue-500 ring-opacity-50' : 'border-gray-200' }}"
                                         :class="highlightTrialInfo && selectedPlan == {{ $plan->id }} ? 'border-green-500 ring-2 ring-green-500 ring-opacity-50' : ''">
                                        @if($plan->is_featured)
                                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
                                        </div>
                                        @endif
                                        
                                        <!-- Trial Highlight Badge -->
                                        <div x-show="highlightTrialInfo && selectedPlan == {{ $plan->id }}" 
                                             class="absolute -top-4 right-4">
                                            <span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-medium">Trial Selected</span>
                                        </div>

                                        <div class="p-6">
                                            <!-- Plan Header -->
                                            <div class="text-center mb-6">
                                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                                <p class="text-gray-600">{{ $plan->description }}</p>
                                            </div>

                                            <!-- Pricing -->
                                            <div class="text-center mb-6">
                                                <div class="flex items-baseline justify-center">
                                                    <span class="text-5xl font-bold text-gray-900">${{ number_format($plan->monthly_price, 0) }}</span>
                                                    <span class="text-xl text-gray-500 ml-1">/month</span>
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    or ${{ number_format($plan->yearly_price, 0) }}/year 
                                                    <span class="text-green-600 font-medium">(Save ${{ number_format($plan->yearly_savings, 0) }})</span>
                                                </div>
                                            </div>

                                            <!-- Features -->
                                            <ul class="space-y-3 mb-8">
                                                @foreach($plan->features as $feature)
                                                <li class="flex items-start">
                                                    <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-gray-700">{{ $feature }}</span>
                                                </li>
                                                @endforeach
                                            </ul>

                                            <!-- Action Buttons -->
                                            <div class="space-y-3">
                                                <button @click="selectPlan({{ $plan->id }})" 
                                                        class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                                                    Subscribe Now
                                                </button>
                                                
                                                @if(in_array($plan->slug, ['growth', 'premium']))
                                                <p class="text-center text-sm text-gray-600">
                                                    with 14-day free trial
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Founding Partner Offer -->
                                <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6 text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Founding Partner Offer</h3>
                                    <p class="text-lg text-gray-700 mb-6">
                                        Be among the first 10 providers and get the Growth plan for just <span class="font-bold text-purple-600">$399/month</span> for 12 months!
                                    </p>
                                    <div class="text-sm text-gray-600">
                                        <p>Limited time offer • First 10 providers only</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Payment Modal -->
                    <div x-show="showPaymentModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="closePaymentModal()">
                        
                        <div x-show="showPaymentModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                            
                            <!-- Modal Header -->
                            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-3xl font-bold text-gray-900">Complete Your Subscription</h2>
                                        <p class="text-gray-600 mt-1">Secure payment powered by Stripe Elements</p>
                                    </div>
                                    <button @click="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Payment Form -->
                            <div class="p-6">
                                <form @submit.prevent="submitSubscription()" x-show="selectedPlan">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <!-- Left Column: Plan Details & Pricing -->
                                        <div>
                                            <!-- Selected Plan Summary -->
                                            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Plan</h3>
                                                <div class="flex items-center justify-between mb-4">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900" x-text="plans.find(p => p.id === selectedPlan)?.name || 'Plan'"></h4>
                                                        <p class="text-sm text-gray-600" x-text="plans.find(p => p.id === selectedPlan)?.description || ''"></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-2xl font-bold text-blue-600" x-text="billingPeriod === 'monthly' ? '$' + plans.find(p => p.id === selectedPlan)?.monthly_price : '$' + plans.find(p => p.id === selectedPlan)?.yearly_price"></div>
                                                        <div class="text-sm text-gray-500" x-text="billingPeriod === 'monthly' ? '/month' : '/year'"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Features List -->
                                                <div class="space-y-2">
                                                    <template x-for="feature in plans.find(p => p.id === selectedPlan)?.features || []" :key="feature">
                                                        <div class="flex items-center text-sm text-gray-700">
                                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span x-text="feature"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                    <!-- Billing Period Selection -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Billing Period</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label class="relative">
                                                <input type="radio" x-model="billingPeriod" value="monthly" class="sr-only">
                                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                                     :class="billingPeriod === 'monthly' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                                    <div class="text-center">
                                                        <div class="font-semibold text-gray-900">Monthly</div>
                                                                <div class="text-sm text-gray-600" x-text="'$' + plans.find(p => p.id === selectedPlan)?.monthly_price + '/month'"></div>
                                                    </div>
                                                </div>
                                            </label>
                                            <label class="relative">
                                                <input type="radio" x-model="billingPeriod" value="yearly" class="sr-only">
                                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                                     :class="billingPeriod === 'yearly' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                                    <div class="text-center">
                                                        <div class="font-semibold text-gray-900">Yearly</div>
                                                                <div class="text-sm text-gray-600" x-text="'$' + plans.find(p => p.id === selectedPlan)?.yearly_price + '/year'"></div>
                                                                <div class="text-xs text-green-600 font-medium mt-1" x-text="'Save $' + plans.find(p => p.id === selectedPlan)?.yearly_savings"></div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                            <!-- Pricing Breakdown -->
                                            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                                                <h4 class="font-semibold text-gray-900 mb-3">Pricing Breakdown</h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span>Plan Price:</span>
                                                        <span x-text="billingPeriod === 'monthly' ? '$' + plans.find(p => p.id === selectedPlan)?.monthly_price : '$' + plans.find(p => p.id === selectedPlan)?.yearly_price"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Billing Period:</span>
                                                        <span x-text="billingPeriod === 'monthly' ? 'Monthly' : 'Yearly'"></span>
                                                    </div>
                                                    <div class="flex justify-between font-semibold border-t pt-2">
                                                        <span>Total:</span>
                                                        <span x-text="billingPeriod === 'monthly' ? '$' + plans.find(p => p.id === selectedPlan)?.monthly_price + '/month' : '$' + plans.find(p => p.id === selectedPlan)?.yearly_price + '/year'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: Payment Information -->
                                        <div>
                                            <!-- Trial Information -->
                                            <div x-show="highlightTrialInfo && trialInfo" 
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                                 class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <h3 class="text-lg font-semibold text-green-900">Trial Period Details</h3>
                                                        <div class="mt-2 text-sm text-green-800">
                                                            <div class="space-y-2">
                                                                <div><strong>Trial Duration:</strong> <span x-text="trialInfo ? trialInfo.trial_days : ''"></span> days</div>
                                                                <div><strong>Trial Start:</strong> <span x-text="trialInfo ? new Date(trialInfo.trial_start_date).toLocaleDateString() : ''"></span></div>
                                                                <div><strong>Trial End:</strong> <span x-text="trialInfo ? new Date(trialInfo.trial_ends_at).toLocaleDateString() : ''"></span></div>
                                                                <div><strong>Charging Begins:</strong> <span x-text="trialInfo ? new Date(trialInfo.charging_starts_at).toLocaleDateString() : ''"></span></div>
                                                                <div><strong>After Trial:</strong> <span x-text="billingPeriod === 'monthly' ? (trialInfo ? '$' + trialInfo.monthly_price + '/month' : '') : (trialInfo ? '$' + trialInfo.yearly_price + '/year' : '')"></span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Stripe Payment Element -->
                                            <div class="mb-6">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                                                <div id="payment-element" class="mb-4">
                                                    <!-- Stripe Elements will be inserted here -->
                                                </div>
                                                <div id="card-errors" class="text-red-500 text-sm" role="alert"></div>
                                            </div>

                                    <!-- Terms and Conditions -->
                                    <div class="mb-6">
                                        <label class="flex items-start">
                                                    <input type="checkbox" class="mt-1 mr-3" required>
                                            <span class="text-sm text-gray-600">
                                                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>. I understand that my subscription will automatically renew unless cancelled.
                                            </span>
                                        </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex space-x-4 pt-6 border-t border-gray-200">
                                        <button type="button" @click="closePaymentModal()" 
                                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="loading"
                                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                                            <span x-show="!loading" class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                </svg>
                                                Complete Subscription
                                            </span>
                                            <span x-show="loading" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Processing Payment...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Management Modal -->
                    <div x-show="showSubscriptionManageModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="showSubscriptionManageModal = false">
                        
                        <div x-show="showSubscriptionManageModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                            
                            <!-- Modal Header -->
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-3xl font-bold text-gray-900">Manage Subscription</h2>
                                        <p class="text-gray-600 mt-2">View and manage your current subscription details.</p>
                                    </div>
                                    <button @click="showSubscriptionManageModal = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-6">
                                <template x-if="currentSubscription.has_subscription">
                                    <div class="space-y-6">
                                        <!-- Current Subscription Details -->
                                        <div class="bg-gray-50 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Subscription</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-500">Plan Name</label>
                                                    <p class="text-lg font-semibold text-gray-900" x-text="currentSubscription.display_name || currentSubscription.plan_name"></p>
                                                </div>
                                                <div>
                                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                                    <p class="text-lg font-semibold" 
                                                       :class="{
                                                           'text-green-600': currentSubscription.status === 'active',
                                                           'text-blue-600': currentSubscription.status === 'trialing',
                                                           'text-red-600': currentSubscription.status === 'trial_ended',
                                                           'text-gray-600': currentSubscription.status === 'inactive'
                                                       }" 
                                                       x-text="currentSubscription.status === 'trialing' ? 'Trial Active' : 
                                                              currentSubscription.status === 'active' ? 'Active' :
                                                              currentSubscription.status === 'trial_ended' ? 'Trial Ended' : 'Inactive'"></p>
                                                </div>
                                                <div x-show="currentSubscription.trial_active">
                                                    <label class="text-sm font-medium text-gray-500">Trial Remaining</label>
                                                    <p class="text-lg font-semibold text-blue-600" x-text="currentSubscription.trial_remaining_days + ' days'"></p>
                                                </div>
                                                <div x-show="currentSubscription.trial_active">
                                                    <label class="text-sm font-medium text-gray-500">Trial Progress</label>
                                                    <div class="mt-2">
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                                                 :style="'width: ' + currentSubscription.trial_progress + '%'"></div>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1" x-text="Math.round(currentSubscription.trial_progress) + '% complete'"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Trial Warning Banner -->
                                            <div x-show="currentSubscription.trial_ending_soon" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-yellow-800">Trial Ending Soon</h4>
                                                        <p class="text-sm text-yellow-700">Your trial ends in <span x-text="currentSubscription.trial_remaining_days"></span> days. Subscribe now to continue using premium features.</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <button @click="showPlansModal = true; showSubscriptionManageModal = false" 
                                                            class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-yellow-700 transition-colors">
                                                        Subscribe Now
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Trial Ended Banner -->
                                            <div x-show="currentSubscription.trial_ended && !currentSubscription.has_subscription" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-red-800">Trial Ended</h4>
                                                        <p class="text-sm text-red-700">Your trial has ended. Subscribe now to regain access to premium features.</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <button @click="showPlansModal = true; showSubscriptionManageModal = false" 
                                                            class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                                                        Subscribe Now
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Features Access -->
                                        <div class="bg-gray-50 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Feature Access</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-sm text-gray-700">Messaging</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-sm text-gray-700">Participant Matching</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-sm text-gray-700">Accommodations</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex space-x-4">
                                            <button @click="showPlansModal = true; showSubscriptionManageModal = false" 
                                                    class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                                Change Plan
                                            </button>
                                            <button @click="showCancelConfirmModal = true" 
                                                    class="flex-1 bg-red-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                                Cancel Subscription
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!currentSubscription.has_subscription">
                                    <div class="text-center py-8">
                                        <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Subscription</h3>
                                        <p class="text-gray-600 mb-6">You don't have an active subscription. Choose a plan to get started.</p>
                                        <button @click="showPlansModal = true; showSubscriptionManageModal = false" 
                                                class="bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                            Choose a Plan
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel Confirmation Modal -->
                    <div x-show="showCancelConfirmModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="showCancelConfirmModal = false">
                        
                        <div x-show="showCancelConfirmModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                            
                            <!-- Modal Header -->
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-gray-900">Cancel Subscription</h2>
                                    <button @click="showCancelConfirmModal = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Content -->
                            <div class="p-6">
                                <div class="text-center">
                                    <svg class="h-16 w-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Are you sure?</h3>
                                    <p class="text-gray-600 mb-6">
                                        This will cancel your subscription at the end of your current billing period. 
                                        You'll lose access to premium features immediately.
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-4">
                                    <button @click="showCancelConfirmModal = false" 
                                            class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                                        Keep Subscription
                                    </button>
                                    <button @click="cancelSubscription()" 
                                            class="flex-1 bg-red-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                        Yes, Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Modal -->
                    <div x-show="showSuccessModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="showSuccessModal = false">
                        
                        <div x-show="showSuccessModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                            
                            <!-- Modal Content -->
                            <div class="p-6 text-center">
                                <svg class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                                <p class="text-gray-600 mb-6" x-text="successMessage"></p>
                                <button @click="showSuccessModal = false" 
                                        class="bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition-colors">
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Error Modal -->
                    <div x-show="showErrorModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                         @click.self="showErrorModal = false">
                        
                        <div x-show="showErrorModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                            
                            <!-- Modal Content -->
                            <div class="p-6 text-center">
                                <svg class="h-16 w-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                                <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
                                <button @click="showErrorModal = false" 
                                        class="bg-red-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Modal -->
                    <div x-show="showLoadingModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                        
                        <div x-show="showLoadingModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                            
                            <!-- Modal Content -->
                            <div class="p-6 text-center">
                                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Processing...</h3>
                                <p class="text-gray-600">Please wait while we process your request.</p>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== END COMPREHENSIVE MODAL SYSTEM ==================== -->
                </div>
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
    {{-- Stripe.js --}}
    <script src="https://js.stripe.com/clover/stripe.js"></script>

    @stack('scripts')

    @php
        $plansData = \App\Models\Plan::active()->ordered()->get()->map(function($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'monthly_price' => $plan->monthly_price,
                'yearly_price' => $plan->yearly_price,
                'yearly_savings' => $plan->yearly_savings,
                'participant_profile_limit' => $plan->participant_profile_limit,
                'accommodation_listing_limit' => $plan->accommodation_listing_limit,
                'features' => $plan->features,
                'is_featured' => $plan->is_featured,
            ];
        });
    @endphp

    <script>
        // Set up global variables for Alpine.js
        window.subscriptionStatus = @json($subscriptionStatus);
        window.plans = @json($plansData);
        
        // Alpine.js data for subscription functionality
        document.addEventListener('alpine:init', () => {
            // Trial notification component
            Alpine.data('trialNotificationData', () => ({
                showTrialNotification: false,
                trialActive: false,
                trialMessage: '',
                trialSubMessage: '',
                
                init() {
                    this.checkTrialStatus();
                },
                
                checkTrialStatus() {
                    const subscription = window.subscriptionStatus;
                    if (subscription && subscription.trial_active) {
                        this.trialActive = true;
                        this.showTrialNotification = true;
                        
                        if (subscription.trial_ending_soon) {
                            this.trialMessage = `Trial ending in ${subscription.trial_remaining_days} days`;
                            this.trialSubMessage = 'Subscribe now to continue using premium features';
                        } else {
                            this.trialMessage = `You're in a ${subscription.trial_remaining_days}-day trial`;
                            this.trialSubMessage = 'Enjoy exploring our premium features!';
                        }
                    } else if (subscription && subscription.trial_ended && !subscription.has_subscription) {
                        this.trialActive = false;
                        this.showTrialNotification = true;
                        this.trialMessage = 'Your trial has ended';
                        this.trialSubMessage = 'Subscribe now to regain access to premium features';
                    }
                },
                
                convertTrial() {
                    // This will be handled by the subscription data component
                    this.$dispatch('convert-trial');
                }
            }));

            Alpine.data('subscriptionData', () => ({
                // Modal states
                showPlansModal: false,
                showPaymentModal: false,
                showSubscriptionManageModal: false,
                showCancelConfirmModal: false,
                showSuccessModal: false,
                showErrorModal: false,
                showLoadingModal: false,
                
                // Data
                selectedPlan: null,
                billingPeriod: 'monthly',
                loading: false,
                successMessage: '',
                errorMessage: '',
                trialInfo: null,
                highlightTrialInfo: false,
                
                // Subscription data
                currentSubscription: window.subscriptionStatus || {},
                plans: window.plans || [],
                
                // Stripe Elements
                stripe: null,
                elements: null,
                paymentElement: null,
                paymentIntentClientSecret: null,
                currentPlanDetails: null,
                paymentElementsReady: false,
                isInitializingElements: false,
                
                // Watchers
                '$watch': {
                    'billingPeriod': function(newValue, oldValue) {
                        if (this.highlightTrialInfo && this.trialInfo) {
                            this.updateTrialInfoForBillingPeriod();
                        }
                        // Reinitialize payment elements when billing period changes
                        if (this.showPaymentModal && this.selectedPlan && oldValue && !this.isInitializingElements) {
                            // Reset ready state
                            this.paymentElementsReady = false;
                            this.initializePaymentElements();
                        }
                    }
                },
                
                // Methods
                init() {
                    // Initialize Stripe
                    const stripeKey = '{{ config("services.stripe.key") }}';
                    if (stripeKey && typeof Stripe !== 'undefined') {
                        this.stripe = Stripe(stripeKey);
                    }
                    
                    // Debug: Log initial state
                    console.log('Alpine.js initialized with modal states:', {
                        showPlansModal: this.showPlansModal,
                        showPaymentModal: this.showPaymentModal,
                        showSubscriptionManageModal: this.showSubscriptionManageModal
                    });
                },
                
                async initializePaymentElements() {
                    if (!this.stripe || !this.selectedPlan || this.isInitializingElements) {
                        return;
                    }
                    
                    try {
                        this.isInitializingElements = true;
                        this.paymentElementsReady = false;
                        this.loading = true;
                        
                        // Wait a bit to ensure DOM is ready
                        await this.$nextTick();
                        
                        // Check if payment element container exists
                        const paymentElementContainer = document.getElementById('payment-element');
                        if (!paymentElementContainer) {
                            throw new Error('Payment element container not found');
                        }
                        
                        // Create payment intent
                        const response = await fetch('{{ route("subscription.create-payment-intent") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                plan_id: this.selectedPlan,
                                billing_period: this.billingPeriod,
                                use_promo: false
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to create payment intent');
                        }
                        
                        const data = await response.json();
                        
                        if (!data.success) {
                            throw new Error(data.error || 'Failed to initialize payment');
                        }
                        
                        this.paymentIntentClientSecret = data.client_secret;
                        this.currentPlanDetails = data.plan;
                        
                        // Destroy existing elements if any
                        if (this.paymentElement) {
                            try {
                                this.paymentElement.destroy();
                            } catch (e) {
                                console.warn('Error destroying payment element:', e);
                            }
                            this.paymentElement = null;
                        }
                        
                        if (this.elements) {
                            try {
                                this.elements.destroy();
                            } catch (e) {
                                console.warn('Error destroying elements:', e);
                            }
                            this.elements = null;
                        }
                        
                        // Clear the container
                        paymentElementContainer.innerHTML = '';
                        
                        // Initialize Stripe Elements
                        this.elements = this.stripe.elements({ 
                            clientSecret: this.paymentIntentClientSecret 
                        });
                        
                        // Create and mount payment element
                        this.paymentElement = this.elements.create('payment', {
                            layout: 'tabs',
                            defaultValues: {
                                billingDetails: {
                                    name: '{{ Auth::user()->first_name . " " . Auth::user()->last_name }}',
                                    email: '{{ Auth::user()->email }}',
                                    address: {
                                        country: 'AU'
                                    }
                                }
                            },
                            fields: {
                                billingDetails: {
                                    country: 'auto'
                                }
                            }
                        });
                        
                        // Mount the element
                        this.paymentElement.mount('#payment-element');
                        
                        // Wait for element to be fully mounted and ready
                        await new Promise((resolve) => {
                            // Check if element is mounted by checking for Stripe Elements classes
                            let attempts = 0;
                            const maxAttempts = 20; // 2 seconds max
                            
                            const checkMounted = setInterval(() => {
                                attempts++;
                                const container = document.getElementById('payment-element');
                                const isMounted = container && (
                                    container.querySelector('.StripeElement') || 
                                    container.querySelector('[data-testid]') ||
                                    container.children.length > 0
                                );
                                
                                if (isMounted || attempts >= maxAttempts) {
                                    clearInterval(checkMounted);
                                    this.paymentElementsReady = true;
                                    this.isInitializingElements = false;
                                    this.loading = false;
                                    resolve();
                                }
                            }, 100);
                        });
                        
                    } catch (error) {
                        console.error('Error initializing payment elements:', error);
                        this.isInitializingElements = false;
                        this.paymentElementsReady = false;
                        this.loading = false;
                        this.errorMessage = error.message || 'Failed to initialize payment form. Please try again.';
                        this.showErrorModal = true;
                    }
                },
                
                loadSubscriptionData() {
                    // This method can be used to refresh subscription data
                    // For now, we'll just reload the page
                    window.location.reload();
                },
                
                checkSubscriptionAndNavigate(feature) {
                    // Check if user has subscription for the specific feature
                    const hasAccess = this.checkFeatureAccess(feature);
                    
                    if (!hasAccess) {
                        // Prevent default navigation
                        event.preventDefault();
                        // Show subscription modal
                        this.showPlansModal = true;
                    }
                    // If has access, let the default navigation happen
                },
                
                checkFeatureAccess(feature) {
                    switch(feature) {
                        case 'participants':
                            return this.currentSubscription.can_access_participants || false;
                        case 'accommodations':
                            return this.currentSubscription.can_access_accommodations || false;
                        case 'matching':
                            return this.currentSubscription.can_access_matching || false;
                        case 'messages':
                            return this.currentSubscription.can_access_messaging || false;
                        default:
                            return false;
                    }
                },
                
                selectPlan(planId) {
                    this.selectedPlan = parseInt(planId);
                    this.billingPeriod = 'monthly';
                    
                    // Check if this is a Growth or Premium plan to set trial info
                    const selectedPlanData = this.plans.find(p => p.id === parseInt(planId));
                    if (selectedPlanData && ['growth', 'premium'].includes(selectedPlanData.slug)) {
                        // Set trial info for Growth and Premium plans
                        this.trialInfo = {
                            plan_id: selectedPlanData.id,
                            plan_name: selectedPlanData.name,
                            trial_days: 14,
                            trial_ends_at: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString(),
                            trial_start_date: new Date().toISOString(),
                            charging_starts_at: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString(),
                            monthly_price: selectedPlanData.monthly_price,
                            yearly_price: selectedPlanData.yearly_price,
                            yearly_savings: selectedPlanData.yearly_savings,
                        };
                        this.highlightTrialInfo = true;
                    } else {
                        // Clear trial info for other plans
                        this.trialInfo = null;
                        this.highlightTrialInfo = false;
                    }
                    
                    // Go directly to payment modal
                    this.showPlansModal = false;
                    this.showPaymentModal = true;
                    
                    // Reset payment elements state
                    this.paymentElementsReady = false;
                    this.isInitializingElements = false;
                    
                    // Initialize payment elements when modal opens
                    this.$nextTick(() => {
                        // Wait a bit more to ensure modal is fully rendered
                        setTimeout(() => {
                            this.initializePaymentElements();
                        }, 300);
                    });
                },
                
                // Update trial info when billing period changes
                updateTrialInfoForBillingPeriod() {
                    if (this.trialInfo && this.highlightTrialInfo) {
                        // Trial info is already set, just ensure it's displayed correctly
                        // The pricing will be handled by the reactive Alpine.js expressions
                    }
                },
                
                startTrial(planId) {
                    this.showLoadingModal = true;
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('plan_id', planId);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    fetch('{{ route("subscription.trial") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        this.showLoadingModal = false;
                        
                        if (data.success) {
                            this.successMessage = data.message;
                            this.showSuccessModal = true;
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else if (data.show_subscription_modal && data.trial_info) {
                            // Show subscription modal with trial information highlighted
                            this.selectedPlan = data.trial_info.plan_id;
                            this.billingPeriod = 'monthly'; // Default to monthly for trial
                            this.trialInfo = data.trial_info;
                            this.showPlansModal = true;
                            this.highlightTrialInfo = true;
                        } else {
                            this.errorMessage = data.error || 'An error occurred. Please try again.';
                            this.showErrorModal = true;
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        this.showLoadingModal = false;
                        console.error('Error:', error);
                        this.errorMessage = 'An error occurred. Please try again.';
                        this.showErrorModal = true;
                    });
                },
                
                async convertTrial() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.showErrorModal = false;
                    
                    try {
                        const subscription = this.currentSubscription;
                        if (!subscription || !subscription.trial_active) {
                            throw new Error('No active trial found to convert.');
                        }
                        
                        // Create form data for trial conversion
                        const formData = new FormData();
                        formData.append('subscription_id', subscription.id);
                        formData.append('billing_period', subscription.billing_period || 'monthly');
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        
                        const response = await fetch('{{ route("subscription.convert-trial") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({ error: 'Network error occurred' }));
                            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success && data.checkout_url) {
                            // Immediately redirect to Stripe Checkout (hosted page)
                            // Payment will be processed there, then user returns to checkout success page
                            window.location.href = data.checkout_url;
                        } else {
                            this.loading = false;
                            this.errorMessage = data.error || 'An error occurred. Please try again.';
                            this.showErrorModal = true;
                        }
                    } catch (error) {
                        this.loading = false;
                        console.error('Error:', error);
                        this.errorMessage = error.message || 'An error occurred. Please try again.';
                        this.showErrorModal = true;
                    }
                },
                
                async submitSubscription() {
                    // Reset any previous messages
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.showErrorModal = false;
                    this.showSuccessModal = false;
                    
                    // Check if elements are ready
                    if (!this.stripe || !this.elements || !this.paymentElement || !this.paymentIntentClientSecret) {
                        this.errorMessage = 'Payment form not initialized. Please wait a moment and try again.';
                        this.showErrorModal = true;
                        return;
                    }
                    
                    // Check if elements are still initializing
                    if (this.isInitializingElements || !this.paymentElementsReady) {
                        this.errorMessage = 'Payment form is still loading. Please wait a moment and try again.';
                        this.showErrorModal = true;
                        return;
                    }
                    
                    // Verify element is mounted
                    const paymentElementContainer = document.getElementById('payment-element');
                    if (!paymentElementContainer || paymentElementContainer.children.length === 0) {
                        this.errorMessage = 'Payment form is not ready. Please refresh the page and try again.';
                        this.showErrorModal = true;
                        return;
                    }
                    
                    this.loading = true;
                    
                    try {
                        // Verify element is still mounted before proceeding
                        const paymentElementContainer = document.getElementById('payment-element');
                        if (!this.paymentElement || !paymentElementContainer || !paymentElementContainer.hasChildNodes()) {
                            throw new Error('Payment form is not ready. Please refresh and try again.');
                        }
                        
                        // Use submit() first to validate, then confirmPayment
                        const { error: submitError } = await this.elements.submit();
                        if (submitError) {
                            const errorElement = document.getElementById('card-errors');
                            if (errorElement) {
                                errorElement.textContent = submitError.message;
                            }
                            this.loading = false;
                            this.errorMessage = submitError.message;
                            this.showErrorModal = true;
                            return;
                        }
                        
                        // Confirm payment with Stripe - this will handle payment confirmation
                        const { error, paymentIntent } = await this.stripe.confirmPayment({
                            elements: this.elements,
                            confirmParams: {
                                return_url: '{{ route("subscription.checkout.success") }}',
                            },
                            redirect: 'if_required' // Do not redirect automatically
                        });
                        
                        if (error) {
                            // Show error to customer
                            const errorElement = document.getElementById('card-errors');
                            if (errorElement) {
                                errorElement.textContent = error.message;
                            }
                            this.loading = false;
                            this.errorMessage = error.message;
                            this.showErrorModal = true;
                            return;
                        }
                        
                        // Payment succeeded - confirm subscription on server
                        if (paymentIntent && (paymentIntent.status === 'succeeded' || paymentIntent.status === 'requires_capture')) {
                            await this.confirmSubscriptionOnServer(paymentIntent.id);
                        } else {
                            this.loading = false;
                            this.errorMessage = 'Payment was not completed. Status: ' + (paymentIntent?.status || 'unknown');
                            this.showErrorModal = true;
                        }
                    } catch (error) {
                        this.loading = false;
                        console.error('Error:', error);
                        this.errorMessage = error.message || 'An error occurred. Please try again.';
                        this.showErrorModal = true;
                    }
                },
                
                closePaymentModal() {
                    // Clean up payment elements before closing
                    if (this.paymentElement) {
                        try {
                            this.paymentElement.destroy();
                        } catch (e) {
                            console.warn('Error destroying payment element:', e);
                        }
                        this.paymentElement = null;
                    }
                    
                    if (this.elements) {
                        try {
                            this.elements.destroy();
                        } catch (e) {
                            console.warn('Error destroying elements:', e);
                        }
                        this.elements = null;
                    }
                    
                    // Reset state
                    this.paymentElementsReady = false;
                    this.isInitializingElements = false;
                    this.paymentIntentClientSecret = null;
                    this.currentPlanDetails = null;
                    
                    // Clear the container
                    const paymentElementContainer = document.getElementById('payment-element');
                    if (paymentElementContainer) {
                        paymentElementContainer.innerHTML = '';
                    }
                    
                    // Clear errors
                    const errorElement = document.getElementById('card-errors');
                    if (errorElement) {
                        errorElement.textContent = '';
                    }
                    
                    this.showPaymentModal = false;
                },
                
                async confirmSubscriptionOnServer(paymentIntentId) {
                    try {
                        const response = await fetch('{{ route("subscription.confirm-subscription") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                payment_intent_id: paymentIntentId,
                                plan_id: this.currentPlanDetails?.id || this.selectedPlan,
                                billing_period: this.currentPlanDetails?.billing_period || this.billingPeriod,
                            })
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({ error: 'Network error occurred' }));
                            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.loading = false;
                            this.closePaymentModal();
                            this.successMessage = data.message || 'Subscription activated successfully!';
                            this.showSuccessModal = true;
                            
                            // Reload page after 2 seconds to update subscription status
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.loading = false;
                            this.errorMessage = data.error || 'Failed to finalize subscription.';
                            this.showErrorModal = true;
                        }
                    } catch (error) {
                        this.loading = false;
                        console.error('Error confirming subscription:', error);
                        this.errorMessage = error.message || 'An error occurred while finalizing your subscription.';
                        this.showErrorModal = true;
                    }
                },
                
                cancelSubscription() {
                    // Reset any previous messages
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.showErrorModal = false;
                    this.showSuccessModal = false;
                    
                    this.showLoadingModal = true;
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    fetch('{{ route("subscription.cancel") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        this.showLoadingModal = false;
                        
                        if (data.success) {
                            this.showCancelConfirmModal = false;
                            this.successMessage = data.message;
                            this.showSuccessModal = true;
                            
                            // Update the current subscription data
                            this.loadSubscriptionData();
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.errorMessage = data.error || 'An error occurred. Please try again.';
                            this.showErrorModal = true;
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        this.showLoadingModal = false;
                        console.error('Error:', error);
                        this.errorMessage = 'An error occurred. Please try again.';
                        this.showErrorModal = true;
                    });
                }
            }));
        });
    </script>

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
                        window.location.href = '{{ route("provider.dashboard") }}'; // Direct to provider dashboard for profile
                    } else if (action === 'billing') {
                        // Navigate to billing page
                        window.location.href = '{{ route("provider.billing") }}';
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
                    } else if (linkHref === '{{ route('provider.participants.list', [], false) }}' && currentPath.startsWith('{{ route('provider.participants.list', [], false) }}') && !currentPath.includes('/participants-matching')) {
                        // Special handling for /participants routes (e.g., /participants/123) but exclude matching routes
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.participants.matching.index', [], false) }}' && currentPath.startsWith('{{ route('provider.participants.matching.index', [], false) }}')) {
                        // Special handling for /participants-matching routes
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.messages.index', [], false) }}' && currentPath.startsWith('{{ route('provider.messages.index', [], false) }}')) {
                        // Special handling for /messages routes
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.dashboard', [], false) }}' && currentPath.includes('/dashboard')) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.accommodations.index', [], false) }}' && currentPath.includes('/accommodations')) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.enquiries.index', [], false) }}' && currentPath.includes('/enquiries')) {
                        link.classList.add('active');
                    } else if (linkHref === '{{ route('provider.support-center.index', [], false) }}' && currentPath.includes('/support-center')) {
                        link.classList.add('active');
                    }
                });

                // If no specific link matches, default to dashboard if on the root of provider path
                if (!document.querySelector('.sidebar-link.active') && (currentPath === '/provider' || currentPath === '{{ route("provider.dashboard", [], false) }}')) {
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
                console.error('Error:', error);
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

        // Provider Dashboard Functions - Now handled by Alpine.js methods
    </script>


    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Leaflet for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    // Subscription functions - Now handled by Alpine.js methods

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Gender Distribution Pie Chart
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx) {
            const genderData = @json($participantsByGender ?? []);
            const genderLabels = genderData.map(item => item.gender_identity || 'Not Specified');
            const genderCounts = genderData.map(item => item.count);
            
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: genderCounts,
                        backgroundColor: [
                            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Disability Types Bar Chart
        const disabilityCtx = document.getElementById('disabilityChart');
        if (disabilityCtx) {
            const disabilityData = @json($participantsByDisability ?? []);
            const disabilityLabels = disabilityData.map(item => item.primary_disability || 'Not Specified');
            const disabilityCounts = disabilityData.map(item => item.count);
            
            new Chart(disabilityCtx, {
                type: 'bar',
                data: {
                    labels: disabilityLabels,
                    datasets: [{
                        label: 'Participants',
                        data: disabilityCounts,
                        backgroundColor: '#F59E0B',
                        borderColor: '#D97706',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Initialize Map
        const mapElement = document.getElementById('map');
        if (mapElement) {
            // Initialize map centered on Australia
            const map = L.map('map').setView([-25.2744, 133.7751], 4);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add markers for participant locations
            const participantsByState = @json($participantsByState ?? []);
            
            // Sample coordinates for Australian states
            const stateCoordinates = {
                'NSW': [-33.8688, 151.2093],
                'VIC': [-37.8136, 144.9631],
                'QLD': [-27.4698, 153.0251],
                'WA': [-31.9505, 115.8605],
                'SA': [-34.9285, 138.6007],
                'TAS': [-42.8821, 147.3272],
                'NT': [-12.4634, 130.8456],
                'ACT': [-35.2809, 149.1300]
            };
            
            // Add markers for states with participants
            participantsByState.forEach(state => {
                const coords = stateCoordinates[state.state];
                if (coords) {
                    L.circleMarker(coords, {
                        radius: Math.max(5, state.count * 2),
                        fillColor: '#3B82F6',
                        color: '#1E40AF',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.7
                    }).addTo(map).bindPopup(`
                        <strong>${state.state}</strong><br>
                        Participants: ${state.count}
                    `);
                }
            });
            
            // Add a legend
            const legend = L.control({position: 'bottomright'});
            legend.onAdd = function (map) {
                const div = L.DomUtil.create('div', 'info legend');
                div.innerHTML = `
                    <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <h4 style="margin: 0 0 5px 0; font-size: 14px;">Participants by State</h4>
                        <div style="font-size: 12px;">
                            Circle size represents participant count
                        </div>
                    </div>
                `;
                return div;
            };
            legend.addTo(map);
        }
        
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
                            window.location.href = `/provider/messages/${conversationId}`;
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
            window.location.href = `/provider/participants-matching/${participantId}`;
        };
    });
    </script>

    @include('components.modals')
</body>
</html>
