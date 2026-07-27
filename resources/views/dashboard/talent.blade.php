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
                        <a href="{{ route('profile.details.edit') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-5 lg:gap-6 items-start">
            {{-- Colonne gauche : activité --}}
            <div class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6 min-w-0">
                <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.stats.title') }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="rounded-xl bg-slate-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.views') }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['profile_views_7d']) }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                        <p class="text-xs text-gray-400">{{ __('talenma.dashboard.talent.stats.views_total', ['count' => number_format($stats['profile_views_total'])]) }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.cv_downloads') }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['cv_downloads_7d']) }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                    </div>
                    <a href="{{ route('inbox.index') }}" class="rounded-xl bg-slate-50 px-4 py-4 hover:bg-indigo-50 transition block">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.unread_messages') }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['unread_messages']) }}</p>
                        <p class="mt-1 text-xs font-medium text-indigo-600">{{ __('talenma.dashboard.talent.stats.open_inbox') }}</p>
                    </a>
                    <a href="{{ route('talent.direct-hire.index') }}" class="rounded-xl bg-slate-50 px-4 py-4 hover:bg-indigo-50 transition block">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.direct_hire') }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($openDirectHires) }}</p>
                        <p class="mt-1 text-xs font-medium text-indigo-600">{{ __('talenma.dashboard.talent.stats.open_direct_hire') }}</p>
                    </a>
                    <div class="rounded-xl bg-slate-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.recruitment') }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['recruitment_requests_total']) }}</p>
                    </div>
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
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.talent.stats.recent_empty') }}</p>
                        </div>
                    @else
                        <ol class="activity-scroll relative mt-4 max-h-[28.5rem] space-y-2.5 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
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
                                        'direct_hire_hired' => __('talenma.dashboard.talent.stats.activity_direct_hire_hired', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_closed_negative' => __('talenma.dashboard.talent.stats.activity_direct_hire_closed_negative', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_withdrawn' => __('talenma.dashboard.talent.stats.activity_direct_hire_withdrawn', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_round_added' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_added', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '', 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_round_result' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_result', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '', 'result' => $item['result'] ?? '']),
                                        'direct_hire_round_cancelled' => __('talenma.dashboard.talent.stats.activity_direct_hire_round_cancelled', ['actor' => $item['actor'], 'round' => $item['detail'] ?? '']),
                                        'direct_hire_message' => __('talenma.dashboard.talent.stats.activity_direct_hire_message', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        'direct_hire_message_sent' => __('talenma.dashboard.talent.stats.activity_direct_hire_message_sent', ['actor' => $item['actor'], 'subject' => $item['subject'] ?? '']),
                                        default => __('talenma.dashboard.talent.stats.activity_view', ['actor' => $item['actor']]),
                                    };

                                    [$category, $dotClass, $rowClass] = match (true) {
                                        ($item['type'] ?? '') === 'cv_download' => [
                                            __('talenma.dashboard.talent.stats.activity_cat_cv'),
                                            'bg-violet-500',
                                            'hover:bg-violet-50/80',
                                        ],
                                        str_starts_with((string) ($item['type'] ?? ''), 'direct_hire') => [
                                            __('talenma.dashboard.talent.stats.activity_cat_hire'),
                                            'bg-indigo-500',
                                            'hover:bg-indigo-50/80',
                                        ],
                                        default => [
                                            __('talenma.dashboard.talent.stats.activity_cat_profile'),
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
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ $category }}</span>
                                                <p class="mt-1.5 text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </a>
                                    @else
                                        <div class="flex flex-col gap-1.5 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                            <div class="min-w-0">
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ $category }}</span>
                                                <p class="mt-1.5 text-sm font-medium leading-snug text-slate-900">{{ $label }}</p>
                                            </div>
                                            <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>

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
                            <a href="{{ route('profile.details.edit') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ __('talenma.dashboard.talent.edit_profile') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <p class="text-sm text-gray-500">{{ __('talenma.dashboard.talent.complete_profile') }}</p>
                        <a href="{{ route('profile.details.edit') }}" class="mt-3 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                            {{ __('talenma.dashboard.talent.complete_profile') }}
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
