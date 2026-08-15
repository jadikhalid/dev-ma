<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // LinkedIn URLs often exceed VARCHAR(255). SQLite ignores the limit; MySQL does not.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE social_posts MODIFY url TEXT NOT NULL');

            if (Schema::hasTable('social_feed_items')) {
                DB::statement('ALTER TABLE social_feed_items MODIFY url TEXT NOT NULL');
            }

            return;
        }

        // SQLite / others: recreate is unnecessary for local; skip safely.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE social_posts MODIFY url VARCHAR(255) NOT NULL');

            if (Schema::hasTable('social_feed_items')) {
                DB::statement('ALTER TABLE social_feed_items MODIFY url VARCHAR(255) NOT NULL');
            }
        }
    }
};
