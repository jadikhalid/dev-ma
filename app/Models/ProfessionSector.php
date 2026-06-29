<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionSector extends Model
{
    protected $fillable = [
        'slug',
        'name_fr',
        'name_en',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function professions(): HasMany
    {
        return $this->hasMany(Profession::class);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'en' ? $this->name_en : $this->name_fr;
    }
}
