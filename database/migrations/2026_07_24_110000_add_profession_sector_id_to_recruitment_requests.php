<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->foreignId('profession_sector_id')
                ->nullable()
                ->after('developer_user_id')
                ->constrained('profession_sectors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profession_sector_id');
        });
    }
};
