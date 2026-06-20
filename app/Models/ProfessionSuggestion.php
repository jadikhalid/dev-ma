<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionSuggestion extends Model
{
    protected $fillable = [
        'profession_id',
        'label_fr',
        'label_en',
        'keywords',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function localizedLabel(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'en' ? $this->label_en : $this->label_fr;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForTerm(Builder $query, string $term, ?string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();
        $labelColumn = $locale === 'en' ? 'label_en' : 'label_fr';
        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $term);

        return $query
            ->where(function (Builder $q) use ($escaped, $labelColumn) {
                $q->where($labelColumn, 'like', $escaped.'%')
                    ->orWhere($labelColumn, 'like', '%'.$escaped.'%')
                    ->orWhere('keywords', 'like', '%'.$escaped.'%');
            })
            ->orderByRaw(
                'CASE WHEN '.$labelColumn.' LIKE ? THEN 0 WHEN '.$labelColumn.' LIKE ? THEN 1 ELSE 2 END',
                [$escaped.'%', '%'.$escaped.'%']
            )
            ->orderBy('sort_order')
            ->orderBy($labelColumn);
    }
}
