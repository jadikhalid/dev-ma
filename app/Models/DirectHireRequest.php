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
    'company_profile_id',
    'subject',
    'message',
    'status',
    'talent_decision_at',
    'talent_decision_note',
    'conversation_id',
    'closed_at',
    'closed_by',
    'closure_note',
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

    public const DECISION_ACCEPT = 'accept';

    public const DECISION_DECLINE = 'decline';

    public const DECISION_DEFER = 'defer';

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

    protected function casts(): array
    {
        return [
            'talent_decision_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
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

    public function statusLabel(): string
    {
        return __('talenma.direct_hire.status_'.$this->status);
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

    public function companyDisplayName(): string
    {
        $this->loadMissing('companyProfile');

        return $this->companyProfile?->displayName()
            ?: ($this->company?->name ?? '');
    }
}
