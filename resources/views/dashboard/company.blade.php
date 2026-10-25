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
    <x-process-help topic="dashboard" />

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
            'lg:grid-cols-[minmax(0,7fr)_minmax(0,3fr)]' => $showPlanUsage,
        ])>
            <section class="space-y-4 min-w-0">
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

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    @if (count($recentActivity) === 0)
                        <div class="rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.company.activity.recent_empty') }}</p>
                        </div>
                    @else
                        <ol class="activity-scroll relative max-h-[calc(5*4.35rem+4*0.625rem)] space-y-2.5 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
                            @foreach ($recentActivity as $item)
                                @php
                                    $label = match ($item['type']) {
                                        'direct_hire_proposed' => __('talenma.dashboard.company.activity.activity_direct_hire_proposed', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_accepted' => __('talenma.dashboard.company.activity.activity_direct_hire_accepted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_declined' => __('talenma.dashboard.company.activity.activity_direct_hire_declined', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_deferred' => __('talenma.dashboard.company.activity.activity_direct_hire_deferred', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_deferral_accepted' => __('talenma.dashboard.company.activity.activity_direct_hire_deferral_accepted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_message' => __('talenma.dashboard.company.activity.activity_direct_hire_message', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_message_sent' => __('talenma.dashboard.company.activity.activity_direct_hire_message_sent', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_round_added' => __('talenma.dashboard.company.activity.activity_direct_hire_round_added', ['round' => $item['detail'] ?? '', 'subject' => $item['subject'] ?? '', 'actor' => $item['actor']]),
                                        'direct_hire_round_updated' => __('talenma.dashboard.company.activity.activity_direct_hire_round_updated', ['round' => $item['detail'] ?? '', 'subject' => $item['subject'] ?? '', 'actor' => $item['actor']]),
                                        'direct_hire_round_result' => __('talenma.dashboard.company.activity.activity_direct_hire_round_result', ['round' => $item['detail'] ?? '', 'result' => $item['result'] ?? '', 'subject' => $item['subject'] ?? '', 'actor' => $item['actor']]),
                                        'direct_hire_hired' => __('talenma.dashboard.company.activity.activity_direct_hire_hired', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_closed_negative' => __('talenma.dashboard.company.activity.activity_direct_hire_closed_negative', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_withdrawn' => __('talenma.dashboard.company.activity.activity_direct_hire_withdrawn', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'job_application' => __('talenma.dashboard.company.activity.activity_job_application', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'job_created' => __('talenma.dashboard.company.activity.activity_job_created', ['subject' => $item['subject'] ?? '']),
                                        'job_published' => __('talenma.dashboard.company.activity.activity_job_published', ['subject' => $item['subject'] ?? '']),
                                        'job_closed' => __('talenma.dashboard.company.activity.activity_job_closed', ['subject' => $item['subject'] ?? '']),
                                        'job_hidden' => __('talenma.dashboard.company.activity.activity_job_hidden', ['subject' => $item['subject'] ?? '']),
                                        'job_postponed' => __('talenma.dashboard.company.activity.activity_job_postponed', ['subject' => $item['subject'] ?? '']),
                                        'job_deleted' => __('talenma.dashboard.company.activity.activity_job_deleted', ['subject' => $item['subject'] ?? '']),
                                        'job_application_status' => __('talenma.dashboard.company.activity.activity_job_application_status', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '', 'result' => $item['result'] ?? '']),
                                        'recruitment_submitted' => __('talenma.dashboard.company.activity.activity_recruitment_submitted_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'recruitment_message' => __('talenma.dashboard.company.activity.activity_recruitment_message_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'recruitment_message_sent' => __('talenma.dashboard.company.activity.activity_recruitment_message_sent_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'inbox_message' => __('talenma.dashboard.company.activity.activity_inbox_message', ['actor' => $item['actor']]),
                                        'inbox_message_sent' => __('talenma.dashboard.company.activity.activity_inbox_message_sent', ['actor' => $item['actor']]),
                                        'talent_unlocked' => __('talenma.dashboard.company.activity.activity_talent_unlocked', ['actor' => $item['actor']]),
                                        'recruitment_comment' => __('talenma.dashboard.company.activity.activity_recruitment_comment_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'recruitment_status' => match ($item['result'] ?? '') {
                                            'in_progress' => __('talenma.dashboard.company.activity.activity_recruitment_taken_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                            'completed_successful', 'completed' => __('talenma.dashboard.company.activity.activity_recruitment_closed_successful_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                            'completed_unsuccessful', 'cancelled' => __('talenma.dashboard.company.activity.activity_recruitment_closed_unsuccessful_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                            'pending' => __('talenma.dashboard.company.activity.activity_recruitment_reopened_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                            default => __('talenma.dashboard.company.activity.activity_recruitment_status', ['subject' => $item['subject'] ?? '', 'result' => $item['result'] ?? '']),
                                        },
                                        default => $item['actor'],
                                    };

                                    $hideCategoryBadge = in_array(($item['type'] ?? ''), [
                                        'recruitment_submitted',
                                        'recruitment_status',
                                        'recruitment_message',
                                        'recruitment_message_sent',
                                        'recruitment_comment',
                                        'inbox_message',
                                        'inbox_message_sent',
                                    ], true);

                                    [$category, $dotClass, $rowClass] = match (true) {
                                        str_starts_with((string) ($item['type'] ?? ''), 'job_') => [
                                            __('talenma.dashboard.company.activity.activity_cat_jobs'),
                                            'bg-emerald-500',
                                            'hover:bg-emerald-50/80',
                                        ],
                                        str_starts_with((string) ($item['type'] ?? ''), 'inbox_') => [
                                            __('talenma.dashboard.company.activity.activity_cat_inbox'),
                                            'bg-blue-500',
                                            'hover:bg-blue-50/80',
                                        ],
                                        ($item['type'] ?? '') === 'talent_unlocked' => [
                                            __('talenma.dashboard.company.activity.activity_cat_lock'),
                                            'bg-amber-500',
                                            'hover:bg-amber-50/80',
                                        ],
                                        str_starts_with((string) ($item['type'] ?? ''), 'recruitment_') => [
                                            __('talenma.dashboard.company.activity.activity_cat_sourcing'),
                                            'bg-sky-500',
                                            'hover:bg-sky-50/80',
                                        ],
                                        str_starts_with((string) ($item['type'] ?? ''), 'direct_hire') => [
                                            __('talenma.dashboard.company.activity.activity_cat_hire'),
                                            'bg-indigo-500',
                                            'hover:bg-indigo-50/80',
                                        ],
                                        default => [
                                            __('talenma.dashboard.company.activity.activity_cat_hire'),
                                            'bg-sky-500',
                                            'hover:bg-sky-50/80',
                                        ],
                                    };
                                @endphp
                                <li class="relative pl-10">
                                    <span class="absolute left-3 top-4 z-10 flex h-3.5 w-3.5 items-center justify-center">
                                        <span class="absolute inline-flex h-full w-full rounded-full {{ $dotClass }} opacity-25"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $dotClass }} ring-2 ring-white"></span>
                                    </span>

                                    @if (! empty($item['href']))
                                        <a href="{{ $item['href'] }}" class="group flex flex-col gap-1.5 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 {{ $rowClass }} hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                            <div class="min-w-0">
                                                @unless ($hideCategoryBadge)
                                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ $category }}</span>
                                                @endunless
                                                <p @class([
                                                    'text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800',
                                                    'mt-1.5' => ! $hideCategoryBadge,
                                                ])>{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </a>
                                    @else
                                        <div class="flex flex-col gap-1.5 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                            <div class="min-w-0">
                                                @unless ($hideCategoryBadge)
                                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ $category }}</span>
                                                @endunless
                                                <p @class([
                                                    'text-sm font-medium leading-snug text-slate-900',
                                                    'mt-1.5' => ! $hideCategoryBadge,
                                                ])>{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            @if ($showPlanUsage)
                <aside class="min-w-0">
                    @include('dashboard.partials.company-plan-usage', ['orgPlan' => $orgPlan])
                </aside>
            @endif
        </div>
    </div>
</x-app-layout>
