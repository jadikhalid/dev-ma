<?php

namespace Tests\Unit;

use App\Support\PublicStorageUrl;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicStorageUrlTest extends TestCase
{
    #[Test]
    public function it_returns_null_for_empty_path(): void
    {
        $this->assertNull(PublicStorageUrl::make(null));
        $this->assertNull(PublicStorageUrl::make(''));
    }

    #[Test]
    public function it_builds_relative_storage_url_without_version(): void
    {
        $this->assertSame('/storage/avatars/1.jpg', PublicStorageUrl::make('avatars/1.jpg'));
        $this->assertSame('/storage/avatars/1.jpg', PublicStorageUrl::make('/avatars/1.jpg'));
    }

    #[Test]
    public function it_appends_cache_busting_query_from_timestamp(): void
    {
        $at = Carbon::createFromTimestamp(1_700_000_000);

        $this->assertSame(
            '/storage/avatars/1.jpg?v=1700000000',
            PublicStorageUrl::make('avatars/1.jpg', $at),
        );

        $this->assertSame(
            '/storage/avatars/1.jpg?v=42',
            PublicStorageUrl::make('avatars/1.jpg', 42),
        );
    }
}
