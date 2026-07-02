<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('profession_sector_id')
                ->nullable()
                ->after('user_id')
                ->constrained('profession_sectors')
                ->nullOnDelete();
            $table->foreignId('profession_id')
                ->nullable()
                ->after('profession_sector_id')
                ->constrained('professions')
                ->nullOnDelete();
            $table->string('specialization')->nullable()->after('profession_id');
            $table->json('work_modes')->nullable()->after('availability');
            $table->json('languages')->nullable()->after('work_modes');
            $table->string('education_level')->nullable()->after('experience_years');
            $table->text('certifications')->nullable()->after('education_level');
            $table->string('phone', 30)->nullable()->after('portfolio_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profession_id');
            $table->dropConstrainedForeignId('profession_sector_id');
            $table->dropColumn([
                'specialization',
                'work_modes',
                'languages',
                'education_level',
                'certifications',
                'phone',
            ]);
        });
    }
};
