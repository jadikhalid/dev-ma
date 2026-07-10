<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence pour la production (idempotent).
 * N’inclut pas les factories ni la réécriture du compte admin.
 */
class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            ProfessionSeeder::class,
            SocialFeedSeeder::class,
            SocialPostSeeder::class,
            DevTalentSeeder::class,
        ]);
    }
}
