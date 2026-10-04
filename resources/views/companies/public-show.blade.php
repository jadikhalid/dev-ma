@extends('layouts.public')

@section('title', $company->name.' — '.__('talenma.meta.title'))
@section('meta_description', $metaDescription)

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
        <div class="relative h-40 overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500 sm:h-56" aria-hidden="true">
            <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:22px_22px]"></div>
            <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-amber-400/30 blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/4 h-64 w-64 rounded-full bg-teal-300/25 blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative flex flex-col gap-5 pb-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
                    <div class="-mt-12 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-2xl font-bold text-white shadow-lg ring-4 ring-white sm:-mt-14 sm:h-28 sm:w-28">
                        @if ($profile->logoUrl())
                            <img src="{{ $profile->logoUrl() }}" alt="{{ $company->name }}" class="h-full w-full bg-white object-cover">
                        @else
                            <span aria-hidden="true">{{ $profile->initials() }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 sm:pt-5">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ $company->name }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600">
                            @if ($sector)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-medium text-indigo-700 ring-1 ring-indigo-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                                    {{ $sector }}
                                </span>
                            @endif
                            @if ($location !== '')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                    {{ $location }}
                                </span>
                            @endif
                            @if ($profile->employee_count)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                    {{ __('talenma.public_companies.employees', ['count' => $profile->employee_count]) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap items-center gap-2 sm:pt-5">
                    @if ($websiteUrl)
                        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer nofollow" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:border-indigo-300 hover:text-indigo-700" data-company-website>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                            {{ __('talenma.public_companies.website') }}
                        </a>
                    @endif
                    @if ($jobsCount > 0)
                        <a href="#offres" @click.prevent="openTab('jobs', true)" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            {{ trans_choice('talenma.public_companies.open_jobs', $jobsCount, ['count' => $jobsCount]) }}
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Onglets --}}
    <div x-ref="tabsAnchor" aria-hidden="true"></div>
    <nav class="sticky top-20 z-30 border-y border-gray-100 bg-white/95 backdrop-blur sm:top-16">
        <div class="max-w-7xl mx-auto flex gap-6 px-4 sm:px-6 lg:px-8" role="tablist" aria-label="{{ $company->name }}">
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
            <a href="{{ route('companies.public.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                {{ __('talenma.public_companies.back_index') }}
            </a>

            {{-- Présentation --}}
            <div
                id="presentation"
                role="tabpanel"
                aria-labelledby="company-tab-about"
                x-show="tab === 'about'"
                class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start"
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
                class="mt-6"
                data-company-jobs
                data-company-panel="jobs"
            >
                <h2 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                    {{ __('talenma.public_companies.jobs_title') }}
                    <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-indigo-100 px-2 text-xs font-bold text-indigo-700">{{ $jobsCount }}</span>
                </h2>

                @if ($jobsCount === 0)
                    <div class="mt-5 rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center text-sm text-gray-500">
                        {{ __('talenma.public_companies.jobs_empty') }}
                    </div>
                @else
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
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
