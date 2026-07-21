<?php

namespace App\Services;

use App\Models\Profile;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TalentPresentationVideoService
{
    public const ALLOWED_MIMES = [
        'video/mp4',
        'video/quicktime',
    ];

    public function maxKilobytes(): int
    {
        return max(1, (int) config('cloudinary.max_upload_kilobytes', 40960));
    }

    public function store(Profile $profile, UploadedFile $file): Profile
    {
        $cloudinary = $this->client();
        $previousPublicId = $profile->presentation_video_public_id;

        $folder = trim((string) config('cloudinary.folder', 'talents/presentation-videos'), '/');
        $folder .= '/'.$profile->user_id;

        try {
            $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'resource_type' => 'video',
                'folder' => $folder,
                'overwrite' => true,
                'invalidate' => true,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Cloudinary presentation video upload failed', [
                'profile_id' => $profile->id,
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException(__('talenma.talent.presentation_video_upload_failed'), 0, $exception);
        }

        $secureUrl = $result['secure_url'] ?? null;
        $publicId = $result['public_id'] ?? null;

        if (! is_string($secureUrl) || $secureUrl === '' || ! is_string($publicId) || $publicId === '') {
            throw new RuntimeException(__('talenma.talent.presentation_video_upload_failed'));
        }

        $profile->forceFill([
            'presentation_video_url' => $secureUrl,
            'presentation_video_public_id' => $publicId,
        ])->save();

        if (filled($previousPublicId) && $previousPublicId !== $publicId) {
            $this->deleteRemote($previousPublicId);
        }

        return $profile->fresh();
    }

    public function destroy(Profile $profile): void
    {
        $publicId = $profile->presentation_video_public_id;

        $profile->forceFill([
            'presentation_video_url' => null,
            'presentation_video_public_id' => null,
        ])->save();

        if (filled($publicId)) {
            $this->deleteRemote((string) $publicId);
        }
    }

    private function deleteRemote(string $publicId): void
    {
        try {
            $this->client()->uploadApi()->destroy($publicId, [
                'resource_type' => 'video',
                'invalidate' => true,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Cloudinary presentation video delete failed', [
                'public_id' => $publicId,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function client(): Cloudinary
    {
        $url = config('cloudinary.url');

        if (filled($url)) {
            return new Cloudinary(Configuration::instance((string) $url));
        }

        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');

        if (! filled($cloudName) || ! filled($apiKey) || ! filled($apiSecret)) {
            throw new RuntimeException(__('talenma.talent.presentation_video_not_configured'));
        }

        return new Cloudinary(Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ]));
    }
}
