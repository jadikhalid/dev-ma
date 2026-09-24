<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicJobShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_published_job_without_login(): void
    {
        $job = $this->seedPublishedJob('Mission publique');

        $this->get(route('jobs.public.show', $job))
            ->assertOk()
            ->assertSee('Mission publique', false)
            ->assertSee(__('talenma.jobs.public_login_cta'), false)
            ->assertSee(__('talenma.jobs.public_register_cta'), false)
            ->assertSee(route('jobs.gate', $job), false)
            ->assertDontSee(__('talenma.jobs.apply_external_cta'), false);
    }

    #[Test]
    public function guest_cannot_view_draft_or_closed_job(): void
    {
        [$owner, $profile] = $this->makeCompanyOwner();

        $draft = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Brouillon',
            'description' => str_repeat('Description brouillon. ', 3),
            'status' => JobPosting::STATUS_DRAFT,
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $closed = JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => 'Cloturee',
            'description' => str_repeat('Description cloturee. ', 3),
            'status' => JobPosting::STATUS_CLOSED,
            'published_at' => now()->subDay(),
            'closed_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);

        $this->get(route('jobs.public.show', $draft))->assertNotFound();
        $this->get(route('jobs.public.show', $closed))->assertNotFound();
    }

    #[Test]
    public function approved_talent_is_redirected_to_talent_job_show(): void
    {
        $job = $this->seedPublishedJob('Mission talent');
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('jobs.public.show', $job))
            ->assertRedirect(route('talent.jobs.show', $job));
    }

    #[Test]
    public function register_from_job_stores_gate_as_intended_url(): void
    {
        $job = $this->seedPublishedJob('Mission inscription');

        $this->get(route('register', ['role' => 'dev', 'from_job' => $job->id]))
            ->assertOk()
            ->assertSessionHas('url.intended', route('jobs.gate', $job));
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
