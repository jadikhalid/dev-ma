<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_fr');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('profession_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profession_id')->constrained()->cascadeOnDelete();
            $table->string('label_fr');
            $table->string('label_en');
            $table->string('keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['profession_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profession_suggestions');
        Schema::dropIfExists('professions');
    }
};
