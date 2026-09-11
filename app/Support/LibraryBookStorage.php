<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LibraryBookStorage
{
    public const DISK = 'local';

    public const DIR = 'library-books';

    public static function storeUpload(UploadedFile $file): string
    {
        try {
            $disk = Storage::disk(self::DISK);
            $directory = self::DIR;

            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $path = $file->store($directory, self::DISK);
        } catch (Throwable $exception) {
            Log::warning('LibraryBookStorage upload failed', [
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'file' => [__('talenma.admin.library.upload_failed')],
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'file' => [__('talenma.admin.library.upload_failed')],
            ]);
        }

        return $path;
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    public static function exists(?string $path): bool
    {
        return is_string($path)
            && $path !== ''
            && Storage::disk(self::DISK)->exists($path);
    }

    public static function download(string $path, string $downloadName): StreamedResponse
    {
        return Storage::disk(self::DISK)->download($path, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
