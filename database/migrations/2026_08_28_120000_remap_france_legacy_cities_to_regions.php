<?php

use App\Models\Profile;
use App\Support\FranceRegionCatalog;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (FranceRegionCatalog::legacyCityMap() as $legacyCity => $region) {
            Profile::query()
                ->where('country', Profile::COUNTRY_FR)
                ->where('city', $legacyCity)
                ->update(['city' => $region]);
        }
    }

    public function down(): void
    {
        // Non réversible : plusieurs villes peuvent pointer vers la même région.
    }
};
