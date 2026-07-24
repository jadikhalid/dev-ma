<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_hire_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('talent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_profile_id')->nullable()->constrained('company_profiles')->nullOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 32)->default('pending_response');
            $table->timestamp('talent_decision_at')->nullable();
            $table->text('talent_decision_note')->nullable();
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closure_note')->nullable();
            $table->timestamps();

            $table->index(['talent_user_id', 'status']);
            $table->index(['company_user_id', 'status']);
            $table->index(['company_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_hire_requests');
    }
};
