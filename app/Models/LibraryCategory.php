<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LibraryCategory extends Model
{
    public const MAX_DEPTH = 3;

    protected $fillable = [
        'parent_id',
        'slug',
        'name_fr',
        'name_en',
        'depth',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name_fr');
    }

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->where('depth', 1);
    }

    public function scopeLeaves(Builder $query): Builder
    {
        return $query->where('depth', self::MAX_DEPTH);
    }

    public function isLeaf(): bool
    {
        return (int) $this->depth === self::MAX_DEPTH;
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'en' ? $this->name_en : $this->name_fr;
    }

    public static function uniqueSlugForParent(string $name, ?int $parentId, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when(
                    $parentId === null,
                    fn (Builder $q) => $q->whereNull('parent_id'),
                    fn (Builder $q) => $q->where('parent_id', $parentId),
                )
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
