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
            $table->json('work_modes')->nullable()->after('remote_ok');
        });

        DB::table('job_postings')->orderBy('id')->chunkById(100, function ($jobs): void {
            foreach ($jobs as $job) {
                $modes = $job->remote_ok ? ['remote'] : [];

                DB::table('job_postings')
                    ->where('id', $job->id)
                    ->update(['work_modes' => json_encode($modes)]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('work_modes');
        });
    }
};
