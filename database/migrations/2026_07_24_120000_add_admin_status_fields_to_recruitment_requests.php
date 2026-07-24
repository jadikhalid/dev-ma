<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->text('admin_comment')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('admin_comment');
            $table->foreignId('status_updated_by')
                ->nullable()
                ->after('status_updated_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_updated_by');
            $table->dropColumn(['admin_comment', 'status_updated_at']);
        });
    }
};
