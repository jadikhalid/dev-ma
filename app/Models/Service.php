<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug',
    'title',
    'icon',
    'summary',
    'content',
    'translations',
    'sort_order',
    'is_active',
])]
class Service extends Model
{
    use HasFactory, HasTranslations;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'translations' => 'array',
        ];
    }

    protected function localizedTitle(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('title'));
    }

    protected function localizedSummary(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('summary'));
    }

    protected function localizedContent(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('content'));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
