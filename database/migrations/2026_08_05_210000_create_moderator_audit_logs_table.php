<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderator_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('moderator_assignment_id')->nullable()->constrained('moderator_assignments')->nullOnDelete();
            $table->string('moderator_name_snapshot');
            $table->string('moderator_email_snapshot');
            $table->string('action', 40);
            $table->json('permissions_snapshot')->nullable();
            $table->json('context')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['moderator_user_id', 'created_at'], 'moderator_audit_user_created_idx');
            $table->index(['action', 'created_at'], 'moderator_audit_action_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderator_audit_logs');
    }
};
