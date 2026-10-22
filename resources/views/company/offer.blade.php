@extends('layouts.public')

@section('title', __('talenma.company_offer.meta_title'))
@section('meta_description', __('talenma.company_offer.meta_description'))

@section('content')
@php
    $showOfferForms = ! auth()->user()?->isCompany();

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
        init() {
            const leave = () => {
                if (window.location.hash === '#trial') {
                    window.location.replace(@js(route('company.trial')));
                    return;
                }
                if (window.location.hash === '#demo') {
                    window.location.replace(@js(route('company.demo')));
                }
            };
            leave();
            window.addEventListener('hashchange', leave);
        },
    }"
    @endif
    data-company-offer-hero
>
    <div class="home-shell relative">
    <div class="grid items-center pb-12 pt-8 sm:gap-12 sm:pt-[4.5rem] lg:grid-cols-2 lg:gap-16 lg:px-10 lg:pb-16 lg:pt-[6.25rem] xl:px-16 xl:pt-[6.75rem] 2xl:pt-[7.25rem]">
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
                <a
                    href="{{ route('company.demo') }}"
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 bg-gray-950 px-6 py-2.5 text-sm font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800"
                >{{ __('talenma.company_offer.hero_cta_demo') }}</a>
                <a
                    href="{{ route('company.trial') }}"
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 px-6 py-2.5 text-sm font-semibold text-gray-950 transition hover:bg-gray-950 hover:text-white"
                >{{ __('talenma.company_offer.hero_cta_trial') }}</a>
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

</section>

@include('company.partials.platform-services')
@endsection
