<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfessionSuggestion;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = require __DIR__.'/data/profession_catalog.php';

        $activeSectorSlugs = [];
        $activeProfessionSlugs = [];

        foreach ($catalog as $sectorIndex => $sectorData) {
            $activeSectorSlugs[] = $sectorData['slug'];

            $sector = ProfessionSector::updateOrCreate(
                ['slug' => $sectorData['slug']],
                [
                    'name_fr' => $sectorData['name_fr'],
                    'name_en' => $sectorData['name_en'],
                    'is_active' => true,
                    'sort_order' => $sectorIndex + 1,
                ]
            );

            foreach ($sectorData['professions'] as $professionIndex => $professionData) {
                $activeProfessionSlugs[] = $professionData['slug'];

                $profession = Profession::updateOrCreate(
                    ['slug' => $professionData['slug']],
                    [
                        'profession_sector_id' => $sector->id,
                        'name_fr' => $professionData['name_fr'],
                        'name_en' => $professionData['name_en'],
                        'is_active' => true,
                        'sort_order' => $professionIndex + 1,
                    ]
                );

                $activeSuggestionKeys = [];

                foreach ($professionData['suggestions'] as $suggestionIndex => $suggestion) {
                    $activeSuggestionKeys[] = $suggestion['label_fr'];

                    ProfessionSuggestion::updateOrCreate(
                        [
                            'profession_id' => $profession->id,
                            'label_fr' => $suggestion['label_fr'],
                        ],
                        [
                            'label_en' => $suggestion['label_en'],
                            'keywords' => $suggestion['keywords'] ?? null,
                            'is_active' => true,
                            'sort_order' => $suggestionIndex + 1,
                        ]
                    );
                }

                ProfessionSuggestion::query()
                    ->where('profession_id', $profession->id)
                    ->whereNotIn('label_fr', $activeSuggestionKeys)
                    ->update(['is_active' => false]);
            }
        }

        ProfessionSector::query()
            ->whereNotIn('slug', $activeSectorSlugs)
            ->update(['is_active' => false]);

        Profession::query()
            ->whereNotIn('slug', $activeProfessionSlugs)
            ->update(['is_active' => false]);

        // Alias historique : « Corps soignant » → Santé & médico-social
        $legacyHealthcare = ProfessionSector::query()->where('slug', 'healthcare')->first();
        $healthSocial = ProfessionSector::query()->where('slug', 'health-social')->first();

        if ($legacyHealthcare && $healthSocial) {
            Profession::query()
                ->where('profession_sector_id', $legacyHealthcare->id)
                ->update(['profession_sector_id' => $healthSocial->id]);

            \App\Models\CompanyProfile::query()
                ->where('profession_sector_id', $legacyHealthcare->id)
                ->update(['profession_sector_id' => $healthSocial->id]);

            \App\Models\Profile::query()
                ->where('profession_sector_id', $legacyHealthcare->id)
                ->update(['profession_sector_id' => $healthSocial->id]);

            $legacyHealthcare->update(['is_active' => false]);
        }

        // Alias historique : « Techniciens & BTP » désactivé (métiers redistribués)
        ProfessionSector::query()
            ->where('slug', 'technicians')
            ->update(['is_active' => false]);

        $this->remapLegacyItProfessions();
    }

    /**
     * Réassocie profils / offres encore liés aux anciens métiers IT (catalogue 2026-06).
     */
    private function remapLegacyItProfessions(): void
    {
        $slugMap = [
            'web-developer' => 'full-stack-developer',
            'mobile-developer' => 'full-stack-developer',
            'data-specialist' => 'data-engineer',
            'designer' => 'content-manager',
            'cybersecurity' => 'cybersecurity-soc',
            'product-manager' => 'product-owner',
        ];

        $professions = Profession::query()
            ->whereIn('slug', array_merge(array_keys($slugMap), array_values($slugMap), ['data-scientist']))
            ->get(['id', 'slug'])
            ->keyBy('slug');

        foreach ($slugMap as $oldSlug => $newSlug) {
            $old = $professions->get($oldSlug);
            $new = $professions->get($newSlug);

            if (! $old || ! $new || $old->id === $new->id) {
                continue;
            }

            \App\Models\Profile::query()
                ->where('profession_id', $old->id)
                ->update(['profession_id' => $new->id]);

            if (\Illuminate\Support\Facades\Schema::hasTable('job_postings')) {
                \App\Models\JobPosting::query()
                    ->where('profession_id', $old->id)
                    ->update(['profession_id' => $new->id]);
            }
        }

        $dataEngineer = $professions->get('data-engineer');
        $dataScientist = $professions->get('data-scientist');

        if (! $dataEngineer || ! $dataScientist) {
            return;
        }

        $scientistPattern = '/scientist|machine learning|deep learning|\bia\b|\bai\b/i';

        \App\Models\Profile::query()
            ->where('profession_id', $dataEngineer->id)
            ->whereNotNull('specialization')
            ->get(['id', 'specialization'])
            ->each(function (\App\Models\Profile $profile) use ($scientistPattern, $dataScientist) {
                if (preg_match($scientistPattern, (string) $profile->specialization)) {
                    $profile->update(['profession_id' => $dataScientist->id]);
                }
            });
    }
}
