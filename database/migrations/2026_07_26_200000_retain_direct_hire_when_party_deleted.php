<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->string('talent_name_snapshot')->nullable()->after('talent_user_id');
            $table->string('company_name_snapshot')->nullable()->after('company_profile_id');
        });

        // Backfill display snapshots while both parties still exist.
        if (Schema::hasTable('users')) {
            $rows = DB::table('direct_hire_requests')->get(['id', 'company_user_id', 'talent_user_id', 'company_profile_id']);

            foreach ($rows as $row) {
                $talentName = $row->talent_user_id
                    ? DB::table('users')->where('id', $row->talent_user_id)->value('name')
                    : null;

                $companyName = null;
                if ($row->company_profile_id) {
                    $ownerId = DB::table('company_profiles')->where('id', $row->company_profile_id)->value('user_id');
                    if ($ownerId) {
                        $companyName = DB::table('users')->where('id', $ownerId)->value('name');
                    }
                }
                if (! filled($companyName) && $row->company_user_id) {
                    $companyName = DB::table('users')->where('id', $row->company_user_id)->value('name');
                }

                DB::table('direct_hire_requests')->where('id', $row->id)->update([
                    'talent_name_snapshot' => $talentName,
                    'company_name_snapshot' => $companyName,
                ]);
            }
        }

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropForeign(['company_user_id']);
            $table->dropForeign(['talent_user_id']);
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('company_user_id')->nullable()->change();
            $table->unsignedBigInteger('talent_user_id')->nullable()->change();
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->foreign('company_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('talent_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // Keep chat history when a sender account is deleted.
        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_user_id']);
        });

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_user_id')->nullable()->change();
        });

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->foreign('sender_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_user_id']);
        });

        // Orphan rows with null sender cannot regain a NOT NULL FK — drop them.
        DB::table('direct_hire_messages')->whereNull('sender_user_id')->delete();

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_user_id')->nullable(false)->change();
        });

        Schema::table('direct_hire_messages', function (Blueprint $table) {
            $table->foreign('sender_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropForeign(['company_user_id']);
            $table->dropForeign(['talent_user_id']);
        });

        DB::table('direct_hire_requests')
            ->whereNull('company_user_id')
            ->orWhereNull('talent_user_id')
            ->delete();

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('company_user_id')->nullable(false)->change();
            $table->unsignedBigInteger('talent_user_id')->nullable(false)->change();
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->foreign('company_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('talent_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('direct_hire_requests', function (Blueprint $table) {
            $table->dropColumn(['talent_name_snapshot', 'company_name_snapshot']);
        });
    }
};
