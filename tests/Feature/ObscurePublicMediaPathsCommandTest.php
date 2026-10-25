<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ObscurePublicMediaPathsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_renames_sequential_avatar_and_logo_paths(): void
    {
        Storage::fake('public');

        $talent = User::factory()->talent()->create([
            'avatar_path' => 'avatars/42.jpg',
        ]);
        Storage::disk('public')->put('avatars/42.jpg', 'avatar-bytes');

        $company = User::factory()->companyOwner()->create();
        $profile = CompanyProfile::factory()->create([
            'user_id' => $company->id,
            'logo_path' => 'company-logos/7.png',
            'representative_photo_path' => 'company-contacts/7.jpg',
        ]);
        Storage::disk('public')->put('company-logos/7.png', 'logo-bytes');
        Storage::disk('public')->put('company-contacts/7.jpg', 'contact-bytes');

        $this->artisan('media:obscure-public-paths')->assertSuccessful();

        $talent = $talent->fresh();
        $profile = $profile->fresh();

        $this->assertMatchesRegularExpression('#^avatars/[a-z0-9]{40}\.jpg$#', (string) $talent->avatar_path);
        $this->assertFalse(Storage::disk('public')->exists('avatars/42.jpg'));
        $this->assertTrue(Storage::disk('public')->exists($talent->avatar_path));

        $this->assertMatchesRegularExpression('#^company-logos/[a-z0-9]{40}\.png$#', (string) $profile->logo_path);
        $this->assertMatchesRegularExpression('#^company-contacts/[a-z0-9]{40}\.jpg$#', (string) $profile->representative_photo_path);
        $this->assertFalse(Storage::disk('public')->exists('company-logos/7.png'));
        $this->assertTrue(Storage::disk('public')->exists($profile->logo_path));
    }

    public function test_command_leaves_already_opaque_paths_untouched(): void
    {
        Storage::fake('public');

        $opaque = 'avatars/abcdefghijklmnopqrstuvwxyz0123456789abcd.jpg';
        $talent = User::factory()->talent()->create(['avatar_path' => $opaque]);
        Storage::disk('public')->put($opaque, 'avatar-bytes');

        $this->artisan('media:obscure-public-paths')->assertSuccessful();

        $this->assertSame($opaque, $talent->fresh()->avatar_path);
        $this->assertTrue(Storage::disk('public')->exists($opaque));
    }
}
