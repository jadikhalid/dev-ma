<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\Message;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestMessage;
use App\Models\RecruitmentRequestStatusEvent;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StaffRecruitmentActivityService
{
    /**
     * @return list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}>
     */
    public function recentActivity(User $staff, int $limit = 20): array
    {
        if (! $staff->isStaff()) {
            return [];
        }

        $fetch = max($limit * 2, 30);

        return $this->statusEvents($staff, $fetch)
            ->concat($this->messageEvents($staff, $fetch))
            ->concat($this->inboxMessageEvents($staff, $fetch))
            ->concat($this->directHireEvents($staff, $fetch))
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}>
     */
    private function statusEvents(User $staff, int $limit): Collection
    {
        return RecruitmentRequestStatusEvent::query()
            ->with(['request.company', 'request.talent', 'actor'])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function (RecruitmentRequestStatusEvent $event) use ($staff) {
                $request = $event->request;

                if (! $request) {
                    return null;
                }

                $companyName = $request->companyDisplayName();
                $subject = $this->subjectLabel($request);
                $href = route('admin.recruitment.show', $request);
                $isSelf = (int) $event->actor_user_id === (int) $staff->id;

                if ($event->event === RecruitmentRequestStatusEvent::EVENT_SUBMITTED) {
                    return $this->item(
                        type: 'recruitment_submitted',
                        actor: $companyName,
                        at: $event->created_at,
                        subject: $subject,
                        detail: $request->mode,
                        href: $href,
                        self: false,
                    );
                }

                if ($event->event === RecruitmentRequestStatusEvent::EVENT_COMMENT_UPDATED) {
                    return $this->item(
                        type: 'recruitment_comment',
                        actor: $companyName,
                        at: $event->created_at,
                        subject: $subject,
                        detail: $request->mode,
                        href: $href,
                        self: $isSelf,
                    );
                }

                return $this->item(
                    type: 'recruitment_status',
                    actor: $companyName,
                    at: $event->created_at,
                    subject: $subject,
                    detail: $request->mode,
                    result: $event->status,
                    href: $href,
                    self: $isSelf,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}>
     */
    private function messageEvents(User $staff, int $limit): Collection
    {
        return RecruitmentRequestMessage::query()
            ->with(['request.company', 'request.talent', 'sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (RecruitmentRequestMessage $message) use ($staff) {
                $request = $message->request;

                if (! $request || ! $message->sender_user_id) {
                    return null;
                }

                $companyName = $request->companyDisplayName();
                $fromStaff = $message->sender?->isStaff() ?? false;
                $isSelf = (int) $message->sender_user_id === (int) $staff->id;

                return $this->item(
                    type: $fromStaff ? 'recruitment_message_sent' : 'recruitment_message',
                    actor: $companyName,
                    at: $message->created_at,
                    subject: $this->subjectLabel($request),
                    detail: $request->mode,
                    href: route('admin.recruitment.show', $request).'#sourcing-chat',
                    self: $isSelf,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}>
     */
    private function inboxMessageEvents(User $staff, int $limit): Collection
    {
        return Message::query()
            ->whereHas('conversation', fn ($query) => $query->where('channel', Conversation::CHANNEL_STAFF))
            ->with(['conversation.company.companyProfile', 'sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Message $message) use ($staff) {
                $conversation = $message->conversation;

                if (! $conversation || ! $message->sender_user_id) {
                    return null;
                }

                $fromStaff = $message->sender?->isStaff() ?? false;
                $isSelf = (int) $message->sender_user_id === (int) $staff->id;
                $company = $conversation->company;
                $companyName = $company?->companyDisplayName()
                    ?: ($company?->name ?: '—');

                return $this->item(
                    type: $fromStaff ? 'inbox_message_sent' : 'inbox_message',
                    actor: $companyName,
                    at: $message->created_at,
                    subject: $conversation->subject,
                    href: route('inbox.show', $conversation),
                    self: $isSelf,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}>
     */
    private function directHireEvents(User $staff, int $limit): Collection
    {
        $events = collect();

        $requests = DirectHireRequest::query()
            ->whereIn('hire_origin', DirectHireRequest::staffHireOrigins())
            ->with(['talent', 'company', 'companyProfile', 'initiatedBy'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($requests as $request) {
            $href = route('admin.direct-hire.show', $request);
            $subject = $request->shortSubject();
            $talentName = $request->talentDisplayName();
            $detail = $request->hire_origin;

            if ($request->created_at) {
                $isSelf = (int) $request->initiated_by_user_id === (int) $staff->id;
                $events->push($this->item(
                    type: 'direct_hire_proposed',
                    actor: $talentName,
                    at: $request->created_at,
                    subject: $subject,
                    detail: $detail,
                    href: $href,
                    self: $isSelf,
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
                    $events->push($this->item(
                        type: $decisionType,
                        actor: $talentName,
                        at: $request->talent_decision_at,
                        subject: $subject,
                        detail: $detail,
                        href: $href,
                        self: false,
                    ));
                }
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
                $isSelf = (int) $request->closed_by === (int) $staff->id;

                $events->push($this->item(
                    type: $type,
                    actor: $talentName,
                    at: $request->closed_at,
                    subject: $subject,
                    detail: $detail,
                    href: $href,
                    self: $isSelf,
                ));
            }
        }

        $messages = DirectHireMessage::query()
            ->whereHas('request', fn ($query) => $query->whereIn('hire_origin', DirectHireRequest::staffHireOrigins()))
            ->with(['request.talent', 'request.company', 'request.companyProfile', 'sender'])
            ->latest()
            ->limit($limit)
            ->get();

        foreach ($messages as $message) {
            $request = $message->request;

            if (! $request || ! $message->sender_user_id) {
                continue;
            }

            $fromStaff = $message->sender?->isStaff() ?? false;
            $isSelf = (int) $message->sender_user_id === (int) $staff->id;
            $actor = $fromStaff
                ? ($message->sender?->name ?: __('talenma.direct_hire.platform_employer_name'))
                : ($message->sender?->isCompany()
                    ? $request->companyDisplayName()
                    : $request->talentDisplayName());

            $events->push($this->item(
                type: $fromStaff ? 'direct_hire_message_sent' : 'direct_hire_message',
                actor: $actor,
                at: $message->created_at,
                subject: $request->shortSubject(),
                detail: $request->hire_origin,
                href: route('admin.direct-hire.show', $request).'#direct-hire-chat',
                self: $isSelf,
            ));
        }

        $rounds = DirectHireRound::query()
            ->whereHas('request', fn ($query) => $query->whereIn('hire_origin', DirectHireRequest::staffHireOrigins()))
            ->with(['request.talent', 'request.company', 'request.companyProfile'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($rounds as $round) {
            $request = $round->request;

            if (! $request) {
                continue;
            }

            $href = route('admin.direct-hire.show', $request);
            $subject = $request->shortSubject();
            $talentName = $request->talentDisplayName();

            if ($round->created_at) {
                $events->push($this->item(
                    type: 'direct_hire_round_added',
                    actor: $talentName,
                    at: $round->created_at,
                    subject: $subject,
                    detail: $round->title,
                    href: $href,
                    self: false,
                ));
            }

            if ($round->isCancelled()) {
                $events->push($this->item(
                    type: 'direct_hire_round_cancelled',
                    actor: $talentName,
                    at: $round->updated_at ?? $round->completed_at ?? $round->created_at,
                    subject: $subject,
                    detail: $round->title,
                    href: $href,
                    self: false,
                ));
            } elseif ($round->completed_at && in_array($round->status, DirectHireRound::outcomeStatuses(), true)) {
                $events->push($this->item(
                    type: 'direct_hire_round_result',
                    actor: $talentName,
                    at: $round->completed_at,
                    subject: $subject,
                    detail: $round->title,
                    result: $round->statusLabel(),
                    href: $href,
                    self: false,
                ));
            }
        }

        return $events->filter()->values();
    }

    private function subjectLabel(RecruitmentRequest $request): string
    {
        if ($request->isNamed() && filled($request->talent?->name)) {
            return $request->talent->name;
        }

        return Str::limit((string) ($request->subject ?: '—'), 60);
    }

    /**
     * @return array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface, self: bool}
     */
    private function item(
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
