<?php

namespace App\Services;

use App\Mail\DirectHireDecisionMail;
use App\Mail\DirectHireProposalMail;
use App\Mail\DirectHireRoundCancelledMail;
use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
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
                'subject' => $subject,
                'message' => $message,
                'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
                'conversation_id' => null,
                'company_seen_at' => now(),
            ]);

            $request->messages()->create([
                'sender_user_id' => $company->id,
                'body' => $message,
            ]);

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

        $newStatus = match ($decision) {
            DirectHireRequest::DECISION_ACCEPT => DirectHireRequest::STATUS_IN_PROCESS,
            DirectHireRequest::DECISION_DECLINE => DirectHireRequest::STATUS_DECLINED,
            DirectHireRequest::DECISION_DEFER => DirectHireRequest::STATUS_DEFERRED,
        };

        $request->update([
            'status' => $newStatus,
            'talent_decision_at' => now(),
            'talent_decision_note' => filled($note) ? trim($note) : null,
            'talent_seen_at' => now(),
            'closed_at' => $newStatus === DirectHireRequest::STATUS_DECLINED ? now() : null,
            'closed_by' => $newStatus === DirectHireRequest::STATUS_DECLINED ? $talent->id : null,
            'closure_note' => $newStatus === DirectHireRequest::STATUS_DECLINED
                ? (filled($note) ? trim($note) : null)
                : $request->closure_note,
        ]);

        $request->load(['company', 'companyProfile', 'talent']);

        try {
            if (filled($request->company?->email)) {
                Mail::to($request->company->email)->send(new DirectHireDecisionMail($request, $decision));
            }
        } catch (\Throwable) {
            // Never block the hire process on mail failures.
        }

        return $request;
    }

    public function postMessage(DirectHireRequest $request, User $sender, string $body): DirectHireMessage
    {
        $this->assertCanChat($request, $sender);

        if ($request->isTerminal()) {
            throw ValidationException::withMessages([
                'body' => __('talenma.direct_hire.error_chat_closed'),
            ]);
        }

        $message = $request->messages()->create([
            'sender_user_id' => $sender->id,
            'body' => trim($body),
        ]);

        $now = now();

        if ($sender->isCompany()) {
            $this->writeRequestTimestamps($request->id, [
                'updated_at' => $now,
                'company_seen_at' => $now,
            ]);
        } else {
            $this->writeRequestTimestamps($request->id, [
                'updated_at' => $now,
                'talent_seen_at' => $now,
            ]);
        }

        return $message;
    }

    /**
     * Backfill the initial company message for requests created before dedicated chat.
     */
    public function ensureThreadSeeded(DirectHireRequest $request): void
    {
        if ($request->messages()->exists()) {
            return;
        }

        if (! filled($request->message) || $request->company_user_id === null) {
            return;
        }

        $request->messages()->create([
            'sender_user_id' => $request->company_user_id,
            'body' => $request->message,
        ]);
    }

    public function addRound(
        DirectHireRequest $request,
        User $actor,
        string $title,
        string $scheduledAt,
        ?string $note = null,
        ?string $meetingUrl = null,
    ): DirectHireRound {
        $this->assertCompanyCanManage($request, $actor);

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

        $this->markCompanyChangeForBothParties($request);

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
        $this->assertCompanyCanManage($request, $actor);

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

        $this->markCompanyChangeForBothParties($request);

        return $round->fresh();
    }

    /**
     * Soft-cancel a scheduled round with a required reason.
     * Keeps the round and chat thread; notifies both parties.
     */
    public function cancelRound(DirectHireRound $round, User $actor, string $reason): DirectHireRound
    {
        $request = $round->request;
        $this->assertCompanyCanManage($request, $actor);

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

        $this->markCompanyChangeForBothParties($request);

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

        if (filled($request->company?->email)) {
            try {
                Mail::to($request->company->email)->send(
                    new DirectHireRoundCancelledMail($request, $round, 'company')
                );
            } catch (\Throwable) {
                // Keep cancellation even if mail fails.
            }
        }

        return $round;
    }

    /**
     * Company action: bump activity for the talent (blue dots) while keeping
     * the company side marked as already seen.
     */
    private function markCompanyChangeForBothParties(DirectHireRequest $request): void
    {
        $now = now();

        $this->writeRequestTimestamps($request->id, [
            'updated_at' => $now,
            'company_seen_at' => $now,
            'talent_seen_at' => null,
        ]);
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

    public function close(DirectHireRequest $request, User $actor, string $outcome, ?string $note = null): DirectHireRequest
    {
        $this->assertCompanyCanManage($request, $actor);

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
            $request->update([
                'status' => $outcome,
                'closed_at' => $now,
                'closed_by' => $actor->id,
                'closure_note' => filled($note) ? trim($note) : null,
                'company_seen_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return $request;
    }

    public function withdraw(DirectHireRequest $request, User $actor, ?string $note = null): DirectHireRequest
    {
        $this->assertCompanyCanManage($request, $actor);

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
                'company_seen_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return $request;
    }

    /**
     * Base query of direct-hire requests visible to this company account.
     */
    public function queryForCompany(User $company): \Illuminate\Database\Eloquent\Builder
    {
        $query = DirectHireRequest::query();

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

        $org = $company->companyOrganization();
        $query = DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->whereIn('status', DirectHireRequest::openStatuses());

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $company->id);
        }

        return $query->exists();
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

        $org = $company->companyOrganization();
        $query = DirectHireRequest::query()
            ->whereIn('status', DirectHireRequest::openStatuses());

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $company->id);
        }

        return $query
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

        return $this->queryForCompany($company)
            ->where('talent_user_id', $talent->id)
            ->where('status', DirectHireRequest::STATUS_HIRED)
            ->exists();
    }

    /**
     * Talent user IDs already hired by this company (successful direct hire).
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
     * @return 'hired'|'open'|null
     */
    public function companyProposeBlockReason(User $company, User $talent): ?string
    {
        if (! $company->isCompany() || ! $talent->isTalent()) {
            return 'open';
        }

        if ($this->companyHasHiredTalent($company, $talent)) {
            return 'hired';
        }

        if ($this->companyHasOpenRequest($company)) {
            return 'open';
        }

        return null;
    }

    public function companyProposeDisabledHint(User $company, User $talent): ?string
    {
        return match ($this->companyProposeBlockReason($company, $talent)) {
            'hired' => __('talenma.direct_hire.cta_disabled_hired_hint'),
            'open' => __('talenma.direct_hire.cta_disabled_hint'),
            default => null,
        };
    }

    /**
     * Resolve propose flag + hint without N+1 when IDs are preloaded.
     *
     * @param  list<int>  $hiredTalentIds
     * @return array{0: bool, 1: string|null}
     */
    public function resolveProposeForTalent(User $company, User $talent, bool $canProposeGlobally, array $hiredTalentIds): array
    {
        if (in_array((int) $talent->id, $hiredTalentIds, true)) {
            return [false, __('talenma.direct_hire.cta_disabled_hired_hint')];
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

        $org = $company->companyOrganization();
        $query = DirectHireRequest::query()
            ->where(function ($inner) {
                $inner->whereNull('company_seen_at')
                    ->orWhereColumn('company_seen_at', '<', 'updated_at');
            });

        if ($org) {
            $query->where('company_profile_id', $org->id);
        } else {
            $query->where('company_user_id', $company->id);
        }

        return $query->exists();
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
        $query = DirectHireRequest::query()->with(['companyProfile.user', 'company', 'talent']);

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

        $org = $actor->companyOrganization();

        $sameCreator = $request->company_user_id === $actor->id;
        $sameOrg = $org && $request->company_profile_id && $request->company_profile_id === $org->id;

        abort_unless($sameCreator || $sameOrg, 403);
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

        $this->assertCompanyCanManage($request, $user);
    }
}
