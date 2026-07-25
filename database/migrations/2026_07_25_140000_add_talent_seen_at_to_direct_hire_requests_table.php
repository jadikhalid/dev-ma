<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->timestamp('talent_seen_at')->nullable()->after('closure_note');
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropColumn('talent_seen_at');
        });
    }
};
