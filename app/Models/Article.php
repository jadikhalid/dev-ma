<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'slug',
    'category',
    'excerpt',
    'content',
    'translations',
    'cover_emoji',
    'is_published',
    'published_at',
    'author_id',
])]
class Article extends Model
{
    use HasFactory, HasTranslations;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'translations' => 'array',
        ];
    }

    protected function localizedTitle(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('title'));
    }

    protected function localizedExcerpt(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('excerpt'));
    }

    protected function localizedContent(): Attribute
    {
        return Attribute::get(fn () => $this->getTranslated('content'));
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
