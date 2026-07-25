<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->timestamp('company_seen_at')->nullable()->after('talent_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropColumn('company_seen_at');
        });
    }
};
