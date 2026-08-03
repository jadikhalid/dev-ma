<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreignId('profession_sector_id')
                ->nullable()
                ->after('created_by')
                ->constrained('profession_sectors')
                ->nullOnDelete();
            $table->foreignId('profession_id')
                ->nullable()
                ->after('profession_sector_id')
                ->constrained('professions')
                ->nullOnDelete();

            $table->index(['profession_sector_id', 'profession_id'], 'job_postings_profession_idx');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex('job_postings_profession_idx');
            $table->dropConstrainedForeignId('profession_id');
            $table->dropConstrainedForeignId('profession_sector_id');
        });
    }
};
