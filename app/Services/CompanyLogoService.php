<?php

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class CompanyLogoService
{
    public function __construct(private AvatarService $images) {}

    public function store(CompanyProfile $profile, UploadedFile $file): string
    {
        try {
            $path = $this->images->storeAt(
                $file,
                'company-logos/'.$profile->id,
                $profile->logo_path,
            );
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages([
                'logo' => $exception->errors()['avatar'][0] ?? __('talenma.company.logo_invalid_type'),
            ]);
        }

        $profile->update(['logo_path' => $path]);

        return $path;
    }

    public function delete(CompanyProfile $profile): void
    {
        $this->images->deleteAt($profile->logo_path);
        $profile->update(['logo_path' => null]);
    }
}
