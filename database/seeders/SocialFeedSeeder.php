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
                'url' => 'https://www.linkedin.com/company/talents-du-maroc',
                'source' => 'linkedin',
            ],
            [
                'title' => 'Remote France-Maroc : les bonnes pratiques',
                'subtitle' => 'Fuseaux horaires, outils et communication',
                'url' => 'https://www.linkedin.com/company/talents-du-maroc',
                'source' => 'linkedin',
            ],
            [
                'title' => 'Pourquoi recruter des talents marocains ?',
                'subtitle' => 'Qualité, coût et proximité culturelle',
                'url' => 'https://www.linkedin.com/company/talents-du-maroc',
                'source' => 'article',
            ],
        ];

        foreach ($defaults as $item) {
            SocialFeedItem::pushItem($item);
        }
    }
}
