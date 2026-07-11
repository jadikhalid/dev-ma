<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CompanyCatalogSearchService
{
    /**
     * @param  list<string>  $keywords
     * @return Collection<int, array<string, mixed>>
     */
    public function search(?string $sectorSlug, array $keywords, ?string $country = null, int $limit = 24): Collection
    {
        $keywords = array_values(array_filter(array_map(
            fn (string $keyword) => trim($keyword),
            $keywords,
        )));

        $query = User::query()
            ->where('role', 'company')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->with(['companyProfile.professionSector'])
            ->whereHas('companyProfile', function ($q) use ($sectorSlug, $country) {
                $q->whereNotNull('company_name');

                if ($sectorSlug) {
                    $q->whereHas('professionSector', fn ($sq) => $sq->where('slug', $sectorSlug));
                }

                if ($country) {
                    $q->where('country', $country);
                }
            });

        if ($keywords !== []) {
            $query->whereHas('companyProfile', function ($q) use ($keywords) {
                $q->where(function ($outer) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $keyword);

                        $outer->orWhere(function ($subQ) use ($escaped) {
                            $subQ->where('company_name', 'like', '%'.$escaped.'%')
                                ->orWhere('hiring_needs', 'like', '%'.$escaped.'%')
                                ->orWhere('registration_hiring_needs', 'like', '%'.$escaped.'%')
                                ->orWhere('description', 'like', '%'.$escaped.'%')
                                ->orWhere('sector', 'like', '%'.$escaped.'%');
                        });
                    }
                });
            });
        }

        return $query
            ->limit(80)
            ->get()
            ->map(fn (User $company) => $this->present($company, $keywords, $sectorSlug))
            ->sortByDesc('match_score')
            ->values()
            ->take($limit);
    }

    /**
     * @param  list<string>  $keywords
     * @return array{count: int, results: list<array<string, mixed>>}
     */
    public function preview(?string $sectorSlug, array $keywords, ?string $country = null, int $limit = 8): array
    {
        $ranked = $this->search($sectorSlug, $keywords, $country, 80);

        $results = $ranked
            ->take($limit)
            ->map(fn (array $company) => [
                'id' => $company['id'],
                'name' => $company['name'],
                'initials' => $company['initials'],
                'logo_url' => $company['logo_url'],
                'sector' => $company['sector'],
                'country' => $company['country'],
                'city' => $company['city'],
                'excerpt' => $company['excerpt'],
                'matched_keywords' => $company['matched_keywords'],
                'match_score' => $company['match_score'],
            ])
            ->values()
            ->all();

        return [
            'count' => $ranked->count(),
            'results' => $results,
        ];
    }

    /**
     * Pays distincts des entreprises approuvées (pour le filtre).
     *
     * @return list<string>
     */
    public function availableCountries(): array
    {
        return CompanyProfile::query()
            ->whereHas('user', fn ($q) => $q
                ->where('role', 'company')
                ->where('approval_status', User::APPROVAL_APPROVED))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $keywords
     * @return array<string, mixed>
     */
    private function present(User $company, array $keywords, ?string $sectorSlug): array
    {
        $profile = $company->companyProfile;
        $haystack = mb_strtolower(implode(' ', array_filter([
            $profile?->company_name,
            $profile?->sector,
            $profile?->hiring_needs,
            $profile?->registration_hiring_needs,
            $profile?->description,
        ])));

        $matched = [];
        $score = 0;

        if ($sectorSlug && $profile?->professionSector?->slug === $sectorSlug) {
            $score += 40;
        }

        foreach ($keywords as $keyword) {
            if ($keyword !== '' && str_contains($haystack, mb_strtolower($keyword))) {
                $matched[] = $keyword;
                $score += 20;
            }
        }

        $needs = trim((string) ($profile?->hiring_needs ?: $profile?->registration_hiring_needs ?: $profile?->description ?: ''));

        return [
            'id' => $company->id,
            'name' => $profile?->company_name ?: $company->name,
            'initials' => $profile?->initials() ?: '—',
            'logo_url' => $profile?->logoUrl(),
            'sector' => $profile?->professionSector
                ? $profile->professionSector->localizedName()
                : ($profile?->sector ?: null),
            'country' => $profile?->country,
            'city' => $profile?->city,
            'excerpt' => $needs !== '' ? Str::limit(strip_tags($needs), 140) : null,
            'matched_keywords' => $matched,
            'match_score' => $score,
        ];
    }
}
