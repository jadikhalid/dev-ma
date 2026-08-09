<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Autorise plusieurs fils (objets différents) entre la même entreprise et le même talent.
     * Sur MySQL, l’unique sert aussi aux FK : on les recrée après le drop.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['company_user_id']);
            $table->dropForeign(['talent_user_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['company_user_id', 'talent_user_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('company_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('talent_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['company_user_id', 'talent_user_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['company_user_id']);
            $table->dropForeign(['talent_user_id']);
            $table->dropIndex(['company_user_id', 'talent_user_id', 'channel']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(['company_user_id', 'talent_user_id']);
            $table->foreign('company_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('talent_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
