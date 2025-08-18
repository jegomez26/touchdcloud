<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touch D Cloud - Participant Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>
        window.currentRouteName = "{{ Route::currentRouteName() }}";
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /* Main background color from your palette */
            background-color: #F9FAFB; /* secondary-bg */
            color: #374151; /* text-dark */
        }

        /* Modernized Sidebar Links */
        .sidebar-link {
            transition: all 0.25s ease-in-out; /* Slightly longer transition */
            padding-left: 2rem; /* Increased padding for wider sidebar */
            padding-right: 1.5rem; /* Standard padding-right for non-active links */
            border-radius: 0.75rem; /* More rounded */
            display: flex; /* Make it a flex container to align icon and text */
            align-items: center;
            position: relative; /* For potential future hover effects like underlines */
            overflow: hidden; /* To keep hover effects within bounds */
        }

        .sidebar-link.active {
            background-color: #2C494A; /* primary-dark */
            color: #ffffff; /* custom-white */
            font-weight: 600;
            
            /* New: Extend active color to the right edge */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            padding-right: 1.5rem; /* Remove right padding for active state */
            /* Crucial change: Negative right margin to fill the parent's padding */
            margin-right: 0; /* This value should match the parent's right padding or the padding-right of the non-active link */
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

        /* General Scrollbar for modern look */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #E5E7EB; /* border-light */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #6B7280; /* text-light */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #2C494A; /* primary-dark */
        }

        /* Chat bubble styles */
        .chat-bubble {
            max-width: 75%;
            padding: 0.85rem 1.15rem; /* Slightly more padding */
            border-radius: 1rem; /* More rounded corners */
            margin-bottom: 0.6rem; /* Slightly more margin */
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08); /* A bit more prominent shadow */
            transition: transform 0.2s ease-out;
        }
        .chat-bubble:hover {
            transform: translateY(-2px); /* Slight lift on hover */
        }
        .chat-bubble.incoming {
            background-color: #F3F4F6; /* chat-incoming */
            color: #374151; /* text-dark */
            align-self: flex-start;
            border-bottom-left-radius: 0.35rem; /* Keep a slight straight edge */
        }
        .chat-bubble.outgoing {
            background-color: #33595a; /* chat-outgoing */
            color: #2C494A; /* primary-dark */
            align-self: flex-end;
            border-bottom-right-radius: 0.35rem; /* Keep a slight straight edge */
        }
        .chat-message-time {
            font-size: 0.75rem;
            color: #6B7280; /* text-light */
            margin-top: 0.25rem;
        }
        .chat-message-sender {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .chat-bubble.outgoing .chat-message-sender,
        .chat-bubble.outgoing .chat-message-time {
            color: #51797B; /* primary-light, for legibility on light outgoing bubble */
        }

        /* Flatpickr Calendar */
        .flatpickr-calendar {
            background-color: #ffffff; /* custom-white */
            border-radius: 0.75rem;
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.15), 0 6px 12px -3px rgba(0, 0, 0, 0.08); /* Stronger shadow */
            border: 1px solid #E5E7EB; /* border-light */
            font-family: 'Inter', sans-serif;
            color: #374151; /* text-dark */
            padding: 1rem;
        }
        .flatpickr-months .flatpickr-month {
            color: #2C494A; /* primary-dark */
            font-weight: 600;
        }
        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: #6B7280; /* text-light */
            fill: #6B7280; /* text-light */
            opacity: 0.8;
            transition: opacity 0.25s ease-in-out, transform 0.25s ease-in-out; /* Added transform */
        }
        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            opacity: 1;
            color: #2C494A; /* primary-dark */
            fill: #2C494A; /* primary-dark */
            transform: scale(1.1); /* Slight enlarge on hover */
        }
        .flatpickr-weekdays {
            margin-top: 0.5rem;
        }
        .flatpickr-weekday {
            color: #6B7280; /* text-light */
            font-weight: 500;
            font-size: 0.875rem;
        }
        .flatpickr-day {
            color: #374151; /* text-dark */
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 2.25rem;
            height: 2.25rem;
            width: 2.25rem;
            margin: 0.125rem;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-radius 0.2s ease-in-out, transform 0.1s ease-out;
        }
        .flatpickr-day:hover {
            background-color: #E5E7EB; /* border-light */
            border-radius: 0.5rem;
            transform: translateY(-1px); /* Lift on hover */
        }
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange,
        .flatpickr-day.endRange:hover {
            background-color: #2C494A; /* primary-dark */
            color: #ffffff; /* custom-white */
            border-color: #2C494A; /* primary-dark */
            border-radius: 0.5rem;
            transform: scale(1.03); /* Slight scale for selected */
        }
        .flatpickr-day.today {
            border-color: #FBBF24; /* accent-yellow */
            color: #FBBF24; /* accent-yellow */
            font-weight: 600;
        }
        .flatpickr-day.today:hover {
            background-color: rgba(251, 191, 36, 0.1); /* Light tint of accent-yellow */
        }
        .flatpickr-day.today.selected {
            background-color: #2C494A; /* primary-dark */
            color: #ffffff; /* custom-white */
            border-color: #2C494A; /* primary-dark */
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #6B7280; /* text-light */
            opacity: 0.5;
            background-color: transparent;
            cursor: not-allowed;
            transform: none; /* No lift for disabled */
        }
        .flatpickr-clear, .flatpickr-today-button {
            color: #FBBF24; /* accent-yellow */
            font-weight: 600;
            transition: color 0.2s ease-in-out, transform 0.1s ease-out;
        }
        .flatpickr-clear:hover, .flatpickr-today-button:hover {
            color: #b37e3d; /* custom-ochre-darker for a deeper yellow */
            transform: scale(1.05); /* Pop on hover */
        }
        .flatpickr-input[readonly] {
            cursor: pointer;
        }
        .flatpickr-calendar .numInputWrapper span,
        .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthOption,
        .flatpickr-calendar .flatpickr-current-month .numInput {
            font-family: 'Inter', sans-serif;
        }

        /* Overlay Styling */
        #success-overlay, #error-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); /* Faster, more controlled fade */
        }
        #success-overlay.active, #error-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        #success-overlay > div, #error-overlay > div {
            transform: translateY(-24px) scale(0.9); /* Start further, smaller */
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); /* Slower, more impactful pop */
        }
        #success-overlay.active > div, #error-overlay.active > div {
            transform: translateY(0) scale(1);
        }

        /* Full Row Select for Sidebar Links (Refined) */
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
<body class="min-h-screen bg-secondary-bg text-text-dark flex flex-col">

    <header class="bg-custom-white shadow-md p-4 flex items-center justify-between z-30 sticky top-0 w-full h-16 border-b border-border-light">
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-text-dark">Participant Dashboard</h1>
            <button id="mobile-menu-button" class="text-text-light focus:outline-none p-2 rounded-md hover:bg-border-light transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-primary-dark hover:text-text-dark transition duration-300 flex items-center">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }}" class="h-10 inline-block align-middle mr-3">
            </a>
            <div class="flex items-center space-x-4 relative">
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-border-light focus:outline-none focus:ring-2 focus:ring-primary-dark focus:border-transparent text-sm w-64 transition-all duration-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-text-light"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <button class="text-text-light hover:text-text-dark focus:outline-none p-2 rounded-md hover:bg-border-light transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-text-light hover:text-text-dark focus:outline-none p-2 rounded-md hover:bg-border-light transition-colors duration-200">
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
                            $bgColor = '2C494A'; // primary-dark
                            $textColor = 'ffffff'; // custom-white
                        @endphp
                        <img src="https://placehold.co/32x32/{{ $bgColor }}/{{ $textColor }}?text={{ $initials }}" alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-primary-dark">
                        {{-- Display the logged-in user's name --}}
                        <span class="font-medium text-text-dark hidden sm:inline">
                            {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-custom-white rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30 transition-all duration-300 ease-out transform origin-top-right scale-95 opacity-0">
                        <a href="{{ route('indiv.profile.basic-details') }}" class="block px-4 py-2 text-sm text-text-dark hover:bg-border-light hover:text-primary-dark w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </a>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-text-dark hover:bg-border-light hover:text-primary-dark w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-border-light">
                        <button data-action="logout" class="block px-4 py-2 text-sm text-[#ef4444] hover:bg-secondary-bg w-full text-left transition-colors duration-150 rounded-md" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
        <aside id="sidebar" class="hidden md:flex flex-col fixed top-16 left-0 h-[calc(100vh-64px)] w-72 bg-custom-white text-text-dark p-6 space-y-8 border-r border-border-light z-20 transition-all duration-300 ease-in-out">
            <div class="flex items-center justify-center mb-8">
                {{-- No close button needed for desktop fixed sidebar --}}
            </div>
            {{-- Navigation menu: this itself should not scroll --}}
            <nav class="space-y-3 overflow-y-auto pr-2" id="sidebar-nav-container">
                <p class="text-xs font-semibold text-text-light uppercase mb-2 px-4">Menu</p>
                <a href="{{ route('indiv.dashboard') }}"
                    data-active-route="indiv.dashboard"
                    data-section="dashboard" class="sidebar-link flex items-center w-full py-3 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                <a href="#"
                    data-active-route="#"
                    data-section="support-coordinator" class="sidebar-link flex items-center w-full py-3 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg> Support Coordinator
                </a>
                <a href="{{ route('indiv.messages.inbox') }}"
                    data-active-route="indiv.messages.inbox"
                    data-section="messages" class="sidebar-link flex items-center w-full py-3 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </a>

            </nav>
        </aside>

        {{-- Mobile Sidebar (Original, still present for smaller screens) --}}
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 bg-custom-white text-text-dark w-64 p-6 space-y-6 transform -translate-x-full md:hidden transition-transform duration-300 ease-in-out z-40 border-r border-border-light flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold text-text-dark">Menu</h2>
                <button id="close-sidebar-button" class="text-text-light focus:outline-none p-2 rounded-md hover:bg-border-light transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-2 overflow-y-auto pr-2" id="mobile-sidebar-nav-container">
                <p class="text-xs font-semibold text-text-light uppercase mb-2 px-4">Menu</p>
                <a href="{{ route('indiv.dashboard') }}" data-section="dashboard" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </a>
                <a href="#" data-section="support-coordinator" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg> Support Coordinator
                </a>
                <a href="{{ route('indiv.messages.inbox') }}" data-section="messages" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </a>

                {{-- Add Profile Menu items here for mobile --}}
                <hr class="my-4 border-border-light">
                <p class="text-xs font-semibold text-text-light uppercase mb-2 px-4">Account</p>
                <a href="{{ route('indiv.profile.basic-details') }}" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user mr-3"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                </a>
                <button data-action="settings" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings mr-3"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                </button>
                <button data-action="logout" class="sidebar-link flex items-center w-full py-2.5 rounded-md text-left text-base font-medium text-[#ef4444] hover:bg-secondary-bg transition-colors duration-150" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out mr-3"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
                </button>
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
                {{-- This is where the dynamic content will be injected --}}
                @yield('main-content')

            </div>
        </main>
    </div>

    {{-- Flatpickr JS (Moved here to ensure it loads before your inline script) --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing sidebar active state logic
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const currentRoute = window.currentRouteName; // Get current route name from Blade

            sidebarLinks.forEach(link => {
                const linkRoute = link.getAttribute('data-active-route');
                if (linkRoute === currentRoute) {
                    link.classList.add('active');
                } else if (linkRoute === '#' && currentRoute.startsWith('indiv.messages')) {
                    // Specific handling for 'Messages' if its route isn't exactly 'indiv.messages.inbox'
                    // but you want it active for any message related route.
                    // You might need to adjust this based on your actual route structure.
                    if (link.getAttribute('data-section') === 'messages') {
                        link.classList.add('active');
                    }
                }
            });

            // Profile Dropdown
            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');

            if (profileMenuButton && profileDropdown) {
                profileMenuButton.addEventListener('click', function() {
                    const isHidden = profileDropdown.classList.contains('hidden');
                    if (isHidden) {
                        profileDropdown.classList.remove('hidden');
                        setTimeout(() => {
                            profileDropdown.classList.remove('opacity-0', 'scale-95');
                            profileDropdown.classList.add('opacity-100', 'scale-100');
                        }, 10); // Small delay to allow 'hidden' to be removed before transition
                    } else {
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                        profileDropdown.addEventListener('transitionend', function handler() {
                            profileDropdown.classList.add('hidden');
                            profileDropdown.removeEventListener('transitionend', handler);
                        });
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!profileMenuButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                        if (!profileDropdown.classList.contains('hidden')) {
                            profileDropdown.classList.add('opacity-0', 'scale-95');
                            profileDropdown.addEventListener('transitionend', function handler() {
                                profileDropdown.classList.add('hidden');
                                profileDropdown.removeEventListener('transitionend', handler);
                            });
                        }
                    }
                });
            }

            // Mobile Sidebar Toggles
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const closeSidebarButton = document.getElementById('close-sidebar-button');

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileSidebar.classList.remove('-translate-x-full');
                });
            }

            if (closeSidebarButton) {
                closeSidebarButton.addEventListener('click', function() {
                    mobileSidebar.classList.add('-translate-x-full');
                });
            }

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
            flatpickr("#date_of_birth-input", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            });
            // --- End Flatpickr Initialization ---
        });
    </script>
</body>
</html>