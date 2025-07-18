<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Dashboard</title>
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
            <h1 class="text-xl font-bold text-gray-800">Individual Dashboard</h1>
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Desktop Header Content (Dashboard Title, Icons) -->
        <div class="hidden md:flex items-center justify-between w-full">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-700 hover:text-indigo-900 transition duration-300">
                    <img src="{{ asset('images/blue_logo.png') }}" alt="{{ config('app.name', 'TouchdCloud') }} Logo" class="h-10 inline-block align-middle mr-3">
                    {{ config('app.name', 'TouchdCloud') }}
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
                <button data-section="messages" class="sidebar-link flex items-center w-full px-4 py-2 rounded-md text-left text-base font-medium transition-colors duration-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </button>
            </nav>
        </aside>

        <!-- Main content area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-full mx-auto">
                <!-- Dashboard Section (Placeholder for individual dashboard content) -->
                <div id="dashboard-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg mb-8 hidden">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome, John Smith!</h2>
                    <p class="text-gray-600">This is your personalized dashboard overview.</p>
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 text-blue-800">
                        <p class="font-semibold">Quick Updates:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Your next meeting with Sarah Coordinator is on 2024-07-25.</li>
                            <li>New accommodation options available in your area.</li>
                            <li>Review your latest support plan updates.</li>
                        </ul>
                    </div>
                </div>

                <!-- Support Coordinator Section (Placeholder) -->
                <div id="support-coordinator-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Support Coordinator</h2>
                    <p class="text-gray-600 mb-4">Details about your dedicated support coordinator and how to reach them.</p>

                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 flex flex-col md:flex-row items-center gap-6">
                        <img src="https://placehold.co/100x100/a78bfa/ffffff?text=SC" alt="Support Coordinator Avatar" class="w-24 h-24 rounded-full object-cover border-2 border-indigo-300 shadow-md">
                        <div class="flex-1 text-center md:text-left">
                            <p class="text-xl font-semibold text-gray-800">Sarah Coordinator</p>
                            <p class="text-gray-600">Email: sarah.c@example.com</p>
                            <p class="text-gray-600">Phone: (123) 456-7890</p>
                            <p class="text-gray-600 mt-2">Specialization: Accommodation & Employment Support</p>
                            <button class="mt-4 px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                Message Sarah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Section (Chat Interface) -->
                <div id="messages-section" class="dashboard-section p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Messages</h2>
                    <p class="text-gray-600 mb-4">Communicate securely with your support team and coordinators.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Pane: Chat Contacts List -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Support Coordinators</h3>
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

                            <!-- Incoming Request Box -->
                            <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200 text-purple-800 text-sm">
                                <p class="font-semibold mb-2">Incoming Request from John Doe (2024-07-18)</p>
                                <p>"John Doe would like to connect with you."</p>
                                <div class="flex space-x-2 mt-3">
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-md text-xs hover:bg-green-700 transition">Accept</button>
                                    <button class="px-4 py-2 bg-red-600 text-white rounded-md text-xs hover:bg-red-700 transition">Revoke</button>
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
                                        <p>Hi there! How can I assist you today?</p>
                                        <p class="chat-message-time text-right">10:00 AM</p>
                                    </div>
                                </div>
                                <!-- Outgoing Message -->
                                <div class="flex justify-end">
                                    <div class="chat-bubble outgoing">
                                        <p class="chat-message-sender text-right">You</p>
                                        <p>Hello Jane! I have a question about my profile settings.</p>
                                        <p class="chat-message-time text-right">10:05 AM</p>
                                    </div>
                                </div>
                                <!-- Incoming Message -->
                                <div class="flex justify-start">
                                    <div class="chat-bubble incoming">
                                        <p class="chat-message-sender">Jane Smith</p>
                                        <p>Certainly, what would you like to know?</p>
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
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Profile</h2>
                    <p class="text-gray-600 mb-2">Manage your personal information.</p>
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
                            <label for="sc-bio" class="block text-sm font-medium text-gray-700">Bio / Preferences</label>
                            <textarea id="sc-bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Describe your preferences or any relevant notes..."></textarea>
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

            // Chat elements
            const chatContacts = document.querySelectorAll('.chat-contact');
            const chatWindowHeaderName = document.querySelector('#messages-section .border-b p');
            const chatWindowHeaderImage = document.querySelector('#messages-section .border-b img');
            const chatMessagesContainer = document.querySelector('#messages-section .overflow-y-auto');

            // Sample chat data (you would load this from a backend in a real application)
            const chatData = {
                'jane-smith': [
                    { sender: 'Jane Smith', message: 'Hi there! How can I assist you today?', time: '10:00 AM', type: 'incoming' },
                    { sender: 'You', message: 'Hello Jane! I have a question about my profile settings.', time: '10:05 AM', type: 'outgoing' },
                    { sender: 'Jane Smith', message: 'Certainly, what would you like to know?', time: '10:06 AM', type: 'incoming' },
                ],
                'mark-jones': [
                    { sender: 'Mark Jones', message: 'Good morning! Just checking in.', time: 'Yesterday', type: 'incoming' },
                    { sender: 'You', message: 'Good morning Mark! All good here, thanks.', time: 'Yesterday', type: 'outgoing' },
                ],
                'sarah-davis': [
                    { sender: 'Sarah Davis', message: 'Reminder: your appointment is tomorrow at 2 PM.', time: '2 days ago', type: 'incoming' },
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

            // Initial display: Show Messages section and set active link
            showSection('messages'); // Default to messages as per screenshot
            updateActiveLink('messages');
            loadChatMessages('jane-smith'); // Load Jane Smith's chat by default

            // Event listeners for sidebar links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const sectionId = this.dataset.section;
                    showSection(sectionId);
                    updateActiveLink(sectionId);
                    // If navigating to messages, ensure a chat is loaded (e.g., first contact)
                    if (sectionId === 'messages') {
                        const firstContactButton = document.querySelector('.chat-contact');
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

            // --- Chat Contact Selection Logic ---
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

                    chatWindowHeaderName.textContent = contactName;
                    chatWindowHeaderImage.src = contactImageSrc;

                    // Load messages for the selected contact
                    loadChatMessages(contactId);
                });
            });
        });
    </script>
</body>
</html>
