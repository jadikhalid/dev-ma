<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_outbox', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->default(0)->after('status');
            $table->timestamp('retry_at')->nullable()->after('error');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_outbox', function (Blueprint $table) {
            $table->dropColumn(['attempts', 'retry_at']);
        });
    }
};
