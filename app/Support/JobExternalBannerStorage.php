<?php

namespace App\Support;

use App\Models\JobPosting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class JobExternalBannerStorage
{
    public const PUBLIC_DIR = 'job-external-banners';

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
            Log::warning('JobExternalBannerStorage upload failed', [
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'external_company_banner' => [__('talenma.jobs.external_banner_upload_failed')],
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'external_company_banner' => [__('talenma.jobs.external_banner_upload_failed')],
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

    public static function deleteIfUnused(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $stillUsed = JobPosting::query()
            ->where('external_company_banner_path', $path)
            ->exists();

        if ($stillUsed) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
