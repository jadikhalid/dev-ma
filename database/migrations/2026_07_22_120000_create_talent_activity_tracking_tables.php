<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('viewer_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['talent_user_id', 'created_at']);
            $table->index(['talent_user_id', 'viewer_user_id', 'created_at']);
        });

        Schema::create('profile_document_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_document_id')->constrained('profile_documents')->cascadeOnDelete();
            $table->foreignId('talent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('downloader_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['talent_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_document_downloads');
        Schema::dropIfExists('profile_views');
    }
};
