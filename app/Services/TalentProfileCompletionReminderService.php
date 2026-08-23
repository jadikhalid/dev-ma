<?php

namespace App\Services;

use App\Mail\TalentProfileCompletionReminderMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TalentProfileCompletionReminderService
{
    public function __construct(
        private TalentProfileCompletionService $profileCompletion,
    ) {}

    public function delayHours(): int
    {
        return max(1, (int) config('talenma.profile_completion_reminder.delay_hours', 48));
    }

    /**
     * @return array{sent: int, skipped_complete: int, eligible: int}
     */
    public function sendDueReminders(): array
    {
        $cutoff = now()->subHours($this->delayHours());

        $users = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->whereNotNull('approved_at')
            ->where('approved_at', '<=', $cutoff)
            ->whereNull('profile_completion_reminder_sent_at')
            ->whereNull('disabled_at')
            ->with(['profile.documents', 'profile.profession', 'profile.professionSector'])
            ->get();

        $sent = 0;
        $skippedComplete = 0;

        foreach ($users as $user) {
            $assessment = $this->profileCompletion->assess($user->profile);

            if ($assessment['is_catalog_ready']) {
                $skippedComplete++;

                continue;
            }

            Mail::to($user->email)->send(new TalentProfileCompletionReminderMail($user->fresh()));

            $user->forceFill([
                'profile_completion_reminder_sent_at' => now(),
            ])->save();

            $sent++;
        }

        return [
            'sent' => $sent,
            'skipped_complete' => $skippedComplete,
            'eligible' => $users->count(),
        ];
    }
}
