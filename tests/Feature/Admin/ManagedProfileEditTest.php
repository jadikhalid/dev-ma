<?php

namespace Tests\Feature\Admin;

use App\Models\CompanyProfile;
use App\Models\ModeratorPermissionCatalog;
use App\Models\Profile;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagedProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_talent_profile_editor(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create();
        Profile::factory()->create(['user_id' => $talent->id]);

        $this->actingAs($admin)
            ->get(route('admin.users.profile.edit', $talent))
            ->assertOk()
            ->assertSee(__('talenma.admin.users.edit_profile_title'), false);
    }

    public function test_admin_can_update_talent_profile_section(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create();
        Profile::factory()->create([
            'user_id' => $talent->id,
            'availability' => Profile::STATUS_AVAILABLE,
            'work_modes' => ['remote'],
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.profile.update', $talent), [
                'section' => 'availability',
                'availability' => Profile::STATUS_BUSY,
                'work_modes' => ['local'],
            ])
            ->assertRedirect(route('admin.users.profile.edit', $talent));

        $profile = $talent->fresh()->profile;
        $this->assertSame(Profile::STATUS_BUSY, $profile->availability);
        $this->assertSame(['local'], $profile->work_modes);
    }

    public function test_admin_can_update_company_profile_section(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $company = User::factory()->companyOwner()->create(['name' => 'Acme SAS']);
        CompanyProfile::factory()->create([
            'user_id' => $company->id,
            'description' => str_repeat('a', 60),
            'hiring_needs' => str_repeat('b', 40),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.profile.update', $company), [
                'section' => 'presentation',
                'description' => str_repeat('Updated company presentation text here. ', 3),
            ])
            ->assertRedirect(route('admin.users.profile.edit', $company));

        $this->assertStringContainsString('Updated company', $company->fresh()->companyProfile->description);
    }

    public function test_moderator_with_profiles_edit_can_access_editor(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
            ModeratorPermissionCatalog::PROFILES_EDIT,
        ])->create();
        $talent = User::factory()->talent()->create();
        Profile::factory()->create(['user_id' => $talent->id]);

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.users.profile.edit', $talent))
            ->assertOk();
    }

    public function test_moderator_without_profiles_edit_is_forbidden(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
            ModeratorPermissionCatalog::ACCOUNTS_DELETE,
        ])->create();
        $talent = User::factory()->talent()->create();
        Profile::factory()->create(['user_id' => $talent->id]);

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.users.profile.edit', $talent))
            ->assertForbidden();
    }

    public function test_edit_button_is_shown_when_permission_granted(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create([
            'email_verified_at' => now(),
        ]);
        Profile::factory()->create(['user_id' => $talent->id]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['filter' => 'talents']))
            ->assertOk()
            ->assertSee(route('admin.users.profile.edit', $talent), false)
            ->assertSee(__('talenma.admin.users.edit_profile_btn'), false);
    }

    public function test_admin_can_open_talent_avatar_form_on_profile_editor(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create();
        Profile::factory()->create(['user_id' => $talent->id]);

        $this->actingAs($admin)
            ->get(route('admin.users.profile.edit', $talent))
            ->assertOk()
            ->assertSee(__('talenma.account.avatar'), false)
            ->assertSee(route('admin.users.profile.avatar', $talent), false);
    }
}
