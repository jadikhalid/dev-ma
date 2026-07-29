<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY status ENUM(
                'pending',
                'in_progress',
                'completed',
                'cancelled',
                'completed_successful',
                'completed_unsuccessful'
            ) NOT NULL DEFAULT 'pending'");
        }

        DB::table('recruitment_requests')
            ->where('status', 'completed')
            ->update(['status' => 'completed_successful']);

        DB::table('recruitment_requests')
            ->where('status', 'cancelled')
            ->update(['status' => 'completed_unsuccessful']);

        DB::table('recruitment_request_status_events')
            ->where('status', 'completed')
            ->update(['status' => 'completed_successful']);

        DB::table('recruitment_request_status_events')
            ->where('status', 'cancelled')
            ->update(['status' => 'completed_unsuccessful']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY status ENUM(
                'pending',
                'in_progress',
                'completed_successful',
                'completed_unsuccessful'
            ) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY status ENUM(
                'pending',
                'in_progress',
                'completed',
                'cancelled',
                'completed_successful',
                'completed_unsuccessful'
            ) NOT NULL DEFAULT 'pending'");
        }

        DB::table('recruitment_requests')
            ->where('status', 'completed_successful')
            ->update(['status' => 'completed']);

        DB::table('recruitment_requests')
            ->where('status', 'completed_unsuccessful')
            ->update(['status' => 'cancelled']);

        DB::table('recruitment_request_status_events')
            ->where('status', 'completed_successful')
            ->update(['status' => 'completed']);

        DB::table('recruitment_request_status_events')
            ->where('status', 'completed_unsuccessful')
            ->update(['status' => 'cancelled']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY status ENUM(
                'pending',
                'in_progress',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'");
        }
    }
};
