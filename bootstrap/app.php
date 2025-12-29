<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Import your custom middleware classes
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckProfileCompletion;
use App\Http\Middleware\CheckCoordinatorVerification;
use App\Http\Middleware\EnsureProfileIsComplete;
use App\Http\Middleware\SubscriptionRequired;
use App\Http\Middleware\CheckParticipantLimit;
use App\Http\Middleware\CheckAccommodationLimit;
use App\Http\Middleware\CheckPrivilege;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register your route middleware aliases here
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'profile.incomplete' => CheckProfileCompletion::class,
            'coordinator.verified' => CheckCoordinatorVerification::class,
            'profile.complete.check' => EnsureProfileIsComplete::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Laravel's default
            'coordinator.approved' => \App\Http\Middleware\EnsureCoordinatorIsApproved::class, // <-- ADD THIS
            'subscription.required' => SubscriptionRequired::class,
            'check.participant.limit' => CheckParticipantLimit::class,
            'check.accommodation.limit' => CheckAccommodationLimit::class,
            'privilege' => CheckPrivilege::class,
        ]);

        // If you have any global web middleware you want to append, you can do it here.
        // For example, if you want to ensure VerifyCsrfToken runs last in your web group:
        // $middleware->web(append: [
        //     \App\Http\Middleware\VerifyCsrfToken::class,
        // ]);

        // You can also define your global middleware stacks here if you need to customize them
        // $middleware->group('web', [
        //     // ... existing web middleware ...
        // ]);
        // $middleware->group('api', [
        //     // ... existing api middleware ...
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();