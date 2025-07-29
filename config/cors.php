<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'], // IMPORTANT: Ensure 'broadcasting/auth' is here!

    'allowed_methods' => ['*'], // Or specify 'GET', 'POST', 'OPTIONS' etc.

    'allowed_origins' => [
        'http://192.168.254.120:8000', // <--- Add your frontend's exact origin here
        // 'http://localhost:3000', // Example if you use a React/Vue dev server
        // 'https://your-production-frontend.com', // For production
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Or specify 'Content-Type', 'Authorization', 'X-Requested-With' etc.

    'exposed_headers' => [],

    'max_age' => 0, // Cache preflight requests for a short period (in seconds)

    'supports_credentials' => true, // Set to true if you are sending cookies or authentication headers
];