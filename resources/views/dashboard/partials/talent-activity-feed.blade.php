<div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
    @if (($jobsUnseen ?? false))
        <div class="mb-3 flex items-center gap-2">
            @foreach (range(1, 3) as $dot)
                <span class="relative flex h-2.5 w-2.5" @if ($loop->first) title="{{ __('talenma.jobs.nav_new') }}" @endif>
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                </span>
            @endforeach
            <span class="text-xs font-semibold text-blue-700">{{ __('talenma.jobs.nav_new') }}</span>
        </div>
    @endif

    @if (count($stats['recent_activity']) === 0)
        <div class="rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-8 text-center">
            <span class="mx-auto mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">{{ __('talenma.dashboard.talent.stats.recent_empty') }}</p>
        </div>
    @else
        <ol class="activity-scroll relative max-h-[calc((5*4.35rem+4*0.625rem)*1.5)] space-y-2.5 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
            @foreach ($stats['recent_activity'] as $item)
                @php
                    $label = match ($item['type']) {
                        'cv_download' => filled($item['detail'] ?? null)
                            ? __('talenma.dashboard.talent.stats.activity_cv_lang', ['actor' => $item['actor'], 'lang' => $item['detail']])
                            : __('talenma.dashboard.talent.stats.activity_cv', ['actor' => $item['actor']]),
                        'direct_hire_proposed' => __('talenma.dashboard.talent.stats.activity_direct_hire_proposed', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_accepted' => __('talenma.dashboard.talent.stats.activity_direct_hire_accepted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_declined' => __('talenma.dashboard.talent.stats.activity_direct_hire_declined', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_deferred' => __('talenma.dashboard.talent.stats.activity_direct_hire_deferred', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_deferral_accepted' => __('talenma.dashboard.talent.stats.activity_direct_hire_deferral_accepted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_hired' => __('talenma.dashboard.talent.stats.activity_direct_hire_hired', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_closed_negative' => __('talenma.dashboard.talent.stats.activity_direct_hire_closed_negative', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_withdrawn' => __('talenma.dashboard.talent.stats.activity_direct_hire_withdrawn', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_round_added' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_added', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '', 'subject' => $item['subject'] ?? '']),
                        'direct_hire_round_result' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_result', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '', 'result' => $item['result'] ?? '']),
                        'direct_hire_round_cancelled' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_cancelled', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '']),
                        'direct_hire_message' => __('talenma.dashboard.talent.stats.activity_direct_hire_message', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'direct_hire_message_sent' => __('talenma.dashboard.talent.stats.activity_direct_hire_message_sent', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'inbox_message' => __('talenma.dashboard.talent.stats.activity_inbox_message', ['actor' => $item['actor']]),
                        'inbox_message_sent' => __('talenma.dashboard.talent.stats.activity_inbox_message_sent', ['actor' => $item['actor']]),
                        'talent_unlocked' => __('talenma.dashboard.talent.stats.activity_talent_unlocked', ['actor' => $item['actor']]),
                        'job_application_submitted' => __('talenma.dashboard.talent.stats.activity_job_application_submitted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'job_application_status' => __('talenma.dashboard.talent.stats.activity_job_application_status', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '', 'result' => $item['result'] ?? '']),
                        'job_closed' => __('talenma.dashboard.talent.stats.activity_job_closed', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'job_hidden' => __('talenma.dashboard.talent.stats.activity_job_hidden', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'job_postponed' => __('talenma.dashboard.talent.stats.activity_job_postponed', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        'job_deleted' => __('talenma.dashboard.talent.stats.activity_job_deleted', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                        default => __('talenma.dashboard.talent.stats.activity_view', ['actor' => $item['actor']]),
                    };

                    [$category, $dotClass, $badgeClass, $rowClass] = match (true) {
                        ($item['type'] ?? '') === 'cv_download' => [
                            __('talenma.dashboard.talent.stats.activity_cat_cv'),
                            'bg-violet-500',
                            'bg-violet-50 text-violet-700 ring-violet-100',
                            'hover:bg-violet-50/80',
                        ],
                        str_starts_with((string) ($item['type'] ?? ''), 'inbox_') => [
                            __('talenma.dashboard.talent.stats.activity_cat_inbox'),
                            'bg-blue-500',
                            'bg-blue-50 text-blue-700 ring-blue-100',
                            'hover:bg-blue-50/80',
                        ],
                        ($item['type'] ?? '') === 'talent_unlocked' => [
                            __('talenma.dashboard.talent.stats.activity_cat_lock'),
                            'bg-amber-500',
                            'bg-amber-50 text-amber-800 ring-amber-100',
                            'hover:bg-amber-50/80',
                        ],
                        str_starts_with((string) ($item['type'] ?? ''), 'job_') => [
                            __('talenma.dashboard.talent.stats.activity_cat_jobs'),
                            'bg-emerald-500',
                            'bg-emerald-50 text-emerald-700 ring-emerald-100',
                            'hover:bg-emerald-50/80',
                        ],
                        str_starts_with((string) ($item['type'] ?? ''), 'direct_hire') => [
                            __('talenma.dashboard.talent.stats.activity_cat_hire'),
                            'bg-indigo-500',
                            'bg-indigo-50 text-indigo-700 ring-indigo-100',
                            'hover:bg-indigo-50/80',
                        ],
                        default => [
                            __('talenma.dashboard.talent.stats.activity_cat_profile'),
                            'bg-sky-500',
                            'bg-sky-50 text-sky-700 ring-sky-100',
                            'hover:bg-sky-50/80',
                        ],
                    };
                @endphp
                <li class="relative pl-10">
                    <span class="absolute left-3.5 top-4 z-10 flex h-3 w-3 items-center justify-center">
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $dotClass }} ring-2 ring-white"></span>
                    </span>

                    @if (! empty($item['href']))
                        <a href="{{ $item['href'] }}" class="group flex items-start justify-between gap-3 rounded-xl bg-white/95 px-3.5 py-3 ring-1 ring-slate-200/70 shadow-sm transition duration-150 {{ $rowClass }} hover:-translate-y-px hover:shadow-md hover:ring-indigo-200">
                            <div class="min-w-0">
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider ring-1 {{ $badgeClass }}">{{ $category }}</span>
                                <p class="mt-1 text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $label }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2 pt-0.5">
                                <time class="text-[11px] font-medium tabular-nums text-slate-400" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                @if (! empty($item['unseen']))
                                    <span class="relative flex h-2.5 w-2.5" title="{{ __('talenma.jobs.nav_new') }}">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                    </span>
                                    <span class="sr-only">{{ __('talenma.jobs.nav_new') }}</span>
                                @endif
                            </div>
                        </a>
                    @else
                        <div class="flex items-start justify-between gap-3 rounded-xl bg-white/95 px-3.5 py-3 ring-1 ring-slate-200/70 shadow-sm">
                            <div class="min-w-0">
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider ring-1 {{ $badgeClass }}">{{ $category }}</span>
                                <p class="mt-1 text-sm font-medium leading-snug text-slate-900">{{ $label }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2 pt-0.5">
                                <time class="text-[11px] font-medium tabular-nums text-slate-400" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                @if (! empty($item['unseen']))
                                    <span class="relative flex h-2.5 w-2.5" title="{{ __('talenma.jobs.nav_new') }}">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                    </span>
                                    <span class="sr-only">{{ __('talenma.jobs.nav_new') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
