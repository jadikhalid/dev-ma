<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence idempotentes (catalogue, services).
 * Le bandeau d'actualités n'est seedé que si la table est vide.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            ProfessionSeeder::class,
            LibraryCategorySeeder::class,
            SocialFeedSeeder::class,
        ]);
    }
}
