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
    'daily_rate_eur',
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
