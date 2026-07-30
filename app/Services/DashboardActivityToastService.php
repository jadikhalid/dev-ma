<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class DashboardActivityToastService
{
    /** Own actions — already confirmed by action toasts; skip on dashboard reload. */
    private const COMPANY_SELF_TYPES = [
        'recruitment_submitted',
        'recruitment_message_sent',
        'direct_hire_proposed',
        'direct_hire_message_sent',
        'inbox_message_sent',
        'direct_hire_withdrawn',
        'direct_hire_hired',
        'direct_hire_closed_negative',
        'direct_hire_deferral_accepted',
        'direct_hire_round_added',
        'direct_hire_round_updated',
        'direct_hire_round_result',
    ];

    private const TALENT_SELF_TYPES = [
        'direct_hire_accepted',
        'direct_hire_declined',
        'direct_hire_deferred',
        'direct_hire_message_sent',
        'inbox_message_sent',
    ];

    /**
     * Flash a single summary toast when unseen activity exists, then mark the dashboard as seen.
     *
     * @param  list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: ?CarbonInterface, self?: bool}>  $activity
     */
    public function flashUnseen(User $user, array $activity, string $audience): void
    {
        if ($this->hasUnseen($user, $activity, $audience)) {
            session()->now('toast_activity', [
                __('talenma.dashboard.activity_toast.new'),
            ]);
        }

        $this->markSeen($user);
    }

    /**
     * @param  list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: ?CarbonInterface, self?: bool}>  $activity
     */
    public function hasUnseen(User $user, array $activity, string $audience): bool
    {
        $seenAt = $user->dashboard_activity_seen_at;

        // First visit after rollout: baseline without flooding the user.
        if ($seenAt === null) {
            return false;
        }

        $selfTypes = $audience === 'talent'
            ? self::TALENT_SELF_TYPES
            : self::COMPANY_SELF_TYPES;

        return collect($activity)->contains(function (array $item) use ($seenAt, $selfTypes, $audience) {
            $at = $item['at'] ?? null;

            if (! $at instanceof CarbonInterface) {
                return false;
            }

            if ($at->lessThanOrEqualTo($seenAt)) {
                return false;
            }

            if (($item['self'] ?? false) === true) {
                return false;
            }

            // Staff feed uses per-item `self` (actor-aware); anything else is news.
            if ($audience === 'staff') {
                return true;
            }

            return ! in_array($item['type'] ?? '', $selfTypes, true);
        });
    }

    public function markSeen(User $user): void
    {
        $now = now();

        DB::table('users')
            ->where('id', $user->id)
            ->update(['dashboard_activity_seen_at' => $now]);

        $user->dashboard_activity_seen_at = $now;
    }
}
