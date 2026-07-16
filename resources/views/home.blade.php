@extends('layouts.public')

@section('content')
<x-news-ticker />

<section class="relative overflow-hidden bg-white hero-section" aria-label="hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 lg:pt-16 lg:pb-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
            {{-- Colonne texte --}}
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

                <p class="hidden lg:block mt-4 text-base sm:text-lg text-gray-600 leading-relaxed max-w-xl">
                    {!! __('talenma.home.hero_subtitle', [
                        'modes' => '<strong class="font-semibold text-gray-900">'.__('talenma.home.hero_subtitle_modes').'</strong>',
                    ]) !!}
                </p>

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

        {{-- Panneau recherche pleine largeur --}}
        <div class="mt-8 lg:mt-10 w-full hero-search-panel rounded-xl border border-gray-100 bg-white overflow-visible">
            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-gray-100 bg-gray-50/80">
                <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-800">
                    {{ ($showCompanySearch ?? false) ? __('talenma.home.company_search_tab') : __('talenma.home.search_tab') }}
                </span>
            </div>

            @if ($showCompanySearch ?? false)
                <x-hero-company-search
                    :sectors="$professionSectors"
                    :countries="$companyCountries ?? []"
                />
            @else
                <x-hero-progressive-search
                    :sectors="$professionSectors"
                    :keyword="request('keyword', '')"
                    :sector="request('sector', '')"
                    :profession="request('profession', '')"
                    :can-view-profiles="$canViewProfiles ?? false"
                />
            @endif
        </div>

        @if ($talentsCount > 0)
            <p class="mt-3 text-xs sm:text-sm text-gray-500">{{ __('talenma.home.talent_count', ['count' => $talentsCount]) }}</p>
        @endif

        {{-- Bandeau réassurance style Malt --}}
        <div class="mt-8 lg:mt-10 pt-6 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-4 sm:gap-8 lg:gap-12 text-xs sm:text-sm font-medium text-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                    {{ __('talenma.home.reinsurance_remote') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('talenma.home.reinsurance_verified') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
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
            <p class="mt-3 text-gray-600">{{ __('talenma.home.talent_card_desc') }}</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-700">
                <li>✓ {{ __('talenma.home.talent_card_1') }}</li>
                <li>✓ {{ __('talenma.home.talent_card_2') }}</li>
                <li>✓ {{ __('talenma.home.talent_card_3') }}</li>
            </ul>
        </div>
        <div class="bg-white p-8 rounded-2xl border shadow-sm">
            <h3 class="text-xl font-bold">{{ __('talenma.home.company_card_title') }}</h3>
            <p class="mt-3 text-gray-600">{{ __('talenma.home.company_card_desc') }}</p>
            <ul class="mt-4 space-y-2 text-sm text-gray-700">
                <li>✓ {{ __('talenma.home.company_card_1') }}</li>
                <li>✓ {{ __('talenma.home.company_card_2') }}</li>
                <li>✓ {{ __('talenma.home.company_card_3') }}</li>
            </ul>
        </div>
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
            <a href="{{ route('register', ['role' => 'company']) }}" class="px-8 py-3.5 border border-white/30 font-semibold rounded-xl hover:bg-white/10">{{ __('talenma.home.cta_company') }}</a>
        </div>
    </div>
</section>
@endguest
@endsection
