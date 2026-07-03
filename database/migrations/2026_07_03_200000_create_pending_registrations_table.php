<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('email');
            $table->string('locale', 5)->default('fr');
            $table->json('payload');
            $table->json('document_paths')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('email');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
