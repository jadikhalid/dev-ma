@extends('layouts.public')

@section('title', __('talenma.public_companies.index_title').' — '.__('talenma.meta.title'))
@section('meta_description', __('talenma.public_companies.meta_index'))

@section('content')
    <section class="border-b border-indigo-100/80 bg-gradient-to-br from-indigo-50/90 via-white to-teal-50/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <a href="{{ route('home') }}#entreprises" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                {{ __('talenma.public_companies.back_home') }}
            </a>

            <p class="mt-6 text-[12px] sm:text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-600">
                {{ __('talenma.home.companies_marquee_eyebrow') }}
            </p>
            <div class="mt-1.5 flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/25">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">{{ __('talenma.public_companies.index_title') }}</h1>
            </div>
            <p class="mt-3 max-w-2xl text-gray-600">{{ __('talenma.public_companies.index_subtitle') }}</p>

            <form method="GET" action="{{ route('companies.public.index') }}" class="mt-6 flex max-w-xl flex-col gap-2 sm:flex-row" role="search">
                <label for="company-directory-search" class="sr-only">{{ __('talenma.public_companies.search_label') }}</label>
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input
                        id="company-directory-search"
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        maxlength="80"
                        placeholder="{{ __('talenma.public_companies.search_placeholder') }}"
                        class="block w-full rounded-xl border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                    >
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    {{ __('talenma.public_companies.search_button') }}
                </button>
                @if ($search !== '')
                    <a href="{{ route('companies.public.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900">
                        {{ __('talenma.public_companies.search_reset') }}
                    </a>
                @endif
            </form>
        </div>
    </section>

    <section class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            <p class="text-sm font-medium text-gray-500" data-company-directory-count>
                {{ trans_choice('talenma.public_companies.count', $companies->total(), ['count' => $companies->total()]) }}
            </p>

            @if ($companies->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                    <p class="text-base font-semibold text-gray-900">{{ __('talenma.public_companies.empty_title') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.public_companies.empty_text') }}</p>
                </div>
            @else
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($companies as $company)
                        <a
                            href="{{ $company['url'] }}"
                            class="group flex flex-col rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-300 [@media(hover:hover)_and_(pointer:fine)]:hover:-translate-y-0.5 [@media(hover:hover)_and_(pointer:fine)]:hover:border-indigo-200 [@media(hover:hover)_and_(pointer:fine)]:hover:shadow-md"
                            data-company-directory-card
                        >
                            <div class="flex items-start gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white">
                                    @if ($company['logo_url'])
                                        <img src="{{ $company['logo_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <span aria-hidden="true">{{ $company['initials'] }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="line-clamp-1 text-base font-bold text-gray-900 transition-colors group-hover:text-indigo-700">{{ $company['name'] }}</h2>
                                    @if ($company['sector'])
                                        <p class="mt-0.5 line-clamp-1 text-sm text-gray-500">{{ $company['sector'] }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($company['excerpt'])
                                <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-gray-600">{{ $company['excerpt'] }}</p>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-medium text-gray-600">
                                @if ($company['city'] || $company['country'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                        {{ collect([$company['city'], $company['country']])->filter()->implode(', ') }}
                                    </span>
                                @endif
                                @if ($company['employee_count'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                        {{ __('talenma.public_companies.employees', ['count' => $company['employee_count']]) }}
                                    </span>
                                @endif
                                @if ($company['open_jobs'] > 0)
                                    <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-1 text-teal-800 ring-1 ring-teal-200">
                                        {{ trans_choice('talenma.public_companies.open_jobs', $company['open_jobs'], ['count' => $company['open_jobs']]) }}
                                    </span>
                                @endif
                            </div>

                            <span class="mt-auto inline-flex items-center gap-1 pt-5 text-sm font-semibold text-indigo-600 group-hover:text-indigo-800">
                                {{ __('talenma.public_companies.view_company') }}
                                <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>

                @if ($companies->hasPages())
                    <div class="mt-10">{{ $companies->links() }}</div>
                @endif
            @endif
        </div>
    </section>
@endsection
