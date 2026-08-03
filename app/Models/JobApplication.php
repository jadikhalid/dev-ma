<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_posting_id',
    'talent_user_id',
    'cover_message',
    'status',
    'submitted_at',
    'talent_seen_at',
])]
class JobApplication extends Model
{
    use HasFactory;

    public const STATUS_RECEIVED = 'received';

    public const STATUS_VIEWED = 'viewed';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_RECEIVED,
        self::STATUS_VIEWED,
        self::STATUS_CLOSED,
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

    public function statusLabel(): string
    {
        return __('talenma.jobs.application_status_'.$this->normalizedStatus());
    }

    public function normalizedStatus(): string
    {
        return match ($this->status) {
            'submitted' => self::STATUS_RECEIVED,
            'reviewed', 'shortlisted' => self::STATUS_VIEWED,
            'rejected', 'withdrawn' => self::STATUS_CLOSED,
            default => in_array($this->status, self::STATUSES, true)
                ? $this->status
                : self::STATUS_RECEIVED,
        };
    }

    /**
     * @return list<string>
     */
    public function availableNextStatuses(): array
    {
        return match ($this->normalizedStatus()) {
            self::STATUS_RECEIVED => [self::STATUS_VIEWED, self::STATUS_CLOSED],
            self::STATUS_VIEWED => [self::STATUS_CLOSED],
            default => [],
        };
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->availableNextStatuses(), true);
    }

    public function isClosed(): bool
    {
        return $this->normalizedStatus() === self::STATUS_CLOSED;
    }

    /**
     * @return list<string>
     */
    public static function closedStorageStatuses(): array
    {
        return [
            self::STATUS_CLOSED,
            'rejected',
            'withdrawn',
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
