<?php

use App\Models\Profile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('profiles')
            ->where('experience_years', 0)
            ->update([
                'is_fresh_graduate' => true,
                'experience_years' => null,
            ]);

        DB::table('profiles')
            ->where('is_fresh_graduate', true)
            ->update(['experience_years' => null]);

        DB::table('profiles')
            ->where('is_fresh_graduate', false)
            ->where(function ($query) {
                $query->whereNull('experience_years')
                    ->orWhere('experience_years', '<', 1);
            })
            ->update([
                'is_fresh_graduate' => true,
                'experience_years' => null,
            ]);
    }

    public function down(): void
    {
        // Non réversible proprement.
    }
};
