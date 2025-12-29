<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Coordinator Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f3f4f6; /* Light gray background */
        }
        /* Custom styles for active sidebar link */
        .sidebar-link.active {
            background-color: #e0e7ff; /* Light indigo background */
            color: #4338ca; /* Darker indigo text */
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
            background-color: #eef2ff; /* Lighter hover for non-active */
            color: #4338ca; /* Darker indigo on hover */
        }
        /* Style for scrollbar in main content area */
        main::-webkit-scrollbar {
            width: 8px;
        }
        main::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb {
            background: #a0a0a0;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: #808080;
        }
        /* Custom styles for the circular progress bar (simplified) - Kept for other sections if needed */
        .circular-progress {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(#6366f1 75%, #e0e7ff 0%); /* Example: 75% filled */
        }
        .circular-progress::before {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: white;
        }
        .circular-progress .value {
            position: relative;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 flex flex-col">

    <!-- Main Header -->
    <header class="bg-white shadow-md p-4 flex items-center justify-between z-20 sticky top-0">
        <!-- Mobile Header Content (Hamburger Menu, Title) -->
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-gray-800">SC Dashboard</h1>
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Desktop Header Content (Dashboard Title, Icons) -->
        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-700 hover:text-indigo-900 transition duration-300">
                    <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'SIL Match') }} Logo" class="h-10 inline-block align-middle mr-3">
                    {{ config('app.name', 'SIL Match') }}
                </a>
            <div class="flex items-center space-x-4 relative">
                <!-- Search Bar in Header -->
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <!-- Notification Icon -->
                <button class="text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <!-- Profile Icon with Dropdown -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <img src="https://placehold.co/32x32/a78bfa/ffffff?text=JS" alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-indigo-300">
                        <span class="font-medium text-gray-700 hidden sm:inline">John Smith</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </button>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-gray-100">
                        <button data-action="logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left transition-colors duration-150 rounded-md">
                            <a href="{{ route('home') }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out inline-block mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar for navigation -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white text-gray-800 w-64 p-6 space-y-6 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-20 md:w-60 border-r border-gray-200">
            <div class="flex items-center justify-between md:justify-center mb-8">
                <button id="close-sidebar-button" class="text-gray-400 md:hidden focus:outline-none p-2 rounded-md hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2 px-4">Menu</p>
                <button data-section="dashboard" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </button>
                <button data-section="participants-list" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87c-.51-.11-.98-.31-1.43-.58"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Participants List
                </button>
                <button data-section="accommodations" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Accommodations
                </button>
                <button data-section="messages" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Chat
                </button>
            </nav>
        </aside>

        <!-- Main content area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-full mx-auto">
                <!-- Welcome Section (Dashboard) -->
                <div id="dashboard-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">
                        Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </h2>
                    <div class="flex items-center mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
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
                    <p class="text-gray-600">This is your support coordinator report so far</p>
                    <!-- Analytics content removed as requested -->
                </div>

                <!-- Participants List Section -->
                <div id="participants-list-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Participants List</h2>
                    <p class="text-gray-600 mb-4">Manage the list of individuals you are currently supporting.</p>

                    <div class="mb-6 flex flex-col sm:flex-row items-center gap-4">
                        <div class="relative flex-1 w-full">
                            <input type="text" id="participant-search-input" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 pl-10" placeholder="Search participants..." />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                        <select class="border border-gray-300 rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full sm:w-auto">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                        <select class="border border-gray-300 rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full sm:w-auto">
                            <option>All Genders</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>

                    <div class="space-y-4" id="participants-container">
                        <!-- Participant entries will be dynamically added here -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-pink-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Alice Wonderland</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-17</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-blue-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Bob The Builder</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-16</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-blue-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Charlie Chaplin</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-15</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                    </div>
                    <button id="add-participant-button" class="mt-6 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Add New Participant
                    </button>
                </div>

                <!-- Accommodations Section -->
                <div id="accommodations-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Accommodations Management</h2>
                    <p class="text-gray-600 mb-4">View and manage accommodation details for your participants.</p>
                    <div class="mb-6 relative">
                        <input type="text" id="accommodation-search-input" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 pl-10" placeholder="Search accommodations by name, type, or address..." />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                    <div class="space-y-6" id="accommodations-container">
                        <!-- Accommodation for Alice Wonderland -->
                        <div class="accommodation-item bg-yellow-50 p-4 rounded-lg border border-yellow-200 flex flex-col md:flex-row items-start md:items-center gap-4 shadow-sm">
                            <img src="https://placehold.co/120x90/eab308/ffffff?text=SIL+Home" alt="Supported Independent Living Home" class="w-full md:w-32 h-auto rounded-md object-cover shadow-sm">
                            <div class="flex-1">
                                <p class="font-semibold text-yellow-800 text-lg mb-1">Accommodation for Alice Wonderland</p>
                                <p class="text-yellow-700 text-sm">Type: Supported Independent Living (SIL)</p>
                                <p class="text-yellow-700 text-sm">Address: 123 Fantasy Lane, Dreamville</p>
                                <p class="text-yellow-700 text-sm">Status: Active</p>
                                <button class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150">Enquire</button>
                            </div>
                        </div>
                        <!-- Accommodation for Bob The Builder -->
                        <div class="accommodation-item bg-yellow-50 p-4 rounded-lg border border-yellow-200 flex flex-col md:flex-row items-start md:items-center gap-4 shadow-sm">
                            <img src="https://placehold.co/120x90/eab308/ffffff?text=Group+Home" alt="Group Home" class="w-full md:w-32 h-auto rounded-md object-cover shadow-sm">
                            <div class="flex-1">
                                <p class="font-semibold text-yellow-800 text-lg mb-1">Accommodation for Bob The Builder</p>
                                <p class="text-yellow-700 text-sm">Type: Group Home</p>
                                <p class="text-yellow-700 text-sm">Address: 456 Construction Rd, Worktown</p>
                                <p class="text-yellow-700 text-sm">Status: Active</p>
                                <button class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150">Enquire</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Section (Chatbox Layout) -->
                <div id="messages-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Messages</h2>
                    <p class="text-gray-600 mb-4">Communicate securely with your support team and coordinators.</p>

                    <div class="flex flex-col md:flex-row gap-6 h-[600px]">
                        <!-- Chat Contacts Sidebar -->
                        <div class="w-full md:w-1/3 bg-gray-50 rounded-lg p-4 overflow-y-auto shadow-inner">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Support Coordinators</h3>
                            <div class="space-y-2" id="chat-contacts-list">
                                <!-- Example Chat Contacts -->
                                <button class="chat-contact flex items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150 active" data-contact="jane-smith">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=JS" alt="Jane Smith" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="font-medium text-gray-800">Jane Smith</p>
                                        <p class="text-sm text-gray-500">Last message: Hi there!</p>
                                    </div>
                                </button>
                                <button class="chat-contact flex items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150" data-contact="mark-jones">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=MJ" alt="Mark Jones" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="font-medium text-gray-800">Mark Jones</p>
                                        <p class="text-sm text-gray-500">Last message: Got it, thanks!</p>
                                    </div>
                                </button>
                                <button class="chat-contact flex items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150" data-contact="sarah-davis">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=SD" alt="Sarah Davis" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="font-medium text-gray-800">Sarah Davis</p>
                                        <p class="text-sm text-gray-500">Last message: See you soon!</p>
                                    </div>
                                </button>
                                <!-- Incoming Request Message (retained, but visually separated) -->
                                <div id="incoming-request-message" class="bg-purple-50 p-4 rounded-md border border-purple-200 mt-4" data-contact-name="John Doe" data-contact-id="john-doe">
                                    <p class="font-semibold text-purple-800 mb-2">Incoming Request from John Doe (2024-07-18)</p>
                                    <p class="text-purple-700 mb-4">"John Doe would like to connect with you."</p>
                                    <div class="flex space-x-3">
                                        <button class="accept-button px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                            Accept
                                        </button>
                                        <button class="revoke-button px-4 py-2 bg-red-600 text-white font-semibold rounded-md shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                            Revoke
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Chat Conversation Area -->
                        <div class="w-full md:w-2/3 bg-white rounded-lg flex flex-col shadow-lg border border-gray-200">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                                <h3 class="text-xl font-semibold text-gray-800" id="chat-header-name">Jane Smith</h3>
                                <p class="text-sm text-green-500">Online</p>
                            </div>
                            <div class="flex-1 p-4 overflow-y-auto space-y-4" id="chat-messages-container">
                                <!-- Chat messages will be loaded here -->
                                <!-- Example messages for Jane Smith -->
                                <div class="flex justify-start">
                                    <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[70%]">
                                        Hi there! How can I assist you today?
                                        <div class="text-xs text-gray-500 mt-1 text-right">Jane Smith - 10:00 AM</div>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="bg-indigo-500 text-white p-3 rounded-lg max-w-[70%]">
                                        Hello Jane! I have a question about my profile settings.
                                        <div class="text-xs text-indigo-200 mt-1 text-right">You - 10:05 AM</div>
                                    </div>
                                </div>
                                <div class="flex justify-start">
                                    <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[70%]">
                                        Certainly, what would you like to know?
                                        <div class="text-xs text-gray-500 mt-1 text-right">Jane Smith - 10:06 AM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                                <div class="flex">
                                    <input type="text" id="chat-input" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 mr-3" placeholder="Type your message..." />
                                    <button id="send-message-button" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Profile Section (Accessible via dropdown) -->
                <div id="profile-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Coordinator Profile</h2>
                    <p class="text-gray-600 mb-2">Manage your personal and professional information as a Support Coordinator.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="sc-name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="sc-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="John Smith" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="sc-email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="sc-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="john.smith@example.com" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md col-span-full">
                            <label for="sc-bio" class="block text-sm font-medium text-gray-700">Bio / Specializations</label>
                            <textarea id="sc-bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Describe your experience and areas of expertise..."></textarea>
                        </div>
                    </div>
                    <button class="mt-6 px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Save Profile
                    </button>
                </div>

                <!-- Settings Section (Accessible via dropdown) -->
                <div id="settings-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Account Settings</h2>
                    <p class="text-gray-600 mb-2">Adjust your account preferences and security settings.</p>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="notifications" class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Email Notifications</span>
                                <input type="checkbox" id="notifications" class="form-checkbox h-5 w-5 text-indigo-600 rounded" checked />
                            </label>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">Theme Preference</label>
                            <select id="theme" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                                <option>Light</option>
                                <option>Dark</option>
                                <option>System Default</option>
                            </select>
                        </div>
                        <button class="px-6 py-2 bg-red-600 text-white font-semibold rounded-md shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add New Participant Modal -->
    <div id="add-participant-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md relative">
            <button id="close-participant-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Participant</h2>
            <form id="add-participant-form">
                <div class="mb-4">
                    <label for="participant-name" class="block text-sm font-medium text-gray-700 mb-1">Participant Name</label>
                    <input type="text" id="participant-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required />
                </div>
                <div class="mb-4">
                    <label for="participant-gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select id="participant-gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="participant-last-contact" class="block text-sm font-medium text-gray-700 mb-1">Last Contact Date</label>
                    <input type="date" id="participant-last-contact" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required />
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-add-participant" class="px-5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Add Participant
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeSidebarButton = document.getElementById('close-sidebar-button');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const dashboardSections = document.querySelectorAll('.dashboard-section');

            // Header elements
            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileDropdownActions = profileDropdown ? profileDropdown.querySelectorAll('button[data-action]') : [];

            // Participant List elements
            const participantsContainer = document.getElementById('participants-container');
            const addParticipantButton = document.getElementById('add-participant-button');
            const participantSearchInput = document.getElementById('participant-search-input');

            // Add Participant Modal elements
            const addParticipantModal = document.getElementById('add-participant-modal');
            const closeParticipantModalButton = document.getElementById('close-participant-modal');
            const cancelAddParticipantButton = document.getElementById('cancel-add-participant');
            const addParticipantForm = document.getElementById('add-participant-form');
            const participantNameInput = document.getElementById('participant-name');
            const participantGenderSelect = document.getElementById('participant-gender');
            const participantLastContactInput = document.getElementById('participant-last-contact');

            // Accommodation elements for search
            const accommodationSearchInput = document.getElementById('accommodation-search-input');
            const accommodationsContainer = document.getElementById('accommodations-container');


            // Function to show a specific section
            function showSection(sectionId) {
                dashboardSections.forEach(section => {
                    section.classList.add('hidden'); // Hide all sections
                });
                const targetSection = document.getElementById(`${sectionId}-section`);
                if (targetSection) {
                    targetSection.classList.remove('hidden'); // Show the target section
                } else {
                    console.error(`Section with ID ${sectionId}-section not found.`);
                }
            }

            // Function to update active sidebar link styling
            function updateActiveLink(activeSectionId) {
                sidebarLinks.forEach(link => {
                    if (link.dataset.section === activeSectionId) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // Initial display: Show Dashboard section and set active link
            showSection('dashboard');
            updateActiveLink('dashboard');

            // Event listeners for sidebar links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const sectionId = this.dataset.section;
                    showSection(sectionId);
                    updateActiveLink(sectionId);
                    // Close sidebar on mobile after selection
                    if (window.innerWidth < 768) { // Tailwind's 'md' breakpoint is 768px
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            });

            // Mobile menu toggle
            mobileMenuButton.addEventListener('click', function() {
                sidebar.classList.remove('-translate-x-full');
            });

            // Close sidebar button for mobile
            closeSidebarButton.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
            });

            // Close sidebar if clicking outside when open on mobile
            document.body.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && !sidebar.contains(event.target) && !mobileMenuButton.contains(event.target) && !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // --- JavaScript for Profile Dropdown ---
            if (profileMenuButton && profileDropdown) {
                profileMenuButton.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click from bubbling to document and closing immediately
                    profileDropdown.classList.toggle('hidden');
                });

                // Close dropdown if clicked outside
                document.addEventListener('click', function(event) {
                    if (!profileDropdown.contains(event.target) && !profileMenuButton.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });

                // Handle dropdown menu item clicks
                profileDropdownActions.forEach(button => {
                    button.addEventListener('click', function() {
                        const action = this.dataset.action;
                        profileDropdown.classList.add('hidden'); // Hide dropdown after selection

                        if (action === 'profile') {
                            showSection('profile');
                        } else if (action === 'settings') {
                            showSection('settings');
                        } else if (action === 'logout') {
                            // In a real Laravel app, this would be a form submission or redirect
                            alert('Logging out...'); // Replace with actual logout logic
                            // window.location.href = '/logout';
                        }
                    });
                });
            }

            // --- JavaScript for Add New Participant Modal ---

            // Show modal when "Add New Participant" button is clicked
            addParticipantButton.addEventListener('click', function() {
                addParticipantModal.classList.remove('hidden');
                // Set today's date as default for last contact
                participantLastContactInput.value = new Date().toISOString().slice(0, 10);
            });

            // Close modal when close button (X) or Cancel button is clicked
            closeParticipantModalButton.addEventListener('click', function() {
                addParticipantModal.classList.add('hidden');
                addParticipantForm.reset(); // Clear form fields
            });

            cancelAddParticipantButton.addEventListener('click', function() {
                addParticipantModal.classList.add('hidden');
                addParticipantForm.reset(); // Clear form fields
            });

            // Close modal if clicking outside the modal content
            addParticipantModal.addEventListener('click', function(event) {
                if (event.target === addParticipantModal) {
                    addParticipantModal.classList.add('hidden');
                    addParticipantForm.reset(); // Clear form fields
                }
            });

            // Handle form submission
            addParticipantForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                const name = participantNameInput.value.trim();
                const gender = participantGenderSelect.value;
                const lastContact = participantLastContactInput.value;

                if (!name || !gender || !lastContact) {
                    alert('Please fill in all fields.'); // Use a custom modal for errors in a real app
                    return;
                }

                // Create the new participant HTML element
                const newParticipantDiv = document.createElement('div');
                newParticipantDiv.className = 'bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm';
                newParticipantDiv.innerHTML = `
                    <div class="flex items-center mb-2 sm:mb-0">
                        <!-- ${gender === 'female' ? 'Female' : 'Male'} Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round ${gender === 'female' ? 'text-pink-500' : 'text-blue-500'} mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                        <div>
                            <p class="font-semibold text-gray-800">${name}</p>
                            <p class="text-sm text-gray-600">Last Contact: ${lastContact}</p>
                        </div>
                    </div>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                `;

                // Append the new participant to the list
                participantsContainer.appendChild(newParticipantDiv);

                // Clear the form and hide the modal
                addParticipantForm.reset();
                addParticipantModal.classList.add('hidden');
            });

            // --- JavaScript for Participant Search ---
            if (participantSearchInput && participantsContainer) {
                participantSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const participantItems = participantsContainer.querySelectorAll('.bg-gray-50'); // Select all participant items

                    participantItems.forEach(item => {
                        const participantName = item.querySelector('.font-semibold').textContent.toLowerCase();
                        if (participantName.includes(searchTerm)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });
            }

            // --- JavaScript for Accommodation Search ---
            if (accommodationSearchInput && accommodationsContainer) {
                accommodationSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const accommodationItems = accommodationsContainer.querySelectorAll('.accommodation-item'); // Get items dynamically

                    accommodationItems.forEach(item => {
                        const itemText = item.textContent.toLowerCase();
                        if (itemText.includes(searchTerm)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });
            }

            // Ensure the dashboard section is initially visible
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
