<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_user_id',
    'talent_user_id',
    'talent_name_snapshot',
    'company_profile_id',
    'company_name_snapshot',
    'hire_origin',
    'initiated_by_user_id',
    'subject',
    'message',
    'status',
    'talent_decision_at',
    'talent_decision_note',
    'company_deferral_note',
    'company_deferral_responded_at',
    'conversation_id',
    'closed_at',
    'closed_by',
    'closure_note',
    'talent_seen_at',
    'company_seen_at',
    'staff_seen_at',
    'talent_locked_at',
    'talent_unlocked_at',
])]
class DirectHireRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING_RESPONSE = 'pending_response';

    public const STATUS_DEFERRED = 'deferred';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_IN_PROCESS = 'in_process';

    public const STATUS_HIRED = 'hired';

    public const STATUS_CLOSED_NEGATIVE = 'closed_negative';

    public const STATUS_WITHDRAWN = 'withdrawn';

    public const ORIGIN_COMPANY = 'company';

    public const ORIGIN_STAFF_INTERNAL = 'staff_internal';

    public const ORIGIN_STAFF_ON_BEHALF = 'staff_on_behalf';

    public const DECISION_ACCEPT = 'accept';

    public const DECISION_DECLINE = 'decline';

    public const DECISION_DEFER = 'defer';

    public const DEFERRAL_ACCEPT = 'accept';

    public const DEFERRAL_REFUSE = 'refuse';

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_RESPONSE,
            self::STATUS_DEFERRED,
            self::STATUS_DECLINED,
            self::STATUS_IN_PROCESS,
            self::STATUS_HIRED,
            self::STATUS_CLOSED_NEGATIVE,
            self::STATUS_WITHDRAWN,
        ];
    }

    /**
     * @return list<string>
     */
    public static function openStatuses(): array
    {
        return [
            self::STATUS_PENDING_RESPONSE,
            self::STATUS_DEFERRED,
            self::STATUS_IN_PROCESS,
        ];
    }

    /**
     * @return list<string>
     */
    public static function terminalStatuses(): array
    {
        return [
            self::STATUS_DECLINED,
            self::STATUS_HIRED,
            self::STATUS_CLOSED_NEGATIVE,
            self::STATUS_WITHDRAWN,
        ];
    }

    /**
     * @return list<string>
     */
    public static function talentDecisions(): array
    {
        return [
            self::DECISION_ACCEPT,
            self::DECISION_DECLINE,
            self::DECISION_DEFER,
        ];
    }

    /**
     * @return list<string>
     */
    public static function hireOrigins(): array
    {
        return [
            self::ORIGIN_COMPANY,
            self::ORIGIN_STAFF_INTERNAL,
            self::ORIGIN_STAFF_ON_BEHALF,
        ];
    }

    /**
     * @return list<string>
     */
    public static function staffHireOrigins(): array
    {
        return [
            self::ORIGIN_STAFF_INTERNAL,
            self::ORIGIN_STAFF_ON_BEHALF,
        ];
    }

    /**
     * @return list<string>
     */
    public static function companyDeferralActions(): array
    {
        return [
            self::DEFERRAL_ACCEPT,
            self::DEFERRAL_REFUSE,
        ];
    }

    protected function casts(): array
    {
        return [
            'talent_decision_at' => 'datetime',
            'company_deferral_responded_at' => 'datetime',
            'closed_at' => 'datetime',
            'talent_seen_at' => 'datetime',
            'company_seen_at' => 'datetime',
            'staff_seen_at' => 'datetime',
            'talent_locked_at' => 'datetime',
            'talent_unlocked_at' => 'datetime',
        ];
    }

    public function hasActiveTalentLock(): bool
    {
        return $this->talent_locked_at !== null && $this->talent_unlocked_at === null;
    }

    public function activateTalentLock(): void
    {
        $this->forceFill([
            'talent_locked_at' => $this->talent_locked_at ?? now(),
            'talent_unlocked_at' => null,
        ])->save();
    }

    public function releaseTalentLock(): void
    {
        if (! $this->hasActiveTalentLock()) {
            return;
        }

        $this->forceFill([
            'talent_unlocked_at' => now(),
        ])->save();
    }

    public function awaitsCompanyDeferralReply(): bool
    {
        return $this->status === self::STATUS_DEFERRED
            && $this->company_deferral_responded_at === null;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_user_id');
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function isStaffInitiated(): bool
    {
        return in_array($this->hire_origin, self::staffHireOrigins(), true);
    }

    public function isStaffInternal(): bool
    {
        return $this->hire_origin === self::ORIGIN_STAFF_INTERNAL;
    }

    public function isStaffOnBehalf(): bool
    {
        return $this->hire_origin === self::ORIGIN_STAFF_ON_BEHALF;
    }

    /**
     * Counterparty label shown to the talent (never the client company for staff-led hires).
     */
    public function talentFacingCompanyName(): string
    {
        if ($this->isStaffInitiated()) {
            return __('talenma.direct_hire.platform_employer_name');
        }

        return $this->companyDisplayName();
    }

    public function hireOriginLabel(): string
    {
        return match ($this->hire_origin) {
            self::ORIGIN_STAFF_INTERNAL => __('talenma.direct_hire.origin_staff_internal'),
            self::ORIGIN_STAFF_ON_BEHALF => __('talenma.direct_hire.origin_staff_on_behalf'),
            default => __('talenma.direct_hire.origin_company'),
        };
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(DirectHireRound::class)->orderBy('position');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DirectHireMessage::class)->orderBy('created_at')->orderBy('id');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(DirectHireStatusEvent::class)->orderBy('created_at')->orderBy('id');
    }

    public function statusLabel(): string
    {
        return __('talenma.direct_hire.status_'.$this->status);
    }

    /**
     * Short progress line for list badges (current / last step, or final hire outcome).
     */
    public function progressLabel(): ?string
    {
        if ($this->status === self::STATUS_HIRED) {
            return __('talenma.direct_hire.progress_hired');
        }

        if ($this->status !== self::STATUS_IN_PROCESS) {
            return null;
        }

        $this->loadMissing('rounds');

        $rounds = $this->rounds
            ->filter(fn (DirectHireRound $round) => ! $round->isCancelled())
            ->sortBy('position')
            ->values();

        if ($rounds->isEmpty()) {
            return __('talenma.direct_hire.progress_awaiting_steps');
        }

        $parts = [];

        $lastCompleted = $rounds
            ->filter(fn (DirectHireRound $round) => in_array($round->status, DirectHireRound::outcomeStatuses(), true))
            ->last();

        $current = $rounds->first(fn (DirectHireRound $round) => $round->isEditable());

        if ($lastCompleted) {
            $parts[] = __('talenma.direct_hire.progress_round', [
                'n' => $lastCompleted->position,
                'status' => $lastCompleted->statusLabel(),
            ]);
        }

        if ($current && (! $lastCompleted || $current->id !== $lastCompleted->id)) {
            $parts[] = __('talenma.direct_hire.progress_round', [
                'n' => $current->position,
                'status' => $current->statusLabel(),
            ]);
        }

        if ($parts === [] && $rounds->isNotEmpty()) {
            $latest = $rounds->last();
            $parts[] = __('talenma.direct_hire.progress_round', [
                'n' => $latest->position,
                'status' => $latest->statusLabel(),
            ]);
        }

        return $parts === [] ? null : implode(' · ', $parts);
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_RESPONSE => 'amber',
            self::STATUS_DEFERRED => 'violet',
            self::STATUS_DECLINED => 'rose',
            self::STATUS_IN_PROCESS => 'sky',
            self::STATUS_HIRED => 'emerald',
            self::STATUS_CLOSED_NEGATIVE => 'rose',
            self::STATUS_WITHDRAWN => 'slate',
            default => 'slate',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::openStatuses(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::terminalStatuses(), true);
    }

    /**
     * Chat stays open after hire, negative close, or talent decline for continued exchanges.
     * Closed only when the company withdrew the proposal.
     */
    public function allowsChat(): bool
    {
        return $this->status !== self::STATUS_WITHDRAWN;
    }

    public function companyDisplayName(): string
    {
        if ($this->isStaffInternal()) {
            $snapshot = trim((string) ($this->company_name_snapshot ?? ''));

            return $snapshot !== ''
                ? $snapshot
                : __('talenma.direct_hire.platform_employer_name');
        }

        $this->loadMissing(['companyProfile.user', 'company']);

        $live = trim((string) (
            $this->companyProfile?->displayName()
            ?: ($this->company?->name ?? '')
        ));

        if ($live !== '') {
            return $live;
        }

        $snapshot = trim((string) ($this->company_name_snapshot ?? ''));

        return $snapshot !== ''
            ? $snapshot
            : __('talenma.direct_hire.party_deleted');
    }

    public function talentDisplayName(): string
    {
        $live = trim((string) ($this->talent?->name ?? ''));

        if ($live !== '') {
            return $live;
        }

        $snapshot = trim((string) ($this->talent_name_snapshot ?? ''));

        return $snapshot !== ''
            ? $snapshot
            : __('talenma.direct_hire.party_deleted');
    }

    /**
     * Talent full name for outbound mail (From / subject), Title Case.
     */
    public function talentFormalDisplayName(): string
    {
        $this->loadMissing('talent');

        $talent = $this->talent;
        $raw = '';

        if ($talent) {
            $raw = trim((string) (($talent->first_name ?? '').' '.($talent->last_name ?? '')));

            if ($raw === '') {
                $raw = trim((string) $talent->name);
            }
        }

        if ($raw === '') {
            $raw = $this->talentDisplayName();
        }

        return $this->titleCasePersonName($raw);
    }

    /**
     * Company name for talent-facing outbound mail (subject / body) — masked when staff-led.
     */
    public function companyFormalDisplayName(): string
    {
        return $this->talentFacingCompanyName();
    }

    /**
     * Person to greet on company-side mail: initiator user, else primary contact.
     */
    public function companyRecipientGreetingName(): string
    {
        $this->loadMissing(['company', 'companyProfile', 'initiatedBy']);

        if ($this->isStaffInitiated()) {
            $staff = $this->initiatedBy;
            $person = trim((string) (($staff?->first_name ?? '').' '.($staff?->last_name ?? '')));

            if ($person === '') {
                $person = trim((string) ($staff?->name ?? ''));
            }

            return $person !== ''
                ? $this->titleCasePersonName($person)
                : __('talenma.direct_hire.platform_employer_name');
        }

        $initiator = $this->company;

        if ($initiator?->isCompanyMember()) {
            $person = trim((string) (($initiator->first_name ?? '').' '.($initiator->last_name ?? '')));

            if ($person === '') {
                $person = trim((string) $initiator->name);
            }

            if ($person !== '') {
                return $this->titleCasePersonName($person);
            }
        }

        $representative = trim((string) ($this->companyProfile?->representative_name ?? ''));

        if ($representative !== '') {
            return $this->titleCasePersonName($representative);
        }

        if ($initiator) {
            $person = trim((string) (($initiator->first_name ?? '').' '.($initiator->last_name ?? '')));

            if ($person === '') {
                $person = trim((string) $initiator->name);
            }

            if ($person !== '' && $person !== $this->companyDisplayName()) {
                return $this->titleCasePersonName($person);
            }
        }

        return $this->titleCasePersonName($this->companyDisplayName());
    }

    private function titleCasePersonName(string $name): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $name) ?: '');

        if ($normalized === '') {
            return '';
        }

        return mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8');
    }

    public function hasCompanyParty(): bool
    {
        if ($this->isStaffInitiated()) {
            return true;
        }

        return $this->company_user_id !== null || $this->company_profile_id !== null;
    }

    public function hasTalentParty(): bool
    {
        return $this->talent_user_id !== null;
    }

    /**
     * Subject without the redundant "Proposition de candidature :" prefix
     * (kept when the title is already shown separately in the page header).
     */
    public function shortSubject(): string
    {
        $subject = trim((string) $this->subject);

        $stripped = preg_replace(
            '/^(proposition de candidature|proposition de recrutement|application proposal|hire proposal|recruitment proposal)\s*[:—\-–]\s*/iu',
            '',
            $subject
        );

        $stripped = trim((string) $stripped);

        return $stripped !== '' ? $stripped : $subject;
    }

    public function hasUnseenChangesForCompany(): bool
    {
        if ($this->company_seen_at === null) {
            return true;
        }

        if (! $this->updated_at) {
            return false;
        }

        // Compare at second precision — same-second mark-seen must not look "unseen".
        return $this->company_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }

    public function hasUnseenChangesForTalent(): bool
    {
        if ($this->talent_seen_at === null) {
            return true;
        }

        if (! $this->updated_at) {
            return false;
        }

        return $this->talent_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }

    public function hasUnseenChangesForStaff(): bool
    {
        if (! $this->isStaffInitiated()) {
            return false;
        }

        if ($this->staff_seen_at === null) {
            return true;
        }

        if (! $this->updated_at) {
            return false;
        }

        return $this->staff_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }
}
