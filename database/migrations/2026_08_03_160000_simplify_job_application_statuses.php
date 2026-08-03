<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $applicationMap = [
            'submitted' => 'received',
            'reviewed' => 'viewed',
            'shortlisted' => 'viewed',
            'rejected' => 'closed',
            'withdrawn' => 'closed',
        ];

        foreach ($applicationMap as $from => $to) {
            DB::table('job_applications')
                ->where('status', $from)
                ->update(['status' => $to]);

            DB::table('job_posting_activity_events')
                ->where('event', 'application_status')
                ->where('status', $from)
                ->update(['status' => $to]);
        }
    }

    public function down(): void
    {
        $applicationMap = [
            'received' => 'submitted',
            'viewed' => 'reviewed',
            'closed' => 'rejected',
        ];

        foreach ($applicationMap as $from => $to) {
            DB::table('job_applications')
                ->where('status', $from)
                ->update(['status' => $to]);

            DB::table('job_posting_activity_events')
                ->where('event', 'application_status')
                ->where('status', $from)
                ->update(['status' => $to]);
        }
    }
};
