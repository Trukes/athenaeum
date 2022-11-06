<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default ETag Generator
    |--------------------------------------------------------------------------
    |
    | Name of default ETag Generator profile to use, when none specified.
    */

    'default_generator' => env('DEFAULT_ETAG_GENERATOR', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Generator Profiles
    |--------------------------------------------------------------------------
    |
    | List of available ETag Generator profiles
    */

    'profiles' => [

        'default' => [
            'driver' => \Aedart\ETags\Generators\GenericGenerator::class,
            'options' => [

                // Hashing algorithm intended for ETags flagged as "weak" (weak comparison)
                'weak_algo' => 'crc32',

                // Hashing algorithm intended for ETags NOT flagged as "weak" (strong comparison)
                'strong_algo' => 'sha256',
            ],
        ],
    ]
];