<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('developer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('mode', ['direct', 'intermediary'])->default('intermediary');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_requests');
    }
};
