<?php

namespace Tests\Feature\Admin;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\ModeratorPermissionCatalog;
use App\Models\ProfessionSector;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExternalJobPostingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_create_and_publish_external_job(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = $this->makeSector();

        $this->actingAs($admin)
            ->post(route('admin.jobs.store'), $this->externalPayload($sector->slug))
            ->assertRedirect();

        $job = JobPosting::query()->first();
        $this->assertNotNull($job);
        $this->assertTrue($job->isExternalApplication());
        $this->assertNull($job->company_profile_id);
        $this->assertSame($sector->id, $job->profession_sector_id);
        $this->assertNull($job->profession_id);
        $this->assertNull($job->experience_level);
        $this->assertNull($job->work_modes);
        $this->assertSame('Acme Partners', $job->external_company_name);
        $this->assertSame(JobPosting::STATUS_DRAFT, $job->status);

        $this->actingAs($admin)
            ->post(route('admin.jobs.publish', $job))
            ->assertRedirect();

        $this->assertSame(JobPosting::STATUS_PUBLISHED, $job->fresh()->status);
    }

    #[Test]
    public function moderator_with_jobs_manage_can_create_external_job(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::JOBS_MANAGE,
        ])->create();
        $sector = $this->makeSector();

        $this->actingAs($moderator)
            ->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->post(route('admin.jobs.store'), $this->externalPayload($sector->slug))
            ->assertRedirect();

        $this->assertDatabaseHas('job_postings', [
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => 'Acme Partners',
            'created_by' => $moderator->id,
            'profession_sector_id' => $sector->id,
        ]);
    }

    #[Test]
    public function moderator_without_jobs_manage_cannot_create_external_job(): void
    {
        $moderator = User::factory()->moderator([])->create();
        $sector = $this->makeSector();

        $this->actingAs($moderator)
            ->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->post(route('admin.jobs.store'), $this->externalPayload($sector->slug))
            ->assertForbidden();
    }

    #[Test]
    public function talent_sees_external_advertiser_and_cannot_apply_internally(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = $this->makeSector();

        $job = JobPosting::query()->create([
            'company_profile_id' => null,
            'created_by' => $admin->id,
            'profession_sector_id' => $sector->id,
            'title' => 'External role',
            'description' => str_repeat('Interesting external opportunity for talents. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => 'Partner Co',
            'external_apply_url' => 'https://partner.example/careers/apply',
        ]);

        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'experience_years' => 4,
            'work_modes' => ['remote'],
            'country' => 'ma',
        ]);

        $this->actingAs($talent)
            ->get(route('talent.jobs.show', $job))
            ->assertOk()
            ->assertSee('Partner Co', false)
            ->assertSee(__('talenma.jobs.apply_external_cta'), false)
            ->assertDontSee(route('talent.jobs.apply', $job), false);

        $this->actingAs($talent)
            ->from(route('talent.jobs.show', $job))
            ->post(route('talent.jobs.apply', $job), ['cover_message' => 'Hello'])
            ->assertRedirect()
            ->assertSessionHas('toast_error');

        $this->assertSame(0, JobApplication::query()->count());
    }

    #[Test]
    public function cannot_publish_external_job_without_url(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $sector = $this->makeSector();

        $job = JobPosting::query()->create([
            'company_profile_id' => null,
            'created_by' => $admin->id,
            'profession_sector_id' => $sector->id,
            'title' => 'Incomplete external',
            'description' => str_repeat('Interesting external opportunity for talents. ', 3),
            'status' => JobPosting::STATUS_DRAFT,
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => 'Partner Co',
            'external_apply_url' => null,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.jobs.show', $job))
            ->post(route('admin.jobs.publish', $job))
            ->assertRedirect(route('admin.jobs.show', $job))
            ->assertSessionHas('toast_error');

        $this->assertSame(JobPosting::STATUS_DRAFT, $job->fresh()->status);
    }

    private function makeSector(): ProfessionSector
    {
        return ProfessionSector::query()->create([
            'slug' => 'it',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function externalPayload(string $sectorSlug): array
    {
        return [
            'title' => 'External Backend Role',
            'description' => str_repeat('We are hiring experienced backend engineers now. ', 3),
            'sector' => $sectorSlug,
            'external_company_name' => 'Acme Partners',
            'external_apply_url' => 'https://acme.example/jobs/apply',
        ];
    }
}
