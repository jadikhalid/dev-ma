<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserDeletionService
{
    public function __construct(
        private AvatarService $avatars,
        private PendingRegistrationService $pendingRegistrations,
    ) {}

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteProfileAssets($user);
            $this->avatars->delete($user);
            $this->pendingRegistrations->purgeForEmail($user->email);

            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            $user->profile()?->delete();
            $user->companyProfile()?->delete();
            $user->delete();
        });
    }

    private function deleteProfileAssets(User $user): void
    {
        $profile = $user->profile()->with('documents')->first();

        if (! $profile) {
            return;
        }

        foreach ($profile->documents as $document) {
            if (is_string($document->path) && $document->path !== '') {
                Storage::disk('public')->delete($document->path);
            }
        }

        Storage::disk('public')->deleteDirectory('profile-documents/'.$profile->id);
    }
}
