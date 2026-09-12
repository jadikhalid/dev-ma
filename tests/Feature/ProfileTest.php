<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->talent()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile?panel=account');

        $user->refresh();

        $this->assertSame('Test', $user->first_name);
        $this->assertSame('User', $user->last_name);
        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->talent()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'name' => 'Test User',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile?panel=account');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_replacing_avatar_bumps_cache_busting_query(): void
    {
        Storage::fake('public');

        $user = User::factory()->talent()->create();
        $avatars = app(AvatarService::class);

        $avatars->store($user, UploadedFile::fake()->image('first.jpg', 200, 200));
        $firstUrl = $user->fresh()->avatarUrl();
        $firstVersion = (int) $user->fresh()->updated_at->getTimestamp();

        $this->assertNotNull($firstUrl);
        $this->assertStringContainsString('?v='.$firstVersion, $firstUrl);

        sleep(1);

        $avatars->store($user->fresh(), UploadedFile::fake()->image('second.jpg', 200, 200));
        $fresh = $user->fresh();
        $secondUrl = $fresh->avatarUrl();
        $secondVersion = (int) $fresh->updated_at->getTimestamp();

        $this->assertSame('avatars/'.$fresh->id.'.jpg', $fresh->avatar_path);
        $this->assertGreaterThan($firstVersion, $secondVersion);
        $this->assertStringContainsString('?v='.$secondVersion, $secondUrl);
        $this->assertNotSame($firstUrl, $secondUrl);
    }
}
