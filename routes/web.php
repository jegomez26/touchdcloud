<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController; // For specific registration handling if needed
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndividualDashboardController;
// use App\Http\Controllers\CompleteParticipantProfileController; // Corrected controller import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes (Accessible to everyone)
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about-us', function () {
    return view('about');
})->name('about');

Route::get('/listings', function () {
    return view('listings');
})->name('listings');

// Guest Middleware Group (Only for users who are NOT logged in)
Route::middleware('guest')->group(function () {
    // Authentication Routes provided by Breeze (including /register GET and POST, login GET and POST)
    // If you're using a custom modal that POSTs directly to /register, the route from auth.php handles it.
    // If auth.php is *not* included or customized, you'd add:
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register'); // If you need a direct GET route
    // Route::post('register', [RegisteredUserController::class, 'store'])->name('register'); // Your modal POSTs here

    // Login routes are typically handled by auth.php as well.
    // However, if you explicitly want them here:
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Authenticated Middleware Group (Only for users who ARE logged in)
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Default Breeze Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard'); 

    // Custom Dashboards (Protected by auth middleware)
    Route::get('/indiv-dashboard', [IndividualDashboardController::class, 'index'])
        ->name('indiv.dashboard'); 

    Route::get('/sc-dashboard', function () {
        return view('sc-dashboard/sc-dashboard');
    })->name('sc-dashboard');

    // Route to DISPLAY the profile completion form (GET request)
    Route::get('/profile/complete', [App\Http\Controllers\IndividualDashboardController::class, 'showCompleteProfileForm'])
        ->name('profile.complete.show'); // Give it a distinct name

    // Route to HANDLE the submission of the profile completion form (POST request)
    Route::post('/profile/complete', [App\Http\Controllers\IndividualDashboardController::class, 'completeProfile'])
        ->name('profile.complete'); // Keep this name for the form action

    // User Profile Management (Breeze default profile routes)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Participant Profile Completion Routes
    // Route::get('/participant/profile/complete', [CompleteParticipantProfileController::class, 'create'])
    //     ->middleware('verified') // Ensures email is verified before completing profile
    //     ->name('participant.profile.complete');

    // Route::post('/participant/profile/store', [CompleteParticipantProfileController::class, 'store'])
    //     ->middleware('verified') // Ensures email is verified before completing profile
    //     ->name('participant.profile.store');

    // You would add similar profile completion routes for other roles (e.g., coordinator, provider) here
    // Example:
    // Route::get('/coordinator/profile/complete', [CompleteCoordinatorProfileController::class, 'create'])
    //     ->middleware('verified')
    //     ->name('coordinator.profile.complete');
    // Route::post('/coordinator/profile/store', [CompleteCoordinatorProfileController::class, 'store'])
    //     ->middleware('verified')
    //     ->name('coordinator.profile.store');
});

// Include all standard authentication routes from Breeze.
// This handles /register GET/POST, /login GET/POST (if not overridden above),
// password reset, email verification, etc.
require __DIR__.'/auth.php';