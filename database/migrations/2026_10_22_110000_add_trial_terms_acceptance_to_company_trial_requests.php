<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_trial_requests', function (Blueprint $table) {
            $table->timestamp('trial_terms_accepted_at')->nullable()->after('ip_address');
            $table->string('trial_terms_version', 32)->nullable()->after('trial_terms_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('company_trial_requests', function (Blueprint $table) {
            $table->dropColumn(['trial_terms_accepted_at', 'trial_terms_version']);
        });
    }
};
