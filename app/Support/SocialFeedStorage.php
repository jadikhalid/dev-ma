<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class SocialFeedStorage
{
    public const PUBLIC_DIR = 'magazine-banner';

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
            Log::warning('SocialFeedStorage upload failed', [
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'thumbnail' => [__('talenma.admin.publications_upload_failed')],
                'post_thumbnail' => [__('talenma.admin.publications_upload_failed')],
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'thumbnail' => [__('talenma.admin.publications_upload_failed')],
                'post_thumbnail' => [__('talenma.admin.publications_upload_failed')],
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

        // Legacy files written directly under public/magazine-banner
        $publicFile = public_path($path);

        if (is_file($publicFile)) {
            $url = asset($path);
            $mtime = @filemtime($publicFile);

            return is_int($mtime) ? $url.'?v='.$mtime : $url;
        }

        if (Storage::disk('public')->exists($path)) {
            $absolute = Storage::disk('public')->path($path);
            $mtime = is_file($absolute) ? @filemtime($absolute) : false;

            return PublicStorageUrl::make($path, is_int($mtime) ? $mtime : null)
                ?? Storage::disk('public')->url($path);
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
}
