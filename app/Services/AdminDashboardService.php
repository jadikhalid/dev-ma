<?php

namespace App\Services;

use App\Models\ModerationRequest;
use App\Models\RecruitmentRequest;
use App\Models\User;

class AdminDashboardService
{
    public function build(User $actor): array
    {
        $isAdmin = $actor->isAdmin();

        $talentsPending = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_PENDING)
            ->whereNotNull('email_verified_at')
            ->count();

        $companiesPending = User::query()
            ->where('role', 'company')
            ->where('approval_status', User::APPROVAL_PENDING)
            ->whereNotNull('email_verified_at')
            ->count();

        $sourcingOpen = RecruitmentRequest::query()
            ->whereIn('status', RecruitmentRequest::openStatuses())
            ->count();

        $moderationPending = $isAdmin
            ? ModerationRequest::query()->where('status', ModerationRequest::STATUS_PENDING)->count()
            : 0;

        $pendingModerationRequests = $isAdmin
            ? ModerationRequest::query()
                ->with(['requester', 'targetUser'])
                ->where('status', ModerationRequest::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (ModerationRequest $request) => [
                    'id' => $request->id,
                    'action' => __('talenma.admin.users.action_labels.'.$request->action_type),
                    'requester' => $request->requester?->name ?? '—',
                    'target' => $request->targetUser?->name,
                ])
            : collect();

        return [
            'actor' => [
                'name' => $actor->name,
                'email' => $actor->email,
                'role' => $actor->isAdmin() ? 'admin' : 'moderator',
                'role_label' => $actor->isAdmin()
                    ? __('talenma.roles.admin')
                    : __('talenma.roles.moderator'),
                'member_since' => $actor->created_at?->translatedFormat('d M Y'),
                'email_verified' => $actor->hasVerifiedEmail(),
            ],
            'kpis' => $this->kpis(
                $talentsPending,
                $companiesPending,
                $sourcingOpen,
                $moderationPending,
                $isAdmin,
            ),
            'pending_moderation_requests' => $pendingModerationRequests,
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: int|string, href: string|null, tone: string}>
     */
    private function kpis(
        int $talentsPending,
        int $companiesPending,
        int $sourcingOpen,
        int $moderationPending,
        bool $isAdmin,
    ): array {
        $kpis = [
            [
                'key' => 'pending_talents',
                'label' => __('talenma.dashboard.admin.kpi_pending_talents'),
                'value' => $talentsPending,
                'href' => route('admin.users.index', ['filter' => 'pending']),
                'tone' => $talentsPending > 0 ? 'amber' : 'slate',
            ],
            [
                'key' => 'pending_companies',
                'label' => __('talenma.dashboard.admin.kpi_pending_companies'),
                'value' => $companiesPending,
                'href' => route('admin.users.index', ['filter' => 'pending']),
                'tone' => $companiesPending > 0 ? 'amber' : 'slate',
            ],
            [
                'key' => 'sourcing_open',
                'label' => __('talenma.dashboard.admin.kpi_sourcing_open'),
                'value' => $sourcingOpen,
                'href' => route('admin.recruitment.index'),
                'tone' => $sourcingOpen > 0 ? 'sky' : 'slate',
            ],
        ];

        if ($isAdmin) {
            $kpis[] = [
                'key' => 'moderation_pending',
                'label' => __('talenma.dashboard.admin.kpi_moderation_pending'),
                'value' => $moderationPending,
                'href' => route('admin.users.index', ['filter' => 'pending']),
                'tone' => $moderationPending > 0 ? 'violet' : 'slate',
            ];
        }

        return $kpis;
    }
}
