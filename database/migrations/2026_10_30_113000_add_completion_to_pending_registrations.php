<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('expires_at');
            $table->foreignId('user_id')->nullable()->after('completed_at')->constrained()->nullOnDelete();
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('completed_at');
        });
    }
};
