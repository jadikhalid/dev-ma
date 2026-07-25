<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_rounds', function (Blueprint $table) {
            $table->string('meeting_url', 2048)->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_rounds', function (Blueprint $table) {
            $table->dropColumn('meeting_url');
        });
    }
};
