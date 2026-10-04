<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
                if ($sectorSlug) {
                    $q->whereHas('professionSector', fn ($sq) => $sq->where('slug', $sectorSlug));
                }

                if ($country) {
                    $q->where('country', $country);
                }
            })
            ->where(function ($q) {
                $q->whereNotNull('name')->where('name', '!=', '');
            });

        if ($keywords !== []) {
            $query->where(function ($outer) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $keyword);

                    $outer->orWhere(function ($subQ) use ($escaped) {
                        $subQ->where('name', 'like', '%'.$escaped.'%')
                            ->orWhereHas('companyProfile', function ($profileQ) use ($escaped) {
                                $profileQ->where('hiring_needs', 'like', '%'.$escaped.'%')
                                    ->orWhere('description', 'like', '%'.$escaped.'%')
                                    ->orWhere('sector', 'like', '%'.$escaped.'%');
                            });
                    });
                }
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
     * Entreprises visibles publiquement : approuvées, avec une fiche et un nom.
     */
    public function publicCompaniesQuery(): Builder
    {
        return User::query()
            ->where('role', 'company')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->with(['companyProfile.professionSector'])
            ->whereHas('companyProfile')
            ->where(fn ($q) => $q->whereNotNull('name')->where('name', '!=', ''));
    }

    public function isPubliclyVisible(User $company): bool
    {
        return $this->publicCompaniesQuery()->whereKey($company->getKey())->exists();
    }

    /**
     * @return array{id: int, url: string, name: string, initials: string, logo_url: ?string, sector: ?string, country: ?string, city: ?string, employee_count: ?string, excerpt: ?string, open_jobs: int}
     */
    public function presentForDirectory(User $company): array
    {
        $profile = $company->companyProfile;
        $about = trim(strip_tags((string) ($profile?->description ?: $profile?->hiring_needs ?: '')));

        return [
            ...$this->presentForMarquee($company),
            'city' => $profile?->city,
            'employee_count' => $profile?->employee_count,
            'excerpt' => $about !== '' ? Str::limit($about, 150) : null,
            'open_jobs' => (int) ($profile?->open_jobs_count ?? 0),
        ];
    }

    /**
     * Échantillon d'entreprises approuvées pour le bandeau de la page d'accueil.
     *
     * @return Collection<int, array{id?: int, url?: string, name: string, initials: string, logo_url: ?string, sector: ?string, country: ?string}>
     */
    public function featuredForHome(int $limit = 10): Collection
    {
        $companies = $this->publicCompaniesQuery()
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn (User $company) => $this->presentForMarquee($company));

        if ($companies->isNotEmpty()) {
            return $companies;
        }

        return collect($this->sampleFeaturedCompanies())->take($limit);
    }

    /**
     * @return list<array{name: string, initials: string, logo_url: null, sector: string, country: string}>
     */
    private function sampleFeaturedCompanies(): array
    {
        return [
            ['name' => 'Atlas Tech Maroc', 'initials' => 'AT', 'logo_url' => null, 'sector' => 'Technologie', 'country' => 'Maroc'],
            ['name' => 'Méditerranée Pharma', 'initials' => 'MP', 'logo_url' => null, 'sector' => 'Santé', 'country' => 'Maroc'],
            ['name' => 'Casablanca Finance Group', 'initials' => 'CF', 'logo_url' => null, 'sector' => 'Finance', 'country' => 'Maroc'],
            ['name' => 'AgriSouss Export', 'initials' => 'AE', 'logo_url' => null, 'sector' => 'Agroalimentaire', 'country' => 'Maroc'],
            ['name' => 'Rif Energies', 'initials' => 'RE', 'logo_url' => null, 'sector' => 'Énergie', 'country' => 'Maroc'],
            ['name' => 'EuroBuild Partners', 'initials' => 'EB', 'logo_url' => null, 'sector' => 'BTP', 'country' => 'France'],
            ['name' => 'Nova Consulting', 'initials' => 'NC', 'logo_url' => null, 'sector' => 'Conseil', 'country' => 'Belgique'],
            ['name' => 'Horizon Logistics', 'initials' => 'HL', 'logo_url' => null, 'sector' => 'Transport', 'country' => 'Espagne'],
            ['name' => 'Digital Maghreb', 'initials' => 'DM', 'logo_url' => null, 'sector' => 'Numérique', 'country' => 'Maroc'],
            ['name' => 'Blue Ocean Hotels', 'initials' => 'BO', 'logo_url' => null, 'sector' => 'Tourisme', 'country' => 'Maroc'],
            ['name' => 'InnoLab Rabat', 'initials' => 'IR', 'logo_url' => null, 'sector' => 'Recherche', 'country' => 'Maroc'],
            ['name' => 'Green Valley Foods', 'initials' => 'GV', 'logo_url' => null, 'sector' => 'Agroalimentaire', 'country' => 'Maroc'],
        ];
    }

    /**
     * Catalogue des pays disponibles pour le filtre (toujours complet).
     *
     * @return list<array{value: string, label: string}>
     */
    public function availableCountries(): array
    {
        return collect(CompanyProfile::countryOptions())
            ->map(fn (string $label, string $code) => [
                'value' => $code,
                'label' => $label,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{id: int, url: string, name: string, initials: string, logo_url: ?string, sector: ?string, country: ?string}
     */
    private function presentForMarquee(User $company): array
    {
        $profile = $company->companyProfile;

        return [
            'id' => $company->id,
            'url' => route('companies.public.show', $company),
            'name' => $company->name,
            'initials' => $profile?->initials() ?: '—',
            'logo_url' => $profile?->logoUrl(),
            'sector' => $profile?->professionSector
                ? $profile->professionSector->localizedName()
                : ($profile?->sector ?: null),
            'country' => $profile?->countryLabel(),
        ];
    }

    /**
     * @param  list<string>  $keywords
     * @return array<string, mixed>
     */
    private function present(User $company, array $keywords, ?string $sectorSlug): array
    {
        $profile = $company->companyProfile;
        $haystack = mb_strtolower(implode(' ', array_filter([
            $company->name,
            $profile?->sector,
            $profile?->hiring_needs,
            $profile?->description,
            $profile?->countryLabel(),
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

        $needs = trim((string) ($profile?->hiring_needs ?: $profile?->description ?: ''));

        return [
            'id' => $company->id,
            'name' => $company->name,
            'initials' => $profile?->initials() ?: '—',
            'logo_url' => $profile?->logoUrl(),
            'sector' => $profile?->professionSector
                ? $profile->professionSector->localizedName()
                : ($profile?->sector ?: null),
            'country' => $profile?->countryLabel(),
            'city' => $profile?->city,
            'excerpt' => $needs !== '' ? Str::limit(strip_tags($needs), 140) : null,
            'matched_keywords' => $matched,
            'match_score' => $score,
        ];
    }
}
