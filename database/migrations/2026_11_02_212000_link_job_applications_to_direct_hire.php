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
            $table->foreignId('direct_hire_request_id')
                ->nullable()
                ->after('talent_user_id')
                ->constrained('direct_hire_requests')
                ->nullOnDelete();
            $table->unique('direct_hire_request_id');
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->foreignId('job_application_id')
                ->nullable()
                ->after('talent_user_id')
                ->constrained('job_applications')
                ->nullOnDelete();
            $table->unique('job_application_id');
        });

        $applicationMap = [
            'viewed' => 'received',
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

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_application_id');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('direct_hire_request_id');
        });

        DB::table('job_applications')
            ->where('status', 'rejected')
            ->update(['status' => 'closed']);

        DB::table('job_posting_activity_events')
            ->where('event', 'application_status')
            ->where('status', 'rejected')
            ->update(['status' => 'closed']);
    }
};
