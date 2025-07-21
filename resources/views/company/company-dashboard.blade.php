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
        :root {
            /* Define your custom colors from the palette */
            --color-white: #FFFFFF;
            --color-linen: #F8F2E4; /* Light beige/off-white */
            --color-light-green: #E0F0E0; /* Very light, desaturated green */
            --color-gray: #D0D0D0; /* Light, cool gray */
            --color-copper: #CD8250; /* The orange shade */
            --color-copper-dark: #B87040; /* Darker shade for hover */
            --color-teal: #2F5F6C; /* Dark blue-green */
            --color-fern: #3F4F3F; /* Dark olive green */
            --color-black: #000000;
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--color-linen); /* Using the linen color for background */
            color: var(--color-fern); /* Default text color */
        }
        /* Custom styles for active sidebar link */
        .sidebar-link.active {
            background-color: var(--color-light-green);
            color: var(--color-fern);
            font-weight: 600;
            box-shadow: none;
            transform: none;
        }
        .sidebar-link {
            transition: all 0.2s ease-in-out;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        .sidebar-link:not(.active):hover {
            background-color: var(--color-light-green);
            color: var(--color-fern);
        }
        /* Style for scrollbar in main content area */
        main::-webkit-scrollbar {
            width: 8px;
        }
        main::-webkit-scrollbar-track {
            background: var(--color-gray);
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb {
            background: var(--color-teal);
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: var(--color-fern);
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
            background-color: var(--color-linen);
            align-self: flex-start;
            border-bottom-left-radius: 0.25rem;
        }
        .chat-bubble.outgoing {
            background-color: var(--color-teal);
            color: var(--color-white);
            align-self: flex-end;
            border-bottom-right-radius: 0.25rem;
        }
        .chat-message-time {
            font-size: 0.75rem;
            color: var(--color-fern);
            margin-top: 0.25rem;
        }
        .chat-bubble.outgoing .chat-message-sender,
        .chat-bubble.outgoing .chat-message-time {
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="min-h-screen bg-[var(--color-linen)] text-[var(--color-fern)] flex flex-col">

    <!-- Main Header -->
    <header class="bg-[var(--color-white)] shadow-md p-4 flex items-center justify-between z-20 sticky top-0">
        <!-- Mobile Header Content (Hamburger Menu, Title) -->
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-[var(--color-fern)]">Company Dashboard</h1>
            <button id="mobile-menu-button" class="text-[var(--color-teal)] focus:outline-none p-2 rounded-md hover:bg-[var(--color-linen)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Desktop Header Content (Dashboard Title, Icons) -->
        <div class="hidden md:flex items-center justify-between w-full">
            <h1 class="text-2xl font-extrabold text-[var(--color-teal)]">Company Dashboard</h1>
            <div class="flex items-center space-x-4 relative">
                <!-- Search Bar in Header -->
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-[var(--color-gray)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:border-transparent text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-fern)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <!-- Notification Icon -->
                <button class="text-[var(--color-fern)] hover:text-[var(--color-black)] focus:outline-none p-2 rounded-md hover:bg-[var(--color-linen)] transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <!-- Profile Icon with Dropdown -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-[var(--color-fern)] hover:text-[var(--color-black)] focus:outline-none p-2 rounded-md hover:bg-[var(--color-linen)] transition-colors duration-200">
                        <img src="https://placehold.co/32x32/a78bfa/ffffff?text=AD" alt="Admin Avatar" class="w-8 h-8 rounded-full border-2 border-[var(--color-teal)]">
                        <span class="font-medium text-[var(--color-fern)] hidden sm:inline">Admin User</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-[var(--color-white)] rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </button>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)] w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-[var(--color-gray)]">
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
        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-[var(--color-white)] text-[var(--color-fern)] w-64 p-6 space-y-6 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-20 md:w-60 border-r border-[var(--color-gray)]">
            <div class="flex items-center justify-between md:justify-center mb-8">
                <h1 class="text-2xl font-extrabold text-[var(--color-teal)]">Company Panel</h1>
                <button id="close-sidebar-button" class="text-[var(--color-fern)] md:hidden focus:outline-none p-2 rounded-md hover:bg-[var(--color-linen)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-[var(--color-gray)] uppercase mb-2 px-4">Menu</p>
                <button data-section="dashboard" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)] active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </button>
                <button data-section="participants-list" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87c-.51-.11-.98-.31-1.43-.58"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Participants List
                </button>
                <button data-section="accommodations-list" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Accommodations List
                </button>
                <button data-section="chat" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-[var(--color-fern)] hover:bg-[var(--color-linen)] hover:text-[var(--color-teal)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Chat
                </button>
            </nav>
        </aside>

        <!-- Main content area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-full mx-auto">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-xl shadow-lg mb-8">
                    <h2 class="text-3xl font-bold text-[var(--color-fern)] mb-2">Welcome to your Company Dashboard!</h2>
                    <p class="text-[var(--color-teal)]">Here's a quick overview of your company's operations.</p>
                    <div class="mt-6 p-4 bg-[var(--color-light-green)] rounded-lg border border-[var(--color-fern)] text-[var(--color-fern)]">
                        <p class="font-semibold">Company Updates:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>New policy updates rolled out on 2024-07-18.</li>
                            <li>Quarterly performance review meeting scheduled for next week.</li>
                            <li>Check the latest participant registrations.</li>
                        </ul>
                    </div>
                </div>

                <!-- Participants List Section -->
                <div id="participants-list-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-4">Company Participants</h2>
                    <p class="text-[var(--color-teal)] mb-4">Manage the list of individuals supported by the company.</p>

                    <div class="mb-6 flex flex-wrap items-end gap-4">
                        <div class="relative flex-1 min-w-[180px]">
                            <label for="participant-search-input" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Search Name</label>
                            <input type="text" id="participant-search-input" class="block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2 pl-10" placeholder="Search participants..." />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-fern)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label for="accommodation-type-filter" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Accommodation Type</label>
                            <select class="border border-[var(--color-gray)] rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-copper)] w-full" id="accommodation-type-filter">
                                <option value="">All Types</option>
                                <option value="SIL">Supported Independent Living (SIL)</option>
                                <option value="Group Home">Group Home</option>
                                <option value="Respite">Respite</option>
                                <option value="Community">Community Housing</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[100px]">
                            <label for="age-filter" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Age</label>
                            <input type="number" id="age-filter" class="block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="Any" min="0" />
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label for="gender-filter" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Gender</label>
                            <select class="border border-[var(--color-gray)] rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-copper)] w-full" id="gender-filter">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label for="disability-type-filter" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Type of Disability</label>
                            <select class="border border-[var(--color-gray)] rounded-md py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-copper)] w-full" id="disability-type-filter">
                                <option value="">All Types</option>
                                <option value="Physical">Physical Disability</option>
                                <option value="Intellectual">Intellectual Disability</option>
                                <option value="Sensory">Sensory Disability</option>
                                <option value="Psychosocial">Psychosocial Disability</option>
                                <option value="Neurological">Neurological Disability</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label for="location-filter" class="block text-xs font-medium text-[var(--color-fern)] mb-1">Location</label>
                            <input type="text" id="location-filter" class="block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="Any" />
                        </div>
                        <button id="apply-filters-button" class="px-6 py-2 bg-[var(--color-copper)] text-[var(--color-white)] font-semibold rounded-md shadow-md hover:bg-[var(--color-copper-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2 transition duration-150 ease-in-out self-end">
                            Search
                        </button>
                    </div>

                    <div class="space-y-4" id="participants-container">
                        <!-- Participant entries will be dynamically added here by JavaScript -->
                    </div>
                    <button id="add-participant-button" class="mt-6 px-6 py-3 bg-[var(--color-teal)] text-[var(--color-white)] font-semibold rounded-lg shadow-md hover:bg-[var(--color-fern)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal)] focus:ring-offset-2 transition duration-150 ease-in-out">
                        Add New Participant
                    </button>
                </div>

                <!-- Accommodations List Section -->
                <div id="accommodations-list-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-4">Company Accommodations</h2>
                    <p class="text-[var(--color-teal)] mb-4">View and manage accommodation details for company participants.</p>
                    <div class="mb-6 relative">
                        <input type="text" id="accommodation-search-input" class="block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2 pl-10" placeholder="Search accommodations by name, type, or address..." />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-fern)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                    <div class="space-y-6" id="accommodations-container">
                        <!-- Accommodation entries will be dynamically added here by JavaScript -->
                    </div>
                    <button id="submit-accommodation-button" class="mt-6 px-6 py-3 bg-[var(--color-copper)] text-[var(--color-white)] font-semibold rounded-lg shadow-md hover:bg-[var(--color-copper-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2 transition duration-150 ease-in-out">
                        Submit New Accommodation
                    </button>
                </div>

                <!-- Chat Section -->
                <div id="chat-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-4">Company Chat</h2>
                    <p class="text-[var(--color-teal)] mb-4">Communicate securely with internal teams and external contacts.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Pane: Chat Contacts List -->
                        <div class="md:col-span-1 bg-[var(--color-linen)] p-4 rounded-lg border border-[var(--color-gray)]">
                            <h3 class="text-lg font-semibold text-[var(--color-fern)] mb-4">Contacts</h3>
                            <div class="space-y-2">
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-[var(--color-light-green)] transition-colors duration-150 active" data-contact="jane-smith">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=JS" alt="Jane Smith" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-[var(--color-fern)]">Jane Smith</p>
                                        <p class="text-sm text-[var(--color-teal)]">Last message: Hi there!</p>
                                    </div>
                                </button>
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-[var(--color-light-green)] transition-colors duration-150" data-contact="mark-jones">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=MJ" alt="Mark Jones" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-[var(--color-fern)]">Mark Jones</p>
                                        <p class="text-sm text-[var(--color-teal)]">Last message: Got it, thanks!</p>
                                    </div>
                                </button>
                                <button class="chat-contact grid grid-cols-[auto_1fr] gap-x-3 items-center w-full p-3 rounded-md hover:bg-[var(--color-light-green)] transition-colors duration-150" data-contact="sarah-davis">
                                    <img src="https://placehold.co/40x40/cbd5e1/475569?text=SD" alt="Sarah Davis" class="w-10 h-10 rounded-full row-span-2">
                                    <div class="col-start-2">
                                        <p class="font-medium text-[var(--color-fern)]">Sarah Davis</p>
                                        <p class="text-sm text-[var(--color-teal)]">Last message: See you soon!</p>
                                    </div>
                                </button>
                            </div>

                            <!-- Incoming Request Box (Example for company context) -->
                            <div class="mt-6 p-4 bg-[var(--color-light-green)] rounded-lg border border-[var(--color-fern)] text-[var(--color-fern)] text-sm">
                                <p class="font-semibold mb-2">New Team Request (2024-07-18)</p>
                                <p>"Marketing Team would like to add you to their chat group."</p>
                                <div class="flex space-x-2 mt-3">
                                    <button class="px-4 py-2 bg-[var(--color-teal)] text-[var(--color-white)] rounded-md text-xs hover:bg-[var(--color-fern)] transition">Accept</button>
                                    <button class="px-4 py-2 bg-red-600 text-[var(--color-white)] rounded-md text-xs hover:bg-red-700 transition">Decline</button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Pane: Chat Window -->
                        <div class="md:col-span-2 bg-[var(--color-white)] p-4 rounded-lg border border-[var(--color-gray)] flex flex-col h-[500px]">
                            <div class="border-b border-[var(--color-gray)] pb-3 mb-3 flex items-center">
                                <img src="https://placehold.co/40x40/cbd5e1/475569?text=JS" alt="Jane Smith" class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-semibold text-[var(--color-fern)]">Jane Smith</p>
                                    <p class="text-sm text-[var(--color-teal)]">Online</p>
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
                            <div class="mt-4 flex items-center border-t border-[var(--color-gray)] pt-4">
                                <input type="text" placeholder="Type your message..." class="flex-1 p-3 rounded-md border border-[var(--color-gray)] focus:outline-none focus:ring-1 focus:ring-[var(--color-copper)]">
                                <button class="ml-3 px-6 py-3 bg-[var(--color-copper)] text-[var(--color-white)] font-semibold rounded-md shadow-md hover:bg-[var(--color-copper-dark)] transition duration-150 ease-in-out">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Section (Accessible via dropdown) -->
                <div id="profile-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-4">Your Company Profile</h2>
                    <p class="text-[var(--color-teal)] mb-2">Manage your personal and company-related information.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-[var(--color-linen)] p-4 rounded-md">
                            <label for="company-name" class="block text-sm font-medium text-[var(--color-fern)]">Company Name</label>
                            <input type="text" id="company-name" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="Acme Corp" />
                        </div>
                        <div class="bg-[var(--color-linen)] p-4 rounded-md">
                            <label for="admin-email" class="block text-sm font-medium text-[var(--color-fern)]">Admin Email</label>
                            <input type="email" id="admin-email" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="admin@acmecorp.com" />
                        </div>
                        <div class="bg-[var(--color-linen)] p-4 rounded-md col-span-full">
                            <label for="company-bio" class="block text-sm font-medium text-[var(--color-fern)]">Company Description</label>
                            <textarea id="company-bio" rows="3" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="Describe your company's mission and services..."></textarea>
                        </div>
                    </div>
                    <button class="mt-6 px-6 py-2 bg-[var(--color-copper)] text-[var(--color-white)] font-semibold rounded-md shadow-md hover:bg-[var(--color-copper-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2 transition duration-150 ease-in-out">
                        Save Company Profile
                    </button>
                </div>

                <!-- Settings Section (Accessible via dropdown) -->
                <div id="settings-section" class="dashboard-section p-6 bg-[var(--color-white)] rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-4">Account Settings</h2>
                    <p class="text-[var(--color-teal)] mb-2">Adjust your account preferences and security settings.</p>
                    <div class="space-y-4">
                        <div class="bg-[var(--color-linen)] p-4 rounded-lg shadow-sm">
                            <label for="notifications" class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-medium text-[var(--color-fern)]">Email Notifications</span>
                                <input type="checkbox" id="notifications" class="form-checkbox h-5 w-5 text-[var(--color-teal)] border-[var(--color-gray)] rounded" checked />
                            </label>
                        </div>
                        <div class="bg-[var(--color-linen)] p-4 rounded-lg shadow-sm">
                            <label for="theme" class="block text-sm font-medium text-[var(--color-fern)] mb-2">Theme Preference</label>
                            <select id="theme" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2">
                                <option>Light</option>
                                <option>Dark</option>
                                <option>System Default</option>
                            </select>
                        </div>
                        <button class="px-6 py-2 bg-red-600 text-[var(--color-white)] font-semibold rounded-md shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add New Participant Modal -->
    <div id="add-participant-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-white)] p-8 rounded-lg shadow-xl w-full max-w-md relative">
            <button id="close-participant-modal" class="absolute top-4 right-4 text-[var(--color-fern)] hover:text-[var(--color-black)] p-1 rounded-full hover:bg-[var(--color-linen)] transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-6">Add New Participant</h2>
            <form id="add-participant-form">
                <div class="mb-4">
                    <label for="participant-name" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Participant Name</label>
                    <input type="text" id="participant-name" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required />
                </div>
                <div class="mb-4">
                    <label for="participant-gender" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Gender</label>
                    <select id="participant-gender" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="participant-age" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Age</label>
                    <input type="number" id="participant-age" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="participant-disability" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Type of Disability</label>
                    <select id="participant-disability" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required>
                        <option value="">Select Disability</option>
                        <option value="Physical">Physical Disability</option>
                        <option value="Intellectual">Intellectual Disability</option>
                        <option value="Sensory">Sensory Disability</option>
                        <option value="Psychosocial">Psychosocial Disability</option>
                        <option value="Neurological">Neurological Disability</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="participant-location" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Location</label>
                    <input type="text" id="participant-location" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required />
                </div>
                <div class="mb-6">
                    <label for="participant-last-contact" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Last Contact Date</label>
                    <input type="date" id="participant-last-contact" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required />
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-add-participant" class="px-5 py-2 border border-[var(--color-gray)] rounded-md shadow-sm text-sm font-medium text-[var(--color-fern)] hover:bg-[var(--color-linen)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2 transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[var(--color-copper)] text-[var(--color-white)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-copper-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2">
                        Add Participant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit New Accommodation Modal -->
    <div id="submit-accommodation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-white)] p-8 rounded-lg shadow-xl w-full max-w-2xl relative overflow-y-auto max-h-[90vh]">
            <button id="close-accommodation-modal" class="absolute top-4 right-4 text-[var(--color-fern)] hover:text-[var(--color-black)] p-1 rounded-full hover:bg-[var(--color-linen)] transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-fern)] mb-6">Submit New Accommodation</h2>
            <form id="submit-accommodation-form">
                <div class="mb-4">
                    <label for="accommodation-title" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Property Title</label>
                    <input type="text" id="accommodation-title" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="e.g., Spacious Home with Garden" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-fern)] mb-1">Property Address</label>
                    <input type="text" id="accommodation-address-province" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2 mb-2" placeholder="Province/State" required />
                    <input type="text" id="accommodation-address-city" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="City/Town" required />
                </div>
                <div class="mb-4">
                    <label for="accommodation-description" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Property Description</label>
                    <textarea id="accommodation-description" rows="3" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" placeholder="Describe the property..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-fern)] mb-2">Accommodation Type</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-apartment" name="accommodation-type" value="Apartment" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]">
                            <label for="acc-type-apartment" class="ml-2 text-sm text-[var(--color-fern)]">Apartment</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-house" name="accommodation-type" value="House" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]">
                            <label for="acc-type-house" class="ml-2 text-sm text-[var(--color-fern)]">House</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-villa" name="accommodation-type" value="Villa/Duplex/Townhouse" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]">
                            <label for="acc-type-villa" class="ml-2 text-sm text-[var(--color-fern)]">Villa/Duplex/Townhouse</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-fern)] mb-2">Property Type</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="prop-type-sda" name="property-type" value="SDA" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]">
                            <label for="prop-type-sda" class="ml-2 text-sm text-[var(--color-fern)]">SDA</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="prop-type-sil" name="property-type" value="SIL" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]">
                            <label for="prop-type-sil" class="ml-2 text-sm text-[var(--color-fern)]">SIL</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="date-available" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Date Available</label>
                    <input type="date" id="date-available" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required />
                </div>
                <div class="mb-4">
                    <label for="num-bedrooms" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Number of Bedrooms</label>
                    <input type="number" id="num-bedrooms" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="num-bathrooms" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Number of Bathrooms</label>
                    <input type="number" id="num-bathrooms" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="num-current-participants" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Number of Current Participants</label>
                    <input type="number" id="num-current-participants" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="gender-participants" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Gender of Participants in Accommodation</label>
                    <select id="gender-participants" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" required>
                        <option value="">Select Gender</option>
                        <option value="Mixed">Mixed</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="num-vacancies" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Current Number of Vacancies</label>
                    <input type="number" id="num-vacancies" class="mt-1 block w-full rounded-md border-[var(--color-gray)] shadow-sm focus:border-[var(--color-copper)] focus:ring-[var(--color-copper)] sm:text-sm p-2" min="0" required />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-fern)] mb-2">Property Features (Check all that Apply)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center"><input type="checkbox" id="feat-accessible-bathroom" name="property-features" value="Accessible Bathroom" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-accessible-bathroom" class="ml-2 text-sm text-[var(--color-fern)]">Accessible Bathroom</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-accessible-kitchen" name="property-features" value="Accessible Kitchen" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-accessible-kitchen" class="ml-2 text-sm text-[var(--color-fern)]">Accessible Kitchen</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-ramps" name="property-features" value="Ramps/Level Access" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-ramps" class="ml-2 text-sm text-[var(--color-fern)]">Ramps/Level Access</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-wide-doorways" name="property-features" value="Wide Doorways" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-wide-doorways" class="ml-2 text-sm text-[var(--color-fern)]">Wide Doorways</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-hoist-provision" name="property-features" value="Hoist Provision" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-hoist-provision" class="ml-2 text-sm text-[var(--color-fern)]">Hoist Provision</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-smart-home" name="property-features" value="Smart Home Technology" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-smart-home" class="ml-2 text-sm text-[var(--color-fern)]">Smart Home Technology</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-on-site-staff" name="property-features" value="On-site Staff" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-on-site-staff" class="ml-2 text-sm text-[var(--color-fern)]">On-site Staff</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-garden" name="property-features" value="Garden/Outdoor Area" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-garden" class="ml-2 text-sm text-[var(--color-fern)]">Garden/Outdoor Area</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-close-to-transport" name="property-features" value="Close to Public Transport" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-close-to-transport" class="ml-2 text-sm text-[var(--color-fern)]">Close to Public Transport</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-air-conditioning" name="property-features" value="Air Conditioning" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-air-conditioning" class="ml-2 text-sm text-[var(--color-fern)]">Air Conditioning</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-heating" name="property-features" value="Heating" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-heating" class="ml-2 text-sm text-[var(--color-fern)]">Heating</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-pets-allowed" name="property-features" value="Pets Allowed" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-pets-allowed" class="ml-2 text-sm text-[var(--color-fern)]">Pets Allowed</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-emergency-call" name="property-features" value="Emergency Call System" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-emergency-call" class="ml-2 text-sm text-[var(--color-fern)]">Emergency Call System</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-security" name="property-features" value="Security System" class="h-4 w-4 text-[var(--color-teal)] border-[var(--color-gray)] rounded focus:ring-[var(--color-copper)]"><label for="feat-security" class="ml-2 text-sm text-[var(--color-fern)]">Security System</label></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="image-upload" class="block text-sm font-medium text-[var(--color-fern)] mb-1">Upload Property Image</label>
                    <input type="file" id="image-upload" accept="image/*" class="mt-1 block w-full text-sm text-[var(--color-fern)] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[var(--color-light-green)] file:text-[var(--color-teal)] hover:file:bg-[var(--color-linen)]" />
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-accommodation-submission" class="px-5 py-2 border border-[var(--color-gray)] rounded-md shadow-sm text-sm font-medium text-[var(--color-fern)] hover:bg-[var(--color-linen)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2 transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[var(--color-copper)] text-[var(--color-white)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-copper-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-copper)] focus:ring-offset-2">
                        Submit New Accommodation
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
            const participantAgeInput = document.getElementById('participant-age');
            const participantDisabilitySelect = document.getElementById('participant-disability');
            const participantLocationInput = document.getElementById('participant-location');
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
            // New input elements for the accommodation form
            const accommodationTitleInput = document.getElementById('accommodation-title');
            const accommodationAddressProvinceInput = document.getElementById('accommodation-address-province');
            const accommodationAddressCityInput = document.getElementById('accommodation-address-city');
            const accommodationDescriptionTextarea = document.getElementById('accommodation-description');
            const accommodationTypeCheckboxes = document.querySelectorAll('input[name="accommodation-type"]');
            const propertyTypeCheckboxes = document.querySelectorAll('input[name="property-type"]');
            const dateAvailableInput = document.getElementById('date-available');
            const numBedroomsInput = document.getElementById('num-bedrooms');
            const numBathroomsInput = document.getElementById('num-bathrooms');
            const numCurrentParticipantsInput = document.getElementById('num-current-participants');
            const genderParticipantsSelect = document.getElementById('gender-participants');
            const numVacanciesInput = document.getElementById('num-vacancies');
            const propertyFeaturesCheckboxes = document.querySelectorAll('input[name="property-features"]');
            const imageUploadInput = document.getElementById('image-upload');


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

            // --- Data Storage (for demonstration) ---
            let participants = [
                { name: 'Alice Wonderland', lastContact: '2024-07-17', accommodationType: 'SIL', age: 25, gender: 'female', disability: 'Physical', location: 'Dreamville' },
                { name: 'Bob The Builder', lastContact: '2024-07-16', accommodationType: 'Group Home', age: 30, gender: 'male', disability: 'Intellectual', location: 'Worktown' },
                { name: 'Charlie Chaplin', lastContact: '2024-07-15', accommodationType: 'Respite', age: 40, gender: 'male', disability: 'Psychosocial', location: 'Hollywood' }
            ];

            let accommodations = [
                {
                    title: 'Cozy SIL Apartment',
                    address: { province: 'NSW', city: 'Sydney' },
                    description: 'A comfortable apartment suitable for independent living.',
                    accommodationTypes: ['Apartment'],
                    propertyTypes: ['SIL'],
                    dateAvailable: '2024-08-01',
                    bedrooms: 2,
                    bathrooms: 1,
                    currentParticipants: 1,
                    genderParticipants: 'Mixed',
                    vacancies: 1,
                    features: ['Accessible Bathroom', 'Ramps/Level Access'],
                    image: 'https://placehold.co/120x90/CD8250/FFFFFF?text=SIL+Apartment'
                },
                {
                    title: 'Large Group Home',
                    address: { province: 'VIC', city: 'Melbourne' },
                    description: 'Spacious group home with 3 current residents.',
                    accommodationTypes: ['House'],
                    propertyTypes: ['SDA'],
                    dateAvailable: '2024-09-15',
                    bedrooms: 4,
                    bathrooms: 2,
                    currentParticipants: 3,
                    genderParticipants: 'Mixed',
                    vacancies: 1,
                    features: ['Accessible Kitchen', 'On-site Staff', 'Garden/Outdoor Area'],
                    image: 'https://placehold.co/120x90/CD8250/FFFFFF?text=Group+Home'
                }
            ];

            // --- Function to render participants list ---
            function renderParticipants(filteredParticipants = participants) {
                participantsContainer.innerHTML = ''; // Clear current list
                if (filteredParticipants.length === 0) {
                    participantsContainer.innerHTML = '<p class="text-[var(--color-fern)] text-center py-4">No participants found matching your criteria.</p>';
                    return;
                }
                filteredParticipants.forEach(p => {
                    const participantDiv = document.createElement('div');
                    participantDiv.className = 'bg-[var(--color-linen)] p-4 rounded-lg border border-[var(--color-gray)] flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm';
                    participantDiv.innerHTML = `
                        <div class="flex items-center mb-2 sm:mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round ${p.gender === 'female' ? 'text-pink-500' : 'text-[var(--color-teal)]'} mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                            <div>
                                <p class="font-semibold text-[var(--color-fern)]">${p.name}</p>
                                <p class="text-sm text-[var(--color-teal)]">Last Contact: ${p.lastContact}</p>
                                <p class="text-xs text-[var(--color-fern)]">Age: ${p.age}, Gender: ${p.gender}, Disability: ${p.disability}, Location: ${p.location}</p>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-[var(--color-teal)] text-[var(--color-white)] rounded-md hover:bg-[var(--color-fern)] transition duration-150">View Details</button>
                    `;
                    participantsContainer.appendChild(participantDiv);
                });
            }

            // --- Function to render accommodations list ---
            function renderAccommodations(filteredAccommodations = accommodations) {
                accommodationsContainer.innerHTML = ''; // Clear current list
                if (filteredAccommodations.length === 0) {
                    accommodationsContainer.innerHTML = '<p class="text-[var(--color-fern)] text-center py-4">No accommodations found matching your criteria.</p>';
                    return;
                }
                filteredAccommodations.forEach(acc => {
                    const accommodationDiv = document.createElement('div');
                    accommodationDiv.className = 'accommodation-item bg-[var(--color-linen)] p-4 rounded-lg border border-[var(--color-gray)] flex flex-col md:flex-row items-start md:items-center gap-4 shadow-sm';

                    // Determine image source, use placeholder if not available
                    const imageUrl = acc.image && acc.image !== '' ? acc.image : `https://placehold.co/120x90/CD8250/FFFFFF?text=No+Image`;

                    accommodationDiv.innerHTML = `
                        <img src="${imageUrl}" alt="${acc.title || 'Accommodation Image'}" class="w-full md:w-32 h-auto rounded-md object-cover shadow-sm">
                        <div class="flex-1">
                            <p class="font-semibold text-[var(--color-fern)] text-lg mb-1">${acc.title}</p>
                            <p class="text-[var(--color-teal)] text-sm">Address: ${acc.address.city}, ${acc.address.province}</p>
                            <p class="text-[var(--color-teal)] text-sm">Type: ${acc.accommodationTypes.join(', ')} (${acc.propertyTypes.join(', ')})</p>
                            <p class="text-[var(--color-teal)] text-sm">Bedrooms: ${acc.bedrooms}, Bathrooms: ${acc.bathrooms}</p>
                            <p class="text-[var(--color-teal)] text-sm">Participants: ${acc.currentParticipants} (${acc.genderParticipants}), Vacancies: ${acc.vacancies}</p>
                            <p class="text-[var(--color-teal)] text-sm">Available: ${acc.dateAvailable}</p>
                            <p class="text-[var(--color-teal)] text-sm">Features: ${acc.features.length > 0 ? acc.features.join(', ') : 'N/A'}</p>
                            <p class="text-[var(--color-teal)] text-sm">Status: ${acc.status}</p>
                        </div>
                        <div class="flex flex-row md:flex-col gap-2 md:gap-1 ml-auto items-end">
                            <button class="px-4 py-2 bg-[var(--color-teal)] text-[var(--color-white)] rounded-md hover:bg-[var(--color-fern)] transition duration-150 text-sm">Edit</button>
                            <button class="px-4 py-2 bg-red-600 text-[var(--color-white)] rounded-md hover:bg-red-700 transition duration-150 text-sm">Delete</button>
                        </div>
                    `;
                    accommodationsContainer.appendChild(accommodationDiv);
                });
            }


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
            // Initial rendering of lists
            renderParticipants();
            renderAccommodations();


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
            if (addParticipantButton) {
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

            // Handle form submission for adding new participant
            if (addParticipantForm) {
                addParticipantForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission

                    const newParticipant = {
                        name: participantNameInput.value.trim(),
                        gender: participantGenderSelect.value,
                        age: parseInt(participantAgeInput.value),
                        disability: participantDisabilitySelect.value,
                        location: participantLocationInput.value.trim(),
                        lastContact: participantLastContactInput.value
                    };

                    if (!newParticipant.name || !newParticipant.gender || !newParticipant.age || !newParticipant.disability || !newParticipant.location || !newParticipant.lastContact) {
                        alert('Please fill in all participant fields.');
                        return;
                    }

                    participants.push(newParticipant); // Add new participant to the array
                    renderParticipants(); // Re-render the list with the new participant

                    // Clear the form and hide the modal
                    addParticipantForm.reset();
                    addParticipantModal.classList.add('hidden');
                });
            }


            // --- JavaScript for Participant Search and Filters ---
            function filterParticipants() {
                const searchTerm = participantSearchInput.value.toLowerCase();
                const accommodationType = accommodationTypeFilter.value.toLowerCase();
                const age = ageFilter.value;
                const gender = genderFilter.value.toLowerCase();
                const disabilityType = disabilityTypeFilter.value.toLowerCase();
                const location = locationFilter.value.toLowerCase();

                const filtered = participants.filter(p => {
                    const matchesSearch = p.name.toLowerCase().includes(searchTerm);
                    const matchesAccommodationType = accommodationType === '' || p.accommodationType.toLowerCase().includes(accommodationType);
                    const matchesAge = age === '' || p.age === parseInt(age);
                    const matchesGender = gender === '' || p.gender.toLowerCase() === gender;
                    const matchesDisabilityType = disabilityType === '' || p.disability.toLowerCase().includes(disabilityType);
                    const matchesLocation = location === '' || p.location.toLowerCase().includes(location);

                    return matchesSearch && matchesAccommodationType && matchesAge && matchesGender && matchesDisabilityType && matchesLocation;
                });
                renderParticipants(filtered);
            }

            if (applyFiltersButton) applyFiltersButton.addEventListener('click', filterParticipants);


            // --- JavaScript for Accommodation Search ---
            if (accommodationSearchInput && accommodationsContainer) {
                accommodationSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const filteredAccommodations = accommodations.filter(acc => {
                        return acc.title.toLowerCase().includes(searchTerm) ||
                               acc.address.province.toLowerCase().includes(searchTerm) ||
                               acc.address.city.toLowerCase().includes(searchTerm) ||
                               acc.description.toLowerCase().includes(searchTerm) ||
                               acc.accommodationTypes.some(type => type.toLowerCase().includes(searchTerm)) ||
                               acc.propertyTypes.some(type => type.toLowerCase().includes(searchTerm));
                    });
                    renderAccommodations(filteredAccommodations);
                });
            }

            // --- JavaScript for Submit New Accommodation Modal ---
            if (submitAccommodationButton) {
                submitAccommodationButton.addEventListener('click', function() {
                    submitAccommodationModal.classList.remove('hidden');
                    // Set today's date as default for date available
                    dateAvailableInput.value = new Date().toISOString().slice(0, 10);
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

                    // Get selected accommodation types
                    const selectedAccommodationTypes = Array.from(accommodationTypeCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    // Get selected property types
                    const selectedPropertyTypes = Array.from(propertyTypeCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    // Get selected property features
                    const selectedPropertyFeatures = Array.from(propertyFeaturesCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    // Handle image upload (for demonstration, just store a placeholder or base64)
                    let imageUrl = '';
                    if (imageUploadInput.files && imageUploadInput.files[0]) {
                        // In a real application, you would upload this file to a server
                        // and get a URL back. For this example, we'll use a generic placeholder.
                        imageUrl = 'https://placehold.co/120x90/CD8250/FFFFFF?text=Uploaded+Image';
                        console.log('Image selected:', imageUploadInput.files[0].name);
                    }

                    const newAccommodation = {
                        title: accommodationTitleInput.value.trim(),
                        address: {
                            province: accommodationAddressProvinceInput.value.trim(),
                            city: accommodationAddressCityInput.value.trim()
                        },
                        description: accommodationDescriptionTextarea.value.trim(),
                        accommodationTypes: selectedAccommodationTypes,
                        propertyTypes: selectedPropertyTypes,
                        dateAvailable: dateAvailableInput.value,
                        bedrooms: parseInt(numBedroomsInput.value),
                        bathrooms: parseInt(numBathroomsInput.value),
                        currentParticipants: parseInt(numCurrentParticipantsInput.value),
                        genderParticipants: genderParticipantsSelect.value,
                        vacancies: parseInt(numVacanciesInput.value),
                        features: selectedPropertyFeatures,
                        image: imageUrl,
                        status: 'Active' // Default status for new accommodations
                    };

                    // Basic validation
                    if (!newAccommodation.title || !newAccommodation.address.province || !newAccommodation.address.city ||
                        newAccommodation.accommodationTypes.length === 0 || newAccommodation.propertyTypes.length === 0 ||
                        !newAccommodation.dateAvailable || isNaN(newAccommodation.bedrooms) || isNaN(newAccommodation.bathrooms) ||
                        isNaN(newAccommodation.currentParticipants) || !newAccommodation.genderParticipants || isNaN(newAccommodation.vacancies)) {
                        alert('Please fill in all required fields and select at least one Accommodation Type and Property Type.');
                        return;
                    }

                    accommodations.push(newAccommodation); // Add new accommodation to the array
                    renderAccommodations(); // Re-render the list with the new accommodation

                    submitAccommodationForm.reset();
                    submitAccommodationModal.classList.add('hidden');
                });
            }


            // --- Chat Contact Selection Logic ---
            if (chatContacts.length > 0) {
                chatContacts.forEach(contactButton => {
                    contactButton.addEventListener('click', function() {
                        chatContacts.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');

                        const contactName = this.querySelector('.font-medium').textContent;
                        const contactImageSrc = this.querySelector('img').src;
                        const contactId = this.dataset.contact;

                        if (chatWindowHeaderName) chatWindowHeaderName.textContent = contactName;
                        if (chatWindowHeaderImage) chatWindowHeaderImage.src = contactImageSrc;

                        loadChatMessages(contactId);
                    });
                });
            }
        });
    </script>
</body>
</html>
