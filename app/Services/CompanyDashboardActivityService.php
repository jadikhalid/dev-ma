<?php

namespace App\Services;

use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\JobApplication;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class CompanyDashboardActivityService
{
    public function __construct(
        private DirectHireService $directHires,
    ) {}

    /**
     * @return list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    public function recentActivity(User $company, int $limit = 20): array
    {
        if (! $company->isCompany()) {
            return [];
        }

        $fetch = max($limit * 2, 20);

        return $this->directHireEvents($company, $fetch)
            ->concat($this->roundEvents($company, $fetch))
            ->concat($this->recruitmentEvents($company, $fetch))
            ->concat($this->jobApplicationEvents($company, $fetch))
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function directHireEvents(User $company, int $limit): Collection
    {
        $events = collect();

        $requests = $this->directHires->queryForCompany($company)
            ->with(['talent'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($requests as $request) {
            $actor = $request->talentDisplayName()
                ?: __('talenma.dashboard.company.activity.unknown_talent');
            $subject = $request->shortSubject();
            $href = route('company.direct-hire.show', $request);

            if ($request->created_at) {
                $events->push($this->activityItem(
                    type: 'direct_hire_proposed',
                    actor: $actor,
                    at: $request->created_at,
                    subject: $subject,
                    href: $href,
                ));
            }

            if ($request->talent_decision_at) {
                $type = match ($request->status) {
                    DirectHireRequest::STATUS_IN_PROCESS,
                    DirectHireRequest::STATUS_HIRED,
                    DirectHireRequest::STATUS_CLOSED_NEGATIVE => 'direct_hire_accepted',
                    DirectHireRequest::STATUS_DECLINED => 'direct_hire_declined',
                    DirectHireRequest::STATUS_DEFERRED => 'direct_hire_deferred',
                    default => null,
                };

                if ($type !== null) {
                    $events->push($this->activityItem(
                        type: $type,
                        actor: $actor,
                        at: $request->talent_decision_at,
                        subject: $subject,
                        href: $href,
                    ));
                }
            }

            if ($request->closed_at && in_array($request->status, [
                DirectHireRequest::STATUS_HIRED,
                DirectHireRequest::STATUS_CLOSED_NEGATIVE,
            ], true)) {
                $events->push($this->activityItem(
                    type: $request->status === DirectHireRequest::STATUS_HIRED
                        ? 'direct_hire_hired'
                        : 'direct_hire_closed_negative',
                    actor: $actor,
                    at: $request->closed_at,
                    subject: $subject,
                    href: $href,
                ));
            }
        }

        $messageQuery = DirectHireMessage::query()
            ->whereHas('request', function ($query) use ($company) {
                $this->scopeDirectHireQuery($query, $company);
            })
            ->with(['request.talent'])
            ->latest()
            ->limit($limit)
            ->get();

        foreach ($messageQuery as $message) {
            $request = $message->request;

            if (! $request || (int) $message->sender_user_id !== (int) $request->talent_user_id) {
                continue;
            }

            $events->push($this->activityItem(
                type: 'direct_hire_message',
                actor: $request->talentDisplayName()
                    ?: __('talenma.dashboard.company.activity.unknown_talent'),
                at: $message->created_at,
                subject: $request->shortSubject(),
                href: route('company.direct-hire.show', $request),
            ));
        }

        return $events;
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function roundEvents(User $company, int $limit): Collection
    {
        $events = collect();

        $rounds = DirectHireRound::query()
            ->whereHas('request', function ($query) use ($company) {
                $this->scopeDirectHireQuery($query, $company);
            })
            ->with(['request.talent'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($rounds as $round) {
            $request = $round->request;

            if (! $request) {
                continue;
            }

            $actor = $request->talentDisplayName()
                ?: __('talenma.dashboard.company.activity.unknown_talent');
            $subject = $request->shortSubject();
            $href = route('company.direct-hire.show', $request);

            if ($round->created_at) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_added',
                    actor: $actor,
                    at: $round->created_at,
                    detail: $round->title,
                    subject: $subject,
                    href: $href,
                ));
            }

            if (
                $round->completed_at
                && in_array($round->status, DirectHireRound::outcomeStatuses(), true)
            ) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_result',
                    actor: $actor,
                    at: $round->completed_at,
                    detail: $round->title,
                    subject: $subject,
                    result: $round->statusLabel(),
                    href: $href,
                ));
            } elseif (
                $round->updated_at
                && $round->created_at
                && $round->updated_at->gt($round->created_at->copy()->addSeconds(2))
                && $round->isEditable()
            ) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_updated',
                    actor: $actor,
                    at: $round->updated_at,
                    detail: $round->title,
                    subject: $subject,
                    href: $href,
                ));
            }
        }

        return $events;
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function recruitmentEvents(User $company, int $limit): Collection
    {
        if (! $company->isCompanyOwner()) {
            return collect();
        }

        return RecruitmentRequest::query()
            ->where('company_user_id', $company->id)
            ->whereNotNull('status_updated_at')
            ->latest('status_updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (RecruitmentRequest $request) => $this->activityItem(
                type: 'recruitment_status',
                actor: __('talenma.dashboard.company.activity.team_actor'),
                at: $request->status_updated_at,
                subject: \Illuminate\Support\Str::limit((string) ($request->subject ?: '—'), 60),
                result: $request->statusLabel(),
                href: null,
            ));
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function jobApplicationEvents(User $company, int $limit): Collection
    {
        $org = $company->companyOrganization();

        if (! $org) {
            return collect();
        }

        return JobApplication::query()
            ->whereHas('jobPosting', fn ($query) => $query->where('company_profile_id', $org->id))
            ->with(['talent', 'jobPosting'])
            ->latest('submitted_at')
            ->limit($limit)
            ->get()
            ->map(function (JobApplication $application) {
                $talentName = $application->talent?->name
                    ?: __('talenma.dashboard.company.activity.unknown_talent');
                $jobTitle = $application->jobPosting?->title ?? '—';
                $href = $application->jobPosting
                    ? route('company.jobs.show', $application->jobPosting)
                    : null;

                return $this->activityItem(
                    type: 'job_application',
                    actor: $talentName,
                    at: $application->submitted_at ?? $application->created_at,
                    subject: $jobTitle,
                    href: $href,
                );
            });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\DirectHireRequest>  $query
     */
    private function scopeDirectHireQuery($query, User $company): void
    {
        $org = $company->companyOrganization();

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $company->id);
        }
    }

    /**
     * @return array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}
     */
    private function activityItem(
        string $type,
        string $actor,
        ?CarbonInterface $at,
        ?string $detail = null,
        ?string $subject = null,
        ?string $result = null,
        ?string $href = null,
    ): array {
        return [
            'type' => $type,
            'actor' => $actor,
            'detail' => $detail,
            'subject' => $subject,
            'result' => $result,
            'href' => $href,
            'at' => $at,
        ];
    }
}
