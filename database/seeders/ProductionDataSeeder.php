<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence pour la production (idempotent).
 * N’inclut pas les factories ni les comptes de démonstration.
 */
class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
