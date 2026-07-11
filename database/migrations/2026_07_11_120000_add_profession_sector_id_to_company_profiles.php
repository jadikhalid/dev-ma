<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->foreignId('profession_sector_id')
                ->nullable()
                ->after('registration_sector')
                ->constrained('profession_sectors')
                ->nullOnDelete();
        });

        // Rattacher les profils existants via le libellé FR/EN du secteur.
        $sectors = DB::table('profession_sectors')->get(['id', 'name_fr', 'name_en', 'slug']);

        foreach ($sectors as $sector) {
            DB::table('company_profiles')
                ->whereNull('profession_sector_id')
                ->where(function ($query) use ($sector) {
                    $query->where('sector', $sector->name_fr)
                        ->orWhere('sector', $sector->name_en)
                        ->orWhere('registration_sector', $sector->name_fr)
                        ->orWhere('registration_sector', $sector->name_en)
                        ->orWhere('registration_sector', $sector->slug)
                        ->orWhere('sector', $sector->slug);
                })
                ->update(['profession_sector_id' => $sector->id]);
        }
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profession_sector_id');
        });
    }
};
