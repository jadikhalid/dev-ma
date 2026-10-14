@extends('layouts.public')

@section('title', $company->name.' — '.__('talenma.meta.title'))
@section('meta_description', $metaDescription)
@section('main_class', 'bg-gray-50')

@section('og')
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $company->name }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ route('companies.public.show', $company) }}">
    @if ($profile->logoUrl())
        <meta property="og:image" content="{{ $profile->logoUrl() }}">
    @endif
    <meta name="twitter:card" content="summary">
@endsection

@section('content')
    @php
        $jobsCount = $jobs->count();
    @endphp

    <div x-data="companyPageTabs()">
    {{-- Bannière + identité --}}
    <section class="relative bg-white">
        <div @class([
            'relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500',
            'h-48 sm:h-[300px] lg:h-[450px]' => $profile->bannerUrl(),
            'h-40 sm:h-64' => ! $profile->bannerUrl(),
        ])>
            @if ($profile->bannerUrl())
                <img src="{{ $profile->bannerUrl() }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center" data-company-banner>
            @else
                <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:22px_22px]" aria-hidden="true"></div>
                <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-amber-400/30 blur-3xl" aria-hidden="true"></div>
                <div class="absolute -bottom-24 left-1/4 h-64 w-64 rounded-full bg-teal-300/25 blur-3xl" aria-hidden="true"></div>
            @endif

            <div class="absolute inset-x-0 bottom-0 hidden bg-gradient-to-t from-gray-950/90 from-0% via-gray-950/70 via-65% to-transparent to-100% pt-24 sm:block" data-company-identity-banner>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="pb-5 sm:ml-[180px] lg:ml-[224px] lg:pb-7">
                        @include('companies._identity', ['onBanner' => true])
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative pb-6 sm:pb-0">
                <div class="flex flex-col gap-4">
                    <div class="-mt-16 flex h-[130px] w-[130px] shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-3xl font-bold text-white shadow-lg ring-4 ring-white sm:absolute sm:-top-20 sm:z-40 sm:mt-0 sm:h-40 sm:w-40 lg:-top-[124px] lg:h-[200px] lg:w-[200px] lg:text-5xl" data-company-logo>
                        @if ($profile->logoUrl())
                            <img src="{{ $profile->logoUrl() }}" alt="{{ $company->name }}" class="h-full w-full bg-white object-cover">
                        @else
                            <span aria-hidden="true">{{ $profile->initials() }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 sm:hidden">
                        @include('companies._identity', ['onBanner' => false])
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Onglets --}}
    <div x-ref="tabsAnchor" aria-hidden="true"></div>
    <nav class="sticky top-20 z-30 border-y border-gray-100 bg-white/95 backdrop-blur sm:top-16">
        <div class="max-w-7xl mx-auto flex gap-6 px-4 sm:pl-[204px] sm:pr-6 lg:pl-[256px] lg:pr-8" role="tablist" aria-label="{{ $company->name }}">
            <button
                type="button"
                id="company-tab-about"
                role="tab"
                aria-controls="presentation"
                :aria-selected="(tab === 'about').toString()"
                :tabindex="tab === 'about' ? 0 : -1"
                @click="openTab('about')"
                @keydown.arrow-right.prevent="openTab('jobs'); $nextTick(() => document.getElementById('company-tab-jobs').focus())"
                :class="tab === 'about' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900'"
                class="border-b-2 py-3.5 text-sm font-semibold transition"
                data-company-tab="about"
            >
                {{ __('talenma.public_companies.tab_about') }}
            </button>
            <button
                type="button"
                id="company-tab-jobs"
                role="tab"
                aria-controls="offres"
                aria-selected="false"
                tabindex="-1"
                :aria-selected="(tab === 'jobs').toString()"
                :tabindex="tab === 'jobs' ? 0 : -1"
                @click="openTab('jobs')"
                @keydown.arrow-left.prevent="openTab('about'); $nextTick(() => document.getElementById('company-tab-about').focus())"
                :class="tab === 'jobs' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900'"
                class="inline-flex items-center gap-2 border-b-2 py-3.5 text-sm font-semibold transition"
                data-company-tab="jobs"
            >
                {{ __('talenma.public_companies.tab_jobs') }}
                <span
                    :class="tab === 'jobs' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700'"
                    class="inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-xs font-bold"
                >{{ $jobsCount }}</span>
            </button>
        </div>
    </nav>

    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            {{-- Présentation --}}
            <div
                id="presentation"
                role="tabpanel"
                aria-labelledby="company-tab-about"
                x-show="tab === 'about'"
                class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start"
                data-company-panel="about"
            >
                <div class="space-y-6">
                    <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.public_companies.about_title') }}</h2>
                        @if (filled($profile->description))
                            <div class="mt-4 whitespace-pre-line text-[15px] leading-relaxed text-gray-700">{{ trim(strip_tags($profile->description)) }}</div>
                        @else
                            <p class="mt-4 text-sm text-gray-500">{{ __('talenma.public_companies.about_empty') }}</p>
                        @endif
                    </article>

                    @if (filled($profile->hiring_needs))
                        <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.public_companies.hiring_title') }}</h2>
                            <div class="mt-4 whitespace-pre-line text-[15px] leading-relaxed text-gray-700">{{ trim(strip_tags($profile->hiring_needs)) }}</div>
                        </article>
                    @endif
                </div>

                <aside class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:sticky lg:top-36">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-indigo-600">{{ __('talenma.public_companies.facts_title') }}</h2>
                    <dl class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-1">
                        @if ($sector)
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs font-medium text-gray-500">{{ __('talenma.public_companies.fact_sector') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $sector }}</dd>
                            </div>
                        @endif
                        @if ($location !== '')
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs font-medium text-gray-500">{{ __('talenma.public_companies.fact_location') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $location }}</dd>
                            </div>
                        @endif
                        @if ($profile->employee_count)
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs font-medium text-gray-500">{{ __('talenma.public_companies.fact_size') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $profile->employee_count }}</dd>
                            </div>
                        @endif
                        <div class="rounded-xl bg-gray-50 px-4 py-3">
                            <dt class="text-xs font-medium text-gray-500">{{ __('talenma.public_companies.fact_jobs') }}</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $jobsCount }}</dd>
                        </div>
                        @if ($company->created_at)
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs font-medium text-gray-500">{{ __('talenma.public_companies.fact_member_since') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ ucfirst($company->created_at->translatedFormat('F Y')) }}</dd>
                            </div>
                        @endif
                    </dl>
                </aside>
            </div>

            {{-- Offres --}}
            <section
                id="offres"
                role="tabpanel"
                aria-labelledby="company-tab-jobs"
                x-show="tab === 'jobs'"
                x-cloak
                data-company-jobs
                data-company-panel="jobs"
            >
                @if ($jobsCount === 0)
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center text-sm text-gray-500">
                        {{ __('talenma.public_companies.jobs_empty') }}
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($jobs as $job)
                            <a href="{{ route('jobs.public.show', $job) }}" class="group flex flex-col rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-300 [@media(hover:hover)_and_(pointer:fine)]:hover:-translate-y-0.5 [@media(hover:hover)_and_(pointer:fine)]:hover:border-indigo-200 [@media(hover:hover)_and_(pointer:fine)]:hover:shadow-md">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-indigo-500/80">
                                    {{ ($job->published_at ?? $job->created_at)?->translatedFormat('d M Y') }}
                                </p>
                                <h3 class="mt-1.5 line-clamp-2 text-base font-bold text-gray-900 group-hover:text-indigo-700">{{ $job->title }}</h3>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-gray-600">
                                    @if ($job->contractTypeLabel() !== '')
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1">{{ $job->contractTypeLabel() }}</span>
                                    @endif
                                    @if ($job->locationLabel() !== '')
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1">{{ $job->locationLabel() }}</span>
                                    @endif
                                    @if ($job->workModesSummary() !== '')
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1">{{ $job->workModesSummary() }}</span>
                                    @endif
                                </div>
                                <span class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-indigo-600">
                                    {{ __('talenma.public_companies.view_job') }}
                                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
    </div>
@endsection
