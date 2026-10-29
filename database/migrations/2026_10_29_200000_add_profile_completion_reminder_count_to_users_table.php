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
            $table->unsignedTinyInteger('profile_completion_reminder_count')
                ->default(0)
                ->after('profile_completion_reminder_sent_at');
        });

        DB::table('users')
            ->whereNotNull('profile_completion_reminder_sent_at')
            ->where('profile_completion_reminder_count', 0)
            ->update(['profile_completion_reminder_count' => 1]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_completion_reminder_count');
        });
    }
};
