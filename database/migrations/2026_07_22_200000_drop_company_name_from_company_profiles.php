<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('company_profiles', 'company_name')) {
            return;
        }

        // Prefer the company-profile name when it diverges from users.name (catalog previously used it).
        DB::table('company_profiles')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->orderBy('id')
            ->each(function (object $profile): void {
                DB::table('users')
                    ->where('id', $profile->user_id)
                    ->update(['name' => $profile->company_name]);
            });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('company_profiles', 'company_name')) {
            return;
        }

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('user_id');
        });

        DB::table('users')
            ->where('role', 'company')
            ->orderBy('id')
            ->each(function (object $user): void {
                DB::table('company_profiles')
                    ->where('user_id', $user->id)
                    ->update(['company_name' => $user->name]);
            });
    }
};
