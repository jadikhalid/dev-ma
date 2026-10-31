<aside class="order-2 min-w-0 space-y-4 lg:order-none" data-talent-info-column>
    <div class="flex min-h-[3.25rem] items-center rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
        <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.profile_section_title') }}</h2>
    </div>

    <x-talent-video-snapshot
        class="!h-auto"
        :compact="true"
        :editable="true"
        :video-url="$profile->presentation_video_url ?? null"
        :person-name="trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->name"
    />

    @if ($profile?->profession_id)
        @php
            $specialtyItems = collect(explode(',', (string) $profile->specialization))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->unique()
                ->values();
            $cvDocuments = $profile->cvDocuments();
            $toneClass = match ($profile->availability ? $profile->statusTone() : null) {
                'busy' => 'bg-slate-100 text-slate-700 ring-slate-200/80',
                'listening' => 'bg-amber-50 text-amber-800 ring-amber-200/80',
                'available', 'open' => 'bg-emerald-50 text-emerald-800 ring-emerald-200/80',
                default => $profile->availability
                    ? 'bg-emerald-50 text-emerald-800 ring-emerald-200/80'
                    : null,
            };
        @endphp
        <div class="rounded-2xl border border-slate-200/90 bg-white p-3 space-y-2.5">
            <div class="space-y-1">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.profile_snapshot') }}</p>
                    @if ($profile->availability && $toneClass)
                        <span class="inline-flex shrink-0 items-center rounded px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $toneClass }}">
                            {{ $profile->statusLabel() }}
                        </span>
                    @endif
                </div>

                @if ($profile->professionLabel())
                    <h3 class="text-xs font-semibold leading-snug text-slate-900">
                        {{ $profile->professionLabel() }}
                    </h3>
                @endif

                @if ($profile->sectorLabel() || $profile->hasExperienceDeclared())
                    <p class="text-[11px] leading-snug text-slate-600">
                        @if ($profile->sectorLabel())
                            <span class="font-medium text-indigo-700">{{ $profile->sectorLabel() }}</span>
                        @endif
                        @if ($profile->sectorLabel() && $profile->hasExperienceDeclared())
                            <span class="text-slate-300"> · </span>
                        @endif
                        @if ($profile->hasExperienceDeclared())
                            <span>{{ $profile->experienceLabel() }}</span>
                        @endif
                    </p>
                @endif
            </div>

            @if ($specialtyItems->isNotEmpty())
                <div class="space-y-1 border-t border-slate-100 pt-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ __('talenma.dashboard.talent.specialty_skills') }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($specialtyItems as $item)
                            <span class="rounded bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium leading-tight text-indigo-800">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($profile->workModeLabels())
                <div class="space-y-1 border-t border-slate-100 pt-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ __('talenma.talent.work_modes') }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($profile->workModeLabels() as $mode)
                            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium leading-tight text-slate-700">{{ $mode }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($cvDocuments->isNotEmpty())
                <div class="space-y-1 border-t border-slate-100 pt-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ __('talenma.talent.cv') }}</p>
                    <div class="space-y-1">
                        @foreach ($cvDocuments as $cvDocument)
                            <a
                                href="{{ route('profile.documents.show', $cvDocument) }}"
                                target="_blank"
                                class="group flex items-center gap-1.5 rounded-lg border border-indigo-100/80 bg-indigo-50/50 px-2 py-1.5 transition hover:border-indigo-200 hover:bg-indigo-50"
                            >
                                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded bg-white text-indigo-600 ring-1 ring-indigo-100" aria-hidden="true">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1 truncate text-[11px] font-semibold text-indigo-800 group-hover:text-indigo-950" title="{{ $cvDocument->original_name }}">{{ $cvDocument->original_name }}</span>
                                <span class="shrink-0 text-[10px] font-medium text-indigo-500">{{ $cvDocument->languageLabel() }}</span>
                            </a>
                        @endforeach
                    </div>
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
            $contactRows = collect([
                $locationLine !== '' ? [
                    'label' => __('talenma.talent.city'),
                    'value' => $locationLine,
                    'href' => null,
                    'external' => false,
                ] : null,
                filled($profile->phone) ? [
                    'label' => __('talenma.talent.phone'),
                    'value' => $profile->phone,
                    'href' => 'tel:'.preg_replace('/\s+/', '', $profile->phone),
                    'external' => false,
                ] : null,
                filled($profile->whatsapp) ? [
                    'label' => __('talenma.talent.whatsapp'),
                    'value' => $profile->whatsapp,
                    'href' => 'https://wa.me/'.preg_replace('/\D+/', '', $profile->whatsapp),
                    'external' => true,
                ] : null,
                filled($profile->linkedin_url) ? [
                    'label' => 'LinkedIn',
                    'value' => parse_url($profile->linkedin_url, PHP_URL_HOST) ?: $profile->linkedin_url,
                    'href' => $profile->linkedin_url,
                    'external' => true,
                ] : null,
                filled($profile->github_url) ? [
                    'label' => 'GitHub',
                    'value' => parse_url($profile->github_url, PHP_URL_HOST) ?: $profile->github_url,
                    'href' => $profile->github_url,
                    'external' => true,
                ] : null,
                filled($profile->portfolio_url) ? [
                    'label' => __('talenma.talent.portfolio'),
                    'value' => parse_url($profile->portfolio_url, PHP_URL_HOST) ?: $profile->portfolio_url,
                    'href' => $profile->portfolio_url,
                    'external' => true,
                ] : null,
            ])->filter()->values();
        @endphp
        <div class="rounded-2xl border border-slate-200/90 bg-white p-3 space-y-2">
            <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.contact_snapshot') }}</p>

            @if ($hasContact)
                <dl class="space-y-1">
                    @foreach ($contactRows as $row)
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="shrink-0 text-[10px] font-bold uppercase tracking-[0.06em] text-slate-400">{{ $row['label'] }}</dt>
                            <dd class="min-w-0 max-w-[65%] text-right text-[11px] font-semibold leading-snug text-slate-900">
                                @if ($row['href'])
                                    <a
                                        href="{{ $row['href'] }}"
                                        @if ($row['external']) target="_blank" rel="noopener noreferrer" @endif
                                        class="block truncate text-indigo-700 transition hover:text-indigo-900"
                                        title="{{ $row['value'] }}"
                                    >{{ $row['value'] }}</a>
                                @else
                                    <span class="block truncate">{{ $row['value'] }}</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.contact_empty') }}</p>
                <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="inline-flex text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">
                    {{ __('talenma.dashboard.talent.edit_profile') }}
                </a>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border p-5 sm:p-6">
            <p class="text-sm text-gray-500">{{ __('talenma.dashboard.talent.complete_profile') }}</p>
            <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="mt-3 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                {{ __('talenma.dashboard.talent.complete_profile') }}
            </a>
        </div>
    @endif
</aside>
