<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileCompletionController;


Route::middleware('guest')->group(function () {
    // We no longer need a GET route for /register as it's purely modal-driven now.
    // The modal directly POSTs to the store method.
    // However, if you want direct access for coordinator/provider for any reason, you'd keep a GET route here.

    // Route::post('register', [RegisteredUserController::class, 'store'])->name('register'); // Handles initial registration post
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// New route for profile completion
Route::post('/profile/complete', [ProfileCompletionController::class, 'complete'])
    ->middleware(['auth', 'verified']) // Only authenticated and verified users can complete profile
    ->name('profile.complete');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Your custom landing page (Home)
Route::get('/', function () {
    return view('home'); // <-- CHANGE THIS LINE from 'welcome' to 'home'
})->name('home'); // <-- ADD THIS LINE to name your home route

// Your custom About Us page
Route::get('/about-us', function () {
    return view('about');
})->name('about');

// Your custom Listings page
Route::get('/listings', function () {
    return view('listings');
})->name('listings');

Route::get('/indiv-dashboard', function () {
    return view('indiv-dashboard/indiv-dashboard');
})->name('indiv-dashboard');

Route::get('/sc-dashboard', function () {
    return view('sc-dashboard/sc-dashboard');
})->name('sc-dashboard');

// Default Breeze dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/register/{role?}', [RegisteredUserController::class, 'create'])
//             ->name('register'); // The 'role?' makes the parameter optional

// Route::post('/register', [RegisteredUserController::class, 'store']);

// Default Breeze profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Includes all authentication routes (login, register, logout, etc.) from Breeze
require __DIR__.'/auth.php';