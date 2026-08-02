<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_profile_id',
    'job_posting_id',
    'job_title',
    'event',
    'status',
    'actor_label',
    'actor_user_id',
    'talent_user_id',
    'is_self',
    'created_at',
])]
class JobPostingActivityEvent extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_CREATED = 'created';

    public const EVENT_PUBLISHED = 'published';

    public const EVENT_CLOSED = 'closed';

    public const EVENT_HIDDEN = 'hidden';

    public const EVENT_POSTPONED = 'postponed';

    public const EVENT_DELETED = 'deleted';

    public const EVENT_APPLICATION_STATUS = 'application_status';

    protected function casts(): array
    {
        return [
            'is_self' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public static function record(
        JobPosting $job,
        string $event,
        ?User $actor = null,
        ?string $status = null,
        ?string $actorLabel = null,
        ?bool $isSelf = null,
        ?int $talentUserId = null,
    ): self {
        $self = $isSelf ?? ($actor?->isCompany() ?? false);

        return self::query()->create([
            'company_profile_id' => $job->company_profile_id,
            'job_posting_id' => $job->id,
            'job_title' => $job->title,
            'event' => $event,
            'status' => $status ?? $job->status,
            'actor_label' => $actorLabel,
            'actor_user_id' => $actor?->id,
            'talent_user_id' => $talentUserId,
            'is_self' => $self,
            'created_at' => now(),
        ]);
    }
}
