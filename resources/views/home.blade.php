@extends('layouts.public')

@section('content')
<x-news-ticker />

<section class="relative overflow-hidden bg-white hero-section" aria-label="hero">
    <div class="home-hero-wide-atmosphere" aria-hidden="true"></div>

    {{-- Petit écran : header (5rem) + panneau marque = 100svh au chargement --}}
    <div class="sm:hidden relative isolate flex h-[calc(100svh-5rem)] min-h-[calc(100svh-5rem)] flex-col justify-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-600 via-indigo-600 to-teal-600" aria-hidden="true"></div>
        <div class="absolute inset-x-0 top-0 h-28 bg-gradient-to-b from-indigo-500/40 to-transparent" aria-hidden="true"></div>
        <div class="absolute -right-16 -top-10 h-56 w-56 rounded-full bg-amber-300/20 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-teal-200/25 blur-3xl" aria-hidden="true"></div>
        <div class="absolute inset-0 opacity-[0.08]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 22px 22px;" aria-hidden="true"></div>

        <div class="relative z-10 flex flex-1 flex-col justify-center gap-6 px-6 py-10">
            <div class="flex flex-col gap-3.5">
                {{-- Relais du nom de marque quand le header le masque (< 420px) --}}
                <p class="hidden max-w-420-block text-[2.25rem] font-bold leading-[1.05] tracking-tight text-white">
                    Talents du <span class="text-amber-200">Maroc</span>
                </p>

                <p class="inline-flex w-fit max-w-full items-center rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-[13px] font-semibold leading-snug tracking-[0.04em] text-white/90 backdrop-blur-sm">
                    {{ __('talenma.home.badge') }}
                </p>
            </div>

            <h1 class="max-w-[20.5rem] text-[2.4rem] font-extrabold leading-[1.2] tracking-[-0.02em] text-white min-w-480-hero-title">
                {!! __('talenma.home.hero_title', [
                    'talents' => '<span class="text-amber-200">'.__('talenma.home.hero_talents').'</span>',
                    'emphasis' => '<strong class="font-extrabold text-white underline decoration-amber-200/60 decoration-2 underline-offset-[6px]">'.__('talenma.home.hero_emphasis').'</strong>',
                ]) !!}
            </h1>
        </div>
    </div>

    <div class="home-shell relative z-10 pt-6 pb-8 sm:pt-12 lg:pt-16 lg:pb-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
            {{-- Colonne texte --}}
            <div class="max-w-xl">
                <p data-home-hero-badge class="mb-4 hidden items-center gap-2 rounded-full bg-indigo-100 px-3.5 py-1.5 text-sm font-semibold text-indigo-800 ring-1 ring-indigo-200/70 sm:inline-flex">
                    {{ __('talenma.home.badge') }}
                </p>

                <h1 class="hidden text-[2.65rem] font-extrabold leading-[1.05] tracking-tight text-slate-900 sm:block lg:text-6xl">
                    {{ __('talenma.home.hero_career_line') }}<br>
                    <span class="bg-gradient-to-r from-blue-600 to-violet-500 bg-clip-text text-transparent">{{ __('talenma.home.hero_career_accent') }}</span>
                </h1>

                <p class="mt-4 hidden max-w-xl text-base leading-relaxed text-gray-600 sm:text-lg lg:block">
                    {!! __('talenma.home.hero_subtitle', [
                        'modes' => '<strong class="font-semibold text-gray-900">'.__('talenma.home.hero_subtitle_modes').'</strong>',
                    ]) !!}
                </p>

                <div class="mt-6 sm:mt-8">
                    <p class="mb-2 text-xs font-medium tracking-wide text-slate-400">{{ __('talenma.home.hero_search_caption') }}</p>
                    <form action="{{ route('career.offers') }}" method="GET" class="flex items-center gap-2 rounded-[1.35rem] bg-white p-2 shadow-[0_16px_40px_rgba(79,70,229,0.12)] ring-1 ring-slate-200/80">
                        <label for="hero-career-search" class="sr-only">{{ __('talenma.home.hero_search_label') }}</label>
                        <svg class="ml-2 h-5 w-5 shrink-0 text-indigo-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                            <circle cx="9" cy="9" r="5.25" stroke-width="1.75"/>
                            <path d="M13.2 13.2 17 17" stroke-width="1.75" stroke-linecap="round"/>
                        </svg>
                        <input
                            id="hero-career-search"
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="{{ __('talenma.home.hero_search_placeholder') }}"
                            class="min-w-0 flex-1 border-0 bg-transparent py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0"
                        >
                        <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-violet-500 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-500/25 transition hover:from-blue-700 hover:to-violet-600">
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

            {{-- Visuel droit — mosaïque bento (masqué sur petits écrans) --}}
            <div class="hidden lg:flex justify-end lg:order-2">
                <x-hero-freelancers-visual :talents-count="$talentsCount" :tiles="$heroTiles" />
            </div>
        </div>

        <x-home-steps />

        <div id="opportunites" class="scroll-mt-24">
            <x-latest-jobs-marquee
                :jobs="$latestJobs ?? collect()"
                :index-url="$latestJobsIndexUrl ?? route('jobs.gate')"
            />
        </div>

        <x-home-work-ways />
    </div>
</section>

<x-companies-marquee :companies="$featuredCompanies" />

<x-social-posts-slider :items="$socialPosts" />

@guest
<section class="py-20 bg-indigo-600 text-white">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold">{{ __('talenma.home.cta_title') }}</h2>
        <p class="mt-4 text-indigo-100">{{ __('talenma.home.cta_subtitle') }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register', ['role' => 'dev']) }}" class="px-8 py-3.5 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50">{{ __('talenma.home.cta_talent') }}</a>
            <a href="{{ route('company.offer') }}" target="_blank" rel="noopener noreferrer" class="px-8 py-3.5 border border-white/30 font-semibold rounded-xl hover:bg-white/10">{{ __('talenma.home.cta_company') }}</a>
        </div>
    </div>
</section>
@endguest

@endsection
