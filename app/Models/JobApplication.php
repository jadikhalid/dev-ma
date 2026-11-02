<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_posting_id',
    'talent_user_id',
    'direct_hire_request_id',
    'cover_message',
    'status',
    'submitted_at',
    'talent_seen_at',
])]
class JobApplication extends Model
{
    use HasFactory;

    public const STATUS_RECEIVED = 'received';

    /** Direct Hire launched — process ongoing. */
    public const STATUS_IN_PROCESS = 'in_process';

    /** Hired after a successful Direct Hire. */
    public const STATUS_RETAINED = 'retained';

    public const STATUS_REJECTED = 'rejected';

    /** @deprecated Use STATUS_IN_PROCESS */
    public const STATUS_SHORTLISTED = 'in_process';

    /** @deprecated Use STATUS_REJECTED */
    public const STATUS_CLOSED = 'rejected';

    /** @deprecated Legacy status removed from the workflow */
    public const STATUS_VIEWED = 'received';

    public const STATUSES = [
        self::STATUS_RECEIVED,
        self::STATUS_IN_PROCESS,
        self::STATUS_RETAINED,
        self::STATUS_REJECTED,
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'talent_seen_at' => 'datetime',
        ];
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function directHireRequest(): BelongsTo
    {
        return $this->belongsTo(DirectHireRequest::class);
    }

    public function statusLabel(): string
    {
        return __('talenma.jobs.application_status_'.$this->normalizedStatus());
    }

    public function normalizedStatus(): string
    {
        return match ($this->status) {
            'submitted' => self::STATUS_RECEIVED,
            'reviewed', 'viewed' => self::STATUS_RECEIVED,
            'shortlisted', 'in_process' => self::STATUS_IN_PROCESS,
            'retained', 'hired' => self::STATUS_RETAINED,
            'rejected', 'withdrawn', 'closed' => self::STATUS_REJECTED,
            default => in_array($this->status, self::STATUSES, true)
                ? $this->status
                : self::STATUS_RECEIVED,
        };
    }

    /**
     * Statuses the company may set manually from the applications panel.
     * In-process / retained are driven by Direct Hire only.
     *
     * @return list<string>
     */
    public function availableNextStatuses(): array
    {
        return match ($this->normalizedStatus()) {
            self::STATUS_RECEIVED => [self::STATUS_REJECTED],
            default => [],
        };
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->availableNextStatuses(), true);
    }

    public function isClosed(): bool
    {
        return in_array($this->normalizedStatus(), [
            self::STATUS_REJECTED,
            self::STATUS_RETAINED,
        ], true);
    }

    public function isInProcess(): bool
    {
        return $this->normalizedStatus() === self::STATUS_IN_PROCESS;
    }

    public function isRetained(): bool
    {
        return $this->normalizedStatus() === self::STATUS_RETAINED;
    }

    /**
     * @return list<string>
     */
    public static function closedStorageStatuses(): array
    {
        return [
            self::STATUS_REJECTED,
            self::STATUS_RETAINED,
            'closed',
            'rejected',
            'withdrawn',
            'retained',
            'hired',
        ];
    }

    public function hasUnseenChangesForTalent(): bool
    {
        if ($this->talent_seen_at === null) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $this->talent_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }
}
