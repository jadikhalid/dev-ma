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

        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                <x-company-contact-photo :profile="$profile" size="md" class="mx-auto sm:mx-0 shrink-0" />
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <p class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                        {{ __('talenma.dashboard.welcome', ['name' => $welcomeName]) }}
                    </p>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $welcomeRole }}</p>
                    @if (! $completion['is_catalog_ready'])
                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ __('talenma.dashboard.company.profile_incomplete') }}
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
                    @if ($isOwner)
                        <a href="{{ $profileEditUrl }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition whitespace-nowrap">
                            {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.company.edit_profile') : __('talenma.dashboard.company.complete_profile') }}
                        </a>
                    @else
                        <a href="{{ route('company.jobs.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition whitespace-nowrap">
                            {{ __('talenma.dashboard.company.jobs_manage') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activités (70%) + Services (30%) --}}
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,7fr)_minmax(0,3fr)] gap-4 lg:gap-5 items-start">
            <section class="space-y-4 min-w-0">
                <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
                    <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.company.section_ongoing') }}</h2>
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.dashboard.company.recent_requests') }}</p>
                                <p class="text-xs text-slate-500">{{ __('talenma.dashboard.company.recent_requests_subtitle') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('sourcing.index') }}" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.company.sourcing_all') }}
                        </a>
                    </div>

                    @if ($recentRequests->isEmpty())
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.company.ongoing_empty') }}</p>
                        </div>
                    @else
                        <ul class="mt-4 space-y-2.5">
                            @foreach ($recentRequests as $req)
                                @php
                                    $tone = match ($req->status) {
                                        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
                                        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'completed_successful', 'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'completed_unsuccessful', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <li>
                                    <a
                                        href="{{ route('sourcing.show', $req) }}"
                                        class="group block rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200"
                                    >
                                        <div class="flex flex-col gap-1.5 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $req->displayTitle() }}</span>
                                                    <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $tone }}">
                                                        {{ $req->statusLabel() }}
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $req->updated_at?->translatedFormat('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    {{-- Title dots: remain while ANY request still has unseen changes --}}
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="inline-flex items-center gap-2 text-sm font-bold tracking-tight text-slate-900">
                                    {{ __('talenma.dashboard.company.direct_hires') }}
                                    @if ($directHireUnseen ?? false)
                                        @foreach (range(1, 3) as $dot)
                                            <span class="relative flex h-2.5 w-2.5" @if ($loop->first) title="{{ __('talenma.direct_hire.nav_new') }}" @endif>
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                            </span>
                                        @endforeach
                                        <span class="sr-only">{{ __('talenma.direct_hire.nav_new') }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">{{ __('talenma.dashboard.company.direct_hires_subtitle') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('company.direct-hire.index') }}" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.company.direct_hires_all') }}
                        </a>
                    </div>

                    @if ($directHires->isEmpty())
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.company.direct_hires_empty') }}</p>
                        </div>
                    @else
                        <ul class="mt-4 space-y-2.5">
                            @foreach ($directHires as $hire)
                                @php
                                    $tone = match ($hire->statusTone()) {
                                        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
                                        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
                                        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    $latestRound = $hire->rounds->last();
                                    $hireUnseen = $hire->hasUnseenChangesForCompany();
                                @endphp
                                <li>
                                    <a href="{{ route('company.direct-hire.show', $hire) }}" class="group flex items-center gap-3 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-50/80 hover:shadow-md hover:ring-indigo-200">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-medium leading-snug text-slate-900 truncate group-hover:text-indigo-800">{{ $hire->shortSubject() }}</span>
                                                <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $tone }}">
                                                    {{ $hire->statusLabel() }}
                                                </span>
                                            </div>
                                            <p class="mt-1.5 text-xs font-medium text-slate-400">
                                                {{ $hire->talentDisplayName() }}
                                                · {{ $hire->created_at?->translatedFormat('d M Y') }}
                                            </p>
                                            @if ($latestRound)
                                                <p class="mt-1 text-xs text-slate-600">
                                                    {{ __('talenma.direct_hire.round_n', ['n' => $latestRound->position]) }} — {{ $latestRound->title }}
                                                    ({{ $latestRound->statusLabel() }})
                                                </p>
                                            @endif
                                        </div>
                                        @if ($hireUnseen)
                                            <span class="relative flex h-2.5 w-2.5 shrink-0 self-center" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                            </span>
                                            <span class="sr-only">{{ __('talenma.direct_hire.nav_new') }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.dashboard.company.activity.recent_title') }}</p>
                        </div>
                    </div>

                    @if (count($recentActivity) === 0)
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.company.activity.recent_empty') }}</p>
                        </div>
                    @else
                        <ol class="activity-scroll relative mt-4 max-h-[calc(5*4.35rem+4*0.625rem)] space-y-2.5 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
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
                                        'recruitment_submitted' => __('talenma.dashboard.company.activity.activity_recruitment_submitted_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'recruitment_message' => __('talenma.dashboard.company.activity.activity_recruitment_message_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'recruitment_message_sent' => __('talenma.dashboard.company.activity.activity_recruitment_message_sent_'.(($item['detail'] ?? 'open') === 'named' ? 'named' : 'open'), ['subject' => $item['subject'] ?? '']),
                                        'inbox_message' => __('talenma.dashboard.company.activity.activity_inbox_message', ['actor' => $item['actor']]),
                                        'inbox_message_sent' => __('talenma.dashboard.company.activity.activity_inbox_message_sent', ['actor' => $item['actor']]),
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
                                        ($item['type'] ?? '') === 'job_application' => [
                                            __('talenma.dashboard.company.activity.activity_cat_jobs'),
                                            'bg-emerald-500',
                                            'hover:bg-emerald-50/80',
                                        ],
                                        str_starts_with((string) ($item['type'] ?? ''), 'inbox_') => [
                                            __('talenma.dashboard.company.activity.activity_cat_inbox'),
                                            'bg-blue-500',
                                            'hover:bg-blue-50/80',
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

            <aside class="space-y-3">
                <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
                    <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.company.section_services') }}</h2>
                </div>

                <div class="bg-white rounded-2xl border p-4 flex flex-col">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('talenma.dashboard.company.recruit_title') }}</h3>
                    <p class="mt-1.5 text-xs text-gray-600 leading-relaxed flex-1">{{ __('talenma.dashboard.company.recruit_desc') }}</p>
                    <div class="mt-3 flex flex-col gap-2">
                        <a
                            href="{{ route('company.search') }}"
                            class="group relative inline-flex items-center justify-center gap-2 overflow-hidden px-3 py-2.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-indigo-600/30 ring-2 ring-indigo-300 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/40 transition"
                        >
                            <span class="absolute inset-0 animate-pulse bg-white/10" aria-hidden="true"></span>
                            <span class="relative">{{ __('talenma.dashboard.company.browse') }}</span>
                            <svg class="relative h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border p-4 flex flex-col">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('talenma.dashboard.company.jobs_title') }}</h3>
                    <p class="mt-1.5 text-xs text-gray-600 leading-relaxed flex-1">{{ __('talenma.dashboard.company.jobs_desc') }}</p>
                    <div class="mt-3 flex flex-col gap-2">
                        <a href="{{ route('company.jobs.index') }}" class="px-3 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 text-center">{{ __('talenma.dashboard.company.jobs_manage') }}</a>
                        <a href="{{ route('company.jobs.create') }}" class="px-3 py-2 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-lg hover:bg-emerald-50 text-center">{{ __('talenma.dashboard.company.jobs_create') }}</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
