@php
    $showCompanyPanel = $showCompanyPanel ?? false;
    $panel = $panel ?? 'account';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start gap-3">
            @if ($showCompanyPanel)
                <span class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995s.145.755.438.995l1.003.827c.424.35.534.954.26 1.431l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.437-.995s-.145-.755-.437-.995l-1.004-.827a1.125 1.125 0 0 1-.26-1.431l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                </span>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $showCompanyPanel ? __('talenma.account.settings_title') : __('talenma.account.title') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if ($showCompanyPanel)
                        {{ __('talenma.account.settings_subtitle') }}
                    @elseif (Auth::user()->isCompany())
                        {{ __('talenma.account.subtitle_company') }}
                    @else
                        {{ __('talenma.account.subtitle') }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-10"
        data-ajax-network-error="{{ __('talenma.common.network_error') }}"
        data-ajax-timeout-error="{{ __('talenma.common.timeout_error') }}"
    >
        <x-toast-stack />
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($showCompanyPanel)
                <div class="flex flex-col lg:flex-row gap-8">
                    <aside class="lg:w-56 shrink-0">
                        <nav class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0" aria-label="{{ __('talenma.account.settings_nav') }}">
                            <a
                                href="{{ route('profile.edit', ['panel' => 'account']) }}"
                                @class([
                                    'inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3.5 py-2.5 text-sm font-semibold transition',
                                    'bg-indigo-50 text-indigo-800' => $panel === 'account',
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => $panel !== 'account',
                                ])
                            >
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                {{ __('talenma.nav.my_account') }}
                            </a>
                            <a
                                href="{{ route('profile.edit', ['panel' => 'company']) }}"
                                @class([
                                    'inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3.5 py-2.5 text-sm font-semibold transition',
                                    'bg-indigo-50 text-indigo-800' => $panel === 'company',
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => $panel !== 'company',
                                ])
                            >
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3M21 21v-6.75A2.25 2.25 0 0 0 18.75 12h-2.25"/></svg>
                                {{ __('talenma.nav.my_company') }}
                            </a>
                        </nav>

                        @if ($panel === 'company' && isset($profile))
                            <div class="hidden lg:block mt-6 pt-6 border-t border-gray-100">
                                <div class="flex items-center gap-3">
                                    <x-company-logo :profile="$profile" size="sm" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                        <p id="company-header-sector" class="text-xs text-gray-500 truncate">{{ $profile->sector ?? '—' }}</p>
                                        @php
                                            $locationLabel = collect([$profile->city, $profile->countryLabel()])->filter()->implode(', ');
                                        @endphp
                                        <p
                                            id="company-header-location"
                                            class="mt-0.5 text-[11px] font-medium text-emerald-600 truncate"
                                            @class(['hidden' => $locationLabel === ''])
                                        >{{ $locationLabel }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </aside>

                    <div class="min-w-0 flex-1 space-y-6">
                        @if ($panel === 'company')
                            @include('company.partials.fiche-content')
                        @else
                            @include('profile.partials.account-panels')
                        @endif
                    </div>
                </div>
            @else
                <div class="max-w-3xl mx-auto space-y-6">
                    @include('profile.partials.account-panels')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
