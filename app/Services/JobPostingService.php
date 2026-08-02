<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Models\User;

class JobPostingService
{
    public function companyHasUnseenChanges(User $company): bool
    {
        if (! $company->isCompany()) {
            return false;
        }

        $org = $company->companyOrganization();

        if (! $org) {
            return false;
        }

        return JobPosting::query()
            ->where('company_profile_id', $org->id)
            ->where(function ($inner) {
                $inner->whereNull('company_seen_at')
                    ->orWhereColumn('company_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function staffHasUnseenChanges(User $staff): bool
    {
        if (! $staff->isStaff()) {
            return false;
        }

        return JobPosting::query()
            ->where(function ($inner) {
                $inner->whereNull('staff_seen_at')
                    ->orWhereColumn('staff_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function talentHasUnseenChanges(User $talent): bool
    {
        if (! $talent->isTalent()) {
            return false;
        }

        return JobApplication::query()
            ->where('talent_user_id', $talent->id)
            ->where(function ($inner) {
                $inner->whereNull('talent_seen_at')
                    ->orWhereColumn('talent_seen_at', '<', 'updated_at');
            })
            ->exists();
    }

    public function markSeenForCompany(User $company, JobPosting $job): void
    {
        abort_unless($company->canManageJobs(), 403);

        $org = $company->companyOrganization();
        abort_unless($org && $job->company_profile_id === $org->id, 403);

        JobPosting::withoutTimestamps(function () use ($job) {
            JobPosting::query()
                ->whereKey($job->id)
                ->update(['company_seen_at' => now()]);
        });

        $job->company_seen_at = now();
    }

    public function markSeenForStaff(User $staff, JobPosting $job): void
    {
        abort_unless($staff->isStaff(), 403);

        JobPosting::withoutTimestamps(function () use ($job) {
            JobPosting::query()
                ->whereKey($job->id)
                ->update(['staff_seen_at' => now()]);
        });

        $job->staff_seen_at = now();
    }

    public function markApplicationsSeenForTalent(User $talent, JobPosting $job): void
    {
        abort_unless($talent->isTalent(), 403);

        JobApplication::withoutTimestamps(function () use ($talent, $job) {
            JobApplication::query()
                ->where('talent_user_id', $talent->id)
                ->where('job_posting_id', $job->id)
                ->update(['talent_seen_at' => now()]);
        });
    }

    public function acknowledgeForCompany(JobPosting $job): void
    {
        JobPosting::withoutTimestamps(function () use ($job) {
            JobPosting::query()
                ->whereKey($job->id)
                ->update(['company_seen_at' => now()]);
        });

        $job->company_seen_at = now();
    }

    public function acknowledgeForStaff(JobPosting $job): void
    {
        JobPosting::withoutTimestamps(function () use ($job) {
            JobPosting::query()
                ->whereKey($job->id)
                ->update(['staff_seen_at' => now()]);
        });

        $job->staff_seen_at = now();
    }

    public function flagUnseenForParties(JobPosting $job): void
    {
        JobPosting::query()
            ->whereKey($job->id)
            ->update([
                'company_seen_at' => null,
                'staff_seen_at' => null,
                'updated_at' => now(),
            ]);

        $job->company_seen_at = null;
        $job->staff_seen_at = null;
        $job->updated_at = now();
    }

    public function flagUnseenForCompanyFromStaff(JobPosting $job): void
    {
        JobPosting::query()
            ->whereKey($job->id)
            ->update([
                'company_seen_at' => null,
                'staff_seen_at' => now(),
                'updated_at' => now(),
            ]);

        $job->company_seen_at = null;
        $job->staff_seen_at = now();
        $job->updated_at = now();
    }

    public function flagUnseenForTalentApplication(JobApplication $application): void
    {
        JobApplication::query()
            ->whereKey($application->id)
            ->update([
                'talent_seen_at' => null,
                'updated_at' => now(),
            ]);

        $application->talent_seen_at = null;
        $application->updated_at = now();
    }

    public function flagApplicantsUnseen(JobPosting $job): void
    {
        JobApplication::query()
            ->where('job_posting_id', $job->id)
            ->update([
                'talent_seen_at' => null,
                'updated_at' => now(),
            ]);
    }

    public function recordDeletedForApplicants(JobPosting $job, ?User $actor = null): void
    {
        $job->loadMissing('applications');

        foreach ($job->applications as $application) {
            JobPostingActivityEvent::record(
                $job,
                JobPostingActivityEvent::EVENT_DELETED,
                $actor,
                $job->status,
                null,
                false,
                (int) $application->talent_user_id,
            );
        }
    }
}
