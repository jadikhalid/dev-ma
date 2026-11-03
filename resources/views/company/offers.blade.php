@extends('layouts.public')

@section('title', __('talenma.company_plans.meta_title'))
@section('meta_description', __('talenma.company_plans.meta_description'))

@php
    $demoUrl = route('company.demo');
    $trialUrl = route('company.trial');
    $gradient = 'bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)]';
    $checkPath = 'm5 12 5 5 9-10';
    $arrowPath = 'M5 12h14M13 6l6 6-6 6';

    $plans = [
        [
            'key' => 'essential',
            'featured' => false,
            'cta' => __('talenma.company_plans.cta_demo'),
            'href' => $demoUrl,
            'price' => __('talenma.company_plans.price_essential_label'),
        ],
        [
            'key' => 'pro',
            'featured' => true,
            'cta' => __('talenma.company_plans.cta_demo'),
            'href' => $demoUrl,
            'price' => __('talenma.company_plans.price_pro_label'),
        ],
        [
            'key' => 'custom',
            'featured' => false,
            'cta' => __('talenma.company_plans.cta_custom'),
            'href' => $demoUrl,
            'price' => null,
        ],
    ];

    $compareRows = [
        ['row' => 'page', 'kind' => 'text', 'trial' => 'page_trial', 'essential' => 'page_essential', 'pro' => 'page_pro'],
        ['row' => 'jobs', 'kind' => 'text', 'trial' => 'jobs_trial', 'essential' => 'jobs_essential', 'pro' => 'jobs_pro'],
        ['row' => 'ats', 'kind' => 'check', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'applications', 'kind' => 'check', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'catalogue', 'kind' => 'check', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'direct_hire', 'kind' => 'check', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'sourcing', 'kind' => 'text', 'trial' => 'option', 'essential' => 'option', 'pro' => 'sourcing_pro'],
        ['row' => 'brand', 'kind' => 'check', 'trial' => 'no', 'essential' => 'no', 'pro' => 'yes'],
        ['row' => 'support', 'kind' => 'text', 'trial' => 'support_std', 'essential' => 'support_std', 'pro' => 'support_prio'],
    ];

    $atsIcons = [
        'M7 3h7l5 5v13H7z M14 3v5h5M10 13h6M10 17h4',
        'M9 8a3.5 3.5 0 1 0 0-0.01M2.5 20c1-3.5 3.5-5.5 6.5-5.5s5.5 2 6.5 5.5M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2 .8 3.2 2.8 3.7 5.5',
        'm12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z',
        'M11 11a7 7 0 1 0 0.01 0M20 20l-3.5-3.5',
    ];
@endphp

@section('content')
<div data-company-offers>
    {{-- Formules --}}
    <section id="formules" class="relative overflow-hidden">
        <div class="home-shell flex flex-col gap-10 py-14 sm:gap-12 sm:py-20 lg:py-24">
            <div class="mx-auto flex max-w-3xl flex-col items-center gap-3 text-center">
                <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_plans.formulas_badge') }}</span>
                <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.875rem]">{{ __('talenma.company_plans.formulas_title') }}</h2>
                <p class="text-lg font-light text-gray-600">{{ __('talenma.company_plans.formulas_intro') }}</p>
            </div>

            <div class="grid items-stretch gap-5 lg:grid-cols-3 lg:gap-6">
                @foreach ($plans as $plan)
                    @php $copy = __('talenma.company_plans.plans.'.$plan['key']); @endphp
                    <article @class([
                        'relative flex flex-col gap-5 rounded-[26px] px-6 pb-7 pt-9 sm:px-7',
                        'bg-[#0D0B2B] text-white shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)] lg:-translate-y-3' => $plan['featured'],
                        'border border-[#E8E9F0] bg-white text-gray-950 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]' => ! $plan['featured'],
                    ])>
                        @if ($plan['featured'])
                            <span class="absolute -top-3.5 left-7 rounded-full bg-[#EBBA6E] px-3 py-1.5 text-xs font-extrabold tracking-[0.05em] text-[#0D0B2B]">{{ __('talenma.company_plans.recommended') }}</span>
                        @endif
                        <div @class(['flex flex-col gap-2 border-b pb-5', 'border-white/14' => $plan['featured'], 'border-[#EEF0F4]' => ! $plan['featured']])>
                            <h3 class="text-[26px] font-extrabold">{{ $copy['name'] }}</h3>
                            <p @class(['min-h-[2.875rem] text-[15px] leading-relaxed', 'text-indigo-200' => $plan['featured'], 'text-gray-500' => ! $plan['featured']])>{{ $copy['tagline'] }}</p>
                            @if ($plan['price'])
                                <p @class(['text-sm font-semibold', 'text-indigo-100' => $plan['featured'], 'text-[#5546F0]' => ! $plan['featured']])>{{ $plan['price'] }}</p>
                            @endif
                        </div>
                        <ul class="flex flex-1 flex-col gap-3">
                            @foreach ($copy['features'] as $feature)
                                <li @class(['flex items-start gap-2.5 text-[15px] leading-snug', 'text-indigo-100' => $plan['featured'], 'text-gray-700' => ! $plan['featured']])>
                                    <span @class(['inline-flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full', 'bg-white/18 text-white' => $plan['featured'], 'bg-[#EEF0FF] text-[#5546F0]' => ! $plan['featured']]) aria-hidden="true">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg>
                                    </span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a
                            href="{{ $plan['href'] }}"
                            @class([
                                'mt-1 inline-flex items-center justify-center gap-2 rounded-[14px] px-4 py-3.5 text-[15px] font-bold',
                                $gradient.' text-white shadow-[0_14px_28px_-14px_rgba(139,92,246,0.9)]' => $plan['featured'],
                                'border-[1.5px] border-indigo-200 bg-white text-[#5546F0]' => ! $plan['featured'],
                            ])
                        >
                            {{ $plan['cta'] }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                        </a>
                    </article>
                @endforeach
            </div>

            {{-- À la carte --}}
            <div class="flex flex-col gap-5 rounded-[28px] border border-[#E8E9F0] bg-white p-6 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)] sm:p-9">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-1.5">
                        <span class="text-[13px] font-extrabold tracking-[0.08em] text-[#7C3AED]">{{ __('talenma.company_plans.a_la_carte.badge') }}</span>
                        <h3 class="text-[26px] font-extrabold text-gray-950">{{ __('talenma.company_plans.a_la_carte.title') }}</h3>
                        <p class="text-[15px] text-gray-600">{{ __('talenma.company_plans.a_la_carte.text') }}</p>
                    </div>
                    <a href="{{ $demoUrl }}" class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-[14px] border-[1.5px] border-indigo-200 bg-white px-6 py-3.5 text-[15px] font-semibold text-[#5546F0]">
                        {{ __('talenma.company_plans.a_la_carte.cta') }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                    </a>
                </div>
                <div class="grid gap-3.5 sm:grid-cols-3">
                    @foreach (__('talenma.company_plans.a_la_carte.items') as $index => $item)
                        <div class="flex flex-col gap-3 rounded-[18px] bg-[#F5F6FF] p-5">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-[14px] bg-white text-[#5546F0]" aria-hidden="true">
                                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $atsIcons[$index === 2 ? 1 : ($index === 1 ? 2 : 0)] }}"/></svg>
                            </span>
                            <span class="text-[17px] font-bold text-gray-950">{{ $item['title'] }}</span>
                            <span class="flex-1 text-sm leading-relaxed text-gray-600">{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Essai banner --}}
            <div class="grid items-center gap-6 rounded-[28px] border-2 border-[#EBBA6E] bg-[linear-gradient(180deg,#FFF8EA_0%,#FFFFFF_100%)] px-6 py-7 sm:px-9 lg:grid-cols-[minmax(0,1fr)_auto]">
                <div class="flex flex-col gap-3.5">
                    <span class="flex items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-[14px] bg-[#EBBA6E] text-[#0D0B2B]" aria-hidden="true">
                            <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M3 12h18M12 8v13M12 8c-2-4-6-4-6-1s4 1 6 1zM12 8c2-4 6-4 6-1s-4 1-6 1z"/></svg>
                        </span>
                        <span class="flex flex-col gap-0.5">
                            <span class="text-xs font-extrabold tracking-[0.08em] text-amber-700">{{ __('talenma.company_plans.trial_banner.badge') }}</span>
                            <span class="text-2xl font-extrabold text-gray-950">{{ __('talenma.company_plans.trial_banner.title') }}</span>
                        </span>
                    </span>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach (__('talenma.company_plans.plans.trial.features') as $feature)
                            <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-2 text-sm font-semibold text-gray-800">
                                <span class="text-amber-700" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg></span>
                                {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ $trialUrl }}" class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-[14px] {{ $gradient }} px-6 py-3.5 text-[15px] font-semibold text-white shadow-[0_14px_28px_-14px_rgba(79,70,229,0.8)]">
                    {{ __('talenma.company_plans.trial_banner.cta') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Intermédiation --}}
    <section class="bg-[linear-gradient(180deg,#F5F3FF_0%,#FFFFFF_100%)]">
        <div class="home-shell grid items-center gap-10 py-14 sm:gap-12 sm:py-20 lg:grid-cols-2 lg:gap-16 lg:py-24">
            <div class="flex flex-col gap-5">
                <span class="inline-flex self-start rounded-full bg-[#7C3AED] px-3.5 py-2 text-[13px] font-extrabold tracking-[0.08em] text-white">{{ __('talenma.company_plans.sourcing.badge') }}</span>
                <h2 class="text-3xl font-extrabold leading-[1.08] tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.75rem]">
                    {{ __('talenma.company_plans.sourcing.title') }}
                    <span class="text-[#7C3AED]">{{ __('talenma.company_plans.sourcing.title_accent') }}</span>
                </h2>
                <p class="text-lg font-light leading-relaxed text-gray-600">{{ __('talenma.company_plans.sourcing.text') }}</p>
                <div class="flex flex-wrap gap-2.5">
                    @foreach (__('talenma.company_plans.sourcing.pills') as $pill)
                        <span class="inline-flex items-center gap-2 rounded-full border border-violet-100 bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-800">
                            <span class="text-[#7C3AED]" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg></span>
                            {{ $pill }}
                        </span>
                    @endforeach
                </div>
                <a href="{{ $demoUrl }}" class="inline-flex self-start items-center gap-2 rounded-[14px] bg-[#7C3AED] px-6 py-4 text-base font-bold text-white">
                    {{ __('talenma.company_plans.sourcing.cta') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                </a>
            </div>
            <div class="flex flex-col gap-3">
                @foreach (__('talenma.company_plans.sourcing.steps') as $index => $step)
                    <div class="flex items-start gap-4 rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-4 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $gradient }} text-[15px] font-extrabold text-white">{{ $index + 1 }}</span>
                        <span class="flex flex-col gap-0.5">
                            <span class="text-[17px] font-bold text-gray-950">{{ $step['title'] }}</span>
                            <span class="text-[15px] text-gray-600">{{ $step['text'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Comparatif --}}
    <section id="comparer" class="home-shell flex flex-col gap-8 py-14 sm:gap-9 sm:py-20 lg:py-24">
        <div class="mx-auto flex max-w-3xl flex-col items-center gap-2.5 text-center">
            <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_plans.compare.badge') }}</span>
            <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.75rem]">{{ __('talenma.company_plans.compare.title') }}</h2>
        </div>

        <div class="overflow-x-auto rounded-[26px] border border-[#E8E9F0] bg-white shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
            <div class="min-w-[40rem]">
                <div class="grid grid-cols-[1.6fr_repeat(3,minmax(0,1fr))] items-stretch">
                    <span class="px-4 py-6 text-sm font-bold text-gray-500 sm:px-6">{{ __('talenma.company_plans.compare.feature') }}</span>
                    <span class="flex flex-col gap-1 px-2 py-6 text-center sm:px-3">
                        <span class="text-[17px] font-extrabold text-gray-950">{{ __('talenma.company_plans.plans.trial.name') }}</span>
                        <span class="text-[13px] font-semibold text-amber-700">{{ __('talenma.company_plans.compare.period_trial') }}</span>
                    </span>
                    <span class="flex flex-col gap-1 px-2 py-6 text-center sm:px-3">
                        <span class="text-[17px] font-extrabold text-gray-950">{{ __('talenma.company_plans.plans.essential.name') }}</span>
                        <span class="text-[13px] text-gray-500">{{ __('talenma.company_plans.compare.period_paid') }}</span>
                    </span>
                    <span class="flex flex-col gap-1 bg-[#F5F3FF] px-2 py-6 text-center shadow-[inset_0_3px_0_#5546F0] sm:px-3">
                        <span class="text-[17px] font-extrabold text-[#5546F0]">{{ __('talenma.company_plans.plans.pro.name') }}</span>
                        <span class="text-[13px] text-gray-500">{{ __('talenma.company_plans.compare.period_paid') }}</span>
                    </span>
                </div>
                @foreach ($compareRows as $row)
                    <div class="grid grid-cols-[1.6fr_repeat(3,minmax(0,1fr))] items-center border-t border-[#EEF0F4]">
                        <span class="px-4 py-4 text-[15px] text-gray-700 sm:px-6">{{ __('talenma.company_plans.compare.rows.'.$row['row']) }}</span>
                        @foreach (['trial', 'essential', 'pro'] as $col)
                            @php
                                $valueKey = $row[$col];
                                $isPro = $col === 'pro';
                                $isCheck = $row['kind'] === 'check';
                                $isYes = $isCheck && $valueKey === 'yes';
                                $isNo = $isCheck && $valueKey === 'no';
                            @endphp
                            <span @class(['flex justify-center px-2 py-4 sm:px-3', 'bg-[#F5F3FF]' => $isPro])>
                                @if ($isYes)
                                    <span @class(['inline-flex h-[26px] w-[26px] items-center justify-center rounded-full', 'bg-[#5546F0] text-white' => $isPro, 'bg-[#EEF0FF] text-[#5546F0]' => ! $isPro]) aria-label="{{ __('talenma.company_plans.compare.values.yes') }}">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg>
                                    </span>
                                @elseif ($isNo)
                                    <span class="block h-0.5 w-3.5 rounded-sm bg-slate-300" aria-hidden="true"></span>
                                @else
                                    <span @class(['text-[15px] font-semibold', 'text-[#5546F0]' => $isPro, 'text-gray-950' => ! $isPro])>
                                        {{ __('talenma.company_plans.compare.values.'.$valueKey) }}
                                    </span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sur mesure dark CTA --}}
        <div class="relative overflow-hidden rounded-[32px] bg-[#0D0B2B] px-6 py-10 sm:px-12 sm:py-12">
            <div aria-hidden="true" class="pointer-events-none absolute -left-28 -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(124,58,237,1)_0%,rgba(255,255,255,0)_68%)] opacity-70"></div>
            <div aria-hidden="true" class="pointer-events-none absolute left-[60%] top-20 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(37,99,235,1)_0%,rgba(255,255,255,0)_68%)] opacity-45"></div>
            <div class="relative grid items-center gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.25fr)] lg:gap-12">
                <div class="flex flex-col gap-4">
                    <span class="inline-flex self-start rounded-full bg-[#EBBA6E] px-3.5 py-2 text-[13px] font-extrabold tracking-[0.08em] text-[#0D0B2B]">{{ __('talenma.company_plans.custom.badge') }}</span>
                    <h3 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-white sm:text-[2.375rem]">{{ __('talenma.company_plans.custom.cta_title') }}</h3>
                    <p class="text-[17px] leading-relaxed text-indigo-200">{{ __('talenma.company_plans.custom.cta_text') }}</p>
                    <a href="{{ $demoUrl }}" class="inline-flex self-start items-center gap-2 rounded-[14px] bg-[#EBBA6E] px-6 py-4 text-base font-bold text-[#0D0B2B]">
                        {{ __('talenma.company_plans.custom.cta_button') }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                    </a>
                </div>
                <div class="grid gap-3.5 sm:grid-cols-2">
                    @foreach (__('talenma.company_plans.custom.cards') as $index => $card)
                        <div class="flex flex-col gap-2.5 rounded-[18px] border border-white/14 bg-white/[0.06] p-5">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-[14px] {{ $gradient }} text-white" aria-hidden="true">
                                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $atsIcons[$index] }}"/></svg>
                            </span>
                            <span class="text-[17px] font-bold text-white">{{ $card['title'] }}</span>
                            <span class="text-sm leading-relaxed text-indigo-200">{{ $card['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Après l'essai --}}
    <section class="home-shell pb-14 sm:pb-20 lg:pb-24">
        <div class="relative overflow-hidden rounded-[32px] bg-[#0D0B2B] px-6 py-10 sm:px-12 sm:py-12">
            <div aria-hidden="true" class="pointer-events-none absolute -left-28 -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(124,58,237,1)_0%,rgba(255,255,255,0)_68%)] opacity-70"></div>
            <div aria-hidden="true" class="pointer-events-none absolute left-[55%] top-8 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(37,99,235,1)_0%,rgba(255,255,255,0)_68%)] opacity-45"></div>
            <div class="relative grid items-center gap-8 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] lg:gap-12">
                <div class="flex flex-col gap-4">
                    <span class="inline-flex self-start items-center gap-2 rounded-full bg-[rgba(235,186,110,0.15)] px-3 py-1.5 text-[13px] font-bold text-[#EBBA6E]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        {{ __('talenma.company_plans.after_trial.badge') }}
                    </span>
                    <h2 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-white sm:text-4xl">{{ __('talenma.company_plans.after_trial.title') }}</h2>
                    <p class="text-[17px] leading-relaxed text-indigo-200">{{ __('talenma.company_plans.after_trial.text') }}</p>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach (__('talenma.company_plans.after_trial.steps') as $index => $step)
                        @php
                            $stepTone = match ($index) {
                                0 => 'bg-[#EBBA6E] text-[#0D0B2B]',
                                1 => 'bg-[#5546F0] text-white',
                                default => 'bg-[#7C3AED] text-white',
                            };
                        @endphp
                        <div class="flex items-center gap-3.5 rounded-2xl border border-white/12 bg-white/[0.06] px-4 py-4 text-white">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-[10px] text-sm font-extrabold {{ $stepTone }}">{{ $index + 1 }}</span>
                            <span class="text-[15px] font-semibold">{{ $step }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Essai & démo CTAs --}}
    <section id="demo" class="bg-[#F7F7FB]">
        <div class="home-shell flex flex-col gap-8 py-14 sm:gap-10 sm:py-20 lg:py-24">
            <div class="mx-auto flex max-w-3xl flex-col items-center gap-2.5 text-center">
                <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_plans.request.badge') }}</span>
                <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.875rem]">{{ __('talenma.company_plans.request.title') }}</h2>
                <p class="text-lg font-light text-gray-600">{{ __('talenma.company_plans.request.text') }}</p>
            </div>

            <div class="grid items-stretch gap-6 lg:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">
                <div class="flex flex-col gap-4 rounded-[28px] border border-[#EEF0F4] bg-white p-7 shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)] sm:p-9">
                    <div class="grid gap-2 rounded-2xl bg-[#EEF0FF] p-1.5 sm:grid-cols-2">
                        <a href="{{ $trialUrl }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-3.5 text-[15px] font-bold text-[#5546F0] shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                            {{ __('talenma.company_plans.cta_trial') }}
                        </a>
                        <a href="{{ $demoUrl }}" class="inline-flex items-center justify-center rounded-xl px-4 py-3.5 text-[15px] font-semibold text-gray-600 transition hover:bg-white/70 hover:text-[#5546F0]">
                            {{ __('talenma.company_plans.cta_demo') }}
                        </a>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_plans.intro') }}</p>
                    <div class="mt-auto flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $trialUrl }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-[14px] {{ $gradient }} px-5 py-4 text-base font-bold text-white shadow-[0_14px_28px_-14px_rgba(79,70,229,0.8)]">
                            {{ __('talenma.company_plans.trial_banner.cta') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arrowPath }}"/></svg>
                        </a>
                        <a href="{{ $demoUrl }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-[14px] border-[1.5px] border-indigo-200 bg-white px-5 py-4 text-base font-bold text-[#5546F0]">
                            {{ __('talenma.company_plans.cta_demo') }}
                        </a>
                    </div>
                </div>

                <div class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] bg-[#0D0B2B] p-7 sm:p-9">
                    <div aria-hidden="true" class="pointer-events-none absolute -left-32 -top-40 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(124,58,237,1)_0%,rgba(255,255,255,0)_68%)] opacity-70"></div>
                    <div class="relative flex items-center gap-3.5">
                        <span class="inline-flex h-[52px] w-[52px] items-center justify-center rounded-2xl {{ $gradient }} text-white" aria-hidden="true">
                            <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                        </span>
                        <span class="flex flex-col gap-0.5">
                            <span class="text-xl font-extrabold text-white">{{ __('talenma.company_plans.request.demo_title') }}</span>
                            <span class="text-sm text-indigo-200">{{ __('talenma.company_plans.request.demo_subtitle') }}</span>
                        </span>
                    </div>
                    <div class="relative flex flex-col gap-4">
                        @foreach (__('talenma.company_plans.request.demo_steps') as $index => $step)
                            <div class="flex items-start gap-3.5">
                                <span @class([
                                    'inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[13px] font-extrabold',
                                    'bg-[#EBBA6E] text-[#0D0B2B]' => $index === 4,
                                    'bg-white/12 text-white' => $index !== 4,
                                ])>{{ $index + 1 }}</span>
                                <span class="flex flex-col gap-0.5">
                                    <span class="text-[15px] font-bold text-white">{{ $step['title'] }}</span>
                                    <span class="text-sm text-indigo-200">{{ $step['text'] }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="relative mt-auto flex flex-col gap-2.5 border-t border-white/12 pt-5">
                        @foreach (__('talenma.company_plans.request.notes') as $note)
                            <span class="inline-flex items-center gap-2 text-sm text-indigo-100">
                                <span class="text-[#EBBA6E]" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg></span>
                                {{ $note }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="home-shell grid gap-10 py-14 sm:gap-12 sm:py-20 lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)] lg:gap-16 lg:py-24">
        <div class="flex flex-col gap-3.5">
            <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_plans.faq.badge') }}</span>
            <h2 class="text-3xl font-extrabold leading-[1.08] tracking-[-0.03em] text-gray-950 sm:text-[2.625rem]">{{ __('talenma.company_plans.faq.title') }}</h2>
            <p class="text-base text-gray-600">
                {{ __('talenma.company_plans.faq.contact') }}
                <a href="{{ $demoUrl }}" class="font-semibold text-[#5546F0] hover:text-indigo-800">{{ __('talenma.company_plans.faq.contact_link') }}</a>
            </p>
        </div>
        <div class="flex flex-col gap-3" x-data="{ open: 0 }">
            @foreach (__('talenma.company_plans.faq.items') as $index => $item)
                <div
                    @class([
                        'rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-5 sm:px-6',
                    ])
                    :class="open === {{ $index }} ? 'shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]' : ''"
                >
                    <button
                        type="button"
                        class="flex w-full items-center justify-between gap-4 text-left text-[17px] font-semibold text-gray-950"
                        @click="open = open === {{ $index }} ? null : {{ $index }}"
                        :aria-expanded="(open === {{ $index }}).toString()"
                    >
                        <span>{{ $item['q'] }}</span>
                        <span
                            class="inline-flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full text-xl"
                            :class="open === {{ $index }} ? 'bg-[#0D0B2B] text-white' : 'bg-gray-100 text-gray-950'"
                            aria-hidden="true"
                            x-text="open === {{ $index }} ? '−' : '+'"
                        ></span>
                    </button>
                    <p
                        class="mt-3 text-base leading-relaxed text-gray-600"
                        x-show="open === {{ $index }}"
                        x-cloak
                    >{{ $item['a'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="home-shell pb-14 sm:pb-20 lg:pb-24">
        <div class="relative overflow-hidden rounded-[36px] {{ $gradient }} px-6 py-12 sm:px-12 sm:py-14">
            <div aria-hidden="true" class="pointer-events-none absolute left-[55%] -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(235,186,110,1)_0%,rgba(255,255,255,0)_68%)] opacity-40"></div>
            <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                <h2 class="max-w-xl text-3xl font-extrabold leading-tight tracking-[-0.03em] text-white sm:text-[2.625rem]">
                    {{ __('talenma.company_plans.final.title') }}
                    <span class="text-[#EBBA6E]">{{ __('talenma.company_plans.final.title_accent') }}</span>
                </h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $demoUrl }}" class="inline-flex items-center justify-center rounded-[14px] bg-white px-6 py-4 text-base font-bold text-[#0D0B2B]">
                        {{ __('talenma.company_plans.cta_demo') }}
                    </a>
                    <a href="{{ $trialUrl }}" class="inline-flex items-center justify-center rounded-[14px] border-[1.5px] border-white/55 px-6 py-4 text-base font-semibold text-white">
                        {{ __('talenma.company_plans.cta_trial') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
