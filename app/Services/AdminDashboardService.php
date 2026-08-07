<?php

namespace App\Services;

use App\Models\DirectHireRequest;
use App\Models\JobPosting;
use App\Models\ModeratorAssignment;
use App\Models\ModeratorPermissionCatalog;
use App\Models\RecruitmentRequest;
use App\Models\User;

class AdminDashboardService
{
    public function build(User $actor): array
    {
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
            'stat_groups' => $this->statGroups($actor),
        ];
    }

    /**
     * @return list<array{key: string, label: string, items: list<array{key: string, label: string, value: int, href: string|null, tone: string}>}>
     */
    private function statGroups(User $actor): array
    {
        $groups = [];

        if ($actor->isAdmin() || $actor->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_VIEW)) {
            $groups[] = $this->accountsGroup();
        }

        if ($actor->isAdmin() || $actor->hasModeratorPermission(ModeratorPermissionCatalog::SOURCING_MANAGE)) {
            $groups[] = $this->sourcingGroup();
        }

        if ($actor->isAdmin() || $actor->hasModeratorPermission(ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE)) {
            $groups[] = $this->directHireGroup();
        }

        if ($actor->isAdmin() || $actor->hasModeratorPermission(ModeratorPermissionCatalog::JOBS_MANAGE)) {
            $groups[] = $this->jobsGroup();
        }

        return $groups;
    }

    /**
     * @return array{key: string, label: string, items: list<array{key: string, label: string, value: int, href: string|null, tone: string}>}
     */
    private function accountsGroup(): array
    {
        $talentsApproved = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->count();

        $talentsPending = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_PENDING)
            ->whereNotNull('email_verified_at')
            ->count();

        $companiesApproved = User::query()
            ->where('role', 'company')
            ->where('company_seat', User::SEAT_OWNER)
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->count();

        $companiesPending = User::query()
            ->where('role', 'company')
            ->where('company_seat', User::SEAT_OWNER)
            ->where('approval_status', User::APPROVAL_PENDING)
            ->whereNotNull('email_verified_at')
            ->count();

        $moderators = ModeratorAssignment::query()
            ->whereNull('revoked_at')
            ->count();

        return [
            'key' => 'accounts',
            'label' => __('talenma.dashboard.admin.stats_group_accounts'),
            'items' => [
                $this->item('talents_approved', __('talenma.dashboard.admin.kpi_approved_talents'), $talentsApproved, route('admin.users.index', ['filter' => 'talents']), 'emerald'),
                $this->item('talents_pending', __('talenma.dashboard.admin.kpi_pending_talents'), $talentsPending, route('admin.users.index', ['filter' => 'pending']), $talentsPending > 0 ? 'amber' : 'slate'),
                $this->item('companies_approved', __('talenma.dashboard.admin.kpi_companies'), $companiesApproved, route('admin.users.index', ['filter' => 'companies']), 'indigo'),
                $this->item('companies_pending', __('talenma.dashboard.admin.kpi_pending_companies'), $companiesPending, route('admin.users.index', ['filter' => 'pending']), $companiesPending > 0 ? 'amber' : 'slate'),
                $this->item('moderators', __('talenma.dashboard.admin.kpi_moderators'), $moderators, route('admin.users.index', ['filter' => 'moderators']), 'violet'),
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, items: list<array{key: string, label: string, value: int, href: string|null, tone: string}>}
     */
    private function sourcingGroup(): array
    {
        $pending = RecruitmentRequest::query()
            ->where('status', RecruitmentRequest::STATUS_PENDING)
            ->count();

        $inProgress = RecruitmentRequest::query()
            ->where('status', RecruitmentRequest::STATUS_IN_PROGRESS)
            ->count();

        $open = $pending + $inProgress;

        $closed = RecruitmentRequest::query()
            ->whereIn('status', RecruitmentRequest::closedStatuses())
            ->count();

        return [
            'key' => 'sourcing',
            'label' => __('talenma.dashboard.admin.stats_group_sourcing'),
            'items' => [
                $this->item('sourcing_open', __('talenma.dashboard.admin.kpi_sourcing_open'), $open, route('admin.recruitment.index', ['filter' => 'pending']), $open > 0 ? 'sky' : 'slate'),
                $this->item('sourcing_pending', __('talenma.dashboard.admin.kpi_sourcing_pending'), $pending, route('admin.recruitment.index', ['filter' => 'pending']), $pending > 0 ? 'amber' : 'slate'),
                $this->item('sourcing_in_progress', __('talenma.dashboard.admin.kpi_sourcing_in_progress'), $inProgress, route('admin.recruitment.index', ['filter' => 'in_progress']), $inProgress > 0 ? 'sky' : 'slate'),
                $this->item('sourcing_closed', __('talenma.dashboard.admin.kpi_sourcing_closed'), $closed, route('admin.recruitment.index', ['filter' => 'completed_successful']), 'slate'),
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, items: list<array{key: string, label: string, value: int, href: string|null, tone: string}>}
     */
    private function directHireGroup(): array
    {
        $open = DirectHireRequest::query()
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->count();

        $pendingResponse = DirectHireRequest::query()
            ->where('status', DirectHireRequest::STATUS_PENDING_RESPONSE)
            ->count();

        $inProcess = DirectHireRequest::query()
            ->where('status', DirectHireRequest::STATUS_IN_PROCESS)
            ->count();

        $hired = DirectHireRequest::query()
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->count();

        return [
            'key' => 'direct_hire',
            'label' => __('talenma.dashboard.admin.stats_group_direct_hire'),
            'items' => [
                $this->item('direct_hire_open', __('talenma.dashboard.admin.kpi_direct_hire_open'), $open, route('admin.direct-hire.index'), $open > 0 ? 'sky' : 'slate'),
                $this->item('direct_hire_pending', __('talenma.dashboard.admin.kpi_direct_hire_pending'), $pendingResponse, route('admin.direct-hire.index'), $pendingResponse > 0 ? 'amber' : 'slate'),
                $this->item('direct_hire_in_process', __('talenma.dashboard.admin.kpi_direct_hire_in_process'), $inProcess, route('admin.direct-hire.index'), $inProcess > 0 ? 'indigo' : 'slate'),
                $this->item('direct_hire_hired', __('talenma.dashboard.admin.kpi_direct_hire_hired'), $hired, route('admin.direct-hire.index'), 'emerald'),
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, items: list<array{key: string, label: string, value: int, href: string|null, tone: string}>}
     */
    private function jobsGroup(): array
    {
        $published = JobPosting::query()
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->count();

        $draft = JobPosting::query()
            ->where('status', JobPosting::STATUS_DRAFT)
            ->count();

        $closed = JobPosting::query()
            ->where('status', JobPosting::STATUS_CLOSED)
            ->count();

        $hidden = JobPosting::query()
            ->whereIn('status', [JobPosting::STATUS_HIDDEN, JobPosting::STATUS_POSTPONED])
            ->count();

        return [
            'key' => 'jobs',
            'label' => __('talenma.dashboard.admin.stats_group_jobs'),
            'items' => [
                $this->item('jobs_published', __('talenma.dashboard.admin.kpi_jobs_published'), $published, route('admin.jobs.index'), $published > 0 ? 'emerald' : 'slate'),
                $this->item('jobs_draft', __('talenma.dashboard.admin.kpi_jobs_draft'), $draft, route('admin.jobs.index'), $draft > 0 ? 'amber' : 'slate'),
                $this->item('jobs_closed', __('talenma.dashboard.admin.kpi_jobs_closed'), $closed, route('admin.jobs.index'), 'slate'),
                $this->item('jobs_other', __('talenma.dashboard.admin.kpi_jobs_other'), $hidden, route('admin.jobs.index'), 'slate'),
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, value: int, href: string|null, tone: string}
     */
    private function item(string $key, string $label, int $value, ?string $href, string $tone): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'value' => $value,
            'href' => $href,
            'tone' => $tone,
        ];
    }
}
