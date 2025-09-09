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
use App\Http\Controllers\SupportCoordinatorDashboardController; // Use alias
use App\Http\Controllers\SupportCoordinator\ParticipantController as SupportCoordinatorParticipantController; // Use alias
use App\Http\Controllers\Provider\ParticipantController as ProviderParticipantController; // Use alias
use App\Http\Controllers\ProviderAccommodationController;
use App\Http\Controllers\CoordinatorMessageController; // Use alias
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

Route::get('/listings', function () {
    return view('listings');
})->name('listings');

Route::get('/faqs', function () {
    return view('faqs');
})->name('faqs');

Route::get('/terms-of-service', function () {
    return view('terms');
})->name('terms.show');

Route::get('/privacy-policy', function () {
    return view('policy');
})->name('policy.show');

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
        Route::get('users', [SuperAdminDashboardController::class, 'manageUsers'])->name('users.index');
        Route::put('users/{user}/activate', [SuperAdminDashboardController::class, 'activateUser'])->name('users.activate');
        Route::put('users/{user}/deactivate', [SuperAdminDashboardController::class, 'deactivateUser'])->name('users.deactivate');
        Route::get('logs', [SuperAdminDashboardController::class, 'viewLogs'])->name('logs.index');
        Route::get('logs/download', [SuperAdminDashboardController::class, 'downloadLogs'])->name('logs.download');
        Route::get('backup', [SuperAdminDashboardController::class, 'backupDataIndex'])->name('backup.index');
        Route::post('backup/create', [SuperAdminDashboardController::class, 'createBackup'])->name('backup.create');
        Route::get('backup/download/{filename}', [SuperAdminDashboardController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('backup/delete/{filename}', [SuperAdminDashboardController::class, 'deleteBackup'])->name('backup.delete');

        Route::get('providers', [SuperAdminDashboardController::class, 'manageProviders'])->name('providers.index');
        Route::put('providers/{provider}/approve', [SuperAdminDashboardController::class, 'approveProvider'])->name('providers.approve');
        Route::put('providers/{provider}/reject', [SuperAdminDashboardController::class, 'rejectProvider'])->name('providers.reject');

        Route::get('support-coordinators', [SuperAdminDashboardController::class, 'manageSupportCoordinators'])->name('support-coordinators.index');
        Route::put('support-coordinators/{coordinator}/approve', [SuperAdminDashboardController::class, 'approveSupportCoordinator'])->name('support-coordinators.approve');
        Route::put('support-coordinators/{coordinator}/reject', [SuperAdminDashboardController::class, 'rejectSupportCoordinator'])->name('support-coordinators.reject');
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
            $user->loadMissing('provider');
            if ($user->provider && $user->provider->status === 'verified') {
                return redirect()->route('provider.dashboard');
            }
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
        });
    });

    // --- Support Coordinator Routes (Require Admin Approval) ---
    // Pending Approval View for Support Coordinators
    Route::get('/coordinator/account/pending-approval', function () {
        return view('auth.coordinator-pending-approval');
    })->name('coordinator.account.pending-approval');

    Route::prefix('sc')->middleware(['role:coordinator', 'coordinator.approved'])->name('sc.')->group(function () {
        // Support Coordinator Dashboard
        Route::get('/', [SupportCoordinatorDashboardController::class, 'index'])->name('dashboard');

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
    });

    // --- Provider Routes ---
    Route::prefix('provider')->middleware('role:provider')->name('provider.')->group(function () {
        

        Route::get('/', [ProviderDashboardController::class, 'index'])->name('dashboard');

        Route::get('participants', [ProviderDashboardController::class, 'listParticipants'])->name('participants.list');
        Route::get('participants/create', [ProviderParticipantController::class, 'create'])->name('participants.create');
        Route::post('participants', [ProviderParticipantController::class, 'store'])->name('participants.store');
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
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Provider Profile Management (for their specific Provider model fields)
        Route::get('/my-profile', [ProviderDashboardController::class, 'editProfile'])->name('my-profile.edit');
        Route::patch('/my-profile', [ProviderDashboardController::class, 'updateProfile'])->name('my-profile.update');

        // Accommodation Management by Providers
        Route::get('/accommodations', [ProviderAccommodationController::class, 'index'])->name('accommodations.list');
        Route::get('/accommodations/create', [ProviderAccommodationController::class, 'create'])->name('accommodations.create');
        Route::post('/accommodations', [ProviderAccommodationController::class, 'store'])->name('accommodations.store');
        Route::get('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'show'])->name('accommodations.show');
        Route::get('/accommodations/{accommodation}/edit', [ProviderAccommodationController::class, 'edit'])->name('accommodations.edit');
        Route::put('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'update'])->name('accommodations.update');
        Route::delete('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'destroy'])->name('accommodations.destroy');

        // Participant Matching by Providers
        Route::get('/participants-matching', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'index'])->name('participants.matching.index');
        Route::get('/participants-matching/{participant}', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'show'])->name('participants.matching.show');
        Route::get('/participants-matching/{participant}/find-matches', [App\Http\Controllers\Provider\ParticipantMatchingController::class, 'findMatches'])->name('participants.matching.find');

        // Provider messaging to participant owner (SC/Provider/Participant)
        Route::post('/participants/{participant}/messages/send-to-owner', [App\Http\Controllers\Provider\ProviderMessageController::class, 'sendToOwner'])->name('participants.messages.sendToOwner');

        // Provider messaging inbox
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [App\Http\Controllers\Provider\ProviderMessageController::class, 'index'])->name('index');
            Route::get('{conversation}', [App\Http\Controllers\Provider\ProviderMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [App\Http\Controllers\Provider\ProviderMessageController::class, 'reply'])->name('reply');
        });
    });

});

// Authentication routes included by Laravel Breeze (login, register, reset password, etc.)
require __DIR__.'/auth.php';