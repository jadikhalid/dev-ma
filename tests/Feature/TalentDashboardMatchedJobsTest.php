<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TalentDashboardMatchedJobsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function incomplete_profile_shows_matching_gate_without_job_cards(): void
    {
        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'bio' => 'Short',
        ]);

        [$owner, $company] = $this->makeCompany();
        [$sector, $profession] = $this->makeProfessionCatalog();
        $this->makePublishedJob($owner, $company, $sector, $profession, 'Laravel engineer');

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('talenma.dashboard.talent.matched_jobs_gate_title'), false)
            ->assertDontSee('Laravel engineer', false)
            ->assertSee('data-talent-matched-jobs', false);
    }

    #[Test]
    public function catalog_ready_profile_sees_matching_published_job(): void
    {
        [$sector, $profession] = $this->makeProfessionCatalog();
        $talent = $this->makeCatalogReadyTalent($sector, $profession);

        [$owner, $company] = $this->makeCompany();
        $this->makePublishedJob($owner, $company, $sector, $profession, 'Matched remote role');

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(__('talenma.dashboard.talent.matched_jobs_gate_title'), false)
            ->assertSee('Matched remote role', false)
            ->assertSee(route('talent.jobs.show', JobPosting::query()->first()), false);
    }

    #[Test]
    public function sector_only_job_without_profession_still_matches(): void
    {
        [$sector, $profession] = $this->makeProfessionCatalog();
        $talent = $this->makeCatalogReadyTalent($sector, $profession);

        [$owner, $company] = $this->makeCompany();
        $job = $this->makePublishedJob($owner, $company, $sector, $profession, 'Sector-wide consulting role');
        $job->forceFill(['profession_id' => null])->save();

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sector-wide consulting role', false);
    }

    #[Test]
    public function external_style_job_without_xp_or_modes_still_matches_on_sector(): void
    {
        [$sector, $profession] = $this->makeProfessionCatalog();
        $talent = $this->makeCatalogReadyTalent($sector, $profession);

        [$owner, $company] = $this->makeCompany();
        $job = $this->makePublishedJob($owner, $company, $sector, $profession, 'EY graduate programme');
        $job->forceFill([
            'profession_id' => null,
            'experience_level' => null,
            'work_modes' => null,
        ])->save();

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('EY graduate programme', false);
    }

    #[Test]
    public function already_applied_jobs_are_excluded_from_matching_block(): void
    {
        [$sector, $profession] = $this->makeProfessionCatalog();
        $talent = $this->makeCatalogReadyTalent($sector, $profession);

        [$owner, $company] = $this->makeCompany();
        $job = $this->makePublishedJob($owner, $company, $sector, $profession, 'Already applied role');

        JobApplication::query()->create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $talent->id,
            'cover_message' => 'Interested in this role.',
            'status' => JobApplication::STATUS_RECEIVED,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('talenma.jobs.talent_empty_matching'), false);

        // Title may still appear in recent activity; the matching cards must stay empty.
        $this->assertStringNotContainsString(
            'group-hover:text-emerald-800">Already applied role',
            $response->getContent(),
        );
    }

    /**
     * @return array{0: User, 1: CompanyProfile}
     */
    private function makeCompany(): array
    {
        $owner = User::factory()->companyOwner()->create([
            'name' => 'Match Co',
        ]);
        $profile = CompanyProfile::factory()->create([
            'user_id' => $owner->id,
        ]);

        return [$owner->fresh(), $profile];
    }

    /**
     * @return array{0: ProfessionSector, 1: Profession}
     */
    private function makeProfessionCatalog(): array
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it-match',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'fullstack-match',
            'name_fr' => 'Développeur Full-Stack',
            'name_en' => 'Full-Stack Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return [$sector, $profession];
    }

    private function makeCatalogReadyTalent(ProfessionSector $sector, Profession $profession): User
    {
        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $talent->profile()->create([
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'specialization' => 'Laravel, Vue',
            'bio' => 'Développeur full-stack avec plusieurs années d’expérience sur des produits web.',
            'experience_years' => 5,
            'is_fresh_graduate' => false,
            'education_level' => 'bac+5',
            'languages' => ['fr', 'en'],
            'availability' => 'available',
            'work_modes' => ['remote'],
            'country' => 'MA',
            'city' => 'Casablanca',
        ]);

        return $talent->fresh(['profile']);
    }

    private function makePublishedJob(
        User $owner,
        CompanyProfile $profile,
        ProfessionSector $sector,
        Profession $profession,
        string $title,
    ): JobPosting {
        return JobPosting::query()->create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => $title,
            'description' => str_repeat('Looking for an experienced full-stack developer. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'experience_level' => JobPosting::EXPERIENCE_JUNIOR,
            'work_modes' => ['remote'],
            'remote_ok' => true,
        ]);
    }
}
