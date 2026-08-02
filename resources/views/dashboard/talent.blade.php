@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                <x-user-avatar :user="$user" size="lg" class="mx-auto lg:mx-0" />
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    @php
                        $helloName = filled($user->first_name)
                            ? $user->first_name
                            : (preg_split('/\s+/u', trim((string) $user->name))[0] ?? $user->name);
                    @endphp
                    <p class="text-lg font-semibold text-gray-900">
                        {{ __('talenma.dashboard.talent.hello', ['name' => $helloName]) }}
                    </p>
                </div>

                <div class="flex items-center gap-5 shrink-0">
                    <div class="relative w-24 h-24">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="10" />
                            <circle
                                cx="50" cy="50" r="42" fill="none"
                                stroke="{{ $completion['percent'] >= 100 ? '#10b981' : '#4f46e5' }}"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $completion['percent'] / 100) }}"
                            />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-base font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.progress_label') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-5 lg:gap-6 items-start">
            {{-- Colonne gauche : activité --}}
            <section class="space-y-4 min-w-0">
                <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
                    <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.stats.title') }}</h2>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.views') }}</p>
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($stats['profile_views_7d']) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ __('talenma.dashboard.talent.stats.views_total', ['count' => number_format($stats['profile_views_total'])]) }}</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-white p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.cv_downloads') }}</p>
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($stats['cv_downloads_7d']) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                    </div>

                    <a
                        href="{{ route('inbox.index') }}"
                        @class([
                            'group rounded-2xl border p-4 transition duration-150 hover:-translate-y-0.5 hover:shadow-md',
                            'border-indigo-200 bg-indigo-50/60 hover:bg-indigo-50 hover:ring-1 hover:ring-indigo-200' => $stats['unread_messages'] > 0,
                            'border-slate-200/90 bg-white hover:border-indigo-200 hover:bg-indigo-50/40' => $stats['unread_messages'] === 0,
                        ])
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.unread_messages') }}</p>
                            <span @class([
                                'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' => $stats['unread_messages'] > 0,
                                'bg-blue-50 text-blue-600' => $stats['unread_messages'] === 0,
                            ]) aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($stats['unread_messages']) }}</p>
                        <p class="mt-1 text-xs font-semibold text-indigo-600 group-hover:text-indigo-800">{{ __('talenma.dashboard.talent.stats.open_inbox') }}</p>
                    </a>

                    <a
                        href="{{ route('talent.direct-hire.index') }}"
                        @class([
                            'group rounded-2xl border p-4 transition duration-150 hover:-translate-y-0.5 hover:shadow-md',
                            'border-indigo-200 bg-indigo-50/60 hover:bg-indigo-50 hover:ring-1 hover:ring-indigo-200' => $openDirectHires > 0,
                            'border-slate-200/90 bg-white hover:border-indigo-200 hover:bg-indigo-50/40' => $openDirectHires === 0,
                        ])
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.direct_hire') }}</p>
                            <span @class([
                                'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' => $openDirectHires > 0,
                                'bg-indigo-50 text-indigo-600' => $openDirectHires === 0,
                            ]) aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($openDirectHires) }}</p>
                        <p class="mt-1 text-xs font-semibold text-indigo-600 group-hover:text-indigo-800">{{ __('talenma.dashboard.talent.stats.open_direct_hire') }}</p>
                    </a>
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.dashboard.talent.stats.recent_title') }}</p>
                            <p class="text-xs text-slate-500">{{ __('talenma.dashboard.talent.stats.recent_subtitle') }}</p>
                        </div>
                    </div>

                    @if (count($stats['recent_activity']) === 0)
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-8 text-center">
                            <span class="mx-auto mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">{{ __('talenma.dashboard.talent.stats.recent_empty') }}</p>
                        </div>
                    @else
                        <ol class="activity-scroll relative mt-4 max-h-[calc(5*3.85rem+4*0.5rem)] space-y-2 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[0.95rem] before:top-2.5 before:bottom-2.5 before:w-px before:bg-indigo-100/90">
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
                                <li class="relative pl-9">
                                    <span class="absolute left-2.5 top-3.5 z-10 flex h-3 w-3 items-center justify-center">
                                        <span class="relative inline-flex h-2 w-2 rounded-full {{ $dotClass }} ring-2 ring-white"></span>
                                    </span>

                                    @if (! empty($item['href']))
                                        <a href="{{ $item['href'] }}" class="group flex items-start justify-between gap-3 rounded-xl bg-white/95 px-3 py-2.5 ring-1 ring-slate-200/70 shadow-sm transition duration-150 {{ $rowClass }} hover:-translate-y-px hover:shadow-md hover:ring-indigo-200">
                                            <div class="min-w-0">
                                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider ring-1 {{ $badgeClass }}">{{ $category }}</span>
                                                <p class="mt-1 text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 pt-0.5 text-[11px] font-medium tabular-nums text-slate-400" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </a>
                                    @else
                                        <div class="flex items-start justify-between gap-3 rounded-xl bg-white/95 px-3 py-2.5 ring-1 ring-slate-200/70 shadow-sm">
                                            <div class="min-w-0">
                                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider ring-1 {{ $badgeClass }}">{{ $category }}</span>
                                                <p class="mt-1 text-sm font-medium leading-snug text-slate-900">{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 pt-0.5 text-[11px] font-medium tabular-nums text-slate-400" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            {{-- Colonne droite : vidéo → profil → coordonnées --}}
            <aside class="space-y-5 min-w-0">
                @if ($profile?->profession_id)
                    <x-talent-video-snapshot
                        class="!h-auto"
                        :editable="true"
                        :video-url="$profile->presentation_video_url ?? null"
                        :person-name="trim($user->first_name.' '.$user->last_name) ?: $user->name"
                    />

                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.profile_snapshot') }}</p>
                                @if ($profile->professionLabel())
                                    <h3 class="mt-1.5 text-base font-semibold text-gray-900 truncate">
                                        {{ $profile->professionLabel() }}
                                    </h3>
                                @endif
                                @if ($profile->sectorLabel() || $profile->experience_years !== null)
                                    <p class="mt-0.5 text-sm text-gray-600">
                                        @if ($profile->sectorLabel())
                                            <span class="font-medium text-indigo-600">{{ $profile->sectorLabel() }}</span>
                                        @endif
                                        @if ($profile->sectorLabel() && $profile->experience_years !== null)
                                            <span class="text-gray-300"> · </span>
                                        @endif
                                        @if ($profile->experience_years !== null)
                                            <span>{{ __('talenma.talents.experience', ['years' => $profile->experience_years]) }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                            @if ($profile->availability)
                                @php
                                    $tone = $profile->statusTone();
                                    $toneClass = match ($tone) {
                                        'busy' => 'bg-gray-200 text-gray-700',
                                        'listening' => 'bg-amber-100 text-amber-800',
                                        default => 'bg-emerald-100 text-emerald-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full whitespace-nowrap {{ $toneClass }}">
                                    {{ $profile->statusLabel() }}
                                </span>
                            @endif
                        </div>

                        @php
                            $specialtyItems = collect(explode(',', (string) $profile->specialization))
                                ->map(fn ($item) => trim($item))
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp

                        @if ($specialtyItems->isNotEmpty())
                            <div class="mt-3">
                                <p class="text-xs text-gray-500">{{ __('talenma.dashboard.talent.specialty_skills') }}</p>
                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    @foreach ($specialtyItems as $item)
                                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($profile->workModeLabels())
                            <div class="mt-3">
                                <p class="text-xs text-gray-500">{{ __('talenma.talent.work_modes') }}</p>
                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    @foreach ($profile->workModeLabels() as $mode)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">{{ $mode }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @php $cvDocuments = $profile->cvDocuments(); @endphp
                        @if ($cvDocuments->isNotEmpty())
                            <div class="mt-3 border-t border-gray-100 pt-3 space-y-1.5">
                                <p class="text-xs text-gray-500">{{ __('talenma.talent.cv') }}</p>
                                @foreach ($cvDocuments as $cvDocument)
                                    <a
                                        href="{{ route('profile.documents.show', $cvDocument) }}"
                                        target="_blank"
                                        class="flex items-center justify-between gap-2 rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                                    >
                                        <span class="truncate">{{ $cvDocument->original_name }}</span>
                                        <span class="shrink-0 text-xs font-medium text-indigo-500">{{ $cvDocument->languageLabel() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @php
                        $hasContact = filled($profile->city)
                            || filled($profile->country)
                            || filled($profile->phone)
                            || filled($profile->whatsapp)
                            || filled($profile->linkedin_url)
                            || filled($profile->github_url)
                            || filled($profile->portfolio_url);
                        $locationLine = collect([$profile->city, $profile->countryLabel()])->filter()->implode(', ');
                    @endphp
                    <div class="bg-white rounded-2xl border p-4 sm:p-5">
                        <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.contact_snapshot') }}</p>

                        @if ($hasContact)
                            <dl class="mt-2.5 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                @if ($locationLine !== '')
                                    <div class="sm:col-span-2 min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">{{ __('talenma.talent.city') }}</dt>
                                        <dd class="min-w-0 font-medium text-gray-900 truncate">{{ $locationLine }}</dd>
                                    </div>
                                @endif
                                @if (filled($profile->phone))
                                    <div class="min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">{{ __('talenma.talent.phone') }}</dt>
                                        <dd class="min-w-0 truncate">
                                            <a href="tel:{{ preg_replace('/\s+/', '', $profile->phone) }}" class="font-medium text-gray-900 hover:text-indigo-700">{{ $profile->phone }}</a>
                                        </dd>
                                    </div>
                                @endif
                                @if (filled($profile->whatsapp))
                                    <div class="min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">{{ __('talenma.talent.whatsapp') }}</dt>
                                        <dd class="min-w-0 truncate">
                                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $profile->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-gray-900 hover:text-indigo-700">{{ $profile->whatsapp }}</a>
                                        </dd>
                                    </div>
                                @endif
                                @if (filled($profile->linkedin_url))
                                    <div class="min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">LinkedIn</dt>
                                        <dd class="min-w-0 truncate">
                                            <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800">{{ parse_url($profile->linkedin_url, PHP_URL_HOST) ?: $profile->linkedin_url }}</a>
                                        </dd>
                                    </div>
                                @endif
                                @if (filled($profile->github_url))
                                    <div class="min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">GitHub</dt>
                                        <dd class="min-w-0 truncate">
                                            <a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800">{{ parse_url($profile->github_url, PHP_URL_HOST) ?: $profile->github_url }}</a>
                                        </dd>
                                    </div>
                                @endif
                                @if (filled($profile->portfolio_url))
                                    <div class="sm:col-span-2 min-w-0 flex items-baseline gap-2">
                                        <dt class="shrink-0 text-xs text-gray-500">{{ __('talenma.talent.portfolio') }}</dt>
                                        <dd class="min-w-0 truncate">
                                            <a href="{{ $profile->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800">{{ parse_url($profile->portfolio_url, PHP_URL_HOST) ?: $profile->portfolio_url }}</a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        @else
                            <p class="mt-2 text-sm text-gray-500">{{ __('talenma.dashboard.talent.contact_empty') }}</p>
                            <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ __('talenma.dashboard.talent.edit_profile') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <p class="text-sm text-gray-500">{{ __('talenma.dashboard.talent.complete_profile') }}</p>
                        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="mt-3 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                            {{ __('talenma.dashboard.talent.complete_profile') }}
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
