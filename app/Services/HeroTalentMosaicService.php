<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class HeroTalentMosaicService
{
    public const TILE_COUNT = 6;

    /**
     * @return list<array{photo: string, name: string, role: string, city: string, availability: string, is_fake: bool}>
     */
    public function tiles(int $count = self::TILE_COUNT): array
    {
        $count = max(1, $count);

        $realTiles = $this->publicTalentTiles($count);
        $needed = $count - $realTiles->count();

        $tiles = $realTiles->all();

        if ($needed > 0) {
            $tiles = array_merge($tiles, $this->fakeTiles($needed));
        }

        return collect($tiles)
            ->shuffle()
            ->take($count)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{photo: string, name: string, role: string, city: string, availability: string, is_fake: bool}>
     */
    private function publicTalentTiles(int $limit): Collection
    {
        return User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->whereNotNull('avatar_path')
            ->where('avatar_path', '!=', '')
            ->whereHas('profile', fn ($query) => $query->where('is_public', true))
            ->with(['profile.profession'])
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function (User $talent) {
                $profile = $talent->profile;
                $photo = $talent->avatarUrl();

                if (! $photo) {
                    return null;
                }

                return [
                    'photo' => $photo,
                    'name' => $talent->publicDisplayName(),
                    'role' => $profile?->professionLabel() ?: __('talenma.home.hero_role_fallback'),
                    'city' => filled($profile?->city) ? $profile->city : __('talenma.home.hero_city_fallback'),
                    'availability' => $profile?->statusTone() ?? 'available',
                    'is_fake' => false,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return list<array{photo: string, name: string, role: string, city: string, availability: string, is_fake: bool}>
     */
    private function fakeTiles(int $needed): array
    {
        $photos = collect(config('talenma.hero_fake_photos', []))
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->values();

        if ($photos->isEmpty()) {
            $photos = collect([config('talenma.hero_fallback_photo', 'images/hero/karim.jpg')]);
        }

        $profiles = collect(__('talenma.home.hero_profiles'));
        if ($profiles->isEmpty()) {
            $profiles = collect([[
                'name' => 'Talent',
                'role' => __('talenma.home.hero_role_fallback'),
                'city' => __('talenma.home.hero_city_fallback'),
                'availability' => 'available',
            ]]);
        }

        $photos = $photos->shuffle()->values();
        $profiles = $profiles->shuffle()->values();

        $tiles = [];

        for ($i = 0; $i < $needed; $i++) {
            $profile = $profiles[$i % $profiles->count()];
            $photo = $photos[$i % $photos->count()];

            $tiles[] = [
                'photo' => asset($photo),
                'name' => (string) ($profile['name'] ?? 'Talent'),
                'role' => (string) ($profile['role'] ?? __('talenma.home.hero_role_fallback')),
                'city' => (string) ($profile['city'] ?? __('talenma.home.hero_city_fallback')),
                'availability' => (string) ($profile['availability'] ?? 'available'),
                'is_fake' => true,
            ];
        }

        return $tiles;
    }
}
