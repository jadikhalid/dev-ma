<?php

namespace App\Services;

use App\Mail\DirectHireDecisionMail;
use App\Mail\DirectHireProposalMail;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DirectHireService
{
    public function __construct(private MessagingService $messaging) {}

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

        $duplicateQuery = DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->whereIn('status', DirectHireRequest::openStatuses());

        if ($org) {
            $duplicateQuery->where('company_profile_id', $org->id);
        } else {
            $duplicateQuery->where('company_user_id', $company->id);
        }

        if ($duplicateQuery->exists()) {
            throw ValidationException::withMessages([
                'talent_id' => __('talenma.direct_hire.error_already_open'),
            ]);
        }

        return DB::transaction(function () use ($company, $talent, $subject, $message, $org) {
            $companyName = $org?->displayName() ?: $company->name;

            $inboxBody = implode("\n", [
                __('talenma.direct_hire.inbox_intro'),
                '',
                __('talenma.direct_hire.inbox_company', ['name' => $companyName]),
                __('talenma.direct_hire.inbox_subject_line', ['title' => $subject]),
                '',
                __('talenma.direct_hire.inbox_message_label'),
                $message,
            ]);

            $conversation = $this->messaging->startConversation(
                $company,
                $talent,
                __('talenma.direct_hire.inbox_subject', ['title' => $subject]),
                $inboxBody,
            );

            $request = DirectHireRequest::create([
                'company_user_id' => $company->id,
                'talent_user_id' => $talent->id,
                'company_profile_id' => $org?->id,
                'subject' => $subject,
                'message' => $message,
                'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
                'conversation_id' => $conversation->id,
            ]);

            Mail::to($talent->email)->send(new DirectHireProposalMail($request->fresh([
                'company',
                'companyProfile',
                'talent',
            ])));

            return $request;
        });
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
            'closed_at' => $newStatus === DirectHireRequest::STATUS_DECLINED ? now() : null,
            'closed_by' => $newStatus === DirectHireRequest::STATUS_DECLINED ? $talent->id : null,
            'closure_note' => $newStatus === DirectHireRequest::STATUS_DECLINED
                ? (filled($note) ? trim($note) : null)
                : $request->closure_note,
        ]);

        $request->load(['company', 'companyProfile', 'talent']);

        Mail::to($request->company->email)->send(new DirectHireDecisionMail($request, $decision));

        return $request;
    }

    public function addRound(DirectHireRequest $request, User $actor, string $title, ?string $note = null): DirectHireRound
    {
        $this->assertCompanyCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'title' => __('talenma.direct_hire.error_rounds_locked'),
            ]);
        }

        $position = ((int) $request->rounds()->max('position')) + 1;

        return $request->rounds()->create([
            'position' => $position,
            'title' => $title,
            'status' => DirectHireRound::STATUS_PENDING,
            'company_note' => filled($note) ? trim($note) : null,
        ]);
    }

    /**
     * @param  array{title?: string, status?: string, scheduled_at?: string|null, company_note?: string|null}  $data
     */
    public function updateRound(DirectHireRound $round, User $actor, array $data): DirectHireRound
    {
        $request = $round->request;
        $this->assertCompanyCanManage($request, $actor);

        if ($request->status !== DirectHireRequest::STATUS_IN_PROCESS) {
            throw ValidationException::withMessages([
                'status' => __('talenma.direct_hire.error_rounds_locked'),
            ]);
        }

        $status = $data['status'] ?? $round->status;

        if (! in_array($status, DirectHireRound::statuses(), true)) {
            throw ValidationException::withMessages([
                'status' => __('talenma.direct_hire.error_round_status_invalid'),
            ]);
        }

        $completedAt = $round->completed_at;
        if (in_array($status, DirectHireRound::completedStatuses(), true)) {
            $completedAt = $completedAt ?? now();
        } else {
            $completedAt = null;
        }

        $round->update([
            'title' => array_key_exists('title', $data) && filled($data['title'])
                ? trim($data['title'])
                : $round->title,
            'status' => $status,
            'scheduled_at' => array_key_exists('scheduled_at', $data)
                ? ($data['scheduled_at'] ?: null)
                : $round->scheduled_at,
            'company_note' => array_key_exists('company_note', $data)
                ? (filled($data['company_note'] ?? null) ? trim($data['company_note']) : null)
                : $round->company_note,
            'completed_at' => $completedAt,
        ]);

        return $round->fresh();
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

        $request->update([
            'status' => $outcome,
            'closed_at' => now(),
            'closed_by' => $actor->id,
            'closure_note' => filled($note) ? trim($note) : null,
        ]);

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

        $request->update([
            'status' => DirectHireRequest::STATUS_WITHDRAWN,
            'closed_at' => now(),
            'closed_by' => $actor->id,
            'closure_note' => filled($note) ? trim($note) : null,
        ]);

        return $request;
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
}
