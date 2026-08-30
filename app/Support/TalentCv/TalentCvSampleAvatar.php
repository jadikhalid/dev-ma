<?php

namespace App\Support\TalentCv;

class TalentCvSampleAvatar
{
    public static function dataUri(): string
    {
        static $cached = null;

        if (is_string($cached)) {
            return $cached;
        }

        foreach (['sample-avatar.jpg', 'sample-avatar.png', 'sample-avatar.svg'] as $file) {
            $path = public_path('images/cv-builder/'.$file);

            if (! is_file($path)) {
                continue;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($extension) {
                'svg' => 'image/svg+xml',
                'png' => 'image/png',
                default => 'image/jpeg',
            };

            $cached = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));

            return $cached;
        }

        $cached = '';

        return $cached;
    }
}
