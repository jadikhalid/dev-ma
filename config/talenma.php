<?php

return [
    'social' => [
        'x' => env('SOCIAL_X_URL', 'https://x.com/talentsdumaroc'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://www.instagram.com/talentsdumaroc/'),
        'linkedin' => env('SOCIAL_LINKEDIN_URL', 'https://www.linkedin.com/company/talentsdumaroc/'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://www.youtube.com/channel/UCjmtHFsH0U-Uddo5xM3mhgg'),
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
            'photo' => 'images/hero/tarik.jpg',
            'profile_index' => 4,
        ],
    ],

    'hero_fallback_photo' => 'images/hero/fallback.jpg',

    /*
    | Pays francophones — locale FR sur mobile (détection IP).
    | Codes ISO 3166-1 alpha-2.
    */
    'francophone_countries' => [
        'FR', 'MA', 'BE', 'CH', 'LU', 'MC', 'CA',
        'SN', 'CI', 'ML', 'BF', 'NE', 'TG', 'BJ', 'CM', 'CD', 'CG', 'GA', 'GN',
        'MG', 'HT', 'MU', 'RW', 'SC', 'TD', 'CF', 'DJ', 'KM', 'TN', 'DZ', 'VU',
        'NC', 'PF', 'RE', 'GP', 'MQ', 'GF', 'YT', 'PM', 'WF', 'BL', 'MF',
    ],
];
