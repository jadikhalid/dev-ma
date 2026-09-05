<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class BlogCoverStorage
{
    public const PUBLIC_DIR = 'blog-covers';

    public static function storeUpload(UploadedFile $file): string
    {
        try {
            $disk = Storage::disk('public');
            $directory = self::PUBLIC_DIR;

            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $path = $file->store($directory, 'public');
        } catch (Throwable $exception) {
            Log::warning('BlogCoverStorage upload failed', [
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'cover' => [__('talenma.blog.admin.upload_failed')],
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'cover' => [__('talenma.blog.admin.upload_failed')],
            ]);
        }

        return $path;
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
