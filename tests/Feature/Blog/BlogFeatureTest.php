<?php

namespace Tests\Feature\Blog;

use App\Models\BlogPost;
use App\Models\ModeratorPermissionCatalog;
use App\Models\SocialFeedItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_see_published_posts_in_current_locale(): void
    {
        BlogPost::query()->create([
            'title' => 'Article FR publié',
            'slug' => 'article-fr-publie',
            'excerpt' => 'Chapô FR',
            'body' => '<p>Contenu FR</p>',
            'locale' => 'fr',
            'status' => BlogPost::STATUS_PUBLISHED,
            'show_in_ticker' => false,
            'published_at' => now()->subHour(),
            'created_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);

        BlogPost::query()->create([
            'title' => 'Draft FR',
            'slug' => 'draft-fr',
            'excerpt' => 'Draft',
            'body' => '<p>Draft</p>',
            'locale' => 'fr',
            'status' => BlogPost::STATUS_DRAFT,
            'show_in_ticker' => false,
            'published_at' => null,
            'created_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Article FR publié', false)
            ->assertDontSee('Draft FR', false);
    }

    #[Test]
    public function draft_article_is_not_publicly_reachable(): void
    {
        BlogPost::query()->create([
            'title' => 'Secret',
            'slug' => 'secret',
            'excerpt' => 'Hidden',
            'body' => '<p>Hidden</p>',
            'locale' => 'fr',
            'status' => BlogPost::STATUS_DRAFT,
            'show_in_ticker' => false,
            'published_at' => null,
            'created_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);

        $this->get(route('blog.show', 'secret'))->assertNotFound();
    }

    #[Test]
    public function admin_can_publish_article_into_news_ticker(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->post(route('admin.blog.store'), [
                'title' => 'Nouveau sur la plateforme',
                'slug' => 'nouveau-sur-la-plateforme',
                'excerpt' => 'Un extrait utile',
                'body' => '<p>Corps de l article</p>',
                'locale' => 'fr',
                'status' => BlogPost::STATUS_PUBLISHED,
                'show_in_ticker' => '1',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $post = BlogPost::query()->where('slug', 'nouveau-sur-la-plateforme')->first();
        $this->assertNotNull($post);
        $this->assertTrue($post->isPublished());

        $this->assertDatabaseHas('social_feed_items', [
            'source' => 'article',
            'title' => 'Nouveau sur la plateforme',
            'url' => route('blog.show', 'nouveau-sur-la-plateforme'),
        ]);
    }

    #[Test]
    public function deleting_article_removes_ticker_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $post = BlogPost::query()->create([
            'title' => 'À retirer',
            'slug' => 'a-retirer',
            'excerpt' => 'Excerpt',
            'body' => '<p>Body</p>',
            'locale' => 'fr',
            'status' => BlogPost::STATUS_PUBLISHED,
            'show_in_ticker' => true,
            'published_at' => now()->subMinute(),
            'created_by' => $admin->id,
        ]);
        $post->syncTicker();

        $this->assertSame(1, SocialFeedItem::query()->where('url', $post->tickerUrl())->count());

        $this->actingAs($admin)
            ->delete(route('admin.blog.destroy', $post))
            ->assertRedirect(route('admin.blog.index'));

        $this->assertSame(0, SocialFeedItem::query()->where('url', route('blog.show', 'a-retirer'))->count());
    }

    #[Test]
    public function moderator_with_publications_permission_can_manage_blog(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::PUBLICATIONS_MANAGE,
        ])->create();

        session([
            \App\Services\ModeratorAssignmentService::SESSION_MODE_KEY => true,
        ]);

        $this->actingAs($moderator)
            ->get(route('admin.blog.index'))
            ->assertOk();
    }

    #[Test]
    public function home_header_includes_blog_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee(__('talenma.nav.blog'), false);
    }
}
