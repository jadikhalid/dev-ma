<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'direct_hire_request_id',
    'position',
    'title',
    'status',
    'scheduled_at',
    'meeting_url',
    'completed_at',
    'company_note',
    'cancellation_reason',
])]
class DirectHireRound extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending'; // legacy only — no longer used for new rounds

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PASSED = 'passed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * All known statuses (including cancelled).
     *
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_PASSED,
            self::STATUS_FAILED,
            self::STATUS_SKIPPED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Statuses selectable as round outcome (not cancellation).
     *
     * @return list<string>
     */
    public static function outcomeStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_PASSED,
            self::STATUS_FAILED,
            self::STATUS_SKIPPED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function completedStatuses(): array
    {
        return [
            self::STATUS_PASSED,
            self::STATUS_FAILED,
            self::STATUS_SKIPPED,
            self::STATUS_CANCELLED,
        ];
    }

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(DirectHireRequest::class, 'direct_hire_request_id');
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isCancellable(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            || $this->status === self::STATUS_PENDING;
    }

    public function statusLabel(): string
    {
        $key = 'talenma.direct_hire.round_status_'.$this->status;

        if ($this->status === self::STATUS_PENDING) {
            return __('talenma.direct_hire.round_status_scheduled');
        }

        return __($key);
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING, self::STATUS_SCHEDULED => 'sky',
            self::STATUS_PASSED => 'emerald',
            self::STATUS_FAILED => 'rose',
            self::STATUS_SKIPPED => 'amber',
            self::STATUS_CANCELLED => 'slate',
            default => 'slate',
        };
    }
}
