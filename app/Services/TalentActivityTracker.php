<?php

namespace App\Services;

use App\Models\ProfileDocument;
use App\Models\ProfileDocumentDownload;
use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TalentActivityTracker
{
    public function recordProfileView(User $talent, User $viewer): void
    {
        if (! $talent->isTalent() || ! $viewer->isCompany() || $talent->id === $viewer->id) {
            return;
        }

        try {
            $alreadyToday = ProfileView::query()
                ->where('talent_user_id', $talent->id)
                ->where('viewer_user_id', $viewer->id)
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if ($alreadyToday) {
                return;
            }

            ProfileView::query()->create([
                'talent_user_id' => $talent->id,
                'viewer_user_id' => $viewer->id,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to record profile view', [
                'talent_user_id' => $talent->id,
                'viewer_user_id' => $viewer->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function recordCvDownload(User $talent, User $downloader, ProfileDocument $document): void
    {
        if (! $talent->isTalent() || ! $downloader->isCompany() || ! $document->isCv()) {
            return;
        }

        try {
            ProfileDocumentDownload::query()->create([
                'profile_document_id' => $document->id,
                'talent_user_id' => $talent->id,
                'downloader_user_id' => $downloader->id,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to record CV download', [
                'talent_user_id' => $talent->id,
                'downloader_user_id' => $downloader->id,
                'profile_document_id' => $document->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
