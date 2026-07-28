<?php

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class CompanyContactPhotoService
{
    public function __construct(private AvatarService $images) {}

    public function store(CompanyProfile $profile, UploadedFile $file): string
    {
        try {
            $path = $this->images->storeAt(
                $file,
                'company-contacts/'.$profile->id,
                $profile->representative_photo_path,
            );
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages([
                'representative_photo' => $exception->errors()['avatar'][0]
                    ?? __('talenma.company.representative_photo_invalid_type'),
            ]);
        }

        $profile->update(['representative_photo_path' => $path]);

        return $path;
    }

    public function delete(CompanyProfile $profile): void
    {
        $this->images->deleteAt($profile->representative_photo_path);
        $profile->update(['representative_photo_path' => null]);
    }
}
