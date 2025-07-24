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
        /* Original Light Theme Colors (now hardcoded as the only theme) */
        :root { /* Using :root to define variables for consistency, but no toggling logic */
            --color-sidebar-bg: #FFFFFF;
            --color-main-accent: #cc8e45;
            --color-main-accent-darker: #b87e3a;
            --color-page-background: #e1e7dd;
            --color-card-background: #FFFFFF;
            --color-text-dark: #333333;
            --color-text-light: #6B7280;
            --color-sidebar-text: #333333;
            --color-sidebar-icon: #333333;
            --color-sidebar-active-bg: #e0e7ff;
            --color-sidebar-active-text: #33595a;
            --color-sidebar-active-icon: #33595a;
            --color-border: #D1D5DB;
            --color-progress-track: #F0DDBF;
            --color-error: #EF4444;
            --color-success: #22C55E;
            --color-info-box-bg-transparent: rgba(62, 71, 50, 0.1);
            --color-white-text-on-dark: #FFFFFF;
            --color-search-input-text: #33595a;
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--color-page-background); /* Overall page background */
            color: var(--color-text-dark); /* Default text color */
            /* Removed transition as theme toggling is gone */
        }

        /* Custom styles for sidebar links */
        .sidebar-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            text-align: left;
            font-weight: 500;
            color: var(--color-sidebar-text);
            transition: all 0.2s ease-in-out;
            margin-bottom: 0.25rem;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .sidebar-link:hover {
            background-color: var(--color-main-accent);
            color: var(--color-white-text-on-dark);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .sidebar-link:hover svg {
            color: var(--color-white-text-on-dark);
        }

        /* Corrected active state for sidebar links to match screenshot */
        .sidebar-link.active {
            background-color: var(--color-sidebar-active-bg);
            color: var(--color-sidebar-active-text);
            font-weight: 600;
            border-radius: 0.5rem;
            width: 100%;
            margin-right: 0;
            padding-right: 1.25rem;
            right: 0;
            transform: none;
            box-shadow: none;
        }

        .sidebar-link.active svg {
            color: var(--color-sidebar-active-icon);
        }

        .sidebar-link svg {
            color: var(--color-sidebar-icon);
            margin-right: 0.75rem;
        }

        /* General button styling for lift effect */
        .btn-lift {
            transition: all 0.2s ease-in-out;
        }
        .btn-lift:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Style for scrollbar in main content area */
        main::-webkit-scrollbar {
            width: 8px;
        }
        main::-webkit-scrollbar-track {
            background: var(--color-border);
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb {
            background: var(--color-main-accent);
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: var(--color-main-accent-darker);
        }

        /* Chat bubble styles */
        .chat-bubble {
            max-width: 75%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            word-wrap: break-word;
            transition: all 0.2s ease-in-out;
        }
        .chat-bubble.incoming {
            background-color: var(--color-info-box-bg-transparent);
            color: var(--color-text-dark);
            align-self: flex-start;
            border-bottom-left-radius: 0.25rem;
        }
        .chat-bubble.outgoing {
            background-color: var(--color-main-accent);
            color: var(--color-white-text-on-dark);
            align-self: flex-end;
            border-bottom-right-radius: 0.25rem;
        }
        /* Apply lift effect to chat bubbles on hover */
        .chat-bubble:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .chat-message-time {
            font-size: 0.75rem;
            color: var(--color-text-light);
            margin-top: 0.25rem;
        }
        .chat-bubble.outgoing .chat-message-sender,
        .chat-bubble.outgoing .chat-message-time {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Progress bar styling for storage details */
        .progress-bar-container {
            background-color: var(--color-progress-track);
            border-radius: 9999px;
            height: 8px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: var(--color-main-accent);
            height: 100%;
            border-radius: 9999px;
        }

        /* Styles for action buttons within the request box */
        .request-action-button {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .request-action-button:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .request-action-button.accept {
            background-color: var(--color-success);
            color: var(--color-white-text-on-dark);
        }
        .request-action-button.revoke {
            background-color: var(--color-error);
            color: var(--color-white-text-on-dark);
        }

        /* Search input specific styling */
        .header-search-input {
            color: var(--color-search-input-text); /* Uses the new variable */
        }
        .header-search-input::placeholder { /* For placeholder text */
            color: var(--color-text-light);
            opacity: 1; /* Firefox default is lower */
        }
    </style>
</head>
<body class="min-h-screen bg-[var(--color-page-background)] text-[var(--color-text-dark)] flex">

    <!-- Sidebar for navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 bg-[var(--color-sidebar-bg)] text-[var(--color-sidebar-text)] w-64 p-6 space-y-6 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-20 md:w-72 shadow-lg">
        <div class="flex items-center justify-between md:justify-start mb-8">
            <div class="flex items-center">
                <!-- Icon for Company Panel - you can change this if needed -->
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase text-[var(--color-sidebar-icon)] mr-2">
                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                <h1 class="text-2xl font-extrabold text-[var(--color-sidebar-text)]">Company Panel</h1>
            </div>
            <button id="close-sidebar-button" class="text-[var(--color-sidebar-text)] md:hidden focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        <nav class="space-y-1">
            <button data-section="dashboard" class="sidebar-link active">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
            </button>
            <button data-section="participants" class="sidebar-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87c-.51-.11-.98-.31-1.43-.58"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Participants
            </button>
            <button data-section="accommodations" class="sidebar-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Accommodations
            </button>
            <button data-section="chat" class="sidebar-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Chat
                <span id="chat-notification-count" class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full hidden">0</span>
            </button>
            <!-- Removed Storage Link -->
            <!-- Removed Settings Link -->
        </nav>
    </aside>

    <!-- Main content area -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto">
        <div class="max-w-full mx-auto">
            <!-- Main Header Content (Dashboard Title, Icons) -->
            <header class="bg-[var(--color-search-input-text)] rounded-full shadow-md p-2 flex items-center justify-between z-10 mb-8">
                <!-- Mobile Header Content (Hamburger Menu, Title) -->
                <div class="flex items-center md:hidden w-full justify-between">
                    <h1 class="text-xl font-bold text-[var(--color-text-dark)]">Company Dashboard</h1>
                    <button id="mobile-menu-button" class="text-[var(--color-text-dark)] focus:outline-none p-2 rounded-md hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Desktop Header Content (Dashboard Title, Icons) -->
                <div class="hidden md:flex items-center justify-between w-full">
                    <div class="relative flex-1 max-w-md mx-auto">
                        <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-[var(--color-search-input-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-search-input-text)] focus:border-transparent text-sm w-full header-search-input">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-text-light)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 ml-auto">
                        <!-- Notification Icon -->
                        <button id="notification-button" class="text-[var(--color-white-text-on-dark)] hover:text-[var(--color-main-accent)] focus:outline-none p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        </button>
                        <!-- Settings Icon (This is the general settings icon, not the sidebar link) -->
                        <button class="text-[var(--color-white-text-on-dark)] hover:text-[var(--color-main-accent)] focus:outline-none p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        <!-- Profile Icon with Dropdown -->
                        <div class="relative">
                            <button id="profile-menu-button" class="flex items-center space-x-2 text-[var(--color-white-text-on-dark)] focus:outline-none p-1 rounded-full hover:text-[var(--color-main-accent)] transition-colors duration-200">
                                <img src="https://placehold.co/32x32/A78BFA/FFFFFF?text=AD" alt="Admin Avatar" class="w-8 h-8 rounded-full border-2 border-[var(--color-main-accent)]">
                                <span class="font-medium text-[var(--color-white-text-on-dark)] hidden sm:inline">Admin User</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <!-- Profile Dropdown Menu -->
                            <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-[var(--color-card-background)] rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                                <button data-action="profile" class="block px-4 py-2 text-sm text-[var(--color-text-dark)] hover:bg-gray-100 hover:text-[var(--color-main-accent)] w-full text-left transition-colors duration-150 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                                </button>
                                <button data-action="settings" class="block px-4 py-2 text-sm text-[var(--color-text-dark)] hover:bg-gray-100 hover:text-[var(--color-main-accent)] w-full text-left transition-colors duration-150 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                                </button>
                                <hr class="my-1 border-[var(--color-border)]">
                                <button data-action="logout" class="block px-4 py-2 text-sm text-[var(--color-error)] hover:bg-red-50 w-full text-left transition-colors duration-150 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out inline-block mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Section -->
            <div id="dashboard-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-xl shadow-lg mb-8">
                <h2 class="text-3xl font-bold text-[var(--color-text-dark)] mb-2">Welcome to your Company Dashboard!</h2>
                <p class="text-[var(--color-text-light)]">Here's a quick overview of your company's operations.</p>
                <div class="mt-6 p-4 bg-[var(--color-info-box-bg-transparent)] rounded-lg border border-[var(--color-border)] text-[var(--color-text-dark)]">
                    <p class="font-semibold">Company Updates:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>New policy updates rolled out on 2024-07-18.</li>
                        <li>Quarterly performance review meeting scheduled for next week.</li>
                        <li>Check the latest participant registrations.</li>
                    </ul>
                </div>
            </div>

            <!-- Participants Section -->
            <div id="participants-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-xl shadow-lg hidden">
                <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-4">Participants</h2>
                <p class="text-[var(--color-text-light)] mb-4">View and manage participant records.</p>
                <div class="mb-6 flex flex-wrap items-end gap-4">
                    <div class="relative flex-1 min-w-[180px]">
                        <label for="participant-search-input" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Search Participant</label>
                        <input type="text" id="participant-search-input" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 pl-10 text-[var(--color-text-dark)]" placeholder="Search by name or ID..." />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-text-light)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                    <div class="relative flex-1 min-w-[180px]">
                        <label for="accommodation-type-filter" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Accommodation Type</label>
                        <select id="accommodation-type-filter" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]">
                            <option value="">All</option>
                            <option value="SIL">SIL</option>
                            <option value="SDA">SDA</option>
                            <option value="Respite">Respite</option>
                            <option value="Group Home">Group Home</option>
                        </select>
                    </div>
                    <div class="relative flex-1 min-w-[120px]">
                        <label for="age-filter" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Age</label>
                        <input type="number" id="age-filter" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="Min Age" min="0" />
                    </div>
                    <div class="relative flex-1 min-w-[150px]">
                        <label for="gender-filter" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Gender</label>
                        <select id="gender-filter" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]">
                            <option value="">All</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="relative flex-1 min-w-[180px]">
                        <label for="disability-type-filter" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Disability Type</label>
                        <select id="disability-type-filter" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]">
                            <option value="">All</option>
                            <option value="Physical">Physical Disability</option>
                            <option value="Intellectual">Intellectual Disability</option>
                            <option value="Sensory">Sensory Disability</option>
                            <option value="Psychosocial">Psychosocial Disability</option>
                            <option value="Neurological">Neurological Disability</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="relative flex-1 min-w-[180px]">
                        <label for="location-filter" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Location</label>
                        <input type="text" id="location-filter" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="Filter by city/town" />
                    </div>
                    <button id="apply-filters-button" class="px-6 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] font-semibold rounded-md shadow-md hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out self-end btn-lift">
                        Apply Filters
                    </button>
                    <button id="add-participant-button" class="px-6 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] font-semibold rounded-md shadow-md hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out self-end btn-lift">
                        Add New Participant
                    </button>
                </div>
                <div class="space-y-4" id="participants-container">
                    <!-- Participant items will be rendered here by JavaScript -->
                </div>
            </div>

            <!-- Accommodations Section -->
            <div id="accommodations-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-xl shadow-lg hidden">
                <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-4">Accommodations</h2>
                <p class="text-[var(--color-text-light)] mb-4">Browse and manage available accommodations for participants.</p>
                <div class="mb-6 flex flex-wrap items-end gap-4">
                    <div class="relative flex-1 min-w-[180px]">
                        <label for="accommodation-search-input" class="block text-xs font-medium text-[var(--color-text-dark)] mb-1">Search Accommodation</label>
                        <input type="text" id="accommodation-search-input" class="block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 pl-10 text-[var(--color-text-dark)]" placeholder="Search by title, address, or type..." />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-[var(--color-text-light)]"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                    </div>
                    <button id="submit-accommodation-button" class="px-6 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] font-semibold rounded-md shadow-md hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out self-end btn-lift">
                        Submit New Accommodation
                    </button>
                </div>
                <!-- Changed from grid to space-y for listing layout -->
                <div class="space-y-4" id="accommodations-container">
                    <!-- Accommodation items will be rendered here by JavaScript -->
                </div>
            </div>

            <!-- Chat Section -->
            <div id="chat-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-xl shadow-lg hidden flex">
                <div class="w-1/3 border-r border-[var(--color-border)] pr-4">
                    <h3 class="text-xl font-bold text-[var(--color-text-dark)] mb-4">Contacts</h3>
                    <div class="space-y-2 chat-contacts-container">
                        <!-- Chat contacts will be rendered here by JavaScript -->
                    </div>

                    <!-- Incoming Request Box -->
                    <div id="john-doe-request-box" class="mt-6 p-4 bg-[var(--color-info-box-bg-transparent)] rounded-lg border border-[var(--color-border)] text-[var(--color-text-dark)] text-sm">
                        <p class="font-semibold mb-2">Incoming Request from John Doe</p>
                        <p>"John Doe would like to connect with your company."</p>
                        <div class="flex space-x-2 mt-3">
                            <button class="request-action-button accept" data-request-id="john-doe-connect">Accept</button>
                            <button class="request-action-button revoke" data-request-id="john-doe-connect">Revoke</button>
                        </div>
                    </div>
                </div>

                <div class="flex-1 flex flex-col pl-4">
                    <div class="border-b border-[var(--color-border)] pb-4 mb-4 flex items-center">
                        <img src="https://placehold.co/40x40/FF7F50/FFFFFF?text=JS" alt="Contact Avatar" class="w-10 h-10 rounded-full mr-3" id="chat-window-header-image">
                        <p class="text-xl font-bold text-[var(--color-text-dark)]" id="chat-window-header-name">Jane Smith</p>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-3 p-2" id="chat-messages-container">
                        <!-- Chat messages will be loaded here by JavaScript -->
                    </div>
                    <div class="mt-4 flex items-center">
                        <input type="text" placeholder="Type your message..." class="flex-1 p-3 rounded-full border border-[var(--color-border)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:border-transparent text-[var(--color-text-dark)]">
                        <button class="ml-3 p-3 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] rounded-full hover:bg-[var(--color-main-accent-darker)] transition duration-150 btn-lift">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Removed Storage Section -->
            <!-- Removed Settings Section -->


            <!-- Profile Section (Accessible via dropdown) -->
            <div id="profile-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-lg shadow-md hidden">
                <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-4">Your Company Profile</h2>
                <p class="text-[var(--color-text-light)] mb-2">Manage your personal and company-related information.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[var(--color-info-box-bg-transparent)] p-4 rounded-md">
                        <label for="company-name" class="block text-sm font-medium text-[var(--color-text-dark)]">Company Name</label>
                        <input type="text" id="company-name" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="Acme Corp" />
                    </div>
                    <div class="bg-[var(--color-info-box-bg-transparent)] p-4 rounded-md">
                        <label for="admin-email" class="block text-sm font-medium text-[var(--color-text-dark)]">Admin Email</label>
                        <input type="email" id="admin-email" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="admin@acmecorp.com" />
                    </div>
                    <div class="bg-[var(--color-info-box-bg-transparent)] p-4 rounded-md col-span-full">
                        <label for="company-bio" class="block text-sm font-medium text-[var(--color-text-dark)]">Company Description</label>
                        <textarea id="company-bio" rows="3" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="Describe your company's mission and services..."></textarea>
                    </div>
                </div>
                <button class="mt-6 px-6 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] font-semibold rounded-md shadow-md hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out btn-lift">
                    Save Company Profile
                </button>
            </div>

            <!-- Settings Section (Accessible via dropdown) -->
            <!-- Note: This settings section is still here because it's linked from the profile dropdown, not the sidebar. -->
            <div id="settings-section" class="dashboard-section p-6 bg-[var(--color-card-background)] rounded-lg shadow-md hidden">
                <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-4">Account Settings</h2>
                <p class="text-[var(--color-text-light)] mb-2">Adjust your account preferences and security settings.</p>
                <div class="space-y-4">
                    <div class="bg-[var(--color-info-box-bg-transparent)] p-4 rounded-lg shadow-sm">
                        <label for="notifications" class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm font-medium text-[var(--color-text-dark)]">Email Notifications</span>
                            <input type="checkbox" id="notifications" class="form-checkbox h-5 w-5 text-[var(--color-main-accent)] border-[var(--color-border)] rounded" checked />
                        </label>
                    </div>
                    <div class="bg-[var(--color-info-box-bg-transparent)] p-4 rounded-lg shadow-sm">
                        <label for="theme" class="block text-sm font-medium text-[var(--color-text-dark)] mb-2">Theme Preference</label>
                        <select id="theme" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]">
                            <option>Light</option>
                            <option>Dark</option>
                            <option>System Default</option>
                        </select>
                    </div>
                    <button class="px-6 py-2 bg-[var(--color-error)] text-[var(--color-white-text-on-dark)] font-semibold rounded-md shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-[var(--color-error)] focus:ring-offset-2 transition duration-150 ease-in-out btn-lift">
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Add New Participant Modal -->
    <div id="add-participant-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-card-background)] p-8 rounded-lg shadow-xl w-full max-w-md relative">
            <button id="close-participant-modal" class="absolute top-4 right-4 text-[var(--color-text-dark)] hover:text-[var(--color-main-accent)] p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-6">Add New Participant</h2>
            <form id="add-participant-form">
                <div class="mb-4">
                    <label for="participant-name" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Participant Name</label>
                    <input type="text" id="participant-name" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required />
                </div>
                <div class="mb-4">
                    <label for="participant-gender" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Gender</label>
                    <select id="participant-gender" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="participant-age" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Age</label>
                    <input type="number" id="participant-age" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="participant-disability" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Type of Disability</label>
                    <select id="participant-disability" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required>
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
                    <label for="participant-location" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Location</label>
                    <input type="text" id="participant-location" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required />
                </div>
                <div class="mb-6">
                    <label for="participant-last-contact" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Last Contact Date</label>
                    <input type="date" id="participant-last-contact" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required />
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-add-participant" class="px-5 py-2 border border-[var(--color-border)] rounded-md shadow-sm text-sm font-medium text-[var(--color-text-dark)] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out btn-lift">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 btn-lift">
                        Add Participant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit New Accommodation Modal -->
    <div id="submit-accommodation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-card-background)] p-8 rounded-lg shadow-xl w-full max-w-2xl relative overflow-y-auto max-h-[90vh]">
            <button id="close-accommodation-modal" class="absolute top-4 right-4 text-[var(--color-text-dark)] hover:text-[var(--color-main-accent)] p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-6" id="accommodation-modal-title">Submit New Accommodation</h2>
            <form id="submit-accommodation-form">
                <input type="hidden" id="accommodation-index" value="-1"> <!-- Hidden field to store index for editing -->
                <div class="mb-4">
                    <label for="accommodation-title" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Property Title</label>
                    <input type="text" id="accommodation-title" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="e.g., Spacious Home with Garden" required />
                </div>
                <div class="mb-4">
                    <label for="accommodation-phone" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Telephone/Phone Number</label>
                    <input type="tel" id="accommodation-phone" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="e.g., +1234567890" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Property Address</label>
                    <input type="text" id="accommodation-address-province" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 mb-2 text-[var(--color-text-dark)]" placeholder="Province/State" required />
                    <input type="text" id="accommodation-address-city" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="City/Town" required />
                </div>
                <div class="mb-4">
                    <label for="accommodation-description" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Property Description</label>
                    <textarea id="accommodation-description" rows="3" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" placeholder="Describe the property..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-text-dark)] mb-2">Accommodation Type</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-apartment" name="accommodation-type" value="Apartment" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]">
                            <label for="acc-type-apartment" class="ml-2 text-sm text-[var(--color-text-dark)]">Apartment</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-house" name="accommodation-type" value="House" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]">
                            <label for="acc-type-house" class="ml-2 text-sm text-[var(--color-text-dark)]">House</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="acc-type-villa" name="accommodation-type" value="Villa/Duplex/Townhouse" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]">
                            <label for="acc-type-villa" class="ml-2 text-sm text-[var(--color-text-dark)]">Villa/Duplex/Townhouse</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-text-dark)] mb-2">Property Type</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="prop-type-sda" name="property-type" value="SDA" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]">
                            <label for="prop-type-sda" class="ml-2 text-sm text-[var(--color-text-dark)]">SDA</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="prop-type-sil" name="property-type" value="SIL" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]">
                            <label for="prop-type-sil" class="ml-2 text-sm text-[var(--color-text-dark)]">SIL</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="date-available" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Date Available</label>
                    <input type="date" id="date-available" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required />
                </div>
                <div class="mb-4">
                    <label for="num-bedrooms" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Number of Bedrooms</label>
                    <input type="number" id="num-bedrooms" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="num-bathrooms" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Number of Bathrooms</label>
                    <input type="number" id="num-bathrooms" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="num-current-participants" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Number of Current Participants</label>
                    <input type="number" id="num-current-participants" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" min="0" required />
                </div>
                <div class="mb-4">
                    <label for="gender-participants" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Gender of Participants in Accommodation</label>
                    <select id="gender-participants" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" required>
                        <option value="">Select Gender</option>
                        <option value="Mixed">Mixed</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="num-vacancies" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Current Number of Vacancies</label>
                    <input type="number" id="num-vacancies" class="mt-1 block w-full rounded-md border-[var(--color-border)] shadow-sm focus:border-[var(--color-main-accent)] focus:ring-[var(--color-main-accent)] sm:text-sm p-2 text-[var(--color-text-dark)]" min="0" required />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--color-text-dark)] mb-2">Property Features (Check all that Apply)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center"><input type="checkbox" id="feat-accessible-bathroom" name="property-features" value="Accessible Bathroom" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-accessible-bathroom" class="ml-2 text-sm text-[var(--color-text-dark)]">Accessible Bathroom</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-accessible-kitchen" name="property-features" value="Accessible Kitchen" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-accessible-kitchen" class="ml-2 text-sm text-[var(--color-text-dark)]">Accessible Kitchen</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-ramps" name="property-features" value="Ramps/Level Access" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-ramps" class="ml-2 text-sm text-[var(--color-text-dark)]">Ramps/Level Access</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-wide-doorways" name="property-features" value="Wide Doorways" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-wide-doorways" class="ml-2 text-sm text-[var(--color-text-dark)]">Wide Doorways</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-hoist-provision" name="property-features" value="Hoist Provision" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-hoist-provision" class="ml-2 text-sm text-[var(--color-text-dark)]">Hoist Provision</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-smart-home" name="property-features" value="Smart Home Technology" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-smart-home" class="ml-2 text-sm text-[var(--color-text-dark)]">Smart Home Technology</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-on-site-staff" name="property-features" value="On-site Staff" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-on-site-staff" class="ml-2 text-sm text-[var(--color-text-dark)]">On-site Staff</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-garden" name="property-features" value="Garden/Outdoor Area" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-garden" class="ml-2 text-sm text-[var(--color-text-dark)]">Garden/Outdoor Area</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-close-to-transport" name="property-features" value="Close to Public Transport" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-close-to-transport" class="ml-2 text-sm text-[var(--color-text-dark)]">Close to Public Transport</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-air-conditioning" name="property-features" value="Air Conditioning" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-air-conditioning" class="ml-2 text-sm text-[var(--color-text-dark)]">Air Conditioning</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-heating" name="property-features" value="Heating" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-heating" class="ml-2 text-sm text-[var(--color-text-dark)]">Heating</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-pets-allowed" name="property-features" value="Pets Allowed" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-pets-allowed" class="ml-2 text-sm text-[var(--color-text-dark)]">Pets Allowed</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-emergency-call" name="property-features" value="Emergency Call System" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-emergency-call" class="ml-2 text-sm text-[var(--color-text-dark)]">Emergency Call System</label></div>
                        <div class="flex items-center"><input type="checkbox" id="feat-security" name="property-features" value="Security System" class="h-4 w-4 text-[var(--color-main-accent)] border-[var(--color-border)] rounded focus:ring-[var(--color-main-accent)]"><label for="feat-security" class="ml-2 text-sm text-[var(--color-text-dark)]">Security System</label></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="image-upload" class="block text-sm font-medium text-[var(--color-text-dark)] mb-1">Upload Property Image</label>
                    <input type="file" id="image-upload" accept="image/*" class="mt-1 block w-full text-sm text-[var(--color-text-dark)] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-[var(--color-main-accent)] hover:file:bg-gray-200" />
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-accommodation-submission" class="px-5 py-2 border border-[var(--color-border)] rounded-md shadow-sm text-sm font-medium text-[var(--color-text-dark)] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 transition duration-150 ease-in-out btn-lift">
                        Cancel
                    </button>
                    <button type="submit" id="submit-accommodation-form-button" class="px-5 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 btn-lift">
                        Submit New Accommodation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notification-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-card-background)] p-8 rounded-lg shadow-xl w-full max-w-sm relative">
            <button id="close-notification-modal" class="absolute top-4 right-4 text-[var(--color-text-dark)] hover:text-[var(--color-main-accent)] p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-4">Notifications</h2>
            <div id="notification-content" class="text-[var(--color-text-dark)]">
                <p>No new notifications at this time.</p>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" id="ok-notification-modal" class="px-5 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 btn-lift">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Participant Details Modal -->
    <div id="participant-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[var(--color-card-background)] p-8 rounded-lg shadow-xl w-full max-w-md relative">
            <button id="close-participant-details-modal" class="absolute top-4 right-4 text-[var(--color-text-dark)] hover:text-[var(--color-main-accent)] p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-[var(--color-text-dark)] mb-6">Participant Details</h2>
            <div id="participant-details-content" class="space-y-2 text-[var(--color-text-dark)]">
                <!-- Details will be populated here by JavaScript -->
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" id="ok-participant-details-modal" class="px-5 py-2 bg-[var(--color-main-accent)] text-[var(--color-white-text-on-dark)] rounded-md shadow-sm text-sm font-medium hover:bg-[var(--color-main-accent-darker)] focus:outline-none focus:ring-2 focus:ring-[var(--color-main-accent)] focus:ring-offset-2 btn-lift">
                    OK
                </button>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement;
            const sidebar = document.getElementById('sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeSidebarButton = document.getElementById('close-sidebar-button');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            const dashboardSections = document.querySelectorAll('.dashboard-section');

            // Header elements
            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileDropdownActions = profileDropdown ? profileDropdown.querySelectorAll('button[data-action]') : [];
            const notificationButton = document.getElementById('notification-button');


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

            // Accommodation elements
            const accommodationSearchInput = document.getElementById('accommodation-search-input');
            const accommodationsContainer = document.getElementById('accommodations-container');
            const submitAccommodationButton = document.getElementById('submit-accommodation-button');

            // Submit Accommodation Modal elements
            const submitAccommodationModal = document.getElementById('submit-accommodation-modal');
            const closeAccommodationModalButton = document.getElementById('close-accommodation-modal');
            const cancelAccommodationSubmissionButton = document.getElementById('cancel-accommodation-submission');
            const submitAccommodationForm = document.getElementById('submit-accommodation-form');
            const accommodationModalTitle = document.getElementById('accommodation-modal-title');
            const accommodationIndexInput = document.getElementById('accommodation-index'); // Hidden input for editing
            const accommodationTitleInput = document.getElementById('accommodation-title');
            const accommodationPhoneInput = document.getElementById('accommodation-phone'); // New phone input
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
            const submitAccommodationFormButton = document.getElementById('submit-accommodation-form-button'); // Reference to the submit button

            // Notification Modal elements
            const notificationModal = document.getElementById('notification-modal');
            const closeNotificationModalButton = document.getElementById('close-notification-modal');
            const okNotificationModalButton = document.getElementById('ok-notification-modal');
            const notificationContent = document.getElementById('notification-content');

            // Participant Details Modal elements
            const participantDetailsModal = document.getElementById('participant-details-modal');
            const closeParticipantDetailsModalButton = document.getElementById('close-participant-details-modal');
            const okParticipantDetailsModalButton = document.getElementById('ok-participant-details-modal');
            const participantDetailsContent = document.getElementById('participant-details-content');


            // Chat elements
            let chatContacts = document.querySelectorAll('.chat-contact');
            const chatWindowHeaderName = document.querySelector('#chat-section .border-b p');
            const chatWindowHeaderImage = document.querySelector('#chat-section .border-b img');
            const chatMessagesContainer = document.querySelector('#chat-section .overflow-y-auto');
            const johnDoeRequestBox = document.getElementById('john-doe-request-box');
            const chatContactsContainer = document.querySelector('.chat-contacts-container');
            const chatNotificationCountSpan = document.getElementById('chat-notification-count');


            // Sample chat data (for demonstration)
            let chatData = {
                'jane-smith': {
                    name: 'Jane Smith',
                    image: 'https://placehold.co/40x40/FF7F50/FFFFFF?text=JS',
                    messages: [
                        { sender: 'Jane Smith', message: 'Hi team! Just wanted to share the latest project updates.', time: '10:00 AM', type: 'incoming', read: true },
                        { sender: 'You', message: 'Thanks Jane! I\'ll review them now.', time: '10:05 AM', type: 'outgoing', read: true },
                        { sender: 'Jane Smith', message: 'Great! Let me know if you have any questions.', time: '10:06 AM', type: 'incoming', read: false }, // Mark this as unread
                    ]
                },
                'mark-jones': {
                    name: 'Mark Jones',
                    image: 'https://placehold.co/40x40/6A5ACD/FFFFFF?text=MJ',
                    messages: [
                        { sender: 'Mark Jones', message: 'Good morning! Meeting at 11 AM today.', time: 'Yesterday', type: 'incoming', read: true },
                        { sender: 'You', message: 'Got it, thanks Mark!', time: 'Yesterday', type: 'outgoing', read: true },
                    ]
                },
                'sarah-davis': {
                    name: 'Sarah Davis',
                    image: 'https://placehold.co/40x40/3CB371/FFFFFF?text=SD',
                    messages: [
                        { sender: 'Sarah Davis', message: 'Please submit your weekly reports by EOD.', time: '2 days ago', type: 'incoming', read: false }, // Mark this as unread
                    ]
                },
                // John Doe will be added here dynamically
            };

            // --- Data Storage (for demonstration) ---
            let participants = [
                { id: 'p1', name: 'Emily Johnson', lastContact: '2024-07-20', accommodationType: 'SIL', age: 28, gender: 'female', disability: 'Physical', location: 'Melbourne' },
                { id: 'p2', name: 'Daniel Garcia', lastContact: '2024-07-19', accommodationType: 'SDA', age: 35, gender: 'male', disability: 'Intellectual', location: 'Melbourne' },
                { id: 'p3', name: 'Sophia Lee', lastContact: '2024-07-18', accommodationType: 'Respite', age: 22, gender: 'female', disability: 'Sensory', location: 'Melbourne' },
                { id: 'p4', name: 'Michael Chen', lastContact: '2024-07-17', accommodationType: 'Group Home', age: 42, gender: 'male', disability: 'Psychosocial', location: 'Melbourne' },
                { id: 'p5', name: 'Olivia Martinez', lastContact: '2024-07-16', accommodationType: 'SIL', age: 31, gender: 'female', disability: 'Neurological', location: 'Melbourne' }
            ];

            let accommodations = [
                {
                    id: 'a1',
                    title: 'Modern Apartment with City View',
                    phone: '+61 412 345 678',
                    address: { province: 'Victoria', city: 'Melbourne' },
                    description: 'Spacious 2-bedroom apartment, fully accessible, near CBD.',
                    accommodationTypes: ['Apartment'],
                    propertyTypes: ['SIL'],
                    dateAvailable: '2024-08-10',
                    bedrooms: 2,
                    bathrooms: 2,
                    currentParticipants: 1,
                    genderParticipants: 'Mixed',
                    vacancies: 1,
                    features: ['Accessible Bathroom', 'Wide Doorways', 'Air Conditioning'],
                    image: 'https://placehold.co/250x150/cc8e45/FFFFFF?text=Apt+City+View',
                    status: 'Active'
                },
                {
                    id: 'a2',
                    title: 'Suburban Family House',
                    phone: '+61 400 111 222',
                    address: { province: 'Victoria', city: 'Melbourne' },
                    description: 'Comfortable house with a large garden, ideal for a small group.',
                    accommodationTypes: ['House'],
                    propertyTypes: ['Group Home'],
                    dateAvailable: '2024-09-01',
                    bedrooms: 4,
                    bathrooms: 3,
                    currentParticipants: 3,
                    genderParticipants: 'Mixed',
                    vacancies: 1,
                    features: ['Garden/Outdoor Area', 'On-site Staff', 'Close to Public Transport'],
                    image: 'https://placehold.co/250x150/cc8e45/FFFFFF?text=Family+House',
                    status: 'Active'
                },
                {
                    id: 'a3',
                    title: 'Accessible Villa near Park',
                    phone: '+61 455 999 888',
                    address: { province: 'Victoria', city: 'Melbourne' },
                    description: 'Beautiful villa designed for wheelchair access, perfect for respite care.',
                    accommodationTypes: ['Villa/Duplex/Townhouse'],
                    propertyTypes: ['Respite'],
                    dateAvailable: '2024-08-25',
                    bedrooms: 3,
                    bathrooms: 2,
                    currentParticipants: 0,
                    genderParticipants: 'Mixed',
                    vacancies: 3,
                    features: ['Accessible Bathroom', 'Ramps/Level Access', 'Hoist Provision', 'Pets Allowed'],
                    image: 'https://placehold.co/250x150/cc8e45/FFFFFF?text=Park+Villa',
                    status: 'Active'
                },
                {
                    id: 'a4',
                    title: 'Cozy Townhouse in Quiet Neighborhood',
                    phone: '+61 423 765 432',
                    address: { province: 'Victoria', city: 'Melbourne' },
                    description: 'Safe and peaceful townhouse, close to amenities.',
                    accommodationTypes: ['Townhouse'],
                    propertyTypes: ['SIL'],
                    dateAvailable: '2024-10-01',
                    bedrooms: 2,
                    bathrooms: 1,
                    currentParticipants: 1,
                    genderParticipants: 'Female',
                    vacancies: 1,
                    features: ['Security System', 'Heating'],
                    image: 'https://placehold.co/250x150/cc8e45/FFFFFF?text=Townhouse',
                    status: 'Active'
                },
                {
                    id: 'a5',
                    title: 'SDA Certified Duplex',
                    phone: '+61 498 765 432',
                    address: { province: 'Victoria', city: 'Melbourne' },
                    description: 'Newly built duplex with high-level SDA features.',
                    accommodationTypes: ['Duplex'],
                    propertyTypes: ['SDA'],
                    dateAvailable: '2024-09-05',
                    bedrooms: 3,
                    bathrooms: 2,
                    currentParticipants: 0,
                    genderParticipants: 'Male',
                    vacancies: 2,
                    features: ['Smart Home Technology', 'Emergency Call System', 'Accessible Kitchen'],
                    image: 'https://placehold.co/250x150/cc8e45/FFFFFF?text=SDA+Duplex',
                    status: 'Active'
                }
            ];

            // --- Function to update chat notification count ---
            function updateChatNotificationCount() {
                let unreadCount = 0;
                for (const contactId in chatData) {
                    const contact = chatData[contactId];
                    unreadCount += contact.messages.filter(msg => msg.type === 'incoming' && !msg.read).length;
                }

                if (chatNotificationCountSpan) {
                    if (unreadCount > 0) {
                        chatNotificationCountSpan.textContent = unreadCount;
                        chatNotificationCountSpan.classList.remove('hidden');
                    } else {
                        chatNotificationCountSpan.classList.add('hidden');
                    }
                }
            }

            // --- Function to render participants list ---
            function renderParticipants(filteredParticipants = participants) {
                if (!participantsContainer) {
                    console.error("Participants container not found. Cannot render participants.");
                    return;
                }
                participantsContainer.innerHTML = '';
                if (filteredParticipants.length === 0) {
                    participantsContainer.innerHTML = '<p class="text-[var(--color-text-dark)] text-center py-4">No participants found matching your criteria.</p>';
                    return;
                }
                filteredParticipants.forEach(p => {
                    const participantDiv = document.createElement('div');
                    participantDiv.className = 'bg-[var(--color-info-box-bg-transparent)] p-4 rounded-lg border border-[var(--color-border)] flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm btn-lift';
                    participantDiv.innerHTML = `
                        <div class="flex items-center mb-2 sm:mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round ${p.gender === 'female' ? 'text-pink-500' : 'text-[var(--color-main-accent)]'} mr-2"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
                            <div>
                                <p class="font-semibold text-[var(--color-text-dark)]">${p.name}</p>
                                <p class="text-sm text-[var(--color-text-light)]">Last Contact: ${p.lastContact}</p>
                                <p class="text-xs text-[var(--color-text-light)]">Accommodation: ${p.accommodationType}, Age: ${p.age}, Gender: ${p.gender}, Disability: ${p.disability}, Location: ${p.location}</p>
                            </div>
                        </div>
                        <button class="view-details-participant px-4 py-2 bg-[var(--color-sidebar-active-icon)] text-[var(--color-white-text-on-dark)] rounded-md hover:bg-[var(--color-progress-track)] transition duration-150 btn-lift" data-participant-id="${p.id}">View Details</button>
                    `;
                    participantsContainer.appendChild(participantDiv);
                });
            }

            // --- Function to render accommodations list ---
            function renderAccommodations(filteredAccommodations = accommodations) {
                if (!accommodationsContainer) {
                    console.error("Accommodations container not found. Cannot render accommodations.");
                    return;
                }
                accommodationsContainer.innerHTML = '';
                if (filteredAccommodations.length === 0) {
                    accommodationsContainer.innerHTML = '<p class="text-[var(--color-text-dark)] text-center py-4 col-span-full">No accommodations found matching your criteria.</p>';
                    return;
                }
                filteredAccommodations.forEach((acc, index) => {
                    const accommodationDiv = document.createElement('div');
                    // Changed class for listing layout
                    accommodationDiv.className = 'bg-[var(--color-info-box-bg-transparent)] p-4 rounded-lg border border-[var(--color-border)] shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4 btn-lift';

                    const imageUrl = acc.image && acc.image !== '' ? acc.image : `https://placehold.co/250x150/cc8e45/FFFFFF?text=No+Image`;

                    accommodationDiv.innerHTML = `
                        <img src="${imageUrl}" alt="${acc.title || 'Accommodation Image'}" class="w-full md:w-40 h-auto object-cover rounded-md mb-4 md:mb-0 shadow-sm">
                        <div class="flex-1 w-full">
                            <p class="font-semibold text-[var(--color-text-dark)] text-lg mb-1">${acc.title} <span class="text-sm text-[var(--color-text-light)] ml-2">${acc.phone ? `(${acc.phone})` : ''}</span></p>
                            <p class="text-[var(--color-text-light)] text-sm">Address: ${acc.address.city}, ${acc.address.province}</p>
                            <p class="text-[var(--color-text-light)] text-sm">Type: ${acc.accommodationTypes.join(', ')} (${acc.propertyTypes.join(', ')})</p>
                            <p class="text-[var(--color-text-light)] text-sm">Bedrooms: ${acc.bedrooms}, Bathrooms: ${acc.bathrooms}</p>
                            <p class="text-[var(--color-text-light)] text-sm">Participants: ${acc.currentParticipants} (${acc.genderParticipants}), Vacancies: ${acc.vacancies}</p>
                            <p class="text-[var(--color-text-light)] text-sm">Available: ${acc.dateAvailable}</p>
                            <p class="text-[var(--color-text-light)] text-sm mb-4">Features: ${acc.features.length > 0 ? acc.features.join(', ') : 'N/A'}</p>
                            <p class="text-[var(--color-text-light)] text-sm">Status: ${acc.status}</p>
                        </div>
                        <div class="flex justify-end w-full md:w-auto gap-2 mt-4 md:mt-0">
                            <button class="edit-accommodation-btn px-4 py-2 bg-[var(--color-sidebar-active-icon)] text-[var(--color-white-text-on-dark)] rounded-md hover:bg-[var( --color-progress-track)] transition duration-150 text-sm btn-lift" data-index="${index}">Edit</button>
                            <button class="delete-accommodation-btn px-4 py-2 bg-[var(--color-error)] text-[var(--color-white-text-on-dark)] rounded-md hover:bg-red-700 transition duration-150 text-sm btn-lift" data-index="${index}">Delete</button>
                        </div>
                    `;
                    accommodationsContainer.appendChild(accommodationDiv);
                });
            }

            // Function to render chat contacts
            function renderChatContacts() {
                chatContactsContainer.innerHTML = '';
                for (const id in chatData) {
                    const contact = chatData[id];
                    const contactButton = document.createElement('button');
                    contactButton.className = 'chat-contact flex items-center w-full p-3 rounded-lg hover:bg-gray-100 transition-colors duration-150 cursor-pointer btn-lift';
                    contactButton.dataset.contact = id;
                    contactButton.innerHTML = `
                        <img src="${contact.image}" alt="${contact.name}" class="w-10 h-10 rounded-full mr-3">
                        <div class="flex-1">
                            <p class="font-medium text-[var(--color-text-dark)]">${contact.name}</p>
                            <p class="text-sm text-[var(--color-text-light)]">Last message...</p>
                        </div>
                        <button class="delete-chat-contact-btn text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-gray-200 transition-colors duration-200 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                    `;
                    chatContactsContainer.appendChild(contactButton);
                }
                // Re-attach event listeners after rendering
                chatContacts = document.querySelectorAll('.chat-contact');
                chatContacts.forEach(btn => {
                    btn.removeEventListener('click', handleChatContactClick); // Remove existing to prevent duplicates
                    btn.addEventListener('click', handleChatContactClick);
                });
                document.querySelectorAll('.delete-chat-contact-btn').forEach(btn => {
                    btn.addEventListener('click', handleDeleteChatContact);
                });
                updateChatNotificationCount(); // Update count after rendering contacts
            }


            // Function to show a specific section
            function showSection(sectionId) {
                dashboardSections.forEach(section => {
                    section.classList.add('hidden');
                });
                const targetSection = document.getElementById(`${sectionId}-section`);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
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
                chatMessagesContainer.innerHTML = '';
                const contact = chatData[contactId];
                if (!contact) {
                    chatMessagesContainer.innerHTML = '<p class="text-[var(--color-text-dark)] text-center py-4">Select a contact to view messages.</p>';
                    return;
                }
                const messages = contact.messages || [];
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
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;

                // Mark all incoming messages for this contact as read
                contact.messages.forEach(msg => {
                    if (msg.type === 'incoming') {
                        msg.read = true;
                    }
                });
                updateChatNotificationCount(); // Update count after messages are read
            }

            // Initial display: Show Dashboard section and set active link
            showSection('dashboard');
            updateActiveLink('dashboard');
            // Initial rendering of lists (only if their containers exist)
            if (participantsContainer) renderParticipants();
            if (accommodationsContainer) renderAccommodations();
            if (chatContactsContainer) renderChatContacts(); // Call this to set initial count


            // Function to handle chat contact clicks (refactored for reusability)
            function handleChatContactClick() {
                chatContacts.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const contactName = this.querySelector('.font-medium').textContent;
                const contactImageSrc = this.querySelector('img').src;
                const contactId = this.dataset.contact;

                if (chatWindowHeaderName) chatWindowHeaderName.textContent = contactName;
                if (chatWindowHeaderImage) chatWindowHeaderImage.src = contactImageSrc;

                loadChatMessages(contactId);
            }

            // Function to handle deleting a chat contact
            function handleDeleteChatContact(event) {
                event.stopPropagation(); // Prevent the parent button's click event from firing
                const button = event.currentTarget;
                const contactIdToDelete = button.closest('.chat-contact').dataset.contact;

                if (confirm(`Are you sure you want to delete this chat with ${chatData[contactIdToDelete].name}?`)) {
                    delete chatData[contactIdToDelete];
                    renderChatContacts(); // Re-render the contacts list
                    // If the deleted contact was active, clear the chat window
                    if (chatWindowHeaderName.textContent === chatData[contactIdToDelete]?.name) { // This check might fail if chatData[contactIdToDelete] is already deleted
                        chatWindowHeaderName.textContent = 'Select a contact';
                        chatWindowHeaderImage.src = 'https://placehold.co/40x40/CCCCCC/FFFFFF?text=NA';
                        chatMessagesContainer.innerHTML = '';
                    }
                    updateChatNotificationCount(); // Update count after deletion
                    console.log(`Chat with ${contactIdToDelete} deleted.`);
                }
            }


            // Event listeners for sidebar links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const sectionId = this.dataset.section;
                    showSection(sectionId);
                    updateActiveLink(sectionId);

                    // If navigating to chat, ensure a chat is loaded (e.g., first contact)
                    if (sectionId === 'chat') {
                        renderChatContacts(); // Ensure contacts are up-to-date
                        const firstContactButton = chatContactsContainer.querySelector('.chat-contact'); // Get the first contact dynamically
                        if (firstContactButton) {
                            firstContactButton.classList.add('active');
                            chatWindowHeaderName.textContent = firstContactButton.querySelector('.font-medium').textContent;
                            chatWindowHeaderImage.src = firstContactButton.querySelector('img').src;
                            loadChatMessages(firstContactButton.dataset.contact);
                        } else {
                            chatWindowHeaderName.textContent = 'No contacts available';
                            chatWindowHeaderImage.src = 'https://placehold.co/40x40/CCCCCC/FFFFFF?text=NA';
                            chatMessagesContainer.innerHTML = '<p class="text-[var(--color-text-dark)] text-center py-4">No contacts to display.</p>';
                        }
                    }
                    // Close sidebar on mobile after selection
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            });

            // Mobile menu toggle
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    sidebar.classList.remove('-translate-x-full');
                });
            }

            // Close sidebar button for mobile
            if (closeSidebarButton) {
                closeSidebarButton.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                });
            }

            // Close sidebar if clicking outside when open on mobile
            document.body.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && sidebar && !sidebar.contains(event.target) && mobileMenuButton && !mobileMenuButton.contains(event.target) && !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // --- JavaScript for Profile Dropdown ---
            if (profileMenuButton && profileDropdown) {
                profileMenuButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                // Close dropdown if clicked outside
                document.addEventListener('click', function(event) {
                    if (profileDropdown && !profileDropdown.contains(event.target) && profileMenuButton && !profileMenuButton.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });

                // Handle dropdown menu item clicks
                profileDropdownActions.forEach(button => {
                    button.addEventListener('click', function() {
                        const action = this.dataset.action;
                        profileDropdown.classList.add('hidden');

                        if (action === 'profile') {
                            showSection('profile');
                        } else if (action === 'settings') {
                            showSection('settings');
                        } else if (action === 'logout') {
                            alert('Logging out...');
                        }
                    });
                });
            }

            // --- JavaScript for Notification Modal ---
            if (notificationButton) {
                notificationButton.addEventListener('click', function() {
                    notificationModal.classList.remove('hidden');
                });
            }

            if (closeNotificationModalButton) {
                closeNotificationModalButton.addEventListener('click', function() {
                    notificationModal.classList.add('hidden');
                });
            }

            if (okNotificationModalButton) {
                okNotificationModalButton.addEventListener('click', function() {
                    notificationModal.classList.add('hidden');
                });
            }

            if (notificationModal) {
                notificationModal.addEventListener('click', function(event) {
                    if (event.target === notificationModal) {
                        notificationModal.classList.add('hidden');
                    }
                });
            }


            // --- JavaScript for Add New Participant Modal ---
            if (addParticipantButton) {
                addParticipantButton.addEventListener('click', function() {
                    addParticipantModal.classList.remove('hidden');
                    addParticipantForm.reset(); // Clear form for new entry
                    participantLastContactInput.value = new Date().toISOString().slice(0, 10);
                });
            }

            if (closeParticipantModalButton) {
                closeParticipantModalButton.addEventListener('click', function() {
                    addParticipantModal.classList.add('hidden');
                    addParticipantForm.reset();
                });
            }
            if (cancelAddParticipantButton) {
                cancelAddParticipantButton.addEventListener('click', function() {
                    addParticipantModal.classList.add('hidden');
                    addParticipantForm.reset();
                });
            }

            if (addParticipantModal) {
                addParticipantModal.addEventListener('click', function(event) {
                    if (event.target === addParticipantModal) {
                        addParticipantModal.classList.add('hidden');
                        addParticipantForm.reset();
                    }
                });
            }

            if (addParticipantForm) {
                addParticipantForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const newParticipant = {
                        id: 'p' + (participants.length + 1), // Simple unique ID
                        name: participantNameInput.value.trim(),
                        gender: participantGenderSelect.value,
                        age: parseInt(participantAgeInput.value),
                        disability: participantDisabilitySelect.value,
                        location: participantLocationInput.value.trim(),
                        lastContact: participantLastContactInput.value,
                        accommodationType: 'N/A' // Default for newly added
                    };

                    if (!newParticipant.name || !newParticipant.gender || !newParticipant.age || !newParticipant.disability || !newParticipant.location || !newParticipant.lastContact) {
                        alert('Please fill in all participant fields.');
                        return;
                    }

                    participants.push(newParticipant);
                    renderParticipants(); // Re-render to show new participant

                    addParticipantForm.reset();
                    addParticipantModal.classList.add('hidden');
                });
            }

            // --- JavaScript for Participant Search and Filters ---
            function filterParticipants() {
                const searchTerm = participantSearchInput.value.toLowerCase();
                const selectedAccommodationType = accommodationTypeFilter.value;
                const minAge = parseInt(ageFilter.value);
                const selectedGender = genderFilter.value;
                const selectedDisabilityType = disabilityTypeFilter.value;
                const selectedLocation = locationFilter.value.toLowerCase();

                const filtered = participants.filter(p => {
                    const matchesSearch = p.name.toLowerCase().includes(searchTerm);
                    const matchesAccommodationType = selectedAccommodationType === '' || p.accommodationType === selectedAccommodationType;
                    const matchesAge = isNaN(minAge) || p.age >= minAge;
                    const matchesGender = selectedGender === '' || p.gender === selectedGender;
                    const matchesDisabilityType = selectedDisabilityType === '' || p.disability === selectedDisabilityType;
                    const matchesLocation = selectedLocation === '' || p.location.toLowerCase().includes(selectedLocation);

                    return matchesSearch && matchesAccommodationType && matchesAge && matchesGender && matchesDisabilityType && matchesLocation;
                });
                renderParticipants(filtered);
            }

            if (applyFiltersButton) applyFiltersButton.addEventListener('click', filterParticipants);
            if (participantSearchInput) participantSearchInput.addEventListener('input', filterParticipants);
            if (accommodationTypeFilter) accommodationTypeFilter.addEventListener('change', filterParticipants);
            if (ageFilter) ageFilter.addEventListener('input', filterParticipants);
            if (genderFilter) genderFilter.addEventListener('change', filterParticipants);
            if (disabilityTypeFilter) disabilityTypeFilter.addEventListener('change', filterParticipants);
            if (locationFilter) locationFilter.addEventListener('input', filterParticipants);

            // --- JavaScript for Participant Details Modal ---
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('view-details-participant')) {
                    const participantId = event.target.dataset.participantId;
                    const participant = participants.find(p => p.id === participantId);

                    if (participant) {
                        participantDetailsContent.innerHTML = `
                            <p><span class="font-semibold">Name:</span> ${participant.name}</p>
                            <p><span class="font-semibold">Gender:</span> ${participant.gender}</p>
                            <p><span class="font-semibold">Age:</span> ${participant.age}</p>
                            <p><span class="font-semibold">Disability:</span> ${participant.disability}</p>
                            <p><span class="font-semibold">Location:</span> ${participant.location}</p>
                            <p><span class="font-semibold">Accommodation Type:</span> ${participant.accommodationType}</p>
                            <p><span class="font-semibold">Last Contact:</span> ${participant.lastContact}</p>
                        `;
                        participantDetailsModal.classList.remove('hidden');
                    } else {
                        console.error('Participant not found for ID:', participantId);
                    }
                }
            });

            if (closeParticipantDetailsModalButton) {
                closeParticipantDetailsModalButton.addEventListener('click', function() {
                    participantDetailsModal.classList.add('hidden');
                });
            }
            if (okParticipantDetailsModalButton) {
                okParticipantDetailsModalButton.addEventListener('click', function() {
                    participantDetailsModal.classList.add('hidden');
                });
            }
            if (participantDetailsModal) {
                participantDetailsModal.addEventListener('click', function(event) {
                    if (event.target === participantDetailsModal) {
                        participantDetailsModal.classList.add('hidden');
                    }
                });
            }


            // --- JavaScript for Accommodation Search ---
            if (accommodationSearchInput && accommodationsContainer) {
                accommodationSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const filteredAccommodations = accommodations.filter(acc => {
                        return acc.title.toLowerCase().includes(searchTerm) ||
                               (acc.phone && acc.phone.toLowerCase().includes(searchTerm)) || // Include phone in search
                               acc.address.province.toLowerCase().includes(searchTerm) ||
                               acc.address.city.toLowerCase().includes(searchTerm) ||
                               acc.description.toLowerCase().includes(searchTerm) ||
                               acc.accommodationTypes.some(type => type.toLowerCase().includes(searchTerm)) ||
                               acc.propertyTypes.some(type => type.toLowerCase().includes(searchTerm));
                    });
                    renderAccommodations(filteredAccommodations);
                });
            }

            // --- JavaScript for Submit New Accommodation Modal (and Edit) ---
            if (submitAccommodationButton) {
                submitAccommodationButton.addEventListener('click', function() {
                    accommodationModalTitle.textContent = 'Submit New Accommodation';
                    submitAccommodationFormButton.textContent = 'Submit New Accommodation'; // Set button text for new
                    submitAccommodationFormButton.classList.remove('hidden'); // Ensure button is visible
                    accommodationIndexInput.value = -1; // Indicate new accommodation
                    submitAccommodationForm.reset();
                    // Uncheck all checkboxes
                    accommodationTypeCheckboxes.forEach(cb => cb.checked = false);
                    propertyTypeCheckboxes.forEach(cb => cb.checked = false);
                    propertyFeaturesCheckboxes.forEach(cb => cb.checked = false);
                    dateAvailableInput.value = new Date().toISOString().slice(0, 10);
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

                    const selectedAccommodationTypes = Array.from(accommodationTypeCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    const selectedPropertyTypes = Array.from(propertyTypeCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    const selectedPropertyFeatures = Array.from(propertyFeaturesCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    let imageUrl = '';
                    if (imageUploadInput.files && imageUploadInput.files[0]) {
                        imageUrl = 'https://placehold.co/250x150/cc8e45/FFFFFF?text=Uploaded+Image';
                        console.log('Image selected:', imageUploadInput.files[0].name);
                    }

                    const newOrUpdatedAccommodation = {
                        title: accommodationTitleInput.value.trim(),
                        phone: accommodationPhoneInput.value.trim(), // Get phone number
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
                        status: 'Active'
                    };

                    if (!newOrUpdatedAccommodation.title || !newOrUpdatedAccommodation.address.province || !newOrUpdatedAccommodation.address.city ||
                        newOrUpdatedAccommodation.accommodationTypes.length === 0 || newOrUpdatedAccommodation.propertyTypes.length === 0 ||
                        !newOrUpdatedAccommodation.dateAvailable || isNaN(newOrUpdatedAccommodation.bedrooms) || isNaN(newOrUpdatedAccommodation.bathrooms) ||
                        isNaN(newOrUpdatedAccommodation.currentParticipants) || !newOrUpdatedAccommodation.genderParticipants || isNaN(newOrUpdatedAccommodation.vacancies)) {
                        alert('Please fill in all required fields and select at least one Accommodation Type and Property Type.');
                        return;
                    }

                    const editIndex = parseInt(accommodationIndexInput.value);
                    if (editIndex !== -1 && !isNaN(editIndex) && editIndex < accommodations.length) {
                        // Update existing accommodation
                        newOrUpdatedAccommodation.id = accommodations[editIndex].id; // Preserve original ID
                        accommodations[editIndex] = newOrUpdatedAccommodation;
                        console.log('Accommodation updated:', newOrUpdatedAccommodation);
                    } else {
                        // Add new accommodation
                        newOrUpdatedAccommodation.id = 'a' + (accommodations.length + 1); // Simple new ID
                        accommodations.push(newOrUpdatedAccommodation);
                        console.log('New accommodation added:', newOrUpdatedAccommodation);
                    }

                    renderAccommodations(); // Re-render to show changes
                    submitAccommodationForm.reset();
                    submitAccommodationModal.classList.add('hidden');
                });
            }

            // --- Event listeners for Accommodation Edit/Delete Buttons ---
            document.addEventListener('click', function(event) {
                // Edit button click
                if (event.target.classList.contains('edit-accommodation-btn')) {
                    const index = parseInt(event.target.dataset.index);
                    const accommodationToEdit = accommodations[index];

                    if (accommodationToEdit) {
                        accommodationModalTitle.textContent = 'Edit Accommodation';
                        submitAccommodationFormButton.textContent = 'Save Acommodation'; // Set button text for editing
                        submitAccommodationFormButton.classList.remove('hidden'); // Ensure button is visible
                        accommodationIndexInput.value = index; // Store index for saving
                        accommodationTitleInput.value = accommodationToEdit.title;
                        accommodationPhoneInput.value = accommodationToEdit.phone || ''; // Populate phone number
                        accommodationAddressProvinceInput.value = accommodationToEdit.address.province;
                        accommodationAddressCityInput.value = accommodationToEdit.address.city;
                        accommodationDescriptionTextarea.value = accommodationToEdit.description;
                        dateAvailableInput.value = accommodationToEdit.dateAvailable;
                        numBedroomsInput.value = accommodationToEdit.bedrooms;
                        numBathroomsInput.value = accommodationToEdit.bathrooms;
                        numCurrentParticipantsInput.value = accommodationToEdit.currentParticipants;
                        genderParticipantsSelect.value = accommodationToEdit.genderParticipants;
                        numVacanciesInput.value = accommodationToEdit.vacancies;

                        // Reset and set checkboxes
                        accommodationTypeCheckboxes.forEach(cb => cb.checked = accommodationToEdit.accommodationTypes.includes(cb.value));
                        propertyTypeCheckboxes.forEach(cb => cb.checked = accommodationToEdit.propertyTypes.includes(cb.value));
                        propertyFeaturesCheckboxes.forEach(cb => cb.checked = accommodationToEdit.features.includes(cb.value));

                        submitAccommodationModal.classList.remove('hidden');
                    }
                }

                // Delete button click
                if (event.target.classList.contains('delete-accommodation-btn')) {
                    const index = parseInt(event.target.dataset.index);
                    if (confirm('Are you sure you want to delete this accommodation?')) {
                        accommodations.splice(index, 1); // Remove from array
                        renderAccommodations(); // Re-render the list
                        console.log('Accommodation deleted.');
                    }
                }
            });


            // --- Chat Contact Selection Logic ---
            // Initial setup for existing chat contacts
            if (chatContacts.length > 0) {
                chatContacts.forEach(contactButton => {
                    contactButton.addEventListener('click', handleChatContactClick);
                });
            }

            // --- Request Action Buttons Logic (for the new incoming request box) ---
            // Re-query buttons to ensure new ones are included if added dynamically
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('request-action-button')) {
                    const button = event.target;
                    const requestId = button.dataset.requestId;
                    const action = button.classList.contains('accept') ? 'accept' : 'revoke';
                    const requestBox = button.closest('.text-sm');

                    if (action === 'accept') {
                        console.log(`Accepted request: ${requestId}`);

                        const newContactId = 'john-doe';
                        if (!chatData[newContactId]) {
                            chatData[newContactId] = {
                                name: 'John Doe',
                                image: 'https://placehold.co/40x40/007BFF/FFFFFF?text=JD',
                                messages: [
                                    { sender: 'You', message: 'Welcome to our network, John!', time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }), type: 'outgoing', read: true },
                                    { sender: 'John Doe', message: 'Thanks for accepting my request!', time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }), type: 'incoming', read: false }, // New incoming message, unread
                                ]
                            };

                            renderChatContacts(); // Re-render contacts to include John Doe
                            updateChatNotificationCount(); // Update count after new message
                        } else {
                            console.log('John Doe is already in your contacts.');
                        }
                        // Remove the request box after acceptance
                        if (requestBox) requestBox.remove();

                    } else if (action === 'revoke') {
                        console.log(`Revoked request: ${requestId}`);
                        // Simply remove the request box
                        if (requestBox) requestBox.remove();
                    }
                }
            });

            // Initial call to set the correct notification count on page load
            updateChatNotificationCount();
        });
    </script>
</body>
</html>
