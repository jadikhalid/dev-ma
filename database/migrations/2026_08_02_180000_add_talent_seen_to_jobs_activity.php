<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('talent_seen_at')->nullable()->after('submitted_at');
        });

        // Existing applications: already seen so we do not flood blue badges.
        DB::table('job_applications')->update([
            'talent_seen_at' => DB::raw('COALESCE(updated_at, submitted_at, created_at, CURRENT_TIMESTAMP)'),
        ]);

        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->foreignId('talent_user_id')
                ->nullable()
                ->after('actor_user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->index(['talent_user_id', 'created_at'], 'job_activity_talent_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('talent_user_id');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('talent_seen_at');
        });
    }
};
