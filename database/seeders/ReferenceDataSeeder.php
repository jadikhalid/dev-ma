<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence idempotentes (catalogue, services).
 * Les contenus sociaux ne sont seedés que si les tables sont vides
 * (ne jamais écraser les publications admin en production).
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
