<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profiles')) {
            DB::table('profiles')
                ->where('availability', 'sous 2 semaines')
                ->update(['availability' => 'à l\'écoute']);

            DB::table('profiles')
                ->where('availability', 'mission en cours')
                ->update(['availability' => 'occupé']);

            DB::table('profiles')
                ->where(function ($query) {
                    $query->whereNull('availability')
                        ->orWhereNotIn('availability', ['disponible', 'occupé', 'à l\'écoute']);
                })
                ->update(['availability' => 'disponible']);

            if (Schema::hasColumn('profiles', 'daily_rate_eur')) {
                Schema::table('profiles', function (Blueprint $table) {
                    $table->dropColumn('daily_rate_eur');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('profiles') && ! Schema::hasColumn('profiles', 'daily_rate_eur')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->integer('daily_rate_eur')->nullable()->after('certifications');
            });
        }

        if (Schema::hasTable('profiles')) {
            DB::table('profiles')
                ->where('availability', 'à l\'écoute')
                ->update(['availability' => 'sous 2 semaines']);

            DB::table('profiles')
                ->where('availability', 'occupé')
                ->update(['availability' => 'mission en cours']);
        }
    }
};
