<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preuve de consentement au traitement des données (talent aujourd’hui,
     * entreprise demain — mêmes colonnes, procédure adaptée côté inscription).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('data_processing_consent_at')->nullable()->after('email_verified_at');
            $table->string('data_processing_consent_version', 32)->nullable()->after('data_processing_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['data_processing_consent_at', 'data_processing_consent_version']);
        });
    }
};
