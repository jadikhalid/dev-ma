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
            $table->timestamp('talent_locked_at')->nullable()->after('staff_seen_at');
            $table->timestamp('talent_unlocked_at')->nullable()->after('talent_locked_at');
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->timestamp('talent_locked_at')->nullable()->after('company_seen_at');
            $table->timestamp('talent_unlocked_at')->nullable()->after('talent_locked_at');
        });

        // Backfill: successful closures stay locked until the company unlocks.
        DB::table('recruitment_requests')
            ->where('mode', 'named')
            ->whereIn('status', ['completed_successful', 'completed'])
            ->whereNotNull('developer_user_id')
            ->whereNull('talent_locked_at')
            ->update([
                'talent_locked_at' => DB::raw('COALESCE(status_updated_at, updated_at, created_at)'),
            ]);

        DB::table('direct_hire_requests')
            ->where('status', 'hired')
            ->whereNotNull('talent_user_id')
            ->whereNull('talent_locked_at')
            ->update([
                'talent_locked_at' => DB::raw('COALESCE(closed_at, updated_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropColumn(['talent_locked_at', 'talent_unlocked_at']);
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropColumn(['talent_locked_at', 'talent_unlocked_at']);
        });
    }
};
