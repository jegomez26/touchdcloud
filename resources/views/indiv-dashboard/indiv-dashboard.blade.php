<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Dashboard</title>
    <!-- Tailwind CSS CDN - for quick prototyping. In a Laravel project, you'd use your compiled app.css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Custom styles for active sidebar link */
        .sidebar-link.active {
            background-color: #4f46e5; /* indigo-600 */
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-lg */
        }
        /* Custom styles for active chat contact */
        .chat-contact.active {
            background-color: #e0f2fe; /* blue-50 */
            border-left: 4px solid #3b82f6; /* blue-500 */
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 flex flex-col">

    <!-- Main Header (Desktop and Mobile) -->
    <header class="bg-white shadow-sm p-4 flex items-center justify-between z-10">
        <!-- Mobile Header Content (Hamburger Menu, Title) -->
        <div class="flex items-center md:hidden w-full justify-between">
            <h1 class="text-xl font-bold text-gray-800">Individual Dashboard</h1>
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none">
                <!-- Menu icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Desktop Header Content (Dashboard Title, Icons) -->
        <div class="hidden md:flex items-center justify-between w-full">
            <h1 class="text-2xl font-extrabold text-indigo-600">Individual Dashboard</h1>
            <div class="flex items-center space-x-4 relative">
                <!-- Notification Icon -->
                <button class="text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </button>

                <!-- Profile Icon with Dropdown -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <img src="https://placehold.co/32x32/cbd5e1/475569?text=JD" alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-gray-300">
                        <span class="font-medium text-gray-700 hidden sm:inline">John Doe</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 hidden z-30">
                        <button data-action="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user inline-block mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile
                        </button>
                        <button data-action="settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings inline-block mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.22a2 2 0 0 0 .73 2.73l.09.09a2 2 0 0 1 .73 2.73l-.78 1.22a2 2 0 0 0 .73 2.73l.15.08a2 2 0 0 0 2.73-.73l.43-.25a2 2 0 0 1 1-1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.22a2 2 0 0 0-.73-2.73l-.09-.09a2 2 0 0 1-.73-2.73l.78-1.22a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 0-2.73.73l-.43.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg> Settings
                        </button>
                        <hr class="my-1 border-gray-100">
                        <button data-action="logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out inline-block mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="11" y1="12" y2="12"/></svg> Log out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1">
        <!-- Sidebar for navigation -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-gray-800 text-white w-64 p-6 space-y-6 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-20">
            <div class="flex items-center justify-between md:justify-center mb-8">
                <h1 class="text-2xl font-extrabold text-indigo-400">Dashboard</h1>
                <button id="close-sidebar-button" class="text-gray-400 md:hidden focus:outline-none">
                    <!-- X icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-2">
                <!-- Removed Profile link from sidebar -->
                <button data-section="sc" class="sidebar-link flex items-center w-full px-4 py-3 rounded-lg text-left text-lg font-medium transition-colors duration-200 text-gray-300 hover:bg-gray-700 hover:text-white active">
                    <!-- Briefcase icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase mr-3"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg> Support Coordinator
                </button>
                <button data-section="messages" class="sidebar-link flex items-center w-full px-4 py-3 rounded-lg text-left text-lg font-medium transition-colors duration-200 text-gray-300 hover:bg-gray-700 hover:text-white">
                    <!-- MessageSquare icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucude-message-square mr-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Messages
                </button>
                <!-- Removed Settings link from sidebar -->
            </nav>
        </aside>

        <!-- Main content area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-4xl mx-auto">
                <!-- Profile Section -->
                <div id="profile-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Profile</h2>
                    <p class="text-gray-600 mb-2">Manage your personal information and preferences.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="John Doe" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="john.doe@example.com" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md col-span-full">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Tell us about yourself..."></textarea>
                        </div>
                    </div>
                    <button class="mt-6 px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Save Profile
                    </button>
                </div>

                <!-- Support Coordinator Section -->
                <div id="sc-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Support Coordinator Information</h2>
                    <p class="text-gray-600 mb-2">View and manage details related to your support coordinator.</p>
                    <div class="bg-blue-50 p-4 rounded-md border border-blue-200">
                        <p class="text-blue-800 font-semibold mb-2">Current Support Coordinator:</p>
                        <p class="text-blue-700">Name: Jane Smith</p>
                        <p class="text-blue-700">Email: jane.smith@example.com</p>
                        <p class="text-blue-700">Phone: +1 (555) 123-4567</p>
                        <p class="text-blue-700">Last Contact: 2024-07-15</p>
                    </div>
                    <button class="mt-6 px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Contact SC
                    </button>
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
                                <p class="text-sm text-gray-500">Online</p>
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

                <!-- Settings Section -->
                <div id="settings-section" class="dashboard-section p-6 bg-white rounded-lg shadow-md hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Account Settings</h2>
                    <p class="text-gray-600 mb-2">Adjust your account preferences and security settings.</p>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label for="notifications" class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Email Notifications</span>
                                <input type="checkbox" id="notifications" class="form-checkbox h-5 w-5 text-indigo-600 rounded" checked />
                            </label>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
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
            const chatContactsList = document.getElementById('chat-contacts-list'); // Get the parent for event delegation

            // Header elements
            const profileMenuButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileDropdownActions = profileDropdown ? profileDropdown.querySelectorAll('button[data-action]') : [];

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

            // Initial display: Show Support Coordinator section and set active link
            showSection('sc'); // Default to 'sc' since 'profile' is removed from sidebar
            updateActiveLink('sc');

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

            // --- JavaScript for Chatbox functionality ---
            const chatHeaderName = document.getElementById('chat-header-name');
            const chatMessagesContainer = document.getElementById('chat-messages-container');
            const chatInput = document.getElementById('chat-input');
            const sendMessageButton = document.getElementById('send-message-button');

            // Dummy chat data (in a real app, this would come from a backend)
            const chatData = {
                'jane-smith': [
                    { sender: 'Jane Smith', message: 'Hi there! How can I assist you today?', time: '10:00 AM', type: 'incoming' },
                    { sender: 'You', message: 'Hello Jane! I have a question about my profile settings.', time: '10:05 AM', type: 'outgoing' },
                    { sender: 'Jane Smith', message: 'Certainly, what would you like to know?', time: '10:06 AM', type: 'incoming' },
                ],
                'mark-jones': [
                    { sender: 'Mark Jones', message: 'Good morning! Just checking in.', time: 'Yesterday, 09:30 AM', type: 'incoming' },
                    { sender: 'You', message: 'Morning Mark! All good here, thanks.', time: 'Yesterday, 09:35 AM', type: 'outgoing' },
                    { sender: 'Mark Jones', message: 'Great to hear! Let me know if anything comes up.', time: 'Yesterday, 09:36 AM', type: 'incoming' },
                ],
                'sarah-davis': [
                    { sender: 'Sarah Davis', message: 'Your appointment is confirmed for next week.', time: '2 days ago', type: 'incoming' },
                    { sender: 'You', message: 'Thanks Sarah, appreciate the reminder!', time: '2 days ago', type: 'outgoing' },
                ],
                'john-doe': [ // Initial dummy messages for John Doe
                    { sender: 'John Doe', message: 'Hello! I sent you a connection request.', time: 'Just now', type: 'incoming' },
                    { sender: 'You', message: 'Hi John! Thanks for connecting.', time: 'Just now', type: 'outgoing' }
                ]
            };

            // Function to render messages for a given contact
            function renderChatMessages(contactId) {
                chatMessagesContainer.innerHTML = ''; // Clear existing messages
                const messages = chatData[contactId] || [];

                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `flex ${msg.type === 'outgoing' ? 'justify-end' : 'justify-start'}`;

                    const messageBubble = document.createElement('div');
                    messageBubble.className = `p-3 rounded-lg max-w-[70%] ${msg.type === 'outgoing' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-800'}`;
                    messageBubble.innerHTML = `
                        ${msg.message}
                        <div class="text-xs ${msg.type === 'outgoing' ? 'text-indigo-200' : 'text-gray-500'} mt-1 text-right">${msg.sender} - ${msg.time}</div>
                    `;
                    messageDiv.appendChild(messageBubble);
                    chatMessagesContainer.appendChild(messageDiv);
                });
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight; // Scroll to bottom
            }

            // Function to handle chat contact clicks (using event delegation)
            function handleChatContactClick(clickedButton) {
                // Remove active class from all contacts
                document.querySelectorAll('.chat-contact').forEach(btn => btn.classList.remove('active'));
                // Add active class to the clicked contact
                clickedButton.classList.add('active');

                const contactId = clickedButton.dataset.contact;
                const contactName = clickedButton.querySelector('.font-medium').textContent;

                chatHeaderName.textContent = contactName; // Update chat header
                renderChatMessages(contactId); // Load and display messages
            }

            // Event listener for chat contacts (using event delegation on parent)
            chatContactsList.addEventListener('click', function(event) {
                const clickedButton = event.target.closest('.chat-contact');
                if (clickedButton) {
                    handleChatContactClick(clickedButton);
                }
            });

            // Initial chat load (for the first active contact)
            const initialActiveContact = document.querySelector('.chat-contact.active');
            if (initialActiveContact) {
                renderChatMessages(initialActiveContact.dataset.contact);
            }

            // Send message functionality (dummy)
            sendMessageButton.addEventListener('click', function() {
                const messageText = chatInput.value.trim();
                if (messageText) {
                    const activeContact = document.querySelector('.chat-contact.active');
                    if (!activeContact) {
                        alert('Please select a contact to send a message.'); // Use a modal in a real app
                        return;
                    }
                    const activeContactId = activeContact.dataset.contact;
                    const newMessage = {
                        sender: 'You',
                        message: messageText,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        type: 'outgoing'
                    };
                    // Add to dummy data (in a real app, send to backend)
                    chatData[activeContactId].push(newMessage);
                    renderChatMessages(activeContactId); // Re-render messages
                    chatInput.value = ''; // Clear input
                }
            });

            // Allow sending message with Enter key
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessageButton.click();
                }
            });

            // --- JavaScript for Accept/Revoke buttons ---
            const incomingRequestMessage = document.getElementById('incoming-request-message');
            if (incomingRequestMessage) {
                const acceptButton = incomingRequestMessage.querySelector('.accept-button');
                const revokeButton = incomingRequestMessage.querySelector('.revoke-button');
                const johnDoeContactId = incomingRequestMessage.dataset.contactId; // 'john-doe'
                const johnDoeContactName = incomingRequestMessage.dataset.contactName; // 'John Doe'

                if (acceptButton) {
                    acceptButton.addEventListener('click', function() {
                        // 1. Hide the incoming request message
                        incomingRequestMessage.classList.add('hidden');

                        // 2. Create and add John Doe to the chat contacts list
                        const newContactButton = document.createElement('button');
                        newContactButton.className = 'chat-contact flex items-center w-full p-3 rounded-md hover:bg-gray-100 transition-colors duration-150';
                        newContactButton.dataset.contact = johnDoeContactId;
                        newContactButton.innerHTML = `
                            <img src="https://placehold.co/40x40/cbd5e1/475569?text=JD" alt="${johnDoeContactName}" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <p class="font-medium text-gray-800">${johnDoeContactName}</p>
                                <p class="text-sm text-gray-500">New connection!</p>
                            </div>
                        `;
                        chatContactsList.appendChild(newContactButton);

                        // 3. Add John Doe's initial chat data (if not already present)
                        if (!chatData[johnDoeContactId]) {
                             chatData[johnDoeContactId] = [
                                { sender: johnDoeContactName, message: 'Thanks for accepting my request!', time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), type: 'incoming' },
                                { sender: 'You', message: 'You\'re welcome, John! How can I help?', time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), type: 'outgoing' }
                            ];
                        }


                        // 4. Simulate a click on the new contact to open their chat
                        handleChatContactClick(newContactButton);

                        // Optional: Remove the request message from the DOM entirely if preferred
                        // incomingRequestMessage.remove();
                    });
                }

                if (revokeButton) {
                    revokeButton.addEventListener('click', function() {
                        // Simply hide the incoming request message
                        incomingRequestMessage.classList.add('hidden');
                        // Optional: Remove from DOM
                        // incomingRequestMessage.remove();
                    });
                }
            }

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
                            // No sidebar link for profile, so no updateActiveLink for sidebar
                        } else if (action === 'settings') {
                            showSection('settings');
                            // No sidebar link for settings, so no updateActiveLink for sidebar
                        } else if (action === 'logout') {
                            // In a real Laravel app, this would be a form submission or redirect
                            alert('Logging out...'); // Replace with actual logout logic
                            // window.location.href = '/logout';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
