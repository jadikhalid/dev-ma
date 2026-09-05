<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
        });

        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->foreign('company_profile_id')
                ->references('id')
                ->on('company_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
        });

        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('job_posting_activity_events', function (Blueprint $table) {
            $table->foreign('company_profile_id')
                ->references('id')
                ->on('company_profiles')
                ->cascadeOnDelete();
        });
    }
};
