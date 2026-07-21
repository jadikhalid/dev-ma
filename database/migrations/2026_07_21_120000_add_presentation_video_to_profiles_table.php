<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('presentation_video_url', 500)->nullable()->after('whatsapp');
            $table->string('presentation_video_public_id', 255)->nullable()->after('presentation_video_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['presentation_video_url', 'presentation_video_public_id']);
        });
    }
};
