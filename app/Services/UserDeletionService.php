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
            $this->deleteCompanyAssets($user);
            $this->deleteMessageAttachments($user);
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

    private function deleteCompanyAssets(User $user): void
    {
        $company = $user->companyProfile()->with('documents')->first();

        if (! $company) {
            return;
        }

        if (is_string($company->logo_path) && $company->logo_path !== '') {
            Storage::disk('public')->delete($company->logo_path);
        }

        foreach ($company->documents as $document) {
            if (is_string($document->path) && $document->path !== '') {
                Storage::disk('public')->delete($document->path);
            }
        }

        Storage::disk('public')->deleteDirectory('company-profile-documents/'.$company->id);
    }

    private function deleteMessageAttachments(User $user): void
    {
        $conversations = $user->companyConversations()
            ->with('messages.attachments')
            ->get()
            ->merge($user->talentConversations()->with('messages.attachments')->get());

        foreach ($conversations as $conversation) {
            foreach ($conversation->messages as $message) {
                foreach ($message->attachments as $attachment) {
                    if (is_string($attachment->path) && $attachment->path !== '') {
                        Storage::disk($attachment->disk ?: 'local')->delete($attachment->path);
                    }
                }
            }

            Storage::disk('local')->deleteDirectory('message-attachments/'.$conversation->id);
        }
    }
}
