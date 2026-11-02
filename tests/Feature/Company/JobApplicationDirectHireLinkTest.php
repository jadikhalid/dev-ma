<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\DirectHireRequest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\DirectHireService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationDirectHireLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_shortlist_application_by_launching_direct_hire(): void
    {
        [$owner, $profile] = $this->makeCompany();
        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $job = $this->makePublishedJob($owner, $profile, 'Backend engineer');

        $application = JobApplication::query()->create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $talent->id,
            'cover_message' => 'Motivé pour ce poste.',
            'status' => JobApplication::STATUS_RECEIVED,
            'submitted_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post(route('company.jobs.applications.direct-hire', [$job, $application]))
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(JobApplication::STATUS_IN_PROCESS, $application->status);
        $this->assertNotNull($application->direct_hire_request_id);

        $hire = DirectHireRequest::query()->findOrFail($application->direct_hire_request_id);
        $this->assertSame($application->id, $hire->job_application_id);
        $this->assertSame($talent->id, $hire->talent_user_id);
        $this->assertSame(DirectHireRequest::STATUS_PENDING_RESPONSE, $hire->status);
    }

    public function test_direct_hire_negative_outcome_rejects_linked_application(): void
    {
        [$owner, $profile] = $this->makeCompany();
        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
        $job = $this->makePublishedJob($owner, $profile, 'Frontend engineer');

        $application = JobApplication::query()->create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $talent->id,
            'status' => JobApplication::STATUS_RECEIVED,
            'submitted_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post(route('company.jobs.applications.direct-hire', [$job, $application]))
            ->assertRedirect();

        $hire = DirectHireRequest::query()->findOrFail($application->fresh()->direct_hire_request_id);

        app(DirectHireService::class)->decide($hire, $talent, DirectHireRequest::DECISION_ACCEPT);
        app(DirectHireService::class)->close(
            $hire->fresh(),
            $owner,
            DirectHireRequest::STATUS_CLOSED_NEGATIVE,
        );

        $this->assertSame(JobApplication::STATUS_REJECTED, $application->fresh()->status);
    }

    public function test_direct_hire_hire_marks_application_retained(): void
    {
        [$owner, $profile] = $this->makeCompany();
        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
        $job = $this->makePublishedJob($owner, $profile, 'Product engineer');

        $application = JobApplication::query()->create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $talent->id,
            'status' => JobApplication::STATUS_RECEIVED,
            'submitted_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post(route('company.jobs.applications.direct-hire', [$job, $application]))
            ->assertRedirect();

        $hire = DirectHireRequest::query()->findOrFail($application->fresh()->direct_hire_request_id);

        app(DirectHireService::class)->decide($hire, $talent, DirectHireRequest::DECISION_ACCEPT);
        app(DirectHireService::class)->close(
            $hire->fresh(),
            $owner,
            DirectHireRequest::STATUS_HIRED,
        );

        $this->assertSame(JobApplication::STATUS_RETAINED, $application->fresh()->status);
    }

    /**
     * @return array{0: User, 1: CompanyProfile}
     */
    private function makeCompany(): array
    {
        $owner = User::factory()->companyOwner()->create();
        $profile = CompanyProfile::factory()->create([
            'user_id' => $owner->id,
        ]);

        return [$owner->fresh(), $profile];
    }

    private function makePublishedJob(User $owner, CompanyProfile $profile, string $title): JobPosting
    {
        return JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => $title,
            'description' => str_repeat('Looking for experienced talent. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'remote_ok' => true,
            'work_modes' => ['remote'],
        ]);
    }
}
