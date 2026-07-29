<?php

namespace App\Services;

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
