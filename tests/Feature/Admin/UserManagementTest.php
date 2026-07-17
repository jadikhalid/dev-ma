<?php

namespace Tests\Feature\Admin;

use App\Mail\TalentApprovedMail;
use App\Mail\TalentRejectedMail;
use App\Models\ModerationRequest;
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
            'registration_description' => 'Développeur full-stack avec cinq ans d\'expérience.',
            'experience_years' => 0,
            'country' => 'Maroc',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.users.registration', $talent))
            ->assertOk()
            ->assertJsonPath('name', 'Talent En Attente')
            ->assertJsonPath('email', 'pending@example.com')
            ->assertJsonPath('description', 'Développeur full-stack avec cinq ans d\'expérience.')
            ->assertJsonPath('sector', $sector->localizedName());
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
            'registration_description' => 'Description à l\'inscription.',
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
            ->assertJsonPath('description', 'Description à l\'inscription.')
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
            'registration_description' => 'Profil test.',
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
            'registration_description' => 'Profil à supprimer.',
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
            'name' => 'Acme Europe',
            'email' => 'contact@acme-europe.test',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);
        $company->companyProfile()->create([
            'company_name' => 'Acme Europe SAS',
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
}
