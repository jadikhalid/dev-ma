<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary
    |--------------------------------------------------------------------------
    |
    | Used for talent presentation videos. Prefer CLOUDINARY_URL when set:
    | cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'url' => env('CLOUDINARY_URL'),
    'folder' => env('CLOUDINARY_FOLDER', 'talents/presentation-videos'),

    'max_upload_kilobytes' => (int) env('CLOUDINARY_VIDEO_MAX_KB', 40960),

];
