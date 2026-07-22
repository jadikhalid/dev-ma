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

        {{-- Statistiques d'activité --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.stats.title') }}</p>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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
                <div class="rounded-xl bg-slate-50 px-4 py-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.stats.recruitment') }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['recruitment_requests_total']) }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.talent.stats.recent_title') }}</p>
                @if (count($stats['recent_activity']) === 0)
                    <p class="mt-3 text-sm text-gray-500">{{ __('talenma.dashboard.talent.stats.recent_empty') }}</p>
                @else
                    <ul class="mt-3 divide-y divide-gray-100">
                        @foreach ($stats['recent_activity'] as $item)
                            <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 py-3 text-sm">
                                <span class="text-gray-800">
                                    @if ($item['type'] === 'cv_download')
                                        @if (filled($item['detail']))
                                            {{ __('talenma.dashboard.talent.stats.activity_cv_lang', ['actor' => $item['actor'], 'lang' => $item['detail']]) }}
                                        @else
                                            {{ __('talenma.dashboard.talent.stats.activity_cv', ['actor' => $item['actor']]) }}
                                        @endif
                                    @else
                                        {{ __('talenma.dashboard.talent.stats.activity_view', ['actor' => $item['actor']]) }}
                                    @endif
                                </span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $item['at']?->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        @if ($profile?->profession_id)
            <div class="grid lg:grid-cols-3 gap-6 items-start">
                <div class="bg-white rounded-2xl border p-6 sm:p-8 h-full">
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

                <x-talent-video-snapshot
                    :editable="true"
                    :video-url="$profile->presentation_video_url ?? null"
                    :person-name="trim($user->first_name.' '.$user->last_name) ?: $user->name"
                />

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
                <div class="bg-white rounded-2xl border p-6 sm:p-8 h-full">
                    <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.contact_snapshot') }}</p>

                    @if ($hasContact)
                        <div class="mt-4 grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            @if ($locationLine !== '')
                                <div class="sm:col-span-2 min-w-0">
                                    <p class="text-xs text-gray-500">{{ __('talenma.talent.city') }} / {{ __('talenma.talent.country') }}</p>
                                    <p class="mt-0.5 font-medium text-gray-900 truncate">{{ $locationLine }}</p>
                                </div>
                            @endif
                            @if (filled($profile->phone))
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">{{ __('talenma.talent.phone') }}</p>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $profile->phone) }}" class="mt-0.5 block font-medium text-gray-900 hover:text-indigo-700 truncate">{{ $profile->phone }}</a>
                                </div>
                            @endif
                            @if (filled($profile->whatsapp))
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">{{ __('talenma.talent.whatsapp') }}</p>
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $profile->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="mt-0.5 block font-medium text-gray-900 hover:text-indigo-700 truncate">{{ $profile->whatsapp }}</a>
                                </div>
                            @endif
                            @if (filled($profile->linkedin_url))
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">LinkedIn</p>
                                    <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="mt-0.5 block font-medium text-indigo-600 hover:text-indigo-800 truncate">{{ parse_url($profile->linkedin_url, PHP_URL_HOST) ?: $profile->linkedin_url }}</a>
                                </div>
                            @endif
                            @if (filled($profile->github_url))
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">GitHub</p>
                                    <a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer" class="mt-0.5 block font-medium text-indigo-600 hover:text-indigo-800 truncate">{{ parse_url($profile->github_url, PHP_URL_HOST) ?: $profile->github_url }}</a>
                                </div>
                            @endif
                            @if (filled($profile->portfolio_url))
                                <div class="min-w-0 sm:col-span-2">
                                    <p class="text-xs text-gray-500">{{ __('talenma.talent.portfolio') }}</p>
                                    <a href="{{ $profile->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="mt-0.5 block font-medium text-indigo-600 hover:text-indigo-800 truncate">{{ parse_url($profile->portfolio_url, PHP_URL_HOST) ?: $profile->portfolio_url }}</a>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="mt-4 text-sm text-gray-500">{{ __('talenma.dashboard.talent.contact_empty') }}</p>
                        <a href="{{ route('profile.details.edit') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.talent.edit_profile') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
