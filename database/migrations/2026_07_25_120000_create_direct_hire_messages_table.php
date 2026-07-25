<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_hire_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_hire_request_id')->constrained('direct_hire_requests')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index(['direct_hire_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_hire_messages');
    }
};
