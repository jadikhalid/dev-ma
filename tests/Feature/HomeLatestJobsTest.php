<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\ProfessionSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeLatestJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_latest_jobs_linking_to_jobs_gate(): void
    {
        $job = $this->seedPublishedJob('Fullstack Maroc');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('talenma.home.latest_jobs_title'), false)
            ->assertSee('Fullstack Maroc', false)
            ->assertSee('Description annonce publique.', false)
            ->assertSee('ACME Maroc', false)
            ->assertSee('Technologie', false)
            ->assertSee(route('jobs.gate', $job), false)
            ->assertSee(route('jobs.gate'), false);
    }

    public function test_guest_job_gate_redirects_to_login_then_talent_job_after_auth(): void
    {
        $job = $this->seedPublishedJob('Mission gate talent');
        $talent = User::factory()->talent()->create([
            'email' => 'talent.gate@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->get(route('jobs.gate', $job))
            ->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $talent->email,
            'password' => 'password',
        ])->assertRedirect(route('jobs.gate', $job));

        $this->get(route('jobs.gate', $job))
            ->assertRedirect(route('talent.jobs.show', $job));
    }

    public function test_guest_jobs_index_gate_redirects_to_company_jobs_after_auth(): void
    {
        [$owner] = $this->makeCompanyOwner();
        $owner->forceFill([
            'email' => 'company.gate@example.com',
            'password' => bcrypt('password'),
        ])->save();

        $this->get(route('jobs.gate'))
            ->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => 'company.gate@example.com',
            'password' => 'password',
        ])->assertRedirect(route('jobs.gate'));

        $this->get(route('jobs.gate'))
            ->assertRedirect(route('company.jobs.index'));
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

    public function test_home_latest_jobs_shows_at_most_ten_published_jobs(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();

        for ($i = 1; $i <= 11; $i++) {
            JobPosting::create([
                'company_profile_id' => $profile->id,
                'created_by' => $owner->id,
                'title' => sprintf('Annonce limite #%02d', $i),
                'description' => str_repeat(sprintf('Description annonce limite #%02d. ', $i), 3),
                'status' => JobPosting::STATUS_PUBLISHED,
                'published_at' => now()->subMinutes(12 - $i),
                'remote_ok' => true,
                'work_modes' => ['remote'],
            ]);
        }

        $response = $this->get(route('home'))->assertOk();

        $response
            ->assertSee('data-initial-count="10"', false)
            ->assertSee('Annonce limite #11', false)
            ->assertSee('Annonce limite #02', false)
            ->assertDontSee('Annonce limite #01', false);
    }

    private function seedPublishedJob(string $title): JobPosting
    {
        [$owner, $profile] = $this->makeCompanyOwner();
        $sector = ProfessionSector::query()->create([
            'slug' => 'technologie-'.uniqid(),
            'name_fr' => 'Technologie',
            'name_en' => 'Technology',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'profession_sector_id' => $sector->id,
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
