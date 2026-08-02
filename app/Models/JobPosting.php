<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_profile_id',
    'created_by',
    'title',
    'description',
    'contract_type',
    'location_city',
    'location_country',
    'remote_ok',
    'status',
    'published_at',
    'closed_at',
    'company_seen_at',
    'staff_seen_at',
])]
class JobPosting extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_HIDDEN = 'hidden';

    public const STATUS_POSTPONED = 'postponed';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_CLOSED,
        self::STATUS_HIDDEN,
        self::STATUS_POSTPONED,
    ];

    public const CONTRACT_TYPES = [
        'cdi',
        'cdd',
        'freelance',
        'internship',
        'other',
    ];

    protected function casts(): array
    {
        return [
            'remote_ok' => 'boolean',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
            'company_seen_at' => 'datetime',
            'staff_seen_at' => 'datetime',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function activityEvents(): HasMany
    {
        return $this->hasMany(JobPostingActivityEvent::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isHidden(): bool
    {
        return $this->status === self::STATUS_HIDDEN;
    }

    public function isPostponed(): bool
    {
        return $this->status === self::STATUS_POSTPONED;
    }

    public function locationLabel(): string
    {
        $country = CompanyProfile::countryLabelFor($this->location_country);
        $parts = array_filter([$this->location_city, $country]);

        return implode(', ', $parts);
    }

    public function contractTypeLabel(): string
    {
        if (! filled($this->contract_type)) {
            return '—';
        }

        return __('talenma.jobs.contract_'.$this->contract_type);
    }

    public function statusLabel(): string
    {
        return __('talenma.jobs.status_'.$this->status);
    }

    public function hasUnseenChangesForCompany(): bool
    {
        if ($this->company_seen_at === null) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $this->company_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }

    public function hasUnseenChangesForStaff(): bool
    {
        if ($this->staff_seen_at === null) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $this->staff_seen_at->getTimestamp() < $this->updated_at->getTimestamp();
    }
}
