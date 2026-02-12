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

// 1. Path update: We only need to worry about our API and Auth routes
    'paths' => ['api/*', 'login', 'register', 'logout'],

    'allowed_methods' => ['*'],

    // 2. Keep this restricted to your Next.js dev server
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000'), "https://keepr-xi.vercel.app"],

    'allowed_origins_patterns' => [],

    // 3. Ensure 'Authorization' is allowed (Sanctum tokens live here)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 4. Critical Change: Set this to FALSE for Bearer tokens
    'supports_credentials' => false,

];
