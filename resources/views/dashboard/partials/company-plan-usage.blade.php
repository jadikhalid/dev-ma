@php
    /** @var \App\Models\CompanyProfile $orgPlan */
    $tz = config('app.timezone');
    $now = now()->timezone($tz)->startOfDay();
    $planKey = $orgPlan->commercialPlan();
    $isTrial = $orgPlan->isOnTrial();
    $badge = match ($planKey) {
        \App\Models\CompanyProfile::PLAN_TRIAL => __('talenma.dashboard.company.plan_usage.badge_trial'),
        \App\Models\CompanyProfile::PLAN_ESSENTIAL => __('talenma.dashboard.company.plan_usage.badge_essential'),
        \App\Models\CompanyProfile::PLAN_CUSTOM => __('talenma.dashboard.company.plan_usage.badge_custom'),
        default => __('talenma.dashboard.company.plan_usage.badge_pro'),
    };

    $periodStart = null;
    $periodEnd = null;
    $daysLeft = null;
    $progress = null;
    $hasFinitePeriod = false;

    if ($isTrial && $orgPlan->trial_ends_at) {
        $periodEnd = $orgPlan->trial_ends_at->timezone($tz)->startOfDay();
        $periodStart = $periodEnd->copy()->subDays(\App\Models\CompanyProfile::TRIAL_DAYS);
        $hasFinitePeriod = true;
        $daysLeft = $orgPlan->trialDaysRemaining();
    } elseif ($orgPlan->subscription_expires_at) {
        $periodEnd = $orgPlan->subscription_expires_at->timezone($tz)->startOfDay();
        $periodStart = $periodEnd->copy()->subYear();
        $hasFinitePeriod = true;
        $daysLeft = (int) max(0, $now->diffInDays($periodEnd, false));
    }

    if ($hasFinitePeriod && $periodStart && $periodEnd) {
        $totalDays = max(1, $periodStart->diffInDays($periodEnd));
        $elapsed = min($totalDays, max(0, $periodStart->diffInDays($now)));
        $progress = (int) round(($elapsed / $totalDays) * 100);
    }

    $jobsUsed = $orgPlan->publishedJobsCount();
    $jobsLimit = $orgPlan->activeJobLimit();
    $sourcingIncluded = $orgPlan->canUseSourcing();
    $creditsLimit = $orgPlan->sourcingCreditsPerYear();
    $creditsUsed = $sourcingIncluded ? $orgPlan->sourcingCreditsUsedThisYear() : 0;
    $creditsLeft = max(0, $creditsLimit - $creditsUsed);
    $enrichedBrand = $orgPlan->canUseEnrichedBrand();
    $prioritySupport = in_array($planKey, [
        \App\Models\CompanyProfile::PLAN_PRO,
        \App\Models\CompanyProfile::PLAN_CUSTOM,
    ], true);
@endphp

<div class="space-y-4" data-company-plan-usage data-plan="{{ $planKey }}">
    <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </span>
            <h2 class="text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.dashboard.company.plan_usage.section_title') }}</h2>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5 shadow-sm shadow-indigo-500/5">
        <dl class="space-y-2.5">
            <div class="flex items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.account_type_label') }}</dt>
                <dd class="text-sm font-bold text-slate-950">{{ $badge }}</dd>
            </div>

            <div class="rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <p class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.period_label') }}</p>
                @if ($hasFinitePeriod && $periodStart && $periodEnd)
                    <p class="mt-0.5 text-[11px] font-normal leading-snug text-slate-400">
                        {{ __('talenma.dashboard.company.plan_usage.period', [
                            'start' => $periodStart->format('d/m/Y'),
                            'end' => $periodEnd->format('d/m/Y'),
                        ]) }}
                    </p>
                @else
                    <p class="mt-0.5 text-[11px] font-normal leading-snug text-slate-400">{{ __('talenma.dashboard.company.plan_usage.no_end_date') }}</p>
                @endif
            </div>
        </dl>

        @if ($progress !== null && $periodEnd)
            <div class="mt-2.5 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <div class="flex items-center justify-between gap-2 text-xs">
                    <span class="font-medium text-slate-600">
                        {{ $isTrial
                            ? __('talenma.dashboard.company.plan_usage.progress_trial')
                            : __('talenma.dashboard.company.plan_usage.progress_subscription') }}
                    </span>
                    <span class="font-semibold text-slate-900">{{ $progress }}%</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all" style="width: {{ $progress }}%"></div>
                </div>
                @if ($daysLeft !== null)
                    <p class="mt-1.5 text-[11px] text-slate-500">
                        {{ $daysLeft === 1
                            ? __('talenma.dashboard.company.plan_usage.days_left_one')
                            : __('talenma.dashboard.company.plan_usage.days_left', ['days' => $daysLeft]) }}
                    </p>
                @endif
            </div>
        @endif

        <dl class="mt-2.5 space-y-2.5">
            <div class="flex items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.jobs_label') }}</dt>
                <dd class="text-sm font-bold text-slate-950">{{ __('talenma.dashboard.company.plan_usage.jobs_value', ['used' => $jobsUsed, 'limit' => $jobsLimit]) }}</dd>
            </div>

            <div class="flex items-start justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.credits_label') }}</dt>
                <dd class="text-right">
                    @if ($sourcingIncluded)
                        <p class="text-sm font-bold text-slate-950">{{ __('talenma.dashboard.company.plan_usage.credits_value', ['left' => $creditsLeft, 'limit' => $creditsLimit]) }}</p>
                    @else
                        <p class="text-sm font-bold text-slate-950">{{ __('talenma.dashboard.company.plan_usage.credits_not_included') }}</p>
                    @endif
                </dd>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.direct_hire_label') }}</dt>
                <dd class="text-xs font-semibold text-emerald-700">{{ __('talenma.dashboard.company.plan_usage.included') }}</dd>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.page_label') }}</dt>
                <dd class="text-xs font-semibold text-slate-800">
                    {{ $enrichedBrand
                        ? __('talenma.dashboard.company.plan_usage.page_enriched')
                        : __('talenma.dashboard.company.plan_usage.page_standard') }}
                </dd>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-2.5 ring-1 ring-slate-100">
                <dt class="text-xs font-medium text-slate-600">{{ __('talenma.dashboard.company.plan_usage.support_label') }}</dt>
                <dd class="text-xs font-semibold text-slate-800">
                    {{ $prioritySupport
                        ? __('talenma.dashboard.company.plan_usage.support_prio')
                        : __('talenma.dashboard.company.plan_usage.support_std') }}
                </dd>
            </div>
        </dl>
    </div>
</div>
