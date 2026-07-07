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
@endphp

<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                <x-user-avatar :user="$user" size="lg" class="mx-auto lg:mx-0" />
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    <p class="text-lg font-semibold text-gray-900">
                        {{ __('talenma.dashboard.welcome', ['name' => $user->name]) }}
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
                            <span class="text-2xl font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.talent.progress_label') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.details.edit') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                        {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                    </a>
                </div>
            </div>
        </div>

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
        @endif
    </div>
</x-app-layout>
