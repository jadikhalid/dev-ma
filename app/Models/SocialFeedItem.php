<?php

namespace App\Models;

use App\Support\SocialFeedStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialFeedItem extends Model
{
    public const MAX_ITEMS = 50;

    public const TICKER_LIMIT = 10;

    public const SOURCES = [
        'article',
        'linkedin',
        'facebook',
        'instagram',
        'x',
        'youtube',
        'other',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'url',
        'source',
        'thumbnail',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (SocialFeedItem $item) {
            SocialFeedStorage::delete($item->thumbnail);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function thumbnailUrl(): ?string
    {
        return SocialFeedStorage::url($this->thumbnail);
    }

    public function localizedSourceLabel(): string
    {
        return __('talenma.social_feed.sources.'.$this->source);
    }

    public static function detectSource(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        return match (true) {
            str_contains($host, 'linkedin.com') => 'linkedin',
            str_contains($host, 'facebook.com'), str_contains($host, 'fb.com') => 'facebook',
            str_contains($host, 'instagram.com') => 'instagram',
            str_contains($host, 'twitter.com'), str_contains($host, 'x.com') => 'x',
            str_contains($host, 'youtube.com'), str_contains($host, 'youtu.be') => 'youtube',
            default => 'article',
        };
    }

    public static function forNewsTicker(): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->where('source', 'article')
            ->latest()
            ->limit(self::TICKER_LIMIT)
            ->get();
    }

    public static function forFeed(): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->where('source', 'article')
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
