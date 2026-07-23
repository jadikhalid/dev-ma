<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_seat', 16)->nullable()->after('role');
            $table->timestamp('disabled_at')->nullable()->after('rejection_reason');
        });

        DB::table('users')
            ->where('role', 'company')
            ->whereNull('company_seat')
            ->update(['company_seat' => 'owner']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_seat', 'disabled_at']);
        });
    }
};
