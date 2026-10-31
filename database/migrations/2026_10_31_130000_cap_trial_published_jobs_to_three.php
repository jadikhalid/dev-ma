<?php

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Trial concurrent live jobs drop from 5 to 3. Hide the oldest excess
     * published postings so active trial orgs respect the new cap.
     */
    public function up(): void
    {
        $limit = CompanyProfile::TRIAL_ACTIVE_JOB_LIMIT;

        $trialProfileIds = DB::table('company_profiles')
            ->where(function ($query): void {
                $query->where('plan', CompanyProfile::PLAN_TRIAL)
                    ->orWhere(function ($trialWindow): void {
                        $trialWindow
                            ->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '>', now())
                            ->where(function ($paid): void {
                                $paid->where('is_subscribed', false)
                                    ->orWhereNull('is_subscribed');
                            });
                    });
            })
            ->pluck('id');

        foreach ($trialProfileIds as $profileId) {
            $excessIds = DB::table('job_postings')
                ->where('company_profile_id', $profileId)
                ->where('status', JobPosting::STATUS_PUBLISHED)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->skip($limit)
                ->take(1000)
                ->pluck('id');

            if ($excessIds->isEmpty()) {
                continue;
            }

            DB::table('job_postings')
                ->whereIn('id', $excessIds)
                ->update([
                    'status' => JobPosting::STATUS_HIDDEN,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible data correction: excess live jobs were hidden intentionally.
    }
};
