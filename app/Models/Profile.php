<?php

namespace App\Models;

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
    'registration_description',
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
            self::COUNTRY_MA => ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'],
            self::COUNTRY_FR => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Lille'],
            self::COUNTRY_ES => ['Madrid', 'Barcelone', 'Valence', 'Séville', 'Bilbao'],
            self::COUNTRY_BE => ['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Charleroi'],
            self::COUNTRY_DE => ['Berlin', 'Munich', 'Hambourg', 'Francfort', 'Cologne'],
            self::COUNTRY_US => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami'],
            self::COUNTRY_CA => ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Ottawa'],
            self::COUNTRY_OTHER => ['Londres', 'Dubaï', 'Genève', 'Amsterdam', 'Autre'],
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

    public function visibleDisplayName(?User $user = null): string
    {
        $user ??= $this->user;

        if (! $user) {
            return __('talenma.talent.anonymous');
        }

        return $this->isPublic()
            ? $user->name
            : $user->publicDisplayName();
    }

    public function visibleAvatarUrl(?User $user = null): ?string
    {
        $user ??= $this->user;

        if (! $user || $this->isPrivate()) {
            return null;
        }

        return $user->avatarUrl();
    }

    public function employerLabel(): ?string
    {
        if ($this->isPrivate()) {
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
