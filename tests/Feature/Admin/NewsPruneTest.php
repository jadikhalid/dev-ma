<?php

namespace Tests\Feature\Admin;

use App\Models\SocialFeedItem;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPruneTest extends TestCase
{
    use RefreshDatabase;

    public function test_prune_deletes_news_beyond_max_items_from_database(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        for ($i = 1; $i <= 15; $i++) {
            SocialFeedItem::query()->create([
                'title' => "News {$i}",
                'subtitle' => "Subtitle {$i}",
                'url' => "https://example.com/news-{$i}",
                'source' => 'article',
                'created_by' => $admin->id,
                'created_at' => now()->subMinutes(15 - $i),
                'updated_at' => now()->subMinutes(15 - $i),
            ]);
        }

        $this->assertSame(15, SocialFeedItem::query()->where('source', 'article')->count());

        $idsBeyondMax = SocialFeedItem::newsQuery()
            ->skip(SocialFeedItem::MAX_ITEMS)
            ->take(100)
            ->pluck('id')
            ->all();

        $this->assertCount(15 - SocialFeedItem::MAX_ITEMS, $idsBeyondMax);

        SocialFeedItem::pruneExcess();

        $this->assertSame(SocialFeedItem::MAX_ITEMS, SocialFeedItem::query()->where('source', 'article')->count());
        $this->assertSame(0, SocialFeedItem::query()->whereIn('id', $idsBeyondMax)->count());
    }

    public function test_push_item_keeps_only_max_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        for ($i = 1; $i <= 12; $i++) {
            SocialFeedItem::pushItem([
                'title' => "Pushed {$i}",
                'subtitle' => "Subtitle {$i}",
                'url' => "https://example.com/pushed-{$i}",
                'source' => 'article',
                'created_by' => $admin->id,
            ]);
        }

        $this->assertSame(SocialFeedItem::MAX_ITEMS, SocialFeedItem::query()->where('source', 'article')->count());
        $this->assertTrue(SocialFeedItem::query()->where('title', 'Pushed 1')->doesntExist());
        $this->assertTrue(SocialFeedItem::query()->where('title', 'Pushed 2')->doesntExist());
        $this->assertTrue(SocialFeedItem::query()->where('title', 'Pushed 12')->exists());
    }

    public function test_admin_publications_page_prunes_excess(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        for ($i = 1; $i <= 14; $i++) {
            SocialFeedItem::query()->create([
                'title' => "Admin prune {$i}",
                'subtitle' => "Subtitle {$i}",
                'url' => "https://example.com/admin-{$i}",
                'source' => 'article',
                'created_by' => $admin->id,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.publications.index'))
            ->assertOk();

        $this->assertSame(SocialFeedItem::MAX_ITEMS, SocialFeedItem::query()->where('source', 'article')->count());
    }

    public function test_admin_publications_page_prunes_excess_social_posts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        for ($i = 1; $i <= 10; $i++) {
            SocialPost::query()->create([
                'title' => "Post {$i}",
                'subtitle' => "Subtitle {$i}",
                'url' => "https://www.linkedin.com/posts/post-{$i}",
                'network' => 'linkedin',
                'created_by' => $admin->id,
                'created_at' => now()->subMinutes(10 - $i),
                'updated_at' => now()->subMinutes(10 - $i),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.publications.index'))
            ->assertOk();

        $this->assertSame(SocialPost::MAX_ITEMS, SocialPost::query()->count());
        $this->assertTrue(SocialPost::query()->where('title', 'Post 1')->doesntExist());
        $this->assertTrue(SocialPost::query()->where('title', 'Post 10')->exists());
    }
}
