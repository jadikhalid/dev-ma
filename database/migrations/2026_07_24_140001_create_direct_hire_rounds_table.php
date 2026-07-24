<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_hire_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_hire_request_id')->constrained('direct_hire_requests')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('title');
            $table->string('status', 32)->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('company_note')->nullable();
            $table->timestamps();

            $table->unique(['direct_hire_request_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_hire_rounds');
    }
};
