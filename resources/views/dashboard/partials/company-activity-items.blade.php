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
