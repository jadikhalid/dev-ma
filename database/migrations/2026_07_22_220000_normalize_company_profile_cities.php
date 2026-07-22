<?php

use App\Models\CompanyProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('company_profiles', 'city')) {
            return;
        }

        DB::table('company_profiles')
            ->orderBy('id')
            ->each(function (object $profile): void {
                $normalized = CompanyProfile::normalizeCityForCountry(
                    $profile->city,
                    $profile->country,
                );

                if ($normalized === $profile->city) {
                    return;
                }

                DB::table('company_profiles')
                    ->where('id', $profile->id)
                    ->update(['city' => $normalized]);
            });
    }

    public function down(): void
    {
        // Irreversible normalization of free-text cities.
    }
};
