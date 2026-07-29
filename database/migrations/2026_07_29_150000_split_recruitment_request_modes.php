<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY mode VARCHAR(32) NOT NULL DEFAULT 'intermediary'");
        }

        foreach (DB::table('recruitment_requests')->orderBy('id')->get(['id', 'developer_user_id']) as $row) {
            DB::table('recruitment_requests')
                ->where('id', $row->id)
                ->update([
                    'mode' => filled($row->developer_user_id) ? 'named' : 'open',
                ]);
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY mode ENUM('named', 'open') NOT NULL DEFAULT 'open'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY mode VARCHAR(32) NOT NULL DEFAULT 'open'");
        }

        DB::table('recruitment_requests')->update(['mode' => 'intermediary']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recruitment_requests MODIFY mode ENUM('direct', 'intermediary') NOT NULL DEFAULT 'intermediary'");
        }
    }
};
