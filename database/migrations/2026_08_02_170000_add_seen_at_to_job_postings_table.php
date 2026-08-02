<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->timestamp('company_seen_at')->nullable()->after('closed_at');
            $table->timestamp('staff_seen_at')->nullable()->after('company_seen_at');
        });

        // Existing postings: treat as already seen to avoid mass blue badges.
        DB::table('job_postings')->update([
            'company_seen_at' => DB::raw('COALESCE(updated_at, created_at, CURRENT_TIMESTAMP)'),
            'staff_seen_at' => DB::raw('COALESCE(updated_at, created_at, CURRENT_TIMESTAMP)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn(['company_seen_at', 'staff_seen_at']);
        });
    }
};
