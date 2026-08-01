<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->timestamp('staff_seen_at')->nullable()->after('company_seen_at');
            $table->index(['hire_origin', 'staff_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropIndex(['hire_origin', 'staff_seen_at']);
            $table->dropColumn('staff_seen_at');
        });
    }
};
