<?php

namespace Tests\Feature\Admin;

use App\Mail\TalentApprovedMail;
use App\Mail\TalentRejectedMail;
use App\Models\ModerationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderator_approval_is_queued_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $moderator = User::factory()->create(['role' => 'moderator', 'approval_status' => null]);
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($moderator)->post(route('admin.users.approve', $talent));

        $this->assertDatabaseHas('moderation_requests', [
            'requested_by' => $moderator->id,
            'target_user_id' => $talent->id,
            'action_type' => ModerationRequest::ACTION_APPROVE_TALENT,
            'status' => ModerationRequest::STATUS_PENDING,
        ]);

        $this->assertTrue($talent->fresh()->isPendingApproval());

        $request = ModerationRequest::first();
        $this->actingAs($admin)->post(route('admin.moderation.approve', $request));

        $this->assertTrue($talent->fresh()->isApproved());
        $this->assertNotNull($talent->fresh()->profile);
    }

    public function test_admin_approval_sends_email_to_talent(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($admin)->post(route('admin.users.approve', $talent));

        Mail::assertSent(TalentApprovedMail::class, function (TalentApprovedMail $mail) use ($talent) {
            return $mail->hasTo($talent->email) && $mail->user->is($talent->fresh());
        });
    }

    public function test_admin_rejection_sends_email_to_talent_with_optional_reason(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($admin)->post(route('admin.users.reject', $talent), [
            'reason' => 'Profil incomplet pour le moment.',
        ]);

        Mail::assertSent(TalentRejectedMail::class, function (TalentRejectedMail $mail) use ($talent) {
            return $mail->hasTo($talent->email)
                && $mail->user->is($talent->fresh())
                && $mail->reason === 'Profil incomplet pour le moment.';
        });
    }

    public function test_moderator_approval_request_sends_email_after_admin_confirms(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $moderator = User::factory()->create(['role' => 'moderator', 'approval_status' => null]);
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($moderator)->post(route('admin.users.approve', $talent));
        Mail::assertNothingSent();

        $request = ModerationRequest::first();
        $this->actingAs($admin)->post(route('admin.moderation.approve', $request));

        Mail::assertSent(TalentApprovedMail::class, fn (TalentApprovedMail $mail) => $mail->hasTo($talent->email));
    }

    public function test_only_admin_can_grant_moderator_role(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator', 'approval_status' => null]);
        $talent = User::factory()->create(['role' => 'dev', 'approval_status' => User::APPROVAL_APPROVED]);

        $this->actingAs($moderator)->post(route('admin.users.moderator.grant', $talent))->assertForbidden();
    }
}
