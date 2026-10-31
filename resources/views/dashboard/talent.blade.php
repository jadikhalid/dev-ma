@php
    $user = Auth::user();
    $welcomeName = $user->headerDisplayName();
    $professionLabel = $profile?->professionLabel();
    $sectorLabel = $profile?->sectorLabel();
@endphp

<x-app-layout>
    <x-process-help topic="dashboard" />

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-5">
        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                <x-user-avatar :user="$user" size="md" class="mx-auto sm:mx-0 shrink-0" />
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <p class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                        {{ __('talenma.dashboard.talent.hello', ['name' => $welcomeName]) }}
                    </p>
                    @if ($professionLabel || $sectorLabel)
                        <div class="mt-1 space-y-0.5">
                            @if ($professionLabel)
                                <p class="text-sm font-medium text-gray-800 truncate" title="{{ $professionLabel }}">
                                    {{ $professionLabel }}
                                </p>
                            @endif
                            @if ($sectorLabel)
                                <p class="text-xs sm:text-sm text-gray-500 truncate" title="{{ $sectorLabel }}">
                                    {{ $sectorLabel }}
                                </p>
                            @endif
                        </div>
                    @else
                        <a
                            href="{{ route('profile.edit', ['panel' => 'talent']) }}"
                            class="mt-1.5 inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            {{ __('talenma.dashboard.talent.welcome_profession_missing') }}
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-3 sm:gap-4 shrink-0">
                    <div class="relative w-16 h-16 sm:w-20 sm:h-20">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
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
                            <span class="text-sm sm:text-base font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[9px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.progress_label') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                        {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- 3 colonnes : infos | activités récentes | stats + apps --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,25fr)_minmax(0,50fr)_minmax(0,25fr)] lg:gap-5 items-start">
            @include('dashboard.partials.talent-info-column')

            <section class="order-1 min-w-0 space-y-4 lg:order-none" data-talent-activity-column>
                <div class="flex min-h-[3.25rem] items-center gap-2.5 rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h2 class="min-w-0 text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.stats.recent_title') }}</h2>
                </div>

                @include('dashboard.partials.talent-activity-feed')
            </section>

            @include('dashboard.partials.talent-actions-column')
        </div>
    </div>
</x-app-layout>
