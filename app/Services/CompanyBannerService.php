<?php

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CompanyBannerService
{
    public const MAX_WIDTH = 2000;

    public const MAX_BYTES = 4 * 1024 * 1024;

    /** @var list<string> */
    public const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public function store(CompanyProfile $profile, UploadedFile $file): string
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'banner' => __('talenma.company.banner_invalid_type'),
            ]);
        }

        [$contents, $extension] = $this->process($file);

        $this->deleteFile($profile->banner_path);

        // Unique name so CDN/browser caches never serve the previous banner.
        $path = 'company-banners/'.$profile->id.'-'.Str::random(8).'.'.$extension;
        Storage::disk('public')->put($path, $contents);

        $profile->update(['banner_path' => $path]);

        return $path;
    }

    public function delete(CompanyProfile $profile): void
    {
        $this->deleteFile($profile->banner_path);
        $profile->update(['banner_path' => null]);
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function process(UploadedFile $file): array
    {
        $source = $this->createImageResource($file);

        if ($source === false) {
            throw ValidationException::withMessages([
                'banner' => __('talenma.company.banner_invalid_type'),
            ]);
        }

        if ($source === null) {
            return [
                (string) file_get_contents($file->getRealPath()),
                match ($file->getMimeType()) {
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    default => 'jpg',
                },
            ];
        }

        $width = \imagesx($source);
        $height = \imagesy($source);

        if ($width < 1 || $height < 1) {
            \imagedestroy($source);

            throw ValidationException::withMessages([
                'banner' => __('talenma.company.banner_invalid_type'),
            ]);
        }

        $targetWidth = min(self::MAX_WIDTH, $width);
        $targetHeight = (int) round($height * $targetWidth / $width);

        $target = \imagecreatetruecolor($targetWidth, $targetHeight);
        \imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        \imagedestroy($source);

        ob_start();
        \imagejpeg($target, null, 85);
        $contents = (string) ob_get_clean();
        \imagedestroy($target);

        return [$contents, 'jpg'];
    }

    /**
     * @return \GdImage|false|null
     */
    private function createImageResource(UploadedFile $file)
    {
        if (! \extension_loaded('gd') || ! \function_exists('imagejpeg')) {
            return null;
        }

        $path = $file->getRealPath();

        return match ($file->getMimeType()) {
            'image/jpeg' => \function_exists('imagecreatefromjpeg') ? @\imagecreatefromjpeg($path) : null,
            'image/png' => \function_exists('imagecreatefrompng') ? @\imagecreatefrompng($path) : null,
            'image/webp' => \function_exists('imagecreatefromwebp') ? @\imagecreatefromwebp($path) : null,
            default => null,
        };
    }
}
