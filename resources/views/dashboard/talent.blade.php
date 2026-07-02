@php
    $user = Auth::user();
    $availabilityLabels = [
        'disponible' => 'available',
        'sous 2 semaines' => 'two_weeks',
        'mission en cours' => 'on_mission',
    ];
    $workModeLabels = [
        'remote' => 'work_mode_remote',
        'visa_sponsorship' => 'work_mode_visa',
        'local' => 'work_mode_local',
    ];
    $nextSection = $completion['next_section'];
    $nextSectionData = $nextSection ? $completion['sections'][$nextSection] : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.dashboard.talent.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.dashboard.talent.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                <x-user-avatar :user="$user" size="lg" class="mx-auto lg:mx-0" />
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    <p class="text-lg font-semibold text-gray-900">
                        {{ __('talenma.dashboard.welcome', ['name' => $user->name]) }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600">
                        @if ($completion['status'] === 'complete')
                            {{ __('talenma.dashboard.talent.status_complete') }}
                        @elseif ($completion['status'] === 'in_progress')
                            {{ __('talenma.dashboard.talent.status_in_progress') }}
                        @else
                            {{ __('talenma.dashboard.talent.status_starter') }}
                        @endif
                    </p>
                    @if ($completion['is_catalog_ready'])
                        <span class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ __('talenma.dashboard.talent.catalog_ready') }}
                        </span>
                    @else
                        <span class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            {{ __('talenma.dashboard.talent.catalog_pending') }}
                        </span>
                    @endif
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
                            <span class="text-2xl font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.progress_label') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.details.edit') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                        {{ $completion['percent'] >= 100 ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Colonne principale : résumé + prochaine étape --}}
            <div class="lg:col-span-2 space-y-6">
                @if ($profile?->registration_description || $profile?->documents?->isNotEmpty())
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.registration_dossier') }}</p>
                        <h3 class="mt-2 text-lg font-bold text-gray-900">{{ $profile->sectorLabel() ?? '—' }}</h3>
                        @if ($profile->registration_description)
                            <p class="mt-3 text-sm text-gray-600">{{ $profile->registration_description }}</p>
                        @endif
                        @if ($profile->documents?->isNotEmpty())
                            <ul class="mt-4 space-y-2">
                                @foreach ($profile->documents as $document)
                                    <li>
                                        <a href="{{ $document->url() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            <span>📎</span>
                                            <span>{{ $document->original_name }}</span>
                                            <span class="text-gray-400">({{ $document->formattedSize() }})</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                @if ($profile?->profession_id)
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.profile_snapshot') }}</p>
                                <h3 class="mt-2 text-xl font-bold text-gray-900">{{ $profile->title ?: $profile->specialization }}</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ $profile->sectorLabel() }}
                                    @if ($profile->professionLabel())
                                        · {{ $profile->professionLabel() }}
                                    @endif
                                    @if ($profile->specialization)
                                        · <span class="text-indigo-700 font-medium">{{ $profile->specialization }}</span>
                                    @endif
                                </p>
                            </div>
                            @if ($profile->daily_rate_eur)
                                <span class="px-3 py-1.5 bg-emerald-50 text-emerald-800 text-sm font-semibold rounded-full whitespace-nowrap">
                                    {{ $profile->daily_rate_eur }} {{ __('talenma.talents.per_day') }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3 text-sm text-gray-600">
                            @if ($profile->city)
                                <span>📍 {{ $profile->city }}{{ $profile->country ? ', '.$profile->country : '' }}</span>
                            @endif
                            @if ($profile->experience_years !== null)
                                <span>💼 {{ __('talenma.talents.experience', ['years' => $profile->experience_years]) }}</span>
                            @endif
                            @if ($profile->availability)
                                <span>⏱ {{ __('talenma.talent.'.($availabilityLabels[$profile->availability] ?? 'available')) }}</span>
                            @endif
                        </div>

                        @if (is_array($profile->work_modes) && count($profile->work_modes))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($profile->work_modes as $mode)
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">{{ __('talenma.talent.'.($workModeLabels[$mode] ?? $mode)) }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($profile->bio)
                            <p class="mt-5 text-sm text-gray-600 line-clamp-3">{{ $profile->bio }}</p>
                        @endif
                    </div>
                @else
                    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl border border-indigo-100 p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.dashboard.talent.empty_title') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('talenma.dashboard.talent.empty_desc') }}</p>
                        <a href="{{ route('profile.details.edit') }}" class="mt-5 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ __('talenma.dashboard.talent.start_profile') }}
                        </a>
                    </div>
                @endif

                @if ($nextSectionData && $completion['status'] !== 'complete')
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.next_step') }}</p>
                        <h3 class="mt-2 text-lg font-bold text-gray-900">{{ $nextSectionData['label'] }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('talenma.dashboard.talent.next_step_desc.'.$nextSection) }}</p>

                        <ul class="mt-4 space-y-2">
                            @foreach ($nextSectionData['items'] as $item)
                                @unless ($item['done'])
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <span class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-[10px] text-gray-400">○</span>
                                        {{ $item['label'] }}
                                    </li>
                                @endunless
                            @endforeach
                        </ul>

                        <a href="{{ route('profile.details.edit') }}" class="mt-5 inline-flex items-center text-indigo-600 font-semibold text-sm hover:text-indigo-800">
                            {{ __('talenma.dashboard.talent.continue_profile') }} →
                        </a>
                    </div>
                @elseif ($completion['status'] === 'complete')
                    <div class="bg-white rounded-2xl border p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.dashboard.talent.all_done_title') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('talenma.dashboard.talent.all_done_desc') }}</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('profile.details.edit') }}" class="inline-flex items-center px-4 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                                {{ __('talenma.dashboard.talent.edit_profile') }}
                            </a>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">
                                {{ __('talenma.nav.home') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Colonne latérale : parcours évolutif --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border p-6">
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.dashboard.talent.journey_title') }}</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.dashboard.talent.journey_desc') }}</p>

                    <ol class="mt-5 space-y-4">
                        @foreach (['profession', 'presentation', 'availability', 'links'] as $sectionKey)
                            @php $section = $completion['sections'][$sectionKey]; @endphp
                            <li class="relative pl-8">
                                @if (! $loop->last)
                                    <span class="absolute left-[11px] top-6 bottom-0 w-px {{ $section['complete'] ? 'bg-indigo-200' : 'bg-gray-200' }}" aria-hidden="true"></span>
                                @endif
                                <span @class([
                                    'absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold',
                                    'bg-indigo-600 text-white' => $section['complete'],
                                    'bg-indigo-100 text-indigo-700' => ! $section['complete'] && $sectionKey === $nextSection,
                                    'bg-gray-100 text-gray-400' => ! $section['complete'] && $sectionKey !== $nextSection,
                                ])>
                                    @if ($section['complete'])
                                        ✓
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $section['label'] }}</p>
                                    <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div
                                            class="h-full rounded-full transition-all {{ $section['complete'] ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                            style="width: {{ $section['percent'] }}%"
                                        ></div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $section['percent'] }}%</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-6">
                    <h4 class="font-semibold text-indigo-900">{{ __('talenma.dashboard.talent.tip_title') }}</h4>
                    <p class="mt-2 text-sm text-indigo-800/90">{{ __('talenma.dashboard.talent.tip_desc') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
