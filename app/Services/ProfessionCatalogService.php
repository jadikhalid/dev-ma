<?php

namespace App\Services;

use App\Models\Profession;
use App\Models\ProfessionSector;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProfessionCatalogService
{
    public function sectorsForLocale(?string $locale = null): Collection
    {
        $locale = $locale ?? app()->getLocale();

        return ProfessionSector::query()
            ->where('is_active', true)
            ->with(['professions' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->with(['suggestions' => fn ($suggestions) => $suggestions
                    ->where('is_active', true)
                    ->orderBy('sort_order')])])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProfessionSector $sector) => [
                'slug' => $sector->slug,
                'name' => $sector->localizedName($locale),
                'professions' => $sector->professions->map(fn (Profession $profession) => [
                    'slug' => $profession->slug,
                    'name' => $profession->localizedName($locale),
                    'specializations' => $profession->suggestions
                        ->map(fn ($suggestion) => $suggestion->localizedLabel($locale))
                        ->values(),
                ])->values(),
            ])
            ->values();
    }

    /**
     * @return array{profession_sector_id: int|null, profession_id: int|null, specialization: string|null}
     */
    public function resolveSelection(?string $sectorSlug, ?string $professionSlug, ?string $specialization): array
    {
        if (! $sectorSlug && ! $professionSlug && ! $specialization) {
            return [
                'profession_sector_id' => null,
                'profession_id' => null,
                'specialization' => null,
            ];
        }

        if (! $sectorSlug || ! $professionSlug) {
            throw ValidationException::withMessages([
                'profession' => __('talenma.talent.profession_incomplete'),
            ]);
        }

        $sector = ProfessionSector::query()
            ->where('slug', $sectorSlug)
            ->where('is_active', true)
            ->first();

        if (! $sector) {
            throw ValidationException::withMessages([
                'sector' => __('talenma.talent.sector_invalid'),
            ]);
        }

        $profession = Profession::query()
            ->where('slug', $professionSlug)
            ->where('profession_sector_id', $sector->id)
            ->where('is_active', true)
            ->with(['suggestions' => fn ($q) => $q->where('is_active', true)])
            ->first();

        if (! $profession) {
            throw ValidationException::withMessages([
                'profession' => __('talenma.talent.profession_invalid'),
            ]);
        }

        $specialization = trim((string) $specialization) ?: null;

        if ($specialization) {
            $validLabels = $profession->suggestions
                ->map(fn ($suggestion) => $suggestion->localizedLabel())
                ->all();

            $keywords = array_values(array_filter(array_map(
                fn (string $keyword) => trim($keyword),
                explode(',', $specialization),
            )));

            if ($keywords === []) {
                throw ValidationException::withMessages([
                    'specialization' => __('talenma.talent.specialization_required'),
                ]);
            }

            foreach ($keywords as $keyword) {
                if (! in_array($keyword, $validLabels, true)) {
                    throw ValidationException::withMessages([
                        'specialization' => __('talenma.talent.specialization_invalid'),
                    ]);
                }
            }

            $specialization = implode(', ', $keywords);
        }

        return [
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'specialization' => $specialization,
        ];
    }

    public function slugsFromProfile(?int $sectorId, ?int $professionId): array
    {
        $sectorSlug = $sectorId
            ? ProfessionSector::query()->whereKey($sectorId)->value('slug')
            : null;

        $professionSlug = $professionId
            ? Profession::query()->whereKey($professionId)->value('slug')
            : null;

        return [
            'sector' => $sectorSlug ?? '',
            'profession' => $professionSlug ?? '',
        ];
    }
}
