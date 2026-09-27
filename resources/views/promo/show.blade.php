@extends('layouts.public')

@section('title', $title.' — '.__('talenma.meta.title'))
@section('meta_description', $metaDescription)

@section('og')
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $shareUrl }}">
    @if ($imageUrl)
        <meta property="og:image" content="{{ $imageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $imageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if ($imageUrl)
        <meta name="twitter:image" content="{{ $imageUrl }}">
    @endif
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14" data-promo-app="{{ $app }}">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            {{ __('talenma.jobs.public_back_home') }}
        </a>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-4 lg:gap-5 lg:items-start">
            <article class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 min-w-0">
                <div class="flex flex-col sm:flex-row gap-5">
                    @if ($imageUrl)
                        <div class="shrink-0 sm:w-56">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $title }}"
                                class="w-full rounded-lg ring-1 ring-slate-200 {{ $imageFit === 'cover' ? 'object-cover' : 'object-contain bg-slate-50' }}"
                            >
                        </div>
                    @endif
                    <div class="min-w-0 flex-1 space-y-3">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                            {{ __('talenma.promo.badge', ['app' => $appLabel]) }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 leading-tight">{{ $title }}</h1>
                        @if ($subtitle !== '')
                            <p class="text-sm sm:text-base text-gray-600">{{ $subtitle }}</p>
                        @endif
                        @if ($description !== '')
                            <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $description }}</div>
                        @endif
                        <ul class="space-y-1.5 text-sm text-gray-700">
                            @foreach ($highlights as $highlight)
                                <li class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    <span>{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </article>

            <aside class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-4 min-w-0 lg:sticky lg:top-20">
                @if ($viewerIsPendingTalent)
                    <div class="space-y-3">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.promo.access_title') }}</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ __('talenma.promo.pending_talent') }}</p>
                    </div>
                @elseif ($viewerIsAuthenticated)
                    <div class="space-y-3" data-promo-talents-only>
                        <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.promo.talents_only_title') }}</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ __('talenma.promo.talents_only') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.promo.access_title') }}</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ __('talenma.promo.access_guest_hint') }}</p>
                        <a
                            href="{{ $gateUrl }}"
                            class="inline-flex w-full justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700"
                        >{{ __('talenma.promo.login_cta') }}</a>
                        <a
                            href="{{ $registerUrl }}"
                            class="inline-flex w-full justify-center px-4 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50"
                        >{{ __('talenma.promo.register_cta') }}</a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
