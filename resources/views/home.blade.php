@extends('layouts.public')

@section('content')
<x-magazine-ticker />

<section class="relative overflow-hidden bg-white hero-section" aria-label="hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 lg:pt-16 lg:pb-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
            {{-- Colonne texte + recherche --}}
            <div>
                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs sm:text-sm font-medium mb-4">
                    {{ __('talenma.home.badge') }}
                </p>

                <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-bold tracking-tight leading-[1.1] text-gray-900">
                    {!! __('talenma.home.hero_title', [
                        'talents' => '<span class="text-indigo-600">'.__('talenma.home.hero_talents').'</span>',
                        'emphasis' => '<strong class="font-bold">'.__('talenma.home.hero_emphasis').'</strong>',
                    ]) !!}
                </h1>

                <p class="mt-4 text-base sm:text-lg text-gray-600 leading-relaxed max-w-xl">
                    {{ __('talenma.home.hero_subtitle') }}
                </p>

                {{-- Panneau recherche style Malt --}}
                <div class="mt-7 hero-search-panel rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="flex items-center gap-2 px-3 py-2.5 border-b border-gray-100 bg-gray-50/80">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800">{{ __('talenma.home.search_tab') }}</span>
                    </div>

                    <form method="GET" action="{{ route('company.search') }}" class="p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row gap-2.5">
                            <div class="flex-1 min-w-0">
                                <label for="hero-keyword" class="sr-only">{{ __('talenma.home.search_tab') }}</label>
                                <input
                                    id="hero-keyword"
                                    type="text"
                                    name="keyword"
                                    value="{{ request('keyword') }}"
                                    placeholder="{{ __('talenma.home.search_placeholder') }}"
                                    maxlength="128"
                                    class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3"
                                >
                            </div>
                            <div class="sm:w-52 shrink-0 relative">
                                <label for="hero-city" class="sr-only">{{ __('talenma.home.search_location') }}</label>
                                <select
                                    id="hero-city"
                                    name="city"
                                    class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 pl-3 pr-10 appearance-none bg-white"
                                >
                                    <option value="">{{ __('talenma.home.search_location') }}</option>
                                    @foreach (['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'] as $city)
                                        <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shrink-0"
                            >
                                {{ __('talenma.home.search_submit') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                @if ($talentsCount > 0)
                    <p class="mt-3 text-xs sm:text-sm text-gray-500">{{ __('talenma.home.talent_count', ['count' => $talentsCount]) }}</p>
                @endif

                @guest
                    <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50/70 p-4 sm:p-5">
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ __('talenma.home.cta_new_visitor_question') }}</p>
                        <p class="mt-1.5 text-sm text-gray-600 leading-relaxed">{{ __('talenma.home.cta_new_visitor_hint') }}</p>
                        <div class="mt-4 flex flex-col sm:flex-row gap-2.5">
                            <a href="{{ route('register', ['role' => 'dev']) }}" class="inline-flex justify-center px-5 py-2.5 bg-white border border-gray-200 text-gray-800 text-sm font-semibold rounded-xl hover:border-indigo-300 hover:text-indigo-700 transition">
                                {{ __('talenma.home.cta_talent') }}
                            </a>
                            <a href="{{ route('register', ['role' => 'company']) }}" class="inline-flex justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                                {{ __('talenma.home.cta_company') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mt-5">
                        <a href="{{ route('dashboard') }}" class="inline-flex justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                            {{ __('talenma.nav.my_space') }} →
                        </a>
                    </div>
                @endguest
            </div>

            {{-- Visuel droit — mosaïque bento (masqué sur petits écrans) --}}
            <div class="hidden lg:flex justify-end lg:order-2">
                <x-hero-freelancers-visual :talents-count="$talentsCount" />
            </div>
        </div>

        {{-- Bandeau réassurance style Malt --}}
        <div class="mt-8 lg:mt-10 pt-6 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-4 sm:gap-8 lg:gap-12 text-xs sm:text-sm font-medium text-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('talenma.home.reinsurance_remote') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('talenma.home.reinsurance_verified') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('talenma.home.reinsurance_free') }}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center">{{ __('talenma.home.modes_title') }}</h2>
        <p class="text-center text-gray-600 mt-3 max-w-2xl mx-auto">{{ __('talenma.home.modes_subtitle') }}</p>
        <div class="mt-14 grid md:grid-cols-3 gap-8">
            <div class="p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <span class="text-3xl">🎯</span>
                <h3 class="mt-4 text-xl font-bold">{{ __('talenma.home.mode_direct_title') }}</h3>
                <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ __('talenma.home.mode_direct_desc') }}</p>
            </div>
            <div class="p-8 rounded-2xl border-2 border-indigo-200 bg-indigo-50/50 shadow-sm">
                <span class="text-3xl">🤝</span>
                <h3 class="mt-4 text-xl font-bold">{{ __('talenma.home.mode_intermediary_title') }}</h3>
                <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ __('talenma.home.mode_intermediary_desc') }}</p>
            </div>
            <div class="p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <span class="text-3xl">🏢</span>
                <h3 class="mt-4 text-xl font-bold">{{ __('talenma.home.mode_morocco_title') }}</h3>
                <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ __('talenma.home.mode_morocco_desc') }}</p>
                <a href="{{ route('services.index') }}" class="mt-4 inline-block text-indigo-600 font-semibold text-sm hover:text-indigo-800">{{ __('talenma.home.mode_morocco_link') }}</a>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-2xl border shadow-sm">
            <h3 class="text-xl font-bold">{{ __('talenma.home.talent_card_title') }}</h3>
            <p class="mt-3 text-gray-600">{!! __('talenma.home.talent_card_desc', ['price' => '<strong>'.__('talenma.common.price').'</strong>']) !!}</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-700">
                <li>✓ {{ __('talenma.home.talent_card_1') }}</li>
                <li>✓ {{ __('talenma.home.talent_card_2') }}</li>
                <li>✓ {{ __('talenma.home.talent_card_3') }}</li>
            </ul>
        </div>
        <div class="bg-white p-8 rounded-2xl border shadow-sm">
            <h3 class="text-xl font-bold">{{ __('talenma.home.company_card_title') }}</h3>
            <p class="mt-3 text-gray-600">{!! __('talenma.home.company_card_desc', ['free' => '<strong>'.__('talenma.home.free').'</strong>']) !!}</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-700">
                <li>✓ {{ __('talenma.home.company_card_1') }}</li>
                <li>✓ {{ __('talenma.home.company_card_2') }}</li>
                <li>✓ {{ __('talenma.home.company_card_3') }}</li>
            </ul>
        </div>
    </div>
</section>

@if ($latestArticles->isNotEmpty())
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold">{{ __('talenma.home.magazine_title') }}</h2>
                <p class="mt-2 text-gray-600">{{ __('talenma.home.magazine_subtitle') }}</p>
            </div>
            <a href="{{ route('magazine.index') }}" class="text-indigo-600 font-semibold hover:text-indigo-800">{{ __('talenma.home.magazine_all') }}</a>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($latestArticles as $article)
                <a href="{{ route('magazine.show', $article->slug) }}" class="group bg-white rounded-2xl border p-6 hover:shadow-md transition">
                    <span class="text-3xl">{{ $article->cover_emoji ?? '📰' }}</span>
                    <p class="mt-3 text-xs font-medium text-indigo-600 uppercase">{{ __('talenma.magazine.categories.'.$article->category) }}</p>
                    <h3 class="mt-1 font-bold group-hover:text-indigo-600 transition">{{ $article->localized_title }}</h3>
                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $article->localized_excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-20 bg-indigo-600 text-white">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold">{{ __('talenma.home.cta_title') }}</h2>
        <p class="mt-4 text-indigo-100">{{ __('talenma.home.cta_subtitle') }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-3.5 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50">{{ __('talenma.home.cta_register') }}</a>
            <a href="{{ route('magazine.index') }}" class="px-8 py-3.5 border border-white/30 font-semibold rounded-xl hover:bg-white/10">{{ __('talenma.home.cta_magazine') }}</a>
        </div>
    </div>
</section>
@endsection
