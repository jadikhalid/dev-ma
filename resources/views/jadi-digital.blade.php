@extends('layouts.public')

@section('title', __('talenma.jadi_page.meta_title'))
@section('meta_description', __('talenma.jadi_page.meta_description'))

@section('og')
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('talenma.jadi_page.meta_title') }}">
    <meta property="og:description" content="{{ __('talenma.jadi_page.meta_description') }}">
    <meta property="og:url" content="{{ route('jadi-digital') }}">
@endsection

@php
    $contactUrl = 'mailto:khalid@jadi-digital.com';
    $offerIcons = [
        'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z',
        'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z',
        'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
        'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085',
        'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
    ];
@endphp

@section('content')
<div data-jadi-page>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gray-950 text-white">
        <div class="absolute inset-0 opacity-25 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:24px_24px]" aria-hidden="true"></div>
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-amber-500/25 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-32 left-1/4 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto grid gap-12 px-4 py-20 sm:px-6 sm:py-28 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center lg:gap-16 lg:px-8">
            <div class="relative flex h-48 w-48 items-center justify-center sm:h-64 sm:w-64 lg:order-last lg:h-[26rem] lg:w-[26rem]" data-jadi-logo>
                <div class="absolute inset-[22%] rounded-full bg-teal-400/20 blur-3xl" aria-hidden="true"></div>
                <div class="absolute inset-0 rounded-full border border-white/15" aria-hidden="true"></div>
                <div class="absolute inset-[14%] rounded-full border border-white/10" aria-hidden="true"></div>
                <div class="absolute inset-[28%] rounded-full border border-dashed border-teal-300/35" aria-hidden="true"></div>
                <span class="absolute left-[14.6%] top-[14.6%] h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-teal-300 shadow-[0_0_12px_theme(colors.teal.300)]" aria-hidden="true"></span>
                <span class="absolute bottom-[14%] right-[50%] h-1.5 w-1.5 translate-x-1/2 translate-y-1/2 rounded-full bg-amber-300 shadow-[0_0_10px_theme(colors.amber.300)]" aria-hidden="true"></span>
                <span class="absolute right-0 top-1/2 h-1.5 w-1.5 -translate-y-1/2 translate-x-1/2 rounded-full bg-indigo-300" aria-hidden="true"></span>
                <img
                    src="{{ asset('images/brand/jadi-digital-mark.png') }}"
                    alt="JADI DIGITAL"
                    width="560"
                    height="381"
                    class="relative w-[46%] drop-shadow-[0_8px_30px_rgba(45,212,191,0.25)]"
                >
            </div>
            <div>
                <p class="flex items-center gap-3 text-2xl tracking-[0.28em] sm:text-3xl" data-jadi-brand>
                    <span><span class="font-extrabold text-white">JADI</span> <span class="font-light text-teal-300">DIGITAL</span></span>
                    <span class="h-2.5 w-2.5 shrink-0 bg-teal-400 shadow-[0_0_14px_theme(colors.teal.400)]" aria-hidden="true"></span>
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold uppercase tracking-[0.2em]">
                    <span class="text-gray-400">{{ __('talenma.jadi_page.eyebrow') }}</span>
                    <span class="h-px w-10 bg-gradient-to-r from-teal-400/80 to-transparent" aria-hidden="true"></span>
                    <span class="inline-flex flex-wrap items-center gap-x-2.5 gap-y-1 text-white/90">
                        <svg class="h-3.5 w-3.5 text-teal-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        @foreach (__('talenma.jadi_page.cities') as $city)
                            @if (! $loop->first)
                                <span class="h-1 w-1 rounded-full bg-teal-400/70" aria-hidden="true"></span>
                            @endif
                            <span>{{ $city }}</span>
                        @endforeach
                    </span>
                </div>
                <h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">{{ __('talenma.jadi_page.title') }}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-gray-300">{{ __('talenma.jadi_page.subtitle') }}</p>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $contactUrl }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-teal-50" data-jadi-contact>
                        {{ __('talenma.jadi_page.cta_primary') }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="#offres" class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/10">{{ __('talenma.jadi_page.cta_secondary') }}</a>
                </div>            </div>
        </div>
    </section>

    {{-- Fondateur --}}
    <section class="bg-gray-50 py-20 sm:py-24" data-jadi-founder>
        <div class="max-w-7xl mx-auto grid gap-12 px-4 sm:px-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:items-start lg:gap-16 lg:px-8">
            <div class="relative mx-auto w-full max-w-sm lg:sticky lg:top-28 lg:max-w-[488px]">
                <div class="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-teal-300/40 via-indigo-300/30 to-amber-200/40 blur-xl" aria-hidden="true"></div>
                <img
                    src="{{ asset('images/brand/jadi-founder.jpg') }}"
                    alt="{{ __('talenma.jadi_page.founder_photo_alt') }}"
                    width="488"
                    height="610"
                    loading="lazy"
                    class="relative aspect-[4/5] w-full rounded-3xl object-cover shadow-xl ring-1 ring-gray-900/5"
                >
                <div class="absolute -bottom-5 left-6 right-6 rounded-2xl bg-white/95 px-5 py-4 shadow-lg ring-1 ring-gray-900/5 backdrop-blur">
                    <p class="text-base font-bold text-gray-900">{{ __('talenma.jadi_page.founder_name') }}</p>
                    <p class="text-sm text-indigo-600">{{ __('talenma.jadi_page.founder_role') }}</p>
                </div>
            </div>

            <div class="pt-4 lg:pt-0">
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">{{ __('talenma.jadi_page.founder_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.jadi_page.founder_name') }}</h2>
                <div class="mt-5 space-y-4 text-gray-600">
                    @foreach (__('talenma.jadi_page.founder_summary') as $paragraph)
                        <p class="leading-relaxed">{{ $paragraph }}</p>
                    @endforeach
                </div>

                <dl class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach (__('talenma.jadi_page.founder_stats') as $stat)
                        <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-100">
                            <dt class="sr-only">{{ $stat['label'] }}</dt>
                            <dd>
                                <span class="block text-2xl font-bold text-indigo-600">{{ $stat['value'] }}</span>
                                <span class="mt-1 block text-sm text-gray-600">{{ $stat['label'] }}</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('talenma.jadi_page.founder_skills_title') }}</h3>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach (__('talenma.jadi_page.founder_skills') as $skill)
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            <span>{{ $skill }}</span>
                        </li>
                    @endforeach
                </ul>

                <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('talenma.jadi_page.founder_career_title') }}</h3>
                <ol class="mt-4 border-l-2 border-indigo-100">
                    @foreach (__('talenma.jadi_page.founder_career') as $step)
                        <li class="relative pb-5 pl-6 last:pb-0">
                            <span @class([
                                'absolute -left-[7px] top-1.5 h-3 w-3 rounded-full ring-4 ring-gray-50',
                                'bg-teal-400' => $loop->first,
                                'bg-indigo-300' => ! $loop->first,
                            ]) aria-hidden="true"></span>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $step['period'] }}</p>
                            <p class="mt-0.5 text-sm font-semibold text-gray-900">{{ $step['role'] }}</p>
                            <p class="text-sm text-gray-600">{{ $step['company'] }}</p>
                            @if (! empty($step['summary']))
                                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ $step['summary'] }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>

                <p class="mt-8 text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">{{ __('talenma.jadi_page.founder_languages_label') }}</span>
                    {{ __('talenma.jadi_page.founder_languages') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Offres --}}
    <section id="offres" class="scroll-mt-24 bg-white py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">{{ __('talenma.jadi_page.offers_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.jadi_page.offers_title') }}</h2>
                <p class="mt-4 text-gray-600">{{ __('talenma.jadi_page.offers_intro') }}</p>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach (__('talenma.jadi_page.offers') as $i => $offer)
                    <article class="flex flex-col rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md" data-jadi-offer>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $offerIcons[$i] ?? $offerIcons[0] }}"/></svg>
                        </span>
                        <h3 class="mt-5 text-lg font-bold text-gray-900">{{ $offer['title'] }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">{{ $offer['desc'] }}</p>
                        <dl class="mt-5 space-y-2 border-t border-gray-100 pt-4 text-sm">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.jadi_page.offer_for') }}</dt>
                                <dd class="mt-0.5 text-gray-700">{{ $offer['for'] }}</dd>
                            </div>
                            <div class="flex items-end justify-between gap-3">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.jadi_page.offer_model') }}</dt>
                                    <dd class="mt-0.5 text-gray-700">{{ $offer['model'] }}</dd>
                                </div>
                                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ __('talenma.jadi_page.on_quote') }}</span>
                            </div>
                        </dl>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pourquoi nous --}}
    <section class="bg-gray-50 py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">{{ __('talenma.jadi_page.why_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.jadi_page.why_title') }}</h2>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (__('talenma.jadi_page.why') as $reason)
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-100">
                        <h3 class="text-base font-bold text-gray-900">{{ $reason['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $reason['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Étude de cas --}}
    <section class="bg-white py-20 sm:py-24">
        <div class="max-w-7xl mx-auto grid gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">{{ __('talenma.jadi_page.case_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.jadi_page.case_title') }}</h2>
                <p class="mt-4 text-gray-600">{{ __('talenma.jadi_page.case_desc') }}</p>
                <a href="{{ route('home') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    {{ __('talenma.jadi_page.case_cta') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            <ul class="grid gap-3 rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-8 text-white shadow-lg">
                @foreach (__('talenma.jadi_page.case_features') as $feature)
                    <li class="flex items-start gap-3 text-sm sm:text-base">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-teal-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        <span>{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Méthode --}}
    <section class="bg-gray-50 py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">{{ __('talenma.jadi_page.method_eyebrow') }}</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.jadi_page.method_title') }}</h2>
            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach (__('talenma.jadi_page.steps') as $i => $step)
                    <li class="rounded-2xl bg-white p-6 ring-1 ring-gray-100">
                        <span class="text-sm font-bold text-indigo-600">0{{ $i + 1 }}</span>
                        <h3 class="mt-2 text-xl font-bold text-gray-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $step['desc'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Contact --}}
    <section class="bg-gray-950 py-20 text-white sm:py-24">
        <div class="max-w-3xl mx-auto px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ __('talenma.jadi_page.contact_title') }}</h2>
            <p class="mt-4 text-gray-300">{{ __('talenma.jadi_page.contact_desc') }}</p>
            <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ $contactUrl }}" class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-900 transition hover:bg-teal-50">{{ __('talenma.jadi_page.contact_email') }}</a>
                <a href="https://www.jadi-digital.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/10">{{ __('talenma.jadi_page.contact_site') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
