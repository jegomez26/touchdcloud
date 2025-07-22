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

Route::get('/test-home', function () {
    return "Test Home Page Loaded!";
})->name('test.home');

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
    // You can serve a static Blade view for terms of service
    // Make sure you create resources/views/terms.blade.php
    return view('terms');
})->name('terms.show');

Route::get('/privacy-policy', function () {
    // You can serve a static Blade view for privacy policy
    // Make sure you create resources/views/policy.blade.php
    return view('policy');
})->name('policy.show');

// Optionally, if you want to use markdown files and parse them
Route::get('/terms-markdown', function () {
    $markdownContent = File::get(resource_path('markdown/terms.md'));
    // You would use a markdown parser here, e.g., league/commonmark
    // For simplicity, just showing the content for now
    return view('markdown-page', ['content' => $markdownContent]);
})->name('terms.markdown');

Route::get('/privacy-markdown', function () {
    $markdownContent = File::get(resource_path('markdown/privacy.md'));
    // For simplicity, just showing the content for now
    return view('markdown-page', ['content' => $markdownContent]);
})->name('policy.markdown');

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

    // // Registration Role Selection Page
    // Route::get('register-options', function () {
    //     return view('auth.register-options');
    // })->name('register.options');

    // Specific Registration Pages for each role
    Route::get('register/participant', [RegisteredUserController::class, 'createIndividual'])->name('register.individual.create');
    Route::post('register/participant', [RegisteredUserController::class, 'storeIndividual'])->name('register.individual.store');

    Route::get('register/coordinator', [RegisteredUserController::class, 'createCoordinator'])->name('register.coordinator.create');
    Route::post('register/coordinator', [RegisteredUserController::class, 'storeCoordinator'])->name('register.coordinator.store');

    Route::get('register/provider', [RegisteredUserController::class, 'createProvider'])->name('register.provider.create');
    Route::post('register/provider', [RegisteredUserController::class, 'storeProvider'])->name('register.provider.store');

    // For now, assume a single POST /register route handles all.
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
});

    Route::get('/sc-dashboard', function () {
        return view('supcoor/sc-dashboard');
    })->name('sc-dashboard');

    Route::get('/company-dashboard', function () {
        return view('company/company-dashboard');
    })->name('company-dashboard');

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
        ->middleware('verified')
        ->name('indiv.dashboard'); 

    // Route::get('/sc-dashboard', function () {
    //     return view('supcoor/sc-dashboard');
    // })->name('sc-dashboard');

    // Route to DISPLAY the profile completion form (GET request)
    Route::get('/profile/complete', [App\Http\Controllers\IndividualDashboardController::class, 'showCompleteProfileForm'])
        ->middleware('verified') // <-- ADD THIS: ONLY VERIFIED USERS CAN ACCESS PROFILE COMPLETION
        ->name('profile.complete.show'); // Give it a distinct name

    // Route to HANDLE the submission of the profile completion form (POST request)
    Route::post('/profile/complete', [App\Http\Controllers\IndividualDashboardController::class, 'completeProfile'])
        ->middleware('verified') // <-- ADD THIS
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