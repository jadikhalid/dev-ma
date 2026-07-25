<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Treat existing rows as already seen so only future changes raise indicators.
        DB::table('direct_hire_requests')
            ->whereNull('company_seen_at')
            ->update([
                'company_seen_at' => DB::raw('updated_at'),
            ]);
    }

    public function down(): void
    {
        //
    }
};
