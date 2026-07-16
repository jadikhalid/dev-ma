<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence idempotentes (catalogue, services, contenus sociaux).
 * Sûr pour la production.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            ProfessionSeeder::class,
            SocialFeedSeeder::class,
            SocialPostSeeder::class,
        ]);
    }
}
