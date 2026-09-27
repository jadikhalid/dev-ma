<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Remember me" cookies were issued on the shared .talentsdumaroc.com domain
     * and would keep signing company accounts in on the talents host.
     */
    public function up(): void
    {
        DB::table('users')->whereNotNull('remember_token')->update(['remember_token' => null]);
    }

    public function down(): void
    {
        //
    }
};
