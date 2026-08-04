<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\JobPostingActivityEvent;
use App\Models\Message;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestMessage;
use App\Models\RecruitmentRequestStatusEvent;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class CompanyDashboardActivityService
{
    public function __construct(
        private DirectHireService $directHires,
        private MessagingService $messaging,
    ) {}

    /**
     * @return list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    public function recentActivity(User $company, int $limit = 10): array
    {
        if (! $company->isCompany()) {
            return [];
        }

        $fetch = max($limit * 2, 20);

        return $this->directHireEvents($company, $fetch)
            ->concat($this->roundEvents($company, $fetch))
            ->concat($this->recruitmentEvents($company, $fetch))
            ->concat($this->talentUnlockEvents($company, $fetch))
            ->concat($this->jobPostingActivityEvents($company, $fetch))
            ->concat($this->inboxMessageEvents($company, $fetch))
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function inboxMessageEvents(User $company, int $limit): Collection
    {
        $excluded = $this->messaging->directHireConversationIdsFor($company);

        return Message::query()
            ->whereHas('conversation', function ($query) use ($company, $excluded) {
                $query->where('channel', Conversation::CHANNEL_TALENT)
                    ->where('company_user_id', $company->id)
                    ->when($excluded !== [], fn ($q) => $q->whereNotIn('id', $excluded));
            })
            ->with(['conversation.talent.profile', 'sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Message $message) use ($company) {
                $conversation = $message->conversation;
                $isCompanyMessage = (int) $message->sender_user_id === (int) $company->id
                    || ($message->sender?->isCompany() ?? false);

                $talent = $conversation?->talent;
                $talentName = $talent?->publicDisplayName()
                    ?: ($talent?->name ?: __('talenma.dashboard.company.activity.unknown_talent'));

                return $this->activityItem(
                    type: $isCompanyMessage ? 'inbox_message_sent' : 'inbox_message',
                    actor: $talentName,
                    at: $message->created_at,
                    subject: $conversation?->subject,
                    href: $conversation ? route('inbox.show', $conversation) : route('inbox.index'),
                    self: $isCompanyMessage,
                );
            });
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

            if ($request->company_deferral_responded_at && $request->status === DirectHireRequest::STATUS_DEFERRED) {
                $events->push($this->activityItem(
                    type: 'direct_hire_deferral_accepted',
                    actor: $actor,
                    at: $request->company_deferral_responded_at,
                    subject: $subject,
                    href: $href,
                ));
            }

            if ($request->closed_at && in_array($request->status, [
                DirectHireRequest::STATUS_HIRED,
                DirectHireRequest::STATUS_CLOSED_NEGATIVE,
                DirectHireRequest::STATUS_WITHDRAWN,
            ], true)) {
                $type = match ($request->status) {
                    DirectHireRequest::STATUS_HIRED => 'direct_hire_hired',
                    DirectHireRequest::STATUS_WITHDRAWN => 'direct_hire_withdrawn',
                    default => 'direct_hire_closed_negative',
                };

                $events->push($this->activityItem(
                    type: $type,
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

            if (! $request || ! $message->sender_user_id) {
                continue;
            }

            $fromTalent = (int) $message->sender_user_id === (int) $request->talent_user_id;
            $actor = $request->talentDisplayName()
                ?: __('talenma.dashboard.company.activity.unknown_talent');

            $events->push($this->activityItem(
                type: $fromTalent ? 'direct_hire_message' : 'direct_hire_message_sent',
                actor: $actor,
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
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function recruitmentEvents(User $company, int $limit): Collection
    {
        $teamActor = __('talenma.dashboard.company.activity.team_actor');

        $statusEvents = RecruitmentRequestStatusEvent::query()
            ->whereHas('request', fn ($query) => $query->where('company_user_id', $company->id))
            ->with(['request.talent'])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function (RecruitmentRequestStatusEvent $event) use ($teamActor) {
                $request = $event->request;

                if (! $request) {
                    return null;
                }

                $subject = $request->isNamed() && filled($request->talent?->name)
                    ? $request->talent->name
                    : \Illuminate\Support\Str::limit((string) ($request->subject ?: '—'), 60);
                $href = route('sourcing.show', $request);

                if ($event->event === RecruitmentRequestStatusEvent::EVENT_SUBMITTED) {
                    return $this->activityItem(
                        type: 'recruitment_submitted',
                        actor: $teamActor,
                        at: $event->created_at,
                        subject: $subject,
                        detail: $request->mode,
                        href: $href,
                        self: true,
                    );
                }

                if ($event->event === RecruitmentRequestStatusEvent::EVENT_COMMENT_UPDATED) {
                    return $this->activityItem(
                        type: 'recruitment_comment',
                        actor: $teamActor,
                        at: $event->created_at,
                        subject: $subject,
                        detail: $request->mode,
                        href: $href,
                    );
                }

                return $this->activityItem(
                    type: 'recruitment_status',
                    actor: $teamActor,
                    at: $event->created_at,
                    subject: $subject,
                    detail: $request->mode,
                    result: $event->status,
                    href: $href,
                );
            })
            ->filter()
            ->values();

        $messageEvents = RecruitmentRequestMessage::query()
            ->whereHas('request', fn ($query) => $query->where('company_user_id', $company->id))
            ->with(['request.talent', 'sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (RecruitmentRequestMessage $message) use ($company, $teamActor) {
                $request = $message->request;

                if (! $request || ! $message->sender_user_id) {
                    return null;
                }

                $subject = $request->isNamed() && filled($request->talent?->name)
                    ? $request->talent->name
                    : \Illuminate\Support\Str::limit((string) ($request->subject ?: '—'), 60);
                $fromCompany = (int) $message->sender_user_id === (int) $company->id
                    || ($message->sender?->isCompany() ?? false);

                return $this->activityItem(
                    type: $fromCompany ? 'recruitment_message_sent' : 'recruitment_message',
                    actor: $fromCompany ? $teamActor : $teamActor,
                    at: $message->created_at,
                    subject: $subject,
                    detail: $request->mode,
                    href: route('sourcing.show', $request).'#sourcing-chat',
                    self: $fromCompany,
                );
            })
            ->filter()
            ->values();

        return $statusEvents->concat($messageEvents);
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function talentUnlockEvents(User $company, int $limit): Collection
    {
        $events = collect();

        $events = collect();

        RecruitmentRequest::query()
            ->where('company_user_id', $company->id)
            ->where('mode', RecruitmentRequest::MODE_NAMED)
            ->whereNotNull('developer_user_id')
            ->whereNotNull('talent_unlocked_at')
            ->with('talent')
            ->latest('talent_unlocked_at')
            ->limit($limit)
            ->get()
            ->each(function (RecruitmentRequest $request) use ($events) {
                $events->push($this->activityItem(
                    type: 'talent_unlocked',
                    actor: $request->talent?->name
                        ?: __('talenma.dashboard.company.activity.unknown_talent'),
                    at: $request->talent_unlocked_at,
                    subject: $request->talent?->name,
                    detail: 'named',
                    href: route('sourcing.show', $request),
                    self: true,
                ));
            });

        $this->directHires->queryForCompany($company)
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->whereNotNull('talent_user_id')
            ->whereNotNull('talent_unlocked_at')
            ->with('talent')
            ->latest('talent_unlocked_at')
            ->limit($limit)
            ->get()
            ->each(function (DirectHireRequest $request) use ($events) {
                $events->push($this->activityItem(
                    type: 'talent_unlocked',
                    actor: $request->talentDisplayName()
                        ?: __('talenma.dashboard.company.activity.unknown_talent'),
                    at: $request->talent_unlocked_at,
                    subject: $request->shortSubject(),
                    detail: 'direct_hire',
                    href: route('company.direct-hire.show', $request),
                    self: true,
                ));
            });

        return $events;
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function jobPostingActivityEvents(User $company, int $limit): Collection
    {
        $org = $company->companyOrganization();

        if (! $org) {
            return collect();
        }

        return JobPostingActivityEvent::query()
            ->where('company_profile_id', $org->id)
            ->where(function ($query) use ($company) {
                $query->where('actor_user_id', $company->id)
                    ->orWhereHas(
                        'jobPosting',
                        fn ($job) => $job->where('created_by', $company->id)
                    );
            })
            ->whereIn('event', [
                JobPostingActivityEvent::EVENT_CREATED,
                JobPostingActivityEvent::EVENT_PUBLISHED,
                JobPostingActivityEvent::EVENT_CLOSED,
                JobPostingActivityEvent::EVENT_HIDDEN,
                JobPostingActivityEvent::EVENT_POSTPONED,
                JobPostingActivityEvent::EVENT_DELETED,
                JobPostingActivityEvent::EVENT_APPLICATION_STATUS,
            ])
            // Per-applicant deleted copies are for the talent feed only.
            ->where(function ($query) {
                $query->whereNull('talent_user_id')
                    ->orWhere('event', JobPostingActivityEvent::EVENT_APPLICATION_STATUS);
            })
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function (JobPostingActivityEvent $event) {
                $type = match ($event->event) {
                    JobPostingActivityEvent::EVENT_CREATED => 'job_created',
                    JobPostingActivityEvent::EVENT_PUBLISHED => 'job_published',
                    JobPostingActivityEvent::EVENT_CLOSED => 'job_closed',
                    JobPostingActivityEvent::EVENT_HIDDEN => 'job_hidden',
                    JobPostingActivityEvent::EVENT_POSTPONED => 'job_postponed',
                    JobPostingActivityEvent::EVENT_DELETED => 'job_deleted',
                    JobPostingActivityEvent::EVENT_APPLICATION_STATUS => 'job_application_status',
                    default => null,
                };

                if ($type === null) {
                    return null;
                }

                $href = $event->job_posting_id
                    ? route('company.jobs.show', $event->job_posting_id)
                    : route('company.jobs.index');

                $result = null;

                if ($event->event === JobPostingActivityEvent::EVENT_APPLICATION_STATUS && filled($event->status)) {
                    $result = __('talenma.jobs.application_status_'.$event->status);
                }

                return $this->activityItem(
                    type: $type,
                    actor: $event->actor_label
                        ?: __('talenma.dashboard.company.activity.team_actor'),
                    at: $event->created_at,
                    subject: $event->job_title,
                    result: $result,
                    href: $href,
                    self: (bool) $event->is_self,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\DirectHireRequest>  $query
     */
    private function scopeDirectHireQuery($query, User $company): void
    {
        $query->where('hire_origin', \App\Models\DirectHireRequest::ORIGIN_COMPANY)
            ->where('company_user_id', $company->id);
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
        bool $self = false,
    ): array {
        return [
            'type' => $type,
            'actor' => $actor,
            'detail' => $detail,
            'subject' => $subject,
            'result' => $result,
            'href' => $href,
            'at' => $at,
            'self' => $self,
        ];
    }
}
