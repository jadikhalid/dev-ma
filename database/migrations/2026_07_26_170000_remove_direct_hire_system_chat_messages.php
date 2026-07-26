<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('direct_hire_messages', 'is_system')) {
            return;
        }

        DB::table('direct_hire_messages')->where('is_system', true)->delete();

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('direct_hire_messages', 'is_system')) {
            return;
        }

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('body');
        });
    }
};
