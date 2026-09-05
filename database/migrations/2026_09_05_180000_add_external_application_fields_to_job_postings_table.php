<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('application_mode', 16)->default('internal')->after('status');
            $table->string('external_company_name')->nullable()->after('application_mode');
            $table->string('external_company_logo_path')->nullable()->after('external_company_name');
            $table->string('external_apply_url', 2048)->nullable()->after('external_company_logo_path');
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreign('company_profile_id')
                ->references('id')
                ->on('company_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreign('company_profile_id')
                ->references('id')
                ->on('company_profiles')
                ->cascadeOnDelete();
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn([
                'application_mode',
                'external_company_name',
                'external_company_logo_path',
                'external_apply_url',
            ]);
        });
    }
};
