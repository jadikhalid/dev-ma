<?php

namespace Tests\Feature\Admin;

use App\Models\User;
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

    public function test_moderator_sees_admin_dashboard(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator', 'approval_status' => null]);

        $this->actingAs($moderator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin');
    }
}
