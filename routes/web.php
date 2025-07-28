<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndividualDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\NdisBusinessController;
use App\Http\Controllers\CompleteParticipantProfileController;
use App\Http\Controllers\SupportCoordinatorDashboardController; // Make sure this is imported

// --- Public Routes ---
// These routes are accessible to anyone, regardless of authentication or profile completion.
Route::get('/', function () {
    return view('home'); // Your public landing page
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

Route::get('/sc-db', function () {
    return view('supcoor/sc-dashboard');
})->name('sc-db');

Route::get('/get-suburbs/{state}', [LocationController::class, 'getSuburbs'])->name('get.suburbs');


// --- Guest-Only Routes ---
// These routes are only accessible to users who are NOT logged in.
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Participant Registration Routes
    Route::get('register/participant', [RegisteredUserController::class, 'create'])->name('register.participant.create');
    Route::post('register/participant', [RegisteredUserController::class, 'store'])->name('register.participant.store');

    // Support Coordinator Registration Routes
    Route::get('register/coordinator', [RegisteredUserController::class, 'createCoordinator'])->name('register.coordinator.create');
    Route::post('register/coordinator', [RegisteredUserController::class, 'storeCoordinator'])->name('register.coordinator.store');

    // Provider Registration Routes
    Route::get('register/provider', [RegisteredUserController::class, 'createProvider'])->name('register.provider.create');
    Route::post('register/provider', [RegisteredUserController::class, 'storeProvider'])->name('register.provider.store');
});


// --- Authenticated & Verified Routes ---
// These routes require the user to be logged in AND have their email verified.
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // --- Profile Completion Routes ---
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

        Route::resource('ndis-businesses', NdisBusinessController::class);

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
            // Eager load supportCoordinator to prevent N+1 queries if not already loaded
            $user->loadMissing('supportCoordinator');
            if ($user->supportCoordinator && $user->supportCoordinator->status === 'verified') {
                return redirect()->route('sc.dashboard'); // Changed name for consistency
            } else {
                // Redirect to the new pending approval page if email verified but not admin approved
                return redirect()->route('coordinator.account.pending-approval');
            }
        } elseif ($user->role === 'participant') {
            if ($user->profile_completed) {
                return redirect()->route('indiv.dashboard');
            } else {
                return redirect()->route('profile.complete.show');
            }
        }
        // Fallback for any other roles or unexpected scenarios
        return view('home'); // A generic dashboard if none match or for initial setup
    })->name('dashboard');


    // --- Participant Panel Routes (Require Profile Completion) ---
    Route::middleware('profile.complete.check')->group(function () {
        // Individual Participant Dashboard
        Route::get('/indiv-dashboard', [IndividualDashboardController::class, 'index'])
            ->middleware('role:participant')
            ->name('indiv.dashboard');

        // User Profile Management (Breeze default profile routes)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Add any other participant-specific routes that require a complete profile here.
    });

    // --- Support Coordinator Routes (Require Admin Approval) ---
    // Route for the "Waiting for Admin Approval" page for coordinators
    Route::get('/coordinator/account/pending-approval', function () {
        return view('auth.coordinator-pending-approval');
    })->name('coordinator.account.pending-approval');

    // Support Coordinator Dashboard and other specific routes
    // ONLY accessible if email is verified AND admin approved.
    Route::prefix('sc')->middleware(['role:coordinator', 'coordinator.approved'])->name('sc.')->group(function () {
        Route::get('/', [SupportCoordinatorDashboardController::class, 'index'])->name('dashboard');

        // Participants management by Support Coordinator
        Route::get('participants', [SupportCoordinatorDashboardController::class, 'listParticipants'])->name('participants.list');
        Route::get('participants/create', [SupportCoordinatorDashboardController::class, 'createParticipant'])->name('participants.create');
        Route::post('participants', [SupportCoordinatorDashboardController::class, 'storeParticipant'])->name('participants.store');
        Route::get('participants/{participant}', [SupportCoordinatorDashboardController::class, 'showParticipant'])->name('participants.show');
        Route::get('participants/{participant}/edit', [SupportCoordinatorDashboardController::class, 'editParticipant'])->name('participants.edit');
        Route::put('participants/{participant}', [SupportCoordinatorDashboardController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('participants/{participant}', [SupportCoordinatorDashboardController::class, 'destroyParticipant'])->name('participants.destroy');

        // Or you could use a resource controller for participants if appropriate
        // Route::resource('participants', SupportCoordinatorParticipantController::class);

        // NDIS Business/Provider Listing viewing for Support Coordinators
        Route::get('providers', [SupportCoordinatorDashboardController::class, 'viewProviders'])->name('providers.index');
        Route::get('providers/{ndisBusiness}', [SupportCoordinatorDashboardController::class, 'showProvider'])->name('providers.show');

        Route::get('/unassigned-participants', [SupportCoordinatorDashboardController::class, 'viewUnassignedParticipants'])->name('supcoor.unassigned_participants');
    Route::post('/send-message/{participant}', [SupportCoordinatorDashboardController::class, 'sendMessage'])->name('supcoor.send_message');

        // Other SC-specific features can be added here
        // Route::get('appointments', [SupportCoordinatorAppointmentController::class, 'index'])->name('appointments.index');
        // Route::get('reports', [SupportCoordinatorReportController::class, 'index'])->name('reports.index');
    });

    // Removed the direct '/sa-dashboard' route as it's handled by the general '/dashboard' redirection now.
});

// Include all standard authentication routes from Breeze.
// This is crucial for verification.notice, verification.verify, etc.
require __DIR__.'/auth.php';