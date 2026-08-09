<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeLatestJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_latest_jobs_linking_to_login(): void
    {
        $this->seedPublishedJob('Fullstack Maroc');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('talenma.home.latest_jobs_title'), false)
            ->assertSee('Fullstack Maroc', false)
            ->assertSee('Description annonce publique.', false)
            ->assertSee('ACME Maroc', false)
            ->assertSee(route('login'), false);
    }

    public function test_approved_talent_sees_links_to_talent_job_pages(): void
    {
        $job = $this->seedPublishedJob('Mission React');
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('talent.jobs.show', $job), false)
            ->assertSee(route('talent.jobs.index'), false);
    }

    public function test_approved_company_sees_links_to_company_job_pages(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $job = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Annonce interne',
            'description' => str_repeat('Description annonce entreprise. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
            'location_city' => 'Casablanca',
        ]);

        $this->actingAs($owner)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('company.jobs.show', $job), false)
            ->assertSee(route('company.jobs.index'), false);
    }

    private function seedPublishedJob(string $title): JobPosting
    {
        [$owner, $profile] = $this->makeCompanyOwner();

        return JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => $title,
            'description' => str_repeat('Description annonce publique. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
            'location_city' => 'Rabat',
        ]);
    }

    /**
     * @return array{0: User, 1: CompanyProfile}
     */
    private function makeCompanyOwner(): array
    {
        $owner = User::factory()->companyOwner()->create([
            'name' => 'ACME Maroc',
        ]);

        $profile = CompanyProfile::factory()->create([
            'user_id' => $owner->id,
            'sector' => 'SaaS',
            'employee_count' => '11-50',
            'country' => 'ma',
            'city' => 'Casablanca',
            'hiring_needs' => 'Talents pour missions full remote.',
        ]);

        return [$owner->fresh(), $profile];
    }
}
