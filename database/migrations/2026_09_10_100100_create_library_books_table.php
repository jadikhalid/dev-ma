<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_category_id')
                ->constrained('library_categories')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime', 100)->default('application/pdf');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['library_category_id', 'is_published']);
            $table->index(['title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
