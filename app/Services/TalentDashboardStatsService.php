<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\JobApplication;
use App\Models\JobPostingActivityEvent;
use App\Models\Message;
use App\Models\ProfileDocumentDownload;
use App\Models\ProfileView;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TalentDashboardStatsService
{
    public function __construct(
        private MessagingService $messaging,
    ) {}

    /**
     * @return array{
     *     profile_views_7d: int,
     *     profile_views_total: int,
     *     cv_downloads_7d: int,
     *     unread_messages: int,
     *     recent_activity: list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     * }
     */
    public function build(User $talent): array
    {
        $since = now()->subDays(7);

        $views7d = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        $viewsTotal = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->count();

        $downloads7d = ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        return [
            'profile_views_7d' => $views7d,
            'profile_views_total' => $viewsTotal,
            'cv_downloads_7d' => $downloads7d,
            'unread_messages' => $this->messaging->unreadCountFor($talent),
            'recent_activity' => $this->recentActivity($talent),
        ];
    }

    /**
     * @return list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function recentActivity(User $talent, int $limit = 10): array
    {
        $fetch = max($limit * 2, 20);

        return $this->profileViewEvents($talent, $fetch)
            ->concat($this->cvDownloadEvents($talent, $fetch))
            ->concat($this->directHireEvents($talent, $fetch))
            ->concat($this->talentUnlockEvents($talent, $fetch))
            ->concat($this->inboxMessageEvents($talent, $fetch))
            ->concat($this->jobApplicationEvents($talent, $fetch))
            ->concat($this->jobPostingActivityEvents($talent, $fetch))
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function jobApplicationEvents(User $talent, int $limit): Collection
    {
        return JobApplication::query()
            ->where('talent_user_id', $talent->id)
            ->with(['jobPosting.companyProfile'])
            ->latest('submitted_at')
            ->limit($limit)
            ->get()
            ->map(function (JobApplication $application) {
                $job = $application->jobPosting;
                $actor = $job?->companyProfile?->displayName()
                    ?: __('talenma.dashboard.talent.stats.unknown_actor');

                return $this->activityItem(
                    type: 'job_application_submitted',
                    actor: $actor,
                    at: $application->submitted_at ?? $application->created_at,
                    subject: $job?->title,
                    href: $job ? route('talent.jobs.show', $job) : route('talent.jobs.index'),
                    self: true,
                );
            });
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}>
     */
    private function jobPostingActivityEvents(User $talent, int $limit): Collection
    {
        $appliedJobIds = JobApplication::query()
            ->where('talent_user_id', $talent->id)
            ->pluck('job_posting_id')
            ->all();

        return JobPostingActivityEvent::query()
            ->where(function ($query) use ($talent, $appliedJobIds) {
                $query->where('talent_user_id', $talent->id);

                if ($appliedJobIds !== []) {
                    $query->orWhere(function ($inner) use ($appliedJobIds) {
                        $inner->whereNull('talent_user_id')
                            ->whereIn('job_posting_id', $appliedJobIds)
                            ->whereIn('event', [
                                JobPostingActivityEvent::EVENT_CLOSED,
                                JobPostingActivityEvent::EVENT_HIDDEN,
                                JobPostingActivityEvent::EVENT_POSTPONED,
                                JobPostingActivityEvent::EVENT_DELETED,
                            ]);
                    });
                }
            })
            ->whereIn('event', [
                JobPostingActivityEvent::EVENT_CLOSED,
                JobPostingActivityEvent::EVENT_HIDDEN,
                JobPostingActivityEvent::EVENT_POSTPONED,
                JobPostingActivityEvent::EVENT_DELETED,
                JobPostingActivityEvent::EVENT_APPLICATION_STATUS,
            ])
            ->with(['jobPosting.companyProfile', 'companyProfile'])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function (JobPostingActivityEvent $event) {
                $type = match ($event->event) {
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

                $actor = $event->jobPosting?->companyProfile?->displayName()
                    ?: $event->companyProfile?->displayName()
                    ?: __('talenma.dashboard.talent.stats.unknown_actor');

                $href = $event->event === JobPostingActivityEvent::EVENT_DELETED || ! $event->job_posting_id
                    ? route('talent.jobs.index')
                    : route('talent.jobs.show', $event->job_posting_id);

                $result = null;

                if ($event->event === JobPostingActivityEvent::EVENT_APPLICATION_STATUS && filled($event->status)) {
                    $result = __('talenma.jobs.application_status_'.$event->status);
                }

                return $this->activityItem(
                    type: $type,
                    actor: $actor,
                    at: $event->created_at,
                    subject: $event->job_title,
                    result: $result,
                    href: $href,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function inboxMessageEvents(User $talent, int $limit): Collection
    {
        $excluded = $this->messaging->directHireConversationIdsFor($talent);

        return Message::query()
            ->whereHas('conversation', function ($query) use ($talent, $excluded) {
                $query->where('channel', Conversation::CHANNEL_TALENT)
                    ->where('talent_user_id', $talent->id)
                    ->when($excluded !== [], fn ($q) => $q->whereNotIn('id', $excluded));
            })
            ->with(['conversation.company', 'sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Message $message) use ($talent) {
                $conversation = $message->conversation;
                $sender = $message->sender;
                $isTalentMessage = (int) $message->sender_user_id === (int) $talent->id;

                if ($isTalentMessage) {
                    $actor = $conversation?->company
                        ? $this->messaging->companySenderActivityLabel($conversation->company)
                        : __('talenma.dashboard.talent.stats.unknown_actor');
                } else {
                    $actor = $sender && $sender->isCompany()
                        ? $this->messaging->companySenderActivityLabel($sender)
                        : ($this->actorName($sender));
                }

                return $this->activityItem(
                    type: $isTalentMessage ? 'inbox_message_sent' : 'inbox_message',
                    actor: $actor,
                    at: $message->created_at,
                    subject: $conversation?->subject,
                    href: $conversation ? route('inbox.show', $conversation) : route('inbox.index'),
                );
            });
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function profileViewEvents(User $talent, int $limit): Collection
    {
        return ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->with('viewer.companyProfile')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileView $view) => $this->activityItem(
                type: 'profile_view',
                actor: $this->actorName($view->viewer),
                at: $view->created_at,
            ));
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function cvDownloadEvents(User $talent, int $limit): Collection
    {
        return ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->with(['downloader.companyProfile', 'document'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileDocumentDownload $download) => $this->activityItem(
                type: 'cv_download',
                actor: $this->actorName($download->downloader),
                at: $download->created_at,
                detail: $download->document?->languageLabel(),
            ));
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function directHireEvents(User $talent, int $limit): Collection
    {
        $events = collect();

        $requests = DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->with(['companyProfile', 'company'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($requests as $request) {
            $actor = $request->talentFacingCompanyName()
                ?: $this->actorName($request->company);
            $subject = $request->shortSubject();
            $href = route('talent.direct-hire.show', $request);

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
                $decisionType = match ($request->status) {
                    DirectHireRequest::STATUS_IN_PROCESS,
                    DirectHireRequest::STATUS_HIRED,
                    DirectHireRequest::STATUS_CLOSED_NEGATIVE => 'direct_hire_accepted',
                    DirectHireRequest::STATUS_DECLINED => 'direct_hire_declined',
                    DirectHireRequest::STATUS_DEFERRED => 'direct_hire_deferred',
                    default => null,
                };

                if ($decisionType !== null) {
                    $events->push($this->activityItem(
                        type: $decisionType,
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

        $rounds = DirectHireRound::query()
            ->whereHas('request', fn ($query) => $query->where('talent_user_id', $talent->id))
            ->with(['request.companyProfile', 'request.company'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($rounds as $round) {
            $request = $round->request;

            if (! $request) {
                continue;
            }

            $actor = $request->talentFacingCompanyName()
                ?: $this->actorName($request->company);
            $subject = $request->shortSubject();
            $href = route('talent.direct-hire.show', $request);

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

            if ($round->isCancelled()) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_cancelled',
                    actor: $actor,
                    at: $round->updated_at ?? $round->completed_at ?? $round->created_at,
                    detail: $round->title,
                    subject: $subject,
                    href: $href,
                ));
            } elseif ($round->completed_at && in_array($round->status, DirectHireRound::outcomeStatuses(), true)) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_result',
                    actor: $actor,
                    at: $round->completed_at,
                    detail: $round->title,
                    subject: $subject,
                    result: $round->statusLabel(),
                    href: $href,
                ));
            }
        }

        $messages = DirectHireMessage::query()
            ->whereHas('request', fn ($query) => $query->where('talent_user_id', $talent->id))
            ->with(['request.companyProfile', 'request.company'])
            ->latest()
            ->limit($limit)
            ->get();

        foreach ($messages as $message) {
            $request = $message->request;

            if (! $request || ! $message->sender_user_id) {
                continue;
            }

            $actor = $request->talentFacingCompanyName() ?: $this->actorName($request->company);
            $isTalentMessage = (int) $message->sender_user_id === (int) $talent->id;

            $events->push($this->activityItem(
                type: $isTalentMessage ? 'direct_hire_message_sent' : 'direct_hire_message',
                actor: $actor,
                at: $message->created_at,
                subject: $request->shortSubject(),
                href: route('talent.direct-hire.show', $request),
            ));
        }

        return $events;
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function talentUnlockEvents(User $talent, int $limit): Collection
    {
        $events = collect();

        DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->whereNotNull('talent_unlocked_at')
            ->with(['companyProfile', 'company'])
            ->latest('talent_unlocked_at')
            ->limit($limit)
            ->get()
            ->each(function (DirectHireRequest $request) use ($events) {
                $events->push($this->activityItem(
                    type: 'talent_unlocked',
                    actor: $request->talentFacingCompanyName()
                        ?: $this->actorName($request->company),
                    at: $request->talent_unlocked_at,
                    subject: $request->shortSubject(),
                    detail: 'direct_hire',
                    href: route('talent.direct-hire.show', $request),
                ));
            });

        RecruitmentRequest::query()
            ->where('developer_user_id', $talent->id)
            ->where('mode', RecruitmentRequest::MODE_NAMED)
            ->whereNotNull('talent_unlocked_at')
            ->with(['company'])
            ->latest('talent_unlocked_at')
            ->limit($limit)
            ->get()
            ->each(function (RecruitmentRequest $request) use ($events) {
                $events->push($this->activityItem(
                    type: 'talent_unlocked',
                    actor: $request->companyDisplayName()
                        ?: $this->actorName($request->company),
                    at: $request->talent_unlocked_at,
                    subject: $request->talent?->name,
                    detail: 'named',
                    href: null,
                ));
            });

        return $events;
    }

    /**
     * @return array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self?: bool}
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
        $item = [
            'type' => $type,
            'actor' => $actor,
            'detail' => $detail,
            'subject' => $subject,
            'result' => $result,
            'href' => $href,
            'at' => $at,
        ];

        if ($self) {
            $item['self'] = true;
        }

        return $item;
    }

    private function actorName(?User $user): string
    {
        if (! $user) {
            return __('talenma.dashboard.talent.stats.unknown_actor');
        }

        $user->loadMissing('companyProfile');

        return $user->companyProfile?->displayName()
            ?: ($user->name ?: __('talenma.dashboard.talent.stats.unknown_actor'));
    }
}
