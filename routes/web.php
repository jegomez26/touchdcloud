<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndividualDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\ParticipantProfileController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\ParticipantMessageController;
use App\Http\Controllers\ParticipantMatchingController;
use App\Http\Controllers\SupportCoordinatorDashboardController; // Use alias
use App\Http\Controllers\SupportCoordinator\ParticipantController as SupportCoordinatorParticipantController; // Use alias
use App\Http\Controllers\Provider\ParticipantController as ProviderParticipantController; // Use alias
use App\Http\Controllers\ProviderAccommodationController;
use App\Http\Controllers\ProviderEnquiryController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CoordinatorMessageController; // Use alias
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MatchRequestController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Public Routes ---
// Routes accessible without authentication
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about-us', function () {
    return view('about');
})->name('about');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/listings', [AccommodationController::class, 'index'])->name('listings');
Route::get('/accommodation/{accommodation}', [AccommodationController::class, 'show'])->name('accommodation.show');
Route::post('/enquiries', [ProviderEnquiryController::class, 'store'])->name('enquiries.store');

Route::get('/faqs', function () {
    return view('faqs');
})->name('faqs');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sil-sda', function () {
    return view('sil-sda');
})->name('sil-sda');

Route::get('/terms-of-service', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy-policy', function () {
    return view('policy');
})->name('policy');

// Note: '/pr-db' seems like a temporary debug route, consider removing or protecting in production.
Route::get('/pr-db', function () {
    return view('company.company-dashboard');
})->name('pr-db');

// Route for dynamic suburb loading based on state
Route::get('/get-suburbs/{state}', [LocationController::class, 'getSuburbs'])->name('get.suburbs');


// --- Guest-Only Routes (Authentication) ---
// Routes only accessible to users who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Registration for different user roles
    Route::get('register/participant', [RegisteredUserController::class, 'create'])->name('register.participant.create');
    Route::post('register/participant', [RegisteredUserController::class, 'store'])->name('register.participant.store');

    Route::get('register/coordinator', [RegisteredUserController::class, 'createCoordinator'])->name('register.coordinator.create');
    Route::post('register/coordinator', [RegisteredUserController::class, 'storeCoordinator'])->name('register.coordinator.store');

    Route::get('register/provider', [RegisteredUserController::class, 'createProvider'])->name('register.provider.create');
    Route::post('register/provider', [RegisteredUserController::class, 'storeProvider'])->name('register.provider.store');
});


// --- Authenticated & Verified Routes ---
// Routes requiring a logged-in and verified user
Route::middleware(['auth', 'verified'])->group(function () {
    // Simple inbox route to view pending match requests
    Route::get('/match-requests/inbox', function(){
        return view('components.requests-inbox');
    })->name('match-requests.inbox');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // --- Participant/Representative Profile Completion Routes ---
    // These routes are for the multi-step profile completion by the participant themselves
    // or their representative. They are NOT under the 'sc' prefix.
    Route::prefix('profile')->name('indiv.profile.')->middleware('role:participant,representative')->group(function () {
        // Initial profile creation handler (e.g., if a participant registers and needs to start)
        Route::get('create', [ParticipantProfileController::class, 'create'])->name('create'); // Handles finding/creating the participant record
        Route::post('complete', [ParticipantProfileController::class, 'store'])->name('complete.store'); // For a single-page or initial completion form if needed

        // GET routes for displaying individual profile sections
        Route::get('basic-details', [ParticipantProfileController::class, 'basicDetails'])->name('basic-details');
        Route::get('ndis-support-needs', [ParticipantProfileController::class, 'ndisDetails'])->name('ndis-support-needs');
        Route::get('health-safety', [ParticipantProfileController::class, 'healthSafety'])->name('health-safety');
        Route::get('living-preferences', [ParticipantProfileController::class, 'livingPreferences'])->name('living-preferences');
        Route::get('compatibility-personality', [ParticipantProfileController::class, 'compatibilityPersonality'])->name('compatibility-personality');
        Route::get('availability', [ParticipantProfileController::class, 'availability'])->name('availability');
        Route::get('emergency-contact', [ParticipantProfileController::class, 'emergencyContact'])->name('emergency-contact');

        // PUT routes for updating individual profile sections
        Route::put('basic-details', [ParticipantProfileController::class, 'updateBasicDetails'])->name('basic-details.update');
        Route::put('ndis-support-needs', [ParticipantProfileController::class, 'updateNdisDetails'])->name('ndis-support-needs.update');
        Route::put('health-safety', [ParticipantProfileController::class, 'updateHealthSafety'])->name('health-safety.update');
        Route::put('living-preferences', [ParticipantProfileController::class, 'updateLivingPreferences'])->name('living-preferences.update');
        Route::put('compatibility-personality', [ParticipantProfileController::class, 'updateCompatibilityPersonality'])->name('compatibility-personality.update');
        Route::put('availability', [ParticipantProfileController::class, 'updateAvailability'])->name('availability.update');
        Route::put('emergency-contact', [ParticipantProfileController::class, 'updateEmergencyContact'])->name('emergency-contact.update');
    });

    // --- Super Admin Panel Routes ---
    Route::prefix('superadmin')->middleware('role:admin')->name('superadmin.')->group(function () {
        Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        
        // User management routes
        Route::middleware('privilege:manage_users')->group(function () {
            Route::get('users', [SuperAdminDashboardController::class, 'manageUsers'])->name('users.index');
            Route::put('users/{user}/activate', [SuperAdminDashboardController::class, 'activateUser'])->name('users.activate');
            Route::put('users/{user}/deactivate', [SuperAdminDashboardController::class, 'deactivateUser'])->name('users.deactivate');
        });
        
        // Log management routes
        Route::middleware('privilege:view_logs')->group(function () {
            Route::get('logs', [SuperAdminDashboardController::class, 'viewLogs'])->name('logs.index');
            Route::get('logs/download', [SuperAdminDashboardController::class, 'downloadLogs'])->name('logs.download');
        });
        
        // Backup management routes
        Route::middleware('privilege:manage_backups')->group(function () {
            Route::get('backup', [SuperAdminDashboardController::class, 'backupDataIndex'])->name('backup.index');
            Route::post('backup/create', [SuperAdminDashboardController::class, 'createBackup'])->name('backup.create');
            Route::get('backup/download/{filename}', [SuperAdminDashboardController::class, 'downloadBackup'])->name('backup.download');
            Route::delete('backup/delete/{filename}', [SuperAdminDashboardController::class, 'deleteBackup'])->name('backup.delete');
        });

        // Provider management routes
        Route::middleware('privilege:manage_providers')->group(function () {
            Route::get('providers', [SuperAdminDashboardController::class, 'manageProviders'])->name('providers.index');
            Route::put('providers/{provider}/approve', [SuperAdminDashboardController::class, 'approveProvider'])->name('providers.approve');
            Route::put('providers/{provider}/reject', [SuperAdminDashboardController::class, 'rejectProvider'])->name('providers.reject');
            Route::put('providers/{provider}/activate', [SuperAdminDashboardController::class, 'activateProvider'])->name('providers.activate');
            Route::put('providers/{provider}/deactivate', [SuperAdminDashboardController::class, 'deactivateProvider'])->name('providers.deactivate');
        });

        // Participant management routes
        Route::middleware('privilege:manage_participants')->group(function () {
            Route::get('participants', [SuperAdminDashboardController::class, 'manageParticipants'])->name('participants.index');
            Route::put('participants/{participant}/activate', [SuperAdminDashboardController::class, 'activateParticipant'])->name('participants.activate');
            Route::put('participants/{participant}/deactivate', [SuperAdminDashboardController::class, 'deactivateParticipant'])->name('participants.deactivate');
        });
        
        // Support coordinator management routes
        Route::middleware('privilege:manage_support_coordinators')->group(function () {
            Route::get('support-coordinators', [SuperAdminDashboardController::class, 'manageSupportCoordinators'])->name('support-coordinators.index');
            Route::put('support-coordinators/{coordinator}/approve', [SuperAdminDashboardController::class, 'approveSupportCoordinator'])->name('support-coordinators.approve');
            Route::put('support-coordinators/{coordinator}/reject', [SuperAdminDashboardController::class, 'rejectSupportCoordinator'])->name('support-coordinators.reject');
            Route::put('support-coordinators/{coordinator}/activate', [SuperAdminDashboardController::class, 'activateSupportCoordinator'])->name('support-coordinators.activate');
            Route::put('support-coordinators/{coordinator}/deactivate', [SuperAdminDashboardController::class, 'deactivateSupportCoordinator'])->name('support-coordinators.deactivate');
        });

        // Admin management routes
        Route::middleware('privilege:manage_admins')->group(function () {
            Route::get('admins', [SuperAdminDashboardController::class, 'manageAdmins'])->name('admins.index');
            Route::post('admins', [SuperAdminDashboardController::class, 'createAdmin'])->name('admins.create');
            Route::put('admins/{admin}/activate', [SuperAdminDashboardController::class, 'activateAdmin'])->name('admins.activate');
            Route::put('admins/{admin}/deactivate', [SuperAdminDashboardController::class, 'deactivateAdmin'])->name('admins.deactivate');
        });
        
        // Modal Demo Route (accessible to all admins)
        Route::get('modal-demo', [SuperAdminDashboardController::class, 'modalDemo'])->name('modal-demo');
        
        // Test route that requires specific privilege
        Route::get('test-privilege', [SuperAdminDashboardController::class, 'testPrivilege'])->middleware('privilege:manage_backups')->name('test-privilege');
        
        // Support Center routes
        Route::get('support-center', [SuperAdminDashboardController::class, 'supportCenter'])->name('support-center.index');
        Route::put('support-center/{ticket}/status', [SuperAdminDashboardController::class, 'updateTicketStatus'])->name('support-center.update-status');
        Route::put('support-center/{ticket}/assign', [SuperAdminDashboardController::class, 'assignTicket'])->name('support-center.assign');
        Route::get('support-center/{ticket}', [SuperAdminDashboardController::class, 'viewTicket'])->name('support-center.view');
    });

    // --- General Dashboard & Role-Based Redirection ---
    // This route acts as a central hub for authenticated users
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'coordinator') {
            $user->loadMissing('supportCoordinator');
            if ($user->supportCoordinator && $user->supportCoordinator->status === 'verified') {
                return redirect()->route('sc.dashboard');
            } else {
                return redirect()->route('coordinator.account.pending-approval');
            }
        } elseif ($user->role === 'participant' || $user->is_representative) { // Include representatives here
            if ($user->profile_completed) {
                return redirect()->route('indiv.dashboard');
            } else {
                // Redirect to the first step of the multi-step form for profile completion
                return redirect()->route('indiv.profile.basic-details');
            }
        } elseif ($user->role === 'provider') {
            // All providers go to their dashboard
            return redirect()->route('provider.dashboard');
        }
        return view('home'); // Fallback if role is not recognized
    })->name('dashboard');

    // --- Participant Panel Routes (Require Profile Completion) ---
    // These routes are only accessible to participants *after* their profile is completed.
    Route::middleware('role:participant', 'profile.complete.check')->prefix('participant')->name('indiv.')->group(function () {
        // Individual Participant Dashboard
        Route::get('/dashboard', [IndividualDashboardController::class, 'index'])->name('dashboard');

        // User Profile Management (Breeze default profile routes)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Participant Messaging Routes
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ParticipantMessageController::class, 'index'])->name('inbox');
            Route::get('{conversation}', [ParticipantMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [ParticipantMessageController::class, 'reply'])->name('reply');
            Route::post('send-to-coordinator/{supportCoordinatorId}', [ParticipantMessageController::class, 'sendMessageToCoordinator'])->name('sendToCoordinator');
            Route::post('send-to-participant/{participantId}', [ParticipantMessageController::class, 'sendMessageToParticipant'])->name('sendToParticipant');
            
            // Debug route for testing
            Route::get('debug/test-message-creation', function() {
                $user = Auth::user();
                $participant = $user->participant;
                
                if (!$participant) {
                    return response()->json(['error' => 'No participant found for user'], 400);
                }
                
                // Test creating a conversation
                $conversation = \App\Models\Conversation::create([
                    'type' => 'participant_to_participant',
                    'sender_participant_id' => $participant->id,
                    'participant_id' => $participant->id, // Self-conversation for testing
                    'last_message_at' => now(),
                ]);
                
                // Test creating a message
                $message = $conversation->messages()->create([
                    'sender_id' => $user->id,
                    'receiver_id' => $user->id,
                    'content' => 'Test message',
                    'type' => 'text',
                    'sent_at' => now(),
                    'original_sender_role' => 'participant',
                    'original_recipient_role' => 'participant',
                ]);
                
                return response()->json([
                    'success' => true,
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'participant_id' => $participant->id
                ]);
            })->name('debug.test');
        });

        // Participant Matching Routes
        Route::get('/possible-matches', [IndividualDashboardController::class, 'possibleMatches'])->name('possible-matches');
        Route::get('/possible-matches/find', [IndividualDashboardController::class, 'findMatches'])->name('possible-matches.find');
        
        // Participant Details and Messaging Routes
        Route::get('/participants/{participant}/details', [IndividualDashboardController::class, 'getParticipantDetails'])->name('participants.details');
        Route::post('/participants/{participant}/send-message', [IndividualDashboardController::class, 'sendMessage'])->name('participants.send_message');
        Route::get('/match-requests/pending', [IndividualDashboardController::class, 'getPendingMatchRequests'])->name('match_requests.pending');
        Route::get('/match-requests', [IndividualDashboardController::class, 'matchRequests'])->name('match-requests.index');
        
        // Support Center routes
        Route::get('support-center', [IndividualDashboardController::class, 'supportCenter'])->name('support-center.index');
        Route::post('support-center', [IndividualDashboardController::class, 'createTicket'])->name('support-center.create');
        Route::get('support-center/{ticket}', [IndividualDashboardController::class, 'viewTicket'])->name('support-center.view');
        Route::post('support-center/{ticket}/comment', [IndividualDashboardController::class, 'addComment'])->name('support-center.comment');
    });

    // --- Match Request Routes (Available to all authenticated users) ---
    Route::prefix('match-requests')->name('match-requests.')->group(function () {
        Route::post('send', [MatchRequestController::class, 'sendRequest'])->name('send');
        Route::post('send-for-participant', [MatchRequestController::class, 'sendRequestForParticipant'])->name('send_for_participant');
        Route::post('{requestId}/accept', [MatchRequestController::class, 'acceptRequest'])->name('accept');
        Route::post('{requestId}/reject', [MatchRequestController::class, 'rejectRequest'])->name('reject');
        Route::get('pending', [MatchRequestController::class, 'getPendingRequests'])->name('pending');
        Route::get('sent', [MatchRequestController::class, 'getSentRequests'])->name('sent');
        Route::post('check-status', [MatchRequestController::class, 'checkMatchRequestStatus'])->name('check-status');
        Route::post('can-start-conversation', [MatchRequestController::class, 'canStartConversation'])->name('can-start-conversation');
    });

    // --- Support Coordinator Routes (Require Admin Approval) ---
    // Pending Approval View for Support Coordinators
    Route::get('/coordinator/account/pending-approval', function () {
        return view('auth.coordinator-pending-approval');
    })->name('coordinator.account.pending-approval');

    Route::prefix('sc')->middleware(['role:coordinator', 'coordinator.approved'])->name('sc.')->group(function () {
        // Support Coordinator Dashboard
        Route::get('/', [SupportCoordinatorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-content', [SupportCoordinatorDashboardController::class, 'index'])->name('dashboard-content');

        // Participant Management by Support Coordinators (CRUD operations)
        // These routes use the dedicated SupportCoordinatorParticipantController
        Route::get('participants', [SupportCoordinatorDashboardController::class, 'listParticipants'])->name('participants.list');
        Route::get('participants/create', [SupportCoordinatorParticipantController::class, 'create'])->name('participants.create');
        Route::post('participants', [SupportCoordinatorParticipantController::class, 'store'])->name('participants.store');
        Route::get('participants/{participant}', [SupportCoordinatorParticipantController::class, 'show'])->name('participants.show');
        Route::get('participants/{participant}/edit', [SupportCoordinatorParticipantController::class, 'showBasicDetails'])->name('participants.edit');
        Route::put('participants/{participant}', [SupportCoordinatorParticipantController::class, 'update'])->name('participants.update');
        Route::delete('participants/{participant}', [SupportCoordinatorParticipantController::class, 'destroy'])->name('participants.destroy');

        // Participant Profile Editing by Support Coordinators (for specific sections of existing participants)
        // These routes allow a SC to edit sections of a participant's profile, identified by {participant}
        Route::prefix('participants/{participant}/profile')->name('participants.profile.')->group(function () {
            Route::get('basic-details', [SupportCoordinatorParticipantController::class, 'showBasicDetails'])->name('basic-details');
            Route::put('basic-details', [SupportCoordinatorParticipantController::class, 'updateBasicDetails'])->name('basic-details.update');

            Route::get('ndis-support-needs', [SupportCoordinatorParticipantController::class, 'showNdisDetails'])->name('ndis-support-needs');
            Route::put('ndis-support-needs', [SupportCoordinatorParticipantController::class, 'updateNdisDetails'])->name('ndis-support-needs.update');

            Route::get('health-safety', [SupportCoordinatorParticipantController::class, 'showHealthSafety'])->name('health-safety');
            Route::put('health-safety', [SupportCoordinatorParticipantController::class, 'updateHealthSafety'])->name('health-safety.update');

            Route::get('living-preferences', [SupportCoordinatorParticipantController::class, 'showLivingPreferences'])->name('living-preferences');
            Route::put('living-preferences', [SupportCoordinatorParticipantController::class, 'updateLivingPreferences'])->name('living-preferences.update');

            Route::get('compatibility-personality', [SupportCoordinatorParticipantController::class, 'showCompatibilityPersonality'])->name('compatibility-personality');
            Route::put('compatibility-personality', [SupportCoordinatorParticipantController::class, 'updateCompatibilityPersonality'])->name('compatibility-personality.update');

            Route::get('availability', [SupportCoordinatorParticipantController::class, 'showAvailability'])->name('availability');
            Route::put('availability', [SupportCoordinatorParticipantController::class, 'updateAvailability'])->name('availability.update');

            Route::get('emergency-contact', [SupportCoordinatorParticipantController::class, 'showEmergencyContact'])->name('emergency-contact');
            Route::put('emergency-contact', [SupportCoordinatorParticipantController::class, 'updateEmergencyContact'])->name('emergency-contact.update');
        });

        // Viewing Providers (from Support Coordinator's perspective)
        Route::get('providers', [SupportCoordinatorDashboardController::class, 'viewProviders'])->name('providers.index');
        Route::get('providers/{provider}', [SupportCoordinatorDashboardController::class, 'showProvider'])->name('providers.show');

        // Other Support Coordinator specific routes
        Route::get('/unassigned-participants', [SupportCoordinatorDashboardController::class, 'viewUnassignedParticipants'])->name('unassigned_participants');
        Route::post('/send-message-to-participant/{participant}', [CoordinatorMessageController::class, 'sendMessageToParticipant'])->name('send_message_to_participant');

        // Coordinator Messaging Routes
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [CoordinatorMessageController::class, 'index'])->name('index');
            Route::get('{conversation}', [CoordinatorMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [CoordinatorMessageController::class, 'reply'])->name('reply');
        });
        
        // Participant Matching by Support Coordinators
        Route::get('/participants-matching', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'index'])->name('participants.matching.index');
        Route::get('/participants-matching/{participant}', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'show'])->name('participants.matching.show');
        Route::get('/participants-matching/{participant}/find-matches', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'findMatches'])->name('participants.matching.find');
        
        // Participant Details (Anonymous)
        Route::get('/participants/{participant}/details', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'getParticipantDetails'])->name('participants.details');
        
        // Send Message to Participant Owner
        Route::post('/participants/{participant}/send-message', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'sendMessage'])->name('participants.send_message');
        
        // Send Message to Participant Owner (Complex Logic)
        Route::post('/participants/{participant}/messages/send-to-owner', [App\Http\Controllers\SupportCoordinator\ParticipantMatchingController::class, 'sendToOwner'])->name('participants.messages.sendToOwner');
        
        // Support Coordinator messaging inbox
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [App\Http\Controllers\SupportCoordinator\SupportCoordinatorMessageController::class, 'index'])->name('index');
            Route::get('{conversation}', [App\Http\Controllers\SupportCoordinator\SupportCoordinatorMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [App\Http\Controllers\SupportCoordinator\SupportCoordinatorMessageController::class, 'reply'])->name('reply');
        });
        
        // Match Requests
        Route::get('match-requests', [SupportCoordinatorDashboardController::class, 'matchRequests'])->name('match-requests.index');
        
        // Support Center routes
        Route::get('support-center', [SupportCoordinatorDashboardController::class, 'supportCenter'])->name('support-center.index');
        Route::post('support-center', [SupportCoordinatorDashboardController::class, 'createTicket'])->name('support-center.create');
        Route::get('support-center/{ticket}', [SupportCoordinatorDashboardController::class, 'viewTicket'])->name('support-center.view');
        Route::post('support-center/{ticket}/comment', [SupportCoordinatorDashboardController::class, 'addComment'])->name('support-center.comment');
    });

    // --- Subscription Routes ---
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'index'])->name('plans');
        Route::get('/manage', [SubscriptionController::class, 'manage'])->name('manage');
        Route::post('/trial', [SubscriptionController::class, 'startTrial'])->name('trial');
        Route::post('/convert-trial', [SubscriptionController::class, 'convertTrial'])->name('convert-trial');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/create-payment-intent', [SubscriptionController::class, 'createPaymentIntent'])->name('create-payment-intent');
        Route::post('/confirm-subscription', [SubscriptionController::class, 'confirmSubscription'])->name('confirm-subscription');
        Route::get('/checkout/success', [SubscriptionController::class, 'checkoutSuccess'])->name('checkout.success');
        Route::get('/checkout/cancel', [SubscriptionController::class, 'checkoutCancel'])->name('checkout.cancel');
        Route::get('/promo/check', [SubscriptionController::class, 'checkPromoAvailability'])->name('promo.check');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/trial-status', [SubscriptionController::class, 'getTrialStatus'])->name('trial-status');
        Route::get('/analytics', [SubscriptionController::class, 'getAnalytics'])->name('analytics');
        Route::post('/simulate-webhook', [SubscriptionController::class, 'simulateWebhook'])->name('simulate-webhook');
        Route::get('/can-add-participants', [SubscriptionController::class, 'canAddParticipantProfiles'])->name('can-add-participants');
        Route::get('/can-add-accommodations', [SubscriptionController::class, 'canAddAccommodationListings'])->name('can-add-accommodations');
    });

    // --- Provider Routes ---
    Route::prefix('provider')->middleware('role:provider')->name('provider.')->group(function () {
        

        Route::get('/', [ProviderDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-content', [ProviderDashboardController::class, 'index'])->name('dashboard-content');

        // Provider Profile routes
        Route::get('profile/edit', [ProviderDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('profile/update', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update');

        Route::get('participants', [ProviderDashboardController::class, 'listParticipants'])->name('participants.list');
        Route::get('participants/create', [ProviderParticipantController::class, 'create'])->middleware('subscription.required')->name('participants.create');
        Route::post('participants', [ProviderParticipantController::class, 'store'])->middleware('subscription.required', 'check.participant.limit')->name('participants.store');
        Route::get('participants/{participant}', [ProviderParticipantController::class, 'show'])->name('participants.show');
        Route::get('participants/{participant}/edit', [ProviderParticipantController::class, 'showBasicDetails'])->name('participants.edit');
        Route::put('participants/{participant}', [ProviderParticipantController::class, 'update'])->name('participants.update');
        Route::delete('participants/{participant}', [ProviderParticipantController::class, 'destroy'])->name('participants.destroy');

        // Participant Profile Editing by Support Coordinators (for specific sections of existing participants)
        // These routes allow a SC to edit sections of a participant's profile, identified by {participant}
        Route::prefix('participants/{participant}/profile')->name('participants.profile.')->group(function () {
            Route::get('basic-details', [ProviderParticipantController::class, 'showBasicDetails'])->name('basic-details');
            Route::put('basic-details', [ProviderParticipantController::class, 'updateBasicDetails'])->name('basic-details.update');

            Route::get('ndis-support-needs', [ProviderParticipantController::class, 'showNdisDetails'])->name('ndis-support-needs');
            Route::put('ndis-support-needs', [ProviderParticipantController::class, 'updateNdisDetails'])->name('ndis-support-needs.update');

            Route::get('health-safety', [ProviderParticipantController::class, 'showHealthSafety'])->name('health-safety');
            Route::put('health-safety', [ProviderParticipantController::class, 'updateHealthSafety'])->name('health-safety.update');

            Route::get('living-preferences', [ProviderParticipantController::class, 'showLivingPreferences'])->name('living-preferences');
            Route::put('living-preferences', [ProviderParticipantController::class, 'updateLivingPreferences'])->name('living-preferences.update');

            Route::get('compatibility-personality', [ProviderParticipantController::class, 'showCompatibilityPersonality'])->name('compatibility-personality');
            Route::put('compatibility-personality', [ProviderParticipantController::class, 'updateCompatibilityPersonality'])->name('compatibility-personality.update');

            Route::get('availability', [ProviderParticipantController::class, 'showAvailability'])->name('availability');
            Route::put('availability', [ProviderParticipantController::class, 'updateAvailability'])->name('availability.update');

            Route::get('emergency-contact', [ProviderParticipantController::class, 'showEmergencyContact'])->name('emergency-contact');
            Route::put('emergency-contact', [ProviderParticipantController::class, 'updateEmergencyContact'])->name('emergency-contact.update');
        });


        // User Profile Management (Breeze default profile routes, if providers can edit their User profile)
        Route::get('/user-profile', [ProfileController::class, 'edit'])->name('user-profile.edit');
        Route::patch('/user-profile', [ProfileController::class, 'update'])->name('user-profile.update');
        Route::delete('/user-profile', [ProfileController::class, 'destroy'])->name('user-profile.destroy');

        // Provider Profile Management (for their specific Provider model fields)
        Route::get('/my-profile', [ProviderDashboardController::class, 'editProfile'])->name('my-profile.edit');
        Route::patch('/my-profile', [ProviderDashboardController::class, 'updateProfile'])->name('my-profile.update');

        // Accommodation Management by Providers
        Route::get('/accommodations', [ProviderAccommodationController::class, 'index'])->name('accommodations.index');
        Route::get('/accommodations/create', [ProviderAccommodationController::class, 'create'])->middleware('subscription.required')->name('accommodations.create');
        Route::post('/accommodations', [ProviderAccommodationController::class, 'store'])->middleware('subscription.required', 'check.accommodation.limit')->name('accommodations.store');
        Route::get('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'show'])->name('accommodations.show');
        Route::get('/accommodations/{accommodation}/edit', [ProviderAccommodationController::class, 'edit'])->name('accommodations.edit');
        Route::put('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'update'])->name('accommodations.update');
        Route::delete('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'destroy'])->name('accommodations.destroy');

        // Enquiry Management by Providers
        Route::get('/enquiries', [ProviderEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [ProviderEnquiryController::class, 'show'])->name('enquiries.show');
        Route::put('/enquiries/{enquiry}', [ProviderEnquiryController::class, 'update'])->name('enquiries.update');

        // Participant Matching by Providers
        Route::get('/participants-matching', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'index'])->middleware('subscription.required')->name('participants.matching.index');
        Route::get('/participants-matching/{participant}', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'show'])->middleware('subscription.required')->name('participants.matching.show');
        Route::get('/participants-matching/{participant}/find-matches', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'findMatches'])->middleware('subscription.required')->name('participants.matching.find');
        
        // Participant Details (Anonymous)
        Route::get('/participants/{participant}/details', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'getParticipantDetails'])->middleware('subscription.required')->name('participants.details');

        // Provider messaging to participant owner (SC/Provider/Participant)
        Route::post('/participants/{participant}/messages/send-to-owner', [App\Http\Controllers\Provider\ProviderMessageController::class, 'sendToOwner'])->name('provider.participants.messages.sendToOwner');

        // Provider messaging inbox
        Route::prefix('messages')->name('messages.')->middleware('subscription.required')->group(function () {
            Route::get('/', [App\Http\Controllers\Provider\ProviderMessageController::class, 'index'])->name('index');
            Route::get('{conversation}', [App\Http\Controllers\Provider\ProviderMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [App\Http\Controllers\Provider\ProviderMessageController::class, 'reply'])->name('reply');
        });

        // Billing and renewals
        Route::get('/billing', [ProviderDashboardController::class, 'billing'])->name('billing');
        
        // Invoice downloads
        Route::get('/invoice/{paymentId}/download', [App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
        Route::get('/receipt/{paymentId}/download', [App\Http\Controllers\InvoiceController::class, 'downloadReceipt'])->name('receipt.download');
        
        // Auto-renewal toggle
        Route::post('/subscription/auto-renew', [App\Http\Controllers\SubscriptionController::class, 'toggleAutoRenew'])->name('subscription.auto-renew');
        
        // Match Requests
        Route::get('match-requests', [ProviderDashboardController::class, 'matchRequests'])->name('match-requests.index');
        
        // Support Center routes
        Route::get('support-center', [ProviderDashboardController::class, 'supportCenter'])->name('support-center.index');
        Route::post('support-center', [ProviderDashboardController::class, 'createTicket'])->name('support-center.create');
        Route::get('support-center/{ticket}', [ProviderDashboardController::class, 'viewTicket'])->name('support-center.view');
        Route::post('support-center/{ticket}/comment', [ProviderDashboardController::class, 'addComment'])->name('support-center.comment');
    });

});

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');

// Authentication routes included by Laravel Breeze (login, register, reset password, etc.)
require __DIR__.'/auth.php';