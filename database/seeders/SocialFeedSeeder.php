<?php

namespace Database\Seeders;

use App\Models\SocialFeedItem;
use Illuminate\Database\Seeder;

class SocialFeedSeeder extends Seeder
{
    public function run(): void
    {
        if (SocialFeedItem::query()->exists()) {
            return;
        }

        $defaults = [
            [
                'title' => 'Le pool de talents tech au Maroc en 2026',
                'subtitle' => 'État des lieux et tendances du marché',
                'url' => 'https://www.talentsdumaroc.com/actualites/pool-talents-tech-2026',
                'source' => 'article',
            ],
            [
                'title' => 'Remote France-Maroc : les bonnes pratiques',
                'subtitle' => 'Fuseaux horaires, outils et communication',
                'url' => 'https://www.talentsdumaroc.com/actualites/remote-france-maroc',
                'source' => 'article',
            ],
            [
                'title' => 'Pourquoi recruter des talents marocains ?',
                'subtitle' => 'Qualité, coût et proximité culturelle',
                'url' => 'https://www.talentsdumaroc.com/actualites/recruter-talents-marocains',
                'source' => 'article',
            ],
        ];

        foreach ($defaults as $item) {
            SocialFeedItem::pushItem($item);
        }
    }
}
