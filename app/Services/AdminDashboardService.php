<?php

namespace App\Services;

use App\Models\ModerationRequest;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\RecruitmentRequest;
use App\Models\SocialFeedItem;
use App\Models\SocialPost;
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

        $talentsApproved = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->count();

        $talentsRejected = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_REJECTED)
            ->count();

        $companiesCount = User::query()->where('role', 'company')->count();
        $moderatorsCount = User::query()->where('role', 'moderator')->count();

        $registrationsLast7Days = User::query()
            ->whereIn('role', ['dev', 'company'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $registrationsLast30Days = User::query()
            ->whereIn('role', ['dev', 'company'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $recruitmentPending = RecruitmentRequest::query()->where('status', 'pending')->count();
        $recruitmentTotal = RecruitmentRequest::query()->count();

        $moderationPending = $isAdmin
            ? ModerationRequest::query()->where('status', ModerationRequest::STATUS_PENDING)->count()
            : 0;

        $recentPendingTalents = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_PENDING)
            ->whereNotNull('email_verified_at')
            ->with('profile.professionSector')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'sector' => $user->profile?->sectorLabel() ?? '—',
                'registered_at' => $user->created_at?->translatedFormat('d M Y, H:i'),
                'email_verified' => $user->hasVerifiedEmail(),
            ]);

        $recentRegistrations = User::query()
            ->whereIn('role', ['dev', 'company'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'approval_status' => $user->approval_status,
                'registered_at' => $user->created_at?->translatedFormat('d M Y, H:i'),
            ]);

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
            'alerts' => $this->alerts($talentsPending, $moderationPending, $isAdmin),
            'kpis' => $this->kpis(
                $talentsPending,
                $talentsApproved,
                $companiesCount,
                $recruitmentPending,
                $moderationPending,
                $registrationsLast7Days,
                $isAdmin,
            ),
            'user_breakdown' => [
                'talents_pending' => $talentsPending,
                'talents_approved' => $talentsApproved,
                'talents_rejected' => $talentsRejected,
                'companies' => $companiesCount,
                'moderators' => $moderatorsCount,
                'registrations_7d' => $registrationsLast7Days,
                'registrations_30d' => $registrationsLast30Days,
            ],
            'platform' => [
                'recruitment_pending' => $recruitmentPending,
                'recruitment_total' => $recruitmentTotal,
                'news_items' => SocialFeedItem::query()->count(),
                'social_posts' => SocialPost::query()->count(),
                'sectors' => ProfessionSector::query()->where('is_active', true)->count(),
                'professions' => Profession::query()->where('is_active', true)->count(),
            ],
            'recent_pending_talents' => $recentPendingTalents,
            'recent_registrations' => $recentRegistrations,
            'pending_moderation_requests' => $pendingModerationRequests,
            'quick_actions' => $this->quickActions($isAdmin, $talentsPending, $moderationPending),
        ];
    }

    /**
     * @return list<array{message: string, tone: string, href: string|null}>
     */
    private function alerts(int $talentsPending, int $moderationPending, bool $isAdmin): array
    {
        $alerts = [];

        if ($talentsPending > 0) {
            $alerts[] = [
                'message' => trans_choice('talenma.dashboard.admin.alert_pending_talents', $talentsPending, ['count' => $talentsPending]),
                'tone' => 'amber',
                'href' => route('admin.users.index', ['filter' => 'pending']),
            ];
        }

        if ($isAdmin && $moderationPending > 0) {
            $alerts[] = [
                'message' => trans_choice('talenma.dashboard.admin.alert_moderation_requests', $moderationPending, ['count' => $moderationPending]),
                'tone' => 'violet',
                'href' => route('admin.users.index', ['filter' => 'pending']),
            ];
        }

        return $alerts;
    }

    /**
     * @return list<array{key: string, label: string, value: int|string, href: string|null, tone: string}>
     */
    private function kpis(
        int $talentsPending,
        int $talentsApproved,
        int $companiesCount,
        int $recruitmentPending,
        int $moderationPending,
        int $registrationsLast7Days,
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
                'key' => 'approved_talents',
                'label' => __('talenma.dashboard.admin.kpi_approved_talents'),
                'value' => $talentsApproved,
                'href' => route('admin.users.index', ['filter' => 'talents']),
                'tone' => 'indigo',
            ],
            [
                'key' => 'companies',
                'label' => __('talenma.dashboard.admin.kpi_companies'),
                'value' => $companiesCount,
                'href' => route('admin.users.index', ['filter' => 'companies']),
                'tone' => 'emerald',
            ],
            [
                'key' => 'recruitment_pending',
                'label' => __('talenma.dashboard.admin.kpi_recruitment_pending'),
                'value' => $recruitmentPending,
                'href' => null,
                'tone' => $recruitmentPending > 0 ? 'sky' : 'slate',
            ],
            [
                'key' => 'registrations_7d',
                'label' => __('talenma.dashboard.admin.kpi_registrations_7d'),
                'value' => $registrationsLast7Days,
                'href' => route('admin.users.index', ['filter' => 'all']),
                'tone' => 'slate',
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

    /**
     * @return list<array{label: string, href: string, tone: string}>
     */
    private function quickActions(bool $isAdmin, int $talentsPending, int $moderationPending): array
    {
        $actions = [
            [
                'label' => __('talenma.dashboard.admin.action_review_pending'),
                'href' => route('admin.users.index', ['filter' => 'pending']),
                'tone' => 'indigo',
                'badge' => $talentsPending > 0 ? (string) $talentsPending : null,
            ],
            [
                'label' => __('talenma.dashboard.admin.action_all_users'),
                'href' => route('admin.users.index', ['filter' => 'all']),
                'tone' => 'slate',
                'badge' => null,
            ],
            [
                'label' => __('talenma.dashboard.admin.action_home'),
                'href' => route('home'),
                'tone' => 'slate',
                'badge' => null,
            ],
        ];

        if ($isAdmin) {
            array_splice($actions, 1, 0, [[
                'label' => __('talenma.dashboard.admin.action_publications'),
                'href' => route('admin.publications.index'),
                'tone' => 'violet',
                'badge' => null,
            ]]);

            if ($moderationPending > 0) {
                $actions[0]['badge'] = (string) ($talentsPending + $moderationPending);
            }
        }

        return $actions;
    }
}
