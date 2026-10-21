<?php

namespace App\Services;

use App\Models\JobPosting;
use Illuminate\Support\Collection;

class HomeRecruitingCompanyService
{
    /**
     * Companies with a published job, platform or external.
     * Recent advertisers are more likely to be picked, then the selection is shuffled.
     *
     * @return Collection<int, array{name: string, logo_url: ?string, banner_url: ?string, initials: string, url: string}>
     */
    public function forHome(bool $talentJobs, int $limit = 9): Collection
    {
        return $this->pick($this->advertisers(), $limit)
            ->map(function (array $company) use ($talentJobs) {
                $company['url'] = $talentJobs
                    ? route('talent.jobs.index', ['q' => $company['name']])
                    : route('career.offers', ['q' => $company['name']]);

                return $company;
            })
            ->values();
    }

    /**
     * @return Collection<int, array{name: string, logo_url: ?string, banner_url: ?string, initials: string, published_at: \Illuminate\Support\Carbon|null}>
     */
    public function advertisers(): Collection
    {
        $groups = [];

        JobPosting::query()
            ->with(['companyProfile.user'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->each(function (JobPosting $job) use (&$groups): void {
                $name = $job->advertiserName();
                $key = mb_strtolower($name);

                if ($name === '' || $name === '—' || mb_strlen($name) < 2) {
                    return;
                }

                $publishedAt = $job->published_at ?? $job->created_at;
                $bannerUrl = $job->advertiserBannerUrl();

                if (isset($groups[$key]) && $groups[$key]['published_at'] !== null && $publishedAt !== null && $groups[$key]['published_at']->greaterThanOrEqualTo($publishedAt)) {
                    if ($groups[$key]['banner_url'] === null && $bannerUrl !== null) {
                        $groups[$key]['banner_url'] = $bannerUrl;
                    }

                    return;
                }

                $groups[$key] = [
                    'name' => $name,
                    'logo_url' => $job->advertiserLogoUrl(),
                    'banner_url' => $bannerUrl ?? ($groups[$key]['banner_url'] ?? null),
                    'initials' => $job->advertiserInitials(),
                    'published_at' => $publishedAt,
                ];
            });

        return collect($groups)
            ->filter(fn (array $company): bool => filled($company['banner_url']))
            ->sortByDesc(fn (array $company) => $company['published_at']?->getTimestamp() ?? 0)
            ->values();
    }

    /**
     * @param  Collection<int, array{name: string, logo_url: ?string, banner_url: ?string, initials: string, published_at: mixed}>  $advertisers
     * @return Collection<int, array{name: string, logo_url: ?string, banner_url: ?string, initials: string}>
     */
    public function pick(Collection $advertisers, int $limit, ?callable $roll = null): Collection
    {
        $pool = $advertisers->values()->map(function (array $company, int $index) use ($advertisers) {
            $company['weight'] = max(1, $advertisers->count() - $index);

            return $company;
        });

        $roll ??= fn (int $total): int => random_int(1, $total);
        $selected = collect();

        while ($selected->count() < $limit && $pool->isNotEmpty()) {
            $total = (int) $pool->sum('weight');
            $target = max(1, min($total, (int) $roll($total)));
            $cursor = 0;
            $chosenKey = $pool->keys()->first();

            foreach ($pool as $key => $company) {
                $cursor += $company['weight'];

                if ($cursor >= $target) {
                    $chosenKey = $key;
                    break;
                }
            }

            $selected->push($pool->get($chosenKey));
            $pool->forget($chosenKey);
        }

        return $selected
            ->shuffle()
            ->map(function (array $company) {
                unset($company['weight'], $company['published_at']);

                return $company;
            })
            ->values();
    }
}
