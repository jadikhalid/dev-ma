<?php

namespace Tests\Feature\Admin;

use App\Models\DirectHireRequest;
use App\Models\ModeratorPermissionCatalog;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeratorPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_grant_moderator_without_permissions_by_default(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create();
        $talent->profile()->create(['experience_years' => 0]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.moderator.grant', $talent), ['permissions' => []])
            ->assertOk()
            ->assertJsonPath('moderator.is_moderator', true)
            ->assertJsonPath('moderator.permissions', []);

        $this->assertTrue($talent->fresh()->isModerator());
        $this->assertFalse($talent->fresh()->canActAsModerator());
    }

    public function test_admin_can_assign_granular_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->talent()->create();
        $talent->profile()->create(['experience_years' => 0]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.moderator.grant', $talent), [
                'permissions' => [
                    ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE,
                    ModeratorPermissionCatalog::PUBLICATIONS_MANAGE,
                ],
            ])
            ->assertOk();

        $this->actingAs($admin)
            ->putJson(route('admin.users.moderator.permissions', $talent), [
                'permissions' => [ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE],
            ])
            ->assertOk()
            ->assertJsonPath('moderator.permissions', [ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE]);
    }

    public function test_moderator_executes_approve_immediately_when_permission_granted(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_APPROVE,
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.approve', $talent))
            ->assertRedirect();

        $this->assertTrue($talent->fresh()->isApproved());
    }

    public function test_moderator_cannot_create_accounts(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
            ModeratorPermissionCatalog::ACCOUNTS_APPROVE,
            ModeratorPermissionCatalog::ACCOUNTS_DELETE,
        ])->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.store'), [
                'role' => 'dev',
                'first_name' => 'Ada',
                'last_name' => 'Lovelace',
                'email' => 'ada@example.com',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
            ])
            ->assertForbidden();
    }

    public function test_moderator_without_permission_cannot_access_direct_hire(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.direct-hire.index'))
            ->assertForbidden();
    }

    public function test_moderator_with_publications_can_access_publications(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::PUBLICATIONS_MANAGE,
        ])->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.publications.index'))
            ->assertOk();
    }

    public function test_talent_mode_hides_staff_dashboard(): void
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

    public function test_moderator_mode_switch_enables_staff_dashboard(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::SOURCING_MANAGE,
        ])->create();
        $moderator->profile()->create(['experience_years' => 0]);

        $this->actingAs($moderator)
            ->post(route('moderator-mode.update'), ['mode' => 'moderator'])
            ->assertRedirect(route('dashboard'));

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin');
    }

    public function test_only_admin_can_grant_moderator(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $talent = User::factory()->talent()->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.moderator.grant', $talent))
            ->assertForbidden();
    }

    public function test_revoking_moderator_transfers_open_staff_direct_hires_to_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE,
        ])->create();
        $moderator->profile()->create(['experience_years' => 0]);
        $talent = User::factory()->talent()->create();

        $openHire = DirectHireRequest::query()->create([
            'company_user_id' => null,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => null,
            'company_name_snapshot' => 'Talents du Maroc',
            'hire_origin' => DirectHireRequest::ORIGIN_STAFF_INTERNAL,
            'initiated_by_user_id' => $moderator->id,
            'subject' => 'Open staff hire',
            'message' => str_repeat('Dossier ouvert. ', 4),
            'status' => DirectHireRequest::STATUS_IN_PROCESS,
            'staff_seen_at' => now(),
        ]);

        $closedHire = DirectHireRequest::query()->create([
            'company_user_id' => null,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => null,
            'company_name_snapshot' => 'Talents du Maroc',
            'hire_origin' => DirectHireRequest::ORIGIN_STAFF_INTERNAL,
            'initiated_by_user_id' => $moderator->id,
            'subject' => 'Closed staff hire',
            'message' => str_repeat('Dossier clos. ', 4),
            'status' => DirectHireRequest::STATUS_HIRED,
            'closed_at' => now(),
            'closed_by' => $moderator->id,
        ]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.users.moderator.revoke', $moderator))
            ->assertOk()
            ->assertJsonPath('recovery.recovered_direct_hires', 1)
            ->assertJsonPath('moderator.is_moderator', false);

        $this->assertFalse($moderator->fresh()->isModerator());
        $this->assertSame($admin->id, $openHire->fresh()->initiated_by_user_id);
        $this->assertNull($openHire->fresh()->staff_seen_at);
        $this->assertSame($moderator->id, $closedHire->fresh()->initiated_by_user_id);

        $this->assertDatabaseHas('moderator_audit_logs', [
            'moderator_user_id' => $moderator->id,
            'action' => 'revoked',
            'performed_by' => $admin->id,
        ]);
    }

    public function test_deleting_moderator_recovers_open_cases_and_keeps_audit_snapshot(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::DIRECT_HIRE_MANAGE,
            ModeratorPermissionCatalog::ACCOUNTS_DELETE,
        ])->create([
            'name' => 'Moderateur Snapshot',
            'first_name' => 'Moderateur',
            'last_name' => 'Snapshot',
            'email' => 'mod.snapshot@example.com',
        ]);
        $moderator->profile()->create(['experience_years' => 0]);
        $talent = User::factory()->talent()->create();

        $openHire = DirectHireRequest::query()->create([
            'company_user_id' => null,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => null,
            'company_name_snapshot' => 'Talents du Maroc',
            'hire_origin' => DirectHireRequest::ORIGIN_STAFF_INTERNAL,
            'initiated_by_user_id' => $moderator->id,
            'subject' => 'Recover on delete',
            'message' => str_repeat('Dossier à récupérer. ', 4),
            'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
        ]);

        $moderatorId = $moderator->id;

        $this->actingAs($admin)
            ->deleteJson(route('admin.users.destroy', $moderator))
            ->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $moderatorId]);
        $this->assertSame($admin->id, $openHire->fresh()->initiated_by_user_id);

        $this->assertDatabaseHas('moderator_audit_logs', [
            'action' => 'account_deleted',
            'moderator_name_snapshot' => 'Moderateur Snapshot',
            'moderator_email_snapshot' => 'mod.snapshot@example.com',
            'performed_by' => $admin->id,
        ]);
    }

    public function test_non_admin_moderator_cannot_delete_another_moderator(): void
    {
        $actor = User::factory()->moderator([
            ModeratorPermissionCatalog::ACCOUNTS_DELETE,
            ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $target = User::factory()->moderator([
            ModeratorPermissionCatalog::SOURCING_MANAGE,
        ])->create();
        $target->profile()->create(['experience_years' => 0]);

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($actor)
            ->deleteJson(route('admin.users.destroy', $target))
            ->assertForbidden();

        $this->assertTrue($target->fresh()->isModerator());
    }
}
