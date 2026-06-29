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

        foreach ($catalog as $sectorIndex => $sectorData) {
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

                foreach ($professionData['suggestions'] as $suggestionIndex => $suggestion) {
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
            }
        }
    }
}
