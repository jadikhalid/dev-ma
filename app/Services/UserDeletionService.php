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
        private DirectHireService $directHires,
        private ModeratorAssignmentService $moderatorAssignments,
    ) {}

    public function delete(User $user, ?User $actor = null): void
    {
        DB::transaction(function () use ($user, $actor) {
            if ($user->isModerator()) {
                if (! $actor?->isAdmin()) {
                    abort(403);
                }

                $this->moderatorAssignments->revokeForDeletion($actor, $user);
            }

            if ($user->isCompanyOwner()) {
                $company = $user->companyProfile()
                    ->with('memberships.user')
                    ->first();

                // Delete seats first while their organization and owner still exist.
                // This lets process history be reassigned/detached cleanly before
                // the company profile and all organization data are removed.
                foreach ($company?->memberships ?? [] as $membership) {
                    $member = $membership->user;

                    if ($member && (int) $member->id !== (int) $user->id) {
                        $this->deleteSingleUser($member);
                    }
                }
            }

            $this->deleteSingleUser($user);
        });
    }

    private function deleteSingleUser(User $user): void
    {
        // Detach (or purge orphan) direct-hire dossiers before FK/profile cleanup.
        $this->directHires->releasePartyOnUserDeletion($user);

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

        if (is_string($company->representative_photo_path) && $company->representative_photo_path !== '') {
            Storage::disk('public')->delete($company->representative_photo_path);
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
