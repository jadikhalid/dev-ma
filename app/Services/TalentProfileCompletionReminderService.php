<?php

namespace App\Services;

use App\Mail\TalentProfileCompletionReminderMail;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class TalentProfileCompletionReminderService
{
    public function delayHours(): int
    {
        return max(1, (int) config('talenma.profile_completion_reminder.delay_hours', 48));
    }

    public function maxReminders(): int
    {
        return max(1, (int) config('talenma.profile_completion_reminder.max_reminders', 3));
    }

    /**
     * @return array{sent: int, eligible: int}
     */
    public function sendDueReminders(): array
    {
        $cutoff = now()->subHours($this->delayHours());
        $max = $this->maxReminders();

        $users = $this->baseEligibleQuery()
            ->where('profile_completion_reminder_count', '<', $max)
            ->where(function (Builder $query) use ($cutoff) {
                $query->where(function (Builder $first) use ($cutoff) {
                    $first->where('profile_completion_reminder_count', 0)
                        ->where('approved_at', '<=', $cutoff);
                })->orWhere(function (Builder $followUp) use ($cutoff) {
                    $followUp->where('profile_completion_reminder_count', '>', 0)
                        ->whereNotNull('profile_completion_reminder_sent_at')
                        ->where('profile_completion_reminder_sent_at', '<=', $cutoff);
                });
            })
            ->orderBy('id')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $step = ((int) $user->profile_completion_reminder_count) + 1;

            Mail::to($user->email)->send(new TalentProfileCompletionReminderMail($user->fresh(), $step));

            $user->forceFill([
                'profile_completion_reminder_sent_at' => now(),
                'profile_completion_reminder_count' => $step,
            ])->save();

            $sent++;
        }

        return [
            'sent' => $sent,
            'eligible' => $users->count(),
        ];
    }

    private function baseEligibleQuery(): Builder
    {
        return User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->whereNotNull('approved_at')
            ->whereNull('disabled_at')
            ->whereDoesntHave('profile.documents', function ($documents) {
                $documents->where('document_type', ProfileDocument::TYPE_CV);
            });
    }
}
