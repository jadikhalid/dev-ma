<?php

namespace App\Models;

use App\Support\SocialFeedStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    public const MAX_ITEMS = 10;

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

    /**
     * Publications pour le slider d'accueil (avec échantillon si la table est vide).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function forHomeSlider(): \Illuminate\Database\Eloquent\Collection
    {
        $posts = self::forSlider();

        return $posts->isNotEmpty() ? $posts : self::samplePosts();
    }

    /**
     * Échantillon pour la page d'accueil lorsque la table est vide.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function samplePosts(): \Illuminate\Database\Eloquent\Collection
    {
        $now = now();

        $samples = [
            [
                'title' => 'Talents du Maroc — la plateforme qui connecte',
                'subtitle' => 'Découvrez notre vision sur LinkedIn',
                'url' => 'https://www.linkedin.com/company/talents-du-maroc',
                'network' => 'linkedin',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'title' => 'Recruter des talents marocains à distance',
                'subtitle' => 'Nos conseils pour les entreprises européennes',
                'url' => 'https://x.com/talentsdumaroc',
                'network' => 'x',
                'created_at' => $now->copy()->subDays(5),
            ],
            [
                'title' => 'Coulisses d’un entretien avec un talent tech',
                'subtitle' => 'Retour d’expérience sur Instagram',
                'url' => 'https://www.instagram.com/talentsdumaroc',
                'network' => 'instagram',
                'created_at' => $now->copy()->subDays(8),
            ],
            [
                'title' => 'Pourquoi les recruteurs misent sur le Maroc',
                'subtitle' => 'Tendances RH et mobilité internationale',
                'url' => 'https://www.linkedin.com/pulse/talents-du-maroc-recrutement',
                'network' => 'linkedin',
                'created_at' => $now->copy()->subDays(12),
            ],
        ];

        return new \Illuminate\Database\Eloquent\Collection(
            array_map(function (array $attrs) {
                $post = new self($attrs);
                $post->created_at = $attrs['created_at'];
                $post->updated_at = $attrs['created_at'];
                $post->exists = false;

                return $post;
            }, $samples)
        );
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
