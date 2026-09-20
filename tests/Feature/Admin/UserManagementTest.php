<?php

namespace Tests\Feature\Admin;

use App\Mail\TalentApprovedMail;
use App\Mail\TalentRejectedMail;
use App\Models\PendingRegistration;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderator_approval_is_executed_immediately_when_permitted(): void
    {
        $moderator = User::factory()->moderator([
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_APPROVE,
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->withSession([\App\Services\ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.approve', $talent));

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

    public function test_moderator_approval_sends_email_immediately(): void
    {
        Mail::fake();

        $moderator = User::factory()->moderator([
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_APPROVE,
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->withSession([\App\Services\ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.approve', $talent));

        Mail::assertSent(TalentApprovedMail::class, fn (TalentApprovedMail $mail) => $mail->hasTo($talent->email));
    }

    public function test_only_admin_can_grant_moderator_role(): void
    {
        $moderator = User::factory()->moderator([
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();
        $talent = User::factory()->create(['role' => 'dev', 'approval_status' => User::APPROVAL_APPROVED]);

        $this->withSession([\App\Services\ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.moderator.grant', $talent))
            ->assertForbidden();
    }

    public function test_staff_can_view_pending_registration_details(): void
    {
        $this->seed(ProfessionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = ProfessionSector::query()->firstOrFail();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
            'name' => 'Talent En Attente',
            'email' => 'pending@example.com',
        ]);
        $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'bio' => 'Développeur full-stack avec cinq ans d\'expérience.',
            'experience_years' => 0,
            'country' => 'Maroc',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.users.registration', $talent))
            ->assertOk()
            ->assertJsonPath('name', 'Talent En Attente')
            ->assertJsonPath('email', 'pending@example.com')
            ->assertJsonPath('current_profile.bio', 'Développeur full-stack avec cinq ans d\'expérience.')
            ->assertJsonPath('current_profile.sector', $sector->localizedName());
    }

    public function test_staff_can_view_approved_talent_dossier(): void
    {
        $this->seed(ProfessionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = ProfessionSector::query()->firstOrFail();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_APPROVED,
            'first_name' => 'Karim',
            'last_name' => 'Benali',
            'name' => 'Karim Benali',
            'email' => 'approved@example.com',
        ]);
        $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'profession_id' => Profession::query()->where('profession_sector_id', $sector->id)->value('id'),
            'specialization' => 'Laravel, API REST',
            'bio' => 'Bio actuelle du talent.',
            'experience_years' => 5,
            'country' => 'Maroc',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.users.registration', $talent))
            ->assertOk()
            ->assertJsonPath('first_name', 'Karim')
            ->assertJsonPath('last_name', 'Benali')
            ->assertJsonPath('is_pending', false)
            ->assertJsonPath('current_profile.specialization', 'Laravel, API REST')
            ->assertJsonPath('current_profile.bio', 'Bio actuelle du talent.');
    }

    public function test_staff_can_view_profile_document_through_secure_route(): void
    {
        $this->seed(ProfessionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = ProfessionSector::query()->firstOrFail();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);
        $profile = $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'bio' => 'Profil test.',
            'experience_years' => 0,
            'country' => 'Maroc',
        ]);

        $path = 'profile-documents/'.$profile->id.'/test.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, '%PDF-1.4 test');
        $document = $profile->documents()->create([
            'path' => $path,
            'original_name' => 'diploma.pdf',
            'mime_type' => 'application/pdf',
            'size' => 128,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.profile-documents.show', $document))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_delete_user_removes_database_records_files_and_pending_registration(): void
    {
        $this->seed(ProfessionSeeder::class);

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = ProfessionSector::query()->firstOrFail();
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
            'email' => 'delete-me@example.com',
            'avatar_path' => 'avatars/99.jpg',
        ]);
        $profile = $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'bio' => 'Profil à supprimer.',
            'experience_years' => 0,
            'country' => 'Maroc',
        ]);

        $documentPath = 'profile-documents/'.$profile->id.'/test.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($documentPath, '%PDF-1.4 test');
        \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/99.jpg', 'avatar-data');
        $profile->documents()->create([
            'path' => $documentPath,
            'original_name' => 'diploma.pdf',
            'mime_type' => 'application/pdf',
            'size' => 128,
            'sort_order' => 1,
        ]);

        PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'delete-me@example.com',
            'locale' => 'fr',
            'payload' => ['name' => 'Pending', 'password' => bcrypt('Password1'), 'role' => 'dev'],
            'expires_at' => now()->addMinutes(5),
        ]);

        DB::table('sessions')->insert([
            'id' => 'test-session-id',
            'user_id' => $talent->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'payload' => 'test',
            'last_activity' => time(),
        ]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $talent));

        $this->assertDatabaseMissing('users', ['email' => 'delete-me@example.com']);
        $this->assertDatabaseMissing('profiles', ['id' => $profile->id]);
        $this->assertDatabaseMissing('profile_documents', ['path' => $documentPath]);
        $this->assertDatabaseMissing('pending_registrations', ['email' => 'delete-me@example.com']);
        $this->assertDatabaseMissing('sessions', ['id' => 'test-session-id']);
        $this->assertFalse(\Illuminate\Support\Facades\Storage::disk('public')->exists($documentPath));
        $this->assertFalse(\Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/99.jpg'));
    }

    public function test_admin_can_search_users_by_name_within_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        User::factory()->create([
            'role' => 'dev',
            'name' => 'Khalid Benali',
            'first_name' => 'Khalid',
            'last_name' => 'Benali',
            'email' => 'khalid.benali@example.com',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        User::factory()->create([
            'role' => 'dev',
            'name' => 'Sara Amrani',
            'first_name' => 'Sara',
            'last_name' => 'Amrani',
            'email' => 'sara.amrani@example.com',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        $company = User::factory()->create([
            'role' => 'company',
            'company_seat' => User::SEAT_OWNER,
            'name' => 'Acme Europe',
            'email' => 'contact@acme-europe.test',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);
        $company->companyProfile()->create([
            'representative_name' => 'Jean Dupont',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['filter' => 'talents', 'q' => 'Benali']))
            ->assertOk()
            ->assertSee('Khalid Benali')
            ->assertDontSee('Sara Amrani')
            ->assertDontSee('Acme Europe SAS');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['filter' => 'companies', 'q' => 'Acme']))
            ->assertOk()
            ->assertSee('Acme Europe')
            ->assertDontSee('Khalid Benali');
    }

    public function test_admin_can_list_pending_email_registrations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'awaiting@example.com',
            'locale' => 'fr',
            'payload' => [
                'first_name' => 'Amina',
                'last_name' => 'Saidi',
                'name' => 'Amina Saidi',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
                'sector' => 'it-digital',
                'description' => 'Description suffisamment longue pour validation.',
            ],
            'expires_at' => now()->addHours(12),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['filter' => 'email_pending']))
            ->assertOk()
            ->assertSee('Amina Saidi')
            ->assertSee('awaiting@example.com')
            ->assertSee(__('talenma.admin.users.filter_email_pending'));
    }

    public function test_admin_can_delete_pending_email_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'purge-me@example.com',
            'locale' => 'fr',
            'payload' => [
                'name' => 'Purge Me',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
            ],
            'expires_at' => now()->addHour(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.pending-registrations.destroy', $pending))
            ->assertRedirect();

        $this->assertDatabaseMissing('pending_registrations', ['email' => 'purge-me@example.com']);
    }

    public function test_admin_can_complete_pending_email_registration_without_logging_in_as_user(): void
    {
        $this->seed(ProfessionSeeder::class);
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'force-verify@example.com',
            'locale' => 'fr',
            'payload' => [
                'first_name' => 'Force',
                'last_name' => 'Verify',
                'name' => 'Force Verify',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
                'sector' => ProfessionSector::query()->firstOrFail()->slug,
                'description' => 'Description suffisamment longue pour validation.',
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.pending-registrations.complete', $pending));

        $user = User::query()->where('email', 'force-verify@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseMissing('pending_registrations', ['email' => 'force-verify@example.com']);
        $this->assertAuthenticatedAs($admin);

        $response->assertRedirect(route('admin.users.index', [
            'filter' => $user->isPendingApproval() ? 'pending' : 'talents',
        ]));
    }

    public function test_admin_can_resend_pending_email_and_extend_expired_link(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'resend@example.com',
            'locale' => 'fr',
            'payload' => [
                'name' => 'Resend User',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
            ],
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.pending-registrations.resend', $pending))
            ->assertRedirect();

        $pending->refresh();
        $this->assertTrue($pending->expires_at->isFuture());
        Mail::assertSent(\App\Mail\VerifyRegistrationMail::class, fn ($mail) => $mail->hasTo('resend@example.com'));
    }

    public function test_moderator_without_approve_cannot_complete_pending_registration(): void
    {
        $moderator = User::factory()->moderator([
            \App\Models\ModeratorPermissionCatalog::ACCOUNTS_VIEW,
        ])->create();

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'no-approve@example.com',
            'locale' => 'fr',
            'payload' => [
                'name' => 'No Approve',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
            ],
            'expires_at' => now()->addHour(),
        ]);

        $this->withSession([\App\Services\ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->post(route('admin.users.pending-registrations.complete', $pending))
            ->assertForbidden();
    }
}
