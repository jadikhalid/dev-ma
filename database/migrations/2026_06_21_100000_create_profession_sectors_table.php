<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profession_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_fr');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('professions', function (Blueprint $table) {
            $table->foreignId('profession_sector_id')
                ->nullable()
                ->after('id')
                ->constrained('profession_sectors')
                ->nullOnDelete();

            $table->index(['profession_sector_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('professions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profession_sector_id');
        });

        Schema::dropIfExists('profession_sectors');
    }
};
