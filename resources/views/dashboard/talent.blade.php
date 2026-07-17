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
                    <div class="flex flex-col items-start gap-2">
                        @if (($completion['total_count'] ?? 0) > 0)
                            <p class="text-sm text-gray-500">
                                {{ __('talenma.dashboard.talent.progress_detail', [
                                    'done' => $completion['done_count'],
                                    'total' => $completion['total_count'],
                                ]) }}
                            </p>
                        @endif
                        <a href="{{ route('profile.details.edit') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.talent.edit_profile') : __('talenma.dashboard.talent.complete_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($profile?->profession_id)
            <div class="bg-white rounded-2xl border p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.profile_snapshot') }}</p>
                        @if ($profile->professionLabel())
                            <h3 class="mt-2 text-base font-semibold text-gray-900">
                                {{ $profile->professionLabel() }}
                            </h3>
                        @endif
                        @if ($profile->sectorLabel())
                            <p class="mt-1 text-sm font-medium text-indigo-600">
                                {{ $profile->sectorLabel() }}
                            </p>
                        @endif

                        @php
                            $specialtyItems = collect(explode(',', (string) $profile->specialization))
                                ->map(fn ($item) => trim($item))
                                ->filter()
                                ->merge(is_array($profile->skills) ? $profile->skills : [])
                                ->unique()
                                ->values();
                        @endphp

                        @if ($specialtyItems->isNotEmpty())
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-gray-700">{{ __('talenma.dashboard.talent.specialty_skills') }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($specialtyItems as $item)
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
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
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full whitespace-nowrap {{ $toneClass }}">
                            {{ $profile->statusLabel() }}
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
                </div>

                @if ($profile->workModeLabels())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($profile->workModeLabels() as $mode)
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">{{ $mode }}</span>
                        @endforeach
                    </div>
                @endif

                @php $cvDocument = $profile->cvDocument(); @endphp
                @if ($cvDocument)
                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <p class="text-sm font-semibold text-gray-700">{{ __('talenma.talent.cv') }}</p>
                        <a
                            href="{{ route('profile.documents.show', $cvDocument) }}"
                            target="_blank"
                            class="mt-2 inline-flex items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50/70 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                        >
                            <span class="truncate max-w-xs">{{ $cvDocument->original_name }}</span>
                            <span class="text-xs font-medium text-indigo-500">{{ $cvDocument->formattedSize() }}</span>
                        </a>
                    </div>
                @endif

            </div>
        @endif
    </div>
</x-app-layout>
