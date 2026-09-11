<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class LibraryCoverStorage
{
    public const PUBLIC_DIR = 'library-covers';

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
            Log::warning('LibraryCoverStorage upload failed', [
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'cover' => [__('talenma.admin.library.cover_upload_failed')],
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'cover' => [__('talenma.admin.library.cover_upload_failed')],
            ]);
        }

        return $path;
    }

    public static function url(?string $path, $version = null): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return PublicStorageUrl::make($path, $version);
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public static function exists(?string $path): bool
    {
        return is_string($path)
            && $path !== ''
            && Storage::disk('public')->exists($path);
    }
}
