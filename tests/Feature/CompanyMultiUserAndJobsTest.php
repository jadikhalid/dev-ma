<?php

namespace Tests\Feature;

use App\Models\CompanyMembership;
use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyMultiUserAndJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_delete_company_member(): void
    {
        [$owner] = $this->makeCompanyOwner();

        $response = $this->actingAs($owner)->post(route('company.users.store'), [
            'first_name' => 'Sara',
            'last_name' => 'Benali',
            'email' => 'sara@example.com',
            'job_title' => 'RH',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('profile.edit', ['panel' => 'account']));

        $member = User::query()->where('email', 'sara@example.com')->first();
        $this->assertNotNull($member);
        $this->assertTrue($member->isCompanyMember());
        $this->assertDatabaseHas('company_memberships', [
            'user_id' => $member->id,
            'company_profile_id' => $owner->companyProfile->id,
        ]);

        $memberId = $member->id;

        $this->actingAs($owner)
            ->delete(route('company.users.destroy', $member))
            ->assertRedirect(route('profile.edit', ['panel' => 'account']));

        $this->assertNull(User::query()->find($memberId));
        $this->assertDatabaseMissing('users', ['email' => 'sara@example.com']);
        $this->assertDatabaseMissing('company_memberships', [
            'user_id' => $memberId,
        ]);

        // Same email can be reused after hard delete.
        $this->actingAs($owner)->post(route('company.users.store'), [
            'first_name' => 'Sara',
            'last_name' => 'Benali',
            'email' => 'sara@example.com',
            'job_title' => 'RH',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('profile.edit', ['panel' => 'account']));

        $this->assertDatabaseHas('users', ['email' => 'sara@example.com']);
    }

    public function test_member_cannot_access_company_profile_or_user_management(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);

        $this->actingAs($member)->get(route('company.profile.edit'))->assertForbidden();
        $this->actingAs($member)
            ->get(route('profile.edit', ['panel' => 'company']))
            ->assertRedirect(route('profile.edit', ['panel' => 'account']));
        $this->actingAs($member)->post(route('company.users.store'), [
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'email' => 'ali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertForbidden();
    }

    public function test_member_cannot_change_identity_fields_on_profile(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);

        $this->actingAs($member)->patch(route('profile.update'), [
            'first_name' => 'Hacker',
            'last_name' => 'Name',
            'email' => 'hacked@example.com',
        ])->assertRedirect();

        $member->refresh();
        $this->assertSame('Sara', $member->first_name);
        $this->assertSame('Benali', $member->last_name);
        $this->assertNotSame('hacked@example.com', $member->email);
    }

    public function test_owner_can_update_company_member_identity(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);

        $this->actingAs($owner)->put(route('company.users.update', $member), [
            'first_name' => 'Sara',
            'last_name' => 'Amrani',
            'email' => 'sara.amrani@example.com',
            'job_title' => 'Finance',
        ])->assertRedirect(route('profile.edit', ['panel' => 'account']));

        $member->refresh();
        $this->assertSame('Amrani', $member->last_name);
        $this->assertSame('sara.amrani@example.com', $member->email);
        $this->assertSame('Finance', $member->companyMembership?->job_title);
    }

    public function test_owner_and_member_can_manage_jobs(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);
        [$sector, $profession] = $this->makeProfessionCatalog();

        $payload = [
            'title' => 'Développeur Laravel',
            'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
            'sector' => $sector->slug,
            'profession' => $profession->slug,
            'experience_level' => JobPosting::EXPERIENCE_JUNIOR,
            'contract_type' => 'cdi',
            'location_country' => 'fr',
            'location_city' => 'Paris',
            'work_modes' => ['remote', 'local'],
        ];

        $this->actingAs($owner)
            ->post(route('company.jobs.store'), $payload)
            ->assertRedirect();

        $job = JobPosting::query()->first();
        $this->assertNotNull($job);
        $this->assertSame(JobPosting::STATUS_DRAFT, $job->status);
        $this->assertSame(['remote', 'local'], $job->work_modes);
        $this->assertTrue($job->remote_ok);

        $this->actingAs($member)
            ->post(route('company.jobs.publish', $job))
            ->assertRedirect();

        $this->assertTrue($job->fresh()->isPublished());

        $this->actingAs($member)
            ->put(route('company.jobs.update', $job), [
                ...$payload,
                'title' => 'Développeur Laravel Senior',
            ])
            ->assertRedirect(route('company.jobs.show', $job));

        $this->assertSame('Développeur Laravel Senior', $job->fresh()->title);
    }

    public function test_talent_can_apply_and_company_can_update_application_status(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);
        $talent = User::factory()->talent()->create();

        $job = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Backend engineer',
            'description' => str_repeat('Looking for experienced backend talent. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $this->actingAs($talent)
            ->post(route('talent.jobs.apply', $job), [
                'cover_message' => 'Je suis motivé pour ce poste.',
            ])
            ->assertRedirect(route('talent.jobs.show', $job));

        $application = JobApplication::query()->first();
        $this->assertNotNull($application);
        $this->assertSame(JobApplication::STATUS_RECEIVED, $application->status);

        $this->actingAs($member)
            ->patch(route('company.jobs.applications.update', [$job, $application]), [
                'status' => JobApplication::STATUS_VIEWED,
            ])
            ->assertRedirect();

        $this->assertSame(JobApplication::STATUS_VIEWED, $application->fresh()->status);

        $this->actingAs($member)
            ->from(route('company.jobs.show', $job))
            ->patch(route('company.jobs.applications.update', [$job, $application]), [
                'status' => JobApplication::STATUS_RECEIVED,
            ])
            ->assertRedirect(route('company.jobs.show', $job))
            ->assertSessionHas('toast_error');

        $this->assertSame(JobApplication::STATUS_VIEWED, $application->fresh()->status);

        $this->actingAs($member)
            ->patch(route('company.jobs.applications.update', [$job, $application]), [
                'status' => JobApplication::STATUS_CLOSED,
            ])
            ->assertRedirect();

        $this->assertSame(JobApplication::STATUS_CLOSED, $application->fresh()->status);

        $this->actingAs($member)
            ->from(route('company.jobs.show', $job))
            ->patch(route('company.jobs.applications.update', [$job, $application]), [
                'status' => JobApplication::STATUS_VIEWED,
            ])
            ->assertRedirect(route('company.jobs.show', $job))
            ->assertSessionHas('toast_error');

        $this->assertSame(JobApplication::STATUS_CLOSED, $application->fresh()->status);
    }

    public function test_member_cannot_apply_to_jobs_as_talent_route(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $member = $this->makeCompanyMember($owner, $profile);

        $job = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Role',
            'description' => str_repeat('Looking for experienced backend talent. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->actingAs($member)
            ->get(route('talent.jobs.index'))
            ->assertForbidden();

        $this->actingAs($member)
            ->post(route('talent.jobs.apply', $job))
            ->assertForbidden();
    }

    /**
     * @return array{0: ProfessionSector, 1: Profession}
     */
    private function makeProfessionCatalog(): array
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev',
            'name_fr' => 'Dev',
            'name_en' => 'Dev',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return [$sector, $profession];
    }

    /**
     * @return array{0: User, 1: CompanyProfile}
     */
    private function makeCompanyOwner(): array
    {
        $owner = User::factory()->companyOwner()->create([
            'name' => 'JADI DIGITAL',
        ]);

        $profile = CompanyProfile::factory()->create([
            'user_id' => $owner->id,
            'sector' => 'SaaS',
            'employee_count' => '11-50',
            'country' => 'fr',
            'city' => 'Paris',
            'hiring_needs' => 'Talents full-stack pour missions longue durée.',
        ]);

        return [$owner->fresh(), $profile];
    }

    private function makeCompanyMember(User $owner, CompanyProfile $profile): User
    {
        $member = User::factory()->companyMember()->create([
            'name' => 'JADI DIGITAL / Sara Benali',
            'first_name' => 'Sara',
            'last_name' => 'Benali',
        ]);

        CompanyMembership::create([
            'company_profile_id' => $profile->id,
            'user_id' => $member->id,
            'job_title' => 'RH',
            'created_by' => $owner->id,
        ]);

        return $member->fresh();
    }
}
