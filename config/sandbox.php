<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Baseline ID Ceilings (Single Source of Truth)
    |--------------------------------------------------------------------------
    |
    | All records with id <= baseline limit are protected demo records.
    | Visitors can test editing them, but deletion is blocked,
    | and their storage assets are permanently protected.
    |
    */

    'limits' => [
        'users'                   => 7,
        'categories'              => 10,
        'plans'                   => 3,
        'coupons'                 => 5,
        'faqs'                    => 6,
        'contacts'                => 15,
        'clients'                 => 10,
        'ai_modifiers'            => 10,
        'templates'               => 10,
        'properties'              => 10,
        'property_images'         => 20,
        'threads'                 => 6,
        'messages'                => 16,
        'thread_property_matches' => 8,
    ],

    'cleanup_interval_minutes' => 30,

];
