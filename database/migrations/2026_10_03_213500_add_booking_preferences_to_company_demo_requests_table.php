<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_demo_requests', function (Blueprint $table) {
            $table->date('preferred_date')->nullable()->after('uses_ats');
            $table->json('preferred_slots')->nullable()->after('preferred_date');
            $table->string('meeting_platform', 20)->nullable()->after('preferred_slots');
        });
    }

    public function down(): void
    {
        Schema::table('company_demo_requests', function (Blueprint $table) {
            $table->dropColumn(['preferred_date', 'preferred_slots', 'meeting_platform']);
        });
    }
};
