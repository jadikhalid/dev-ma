<?php

namespace Tests\Feature;

use App\Models\CompanyMembership;
use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\User;
use App\Services\UserDeletionService;
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

    public function test_deleting_company_owner_also_deletes_all_attached_members(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $firstMember = $this->makeCompanyMember($owner, $profile);
        $secondMember = $this->makeCompanyMember($owner, $profile);
        $secondMember->update([
            'email' => 'second.member@example.com',
            'name' => 'JADI DIGITAL / Amine Alaoui',
            'first_name' => 'Amine',
            'last_name' => 'Alaoui',
        ]);

        $ownerId = $owner->id;
        $profileId = $profile->id;
        $memberIds = [$firstMember->id, $secondMember->id];

        app(UserDeletionService::class)->delete($owner);

        $this->assertDatabaseMissing('users', ['id' => $ownerId]);
        $this->assertDatabaseMissing('company_profiles', ['id' => $profileId]);

        foreach ($memberIds as $memberId) {
            $this->assertDatabaseMissing('users', ['id' => $memberId]);
            $this->assertDatabaseMissing('company_memberships', ['user_id' => $memberId]);
        }
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

    public function test_company_can_create_job_without_profession_and_matching_uses_sector(): void
    {
        [$owner] = $this->makeCompanyOwner();
        [$sector, $profession] = $this->makeProfessionCatalog();

        $this->actingAs($owner)
            ->post(route('company.jobs.store'), [
                'title' => 'Chef de projet',
                'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
                'sector' => $sector->slug,
                'profession' => '',
                'experience_level' => JobPosting::EXPERIENCE_JUNIOR,
                'work_modes' => ['remote'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $job = JobPosting::query()->sole();
        $this->assertSame($sector->id, $job->profession_sector_id);
        $this->assertNull($job->profession_id);

        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'experience_years' => 4,
            'work_modes' => ['remote'],
        ]);

        $this->assertTrue($job->matchesTalentProfile($talent->fresh()));
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

    public function test_talent_job_cards_show_publication_date_and_only_content_updates(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $talent = User::factory()->talent()->create();

        $this->travelTo(now()->subDays(5));
        $job = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Backend engineer',
            'description' => str_repeat('Looking for experienced backend talent. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'work_modes' => ['remote'],
        ]);
        $this->travelBack();

        $card = fn () => collect($this->actingAs($talent)
            ->getJson(route('talent.jobs.index'))
            ->assertOk()
            ->json('jobs'))->firstWhere('id', $job->id);

        $this->assertNotNull($card()['published_label']);
        $this->assertNull($card()['updated_label']);

        $this->actingAs($talent)->post(route('talent.jobs.apply', $job), ['cover_message' => 'Motivé.']);
        $job->refresh()->update(['staff_seen_at' => now()]);
        $this->assertNull($card()['updated_label']);

        $job->refresh()->update(['title' => 'Senior backend engineer']);
        $this->assertNotNull($card()['updated_label']);
    }

    public function test_talent_jobs_index_is_paginated_by_ten(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $talent = User::factory()->talent()->create();

        foreach (range(1, 12) as $i) {
            JobPosting::create([
                'company_profile_id' => $profile->id,
                'created_by' => $owner->id,
                'title' => "Job {$i}",
                'description' => str_repeat('Looking for experienced backend talent. ', 3),
                'status' => JobPosting::STATUS_PUBLISHED,
                'published_at' => now()->subMinutes(20 - $i),
            ]);
        }

        $first = $this->actingAs($talent)->getJson(route('talent.jobs.index'))->assertOk();
        $this->assertCount(10, $first->json('jobs'));
        $this->assertSame(['page' => 1, 'last_page' => 2, 'from' => 1, 'to' => 10, 'total' => 12], $first->json('pagination'));
        $this->assertSame(12, $first->json('total'));

        $second = $this->actingAs($talent)->getJson(route('talent.jobs.index', ['page' => 2]))->assertOk();
        $this->assertCount(2, $second->json('jobs'));
        $this->assertSame(11, $second->json('pagination.from'));

        $this->actingAs($talent)
            ->get(route('talent.jobs.index'))
            ->assertOk()
            ->assertSee('Annonces', false);
    }

    public function test_talent_jobs_keyword_filters_results_and_keeps_scope_counts(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $talent = User::factory()->talent()->create();

        foreach (['Berlin role', 'Casablanca role'] as $title) {
            JobPosting::create([
                'company_profile_id' => $profile->id,
                'created_by' => $owner->id,
                'title' => $title,
                'description' => str_repeat('Looking for experienced backend talent. ', 3),
                'status' => JobPosting::STATUS_PUBLISHED,
                'published_at' => now(),
                'location_city' => str_contains($title, 'Berlin') ? 'Berlin' : 'Casablanca',
            ]);
        }

        $filtered = $this->actingAs($talent)
            ->getJson(route('talent.jobs.index', ['q' => 'Berlin']))
            ->assertOk();

        $this->assertSame(['Berlin role'], collect($filtered->json('jobs'))->pluck('title')->all());
        $this->assertSame('Berlin', $filtered->json('q'));
        $this->assertSame(1, $filtered->json('pagination.total'));
        $this->assertSame(2, $filtered->json('counts.all'));

        $this->actingAs($talent)
            ->getJson(route('talent.jobs.index', ['q' => 'JADI']))
            ->assertOk()
            ->assertJsonPath('pagination.total', 2);

        $this->actingAs($talent)
            ->getJson(route('talent.jobs.index', ['q' => 'a']))
            ->assertOk()
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonPath('q', 'a');

        $this->actingAs($talent)
            ->getJson(route('talent.jobs.index', ['q' => 'Berlin', 'sector' => 'it', 'profession' => 'dev']))
            ->assertOk()
            ->assertJsonPath('q', 'Berlin')
            ->assertJsonPath('sector', '')
            ->assertJsonPath('profession', '')
            ->assertJsonPath('pagination.total', 1);

        $this->actingAs($talent)
            ->get(route('talent.jobs.index', ['q' => 'Berlin']))
            ->assertOk()
            ->assertSee('id="talent-jobs-query"', false)
            ->assertSee(__('talenma.jobs.search_free'), false)
            ->assertSee(__('talenma.jobs.search_manual'), false)
            ->assertSee(__('talenma.career_pages.offers_search_placeholder'), false)
            ->assertSee('value="Berlin"', false)
            ->assertSee('Berlin role', false)
            ->assertDontSee('Casablanca role', false)
            ->assertDontSee(__('talenma.home.search_submit').'</button>', false);
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
