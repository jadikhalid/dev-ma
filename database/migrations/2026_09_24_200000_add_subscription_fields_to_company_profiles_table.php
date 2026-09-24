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
            $table->boolean('is_subscribed')->default(false)->after('hiring_needs');
            $table->timestamp('subscription_expires_at')->nullable()->after('is_subscribed');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
        });

        // Entreprises déjà présentes : accès conservé (grandfather).
        DB::table('company_profiles')->update([
            'is_subscribed' => true,
            'subscription_expires_at' => null,
            'trial_ends_at' => null,
        ]);
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_subscribed', 'subscription_expires_at', 'trial_ends_at']);
        });
    }
};
