<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('country')->default('Maroc')->after('city');
            $table->json('skills')->nullable()->after('bio');
            $table->string('availability')->default('disponible')->after('daily_rate_eur');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['country', 'skills', 'availability']);
        });
    }
};
