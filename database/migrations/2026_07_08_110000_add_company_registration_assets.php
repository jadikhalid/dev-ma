<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('registration_sector')->nullable()->after('sector');
            $table->text('registration_hiring_needs')->nullable()->after('hiring_needs');
        });

        Schema::create('company_profile_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedInteger('size');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profile_documents');

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_sector', 'registration_hiring_needs']);
        });
    }
};
