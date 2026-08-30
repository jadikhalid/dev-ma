<?php

namespace App\Support\TalentCv;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TalentCvPhotoResolver
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function resolve(array $data, ?User $user): ?string
    {
        $embedded = trim((string) ($data['photo_base64'] ?? ''));

        if ($embedded !== '') {
            if (str_starts_with($embedded, 'data:')) {
                return $embedded;
            }

            return 'data:image/jpeg;base64,'.$embedded;
        }

        if (! $user?->avatar_path) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($user->avatar_path)) {
            return null;
        }

        $contents = $disk->get($user->avatar_path);
        $mime = $disk->mimeType($user->avatar_path) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
