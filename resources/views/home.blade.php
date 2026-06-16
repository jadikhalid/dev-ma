@extends('layouts.public')

@section('content')
<x-magazine-ticker />

<section class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-emerald-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-28 lg:pb-32">
        <div class="max-w-5xl">
            <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium mb-6">{{ __('talenma.home.badge') }}</p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight">
                {!! __('talenma.home.hero_title', [
                    'talents' => '<span class="text-indigo-600">'.__('talenma.home.hero_talents').'</span>',
                ]) !!}
            </h1>
            <p class="mt-6 text-lg text-gray-600 max-w-2xl leading-relaxed">{{ __('talenma.home.hero_subtitle') }}</p>
            <div class="mt-10 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('register') }}" class="inline-flex justify-center px-6 py-3.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200">{{ __('talenma.home.cta_talent') }}</a>
                <a href="{{ route('register') }}" class="inline-flex justify-center px-6 py-3.5 bg-white border-2 border-indigo-200 text-indigo-700 font-semibold rounded-xl hover:border-indigo-400">{{ __('talenma.home.cta_company') }}</a>
            </div>
            @if ($talentsCount > 0)
                <p class="mt-6 text-sm text-gray-500">{{ __('talenma.home.talent_count', ['count' => $talentsCount]) }}</p>
            @endif
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
