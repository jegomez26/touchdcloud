{{-- Unauthorized Access Modal --}}
<div id="unauthorized-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-lg w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="unauthorized-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center text-amber-500 mb-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Access Denied</h3>
            <p class="text-gray-700 text-lg mb-4" id="unauthorized-message">You don't have permission to access this feature.</p>
            <p class="text-gray-600 text-sm mb-6">Please contact your administrator if you believe this is an error.</p>
            <div class="flex gap-3 justify-center">
                <button class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-modal" data-modal="unauthorized-modal">
                    Close
                </button>
                <button class="bg-amber-500 text-white px-6 py-3 rounded-lg hover:bg-amber-600 transition duration-200 font-medium" onclick="window.location.href='{{ route('superadmin.dashboard') }}'">
                    Go to Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Reusable Modal Components for Super Admin Dashboard --}}

{{-- Success Modal --}}
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="success-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center text-green-500 mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Success!</h3>
            <p class="text-gray-700 text-lg mb-6" id="success-message">Operation completed successfully.</p>
            <button class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-200 font-medium close-modal" data-modal="success-modal">
                Continue
            </button>
        </div>
    </div>
</div>

{{-- Error Modal --}}
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="error-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center text-red-500 mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Error!</h3>
            <p class="text-gray-700 text-lg mb-6" id="error-message">Something went wrong. Please try again.</p>
            <button class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition duration-200 font-medium close-modal" data-modal="error-modal">
                Try Again
            </button>
        </div>
    </div>
</div>

{{-- Warning Modal --}}
<div id="warning-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="warning-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center text-yellow-500 mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Warning!</h3>
            <p class="text-gray-700 text-lg mb-6" id="warning-message">Please review the following information.</p>
            <button class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-200 font-medium close-modal" data-modal="warning-modal">
                Understood
            </button>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <div class="text-center">
            <div class="flex items-center justify-center text-blue-500 mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Confirm Action</h3>
            <p class="text-gray-700 text-lg mb-6" id="confirmation-message">Are you sure you want to proceed with this action?</p>
            <div class="flex space-x-4 justify-center">
                <button class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-modal" data-modal="confirmation-modal">
                    Cancel
                </button>
                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium" id="confirmation-confirm">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Loading Modal --}}
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <div class="text-center">
            <div class="flex items-center justify-center text-blue-500 mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Processing...</h3>
            <p class="text-gray-700 text-lg" id="loading-message">Please wait while we process your request.</p>
        </div>
    </div>
</div>

{{-- Info Modal --}}
<div id="info-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="info-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center text-blue-500 mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Information</h3>
            <p class="text-gray-700 text-lg mb-6" id="info-message">Here's some important information for you.</p>
            <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium close-modal" data-modal="info-modal">
                Got it
            </button>
        </div>
    </div>
</div>

{{-- Form Modal (for complex forms) --}}
<div id="form-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-2xl w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none close-modal" data-modal="form-modal">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2" id="form-modal-title">Form Title</h3>
            <p class="text-gray-600" id="form-modal-description">Form description goes here.</p>
        </div>
        <div id="form-modal-content">
            <!-- Form content will be dynamically inserted here -->
        </div>
        <div class="flex space-x-4 justify-end mt-6">
            <button class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-modal" data-modal="form-modal">
                Cancel
            </button>
            <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium" id="form-modal-submit">
                Submit
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full relative transform -translate-y-4 scale-95 transition-all duration-300 ease-out">
        <div class="text-center">
            <div class="flex items-center justify-center text-red-500 mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Delete Confirmation</h3>
            <p class="text-gray-700 text-lg mb-6" id="delete-message">Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="flex space-x-4 justify-center">
                <button class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium close-modal" data-modal="delete-modal">
                    Cancel
                </button>
                <button class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition duration-200 font-medium" id="delete-confirm">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal Management System
class ModalManager {
    constructor() {
        this.activeModal = null;
        this.init();
    }

    init() {
        // Close modal buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('close-modal')) {
                const modalId = e.target.getAttribute('data-modal');
                this.hide(modalId);
            }
        });

        // Close on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.hide(this.activeModal);
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal) {
                this.hide(this.activeModal);
            }
        });
    }

    show(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Hide any currently active modal
        if (this.activeModal) {
            this.hide(this.activeModal);
        }

        // Set content if provided
        if (options.title) {
            const titleElement = modal.querySelector('h3');
            if (titleElement) titleElement.textContent = options.title;
        }

        if (options.message) {
            const messageElement = modal.querySelector('p[id$="-message"]');
            if (messageElement) messageElement.textContent = options.message;
        }

        if (options.onConfirm) {
            const confirmButton = modal.querySelector('#confirmation-confirm, #delete-confirm, #form-modal-submit');
            if (confirmButton) {
                confirmButton.onclick = options.onConfirm;
            }
        }

        // Show modal with animation
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.querySelector('div').classList.remove('-translate-y-4', 'scale-95');
        }, 10);

        this.activeModal = modalId;

        // Auto-hide after delay if specified
        if (options.autoHide) {
            setTimeout(() => {
                this.hide(modalId);
            }, options.autoHide);
        }
    }

    hide(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.add('-translate-y-4', 'scale-95');

        this.activeModal = null;
    }

    // Convenience methods
    success(message, options = {}) {
        this.show('success-modal', { message, ...options });
    }

    error(message, options = {}) {
        this.show('error-modal', { message, ...options });
    }

    warning(message, options = {}) {
        this.show('warning-modal', { message, ...options });
    }

    info(message, options = {}) {
        this.show('info-modal', { message, ...options });
    }

    unauthorized(message, options = {}) {
        this.show('unauthorized-modal', { message, ...options });
    }

    confirm(message, onConfirm, options = {}) {
        this.show('confirmation-modal', { 
            message, 
            onConfirm: () => {
                onConfirm();
                this.hide('confirmation-modal');
            },
            ...options 
        });
    }

    delete(message, onConfirm, options = {}) {
        this.show('delete-modal', { 
            message, 
            onConfirm: () => {
                onConfirm();
                this.hide('delete-modal');
            },
            ...options 
        });
    }

    loading(message = 'Please wait while we process your request.') {
        this.show('loading-modal', { message });
    }

    form(title, description, content, onSubmit, options = {}) {
        const modal = document.getElementById('form-modal');
        const titleElement = modal.querySelector('#form-modal-title');
        const descElement = modal.querySelector('#form-modal-description');
        const contentElement = modal.querySelector('#form-modal-content');
        const submitButton = modal.querySelector('#form-modal-submit');

        titleElement.textContent = title;
        descElement.textContent = description;
        contentElement.innerHTML = content;

        submitButton.onclick = () => {
            onSubmit();
            this.hide('form-modal');
        };

        this.show('form-modal', options);
    }
}

// Initialize modal manager
window.modalManager = new ModalManager();

// Global convenience functions
window.showSuccess = (message, options) => window.modalManager.success(message, options);
window.showError = (message, options) => window.modalManager.error(message, options);
window.showWarning = (message, options) => window.modalManager.warning(message, options);
window.showInfo = (message, options) => window.modalManager.info(message, options);
window.showUnauthorized = (message, options) => window.modalManager.unauthorized(message, options);
window.showConfirm = (message, onConfirm, options) => window.modalManager.confirm(message, onConfirm, options);
window.showDelete = (message, onConfirm, options) => window.modalManager.delete(message, onConfirm, options);
window.showLoading = (message) => window.modalManager.loading(message);
window.showForm = (title, description, content, onSubmit, options) => window.modalManager.form(title, description, content, onSubmit, options);
</script>
