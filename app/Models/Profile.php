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
    'work_modes',
    'languages',
    'city',
    'country',
    'skills',
    'github_url',
    'linkedin_url',
    'portfolio_url',
    'phone',
])]
class Profile extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'disponible';

    public const STATUS_BUSY = 'occupé';

    public const STATUS_LISTENING = 'à l\'écoute';

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
            'skills' => 'array',
            'work_modes' => 'array',
            'languages' => 'array',
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

    public function cvDocument(): ?ProfileDocument
    {
        return $this->documents->firstWhere('document_type', ProfileDocument::TYPE_CV);
    }

    public function otherDocuments()
    {
        return $this->documents->where('document_type', ProfileDocument::TYPE_OTHER)->values();
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
