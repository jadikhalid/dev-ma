<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('plan', 32)->nullable()->after('trial_ends_at');
        });

        DB::table('company_profiles')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->where('is_subscribed', false)
            ->update(['plan' => 'trial']);

        DB::table('company_profiles')
            ->whereNull('plan')
            ->where('is_subscribed', true)
            ->update(['plan' => 'pro']);
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }
};
