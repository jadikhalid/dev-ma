@extends('layouts.public')

@section('title', $job->title.' — '.__('talenma.meta.title'))
@section('meta_description', $metaDescription)

@section('og')
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $job->title }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ route('jobs.public.show', $job) }}">
    @if ($job->advertiserLogoUrl())
        <meta property="og:image" content="{{ $job->advertiserLogoUrl() }}">
    @endif
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $job->title }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            {{ __('talenma.jobs.public_back_home') }}
        </a>

        <header class="mt-6">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 leading-tight">
                {{ $job->title }}
            </h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600">
                <span class="inline-flex items-center gap-2">
                    @if ($job->advertiserLogoUrl())
                        <img src="{{ $job->advertiserLogoUrl() }}" alt="" class="h-7 w-7 rounded object-cover ring-1 ring-slate-200">
                    @endif
                    <span class="font-medium text-gray-800">{{ $job->advertiserName() }}</span>
                </span>
                @if ($job->isExternalApplication())
                    <span class="inline-flex ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-amber-50 text-amber-800 align-middle">{{ __('talenma.jobs.external_badge') }}</span>
                @endif
                @if ($job->professionSummary() !== '')
                    · {{ $job->professionSummary() }}
                @endif
                @if ($job->locationLabel() !== '')
                    · {{ $job->locationLabel() }}
                @endif
                @if ($job->workModesSummary() !== '')
                    · {{ $job->workModesSummary() }}
                @endif
            </p>
        </header>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-4 lg:gap-5 lg:items-start">
            <article class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-3 min-w-0">
                <p class="text-sm text-gray-500">
                    {{ $job->contractTypeLabel() }}
                    @if ($job->professionSummary() !== '')
                        · {{ $job->professionSummary() }}
                    @endif
                    @if ($job->experienceLabel() !== '')
                        · {{ $job->experienceLabel() }}
                    @endif
                    @if ($job->workModesSummary() !== '')
                        · {{ $job->workModesSummary() }}
                    @endif
                </p>
                <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $job->description }}</div>
            </article>

            <aside class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-4 min-w-0 lg:sticky lg:top-20">
                @if ($viewerIsAuthenticated)
                    <div class="space-y-3">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.public_apply_title') }}</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ __('talenma.jobs.public_apply_authenticated_hint') }}</p>
                        <a
                            href="{{ $authContinueUrl }}"
                            class="inline-flex w-full justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700"
                        >{{ __('talenma.jobs.public_continue_cta') }}</a>
                    </div>
                @else
                    <div class="space-y-3">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.public_apply_title') }}</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ __('talenma.jobs.public_apply_guest_hint') }}</p>
                        <a
                            href="{{ $authContinueUrl }}"
                            class="inline-flex w-full justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700"
                        >{{ __('talenma.jobs.public_login_cta') }}</a>
                        <a
                            href="{{ $registerUrl }}"
                            class="inline-flex w-full justify-center px-4 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50"
                        >{{ __('talenma.jobs.public_register_cta') }}</a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
