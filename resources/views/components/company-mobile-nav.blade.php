@props(['bandLinks', 'talentSpaceUrl'])

@php
    $onOfferPage = request()->routeIs('company.offer');
    $showOfferCtas = ! auth()->user()?->isCompany();
@endphp

{{-- Teleport + full-bleed panel: covers 100vw/100dvh with no leftover edge pixels. --}}
<template x-teleport="body">
<div
    class="fixed inset-0 z-[60] overflow-hidden xl:hidden"
    x-show="mobileNav"
    x-cloak
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div
        @click="mobileNav = false"
        class="absolute inset-0 bg-gray-950/40 backdrop-blur-[2px]"
        aria-hidden="true"
    ></div>

    <aside
        id="company-mobile-nav"
        x-show="mobileNav"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-0 z-[70] flex w-full flex-col bg-white"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('talenma.nav.mobile_menu_title') }}"
        data-company-mobile-nav
        @click.stop
    >
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-100 px-5">
            <p class="text-base font-bold text-gray-950">{{ __('talenma.nav.mobile_menu_title') }}</p>
            <button
                type="button"
                @click="mobileNav = false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900"
                aria-label="{{ __('talenma.nav.mobile_menu_close') }}"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-4">
            <a
                href="{{ route('company.offer') }}"
                @class([
                    'mb-2 flex items-center gap-3 rounded-xl border px-4 py-3.5 text-[0.9375rem] font-semibold transition-colors duration-300',
                    'border-indigo-600 bg-indigo-600 text-white' => $onOfferPage,
                    'border-gray-200 text-gray-950 hover:border-indigo-300 hover:bg-indigo-50' => ! $onOfferPage,
                ])
                @if ($onOfferPage) aria-current="page" @endif
                data-company-mobile-home
            >
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                {{ __('talenma.nav.home') }}
            </a>

            <ul class="mt-2 space-y-2" role="list">
                @foreach ($bandLinks as $link)
                    <li>
                        <a
                            href="{{ route($link['route']) }}"
                            @class([
                                'flex items-center justify-between rounded-xl border px-4 py-3.5 text-[0.9375rem] font-semibold transition-colors duration-300',
                                'border-indigo-600 bg-indigo-600 text-white' => $link['active'],
                                'border-gray-200 text-gray-950 hover:border-indigo-300 hover:bg-indigo-50' => ! $link['active'],
                            ])
                            @if ($link['active']) aria-current="page" @endif
                        >
                            {{ $link['label'] }}
                            <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </li>
                @endforeach
                <li>
                    <a
                        href="{{ $talentSpaceUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3.5 text-[0.9375rem] font-semibold text-gray-950 transition-colors duration-300 hover:border-indigo-300 hover:bg-indigo-50"
                        data-company-mobile-talent-space
                        @click="mobileNav = false"
                    >
                        {{ __('talenma.nav.talent_space') }}
                        <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                </li>
            </ul>

            <div class="mt-6 flex items-center justify-between px-1">
                <span class="text-sm font-medium text-gray-600">{{ __('talenma.nav.mobile_menu_language') }}</span>
                <x-header-locale-menu />
            </div>
        </nav>

        @if ($showOfferCtas)
            <div class="grid shrink-0 gap-2.5 border-t border-gray-100 p-4">
                @if (request()->routeIs('company.demo'))
                    <button
                        type="button"
                        disabled
                        class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-xl border-[1.5px] border-gray-300 bg-gray-100 px-6 py-3 text-sm font-semibold text-gray-400"
                        data-company-mobile-demo
                    >{{ __('talenma.nav.request_demo') }}</button>
                @else
                    <a
                        href="{{ route('company.demo') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl border-[1.5px] border-gray-900 px-6 py-3 text-sm font-semibold text-gray-900 transition hover:bg-gray-50"
                        data-company-mobile-demo
                    >{{ __('talenma.nav.request_demo') }}</a>
                @endif
                @if (request()->routeIs('company.trial'))
                    <button
                        type="button"
                        disabled
                        class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-gray-200 px-6 py-3 text-sm font-semibold text-gray-400"
                        data-company-mobile-trial
                    >{{ __('talenma.company_offer.hero_cta_trial') }}</button>
                @else
                    <a
                        href="{{ route('company.trial') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)] px-6 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_-12px_rgba(79,70,229,0.8)]"
                        data-company-mobile-trial
                    >{{ __('talenma.company_offer.hero_cta_trial') }}</a>
                @endif
            </div>
        @endif
    </aside>
</div>
</template>
