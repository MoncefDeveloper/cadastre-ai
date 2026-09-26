<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/v1/*'],

    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],

    // Explicit Production & Local Whitelist
    'allowed_origins' => [
        'https://cadastre.framer.ai',
        'https://framer.com',
        'https://app.framer.com',
        env('FRONTEND_URL', 'https://cadastre.moncefdev.me'),
        'http://localhost:3000',
        'http://127.0.0.1:3000',
    ],

    // Strict regex patterns allowing only Framer preview & CDN subdomains
    'allowed_origins_patterns' => [
        '#^https://.*\.framer\.(app|website|ai|com|wiki)$#',
        '#^https://.*\.framerusercontent\.com$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Requested-With',
    ],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
