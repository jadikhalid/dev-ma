@php
    $user = Auth::user();
    $isOwner = $user->isCompanyOwner();
    $profileEditUrl = route('profile.edit', ['panel' => 'account']);
    $actionCols = $isOwner ? 'md:grid-cols-3' : 'md:grid-cols-2';
    $welcomeName = $isOwner
        ? (filled($profile?->representative_name) ? $profile->representative_name : $user->name)
        : (trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->name);
    $welcomeRole = $isOwner
        ? __('talenma.dashboard.company.welcome_role_owner')
        : __('talenma.dashboard.company.welcome_role_member');
@endphp

<x-app-layout>
    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-5">
        @if (session('recruitment_sent'))
            <div class="p-3 sm:p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ __('talenma.dashboard.company.request_sent') }}</div>
        @endif

        {{-- Bandeau d'accueil + progression --}}
        <div class="bg-white rounded-2xl border px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                <x-company-logo :profile="$profile" size="md" class="mx-auto sm:mx-0 shrink-0" />
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <p class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                        {{ __('talenma.dashboard.welcome', ['name' => $welcomeName]) }}
                    </p>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $welcomeRole }}</p>
                    @if (! $completion['is_catalog_ready'])
                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ __('talenma.dashboard.company.profile_incomplete') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-3 sm:gap-4 shrink-0">
                    <div class="relative w-16 h-16 sm:w-20 sm:h-20">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
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
                            <span class="text-sm sm:text-base font-bold text-gray-900">{{ $completion['percent'] }}%</span>
                            <span class="text-[9px] uppercase tracking-wide text-gray-500">{{ __('talenma.dashboard.company.progress_label') }}</span>
                        </div>
                    </div>
                    @if ($isOwner)
                        <a href="{{ $profileEditUrl }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition whitespace-nowrap">
                            {{ ($completion['status'] === 'complete' || $completion['percent'] >= 100) ? __('talenma.dashboard.company.edit_profile') : __('talenma.dashboard.company.complete_profile') }}
                        </a>
                    @else
                        <a href="{{ route('company.jobs.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition whitespace-nowrap">
                            {{ __('talenma.dashboard.company.jobs_manage') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions principales --}}
        <div class="grid grid-cols-1 {{ $actionCols }} gap-4">
            <div class="bg-white rounded-2xl border p-5 flex flex-col">
                <h3 class="text-base font-bold text-gray-900">{{ __('talenma.dashboard.company.recruit_title') }}</h3>
                <p class="mt-1.5 text-sm text-gray-600 flex-1">{{ __('talenma.dashboard.company.recruit_desc') }}</p>
                <div class="mt-4 flex flex-col gap-2">
                    <a href="{{ route('company.search') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 text-center">{{ __('talenma.dashboard.company.browse') }}</a>
                    <a href="{{ route('recruitment.create') }}" class="px-4 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50 text-center">{{ __('talenma.dashboard.company.intermediary') }}</a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border p-5 flex flex-col">
                <h3 class="text-base font-bold text-gray-900">{{ __('talenma.dashboard.company.jobs_title') }}</h3>
                <p class="mt-1.5 text-sm text-gray-600 flex-1">{{ __('talenma.dashboard.company.jobs_desc') }}</p>
                <div class="mt-4 flex flex-col gap-2">
                    <a href="{{ route('company.jobs.index') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 text-center">{{ __('talenma.dashboard.company.jobs_manage') }}</a>
                    <a href="{{ route('company.jobs.create') }}" class="px-4 py-2 border border-emerald-200 text-emerald-700 text-sm font-semibold rounded-lg hover:bg-emerald-50 text-center">{{ __('talenma.dashboard.company.jobs_create') }}</a>
                </div>
            </div>

            @if ($isOwner)
                <div class="bg-white rounded-2xl border p-5 flex flex-col">
                    <h3 class="text-base font-bold text-gray-900">{{ __('talenma.dashboard.company.morocco_title') }}</h3>
                    <p class="mt-1.5 text-sm text-gray-600 flex-1">{{ __('talenma.dashboard.company.morocco_desc') }}</p>
                    <div class="mt-4">
                        <a href="{{ route('services.index') }}" class="inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.dashboard.company.morocco_link') }}</a>
                    </div>
                </div>
            @endif
        </div>

        @if ($recentRequests->isNotEmpty())
            <div class="bg-white rounded-2xl border p-5">
                <h4 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.company.recent_requests') }}</h4>
                <ul class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    @foreach ($recentRequests as $req)
                        <li class="text-gray-600 truncate">
                            <span class="font-medium text-gray-900">{{ $req->subject }}</span>
                            <span class="text-xs text-gray-400"> — {{ $req->mode === 'intermediary' ? __('talenma.talents.intermediary') : __('talenma.recruitment.mode_direct') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-app-layout>
