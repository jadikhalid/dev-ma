@extends('layouts.public')

@section('content')
<x-news-ticker />

<section class="relative overflow-hidden bg-white hero-section" aria-label="hero">

    <div class="home-hero-canvas">
    <div class="home-hero-wash" aria-hidden="true"></div>
    <div class="home-shell relative z-10 pt-6 pb-8 sm:pt-[7.25rem] lg:pt-[8.25rem] lg:pb-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
            <div class="max-w-xl">
                <p data-home-hero-badge class="hidden">
                    {{ __('talenma.home.badge') }}
                </p>

                <h1 class="text-[2.15rem] font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-[2.65rem] lg:text-[3.35rem] xl:text-6xl">
                    {{ __('talenma.home.hero_career_line') }}<br>
                    <span class="bg-gradient-to-r from-blue-600 to-violet-500 bg-clip-text text-transparent">{{ __('talenma.home.hero_career_accent') }}</span>
                </h1>

                <p class="hidden">
                    {!! __('talenma.home.hero_subtitle', [
                        'modes' => '<strong class="font-semibold text-gray-900">'.__('talenma.home.hero_subtitle_modes').'</strong>',
                    ]) !!}
                </p>

                <div class="my-8 flex justify-center lg:hidden">
                    <x-hero-freelancers-visual :talents-count="$talentsCount" :tiles="$heroTiles" />
                </div>

                <div class="mt-7 sm:mt-8">
                    <p class="hidden">{{ __('talenma.home.hero_search_caption') }}</p>
                    <form
                        action="{{ $offersSearchAction ?? route('career.offers') }}"
                        method="GET"
                        class="flex items-center gap-2 rounded-full border border-slate-200/80 bg-white p-1.5 shadow-[0_10px_30px_rgba(79,70,229,0.08)]"
                        x-data="homeOffersSearch(@js([
                            'empty' => __('talenma.home.search_validation_empty'),
                            'short' => __('talenma.home.search_validation_short'),
                            'long' => __('talenma.home.search_validation_long'),
                            'chars' => __('talenma.home.search_validation_chars'),
                        ]))"
                        @submit="onSubmit($event)"
                    >
                        <label for="hero-career-search" class="sr-only">{{ __('talenma.career_pages.offers_search_label') }}</label>
                        <div class="relative min-w-0 flex-1">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                            </svg>
                            <input
                                id="hero-career-search"
                                x-ref="query"
                                name="q"
                                type="search"
                                value="{{ request('q') }}"
                                placeholder="{{ __('talenma.home.hero_search_placeholder') }}"
                                class="w-full border-0 bg-transparent py-2.5 pl-10 pr-2 text-sm text-slate-900 outline-none ring-0 placeholder:text-slate-400 focus:ring-0"
                            >
                        </div>
                        <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-indigo-600 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:from-indigo-700 hover:to-violet-600">
                            {{ __('talenma.home.search_submit') }}
                        </button>
                    </form>
                </div>

                @guest
                    <div class="mt-5 flex flex-wrap items-center gap-3 sm:mt-6">
                        <p class="text-sm text-slate-500">{{ __('talenma.home.cta_new_visitor_question') }}</p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50">
                            {{ __('talenma.home.hero_create_account') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @endguest
            </div>

            <div class="hidden lg:flex justify-end lg:order-2">
                <x-hero-freelancers-visual :talents-count="$talentsCount" :tiles="$heroTiles" />
            </div>
        </div>
    </div>
    </div>

    <div class="home-shell relative z-10 pb-8 lg:pb-10">
        <x-home-recruiting-companies :companies="$recruitingCompanies ?? collect()" />

        <div id="opportunites" class="scroll-mt-24">
            <x-latest-jobs-marquee
                :jobs="$latestJobs ?? collect()"
                :index-url="$latestJobsIndexUrl ?? route('career.offers')"
            />
        </div>

        <x-home-career-tools />

        <x-home-steps />

        <x-home-talent-banner />

        <x-home-work-ways />
    </div>
</section>

<x-social-posts-slider :items="$socialPosts" />

@guest
<section class="pb-16 lg:pb-20">
    <div class="home-shell">
        <div class="home-cta-banner flex flex-col items-start justify-between gap-8 rounded-[1.75rem] px-6 py-8 sm:px-10 sm:py-10 lg:flex-row lg:items-center lg:px-12 lg:py-11">
            <h2 id="home-cta-title" class="max-w-xl text-3xl font-extrabold leading-[1.15] tracking-tight text-white sm:text-4xl lg:text-[2.65rem]">
                {{ __('talenma.home.cta_title') }}<br>
                {{ __('talenma.home.cta_title_rest') }}
                <span class="text-amber-300">{{ __('talenma.home.cta_title_accent') }}</span>
            </h2>
            <div class="flex w-full flex-col gap-3 sm:w-60">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-indigo-50">
                    {{ __('talenma.home.hero_create_account') }}
                </a>
                <a href="{{ route('career.offers') }}" class="inline-flex items-center justify-center rounded-full border border-white/80 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                    {{ __('talenma.home.cta_offers') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endguest

@endsection
