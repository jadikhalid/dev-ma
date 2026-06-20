<?php

namespace Database\Seeders;

use App\Models\MagazineBannerItem;
use Illuminate\Database\Seeder;

class MagazineBannerSeeder extends Seeder
{
    public function run(): void
    {
        if (MagazineBannerItem::query()->exists()) {
            return;
        }

        $defaults = [
            ['title' => 'Le pool de talents tech au Maroc en 2026', 'subtitle' => 'État des lieux et tendances du marché', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'Remote France-Maroc : les bonnes pratiques', 'subtitle' => 'Fuseaux horaires, outils et communication', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'Pourquoi recruter des talents marocains ?', 'subtitle' => 'Qualité, coût et proximité culturelle', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'S\'installer au Maroc : guide entreprise', 'subtitle' => 'Juridique, fiscal et ressources humaines', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'Laravel & React : profils les plus demandés', 'subtitle' => 'Les stacks plébiscitées par les entreprises françaises', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'Freelance ou CDI remote : que choisir ?', 'subtitle' => 'Comparatif pour les talents et les recruteurs', 'url' => 'https://talentsdumaroc.com/magazine'],
            ['title' => 'L\'écosystème tech de Casablanca en plein essor', 'subtitle' => 'Startups, hubs et opportunités pour les talents', 'url' => 'https://talentsdumaroc.com/magazine'],
        ];

        foreach ($defaults as $item) {
            MagazineBannerItem::pushItem($item);
        }
    }
}
