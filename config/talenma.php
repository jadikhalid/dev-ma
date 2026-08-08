<?php

return [
    'social' => [
        'x' => env('SOCIAL_X_URL', 'https://x.com/talentsdumaroc'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://www.instagram.com/talentsdumaroc/'),
        'linkedin' => env('SOCIAL_LINKEDIN_URL', 'https://www.linkedin.com/company/talentsdumaroc/'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://www.youtube.com/channel/UCjmtHFsH0U-Uddo5xM3mhgg'),
    ],

    /*
    | Photos hero de secours (public/images/hero/) — utilisées si moins de 6
    | talents publics avec photo de profil.
    */
    'hero_fake_photos' => [
        'images/hero/infirmiere.jpg',
        'images/hero/karim.jpg',
        'images/hero/salma.jpg',
        'images/hero/omar.jpg',
        'images/hero/tarik.jpg',
        'images/hero/yasmine.jpg',
    ],

    'hero_fallback_photo' => 'images/hero/karim.jpg',

    /*
    | Compte admin bootstrap (AdminUserSeeder uniquement — jamais au redeploy).
    | Utiliser config() et non env() dans le seeder (config:cache en prod).
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@talentsdumaroc.com'),
        'password' => env('ADMIN_PASSWORD', 'ChangeMe-Admin-2026!'),
    ],

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
