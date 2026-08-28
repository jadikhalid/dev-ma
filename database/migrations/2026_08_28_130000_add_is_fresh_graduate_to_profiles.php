<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('profiles', 'is_fresh_graduate')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->boolean('is_fresh_graduate')->default(false)->after('experience_years');
            });
        }

        // Nullable must be applied before backfilling null experience_years.
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('experience_years')->nullable()->default(null)->change();
        });

        DB::table('profiles')
            ->where('experience_years', 0)
            ->update([
                'is_fresh_graduate' => true,
                'experience_years' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('profiles')
            ->where('is_fresh_graduate', true)
            ->update(['experience_years' => 0]);

        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('experience_years')->default(0)->nullable(false)->change();
            $table->dropColumn('is_fresh_graduate');
        });
    }
};
