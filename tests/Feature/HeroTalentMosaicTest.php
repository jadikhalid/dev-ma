<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use App\Services\HeroTalentMosaicService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroTalentMosaicTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiles_pad_with_fakes_when_fewer_than_six_public_avatars(): void
    {
        $service = app(HeroTalentMosaicService::class);

        $tiles = $service->tiles();

        $this->assertCount(6, $tiles);
        $this->assertTrue(collect($tiles)->every(fn (array $tile) => filled($tile['photo'])));
        $this->assertTrue(collect($tiles)->contains(fn (array $tile) => $tile['is_fake'] === true));
    }

    public function test_tiles_prefer_public_talents_with_avatars(): void
    {
        $public = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_APPROVED,
            'avatar_path' => 'avatars/public.jpg',
            'first_name' => 'Amina',
            'last_name' => 'Public',
        ]);
        Profile::factory()->create([
            'user_id' => $public->id,
            'is_public' => true,
            'city' => 'Casablanca',
        ]);

        $private = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_APPROVED,
            'avatar_path' => 'avatars/private.jpg',
            'first_name' => 'Sara',
            'last_name' => 'Private',
        ]);
        Profile::factory()->create([
            'user_id' => $private->id,
            'is_public' => false,
        ]);

        $tiles = app(HeroTalentMosaicService::class)->tiles();

        $this->assertCount(6, $tiles);
        $real = collect($tiles)->firstWhere('is_fake', false);
        $this->assertNotNull($real);
        $this->assertStringContainsString('avatars/public.jpg', $real['photo']);
        $this->assertFalse(collect($tiles)->contains(
            fn (array $tile) => str_contains($tile['photo'], 'avatars/private.jpg')
        ));
    }
}
