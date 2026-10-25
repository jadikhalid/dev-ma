@php
    $user = Auth::user();
    $isOwner = $user->isCompanyOwner();
    $profileEditUrl = route('profile.edit', ['panel' => 'account']);
    $welcomeNameRaw = $isOwner
        ? (filled($profile?->representative_name) ? $profile->representative_name : $user->name)
        : (trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->name);
    $welcomeName = mb_convert_case(
        trim(preg_replace('/\s+/u', ' ', (string) $welcomeNameRaw) ?: ''),
        MB_CASE_TITLE,
        'UTF-8',
    );
    $welcomeRole = $isOwner
        ? __('talenma.dashboard.company.welcome_role_owner')
        : __('talenma.dashboard.company.welcome_role_member');
@endphp

<x-app-layout>
    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-5">
        @if (session('recruitment_sent'))
            <div class="p-3 sm:p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ __('talenma.dashboard.company.request_sent') }}</div>
        @endif

        @php
            $orgPlan = $profile;
            $planExpired = $user->companyPlanExpired();
            $onTrial = $orgPlan?->isOnTrial() ?? false;
            $trialDays = $orgPlan?->trialDaysRemaining();
        @endphp

        @if ($planExpired)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 sm:px-5 sm:py-4">
                <p class="text-sm font-semibold text-amber-950">{{ __('talenma.company_offer.banner_expired_title') }}</p>
                <p class="mt-1 text-sm text-amber-900/80">{{ __('talenma.company_offer.banner_expired_body') }}</p>
                <a href="{{ route('company.demo') }}" class="mt-3 inline-flex text-sm font-semibold text-amber-950 underline decoration-amber-400 underline-offset-2 hover:text-amber-800">
                    {{ __('talenma.company_offer.banner_expired_cta') }} →
                </a>
            </div>
        @endif

        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                @if ($isOwner)
                    <x-company-contact-photo :profile="$profile" size="md" class="mx-auto sm:mx-0 shrink-0" />
                @else
                    <x-user-avatar :user="$user" size="md" class="mx-auto sm:mx-0 shrink-0 ring-2 ring-emerald-100" />
                @endif
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <p class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                        {{ __('talenma.dashboard.welcome', ['name' => $welcomeName]) }}
                    </p>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $welcomeRole }}</p>
                    @if (! $completion['is_catalog_ready'])
                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $isOwner
                                ? __('talenma.dashboard.company.profile_incomplete')
                                : __('talenma.dashboard.company.profile_incomplete_member') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-3 sm:gap-4 shrink-0">
                    <div class="relative w-16 h-16 sm:w-20 sm:h-20">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="10" />
                            <circle
                                cx="50" cy="50" r="42" fill="none"
                                stroke="{{ $completion['percent'] >= 100 ? '#10b981' : '#059669' }}"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $completion['percent'] / 100) }}"
                            />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-sm sm:text-base font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[9px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.company.progress_label') }}</span>
                        </div>
                    </div>
                    <a href="{{ $profileEditUrl }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition whitespace-nowrap">
                        {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100)
                            ? __('talenma.dashboard.company.edit_profile')
                            : __('talenma.dashboard.company.complete_profile') }}
                    </a>
                </div>
            </div>
        </div>

        @php
            $showPlanUsage = $orgPlan
                && ! $planExpired
                && ($onTrial || $orgPlan->hasPaidAccess());
        @endphp

        {{-- Activités (+ usage offre à droite si applicable) --}}
        <div @class([
            'grid grid-cols-1 gap-4 lg:gap-5 items-start',
            'lg:grid-cols-[minmax(0,25fr)_minmax(0,50fr)_minmax(0,25fr)]' => $showPlanUsage,
            'lg:grid-cols-[minmax(0,25fr)_minmax(0,75fr)]' => ! $showPlanUsage,
        ])>
            @include('dashboard.partials.company-org-column', [
                'profile' => $profile,
                'canEditOrg' => $user->canManageCompanyProfile(),
            ])

            <section class="order-1 min-w-0 space-y-4 lg:order-none">
                <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <h2 class="text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.dashboard.company.activity.recent_title') }}</h2>
                    </div>
                </div>

                <div
                    @class([
                        'rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5',
                    ])
                    @if (count($recentActivity) > 0)
                        x-data="companyActivityFeed"
                        data-company-activity-feed
                        data-activity-url="{{ route('dashboard.company-activity') }}"
                        data-activity-offset="{{ $activityNextOffset }}"
                        data-activity-has-more="{{ $activityHasMore ? '1' : '0' }}"
                    @endif
                >
                    @if (count($recentActivity) === 0)
                        <div class="rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.company.activity.recent_empty') }}</p>
                        </div>
                    @else
                        <ol x-ref="list" :aria-busy="loading" class="relative space-y-2.5 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
                            @include('dashboard.partials.company-activity-items', ['recentActivity' => $recentActivity])
                            @if ($activityHasMore)
                                <li x-ref="sentinel" class="h-px" aria-hidden="true"></li>
                            @endif
                        </ol>
                    @endif
                </div>
            </section>

            @if ($showPlanUsage)
                <aside class="order-3 min-w-0 lg:order-none">
                    @include('dashboard.partials.company-plan-usage', ['orgPlan' => $orgPlan])
                </aside>
            @endif
        </div>
    </div>
</x-app-layout>
