<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_demo_requests', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('contact_name');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('company_size', 20)->nullable()->after('company_name');
            $table->string('hires_planned', 20)->nullable()->after('phone');
            $table->json('hiring_locations')->nullable()->after('hires_planned');
            $table->string('hiring_city', 120)->nullable()->after('hiring_locations');
            $table->string('uses_ats', 10)->nullable()->after('hiring_city');
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('company_demo_requests', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'company_size',
                'hires_planned',
                'hiring_locations',
                'hiring_city',
                'uses_ats',
            ]);
        });
    }
};
