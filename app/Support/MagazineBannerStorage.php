<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MagazineBannerStorage
{
    public const PUBLIC_DIR = 'magazine-banner';

    public static function storeUpload(UploadedFile $file): string
    {
        $directory = public_path(self::PUBLIC_DIR);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $file->hashName();
        $file->move($directory, $filename);

        return self::PUBLIC_DIR.'/'.$filename;
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $publicFile = public_path($path);

        if (is_file($publicFile)) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('storage/'.$path);
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $publicFile = public_path($path);

        if (is_file($publicFile)) {
            File::delete($publicFile);

            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function migrateLegacyFilesToPublic(): int
    {
        $moved = 0;
        $legacyDir = storage_path('app/public/'.self::PUBLIC_DIR);

        if (! is_dir($legacyDir)) {
            return 0;
        }

        $targetDir = public_path(self::PUBLIC_DIR);

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        foreach (File::files($legacyDir) as $file) {
            $filename = $file->getFilename();
            $target = $targetDir.DIRECTORY_SEPARATOR.$filename;

            if (! is_file($target)) {
                File::copy($file->getPathname(), $target);
                $moved++;
            }
        }

        return $moved;
    }
}
