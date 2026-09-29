<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('talent_cv_drafts')
            ->whereIn('template', ['simple_plus', 'starter', 'normal'])
            ->update(['template' => 'classic']);
    }

    public function down(): void
    {
        //
    }
};
