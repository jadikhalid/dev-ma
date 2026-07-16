<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'company_name',
    'logo_path',
    'representative_name',
    'representative_email',
    'phone',
    'linkedin_url',
    'sector',
    'registration_sector',
    'profession_sector_id',
    'country',
    'city',
    'description',
    'website',
    'employee_count',
    'hiring_needs',
    'registration_hiring_needs',
])]
class CompanyProfile extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionSector(): BelongsTo
    {
        return $this->belongsTo(ProfessionSector::class);
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyProfileDocument::class)->orderBy('sort_order');
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        // Relative path so the image works regardless of APP_URL host/port.
        return '/storage/'.ltrim($this->logo_path, '/');
    }

    public function initials(): string
    {
        $name = trim((string) ($this->company_name ?: ''));

        if ($name === '') {
            return '—';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : mb_strtoupper(mb_substr($name, 0, 2));
    }
}
