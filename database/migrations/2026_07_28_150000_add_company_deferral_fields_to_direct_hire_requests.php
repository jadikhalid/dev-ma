<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->text('company_deferral_note')->nullable()->after('talent_decision_note');
            $table->timestamp('company_deferral_responded_at')->nullable()->after('company_deferral_note');
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropColumn(['company_deferral_note', 'company_deferral_responded_at']);
        });
    }
};
