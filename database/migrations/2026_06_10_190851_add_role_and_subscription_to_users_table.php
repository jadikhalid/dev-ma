<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'dev' = Développeur, 'company' = Entreprise cliente, 'admin' = Toi
            $table->string('role')->default('dev')->after('password');

            // Statut du paywall (500 DHS/an)
            $table->boolean('is_subscribed')->default(false)->after('role');
            $table->timestamp('subscription_expires_at')->nullable()->after('is_subscribed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_subscribed', 'subscription_expires_at']);
        });
    }
};
