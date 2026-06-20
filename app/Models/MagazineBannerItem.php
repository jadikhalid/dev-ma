<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\MagazineBannerStorage;

class MagazineBannerItem extends Model
{
    public const MAX_ITEMS = 10;

    protected $fillable = [
        'title',
        'subtitle',
        'url',
        'thumbnail',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (MagazineBannerItem $item) {
            MagazineBannerStorage::delete($item->thumbnail);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function thumbnailUrl(): ?string
    {
        return MagazineBannerStorage::url($this->thumbnail);
    }

    public static function forBanner(): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->latest()
            ->limit(self::MAX_ITEMS)
            ->get();
    }

    public static function pushItem(array $attributes): self
    {
        $item = self::create($attributes);

        $idsToKeep = self::query()
            ->latest()
            ->limit(self::MAX_ITEMS)
            ->pluck('id');

        self::query()
            ->whereNotIn('id', $idsToKeep)
            ->get()
            ->each->delete();

        return $item;
    }
}
