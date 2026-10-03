@extends('layouts.public')

@section('title', __('talenma.company_offer.meta_title'))
@section('meta_description', __('talenma.company_offer.meta_description'))

@section('content')
@php
    $showOfferForms = ! auth()->user()?->isCompany();
    $demoOld = old('offer_form') === 'demo';
    $initialDrawer = match (true) {
        ! $showOfferForms => null,
        old('offer_form') === 'trial', request('tab') === 'trial' => 'trial',
        $demoOld, request('tab') === 'demo' => 'demo',
        default => null,
    };

    $offerIncludes = [
        __('talenma.company_offer.includes_1'),
        __('talenma.company_offer.includes_2'),
        __('talenma.company_offer.includes_3'),
        __('talenma.company_offer.includes_4'),
    ];

    $thousandsSeparator = app()->getLocale() === 'fr' ? ' ' : ',';
    $talentCountLabel = number_format($talentCount, 0, ',', $thousandsSeparator);
    $companyCountLabel = number_format($companyCount, 0, ',', $thousandsSeparator);
@endphp

<section
    class="relative overflow-hidden bg-gradient-to-br from-white via-sky-50 to-sky-100"
    @if ($showOfferForms)
    x-data="{
        drawer: @js($initialDrawer),
        init() {
            if (window.location.hash === '#trial') this.drawer = 'trial';
            if (window.location.hash === '#demo') this.drawer = 'demo';
            this.lockScroll(this.drawer);
            this.$watch('drawer', (value, previous) => {
                this.lockScroll(value);
                if (previous === 'demo' && value !== 'demo') {
                    window.dispatchEvent(new CustomEvent('company-offer-demo-reset'));
                }
                if (previous === 'trial' && value !== 'trial') {
                    window.dispatchEvent(new CustomEvent('company-offer-trial-reset'));
                }
                const hash = value ? '#' + value : '';
                if (window.location.hash !== hash) {
                    history.replaceState(null, '', window.location.pathname + window.location.search + hash);
                }
            });
        },
        lockScroll(value) {
            document.documentElement.classList.toggle('overflow-hidden', !! value);
        },
        open(value) {
            this.drawer = value;
        },
        close() {
            this.drawer = null;
        }
    }"
    @company-offer-drawer.window="open($event.detail)"
    @company-offer-form-sent="close()"
    @company-offer-drawer-close="close()"
    @keydown.escape.window="close()"
    @endif
    data-company-offer-hero
>
    <div class="home-shell relative">
    <div class="grid items-center pb-12 pt-8 sm:gap-12 sm:pt-[4.5rem] lg:grid-cols-2 lg:gap-16 lg:px-10 lg:pb-16 lg:pt-[6.25rem] xl:px-16 xl:pt-[6.75rem] 2xl:px-24 2xl:pt-[7.25rem]">
        {{-- Colonne gauche (sur téléphone, ses enfants sont réordonnés autour de l'image) --}}
        <div class="contents sm:block">
            <p class="mb-5 inline-flex items-center order-1 justify-self-start sm:order-none sm:justify-self-auto gap-2.5 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 px-5 py-2.5 text-base font-bold tracking-wide text-white shadow-lg shadow-indigo-500/30 ring-1 ring-indigo-700/20">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                {{ __('talenma.company_offer.hero_badge') }}
            </p>
            <h1 class="order-2 pl-1.5 text-4xl sm:order-none font-extrabold leading-[1.1] tracking-tight text-gray-950 sm:pl-0 sm:text-5xl lg:text-[2.8rem]">
                {{ __('talenma.company_offer.hero_title') }}
            </h1>

            <ol class="order-4 mt-12 space-y-2 pl-1.5 sm:order-none sm:mt-6 sm:pl-[10%]" role="list">
                @foreach ($offerIncludes as $index => $include)
                    <li class="flex items-start gap-3 text-[0.9375rem] leading-snug text-gray-800">
                        <span class="mt-px inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-950 text-[10px] font-bold text-white" aria-hidden="true">{{ $index + 1 }}</span>
                        <span>{{ $include }}</span>
                    </li>
                @endforeach
            </ol>

            @if ($showOfferForms)
            <div class="mt-8 grid grid-cols-1 order-5 gap-2.5 sm:order-none sm:inline-grid sm:grid-cols-2 sm:gap-3">
                <button
                    type="button"
                    @click="open('demo')"
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 bg-gray-950 px-6 py-2.5 text-sm font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800"
                >{{ __('talenma.company_offer.hero_cta_demo') }}</button>
                <button
                    type="button"
                    @click="open('trial')"
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 px-6 py-2.5 text-sm font-semibold text-gray-950 transition hover:bg-gray-950 hover:text-white"
                >{{ __('talenma.company_offer.hero_cta_trial') }}</button>
            </div>
            @else
            <a
                href="{{ route('dashboard') }}"
                class="mt-8 inline-flex items-center justify-center order-5 sm:order-none rounded-md border-2 border-gray-950 bg-gray-950 px-6 py-2.5 text-sm font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800"
                data-company-offer-dashboard-link
            >{{ auth()->user()->dashboardNavLabel() }}</a>
            @endif
        </div>

        {{-- Colonne droite --}}
        <div class="relative mx-auto w-full order-3 mt-10 max-w-md sm:order-none sm:mt-0 lg:max-w-none">
            <img
                src="{{ asset('images/company/offer-hero.jpg') }}"
                alt="{{ __('talenma.company_offer.hero_image_alt') }}"
                class="h-80 w-full rounded-2xl object-cover shadow-xl sm:h-[28rem] lg:h-[30rem]"
                loading="eager"
            >

            <div class="absolute -top-4 left-2 w-40 overflow-hidden rounded-xl bg-amber-200 shadow-xl ring-1 ring-amber-300 sm:-left-6 sm:-top-5 sm:w-64">
                <p class="bg-amber-100 px-3 py-1 text-center text-[10px] font-semibold text-amber-900 sm:px-4 sm:py-1.5 sm:text-[11px]">{{ __('talenma.company_offer.partners_label') }}</p>
                <div class="px-3 py-2 sm:px-4 sm:py-3">
                    <p class="text-xl font-extrabold text-gray-950 sm:text-3xl" data-company-offer-company-count>{{ $companyCountLabel }}+</p>
                    <p class="mt-1 text-[11px] font-medium leading-tight text-gray-800 sm:mt-1.5 sm:text-xs sm:leading-snug">{{ __('talenma.company_offer.partners_text') }}</p>
                </div>
            </div>

            <div class="absolute -bottom-4 right-2 w-40 overflow-hidden rounded-xl bg-amber-200 shadow-xl ring-1 ring-amber-300 sm:-bottom-5 sm:-right-5 sm:w-64">
                <p class="bg-amber-100 px-3 py-1 text-center text-[10px] font-semibold text-amber-900 sm:px-4 sm:py-1.5 sm:text-[11px]">{{ __('talenma.company_offer.talents_label') }}</p>
                <div class="px-3 py-2 sm:px-4 sm:py-3">
                    <p class="text-xl font-extrabold text-gray-950 sm:text-3xl" data-company-offer-talent-count>{{ $talentCountLabel }}+</p>
                    <p class="mt-1 text-[11px] font-medium leading-tight text-gray-800 sm:mt-1.5 sm:text-xs sm:leading-snug">{{ __('talenma.company_offer.talents_text') }}</p>
                </div>
            </div>
        </div>
    </div>
    </div>

    @if ($showOfferForms)
    {{-- Fond des panneaux --}}
    <div
        x-show="drawer"
        x-cloak
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 z-[60] bg-gray-950/40 backdrop-blur-[2px]"
        aria-hidden="true"
    ></div>

    {{-- Panneau : demande de démo --}}
    <aside
        id="demo"
        x-show="drawer === 'demo'"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[70] flex h-screen w-full flex-col bg-white shadow-2xl md:w-[min(34rem,80vw)] lg:w-[min(36rem,52vw)] xl:w-[min(38rem,42vw)] 2xl:w-[min(40rem,34vw)]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="company-demo-drawer-title"
        data-company-offer-drawer="demo"
    >
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5">
            <div>
                <h2 id="company-demo-drawer-title" class="text-xl font-bold text-gray-900">{{ __('talenma.company_offer.demo_title') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.company_offer.demo_subtitle') }}</p>
            </div>
            <button type="button" @click="close()" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="{{ __('talenma.company_offer.drawer_close') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6">
            @include('company.partials.demo-request-form')
        </div>
    </aside>

    {{-- Panneau : essai gratuit --}}
    <aside
        id="trial"
        x-show="drawer === 'trial'"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[70] flex h-screen w-full flex-col bg-white shadow-2xl md:w-[min(34rem,80vw)] lg:w-[min(36rem,52vw)] xl:w-[min(38rem,42vw)] 2xl:w-[min(40rem,34vw)]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="company-trial-drawer-title"
        data-company-offer-drawer="trial"
    >
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5">
            <div>
                <h2 id="company-trial-drawer-title" class="text-xl font-bold text-gray-900">{{ __('talenma.company_offer.trial_title') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.company_offer.trial_subtitle') }}</p>
            </div>
            <button type="button" @click="close()" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="{{ __('talenma.company_offer.drawer_close') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6">
            @include('company.partials.trial-request-form', [
                'professionSectors' => $professionSectors,
                'companyCountryOptions' => $companyCountryOptions,
            ])
        </div>
    </aside>
    @endif
</section>

@include('company.partials.platform-services')
@endsection
