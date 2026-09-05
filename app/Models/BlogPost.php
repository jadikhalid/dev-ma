<?php

namespace App\Models;

use App\Support\BlogCoverStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const LOCALE_FR = 'fr';

    public const LOCALE_EN = 'en';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_path',
        'locale',
        'status',
        'show_in_ticker',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'show_in_ticker' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (BlogPost $post) {
            BlogCoverStorage::delete($post->cover_path);
            $post->removeFromTicker();
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function coverUrl(): ?string
    {
        return BlogCoverStorage::url($this->cover_path);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function tickerUrl(): string
    {
        return route('blog.show', $this->slug);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        $locale = in_array($locale, [self::LOCALE_FR, self::LOCALE_EN], true)
            ? $locale
            : self::LOCALE_FR;

        return $query->where('locale', $locale);
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function syncTicker(): void
    {
        if ($this->isPublished() && $this->show_in_ticker) {
            $this->pushToTicker();

            return;
        }

        $this->removeFromTicker();
    }

    public function pushToTicker(): void
    {
        $url = $this->tickerUrl();

        $existing = SocialFeedItem::query()
            ->where('source', 'article')
            ->where('url', $url)
            ->first();

        $payload = [
            'title' => $this->title,
            'subtitle' => Str::limit($this->excerpt, 240),
            'url' => $url,
            'source' => 'article',
            // Do not reuse cover_path: SocialFeedItem deletion would remove the blog cover file.
            'thumbnail' => null,
            'created_by' => $this->created_by,
        ];

        if ($existing) {
            $existing->fill($payload)->save();
            SocialFeedItem::pruneExcess();

            return;
        }

        SocialFeedItem::pushItem($payload);
    }

    public function removeFromTicker(): void
    {
        SocialFeedItem::query()
            ->where('source', 'article')
            ->where('url', $this->tickerUrl())
            ->get()
            ->each->delete();
    }
}
