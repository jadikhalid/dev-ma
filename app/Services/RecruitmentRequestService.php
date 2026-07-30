<?php

namespace App\Services;

use App\Mail\RecruitmentRequestChatMessageMail;
use App\Mail\RecruitmentRequestStatusChangedMail;
use App\Mail\RecruitmentRequestSubmittedMail;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestMessage;
use App\Models\RecruitmentRequestStatusEvent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RecruitmentRequestService
{
    public function __construct(
        private MessagingService $messaging,
    ) {}

    public function recordSubmitted(RecruitmentRequest $request, ?User $actor = null, ?Carbon $at = null): RecruitmentRequestStatusEvent
    {
        return $request->statusEvents()->create([
            'event' => RecruitmentRequestStatusEvent::EVENT_SUBMITTED,
            'status' => RecruitmentRequest::STATUS_PENDING,
            'actor_user_id' => $actor?->id ?? $request->company_user_id,
            'created_at' => $at ?? $request->created_at ?? now(),
        ]);
    }

    public function recordStatusChange(
        RecruitmentRequest $request,
        string $status,
        ?User $actor = null,
        ?Carbon $at = null,
        ?string $comment = null,
    ): RecruitmentRequestStatusEvent {
        $status = $request->normalizeStatus($status);

        return $request->statusEvents()->create([
            'event' => RecruitmentRequestStatusEvent::EVENT_STATUS_CHANGED,
            'status' => $status,
            'comment' => filled($comment) ? $comment : null,
            'actor_user_id' => $actor?->id,
            'created_at' => $at ?? now(),
        ]);
    }

    public function recordCommentUpdate(
        RecruitmentRequest $request,
        ?User $actor = null,
        ?Carbon $at = null,
        ?string $comment = null,
    ): RecruitmentRequestStatusEvent {
        return $request->statusEvents()->create([
            'event' => RecruitmentRequestStatusEvent::EVENT_COMMENT_UPDATED,
            'status' => $request->normalizeStatus(),
            'comment' => filled($comment) ? $comment : null,
            'actor_user_id' => $actor?->id,
            'created_at' => $at ?? now(),
        ]);
    }

    public function notifySubmitted(RecruitmentRequest $request): void
    {
        $request->loadMissing(['talent', 'company']);

        try {
            $admin = $this->messaging->resolveAdminRecipient();

            if (filled($admin->email)) {
                Mail::to($admin->email)->send(new RecruitmentRequestSubmittedMail($request));
            }
        } catch (\Throwable) {
            // Never block the request on mail / missing-admin failures.
        }
    }

    public function notifyStatusOrComment(
        RecruitmentRequest $request,
        bool $statusChanged,
        bool $commentChanged,
        ?User $actor = null,
    ): void {
        if (! $statusChanged && ! $commentChanged) {
            return;
        }

        $request->loadMissing('company');

        if ($statusChanged) {
            $this->recordStatusChange(
                $request,
                $request->status,
                $actor,
                comment: $commentChanged ? $request->admin_comment : null,
            );
        } elseif ($commentChanged) {
            $this->recordCommentUpdate(
                $request,
                $actor,
                comment: $request->admin_comment,
            );
        }

        $this->flagUnseenForCompany($request);

        $company = $request->company;

        if (! $company || ! filled($company->email)) {
            return;
        }

        try {
            Mail::to($company->email)->send(new RecruitmentRequestStatusChangedMail(
                $request,
                $request->status,
                commentOnly: ! $statusChanged && $commentChanged,
            ));
        } catch (\Throwable) {
            // Never block the process on mail failures.
        }
    }

    public function postMessage(RecruitmentRequest $request, User $sender, string $body): RecruitmentRequestMessage
    {
        abort_unless($request->canAccess($sender), 403);
        abort_unless($request->allowsChat(), 403);

        $body = trim($body);

        if (mb_strlen($body) < 2) {
            throw ValidationException::withMessages([
                'body' => __('talenma.recruitment.chat_body_min'),
            ]);
        }

        $message = $request->messages()->create([
            'sender_user_id' => $sender->id,
            'body' => $body,
        ]);

        if ($sender->isCompany()) {
            $this->markSeenForCompany($sender, $request);
        } elseif ($sender->isStaff()) {
            $this->flagUnseenForCompany($request);
        }

        $this->notifyChatRecipient($request->fresh(['company', 'talent']), $message, $sender);

        return $message;
    }

    public function companyHasUnseenChanges(User $company): bool
    {
        if (! $company->isCompany()) {
            return false;
        }

        return RecruitmentRequest::query()
            ->where('company_user_id', $company->id)
            ->where(function ($inner) {
                $inner->whereNull('company_seen_at')
                    ->orWhereColumn('company_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function markSeenForCompany(User $company, RecruitmentRequest $request): void
    {
        abort_unless($request->canAccess($company) && $company->isCompany(), 403);

        RecruitmentRequest::withoutTimestamps(function () use ($request) {
            RecruitmentRequest::query()
                ->whereKey($request->id)
                ->update(['company_seen_at' => now()]);
        });

        $request->company_seen_at = now();
    }

    public function flagUnseenForCompany(RecruitmentRequest $request): void
    {
        RecruitmentRequest::withoutTimestamps(function () use ($request) {
            RecruitmentRequest::query()
                ->whereKey($request->id)
                ->update(['company_seen_at' => null]);
        });

        $request->company_seen_at = null;
        $request->touch();
    }

    /**
     * Talent IDs blocked for a new named intermediation (open, in progress, or closed successfully).
     *
     * @return list<int>
     */
    public function blockedNamedTalentIdsForCompany(User $company): array
    {
        if (! $company->isCompany()) {
            return [];
        }

        return RecruitmentRequest::query()
            ->whereIn('company_user_id', $this->companyActorIds($company))
            ->where('mode', RecruitmentRequest::MODE_NAMED)
            ->whereNotNull('developer_user_id')
            ->whereIn('status', RecruitmentRequest::namedBlockingStatuses())
            ->pluck('developer_user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function existingNamedRequestForCompanyTalent(User $company, User $talent): ?RecruitmentRequest
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return null;
        }

        return RecruitmentRequest::query()
            ->whereIn('company_user_id', $this->companyActorIds($company))
            ->where('mode', RecruitmentRequest::MODE_NAMED)
            ->where('developer_user_id', $talent->id)
            ->whereIn('status', RecruitmentRequest::namedBlockingStatuses())
            ->latest()
            ->first();
    }

    public function companyCanRequestNamedForTalent(User $company, User $talent): bool
    {
        return $this->existingNamedRequestForCompanyTalent($company, $talent) === null;
    }

    public function namedRequestDisabledHint(User $company, User $talent): ?string
    {
        $existing = $this->existingNamedRequestForCompanyTalent($company, $talent);

        if (! $existing) {
            return null;
        }

        if ($existing->isClosedSuccessful()) {
            return __('talenma.recruitment.named_blocked_closed_successful');
        }

        return __('talenma.recruitment.named_blocked_open');
    }

    /**
     * @return list<int>
     */
    private function companyActorIds(User $company): array
    {
        $ids = [(int) $company->id];
        $org = $company->companyOrganization();

        if ($org && filled($org->user_id)) {
            $ids[] = (int) $org->user_id;
        }

        return array_values(array_unique($ids));
    }

    private function notifyChatRecipient(
        RecruitmentRequest $request,
        RecruitmentRequestMessage $message,
        User $sender,
    ): void {
        $request->loadMissing('company');
        $recipientIsCompany = $sender->isStaff();

        try {
            if ($recipientIsCompany) {
                $recipient = $request->company;

                if (! $recipient || ! filled($recipient->email)) {
                    return;
                }

                Mail::to($recipient->email)->send(new RecruitmentRequestChatMessageMail(
                    $request,
                    $message,
                    $sender,
                    $recipient,
                    true,
                ));

                return;
            }

            $admin = $this->messaging->resolveAdminRecipient();

            if (! filled($admin->email)) {
                return;
            }

            Mail::to($admin->email)->send(new RecruitmentRequestChatMessageMail(
                $request,
                $message,
                $sender,
                $admin,
                false,
            ));
        } catch (\Throwable) {
            // Never block chat on mail failures.
        }
    }
}
