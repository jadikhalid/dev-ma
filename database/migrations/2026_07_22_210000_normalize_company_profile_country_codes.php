<?php

use App\Models\CompanyProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('company_profiles', 'country')) {
            return;
        }

        DB::table('company_profiles')
            ->orderBy('id')
            ->each(function (object $profile): void {
                $code = CompanyProfile::normalizeCountryCode($profile->country);

                DB::table('company_profiles')
                    ->where('id', $profile->id)
                    ->update([
                        'country' => $code ?? CompanyProfile::DEFAULT_COUNTRY,
                    ]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('company_profiles', 'country')) {
            return;
        }

        DB::table('company_profiles')
            ->orderBy('id')
            ->each(function (object $profile): void {
                $label = match ($profile->country) {
                    'fr' => 'France',
                    'be' => 'Belgique',
                    'es' => 'Espagne',
                    'it' => 'Italie',
                    'de' => 'Allemagne',
                    'nl' => 'Pays-Bas',
                    'pt' => 'Portugal',
                    'at' => 'Autriche',
                    'ie' => 'Irlande',
                    'pl' => 'Pologne',
                    'se' => 'Suède',
                    'dk' => 'Danemark',
                    'fi' => 'Finlande',
                    'gr' => 'Grèce',
                    'ro' => 'Roumanie',
                    'hu' => 'Hongrie',
                    'cz' => 'Tchéquie',
                    'sk' => 'Slovaquie',
                    'si' => 'Slovénie',
                    'hr' => 'Croatie',
                    'bg' => 'Bulgarie',
                    'lt' => 'Lituanie',
                    'lv' => 'Lettonie',
                    'ee' => 'Estonie',
                    'lu' => 'Luxembourg',
                    'mt' => 'Malte',
                    'cy' => 'Chypre',
                    'ca' => 'Canada',
                    'us' => 'États-Unis',
                    'ma' => 'Maroc',
                    'ae' => 'Émirats arabes unis',
                    'bh' => 'Bahreïn',
                    'kw' => 'Koweït',
                    'om' => 'Oman',
                    'qa' => 'Qatar',
                    'sa' => 'Arabie saoudite',
                    default => 'France',
                };

                DB::table('company_profiles')
                    ->where('id', $profile->id)
                    ->update(['country' => $label]);
            });
    }
};
