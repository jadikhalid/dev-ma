<?php

use App\Models\SocialFeedItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('magazine_banner_items', 'social_feed_items');

        Schema::table('social_feed_items', function (Blueprint $table) {
            $table->string('source', 32)->default('article')->after('url');
            $table->index('source');
        });

        foreach (DB::table('social_feed_items')->orderBy('id')->get() as $item) {
            DB::table('social_feed_items')
                ->where('id', $item->id)
                ->update(['source' => SocialFeedItem::detectSource($item->url)]);
        }
    }

    public function down(): void
    {
        Schema::table('social_feed_items', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });

        Schema::rename('social_feed_items', 'magazine_banner_items');
    }
};
