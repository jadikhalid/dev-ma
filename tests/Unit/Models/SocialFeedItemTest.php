<?php

namespace Tests\Unit\Models;

use App\Models\SocialFeedItem;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialFeedItemTest extends TestCase
{
    #[Test]
    public function detects_internal_blog_urls_only(): void
    {
        $blog = new SocialFeedItem(['url' => 'https://example.test/blog/mon-article']);
        $blogTrailing = new SocialFeedItem(['url' => 'http://localhost:8000/blog/autre-article/']);
        $external = new SocialFeedItem(['url' => 'https://linkedin.com/posts/123']);
        $otherInternal = new SocialFeedItem(['url' => 'https://example.test/jobs/1']);

        $this->assertTrue($blog->isInternalBlogPost());
        $this->assertTrue($blogTrailing->isInternalBlogPost());
        $this->assertFalse($external->isInternalBlogPost());
        $this->assertFalse($otherInternal->isInternalBlogPost());
    }
}
