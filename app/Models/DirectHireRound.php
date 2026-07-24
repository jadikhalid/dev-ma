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
    'completed_at',
    'company_note',
])]
class DirectHireRound extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PASSED = 'passed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SKIPPED = 'skipped';

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
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

    public function statusLabel(): string
    {
        return __('talenma.direct_hire.round_status_'.$this->status);
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'slate',
            self::STATUS_SCHEDULED => 'sky',
            self::STATUS_PASSED => 'emerald',
            self::STATUS_FAILED => 'rose',
            self::STATUS_SKIPPED => 'amber',
            default => 'slate',
        };
    }
}
