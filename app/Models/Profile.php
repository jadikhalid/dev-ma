<?php

namespace App\Models;

use App\Support\ProfileCityCatalog;
use App\Support\UsStateCatalog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'profession_sector_id',
    'profession_id',
    'specialization',
    'bio',
    'experience_years',
    'education_level',
    'certifications',
    'availability',
    'is_public',
    'work_modes',
    'languages',
    'country',
    'city',
    'github_url',
    'linkedin_url',
    'portfolio_url',
    'phone',
    'whatsapp',
    'presentation_video_url',
    'presentation_video_public_id',
])]
class Profile extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'disponible';

    public const STATUS_BUSY = 'occupé';

    public const STATUS_LISTENING = 'à l\'écoute';

    public const COUNTRY_MA = 'ma';

    public const COUNTRY_FR = 'fr';

    public const COUNTRY_ES = 'es';

    public const COUNTRY_BE = 'be';

    public const COUNTRY_DE = 'de';

    public const COUNTRY_US = 'us';

    public const COUNTRY_CA = 'ca';

    public const COUNTRY_OTHER = 'other';

    /**
     * @return array<string, string>
     */
    public static function countryOptions(): array
    {
        return [
            self::COUNTRY_MA => __('talenma.talent.country_ma'),
            self::COUNTRY_FR => __('talenma.talent.country_fr'),
            self::COUNTRY_ES => __('talenma.talent.country_es'),
            self::COUNTRY_BE => __('talenma.talent.country_be'),
            self::COUNTRY_DE => __('talenma.talent.country_de'),
            self::COUNTRY_US => __('talenma.talent.country_us'),
            self::COUNTRY_CA => __('talenma.talent.country_ca'),
            self::COUNTRY_OTHER => __('talenma.talent.country_other'),
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function citiesByCountry(): array
    {
        return [
            self::COUNTRY_MA => ProfileCityCatalog::forCountry(self::COUNTRY_MA),
            self::COUNTRY_FR => ProfileCityCatalog::forCountry(self::COUNTRY_FR),
            self::COUNTRY_ES => ProfileCityCatalog::forCountry(self::COUNTRY_ES),
            self::COUNTRY_BE => ProfileCityCatalog::forCountry(self::COUNTRY_BE),
            self::COUNTRY_DE => ProfileCityCatalog::forCountry(self::COUNTRY_DE),
            self::COUNTRY_US => UsStateCatalog::labels(),
            self::COUNTRY_CA => ProfileCityCatalog::forCountry(self::COUNTRY_CA),
            self::COUNTRY_OTHER => ProfileCityCatalog::forCountry(self::COUNTRY_OTHER),
        ];
    }

    /**
     * @return list<string>
     */
    public static function citiesForCountry(?string $country): array
    {
        if (! filled($country)) {
            return [];
        }

        return self::citiesByCountry()[$country] ?? [];
    }

    public function countryLabel(): ?string
    {
        if (! filled($this->country)) {
            return null;
        }

        return self::countryOptions()[$this->country] ?? $this->country;
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'available',
            self::STATUS_BUSY => 'busy',
            self::STATUS_LISTENING => 'listening',
        ];
    }

    public function statusLabel(): string
    {
        $key = self::statusOptions()[$this->availability] ?? 'available';

        return __('talenma.talent.'.$key);
    }

    public function educationLabel(): ?string
    {
        if (! filled($this->education_level)) {
            return null;
        }

        $key = match ($this->education_level) {
            'bac+2' => 'education_bac2',
            'bac+3' => 'education_bac3',
            'bac+5' => 'education_bac5',
            'doctorate' => 'education_doctorate',
            'other' => 'education_other',
            default => null,
        };

        return $key ? __('talenma.talent.'.$key) : $this->education_level;
    }

    public function experienceLabel(): string
    {
        return self::experienceLabelFor($this->experience_years);
    }

    public static function experienceLabelFor(?int $years): string
    {
        if ($years === null) {
            return '';
        }

        if ($years === 0) {
            return __('talenma.talents.experience_fresh_graduate');
        }

        return __('talenma.talents.experience', ['years' => $years]);
    }

    public function statusTone(): string
    {
        return match ($this->availability) {
            self::STATUS_BUSY => 'busy',
            self::STATUS_LISTENING => 'listening',
            default => 'available',
        };
    }

    public function isPublic(): bool
    {
        return (bool) $this->is_public;
    }

    public function isPrivate(): bool
    {
        return ! $this->isPublic();
    }

    /**
     * Whether this profile should be shown with full public details to a viewer
     * (public profile, or company with an open direct-hire process with this talent).
     */
    public function isRevealedAsPublic(bool $forceReveal = false): bool
    {
        return $this->isPublic() || $forceReveal;
    }

    public function visibleDisplayName(?User $user = null, bool $forceReveal = false): string
    {
        $user ??= $this->user;

        if (! $user) {
            return __('talenma.talent.anonymous');
        }

        return $this->isRevealedAsPublic($forceReveal)
            ? $user->formalDisplayName()
            : $user->publicDisplayName();
    }

    public function visibleAvatarUrl(?User $user = null, bool $forceReveal = false): ?string
    {
        $user ??= $this->user;

        if (! $user || ! $this->isRevealedAsPublic($forceReveal)) {
            return null;
        }

        return $user->avatarUrl();
    }

    public function employerLabel(bool $forceReveal = false): ?string
    {
        if (! $this->isRevealedAsPublic($forceReveal)) {
            return __('talenma.talent.employer_confidential');
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function workModeLabels(): array
    {
        return collect($this->work_modes ?? [])
            ->map(fn (string $mode) => self::labelForWorkMode($mode))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function languageLabels(): array
    {
        return collect($this->languages ?? [])
            ->map(fn (string $code) => self::labelForLanguage($code))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Collaboration modes offered on talent fiche and job postings.
     *
     * @return array<string, string>
     */
    public static function workModeOptions(): array
    {
        return [
            'remote' => __('talenma.talent.work_mode_remote'),
            'visa_sponsorship' => __('talenma.talent.work_mode_visa'),
            'local' => __('talenma.talent.work_mode_local'),
        ];
    }

    public static function labelForWorkMode(string $mode): string
    {
        $key = match (strtolower(trim($mode))) {
            'remote', 'full_remote', 'full remote' => 'work_mode_remote',
            'hybrid' => 'work_mode_hybrid',
            'visa_sponsorship', 'visa' => 'work_mode_visa',
            'local', 'onsite', 'on_site' => 'work_mode_local',
            default => null,
        };

        return $key ? __('talenma.talent.'.$key) : $mode;
    }

    public static function labelForLanguage(string $code): string
    {
        $normalized = mb_strtolower(trim($code));

        $key = match ($normalized) {
            'fr', 'français', 'francais', 'french' => 'lang_fr',
            'en', 'anglais', 'english' => 'lang_en',
            'ar', 'arabe', 'arabic' => 'lang_ar',
            'es', 'espagnol', 'spanish' => 'lang_es',
            'de', 'allemand', 'german' => 'lang_de',
            default => null,
        };

        return $key ? __('talenma.talent.'.$key) : $code;
    }

    protected function casts(): array
    {
        return [
            'work_modes' => 'array',
            'languages' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionSector(): BelongsTo
    {
        return $this->belongsTo(ProfessionSector::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProfileDocument::class)->orderBy('sort_order');
    }

    public function cvDocument(?string $language = null): ?ProfileDocument
    {
        $cvs = $this->documents->where('document_type', ProfileDocument::TYPE_CV);

        if (filled($language)) {
            return $cvs->firstWhere('language', $language);
        }

        foreach (ProfileDocument::CV_LANGUAGES as $preferred) {
            $match = $cvs->firstWhere('language', $preferred);

            if ($match) {
                return $match;
            }
        }

        return $cvs->first();
    }

    /**
     * @return \Illuminate\Support\Collection<int, ProfileDocument>
     */
    public function cvDocuments()
    {
        return $this->documents
            ->where('document_type', ProfileDocument::TYPE_CV)
            ->sortBy(function (ProfileDocument $document) {
                $index = array_search($document->language, ProfileDocument::CV_LANGUAGES, true);

                return $index === false ? 99 : $index;
            })
            ->values();
    }

    public function registrationDocuments()
    {
        return $this->documents->where('document_type', ProfileDocument::TYPE_REGISTRATION)->values();
    }

    public function professionLabel(?string $locale = null): ?string
    {
        return $this->profession?->localizedName($locale);
    }

    public function sectorLabel(?string $locale = null): ?string
    {
        return $this->professionSector?->localizedName($locale);
    }
}
