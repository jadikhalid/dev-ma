<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_documents', function (Blueprint $table) {
            $table->string('document_type', 32)->default('registration')->after('profile_id');
            $table->index(['profile_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::table('profile_documents', function (Blueprint $table) {
            $table->dropIndex(['profile_id', 'document_type']);
            $table->dropColumn('document_type');
        });
    }
};
