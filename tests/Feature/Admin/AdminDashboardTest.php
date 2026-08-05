<?php

namespace Tests\Feature\Admin;

use App\Models\ModeratorPermissionCatalog;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin')
            ->assertSee(__('talenma.dashboard.admin.title'));
    }

    public function test_moderator_sees_admin_dashboard_when_in_moderator_mode(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::SOURCING_MANAGE,
        ])->create();
        $moderator->profile()->create(['experience_years' => 0]);

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin');
    }

    public function test_moderator_sees_talent_dashboard_by_default(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::SOURCING_MANAGE,
        ])->create();
        $moderator->profile()->create(['experience_years' => 0]);

        $this->actingAs($moderator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.talent');
    }
}
