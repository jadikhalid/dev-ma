<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_rounds', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('company_note');
        });

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_rounds', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};
