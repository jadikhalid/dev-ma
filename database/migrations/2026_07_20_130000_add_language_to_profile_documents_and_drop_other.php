<?php

use App\Models\ProfileDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_documents', function (Blueprint $table) {
            $table->string('language', 8)->nullable()->after('document_type');
        });

        DB::table('profile_documents')
            ->where('document_type', ProfileDocument::TYPE_CV)
            ->whereNull('language')
            ->update(['language' => 'fr']);

        $otherDocuments = DB::table('profile_documents')
            ->where('document_type', ProfileDocument::TYPE_OTHER)
            ->get(['id', 'path']);

        foreach ($otherDocuments as $document) {
            if (filled($document->path)) {
                Storage::disk('public')->delete($document->path);
            }
        }

        DB::table('profile_documents')
            ->where('document_type', ProfileDocument::TYPE_OTHER)
            ->delete();

        Schema::table('profile_documents', function (Blueprint $table) {
            $table->unique(['profile_id', 'document_type', 'language'], 'profile_documents_profile_type_language_unique');
        });
    }

    public function down(): void
    {
        Schema::table('profile_documents', function (Blueprint $table) {
            $table->dropUnique('profile_documents_profile_type_language_unique');
            $table->dropColumn('language');
        });
    }
};
