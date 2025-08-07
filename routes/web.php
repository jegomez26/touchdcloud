<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndividualDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SuperAdminDashboardController;
// use App\Http\Controllers\NdisBusinessController; // REMOVED - No longer needed
use App\Http\Controllers\ProviderDashboardController; // ADDED - For provider-specific actions
use App\Http\Controllers\CompleteParticipantProfileController;
use App\Http\Controllers\ParticipantMessageController;
use App\Http\Controllers\SupportCoordinatorDashboardController;
use App\Http\Controllers\ProviderAccommodationController;
use App\Http\Controllers\CoordinatorMessageController;

// --- Public Routes ---
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about-us', function () {
    return view('about');
})->name('about');

Route::get('/listings', function () {
    return view('listings');
})->name('listings');

Route::get('/terms-of-service', function () {
    return view('terms');
})->name('terms.show');

Route::get('/privacy-policy', function () {
    return view('policy');
})->name('policy.show');

Route::get('/pr-db', function () {
    return view('company.company-dashboard');
})->name('pr-db');

// Note: '/sc-db' is likely a temporary route. It's usually better to rely on role-based dashboard redirection.
// Route::get('/sc-db', function () {
//     return view('supcoor/sc-dashboard');
// })->name('sc-db');

Route::get('/get-suburbs/{state}', [LocationController::class, 'getSuburbs'])->name('get.suburbs');


// --- Guest-Only Routes (Authentication) ---
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
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // --- Profile Completion Routes for Participants (Only if profile_completed is false) ---
    // This route should only be accessible if the user's profile_completed is false
    Route::get('/profile/complete', [CompleteParticipantProfileController::class, 'create'])->name('profile.complete.show');
    Route::post('/profile/complete', [CompleteParticipantProfileController::class, 'store'])->name('profile.complete.store');

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

        // REMOVED: Route::resource('ndis-businesses', NdisBusinessController::class);
        // ADDED: Provider Management for Admin
        Route::get('providers', [SuperAdminDashboardController::class, 'manageProviders'])->name('providers.index');
        Route::put('providers/{provider}/approve', [SuperAdminDashboardController::class, 'approveProvider'])->name('providers.approve');
        Route::put('providers/{provider}/reject', [SuperAdminDashboardController::class, 'rejectProvider'])->name('providers.reject');


        Route::get('support-coordinators', [SuperAdminDashboardController::class, 'manageSupportCoordinators'])->name('support-coordinators.index');
        Route::put('support-coordinators/{coordinator}/approve', [SuperAdminDashboardController::class, 'approveSupportCoordinator'])->name('support-coordinators.approve');
        Route::put('support-coordinators/{coordinator}/reject', [SuperAdminDashboardController::class, 'rejectSupportCoordinator'])->name('support-coordinators.reject');
    });

    // --- General Dashboard & Role-Based Redirection ---
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'coordinator') {
            $user->loadMissing('supportCoordinator'); // Load the relationship
            if ($user->supportCoordinator && $user->supportCoordinator->status === 'verified') {
                return redirect()->route('sc.dashboard');
            } else {
                return redirect()->route('coordinator.account.pending-approval');
            }
        } elseif ($user->role === 'participant') {
            if ($user->profile_completed) {
                return redirect()->route('indiv.dashboard');
            } else {
                return redirect()->route('profile.complete.show');
            }
        } elseif ($user->role === 'provider') {
            $user->loadMissing('provider'); // Load the relationship
            if ($user->provider && $user->provider->status === 'verified') {
                return redirect()->route('provider.dashboard');
            } 
        }
        // Fallback for any unhandled roles or if no specific dashboard applies
        return view('home');
    })->name('dashboard');


    // --- Participant Panel Routes (Require Profile Completion) ---
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

        // Add any other participant-specific routes that require a complete profile here.
    });


    // --- Support Coordinator Routes (Require Admin Approval) ---
    // Pending Approval View for Support Coordinators
    Route::get('/coordinator/account/pending-approval', function () {
        return view('auth.coordinator-pending-approval');
    })->name('coordinator.account.pending-approval');

    Route::prefix('sc')->middleware(['role:coordinator', 'coordinator.approved'])->name('sc.')->group(function () {
        Route::get('/', [SupportCoordinatorDashboardController::class, 'index'])->name('dashboard');

        // Participant Management by Support Coordinators
        Route::get('participants', [SupportCoordinatorDashboardController::class, 'listParticipants'])->name('participants.list');
        Route::get('participants/create', [SupportCoordinatorDashboardController::class, 'createParticipant'])->name('participants.create');
        Route::post('participants', [SupportCoordinatorDashboardController::class, 'storeParticipant'])->name('participants.store');
        Route::get('participants/{participant}', [SupportCoordinatorDashboardController::class, 'showParticipant'])->name('participants.show');
        Route::get('participants/{participant}/edit', [SupportCoordinatorDashboardController::class, 'editParticipant'])->name('participants.edit');
        Route::put('participants/{participant}', [SupportCoordinatorDashboardController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('participants/{participant}', [SupportCoordinatorDashboardController::class, 'destroyParticipant'])->name('participants.destroy');

        // Viewing Providers (from Support Coordinator's perspective)
        // Ensure this points to the correct controller method that interacts with the 'Provider' model
        Route::get('providers', [SupportCoordinatorDashboardController::class, 'viewProviders'])->name('providers.index');
        Route::get('providers/{provider}', [SupportCoordinatorDashboardController::class, 'showProvider'])->name('providers.show'); // Changed {ndisBusiness} to {provider} to match model

        Route::get('/unassigned-participants', [SupportCoordinatorDashboardController::class, 'viewUnassignedParticipants'])->name('unassigned_participants'); // Renamed route for consistency
        Route::post('/send-message-to-participant/{participant}', [CoordinatorMessageController::class, 'sendMessageToParticipant'])
            ->name('send_message_to_participant');

        // Coordinator Messaging Routes
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [CoordinatorMessageController::class, 'index'])->name('index');
            Route::get('{conversation}', [CoordinatorMessageController::class, 'show'])->name('show');
            Route::post('{conversation}/reply', [CoordinatorMessageController::class, 'reply'])->name('reply');
        });
    });

    // --- Provider Routes ---
    Route::prefix('provider')->middleware('role:provider')->name('provider.')->group(function () { // Removed 'provider.approved' middleware
        Route::get('/', [ProviderDashboardController::class, 'index'])->name('dashboard');

        // User Profile Management (Breeze default profile routes, if providers can edit their User profile)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Provider Profile Management (for their specific Provider model fields)
        Route::get('/my-profile', [ProviderDashboardController::class, 'editProfile'])->name('my-profile.edit');
        Route::patch('/my-profile', [ProviderDashboardController::class, 'updateProfile'])->name('my-profile.update');


        Route::get('/accommodations', [ProviderAccommodationController::class, 'index'])->name('accommodations.list');
        Route::get('/accommodations/create', [ProviderAccommodationController::class, 'create'])->name('accommodations.create');
        Route::post('/accommodations', [ProviderAccommodationController::class, 'store'])->name('accommodations.store');
        Route::get('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'show'])->name('accommodations.show');
        Route::get('/accommodations/{accommodation}/edit', [ProviderAccommodationController::class, 'edit'])->name('accommodations.edit');
        Route::put('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'update'])->name('accommodations.update');
        Route::delete('/accommodations/{accommodation}', [ProviderAccommodationController::class, 'destroy'])->name('accommodations.destroy');
        // Add other provider-specific routes (e.g., managing services, listings, messages) here
    });

});

require __DIR__.'/auth.php';