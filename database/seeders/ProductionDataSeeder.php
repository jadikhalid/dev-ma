<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Données de référence pour la production (idempotent).
 * N’inclut pas les factories, les comptes de démonstration, ni l’admin
 * (AdminUserSeeder est one-shot : php artisan db:seed --class=AdminUserSeeder).
 */
class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
        ]);
    }
}
