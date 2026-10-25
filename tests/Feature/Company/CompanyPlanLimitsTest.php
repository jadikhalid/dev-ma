<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\JobPostingActivityEvent;
use App\Models\JobPosting;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPlanLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_essential_plan_blocks_a_second_published_job(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner(CompanyProfile::PLAN_ESSENTIAL);
        [$sector, $profession] = $this->makeProfessionCatalog();

        JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'First role',
            'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $draft = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Second role',
            'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
            'status' => JobPosting::STATUS_DRAFT,
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $this->actingAs($owner)
            ->post(route('company.jobs.publish', $draft))
            ->assertRedirect(route('company.jobs.show', $draft))
            ->assertSessionHas('toast_error');

        $this->assertSame(JobPosting::STATUS_DRAFT, $draft->fresh()->status);
    }

    public function test_trial_plan_allows_five_published_jobs_and_blocks_sixth(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner(CompanyProfile::PLAN_TRIAL);

        for ($i = 1; $i <= 5; $i++) {
            JobPosting::create([
                'company_profile_id' => $profile->id,
                'created_by' => $owner->id,
                'title' => "Role {$i}",
                'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
                'status' => JobPosting::STATUS_PUBLISHED,
                'published_at' => now(),
                'remote_ok' => true,
                'work_modes' => ['remote'],
            ]);
        }

        $draft = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Role 6',
            'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
            'status' => JobPosting::STATUS_DRAFT,
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $this->assertFalse($profile->fresh()->canPublishAnotherJob());

        $this->actingAs($owner)
            ->post(route('company.jobs.publish', $draft))
            ->assertSessionHas('toast_error');

        $this->assertSame(JobPosting::STATUS_DRAFT, $draft->fresh()->status);
    }

    public function test_trial_and_essential_cannot_create_sourcing_requests(): void
    {
        foreach ([CompanyProfile::PLAN_TRIAL, CompanyProfile::PLAN_ESSENTIAL] as $plan) {
            [$owner] = $this->makeCompanyOwner($plan);

            $this->actingAs($owner)
                ->post(route('recruitment.store'), [
                    'role_title' => 'Ingénieur backend senior',
                    'need' => str_repeat('Besoin détaillé de sourcing pour une mission longue. ', 3),
                ])
                ->assertRedirect(route('sourcing.index'))
                ->assertSessionHas('toast_error');
        }

        $this->assertSame(0, RecruitmentRequest::query()->count());
    }

    public function test_pro_plan_allows_five_sourcing_credits_then_blocks(): void
    {
        [$owner] = $this->makeCompanyOwner(CompanyProfile::PLAN_PRO);

        for ($i = 1; $i <= 5; $i++) {
            $this->actingAs($owner)
                ->post(route('recruitment.store'), [
                    'role_title' => "Mission sourcing {$i}",
                    'need' => str_repeat('Besoin détaillé de sourcing pour une mission longue. ', 3),
                ])
                ->assertRedirect();
        }

        $this->assertSame(5, RecruitmentRequest::query()->count());

        $this->actingAs($owner)
            ->post(route('recruitment.store'), [
                'role_title' => 'Mission sourcing 6',
                'need' => str_repeat('Besoin détaillé de sourcing pour une mission longue. ', 3),
            ])
            ->assertRedirect(route('sourcing.index'))
            ->assertSessionHas('toast_error');

        $this->assertSame(5, RecruitmentRequest::query()->count());
    }

    public function test_offers_page_shows_trial_as_standard_without_sourcing(): void
    {
        $this->get(route('company.offers'))
            ->assertOk()
            ->assertSee(__('talenma.company_plans.plans.trial.features.2'), false)
            ->assertSee(__('talenma.company_plans.compare.values.sourcing_pro'), false)
            ->assertDontSee('Intermédiation et page marque employeur', false);
    }

    public function test_dashboard_shows_plan_usage_card_for_trial_essential_and_pro(): void
    {
        [$trialOwner, $trialProfile] = $this->makeCompanyOwner(CompanyProfile::PLAN_TRIAL);
        JobPosting::create([
            'company_profile_id' => $trialProfile->id,
            'created_by' => $trialOwner->id,
            'title' => 'Published role',
            'description' => str_repeat('Description du poste pour annonce entreprise. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $this->actingAs($trialOwner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('process-help-company-dashboard', false)
            ->assertSee('data-company-org-column', false)
            ->assertSee('lg:grid-cols-[minmax(0,25fr)_minmax(0,50fr)_minmax(0,25fr)]', false)
            ->assertSee('order-1 min-w-0 space-y-4 lg:order-none', false)
            ->assertSee('order-2 min-w-0 space-y-3 lg:order-none', false)
            ->assertSee('order-3 min-w-0 lg:order-none', false)
            ->assertSee(__('talenma.dashboard.company.my_company'), false)
            ->assertSee(__('talenma.company.section_contact'), false)
            ->assertSeeText(__('talenma.company.section_identity_desc'))
            ->assertSee('data-company-plan-usage', false)
            ->assertSee('data-plan="trial"', false)
            ->assertSee(__('talenma.dashboard.company.plan_usage.credits_not_included'), false)
            ->assertSee('1 / 5', false);

        [$essentialOwner] = $this->makeCompanyOwner(CompanyProfile::PLAN_ESSENTIAL);
        $this->actingAs($essentialOwner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-plan="essential"', false)
            ->assertSee(__('talenma.dashboard.company.plan_usage.credits_not_included'), false)
            ->assertSee(__('talenma.dashboard.company.plan_usage.page_standard'), false);

        [$proOwner] = $this->makeCompanyOwner(CompanyProfile::PLAN_PRO);
        $this->actingAs($proOwner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-plan="pro"', false)
            ->assertSee(__('talenma.dashboard.company.plan_usage.page_enriched'), false)
            ->assertSee('5 / 5', false);
    }

    public function test_company_activity_feed_returns_the_next_page(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner(CompanyProfile::PLAN_TRIAL);

        foreach (range(1, 12) as $index) {
            JobPostingActivityEvent::query()->create([
                'company_profile_id' => $profile->id,
                'job_title' => sprintf('Annonce %02d', $index),
                'event' => JobPostingActivityEvent::EVENT_PUBLISHED,
                'actor_label' => 'Owner',
                'actor_user_id' => $owner->id,
                'is_self' => true,
                'created_at' => now()->subMinutes(13 - $index),
            ]);
        }

        $first = $this->actingAs($owner)
            ->getJson(route('dashboard.company-activity', ['offset' => 0]))
            ->assertOk()
            ->assertJsonPath('has_more', true)
            ->assertJsonPath('next_offset', 10);

        $this->assertSame(10, substr_count($first->json('html'), '<li'));
        $this->assertStringContainsString('Annonce 12', $first->json('html'));
        $this->assertStringNotContainsString('Annonce 01', $first->json('html'));

        $this->actingAs($owner)
            ->getJson(route('dashboard.company-activity', ['offset' => 10]))
            ->assertOk()
            ->assertJsonPath('has_more', false)
            ->assertJsonPath('next_offset', 12)
            ->assertSee('Annonce 01', false)
            ->assertSee('Annonce 02', false);

        $this->actingAs(User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]))->getJson(route('dashboard.company-activity'))->assertForbidden();
    }

    /**
     * @return array{0: User, 1: CompanyProfile}
     */
    private function makeCompanyOwner(string $plan): array
    {
        $owner = User::factory()->companyOwner()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $profile = CompanyProfile::factory()->create([
            'user_id' => $owner->id,
            'plan' => $plan,
            'is_subscribed' => $plan !== CompanyProfile::PLAN_TRIAL,
            'trial_ends_at' => $plan === CompanyProfile::PLAN_TRIAL ? now()->addMonths(3) : null,
            'subscription_expires_at' => $plan !== CompanyProfile::PLAN_TRIAL ? now()->addYear() : null,
            'country' => 'fr',
            'city' => 'Paris',
        ]);

        return [$owner->fresh(), $profile];
    }

    /**
     * @return array{0: ProfessionSector, 1: Profession}
     */
    private function makeProfessionCatalog(): array
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'tech-'.uniqid(),
            'name_fr' => 'Tech',
            'name_en' => 'Tech',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-'.uniqid(),
            'name_fr' => 'Développeur',
            'name_en' => 'Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return [$sector, $profession];
    }
}
