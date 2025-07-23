<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndividualDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\CompleteParticipantProfileController; // Import this if it's being used

// Public Routes
Route::get('/get-suburbs/{state}', [LocationController::class, 'getSuburbs'])->name('get.suburbs');
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

    Route::get('/company-dashboard', function () {
        return view('company/company-dashboard');
    })->name('company-dashboard');

// Guest Middleware Group (Only for users who are NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register/participant', [RegisteredUserController::class, 'createIndividual'])->name('register.individual.create');
    Route::post('register/participant', [RegisteredUserController::class, 'storeIndividual'])->name('register.individual.store');

    Route::get('register/coordinator', [RegisteredUserController::class, 'createCoordinator'])->name('register.coordinator.create');
    Route::post('register/coordinator', [RegisteredUserController::class, 'storeCoordinator'])->name('register.coordinator.store');

    Route::get('register/provider', [RegisteredUserController::class, 'createProvider'])->name('register.provider.create');
    Route::post('register/provider', [RegisteredUserController::class, 'storeProvider'])->name('register.provider.store');

    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
});

// REMOVED old /sc-dashboard and /company-dashboard public routes as they seem to be for authenticated users later

// Super Admin Dashboard Routes (Authorization handled within SuperAdminDashboardController)
Route::prefix('superadmin')->name('superadmin.')->group(function () {
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
});


// Authenticated Middleware Group (Only for users who ARE logged in)
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/indiv-dashboard', [IndividualDashboardController::class, 'index'])
        ->middleware('verified')
        ->name('indiv.dashboard');

    // Super Admin Dashboard specific direct route (if you still want /sa-dashboard for direct access)
    // The role check is handled internally by SuperAdminDashboardController
    Route::get('/sa-dashboard', [SuperAdminDashboardController::class, 'index'])
        ->middleware('verified') // Keep verified if you want to enforce email verification
        ->name('sa.dashboard');

    // Custom Dashboards for other roles (example if they use simple views and no specific middleware)
    Route::get('/sc-dashboard', function () {
        return view('supcoor/sc-dashboard');
    })->name('sc-dashboard');

    // Participant Profile Completion
    Route::get('/profile/complete', [CompleteParticipantProfileController::class, 'create'])
        ->middleware('verified')
        ->name('profile.complete.show');
    Route::post('/profile/complete', [CompleteParticipantProfileController::class, 'store'])
        ->middleware('verified')
        ->name('profile.complete');

    // User Profile Management (Breeze default profile routes)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include all standard authentication routes from Breeze.
require __DIR__.'/auth.php';