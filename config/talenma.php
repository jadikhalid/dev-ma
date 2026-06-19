<?php

return [
    'social' => [
        'x' => env('SOCIAL_X_URL', 'https://x.com'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://instagram.com'),
        'linkedin' => env('SOCIAL_LINKEDIN_URL', 'https://linkedin.com'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://youtube.com'),
    ],

    /*
    | Tuiles hero — photos locales pré-recadrées (public/images/hero/).
    | Les ratios correspondent aux tuiles CSS (tall 11:18, square 1:1, landscape 11:9).
    */
    'hero_bento' => [
        [
            'size' => 'tall',
            'photo' => 'images/hero/yasmine.jpg',
            'profile_index' => 0,
        ],
        [
            'size' => 'square',
            'photo' => 'images/hero/karim.jpg',
            'profile_index' => 1,
        ],
        [
            'size' => 'square',
            'photo' => 'images/hero/salma.jpg',
            'profile_index' => 2,
        ],
        [
            'size' => 'square',
            'photo' => 'images/hero/omar.jpg',
            'profile_index' => 3,
        ],
        [
            'size' => 'square',
            'photo' => 'images/hero/equipe.jpg',
            'profile_index' => 3,
        ],
    ],

    'hero_fallback_photo' => 'images/hero/fallback.jpg',
];
