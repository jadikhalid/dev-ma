<?php

namespace App\Services;

use App\Models\Profession;
use App\Models\User;
use Illuminate\Support\Collection;

class TalentSearchService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function search(?string $sectorSlug, ?string $professionSlug, array $keywords, int $limit = 24): Collection
    {
        $keywords = array_values(array_filter(array_map(
            fn (string $keyword) => trim($keyword),
            $keywords,
        )));

        $query = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->with(['profile.profession', 'profile.professionSector', 'profile.documents'])
            ->whereHas('profile', fn ($q) => $q->whereNotNull('profession_id')->whereNotNull('bio'));

        if ($sectorSlug) {
            $query->whereHas('profile.professionSector', fn ($q) => $q->where('slug', $sectorSlug));
        }

        if ($professionSlug) {
            $profession = Profession::query()
                ->where('slug', $professionSlug)
                ->where('is_active', true)
                ->first();

            if ($profession) {
                $query->whereHas('profile', fn ($q) => $q->where('profession_id', $profession->id));
            }
        }

        if ($keywords !== []) {
            $query->whereHas('profile', function ($q) use ($keywords) {
                $q->where(function ($outer) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $keyword);

                        $outer->orWhere(function ($subQ) use ($escaped) {
                            $subQ->where('specialization', 'like', '%'.$escaped.'%')
                                ->orWhere('bio', 'like', '%'.$escaped.'%')
                                ->orWhere('skills', 'like', '%'.$escaped.'%');
                        });
                    }
                });
            });
        }

        return $query
            ->limit(80)
            ->get()
            ->map(fn (User $talent) => $this->present($talent, $keywords, $sectorSlug, $professionSlug))
            ->sortByDesc('match_score')
            ->values()
            ->take($limit);
    }

    /**
     * Aperçu public anonymisé (accueil) : 2 talents max, initiales uniquement.
     *
     * @return array{count: int, results: list<array<string, mixed>>}
     */
    public function preview(?string $sectorSlug, ?string $professionSlug, array $keywords, int $limit = 2): array
    {
        $ranked = $this->search($sectorSlug, $professionSlug, $keywords, 80);

        $results = $ranked
            ->take($limit)
            ->map(function (array $talent) {
                $base = [
                    'id' => $talent['id'],
                    'initials' => $talent['initials'],
                    'display_name' => $talent['display_name'],
                    'specialization' => $talent['specialization'],
                    'sector' => $talent['sector'],
                    'profession' => $talent['profession'],
                    'experience_years' => $talent['experience_years'],
                    'experience_label' => $talent['experience_label'],
                    'availability' => $talent['availability'],
                    'availability_label' => $talent['availability_label'],
                    'availability_tone' => $talent['availability_tone'],
                    'status_rank' => $talent['status_rank'],
                    'match_score' => $talent['match_score'],
                    'is_public' => $talent['is_public'] ?? false,
                    'employer_label' => $talent['employer_label'] ?? null,
                ];

                if (! empty($talent['is_public'])) {
                    $base['name'] = $talent['name'];
                    $base['avatar_url'] = $talent['avatar_url'];
                    $base['city'] = $talent['city'] ?? null;
                    $base['country'] = $talent['country'] ?? null;
                    $base['profile_url'] = $talent['profile_url'] ?? null;
                    $base['cv_url'] = $talent['cv_url'] ?? null;
                }

                return $base;
            })
            ->values()
            ->all();

        return [
            'count' => $ranked->count(),
            'results' => $results,
        ];
    }

    /**
     * @param  list<string>  $keywords
     * @return array<string, mixed>
     */
    private function present(User $talent, array $keywords, ?string $sectorSlug, ?string $professionSlug): array
    {
        $profile = $talent->profile;
        $haystack = mb_strtolower(implode(' ', array_filter([
            $profile?->specialization,
            $profile?->bio,
            is_array($profile?->skills) ? implode(' ', $profile->skills) : '',
            $profile?->professionLabel(),
            $profile?->sectorLabel(),
        ])));

        $matchedKeywords = [];
        $score = 0;

        if ($sectorSlug && $profile?->professionSector?->slug === $sectorSlug) {
            $score += 10;
        }

        if ($professionSlug && $profile?->profession?->slug === $professionSlug) {
            $score += 20;
        }

        foreach ($keywords as $keyword) {
            $needle = mb_strtolower($keyword);

            if ($needle === '' || ! str_contains($haystack, $needle)) {
                continue;
            }

            $matchedKeywords[] = $keyword;
            $score += 15;

            if ($profile?->specialization && str_contains(mb_strtolower($profile->specialization), $needle)) {
                $score += 10;
            }

            if (is_array($profile?->skills)) {
                foreach ($profile->skills as $skill) {
                    if (str_contains(mb_strtolower((string) $skill), $needle)) {
                        $score += 8;
                        break;
                    }
                }
            }
        }

        $experienceYears = $profile?->experience_years;
        $cv = $profile?->cvDocument();
        $availability = $profile?->availability;

        return [
            'id' => $talent->id,
            'name' => $profile?->visibleDisplayName($talent) ?? $talent->publicDisplayName(),
            'display_name' => $talent->publicDisplayName(),
            'initials' => $talent->initials(),
            'avatar_url' => $profile?->visibleAvatarUrl($talent),
            'is_public' => $profile?->isPublic() ?? false,
            'employer_label' => $profile?->employerLabel(),
            'specialization' => $profile?->specialization,
            'skills' => $profile?->skills ?? [],
            'city' => ($profile?->isPublic() ?? false) ? $profile?->city : null,
            'country' => $profile?->country,
            'experience_years' => $experienceYears,
            'experience_label' => $experienceYears !== null
                ? __('talenma.talents.experience', ['years' => $experienceYears])
                : null,
            'availability' => $availability,
            'availability_label' => $profile?->statusLabel(),
            'availability_tone' => $profile?->statusTone(),
            'status_rank' => match ($availability) {
                \App\Models\Profile::STATUS_AVAILABLE => 0,
                \App\Models\Profile::STATUS_LISTENING => 1,
                \App\Models\Profile::STATUS_BUSY => 2,
                default => 3,
            },
            'sector' => $profile?->sectorLabel(),
            'profession' => $profile?->professionLabel(),
            'match_score' => $score,
            'matched_keywords' => $matchedKeywords,
            'cv_url' => (($profile?->isPublic() ?? false) && $cv) ? route('company.talent.cv', $talent) : null,
            'profile_url' => route('company.talent.show', $talent),
        ];
    }
}
