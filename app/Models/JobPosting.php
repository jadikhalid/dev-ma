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
    'profession_sector_id',
    'profession_id',
    'experience_level',
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

    public const EXPERIENCE_BEGINNER = 'beginner';

    public const EXPERIENCE_JUNIOR = 'junior';

    public const EXPERIENCE_SENIOR = 'senior';

    public const EXPERIENCE_LEVELS = [
        self::EXPERIENCE_BEGINNER,
        self::EXPERIENCE_JUNIOR,
        self::EXPERIENCE_SENIOR,
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

    public function professionSector(): BelongsTo
    {
        return $this->belongsTo(ProfessionSector::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
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

    public function isMutable(): bool
    {
        return ! $this->isClosed();
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

    public function sectorLabel(?string $locale = null): string
    {
        return $this->professionSector?->localizedName($locale) ?? '';
    }

    public function professionLabel(?string $locale = null): string
    {
        return $this->profession?->localizedName($locale) ?? '';
    }

    public function professionSummary(?string $locale = null): string
    {
        return implode(' · ', array_filter([
            $this->sectorLabel($locale),
            $this->professionLabel($locale),
        ]));
    }

    public function experienceLabel(): string
    {
        if (! filled($this->experience_level) || ! in_array($this->experience_level, self::EXPERIENCE_LEVELS, true)) {
            return '';
        }

        return __('talenma.jobs.experience_'.$this->experience_level);
    }

    /**
     * Exact profile match: sector, profession and experience range.
     */
    public function matchesTalentProfile(?User $talent): bool
    {
        if (! $talent || ! $talent->isTalent()) {
            return false;
        }

        $talent->loadMissing('profile');
        $profile = $talent->profile;

        if (! $profile) {
            return false;
        }

        if (! $this->profession_sector_id
            || (int) $profile->profession_sector_id !== (int) $this->profession_sector_id) {
            return false;
        }

        if (! $this->profession_id
            || (int) $profile->profession_id !== (int) $this->profession_id) {
            return false;
        }

        return $this->experienceLevelMatchesYears(
            $profile->experience_years !== null ? (int) $profile->experience_years : null
        );
    }

    public function experienceLevelMatchesYears(?int $years): bool
    {
        if ($years === null || ! filled($this->experience_level)) {
            return false;
        }

        return match ($this->experience_level) {
            self::EXPERIENCE_BEGINNER => $years >= 0 && $years <= 3,
            self::EXPERIENCE_JUNIOR => $years >= 3 && $years <= 7,
            self::EXPERIENCE_SENIOR => $years > 7,
            default => false,
        };
    }

    /**
     * @return list<string>
     */
    public static function experienceLevelsForYears(?int $years): array
    {
        if ($years === null) {
            return [];
        }

        $levels = [];

        if ($years >= 0 && $years <= 3) {
            $levels[] = self::EXPERIENCE_BEGINNER;
        }

        if ($years >= 3 && $years <= 7) {
            $levels[] = self::EXPERIENCE_JUNIOR;
        }

        if ($years > 7) {
            $levels[] = self::EXPERIENCE_SENIOR;
        }

        return $levels;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeMatchingTalentProfile($query, User $talent)
    {
        $talent->loadMissing('profile');
        $profile = $talent->profile;

        if (! $profile
            || ! $profile->profession_sector_id
            || ! $profile->profession_id
            || $profile->experience_years === null) {
            return $query->whereRaw('0 = 1');
        }

        $levels = self::experienceLevelsForYears((int) $profile->experience_years);

        if ($levels === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query
            ->where('profession_sector_id', $profile->profession_sector_id)
            ->where('profession_id', $profile->profession_id)
            ->whereIn('experience_level', $levels);
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
