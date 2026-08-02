<?php

namespace App\Services;

use App\Mail\DirectHireChatMessageMail;
use App\Mail\DirectHireClosedMail;
use App\Mail\DirectHireDecisionMail;
use App\Mail\DirectHireDeferralAcknowledgedMail;
use App\Mail\DirectHireProposalMail;
use App\Mail\DirectHireRoundCancelledMail;
use App\Mail\DirectHireRoundChangedMail;
use App\Mail\DirectHireWithdrawnMail;
use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\DirectHireStatusEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DirectHireService
{
    public function create(User $company, User $talent, string $subject, string $message): DirectHireRequest
    {
        if (! $company->isCompany()) {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_company_only'),
            ]);
        }

        if (! $talent->isTalent() || $talent->approval_status !== 'approved') {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_talent_invalid'),
            ]);
        }

        $org = $company->companyOrganization();

        if ($this->companyHasHiredTalent($company, $talent)) {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_already_hired'),
            ]);
        }

        if ($this->companyHasOpenRequest($company)) {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_process_open'),
            ]);
        }

        $request = DB::transaction(function () use ($company, $talent, $subject, $message, $org) {
            $request = DirectHireRequest::create([
                'company_user_id' => $company->id,
                'talent_user_id' => $talent->id,
                'talent_name_snapshot' => $talent->name,
                'company_profile_id' => $org?->id,
                'company_name_snapshot' => $org?->displayName() ?: $company->name,
                'hire_origin' => DirectHireRequest::ORIGIN_COMPANY,
                'initiated_by_user_id' => null,
                'subject' => $subject,
                'message' => $message,
                'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
                'conversation_id' => null,
                'company_seen_at' => now(),
            ]);

            $this->recordStatusEvent(
                $request,
                DirectHireStatusEvent::EVENT_PROPOSED,
                DirectHireRequest::STATUS_PENDING_RESPONSE,
                $company,
                null,
                $request->created_at,
            );

            return $request;
        });

        $request->load(['company', 'companyProfile', 'talent']);

        try {
            Mail::to($talent->email)->send(new DirectHireProposalMail($request));
        } catch (\Throwable) {
            // Never block the hire process on mail failures (e.g. Mailpit down).
        }

        return $request;
    }

    public function decide(DirectHireRequest $request, User $talent, string $decision, ?string $note = null): DirectHireRequest
    {
        if ($request->talent_user_id !== $talent->id) {
            abort(403);
        }

        if (! in_array($request->status, [
            DirectHireRequest::STATUS_PENDING_RESPONSE,
            DirectHireRequest::STATUS_DEFERRED,
        ], true)) {
            throw ValidationException::withMessages([
                'decision' => __('talenma.direct_hire.error_decision_locked'),
            ]);
        }

        if (! in_array($decision, DirectHireRequest::talentDecisions(), true)) {
            throw ValidationException::withMessages([
                'decision' => __('talenma.direct_hire.error_decision_invalid'),
            ]);
        }

        if (
            $decision === DirectHireRequest::DECISION_DEFER
            && $request->status === DirectHireRequest::STATUS_DEFERRED
        ) {
            throw ValidationException::withMessages([
                'decision' => __('talenma.direct_hire.error_already_deferred'),
            ]);
        }

        $newStatus = match ($decision) {
            DirectHireRequest::DECISION_ACCEPT => DirectHireRequest::STATUS_IN_PROCESS,
            DirectHireRequest::DECISION_DECLINE => DirectHireRequest::STATUS_DECLINED,
            DirectHireRequest::DECISION_DEFER => DirectHireRequest::STATUS_DEFERRED,
        };

        $now = now();

        DirectHireRequest::withoutTimestamps(function () use ($request, $newStatus, $talent, $note, $now) {
            $payload = [
                'status' => $newStatus,
                'talent_decision_at' => $now,
                'talent_decision_note' => filled($note) ? trim($note) : null,
                'talent_seen_at' => $now,
                'staff_seen_at' => null,
                'closed_at' => $newStatus === DirectHireRequest::STATUS_DECLINED ? $now : null,
                'closed_by' => $newStatus === DirectHireRequest::STATUS_DECLINED ? $talent->id : null,
                // closure_note is reserved for company close messages — do not mirror talent notes.
                'closure_note' => $newStatus === DirectHireRequest::STATUS_DECLINED
                    ? null
                    : $request->closure_note,
                'updated_at' => $now,
            ];

            // Staff-on-behalf dossiers must never light up the beneficiary company account.
            if (! $request->isStaffInitiated()) {
                $payload['company_seen_at'] = null;
            }

            $request->update($payload);
        });

        $this->recordStatusEvent(
            $request,
            DirectHireStatusEvent::EVENT_TALENT_DECISION,
            $newStatus,
            $talent,
            filled($note) ? trim($note) : null,
            $now,
        );

        $request->refresh();
        $request->load(['company', 'companyProfile', 'talent', 'initiatedBy']);

        try {
            $hiringRecipient = $this->hiringSideMailRecipient($request);

            if (filled($hiringRecipient?->email)) {
                Mail::to($hiringRecipient->email)->send(new DirectHireDecisionMail($request, $decision));
            }
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }

        return $request;
    }

    public function createByStaff(
        User $staff,
        User $talent,
        string $subject,
        string $message,
        string $origin,
        ?User $beneficiaryCompany = null,
    ): DirectHireRequest {
        if (! $staff->isStaff()) {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_staff_only'),
            ]);
        }

        if (! in_array($origin, DirectHireRequest::staffHireOrigins(), true)) {
            throw ValidationException::withMessages([
                'hire_origin' => __('talenma.direct_hire.error_origin_invalid'),
            ]);
        }

        if (! $talent->isTalent() || $talent->approval_status !== 'approved') {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_talent_invalid'),
            ]);
        }

        $companyUserId = null;
        $companyProfileId = null;
        $companyNameSnapshot = __('talenma.direct_hire.platform_employer_name');

        if ($origin === DirectHireRequest::ORIGIN_STAFF_ON_BEHALF) {
            if (! $beneficiaryCompany || ! $beneficiaryCompany->isCompany()) {
                throw ValidationException::withMessages([
                    'company_id' => __('talenma.direct_hire.error_beneficiary_required'),
                ]);
            }

            if ($beneficiaryCompany->approval_status !== 'approved') {
                throw ValidationException::withMessages([
                    'company_id' => __('talenma.direct_hire.error_beneficiary_invalid'),
                ]);
            }

            // Independent from the company account: no company open-slot / hire-lock / intermediation coupling.
            if ($this->staffHasOpenOnBehalfRequestFor($beneficiaryCompany, $talent)) {
                throw ValidationException::withMessages([
                    'talent_id' => __('talenma.direct_hire.error_process_open'),
                ]);
            }

            $org = $beneficiaryCompany->companyOrganization();
            $companyUserId = $beneficiaryCompany->id;
            $companyProfileId = $org?->id;
            $companyNameSnapshot = $org?->displayName() ?: $beneficiaryCompany->name;
        } else {
            if ($this->staffHasOpenInternalRequest()) {
                throw ValidationException::withMessages([
                    'talent_id' => __('talenma.direct_hire.error_staff_internal_open'),
                ]);
            }
        }

        $request = DB::transaction(function () use (
            $staff,
            $talent,
            $subject,
            $message,
            $origin,
            $companyUserId,
            $companyProfileId,
            $companyNameSnapshot,
        ) {
            $request = DirectHireRequest::create([
                'company_user_id' => $companyUserId,
                'talent_user_id' => $talent->id,
                'talent_name_snapshot' => $talent->name,
                'company_profile_id' => $companyProfileId,
                'company_name_snapshot' => $companyNameSnapshot,
                'hire_origin' => $origin,
                'initiated_by_user_id' => $staff->id,
                'subject' => $subject,
                'message' => $message,
                'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
                'conversation_id' => null,
                'company_seen_at' => null,
                'staff_seen_at' => now(),
            ]);

            $this->recordStatusEvent(
                $request,
                DirectHireStatusEvent::EVENT_PROPOSED,
                DirectHireRequest::STATUS_PENDING_RESPONSE,
                $staff,
                null,
                $request->created_at,
            );

            return $request;
        });

        $request->load(['company', 'companyProfile', 'talent', 'initiatedBy']);

        try {
            Mail::to($talent->email)->send(new DirectHireProposalMail($request));
        } catch (\Throwable) {
            // Never block the hire process on mail failures (e.g. Mailpit down).
        }

        return $request;
    }

    public function staffHasOpenInternalRequest(): bool
    {
        return DirectHireRequest::query()
            ->where('hire_origin', DirectHireRequest::ORIGIN_STAFF_INTERNAL)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->exists();
    }

    /**
     * Open staff-on-behalf dossier for the same beneficiary company + talent (admin-only capacity).
     */
    public function staffHasOpenOnBehalfRequestFor(User $beneficiaryCompany, User $talent): bool
    {
        if (! $beneficiaryCompany->isCompany() || ! $talent->isTalent()) {
            return false;
        }

        $org = $beneficiaryCompany->companyOrganization();
        $query = DirectHireRequest::query()
            ->where('hire_origin', DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)
            ->where('talent_user_id', $talent->id)
            ->whereIn('status', DirectHireRequest::openStatuses());

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $beneficiaryCompany->id);
        }

        return $query->exists();
    }

    /**
     * Hiring-side mailbox for staff-led dossiers (never the beneficiary company).
     */
    public function hiringSideMailRecipient(DirectHireRequest $request): ?User
    {
        if ($request->isStaffInitiated()) {
            $request->loadMissing('initiatedBy');

            return $request->initiatedBy;
        }

        $request->loadMissing('company');

        return $request->company;
    }

    /**
     * @return 'open'|'hired'|'intermediation_locked'|null
     */
    public function staffProposeBlockReason(User $talent, string $origin, ?User $beneficiaryCompany = null): ?string
    {
        if (! $talent->isTalent() || $talent->approval_status !== 'approved') {
            return 'open';
        }

        if ($origin === DirectHireRequest::ORIGIN_STAFF_INTERNAL) {
            return $this->staffHasOpenInternalRequest() ? 'open' : null;
        }

        if (! $beneficiaryCompany || ! $beneficiaryCompany->isCompany()) {
            return 'open';
        }

        return $this->staffHasOpenOnBehalfRequestFor($beneficiaryCompany, $talent) ? 'open' : null;
    }

    public function queryForStaff(): \Illuminate\Database\Eloquent\Builder
    {
        return DirectHireRequest::query()
            ->whereIn('hire_origin', DirectHireRequest::staffHireOrigins());
    }

    public function respondToDeferral(
        DirectHireRequest $request,
        User $actor,
        string $action,
        ?string $note = null,
    ): DirectHireRequest {
        $this->assertHiringSideCanManage($request, $actor);

        if (! $request->awaitsCompanyDeferralReply()) {
            throw ValidationException::withMessages([
                'action' => __('talenma.direct_hire.error_deferral_reply_locked'),
            ]);
        }

        if (! in_array($action, DirectHireRequest::companyDeferralActions(), true)) {
            throw ValidationException::withMessages([
                'action' => __('talenma.direct_hire.error_deferral_action_invalid'),
            ]);
        }

        if ($action === DirectHireRequest::DEFERRAL_REFUSE) {
            if (! filled($note)) {
                throw ValidationException::withMessages([
                    'note' => __('talenma.direct_hire.deferral_refuse_note_required'),
                ]);
            }

            return $this->withdraw($request, $actor, $note);
        }

        return $this->acknowledgeDeferral($request, $actor, $note);
    }

    public function acknowledgeDeferral(
        DirectHireRequest $request,
        User $actor,
        ?string $note = null,
    ): DirectHireRequest {
        $this->assertHiringSideCanManage($request, $actor);

        if (! $request->awaitsCompanyDeferralReply()) {
            throw ValidationException::withMessages([
                'action' => __('talenma.direct_hire.error_deferral_reply_locked'),
            ]);
        }

        $now = now();

        DirectHireRequest::withoutTimestamps(function () use ($request, $note, $now, $actor) {
            $request->update([
                'company_deferral_note' => filled($note) ? trim($note) : null,
                'company_deferral_responded_at' => $now,
                ...$this->hiringSideActionSeenFields($request, $actor, $now),
            ]);
        });

        $this->recordStatusEvent(
            $request,
            DirectHireStatusEvent::EVENT_DEFERRAL_ACKNOWLEDGED,
            DirectHireRequest::STATUS_DEFERRED,
            $actor,
            filled($note) ? trim($note) : null,
            $now,
        );

        $request->refresh()->loadMissing(['talent', 'company', 'companyProfile']);

        try {
            if (filled($request->talent?->email)) {
                Mail::to($request->talent->email)->send(new DirectHireDeferralAcknowledgedMail($request));
            }
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }

        return $request;
    }

    public function postMessage(DirectHireRequest $request, User $sender, string $body): DirectHireMessage
    {
        $this->assertCanChat($request, $sender);

        if (! $request->allowsChat()) {
            throw ValidationException::withMessages([
                'body' => __('talenma.direct_hire.error_chat_closed'),
            ]);
        }

        $message = $request->messages()->create([
            'sender_user_id' => $sender->id,
            'body' => trim($body),
        ]);

        $now = now();

        if ($sender->isStaff()) {
            $payload = [
                'updated_at' => $now,
                'staff_seen_at' => $now,
                'talent_seen_at' => null,
            ];
            $this->writeRequestTimestamps($request->id, $payload);
        } elseif ($sender->isCompany()) {
            $payload = [
                'updated_at' => $now,
                'company_seen_at' => $now,
                'talent_seen_at' => null,
            ];
            if ($request->isStaffInitiated()) {
                $payload['staff_seen_at'] = null;
            }
            $this->writeRequestTimestamps($request->id, $payload);
        } else {
            $payload = [
                'updated_at' => $now,
                'talent_seen_at' => $now,
                'staff_seen_at' => null,
            ];
            if (! $request->isStaffInitiated()) {
                $payload['company_seen_at'] = null;
            }
            $this->writeRequestTimestamps($request->id, $payload);
        }

        $request->refresh()->loadMissing(['talent', 'company', 'companyProfile', 'initiatedBy']);
        $this->notifyChatRecipient($request, $message, $sender);

        return $message;
    }

    /**
     * Backfill the initial company message for requests created before dedicated chat.
     */
    public function ensureThreadSeeded(DirectHireRequest $request): void
    {
        // No automatic seeding: the chat must contain only explicit conversation
        // messages (sent from the chat composer), not the initial proposal text.
    }

    public function addRound(
        DirectHireRequest $request,
        User $actor,
        string $title,
        string $scheduledAt,
        ?string $note = null,
        ?string $meetingUrl = null,
    ): DirectHireRound {
        $this->assertHiringSideCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'title' => __('talenma.direct_hire.error_rounds_locked'),
            ]);
        }

        $position = ((int) $request->rounds()->max('position')) + 1;

        $round = $request->rounds()->create([
            'position' => $position,
            'title' => $title,
            'status' => DirectHireRound::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt,
            'meeting_url' => filled($meetingUrl) ? trim($meetingUrl) : null,
            'company_note' => filled($note) ? trim($note) : null,
        ]);

        $this->markHiringSideChangeForTalent($request, $actor);
        $this->notifyTalentRoundChanged($request, $round->fresh(), 'created');

        return $round;
    }

    /**
     * Update round details and/or outcome. Cancelled rounds cannot be edited.
     *
     * @param  array{title?: string, scheduled_at?: string, meeting_url?: ?string, company_note?: ?string, status?: string}  $data
     */
    public function updateRound(DirectHireRound $round, User $actor, array $data): DirectHireRound
    {
        $request = $round->request;
        $this->assertHiringSideCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'title' => __('talenma.direct_hire.error_rounds_locked'),
            ]);
        }

        if ($round->isCancelled()) {
            throw ValidationException::withMessages([
                'title' => __('talenma.direct_hire.error_round_already_cancelled'),
            ]);
        }

        $detailKeys = ['title', 'scheduled_at', 'meeting_url', 'company_note'];
        $wantsDetailUpdate = collect($detailKeys)->contains(fn (string $key) => array_key_exists($key, $data));

        if ($wantsDetailUpdate && ! $round->isEditable()) {
            throw ValidationException::withMessages([
                'title' => __('talenma.direct_hire.error_round_not_editable'),
            ]);
        }

        $updates = [];

        if (array_key_exists('title', $data)) {
            $updates['title'] = trim((string) $data['title']);
        }

        if (array_key_exists('scheduled_at', $data)) {
            $updates['scheduled_at'] = $data['scheduled_at'];
        }

        if (array_key_exists('meeting_url', $data)) {
            $meetingUrl = $data['meeting_url'];
            $updates['meeting_url'] = filled($meetingUrl) ? trim((string) $meetingUrl) : null;
        }

        if (array_key_exists('company_note', $data)) {
            $note = $data['company_note'];
            $updates['company_note'] = filled($note) ? trim((string) $note) : null;
        }

        if (array_key_exists('status', $data)) {
            if (! $round->isEditable()) {
                throw ValidationException::withMessages([
                    'status' => __('talenma.direct_hire.error_round_result_locked'),
                ]);
            }

            $status = $data['status'];

            if ($status === DirectHireRound::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'status' => __('talenma.direct_hire.error_round_use_cancel'),
                ]);
            }

            if (! in_array($status, DirectHireRound::outcomeStatuses(), true)) {
                throw ValidationException::withMessages([
                    'status' => __('talenma.direct_hire.error_round_status_invalid'),
                ]);
            }

            $updates['status'] = $status;
            $updates['completed_at'] = in_array($status, DirectHireRound::completedStatuses(), true)
                ? ($round->completed_at ?? now())
                : null;
        }

        if ($updates === []) {
            return $round->fresh();
        }

        $round->update($updates);

        $this->markHiringSideChangeForTalent($request, $actor);

        $round = $round->fresh();
        $this->notifyTalentRoundChanged($request, $round, 'updated');

        return $round;
    }

    /**
     * Soft-cancel a scheduled round with a required reason.
     * Keeps the round and chat thread; notifies both parties.
     */
    public function cancelRound(DirectHireRound $round, User $actor, string $reason): DirectHireRound
    {
        $request = $round->request;
        $this->assertHiringSideCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'cancellation_reason' => __('talenma.direct_hire.error_rounds_locked'),
            ]);
        }

        if ($round->isCancelled()) {
            throw ValidationException::withMessages([
                'cancellation_reason' => __('talenma.direct_hire.error_round_already_cancelled'),
            ]);
        }

        if (! $round->isCancellable()) {
            throw ValidationException::withMessages([
                'cancellation_reason' => __('talenma.direct_hire.error_round_not_cancellable'),
            ]);
        }

        $reason = trim($reason);

        $round->update([
            'status' => DirectHireRound::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'completed_at' => now(),
        ]);

        $round = $round->fresh();

        $this->markHiringSideChangeForTalent($request, $actor);

        $request->loadMissing(['talent', 'company']);

        if (filled($request->talent?->email)) {
            try {
                Mail::to($request->talent->email)->send(
                    new DirectHireRoundCancelledMail($request, $round, 'talent')
                );
            } catch (\Throwable) {
                // Keep cancellation even if mail fails.
            }
        }

        $hiringRecipient = $this->hiringSideMailRecipient($request);
        if (filled($hiringRecipient?->email)) {
            try {
                Mail::to($hiringRecipient->email)->send(
                    new DirectHireRoundCancelledMail($request, $round, 'company')
                );
            } catch (\Throwable) {
                // Keep cancellation even if mail fails.
            }
        }

        return $round;
    }

    /**
     * Hiring-side action: bump activity for the talent (blue dots) while marking
     * the actor's side as seen and flagging the other hiring party when relevant.
     */
    private function markHiringSideChangeForTalent(DirectHireRequest $request, User $actor): void
    {
        $now = now();
        $payload = [
            'updated_at' => $now,
            'talent_seen_at' => null,
        ];

        if ($actor->isStaff()) {
            $payload['staff_seen_at'] = $now;
        } else {
            $payload['company_seen_at'] = $now;
            if ($request->isStaffInitiated()) {
                $payload['staff_seen_at'] = null;
            }
        }

        $this->writeRequestTimestamps($request->id, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function hiringSideActionSeenFields(DirectHireRequest $request, User $actor, $now): array
    {
        $fields = [
            'talent_seen_at' => null,
            'updated_at' => $now,
        ];

        if ($actor->isStaff()) {
            $fields['staff_seen_at'] = $now;
        } else {
            $fields['company_seen_at'] = $now;
            if ($request->isStaffInitiated()) {
                $fields['staff_seen_at'] = null;
            }
        }

        return $fields;
    }

    /**
     * @param  'created'|'updated'  $event
     */
    private function notifyTalentRoundChanged(
        DirectHireRequest $request,
        DirectHireRound $round,
        string $event,
    ): void {
        $request->loadMissing(['talent', 'company', 'companyProfile']);

        if (! filled($request->talent?->email)) {
            return;
        }

        try {
            Mail::to($request->talent->email)->send(
                new DirectHireRoundChangedMail($request, $round, $event)
            );
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }
    }

    private function notifyTalentClosed(DirectHireRequest $request): void
    {
        if (! filled($request->talent?->email)) {
            return;
        }

        try {
            Mail::to($request->talent->email)->send(new DirectHireClosedMail($request));
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }
    }

    private function notifyTalentWithdrawn(DirectHireRequest $request): void
    {
        if (! filled($request->talent?->email)) {
            return;
        }

        try {
            Mail::to($request->talent->email)->send(new DirectHireWithdrawnMail($request));
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }
    }

    private function notifyChatRecipient(
        DirectHireRequest $request,
        DirectHireMessage $message,
        User $sender,
    ): void {
        if ($sender->isTalent()) {
            $recipient = $this->hiringSideMailRecipient($request);
            $recipientIsCompany = (bool) $recipient;
        } else {
            $recipientIsCompany = false;
            $recipient = $request->talent;
        }

        if (! $recipient || ! filled($recipient->email)) {
            return;
        }

        try {
            Mail::to($recipient->email)->send(new DirectHireChatMessageMail(
                $request,
                $message,
                $sender,
                $recipient,
                $recipientIsCompany,
            ));
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }
    }

    /**
     * Persist seen/activity timestamps without letting Eloquent overwrite
     * updated_at with a second, slightly newer value (false "unseen" dots).
     *
     * @param  array<string, mixed>  $values
     */
    private function writeRequestTimestamps(int $requestId, array $values): void
    {
        DirectHireRequest::withoutTimestamps(function () use ($requestId, $values) {
            DirectHireRequest::query()->whereKey($requestId)->update($values);
        });
    }

    private function recordStatusEvent(
        DirectHireRequest $request,
        string $event,
        string $status,
        ?User $actor = null,
        ?string $comment = null,
        mixed $at = null,
    ): DirectHireStatusEvent {
        return $request->statusEvents()->create([
            'event' => $event,
            'status' => $status,
            'comment' => filled($comment) ? trim($comment) : null,
            'actor_user_id' => $actor?->id,
            'created_at' => $at ?? now(),
        ]);
    }

    public function close(DirectHireRequest $request, User $actor, string $outcome, ?string $note = null): DirectHireRequest
    {
        $this->assertHiringSideCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'outcome' => __('talenma.direct_hire.error_close_locked'),
            ]);
        }

        if (! in_array($outcome, [
            DirectHireRequest::STATUS_HIRED,
            DirectHireRequest::STATUS_CLOSED_NEGATIVE,
        ], true)) {
            throw ValidationException::withMessages([
                'outcome' => __('talenma.direct_hire.error_outcome_invalid'),
            ]);
        }

        $now = now();

        DirectHireRequest::withoutTimestamps(function () use ($request, $outcome, $actor, $note, $now) {
            $payload = [
                'status' => $outcome,
                'closed_at' => $now,
                'closed_by' => $actor->id,
                'closure_note' => filled($note) ? trim($note) : null,
                ...$this->hiringSideActionSeenFields($request, $actor, $now),
            ];

            if ($outcome === DirectHireRequest::STATUS_HIRED) {
                $payload['talent_locked_at'] = $now;
                $payload['talent_unlocked_at'] = null;
            }

            $request->update($payload);
        });

        $this->recordStatusEvent(
            $request,
            DirectHireStatusEvent::EVENT_CLOSED,
            $outcome,
            $actor,
            filled($note) ? trim($note) : null,
            $now,
        );

        $request->refresh()->loadMissing(['talent', 'company', 'companyProfile']);
        $this->notifyTalentClosed($request);

        return $request;
    }

    public function unlockTalentForCompany(DirectHireRequest $request, User $company): DirectHireRequest
    {
        $this->assertHiringSideCanManage($request, $company);

        if ($request->status !== DirectHireRequest::STATUS_HIRED || ! $request->hasActiveTalentLock()) {
            throw ValidationException::withMessages([
                'lock' => __('talenma.direct_hire.unlock_unavailable'),
            ]);
        }

        $request->releaseTalentLock();

        return $request->refresh();
    }

    public function withdraw(DirectHireRequest $request, User $actor, ?string $note = null): DirectHireRequest
    {
        $this->assertHiringSideCanManage($request, $actor);

        if (! in_array($request->status, DirectHireRequest::openStatuses(), true)) {
            throw ValidationException::withMessages([
                'status' => __('talenma.direct_hire.error_withdraw_locked'),
            ]);
        }

        $now = now();

        DirectHireRequest::withoutTimestamps(function () use ($request, $actor, $note, $now) {
            $request->update([
                'status' => DirectHireRequest::STATUS_WITHDRAWN,
                'closed_at' => $now,
                'closed_by' => $actor->id,
                'closure_note' => filled($note) ? trim($note) : null,
                ...$this->hiringSideActionSeenFields($request, $actor, $now),
            ]);
        });

        $this->recordStatusEvent(
            $request,
            DirectHireStatusEvent::EVENT_WITHDRAWN,
            DirectHireRequest::STATUS_WITHDRAWN,
            $actor,
            filled($note) ? trim($note) : null,
            $now,
        );

        $request->refresh()->loadMissing(['talent', 'company', 'companyProfile']);
        $this->notifyTalentWithdrawn($request);

        return $request;
    }

    /**
     * Base query of direct-hire requests visible to this company account.
     * Staff-on-behalf dossiers are admin-only and must never appear here.
     */
    public function queryForCompany(User $company): \Illuminate\Database\Eloquent\Builder
    {
        $query = DirectHireRequest::query()
            ->where('hire_origin', DirectHireRequest::ORIGIN_COMPANY);

        if (! $company->isCompany()) {
            return $query->whereRaw('0 = 1');
        }

        $org = $company->companyOrganization();

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $company->id);
        }

        return $query;
    }

    public function companyHasOpenRequest(User $company): bool
    {
        return $this->queryForCompany($company)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->exists();
    }

    public function companyHasOpenRequestWithTalent(User $company, User $talent): bool
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return false;
        }

        return $this->queryForCompany($company)
            ->where('talent_user_id', $talent->id)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->exists();
    }

    /**
     * Talent user IDs with an open direct-hire process for this company.
     *
     * @return list<int>
     */
    public function openTalentIdsForCompany(User $company): array
    {
        if (! $company->isCompany()) {
            return [];
        }

        return $this->queryForCompany($company)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->pluck('talent_user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function companyCanPropose(User $company): bool
    {
        return $company->isCompany() && ! $this->companyHasOpenRequest($company);
    }

    public function companyHasHiredTalent(User $company, User $talent): bool
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return false;
        }

        return $this->activeHireLockForTalent($company, $talent) !== null;
    }

    public function activeHireLockForTalent(User $company, User $talent): ?DirectHireRequest
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return null;
        }

        return $this->queryForCompany($company)
            ->where('talent_user_id', $talent->id)
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->whereNotNull('talent_locked_at')
            ->whereNull('talent_unlocked_at')
            ->latest()
            ->first();
    }

    /**
     * Talent user IDs still locked after a successful direct hire by this company.
     *
     * @return list<int>
     */
    public function hiredTalentIdsForCompany(User $company): array
    {
        if (! $company->isCompany()) {
            return [];
        }

        return $this->queryForCompany($company)
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->whereNotNull('talent_user_id')
            ->whereNotNull('talent_locked_at')
            ->whereNull('talent_unlocked_at')
            ->pluck('talent_user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Whether this company may start a new direct-hire proposal for this talent.
     * Other companies remain free to propose even if this company already hired them.
     */
    public function companyCanProposeToTalent(User $company, User $talent): bool
    {
        return $this->companyProposeBlockReason($company, $talent) === null;
    }

    /**
     * @return 'hired'|'open'|'intermediation_locked'|null
     */
    public function companyProposeBlockReason(User $company, User $talent): ?string
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return 'open';
        }

        if ($this->companyHasHiredTalent($company, $talent)) {
            return 'hired';
        }

        if (app(RecruitmentRequestService::class)->activeNamedLockForTalent($company, $talent)) {
            return 'intermediation_locked';
        }

        if ($this->companyHasOpenRequest($company)) {
            return 'open';
        }

        return null;
    }

    public function companyProposeDisabledHint(User $company, User $talent): ?string
    {
        return match ($this->companyProposeBlockReason($company, $talent)) {
            'hired' => __('talenma.direct_hire.cta_disabled_locked_hint'),
            'intermediation_locked' => __('talenma.direct_hire.cta_disabled_locked_intermediation_hint'),
            'open' => __('talenma.direct_hire.cta_disabled_hint'),
            default => null,
        };
    }

    /**
     * Resolve propose flag + hint without N+1 when IDs are preloaded.
     *
     * @param  list<int>  $hiredTalentIds
     * @param  list<int>  $lockedNamedTalentIds
     * @return array{0: bool, 1: string|null}
     */
    public function resolveProposeForTalent(
        User $company,
        User $talent,
        bool $canProposeGlobally,
        array $hiredTalentIds,
        array $lockedNamedTalentIds = [],
    ): array {
        if (in_array((int) $talent->id, $hiredTalentIds, true)) {
            return [false, __('talenma.direct_hire.cta_disabled_locked_hint')];
        }

        if (in_array((int) $talent->id, $lockedNamedTalentIds, true)) {
            return [false, __('talenma.direct_hire.cta_disabled_locked_intermediation_hint')];
        }

        if (! $canProposeGlobally) {
            return [false, __('talenma.direct_hire.cta_disabled_hint')];
        }

        if (! $company->isCompany() || ! $talent->isTalent()) {
            return [false, __('talenma.direct_hire.cta_disabled_hint')];
        }

        return [true, null];
    }

    public function talentHasUnseenChanges(User $talent): bool
    {
        if (! $talent->isTalent()) {
            return false;
        }

        return DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->where(function ($query) {
                $query->whereNull('talent_seen_at')
                    ->orWhereColumn('talent_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function markSeenForTalent(User $talent, ?DirectHireRequest $directHire = null): void
    {
        if (! $talent->isTalent()) {
            return;
        }

        $query = DirectHireRequest::query()->where('talent_user_id', $talent->id);

        if ($directHire) {
            $query->whereKey($directHire->id);
        }

        // Viewing must not bump updated_at (would re-notify the company).
        DirectHireRequest::withoutTimestamps(function () use ($query) {
            $query->update(['talent_seen_at' => now()]);
        });
    }

    /**
     * True if at least one of the company's direct-hire requests has unseen changes.
     * Used for the section title indicator (stays until every request is seen).
     */
    public function companyHasUnseenChanges(User $company): bool
    {
        if (! $company->isCompany()) {
            return false;
        }

        return $this->queryForCompany($company)
            ->where(function ($inner) {
                $inner->whereNull('company_seen_at')
                    ->orWhereColumn('company_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function staffHasUnseenChanges(User $staff): bool
    {
        if (! $staff->isStaff()) {
            return false;
        }

        return DirectHireRequest::query()
            ->whereIn('hire_origin', DirectHireRequest::staffHireOrigins())
            ->where(function ($inner) {
                $inner->whereNull('staff_seen_at')
                    ->orWhereColumn('staff_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    /**
     * Mark a single request as seen by the company.
     * Does not clear unseen state on other requests.
     */
    public function markSeenForCompany(User $company, DirectHireRequest $directHire): void
    {
        $this->assertCompanyCanManage($directHire, $company);

        // Viewing must not bump updated_at (would keep/recreate the blue dots).
        DirectHireRequest::withoutTimestamps(function () use ($directHire) {
            DirectHireRequest::query()
                ->whereKey($directHire->id)
                ->update(['company_seen_at' => now()]);
        });
    }

    /**
     * Keep direct-hire dossiers for the surviving party when an account is deleted.
     * Hard-delete a dossier only when both parties are gone.
     */
    public function releasePartyOnUserDeletion(User $user): void
    {
        if ($user->isTalent()) {
            $this->detachTalentParty($user);

            return;
        }

        if (! $user->isCompany()) {
            return;
        }

        $org = $user->companyOrganization();

        // Seat removal: reassign creator to the owner so org history stays intact.
        if ($user->isCompanyMember() && $org && (int) $org->user_id !== (int) $user->id) {
            DirectHireRequest::query()
                ->where('hire_origin', DirectHireRequest::ORIGIN_COMPANY)
                ->where('company_user_id', $user->id)
                ->update(['company_user_id' => $org->user_id]);

            return;
        }

        $this->detachCompanyParty($user, $org);
    }

    private function detachTalentParty(User $talent): void
    {
        $hires = DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->with(['companyProfile.user', 'company'])
            ->get();

        foreach ($hires as $hire) {
            $hire->talent_name_snapshot = filled($hire->talent_name_snapshot)
                ? $hire->talent_name_snapshot
                : $talent->name;
            $hire->company_name_snapshot = filled($hire->company_name_snapshot)
                ? $hire->company_name_snapshot
                : $hire->companyDisplayName();
            $hire->talent_user_id = null;

            if (! $hire->hasCompanyParty()) {
                $hire->delete();

                continue;
            }

            $this->closeOpenHireAfterPartyLeft($hire);
            $hire->save();
        }
    }

    private function detachCompanyParty(User $company, ?\App\Models\CompanyProfile $org): void
    {
        $query = DirectHireRequest::query()->with(['companyProfile.user', 'company', 'talent', 'initiatedBy']);

        if ($org) {
            $actorIds = $org->memberships()->pluck('user_id')
                ->push($org->user_id)
                ->push($company->id)
                ->filter()
                ->unique()
                ->values()
                ->all();

            $query->where(function ($inner) use ($org, $actorIds) {
                $inner->where('company_profile_id', $org->id)
                    ->orWhereIn('company_user_id', $actorIds);
            });
        } else {
            $query->where('company_user_id', $company->id);
        }

        $companyLabel = $org?->displayName() ?: $company->name;

        foreach ($query->get() as $hire) {
            $hire->company_name_snapshot = filled($hire->company_name_snapshot)
                ? $hire->company_name_snapshot
                : $companyLabel;
            $hire->talent_name_snapshot = filled($hire->talent_name_snapshot)
                ? $hire->talent_name_snapshot
                : ($hire->talent?->name ?? $hire->talent_name_snapshot);

            // Staff-on-behalf stays an admin process: keep snapshot, drop company FKs, do not close.
            if ($hire->isStaffOnBehalf()) {
                $hire->company_user_id = null;
                $hire->company_profile_id = null;
                $hire->save();

                continue;
            }

            // Company profile is deleted next — treat company side as leaving now.
            $hire->company_user_id = null;

            if (! $hire->hasTalentParty()) {
                $hire->delete();

                continue;
            }

            $this->closeOpenHireAfterPartyLeft($hire);
            $hire->save();
        }
    }

    private function closeOpenHireAfterPartyLeft(DirectHireRequest $hire): void
    {
        if (! $hire->isOpen()) {
            return;
        }

        $hire->status = DirectHireRequest::STATUS_WITHDRAWN;
        $hire->closed_at = now();
        $hire->closed_by = null;
        $hire->closure_note = __('talenma.direct_hire.closure_party_deleted');
    }

    public function assertCompanyCanManage(DirectHireRequest $request, User $actor): void
    {
        if (! $actor->isCompany()) {
            abort(403);
        }

        // Staff-led dossiers (internal or on behalf) are invisible to the company account.
        if ($request->isStaffInitiated()) {
            abort(403);
        }

        $org = $actor->companyOrganization();

        $sameCreator = $request->company_user_id === $actor->id;
        $sameOrg = $org && $request->company_profile_id && $request->company_profile_id === $org->id;

        abort_unless($sameCreator || $sameOrg, 403);
    }

    public function assertStaffCanManage(DirectHireRequest $request, User $actor): void
    {
        abort_unless($actor->isStaff() && $request->isStaffInitiated(), 403);
    }

    public function assertHiringSideCanManage(DirectHireRequest $request, User $actor): void
    {
        if ($actor->isStaff() && $request->isStaffInitiated()) {
            return;
        }

        $this->assertCompanyCanManage($request, $actor);
    }

    public function assertTalentCanView(DirectHireRequest $request, User $talent): void
    {
        abort_unless($talent->isTalent() && $request->talent_user_id === $talent->id, 403);
    }

    public function assertCanChat(DirectHireRequest $request, User $user): void
    {
        if ($user->isTalent()) {
            $this->assertTalentCanView($request, $user);

            return;
        }

        $this->assertHiringSideCanManage($request, $user);
    }

    public function markSeenForHiringSide(User $actor, DirectHireRequest $directHire): void
    {
        $this->assertHiringSideCanManage($directHire, $actor);

        $fields = $actor->isStaff()
            ? ['staff_seen_at' => now()]
            : ['company_seen_at' => now()];

        DirectHireRequest::withoutTimestamps(function () use ($directHire, $fields) {
            DirectHireRequest::query()
                ->whereKey($directHire->id)
                ->update($fields);
        });
    }
}
