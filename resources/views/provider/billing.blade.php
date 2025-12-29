{{-- resources/views/provider/billing.blade.php --}}
@extends('company.provider-db')

@section('main-content')
<div class="space-y-6" x-data="billingData">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Billing & Renewals</h1>
                <p class="text-gray-600 mt-1">Manage your subscription billing and view payment history</p>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="showPlansModal = true" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Change Plan
                </button>
            </div>
        </div>
    </div>

    <!-- Current Subscription Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Subscription</h2>
        
        <template x-if="currentSubscription.has_subscription">
            <div class="space-y-4">
                <!-- Subscription Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Plan</h3>
                        <p class="text-xl font-semibold text-gray-900" x-text="currentSubscription.display_name || currentSubscription.plan_name"></p>
                        <p class="text-sm text-gray-600" x-text="currentSubscription.billing_period ? currentSubscription.billing_period.charAt(0).toUpperCase() + currentSubscription.billing_period.slice(1) + ' billing' : ''"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                        <p class="text-xl font-semibold" 
                           :class="{
                               'text-green-600': currentSubscription.status === 'active',
                               'text-blue-600': currentSubscription.status === 'trialing',
                               'text-red-600': currentSubscription.status === 'trial_ended',
                               'text-gray-600': currentSubscription.status === 'inactive'
                           }" 
                           x-text="currentSubscription.status === 'trialing' ? 'Trial Active' : 
                                  currentSubscription.status === 'active' ? 'Active' :
                                  currentSubscription.status === 'trial_ended' ? 'Trial Ended' : 'Inactive'"></p>
                        <p class="text-sm text-gray-600" x-text="currentSubscription.next_billing_date ? 'Next billing: ' + currentSubscription.next_billing_date : ''"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Amount</h3>
                        <p class="text-xl font-semibold text-gray-900" x-text="currentSubscription.price ? '$' + currentSubscription.price : 'N/A'"></p>
                        <p class="text-sm text-gray-600" x-text="currentSubscription.billing_period ? 'per ' + currentSubscription.billing_period : ''"></p>
                    </div>
                </div>

                <!-- Trial Information -->
                <div x-show="currentSubscription.trial_active" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Trial Period</h4>
                            <p class="text-sm text-blue-700">
                                <span x-text="currentSubscription.trial_remaining_days"></span> days remaining in your trial
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="w-full bg-blue-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                 :style="'width: ' + currentSubscription.trial_progress + '%'"></div>
                        </div>
                        <p class="text-xs text-blue-600 mt-1" x-text="Math.round(currentSubscription.trial_progress) + '% complete'"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-4 border-t border-gray-200">
                    <button @click="showPlansModal = true" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Change Plan
                    </button>
                    <button @click="showCancelConfirmModal = true" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors">
                        Cancel Subscription
                    </button>
                </div>
            </div>
        </template>

        <template x-if="!currentSubscription.has_subscription">
            <div class="text-center py-8">
                <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Subscription</h3>
                <p class="text-gray-600 mb-6">You don't have an active subscription. Choose a plan to get started.</p>
                <button @click="showPlansModal = true" 
                        class="bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Choose a Plan
                </button>
            </div>
        </template>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h2>
        
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : $payment->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->formatted_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status_badge_class }}">
                                    {{ $payment->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                @if($payment->isSuccessful())
                                    <a href="{{ route('provider.invoice.download', $payment->id) }}" target="_blank" class="hover:underline">Download</a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Payment History</h3>
                <p class="text-gray-600">You haven't made any payments yet.</p>
            </div>
        @endif
        
        @if($payments->count() > 0)
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($payments->previousPageUrl())
                        <a href="{{ $payments->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    @if($payments->nextPageUrl())
                        <a href="{{ $payments->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $payments->firstItem() }}</span> to <span class="font-medium">{{ $payments->lastItem() }}</span> of <span class="font-medium">{{ $payments->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Billing Information -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-3">Payment Method</h3>
                <div class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">•••• •••• •••• 4242</p>
                        <p class="text-xs text-gray-500">Expires 12/25</p>
                    </div>
                    <button @click="showPaymentMethodModal = true" class="ml-auto text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Update
                    </button>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-3">Billing Address</h3>
                <div class="p-4 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-900">John Doe</p>
                    <p class="text-sm text-gray-900">123 Main Street</p>
                    <p class="text-sm text-gray-900">Sydney, NSW 2000</p>
                    <p class="text-sm text-gray-900">Australia</p>
                    <button @click="showBillingAddressModal = true" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Renewals -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Renewals</h2>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">Next Billing Date</h3>
                    <p class="text-sm text-gray-600" x-text="currentSubscription.next_billing_date || 'No upcoming billing'"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900" x-text="currentSubscription.price ? '$' + currentSubscription.price : 'N/A'"></p>
                    <p class="text-xs text-gray-500" x-text="currentSubscription.billing_period ? 'per ' + currentSubscription.billing_period : ''"></p>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">Auto-renewal</h3>
                    <p class="text-sm text-gray-600">
                        @if($subscriptionStatus['has_subscription'] && $subscriptionStatus['auto_renew'])
                            Your subscription will automatically renew
                        @else
                            Your subscription will not automatically renew
                        @endif
                    </p>
                </div>
                <div class="flex items-center">
                    <button @click="toggleAutoRenew()" 
                            :disabled="loading"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            :class="$subscriptionStatus.has_subscription && $subscriptionStatus.auto_renew ? 'bg-blue-600' : 'bg-gray-200'">
                        <span class="sr-only">Toggle auto-renewal</span>
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                              :class="$subscriptionStatus.has_subscription && $subscriptionStatus.auto_renew ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Update Modal -->
    <div x-show="showPaymentMethodModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showPaymentMethodModal = false">
        
        <div x-show="showPaymentMethodModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Update Payment Method</h2>
                        <p class="text-gray-600 mt-1">Update your payment information</p>
                    </div>
                    <button @click="showPaymentMethodModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="p-6">
                <form @submit.prevent="updatePaymentMethod()">
                    <div class="space-y-6">
                        <!-- Card Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Card Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" placeholder="4242 4242 4242 4242" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="4242 4242 4242 4242">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                        <input type="text" placeholder="12/25" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               value="12/25">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                        <input type="text" placeholder="123" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               value="123">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                    <input type="text" placeholder="John Doe" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" placeholder="123 Main Street" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <input type="text" placeholder="Sydney" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                        <input type="text" placeholder="2000" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="AU">Australia</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 pt-6 border-t border-gray-200 mt-6">
                        <button type="button" @click="showPaymentMethodModal = false" 
                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <span x-show="!loading" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Payment Method
                            </span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Billing Address Update Modal -->
    <div x-show="showBillingAddressModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showBillingAddressModal = false">
        
        <div x-show="showBillingAddressModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Update Billing Address</h2>
                        <p class="text-gray-600 mt-1">Update your billing address information</p>
                    </div>
                    <button @click="showBillingAddressModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Address Form -->
            <div class="p-6">
                <form @submit.prevent="updateBillingAddress()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" placeholder="John Doe" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" placeholder="123 Main Street" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" placeholder="Sydney" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input type="text" placeholder="NSW" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" placeholder="2000" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="AU">Australia</option>
                                    <option value="US">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 pt-6 border-t border-gray-200 mt-6">
                        <button type="button" @click="showBillingAddressModal = false" 
                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <span x-show="!loading" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Address
                            </span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('billingData', () => ({
        showPaymentMethodModal: false,
        showBillingAddressModal: false,
        loading: false,
        
        async updatePaymentMethod() {
            this.loading = true;
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Show success message
                alert('Payment method updated successfully!');
                this.showPaymentMethodModal = false;
            } catch (error) {
                alert('Failed to update payment method. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        async updateBillingAddress() {
            this.loading = true;
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Show success message
                alert('Billing address updated successfully!');
                this.showBillingAddressModal = false;
            } catch (error) {
                alert('Failed to update billing address. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        async toggleAutoRenew() {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("provider.subscription.auto-renew") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        auto_renew: !this.subscriptionStatus.auto_renew
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update the subscription status
                    this.subscriptionStatus.auto_renew = data.auto_renew;
                    alert(data.message);
                } else {
                    alert(data.error || 'Failed to update auto-renewal setting.');
                }
            } catch (error) {
                alert('Failed to update auto-renewal setting. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection
