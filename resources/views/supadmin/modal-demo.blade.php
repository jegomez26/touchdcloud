@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-window-maximize text-[#cc8e45] mr-3"></i>
                Modal System Demo
            </h1>
            <p class="mt-2 text-[#bcbabb]">Test and demonstrate all available modal types and functionality.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Modal Demo Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Success Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success Modal</h3>
            <p class="text-gray-600 mb-4">Show success messages with green styling</p>
            <button onclick="showSuccess('Operation completed successfully! Your changes have been saved.')" 
                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                Show Success
            </button>
        </div>
    </div>

    <!-- Error Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Error Modal</h3>
            <p class="text-gray-600 mb-4">Display error messages with red styling</p>
            <button onclick="showError('Something went wrong! Please check your input and try again.')" 
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                Show Error
            </button>
        </div>
    </div>

    <!-- Warning Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Warning Modal</h3>
            <p class="text-gray-600 mb-4">Show warning messages with yellow styling</p>
            <button onclick="showWarning('Please review this information carefully before proceeding.')" 
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-200">
                Show Warning
            </button>
        </div>
    </div>

    <!-- Info Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Info Modal</h3>
            <p class="text-gray-600 mb-4">Display informational messages with blue styling</p>
            <button onclick="showInfo('Here is some important information you should know about this feature.')" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                Show Info
            </button>
        </div>
    </div>

    <!-- Unauthorized Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-amber-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Unauthorized Modal</h3>
            <p class="text-gray-600 mb-4">Show access denied messages with amber styling</p>
            <button onclick="showUnauthorized('You don\'t have permission to access this feature. Required privilege: Manage Providers')" 
                    class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition duration-200">
                Show Unauthorized
            </button>
            <button onclick="window.location.href='{{ route('superadmin.test-privilege') }}'" 
                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 mt-2 block w-full">
                Test Privilege Access
            </button>
        </div>
    </div>

    <!-- Confirmation Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question-circle text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmation Modal</h3>
            <p class="text-gray-600 mb-4">Ask for user confirmation before proceeding</p>
            <button onclick="showConfirm('Are you sure you want to proceed with this action?', () => showSuccess('Action confirmed and executed!'))" 
                    class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition duration-200">
                Show Confirm
            </button>
        </div>
    </div>

    <!-- Delete Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Modal</h3>
            <p class="text-gray-600 mb-4">Confirm destructive actions with delete styling</p>
            <button onclick="showDelete('Are you sure you want to delete this item? This action cannot be undone.', () => showSuccess('Item deleted successfully!'))" 
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                Show Delete
            </button>
        </div>
    </div>

    <!-- Loading Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-spinner text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Loading Modal</h3>
            <p class="text-gray-600 mb-4">Show loading state during operations</p>
            <button onclick="showLoadingDemo()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                Show Loading
            </button>
        </div>
    </div>

    <!-- Form Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-edit text-indigo-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Form Modal</h3>
            <p class="text-gray-600 mb-4">Display forms in modal dialogs</p>
            <button onclick="showFormDemo()" 
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition duration-200">
                Show Form
            </button>
        </div>
    </div>

    <!-- Auto-Hide Modal Demo -->
    <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-teal-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Auto-Hide Modal</h3>
            <p class="text-gray-600 mb-4">Modal that automatically closes after 3 seconds</p>
            <button onclick="showSuccess('This modal will close automatically in 3 seconds!', {autoHide: 3000})" 
                    class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 transition duration-200">
                Show Auto-Hide
            </button>
        </div>
    </div>
</div>

<!-- Advanced Examples -->
<div class="bg-white rounded-lg shadow-xl p-6 mb-8">
    <h2 class="text-2xl font-bold text-[#33595a] mb-4">Advanced Examples</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Chained Modals -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chained Modals</h3>
            <p class="text-gray-600 mb-4">Demonstrate modal chaining - one modal triggers another</p>
            <button onclick="showChainedModals()" 
                    class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 transition duration-200">
                Show Chained Modals
            </button>
        </div>

        <!-- Custom Styling -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Custom Styling</h3>
            <p class="text-gray-600 mb-4">Modals with custom titles and messages</p>
            <button onclick="showCustomModal()" 
                    class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-blue-600 transition duration-200">
                Show Custom Modal
            </button>
        </div>
    </div>
</div>

<!-- Usage Examples -->
<div class="bg-white rounded-lg shadow-xl p-6">
    <h2 class="text-2xl font-bold text-[#33595a] mb-4">Usage Examples</h2>
    
    <div class="space-y-4">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Basic Usage</h3>
            <pre class="text-sm text-gray-700 bg-white p-3 rounded border overflow-x-auto"><code>// Show success message
showSuccess('Operation completed successfully!');

// Show error message
showError('Something went wrong!');

// Show confirmation dialog
showConfirm('Are you sure?', () => {
    // Action to perform on confirmation
    console.log('Confirmed!');
});

// Show delete confirmation
showDelete('Delete this item?', () => {
    // Delete action
    console.log('Deleted!');
});</code></pre>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Advanced Usage</h3>
            <pre class="text-sm text-gray-700 bg-white p-3 rounded border overflow-x-auto"><code>// Modal with custom options
modalManager.show('success-modal', {
    title: 'Custom Title',
    message: 'Custom message',
    autoHide: 5000, // Auto-hide after 5 seconds
    onConfirm: () => {
        // Custom action
    }
});

// Form modal
showForm('Edit User', 'Update user information', formHTML, (formData) => {
    // Handle form submission
});</code></pre>
        </div>
    </div>
</div>

<script>
// Demo functions
function showLoadingDemo() {
    showLoading('Processing your request...');
    
    // Simulate async operation
    setTimeout(() => {
        window.modalManager.hide('loading-modal');
        showSuccess('Loading completed successfully!');
    }, 3000);
}

function showFormDemo() {
    const formHTML = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter email">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Enter message"></textarea>
            </div>
        </div>
    `;
    
    showForm('Contact Form', 'Please fill out the form below', formHTML, () => {
        showSuccess('Form submitted successfully!');
    });
}

function showChainedModals() {
    showConfirm('Do you want to proceed with the first action?', () => {
        showLoading('Processing first action...');
        
        setTimeout(() => {
            window.modalManager.hide('loading-modal');
            showConfirm('First action completed! Do you want to proceed with the second action?', () => {
                showSuccess('All actions completed successfully!');
            });
        }, 2000);
    });
}

function showCustomModal() {
    window.modalManager.show('success-modal', {
        title: 'Custom Success',
        message: 'This is a custom modal with a personalized message and styling!',
        autoHide: 4000
    });
}
</script>
@endsection
