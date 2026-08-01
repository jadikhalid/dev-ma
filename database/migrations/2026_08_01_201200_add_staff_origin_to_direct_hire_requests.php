<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->string('hire_origin', 32)->default('company')->after('company_name_snapshot');
            $table->foreignId('initiated_by_user_id')
                ->nullable()
                ->after('hire_origin')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['hire_origin', 'status']);
            $table->index(['initiated_by_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropForeign(['initiated_by_user_id']);
            $table->dropIndex(['hire_origin', 'status']);
            $table->dropIndex(['initiated_by_user_id', 'status']);
            $table->dropColumn(['hire_origin', 'initiated_by_user_id']);
        });
    }
};
