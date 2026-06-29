<?php

namespace App\Models;

use App\Support\SocialFeedStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    public const MAX_ITEMS = 50;

    public const NETWORKS = [
        'linkedin',
        'x',
        'instagram',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'url',
        'network',
        'thumbnail',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (SocialPost $post) {
            SocialFeedStorage::delete($post->thumbnail);
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

    public function localizedNetworkLabel(): string
    {
        return __('talenma.social_feed.sources.'.$this->network);
    }

    public static function detectNetwork(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        if (str_contains($host, 'linkedin.com')) {
            return 'linkedin';
        }

        if (str_contains($host, 'twitter.com') || str_contains($host, 'x.com')) {
            return 'x';
        }

        if (str_contains($host, 'instagram.com')) {
            return 'instagram';
        }

        return 'linkedin';
    }

    public static function forSlider(): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->latest()
            ->limit(self::MAX_ITEMS)
            ->get();
    }

    public static function pushPost(array $attributes): self
    {
        $post = self::create($attributes);

        $idsToKeep = self::query()
            ->latest()
            ->limit(self::MAX_ITEMS)
            ->pluck('id');

        self::query()
            ->whereNotIn('id', $idsToKeep)
            ->get()
            ->each->delete();

        return $post;
    }
}
