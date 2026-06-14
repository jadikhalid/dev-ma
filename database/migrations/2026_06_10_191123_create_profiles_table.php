<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // Liaison stricte avec l'utilisateur
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title')->nullable(); // ex: "Développeur Full Stack Next.js / Laravel"
            $table->text('bio')->nullable(); // Présentation / Pitch
            $table->integer('experience_years')->default(0);
            $table->integer('daily_rate_eur')->nullable(); // TJM en Euros pour les Européens
            $table->string('city')->nullable(); // Casablanca, Rabat, etc.

            // Liens externes
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
