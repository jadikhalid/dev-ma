<?php

use App\Models\CompanyProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Active trials were opened with a 3-month window. Rebase them to 45 days
     * from the original start (inferred as trial_ends_at − 3 months).
     */
    public function up(): void
    {
        $legacyMonths = 3;
        $trialDays = CompanyProfile::TRIAL_DAYS;

        DB::table('company_profiles')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->orderBy('id')
            ->chunkById(100, function ($profiles) use ($legacyMonths, $trialDays): void {
                foreach ($profiles as $profile) {
                    $endsAt = \Illuminate\Support\Carbon::parse($profile->trial_ends_at);
                    $startedAt = $endsAt->copy()->subMonths($legacyMonths);
                    $rebasedEndsAt = $startedAt->copy()->addDays($trialDays);

                    DB::table('company_profiles')
                        ->where('id', $profile->id)
                        ->update([
                            'trial_ends_at' => $rebasedEndsAt,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        $legacyMonths = 3;
        $trialDays = CompanyProfile::TRIAL_DAYS;

        DB::table('company_profiles')
            ->whereNotNull('trial_ends_at')
            ->where('plan', CompanyProfile::PLAN_TRIAL)
            ->orderBy('id')
            ->chunkById(100, function ($profiles) use ($legacyMonths, $trialDays): void {
                foreach ($profiles as $profile) {
                    $endsAt = \Illuminate\Support\Carbon::parse($profile->trial_ends_at);
                    $startedAt = $endsAt->copy()->subDays($trialDays);
                    $restoredEndsAt = $startedAt->copy()->addMonths($legacyMonths);

                    DB::table('company_profiles')
                        ->where('id', $profile->id)
                        ->update([
                            'trial_ends_at' => $restoredEndsAt,
                            'updated_at' => now(),
                        ]);
                }
            });
    }
};
