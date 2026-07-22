@php
    $user = Auth::user();
    $companyName = $user->name;
@endphp

<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('recruitment_sent'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.dashboard.company.request_sent') }}</div>
        @endif

        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                <x-company-logo :profile="$profile" size="lg" class="mx-auto lg:mx-0" />
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    <p class="text-lg font-semibold text-gray-900">
                        {{ __('talenma.dashboard.welcome', ['name' => $companyName]) }}
                    </p>
                    @if ($profile?->sector)
                        <p class="mt-1 text-sm text-gray-500">{{ $profile->sector }}</p>
                    @endif
                    @if (! $completion['is_catalog_ready'])
                        <span class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            {{ __('talenma.dashboard.company.profile_incomplete') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-5 shrink-0">
                    <div class="relative w-24 h-24">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="10" />
                            <circle
                                cx="50" cy="50" r="42" fill="none"
                                stroke="{{ $completion['percent'] >= 100 ? '#10b981' : '#059669' }}"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $completion['percent'] / 100) }}"
                            />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.company.progress_label') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('company.profile.edit') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition">
                        {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.company.edit_profile') : __('talenma.dashboard.company.complete_profile') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border p-6 sm:p-8">
                    <h3 class="text-xl font-bold">{{ __('talenma.dashboard.company.recruit_title') }}</h3>
                    <p class="mt-2 text-gray-600 text-sm">{{ __('talenma.dashboard.company.recruit_desc') }}</p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        @if ($completion['is_catalog_ready'])
                            <a href="{{ route('company.search') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 text-center">{{ __('talenma.dashboard.company.browse') }}</a>
                        @else
                            <span class="px-5 py-2.5 bg-gray-100 text-gray-400 text-sm font-semibold rounded-lg text-center cursor-not-allowed" title="{{ __('talenma.dashboard.company.profile_incomplete') }}">{{ __('talenma.dashboard.company.browse') }}</span>
                        @endif
                        <a href="{{ route('recruitment.create') }}" class="px-5 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50 text-center">{{ __('talenma.dashboard.company.intermediary') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border p-6 sm:p-8">
                    <h3 class="text-xl font-bold">{{ __('talenma.dashboard.company.morocco_title') }}</h3>
                    <p class="mt-2 text-gray-600 text-sm">{{ __('talenma.dashboard.company.morocco_desc') }}</p>
                    <a href="{{ route('services.index') }}" class="mt-4 inline-block text-indigo-600 font-semibold text-sm hover:text-indigo-800">{{ __('talenma.dashboard.company.morocco_link') }}</a>
                </div>
            </div>

            <div class="space-y-6">
                @if ($profile && ($profile->sector || $profile->hiring_needs))
                    <div class="bg-white rounded-2xl border p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">{{ __('talenma.dashboard.company.my_company') }}</p>
                        @if ($profile->sector)
                            <p class="mt-2 text-sm text-gray-600">{{ $profile->sector }}</p>
                        @endif
                        @if ($profile->city || $profile->country)
                            <p class="text-sm text-gray-500">{{ collect([$profile->city, $profile->countryLabel()])->filter()->implode(', ') }}</p>
                        @endif
                        @if ($profile->hiring_needs)
                            <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ Str::limit($profile->hiring_needs, 120) }}</p>
                        @endif
                        <a href="{{ route('company.profile.edit') }}" class="mt-3 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-800">{{ __('talenma.dashboard.company.edit_company') }}</a>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border p-6">
                        <h4 class="font-semibold">{{ __('talenma.dashboard.company.my_company') }}</h4>
                        <p class="mt-2 text-sm text-gray-600">{{ __('talenma.dashboard.company.complete_profile') }}</p>
                        <a href="{{ route('company.profile.edit') }}" class="mt-3 inline-block text-sm text-emerald-600 font-medium">{{ __('talenma.dashboard.company.edit_company') }}</a>
                    </div>
                @endif

                @if ($recentRequests->isNotEmpty())
                    <div class="bg-white rounded-2xl border p-6">
                        <h4 class="font-semibold">{{ __('talenma.dashboard.company.recent_requests') }}</h4>
                        <ul class="mt-3 space-y-2 text-sm">
                            @foreach ($recentRequests as $req)
                                <li class="text-gray-600">
                                    <span class="font-medium text-gray-900">{{ $req->subject }}</span>
                                    <span class="text-xs text-gray-400"> — {{ $req->mode === 'intermediary' ? __('talenma.talents.intermediary') : __('talenma.recruitment.mode_direct') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
