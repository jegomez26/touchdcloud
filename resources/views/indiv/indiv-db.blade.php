<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touch D Cloud - Participant Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    <header class="bg-white shadow-md p-4 flex items-center justify-between z-20 sticky top-0">
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-gray-800">Participant Dashboard</h1>
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-700 hover:text-indigo-900 transition duration-300">
                <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-10 inline-block align-middle mr-3">
                {{ config('app.name', 'TouchdCloud') }}
            </a>
            <div class="flex items-center space-x-4 relative">
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search anything..." class="pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <button class="text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <img src="https://placehold.co/32x32/a78bfa/ffffff?text=JS" alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-indigo-300">
                        {{-- Display the logged-in user's name --}}
                        <span class="font-medium text-gray-700 hidden sm:inline">
                            {{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </button>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 w-full text-left transition-colors duration-150 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-gray-100">
                        <button data-action="logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left transition-colors duration-150 rounded-md" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

    <div class="flex flex-1">
        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white text-gray-800 w-64 p-6 space-y-6 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-20 md:w-60 border-r border-gray-200">
            <div class="flex items-center justify-between md:justify-center mb-8">
                <button id="close-sidebar-button" class="text-gray-400 md:hidden focus:outline-none p-2 rounded-md hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2 px-4">Menu</p>
                <button data-section="dashboard" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard mr-3"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </button>
                <button data-section="support-coordinator" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg> Support Coordinator
                </button>
                <button data-section="messages" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </button>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-full mx-auto">
                {{-- This is where the dynamic content will be injected --}}
                @yield('main-content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeSidebarButton = document.getElementById('close-sidebar-button');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            // Removed dashboardSections as content is now yielded

            // Header elements
            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileDropdownActions = profileDropdown ? profileDropdown.querySelectorAll('button[data-action]') : [];

            // Mobile menu toggle
            mobileMenuButton.addEventListener('click', function() {
                sidebar.classList.remove('-translate-x-full');
            });

            closeSidebarButton.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
            });

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
                    event.preventDefault(); // Prevent default button behavior
                    document.getElementById('logout-form').submit(); // Submit the hidden form
                });
            });

            profileDropdownActions.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    profileDropdown.classList.add('hidden'); // Hide dropdown after action

                    if (action === 'profile') {
                        window.location.href = '{{ route('indiv.dashboard') }}'; // Navigate to the Participant dashboard
                    } else if (action === 'settings') {
                        console.log('Navigating to settings...'); // Implement settings page navigation
                    } else if (action === 'logout') {
                        document.getElementById('logout-form').submit();
                    }
                });
            });


            // Event listeners for sidebar links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const sectionId = this.dataset.section;
                    // In a real Laravel app, this would trigger a route change
                    // For a pure JS template, you'd handle content loading here
                    console.log(`Navigating to section: ${sectionId}`);

                    // Update active link styling
                    sidebarLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    // In a production Laravel app, you'd typically navigate
                    // to a new route that renders the specific content for the section.
                    // For a frontend-only example, we'll log it.
                    // For instance, if you had routes like:
                    // window.location.href = `/dashboard/${sectionId}`;
                });
            });

            // Handle dropdown actions (Profile, Settings, Logout)
            profileDropdownActions.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    // In a real Laravel app, this would trigger a route change
                    console.log(`Performing action: ${action}`);
                    profileDropdown.classList.add('hidden'); // Hide dropdown after action
                    // Example: if (action === 'logout') { window.location.href = '/logout'; }
                });
            });

            // Initial active link for demonstration (can be set dynamically by Laravel)
            const initialActiveSection = 'dashboard'; // Or retrieve from URL/session
            const initialLink = document.querySelector(`.sidebar-link[data-section="${initialActiveSection}"]`);
            if (initialLink) {
                initialLink.classList.add('active');
            }
        });
    </script>
</body>
</html>