<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard</title>
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

        /* Chat bubble styles */
        .chat-bubble {
            max-width: 75%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            word-wrap: break-word;
        }
        .chat-bubble.incoming {
            background-color: #e2e8f0; /* gray-200 */
            align-self: flex-start;
            border-bottom-left-radius: 0.25rem; /* Smaller radius for corner */
        }
        .chat-bubble.outgoing {
            background-color: #6366f1; /* indigo-500 */
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0.25rem; /* Smaller radius for corner */
        }
        .chat-message-time {
            font-size: 0.75rem; /* text-xs */
            color: #6b7280; /* gray-500 */
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
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 flex flex-col">

    <!-- Main Header -->
    <header class="bg-white shadow-md p-4 flex items-center justify-between z-20 sticky top-0">
        <!-- Mobile Header Content (Hamburger Menu, Title) -->
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-gray-800">Company Dashboard</h1>
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Desktop Header Content (Dashboard Title, Icons) -->
        <div class="hidden md:flex items-center justify-between w-full">
            <h1 class="text-2xl font-extrabold text-indigo-600">Company Dashboard</h1>
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
                        <img src="https://placehold.co/32x32/a78bfa/ffffff?text=AD" alt="Admin Avatar" class="w-8 h-8 rounded-full border-2 border-indigo-300">
                        <span class="font-medium text-gray-700 hidden sm:inline">Admin User</span>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out inline-block mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
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
                <h1 class="text-2xl font-extrabold text-indigo-600">Company Panel</h1>
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
                <button data-section="accommodations-list" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Accommodations List
                </button>
                <button data-section="chat" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Chat
                </button>
            </nav>
        </aside>

        <!-- Main content area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-full mx-auto">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome to your Company Dashboard!</h2>
                    <p class="text-gray-600">Here's a quick overview of your company's operations.</p>
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 text-blue-800">
                        <p class="font-semibold">Company Updates:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>New policy updates rolled out on 2024-07-18.</li>
                            <li>Quarterly performance review meeting scheduled for next week.</li>
                            <li>Check the latest participant registrations.</li>
                        </ul>
                    </div>
                </div>

                <!-- Participants List Section -->
                <div id="participants-list-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Company Participants</h2>
                    <p class="text-gray-600 mb-4">Manage the list of individuals supported by the company.</p>

                    <div class="mb-6 flex flex-wrap items-end gap-4">
                        <div class="relative flex-1 min-w-[180px]">
                            <label for="participant-search-input" class="block text-xs font-medium text-gray-500 mb-1">Search Name</label>
                            <input type="text" id="participant-search-input" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 pl-10" placeholder="Search participants..." />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label for="accommodation-type-filter" class="block text-xs font-medium text-gray-500 mb-1">Accommodation Type</label>
                            <select class="border border-gray-300 rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full" id="accommodation-type-filter">
                                <option value="">All Types</option>
                                <option value="SIL">Supported Independent Living (SIL)</option>
                                <option value="Group Home">Group Home</option>
                                <option value="Respite">Respite</option>
                                <option value="Community">Community Housing</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[100px]">
                            <label for="age-filter" class="block text-xs font-medium text-gray-500 mb-1">Age</label>
                            <input type="number" id="age-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Any" min="0" />
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label for="gender-filter" class="block text-xs font-medium text-gray-500 mb-1">Gender</label>
                            <select class="border border-gray-300 rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full" id="gender-filter">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label for="disability-type-filter" class="block text-xs font-medium text-gray-500 mb-1">Type of Disability</label>
                            <select class="border border-gray-300 rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full" id="disability-type-filter">
                                <option value="">All Types</option>
                                <option value="Physical">Physical Disability</option>
                                <option value="Intellectual">Intellectual Disability</option>
                                <option value="Sensory">Sensory Disability</option>
                                <option value="Psychosocial">Psychosocial Disability</option>
                                <option value="Neurological">Neurological Disability</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label for="location-filter" class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                            <input type="text" id="location-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Any" />
                        </div>
                        <button id="apply-filters-button" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out self-end">
                            Search
                        </button>
                    </div>

                    <div class="space-y-4" id="participants-container">
                        <!-- Participant entries will be dynamically added here -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm" data-accommodation-type="SIL" data-age="25" data-gender="female" data-disability="Physical" data-location="Dreamville">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-pink-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Alice Wonderland</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-17</p>
                                    <p class="text-xs text-gray-500">Age: 25, Gender: Female, Disability: Physical, Location: Dreamville</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm" data-accommodation-type="Group Home" data-age="30" data-gender="male" data-disability="Intellectual" data-location="Worktown">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-blue-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Bob The Builder</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-16</p>
                                    <p class="text-xs text-gray-500">Age: 30, Gender: Male, Disability: Intellectual, Location: Worktown</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm" data-accommodation-type="Respite" data-age="40" data-gender="male" data-disability="Psychosocial" data-location="Hollywood">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round text-blue-500 mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">Charlie Chaplin</p>
                                    <p class="text-sm text-gray-600">Last Contact: 2024-07-15</p>
                                    <p class="text-xs text-gray-500">Age: 40, Gender: Male, Disability: Psychosocial, Location: Hollywood</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                        </div>
                    </div>
                    <button id="add-participant-button" class="mt-6 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Add New Participant
                    </button>
                </div>

                <!-- Accommodations List Section -->
                <div id="accommodations-list-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Company Accommodations</h2>
                    <p class="text-gray-600 mb-4">View and manage accommodation details for company participants.</p>
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
                            </div>
                            <div class="flex flex-row md:flex-col gap-2 md:gap-1 ml-auto items-end">
                                <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150 text-sm">Edit</button>
                                <button class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-150 text-sm">Delete</button>
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
                            </div>
                            <div class="flex flex-row md:flex-col gap-2 md:gap-1 ml-auto items-end">
                                <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150 text-sm">Edit</button>
                                <button class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-150 text-sm">Delete</button>
                            </div>
                        </div>
                    </div>
                    <button id="submit-accommodation-button" class="mt-6 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Submit New Accommodation
                    </button>
                </div>

                <!-- Chat Section -->
                <div id="chat-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Company Chat</h2>
                    <p class="text-gray-600 mb-4">Communicate securely with internal teams and external contacts.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Pane: Chat Contacts List -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contacts</h3>
                            <div class="space-y-2">
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150 active" data-contact="jane-smith">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=JS" alt="Jane Smith" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-gray-800">Jane Smith</p>
                                        <p class="text-sm text-gray-500">Last message: Hi there!</p>
                                    </div>
                                </button>
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150" data-contact="mark-jones">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=MJ" alt="Mark Jones" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-gray-800">Mark Jones</p>
                                        <p class="text-sm text-gray-500">Last message: Got it, thanks!</p>
                                    </div>
                                </button>
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150" data-contact="sarah-davis">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=SD" alt="Sarah Davis" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-gray-800">Sarah Davis</p>
                                        <p class="text-sm text-gray-500">Last message: See you soon!</p>
                                    </div>
                                </button>
                            </div>

                            <!-- Incoming Request Box (Example for company context) -->
                            <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200 text-purple-800 text-sm">
                                <p class="font-semibold mb-2">New Team Request (2024-07-18)</p>
                                <p>"Marketing Team would like to add you to their chat group."</p>
                                <div class="flex space-x-2 mt-3">
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-md text-xs hover:bg-green-700 transition">Accept</button>
                                    <button class="px-4 py-2 bg-red-600 text-white rounded-md text-xs hover:bg-red-700 transition">Decline</button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Pane: Chat Window -->
                        <div class="md:col-span-2 bg-white p-4 rounded-lg border border-gray-200 flex flex-col h-[500px]">
                            <div class="border-b border-gray-200 pb-3 mb-3 flex items-center">
                                <img src="https://placehold.co/40x40/cbd5e1/475569?text=JS" alt="Jane Smith" class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-semibold text-gray-800">Jane Smith</p>
                                    <p class="text-sm text-green-500">Online</p>
                                </div>
                            </div>

                            <!-- Chat Messages Area -->
                            <div class="flex-1 overflow-y-auto space-y-3 p-2">
                                <!-- Incoming Message -->
                                <div class="flex justify-start">
                                    <div class="chat-bubble incoming">
                                        <p class="chat-message-sender">Jane Smith</p>
                                        <p>Hi team! Just wanted to share the latest project updates.</p>
                                        <p class="chat-message-time text-right">10:00 AM</p>
                                    </div>
                                </div>
                                <!-- Outgoing Message -->
                                <div class="flex justify-end">
                                    <div class="chat-bubble outgoing">
                                        <p class="chat-message-sender text-right">You</p>
                                        <p>Thanks Jane! I'll review them now.</p>
                                        <p class="chat-message-time text-right">10:05 AM</p>
                                    </div>
                                </div>
                                <!-- Incoming Message -->
                                <div class="flex justify-start">
                                    <div class="chat-bubble incoming">
                                        <p class="chat-message-sender">Jane Smith</p>
                                        <p>Great! Let me know if you have any questions.</p>
                                        <p class="chat-message-time text-right">10:06 AM</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Input Area -->
                            <div class="mt-4 flex items-center border-t border-gray-200 pt-4">
                                <input type="text" placeholder="Type your message..." class="flex-1 p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <button class="ml-3 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Section (Accessible via dropdown) -->
                <div id="profile-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Company Profile</h2>
                    <p class="text-gray-600 mb-2">Manage your personal and company-related information.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="company-name" class="block text-sm font-medium text-gray-700">Company Name</label>
                            <input type="text" id="company-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Acme Corp" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="admin-email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <input type="email" id="admin-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="admin@acmecorp.com" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md col-span-full">
                            <label for="company-bio" class="block text-sm font-medium text-gray-700">Company Description</label>
                            <textarea id="company-bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Describe your company's mission and services..."></textarea>
                        </div>
                    </div>
                    <button class="mt-6 px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Save Company Profile
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

    <!-- Add New Participant Modal (Re-added for Company Dashboard) -->
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

    <!-- Submit New Accommodation Modal -->
    <div id="submit-accommodation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md relative">
            <button id="close-accommodation-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Submit New Accommodation</h2>
            <form id="submit-accommodation-form">
                <div class="mb-4">
                    <label for="accommodation-for" class="block text-sm font-medium text-gray-700 mb-1">Accommodation For (Participant Name)</label>
                    <input type="text" id="accommodation-for" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="e.g., Alice Wonderland" required />
                </div>
                <div class="mb-4">
                    <label for="accommodation-type" class="block text-sm font-medium text-gray-700 mb-1">Accommodation Type</label>
                    <select id="accommodation-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required>
                        <option value="">Select Type</option>
                        <option value="SIL">Supported Independent Living (SIL)</option>
                        <option value="Group Home">Group Home</option>
                        <option value="Respite">Respite</option>
                        <option value="Community">Community Housing</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="accommodation-address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="accommodation-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="e.g., 123 Main St, Anytown" required />
                </div>
                <div class="mb-6">
                    <label for="accommodation-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="accommodation-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required>
                        <option value="Active">Active</option>
                        <option value="Pending">Pending</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-accommodation-submission" class="px-5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus="ring-indigo-500 focus:ring-offset-2">
                        Submit Accommodation
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

            // Participants List elements
            const participantsContainer = document.getElementById('participants-container');
            const addParticipantButton = document.getElementById('add-participant-button');
            const participantSearchInput = document.getElementById('participant-search-input');
            const accommodationTypeFilter = document.getElementById('accommodation-type-filter');
            const ageFilter = document.getElementById('age-filter');
            const genderFilter = document.getElementById('gender-filter');
            const disabilityTypeFilter = document.getElementById('disability-type-filter');
            const locationFilter = document.getElementById('location-filter');
            const applyFiltersButton = document.getElementById('apply-filters-button');


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
            const submitAccommodationButton = document.getElementById('submit-accommodation-button');

            // Submit Accommodation Modal elements
            const submitAccommodationModal = document.getElementById('submit-accommodation-modal');
            const closeAccommodationModalButton = document.getElementById('close-accommodation-modal');
            const cancelAccommodationSubmissionButton = document.getElementById('cancel-accommodation-submission');
            const submitAccommodationForm = document.getElementById('submit-accommodation-form');
            const accommodationForInput = document.getElementById('accommodation-for');
            const accommodationTypeSelect = document.getElementById('accommodation-type');
            const accommodationAddressInput = document.getElementById('accommodation-address');
            const accommodationStatusSelect = document.getElementById('accommodation-status');


            // Chat elements
            const chatContacts = document.querySelectorAll('.chat-contact');
            const chatWindowHeaderName = document.querySelector('#chat-section .border-b p');
            const chatWindowHeaderImage = document.querySelector('#chat-section .border-b img');
            const chatMessagesContainer = document.querySelector('#chat-section .overflow-y-auto');

            // Sample chat data (you would load this from a backend in a real application)
            const chatData = {
                'jane-smith': [
                    { sender: 'Jane Smith', message: 'Hi team! Just wanted to share the latest project updates.', time: '10:00 AM', type: 'incoming' },
                    { sender: 'You', message: 'Thanks Jane! I\'ll review them now.', time: '10:05 AM', type: 'outgoing' },
                    { sender: 'Jane Smith', message: 'Great! Let me know if you have any questions.', time: '10:06 AM', type: 'incoming' },
                ],
                'mark-jones': [
                    { sender: 'Mark Jones', message: 'Good morning! Meeting at 11 AM today.', time: 'Yesterday', type: 'incoming' },
                    { sender: 'You', message: 'Got it, thanks Mark!', time: 'Yesterday', type: 'outgoing' },
                ],
                'sarah-davis': [
                    { sender: 'Sarah Davis', message: 'Please submit your weekly reports by EOD.', time: '2 days ago', type: 'incoming' },
                ]
            };


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

            // Function to load chat messages for a selected contact
            function loadChatMessages(contactId) {
                chatMessagesContainer.innerHTML = ''; // Clear previous messages
                const messages = chatData[contactId] || [];
                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `flex ${msg.type === 'outgoing' ? 'justify-end' : 'justify-start'}`;
                    messageDiv.innerHTML = `
                        <div class="chat-bubble ${msg.type}">
                            <p class="chat-message-sender ${msg.type === 'outgoing' ? 'text-right' : ''}">${msg.sender}</p>
                            <p>${msg.message}</p>
                            <p class="chat-message-time ${msg.type === 'outgoing' ? 'text-right' : ''}">${msg.time}</p>
                        </div>
                    `;
                    chatMessagesContainer.appendChild(messageDiv);
                });
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight; // Scroll to bottom
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
                    // If navigating to chat, ensure a chat is loaded (e.g., first contact)
                    if (sectionId === 'chat') {
                        const firstContactButton = document.querySelector('#chat-section .chat-contact');
                        if (firstContactButton) {
                            firstContactButton.classList.add('active');
                            chatWindowHeaderName.textContent = firstContactButton.querySelector('.font-medium').textContent;
                            chatWindowHeaderImage.src = firstContactButton.querySelector('img').src;
                            loadChatMessages(firstContactButton.dataset.contact);
                        }
                    }
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
            if (addParticipantButton) { // Check if button exists before adding listener
                addParticipantButton.addEventListener('click', function() {
                    addParticipantModal.classList.remove('hidden');
                    // Set today's date as default for last contact
                    participantLastContactInput.value = new Date().toISOString().slice(0, 10);
                });
            }

            // Close modal when close button (X) or Cancel button is clicked
            if (closeParticipantModalButton) {
                closeParticipantModalButton.addEventListener('click', function() {
                    addParticipantModal.classList.add('hidden');
                    addParticipantForm.reset(); // Clear form fields
                });
            }
            if (cancelAddParticipantButton) {
                cancelAddParticipantButton.addEventListener('click', function() {
                    addParticipantModal.classList.add('hidden');
                    addParticipantForm.reset(); // Clear form fields
                });
            }

            // Close modal if clicking outside the modal content
            if (addParticipantModal) {
                addParticipantModal.addEventListener('click', function(event) {
                    if (event.target === addParticipantModal) {
                        addParticipantModal.classList.add('hidden');
                        addParticipantForm.reset(); // Clear form fields
                    }
                });
            }

            // Handle form submission
            if (addParticipantForm) {
                addParticipantForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission

                    const name = participantNameInput.value.trim();
                    const gender = participantGenderSelect.value;
                    const lastContact = participantLastContactInput.value;

                    // For demonstration, these values are hardcoded for new participant.
                    // In a real app, you'd get these from new input fields in the modal.
                    const newAccommodationType = "N/A"; // Example default
                    const newAge = "N/A"; // Example default
                    const newDisability = "N/A"; // Example default
                    const newLocation = "N/A"; // Example default


                    if (!name || !gender || !lastContact) {
                        alert('Please fill in all fields.'); // Use a custom modal for errors in a real app
                        return;
                    }

                    // Create the new participant HTML element
                    const newParticipantDiv = document.createElement('div');
                    newParticipantDiv.className = 'bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm';
                    // Add data attributes for filtering
                    newParticipantDiv.setAttribute('data-accommodation-type', newAccommodationType);
                    newParticipantDiv.setAttribute('data-age', newAge);
                    newParticipantDiv.setAttribute('data-gender', gender);
                    newParticipantDiv.setAttribute('data-disability', newDisability);
                    newParticipantDiv.setAttribute('data-location', newLocation);

                    newParticipantDiv.innerHTML = `
                        <div class="flex items-center mb-2 sm:mb-0">
                            <!-- ${gender === 'female' ? 'Female' : 'Male'} Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round ${gender === 'female' ? 'text-pink-500' : 'text-blue-500'} mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                            <div>
                                <p class="font-semibold text-gray-800">${name}</p>
                                <p class="text-sm text-gray-600">Last Contact: ${lastContact}</p>
                                <p class="text-xs text-gray-500">Age: ${newAge}, Gender: ${gender}, Disability: ${newDisability}, Location: ${newLocation}</p>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">View Details</button>
                    `;

                    // Append the new participant to the list
                    if (participantsContainer) {
                        participantsContainer.appendChild(newParticipantDiv);
                    }


                    // Clear the form and hide the modal
                    addParticipantForm.reset();
                    addParticipantModal.classList.add('hidden');
                });
            }


            // --- JavaScript for Participant Search and Filters ---
            function filterParticipants() {
                const searchTerm = participantSearchInput.value.toLowerCase();
                const accommodationType = accommodationTypeFilter.value.toLowerCase();
                const age = ageFilter.value; // Age is number, keep as string for comparison with data attribute
                const gender = genderFilter.value.toLowerCase();
                const disabilityType = disabilityTypeFilter.value.toLowerCase();
                const location = locationFilter.value.toLowerCase();

                const participantItems = participantsContainer.querySelectorAll('.bg-gray-50');

                participantItems.forEach(item => {
                    const participantName = item.querySelector('.font-semibold').textContent.toLowerCase();
                    // Get data from data attributes for more robust filtering
                    const itemAccommodationType = item.getAttribute('data-accommodation-type').toLowerCase();
                    const itemAge = item.getAttribute('data-age');
                    const itemGender = item.getAttribute('data-gender').toLowerCase();
                    const itemDisability = item.getAttribute('data-disability').toLowerCase();
                    const itemLocation = item.getAttribute('data-location').toLowerCase();


                    let matchesSearch = participantName.includes(searchTerm);
                    let matchesAccommodationType = accommodationType === '' || itemAccommodationType.includes(accommodationType);
                    let matchesAge = age === '' || parseInt(itemAge) === parseInt(age);
                    let matchesGender = gender === '' || itemGender === gender;
                    let matchesDisabilityType = disabilityType === '' || itemDisability.includes(disabilityType);
                    let matchesLocation = location === '' || itemLocation.includes(location);


                    if (matchesSearch && matchesAccommodationType && matchesAge && matchesGender && matchesDisabilityType && matchesLocation) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            }

            // Removed individual input/change listeners, now only triggered by button click
            if (applyFiltersButton) applyFiltersButton.addEventListener('click', filterParticipants);


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

            // --- JavaScript for Submit New Accommodation Modal ---
            if (submitAccommodationButton) {
                submitAccommodationButton.addEventListener('click', function() {
                    submitAccommodationModal.classList.remove('hidden');
                });
            }

            if (closeAccommodationModalButton) {
                closeAccommodationModalButton.addEventListener('click', function() {
                    submitAccommodationModal.classList.add('hidden');
                    submitAccommodationForm.reset();
                });
            }

            if (cancelAccommodationSubmissionButton) {
                cancelAccommodationSubmissionButton.addEventListener('click', function() {
                    submitAccommodationModal.classList.add('hidden');
                    submitAccommodationForm.reset();
                });
            }

            if (submitAccommodationModal) {
                submitAccommodationModal.addEventListener('click', function(event) {
                    if (event.target === submitAccommodationModal) {
                        submitAccommodationModal.classList.add('hidden');
                        submitAccommodationForm.reset();
                    }
                });
            }

            if (submitAccommodationForm) {
                submitAccommodationForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const accommodationFor = accommodationForInput.value.trim();
                    const accommodationType = accommodationTypeSelect.value;
                    const accommodationAddress = accommodationAddressInput.value.trim();
                    const accommodationStatus = accommodationStatusSelect.value;

                    if (!accommodationFor || !accommodationType || !accommodationAddress || !accommodationStatus) {
                        alert('Please fill in all accommodation fields.');
                        return;
                    }

                    const newAccommodationDiv = document.createElement('div');
                    newAccommodationDiv.className = 'accommodation-item bg-yellow-50 p-4 rounded-lg border border-yellow-200 flex flex-col md:flex-row items-start md:items-center gap-4 shadow-sm';
                    newAccommodationDiv.innerHTML = `
                        <img src="https://placehold.co/120x90/eab308/ffffff?text=${accommodationType.replace(/\s/g, '+')}+Home" alt="${accommodationType}" class="w-full md:w-32 h-auto rounded-md object-cover shadow-sm">
                        <div class="flex-1">
                            <p class="font-semibold text-yellow-800 text-lg mb-1">Accommodation for ${accommodationFor}</p>
                            <p class="text-yellow-700 text-sm">Type: ${accommodationType}</p>
                            <p class="text-yellow-700 text-sm">Address: ${accommodationAddress}</p>
                            <p class="text-yellow-700 text-sm">Status: ${accommodationStatus}</p>
                        </div>
                        <div class="flex flex-row md:flex-col gap-2 md:gap-1 ml-auto items-end">
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150 text-sm">Edit</button>
                            <button class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-150 text-sm">Delete</button>
                        </div>
                    `;

                    if (accommodationsContainer) {
                        accommodationsContainer.appendChild(newAccommodationDiv);
                    }

                    submitAccommodationForm.reset();
                    submitAccommodationModal.classList.add('hidden');
                });
            }


            // --- Chat Contact Selection Logic ---
            if (chatContacts.length > 0) { // Ensure chat contacts exist
                chatContacts.forEach(contactButton => {
                    contactButton.addEventListener('click', function() {
                        // Remove active class from all contacts
                        chatContacts.forEach(btn => btn.classList.remove('active'));
                        // Add active class to the clicked contact
                        this.classList.add('active');

                        // Update chat window header
                        const contactName = this.querySelector('.font-medium').textContent;
                        const contactImageSrc = this.querySelector('img').src;
                        const contactId = this.dataset.contact;

                        if (chatWindowHeaderName) chatWindowHeaderName.textContent = contactName;
                        if (chatWindowHeaderImage) chatWindowHeaderImage.src = contactImageSrc;

                        // Load messages for the selected contact
                        loadChatMessages(contactId);
                    });
                });
            }
        });
    </script>
</body>
</html>
