<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Intermediate "DH launched" was briefly stored as shortlisted (= Retenu by mistake).
        DB::table('job_applications')
            ->where('status', 'shortlisted')
            ->update(['status' => 'in_process']);

        DB::table('job_posting_activity_events')
            ->where('event', 'application_status')
            ->where('status', 'shortlisted')
            ->update(['status' => 'in_process']);
    }

    public function down(): void
    {
        DB::table('job_applications')
            ->where('status', 'in_process')
            ->update(['status' => 'shortlisted']);

        DB::table('job_posting_activity_events')
            ->where('event', 'application_status')
            ->where('status', 'in_process')
            ->update(['status' => 'shortlisted']);

        DB::table('job_applications')
            ->where('status', 'retained')
            ->update(['status' => 'shortlisted']);

        DB::table('job_posting_activity_events')
            ->where('event', 'application_status')
            ->where('status', 'retained')
            ->update(['status' => 'shortlisted']);
    }
};
