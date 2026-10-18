@extends('layouts.public')

@section('title', __('talenma.career_pages.offers_meta_title'))
@section('meta_description', __('talenma.career_pages.offers_meta_description'))

@section('content')
<section
    class="bg-white pb-16 pt-10 sm:pb-20 sm:pt-12"
    data-career-offers
    x-data="careerOffers({ url: @js(route('career.offers')), query: @js($search) })"
>
    <div class="home-shell">
        <div class="rounded-[1.75rem] bg-gradient-to-br from-indigo-50 via-white to-violet-50 px-6 py-7 ring-1 ring-indigo-100 sm:px-8 sm:py-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-600">{{ __('talenma.nav.offers_link') }}</p>
            <h1 class="mt-2 max-w-2xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('talenma.career_pages.offers_title') }}</h1>
            <p class="mt-3 max-w-xl text-base leading-relaxed text-slate-600">{{ __('talenma.career_pages.offers_intro') }}</p>

        <form method="GET" action="{{ route('career.offers') }}" class="mt-6" role="search" @submit.prevent="search(1)">
            <label for="offers-search" class="sr-only">{{ __('talenma.career_pages.offers_search_label') }}</label>
            <div class="relative max-w-xl">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input
                    id="offers-search"
                    name="q"
                    type="search"
                    x-model="q"
                    @input="onInput()"
                    placeholder="{{ __('talenma.career_pages.offers_search_placeholder') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100"
                >
            </div>
        </form>
        </div>

        <div class="mt-8" x-ref="results" @click="onPageClick($event)" :class="loading ? 'opacity-60' : ''">
            @include('career._offers-results')
        </div>
    </div>
</section>
@endsection
