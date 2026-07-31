<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->timestamp('staff_seen_at')->nullable()->after('company_seen_at');
        });

        // Baseline: existing dossiers should not flood the nav with blue dots.
        DB::table('recruitment_requests')->update([
            'staff_seen_at' => DB::raw('updated_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropColumn('staff_seen_at');
        });
    }
};
