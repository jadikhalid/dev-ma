<?php

namespace App\Services;

use App\Models\User;

class CompanyMemberAccountCompletionService
{
    /**
     * Progress for a company member personal account (not the org company profile).
     *
     * @return array{
     *     percent: int,
     *     status: string,
     *     sections: array<string, array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool}>}>,
     *     next_section: string|null,
     *     is_catalog_ready: bool
     * }
     */
    public function assess(User $user): array
    {
        $items = [
            ['label' => __('talenma.account.avatar'), 'done' => filled($user->avatar_path)],
            ['label' => __('talenma.auth.first_name'), 'done' => filled($user->first_name)],
            ['label' => __('talenma.auth.last_name'), 'done' => filled($user->last_name)],
            ['label' => __('talenma.account.email'), 'done' => filled($user->email)],
        ];

        $doneCount = collect($items)->where('done', true)->count();
        $percent = (int) round(($doneCount / count($items)) * 100);
        $complete = $doneCount === count($items);

        $status = match (true) {
            $percent >= 100 => 'complete',
            $percent >= 40 => 'in_progress',
            default => 'starter',
        };

        return [
            'percent' => $percent,
            'status' => $status,
            'sections' => [
                'account' => [
                    'label' => __('talenma.account.personal_info_title'),
                    'complete' => $complete,
                    'percent' => $percent,
                    'items' => $items,
                ],
            ],
            'next_section' => $complete ? null : 'account',
            'is_catalog_ready' => $complete,
        ];
    }
}
