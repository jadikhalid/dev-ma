@extends('layouts.public')

@section('title', __('talenma.company_offer.trial_page.title').' '.__('talenma.company_offer.trial_page.title_accent'))
@section('meta_description', __('talenma.company_offer.trial_page.intro'))

@php
    $demoUrl = route('company.demo');
    $offersUrl = route('company.offers');
    $gradient = 'bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)]';
    $checkPath = 'm5 12 5 5 9-10';

    $mockPipeline = [
        ['label' => __('talenma.company_plans.ats.mock_col_received'), 'count' => 12, 'tone' => 'text-[#5546F0]', 'people' => [['YB', '#5546F0', 'Youssef B.', 'Casablanca'], ['SA', '#8B5CF6', 'Salma A.', 'Rabat']]],
        ['label' => __('talenma.company_plans.ats.mock_col_shortlist'), 'count' => 4, 'tone' => 'text-[#7C3AED]', 'people' => [['HA', '#2563EB', 'Hamza A.', 'Marrakech']]],
        ['label' => __('talenma.company_plans.ats.mock_col_interview'), 'count' => 2, 'tone' => 'text-[#2563EB]', 'people' => [['NK', '#06B6D4', 'Nora K.', 'Tanger']]],
    ];

    $mockTalents = [
        ['DA', '#5546F0', 'Data Analyst', 'Montréal'],
        ['RP', '#7C3AED', 'Resp. Produit', 'Rabat'],
        ['BD', '#2563EB', 'Business Dev.', 'São Paulo'],
        ['DO', '#06B6D4', 'Ingénieure DevOps', 'Paris'],
    ];

    $mockJobs = [
        ['Data Engineer', 'Full remote · 12 candidatures'],
        ['Chef de projet', 'Casablanca · 8 candidatures'],
        ['Ingénieur Supply Chain', 'Visa · 5 candidatures'],
    ];
@endphp

@section('content')
<div
    data-company-trial
    x-data="{ close() { window.location.href = @js(route('company.offer')); } }"
    @company-offer-drawer-close.window="close()"
>
    {{-- Hero + form --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute -left-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-[radial-gradient(circle,rgba(165,180,252,1)_0%,rgba(255,255,255,0)_68%)] opacity-55"></div>
        <div aria-hidden="true" class="pointer-events-none absolute left-[55%] -top-24 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(235,186,110,1)_0%,rgba(255,255,255,0)_68%)] opacity-30"></div>

        <div class="home-shell relative grid items-center gap-10 py-10 sm:gap-12 sm:py-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,28.75rem)] lg:gap-14 lg:py-16">
            <div class="flex flex-col gap-6">
                <span class="inline-flex self-start items-center gap-2 rounded-full bg-[#EBBA6E] px-3.5 py-2 text-[13px] font-extrabold tracking-[0.06em] text-[#0D0B2B]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M3 12h18M12 8v13M12 8c-2-4-6-4-6-1s4 1 6 1zM12 8c2-4 6-4 6-1s-4 1-6 1z"/></svg>
                    {{ __('talenma.company_offer.trial_page.badge') }}
                </span>
                <h1 class="text-4xl font-extrabold leading-[1.05] tracking-[-0.035em] text-gray-950 sm:text-5xl lg:text-[3.75rem]">
                    {{ __('talenma.company_offer.trial_page.title') }}
                    <span class="bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)] bg-clip-text text-transparent">{{ __('talenma.company_offer.trial_page.title_accent') }}</span>
                </h1>
                <p class="max-w-xl text-lg font-light leading-relaxed text-gray-600 sm:text-xl">{{ __('talenma.company_offer.trial_page.intro') }}</p>
                <div class="flex flex-wrap gap-x-5 gap-y-3 text-[15px] text-gray-700">
                    @foreach (__('talenma.company_offer.trial_page.trust') as $item)
                        <span class="inline-flex items-center gap-2">
                            <span class="text-[#5546F0]" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg></span>
                            {{ $item }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div
                id="demande"
                class="relative scroll-mt-28 rounded-[28px] border border-[#EEF0F4] bg-white p-6 shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)] sm:p-8"
                data-company-trial-offer
            >
                <div class="mb-5 flex flex-col gap-1">
                    <h2 class="text-[22px] font-extrabold text-gray-950">{{ __('talenma.company_offer.trial_title') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('talenma.company_offer.trial_subtitle') }}</p>
                </div>
                @include('company.partials.trial-request-form', [
                    'professionSectors' => $professionSectors,
                    'companyCountryOptions' => $companyCountryOptions,
                ])
            </div>
        </div>
    </section>

    {{-- Pourquoi essayer --}}
    <section class="bg-[#F7F7FB]">
        <div class="home-shell flex flex-col gap-8 py-14 sm:gap-9 sm:py-20">
            <div class="mx-auto flex max-w-3xl flex-col items-center gap-2.5 text-center">
                <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_offer.trial_page.why_badge') }}</span>
                <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.75rem]">{{ __('talenma.company_offer.trial_page.why_title') }}</h2>
            </div>
            <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (__('talenma.company_offer.trial_page.why_items') as $item)
                    <span class="flex items-center gap-3 rounded-2xl border border-[#E8E9F0] bg-white px-4 py-4 text-base font-semibold text-gray-950">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $gradient }} text-white" aria-hidden="true">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkPath }}"/></svg>
                        </span>
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Inclus --}}
    <section class="home-shell flex flex-col gap-14 py-14 sm:gap-16 sm:py-20 lg:gap-20 lg:py-24">
        <div class="mx-auto flex max-w-3xl flex-col items-center gap-2.5 text-center">
            <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ __('talenma.company_offer.trial_page.included_badge') }}</span>
            <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-gray-950 sm:text-4xl lg:text-[2.75rem]">{{ __('talenma.company_offer.trial_page.included_title') }}</h2>
        </div>

        @php $features = __('talenma.company_offer.trial_page.features'); @endphp

        {{-- 01 ATS --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="relative flex min-h-[18rem] items-center justify-center overflow-hidden rounded-[28px] bg-[#EEF0FF] p-6 sm:p-8">
                <div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-28 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(165,180,252,1)_0%,rgba(255,255,255,0)_68%)] opacity-60"></div>
                <div class="relative w-full rounded-[26px] border border-[#E8E9F0] bg-white p-5 shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)]">
                    <div class="flex items-center justify-between gap-3">
                        <span class="flex flex-col gap-0.5">
                            <span class="text-xs text-gray-500">{{ __('talenma.company_plans.ats.mock_label') }}</span>
                            <span class="text-base font-extrabold text-gray-950">{{ __('talenma.company_plans.ats.mock_job') }}</span>
                        </span>
                        <span class="rounded-full bg-green-100 px-2.5 py-1.5 text-xs font-bold text-green-800">{{ __('talenma.company_plans.ats.mock_status') }}</span>
                    </div>
                    <div class="mt-3.5 grid gap-2.5 sm:grid-cols-3">
                        @foreach ($mockPipeline as $column)
                            <div class="flex flex-col gap-2.5 rounded-2xl bg-[#F5F6FF] p-3">
                                <span class="flex items-center justify-between text-[13px] font-bold {{ $column['tone'] }}">
                                    {{ $column['label'] }}
                                    <span class="rounded-full bg-white px-2 py-0.5 text-xs text-gray-700">{{ $column['count'] }}</span>
                                </span>
                                @foreach ($column['people'] as $person)
                                    <div class="flex flex-col gap-1.5 rounded-xl border border-[#E8E9F0] bg-white p-3 shadow-sm">
                                        <span class="flex items-center gap-2">
                                            <span class="inline-flex h-[26px] w-[26px] items-center justify-center rounded-full text-[11px] font-bold text-white" style="background: {{ $person[1] }}">{{ $person[0] }}</span>
                                            <span class="text-[13px] font-bold text-gray-950">{{ $person[2] }}</span>
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $person[3] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-right text-[11px] text-gray-400">{{ __('talenma.company_plans.ats.mock_note') }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-3.5">
                <span class="text-sm font-extrabold text-[#5546F0]">{{ $features[0]['num'] }}</span>
                <h3 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-gray-950 sm:text-[2.125rem]">{{ $features[0]['title'] }}</h3>
                <p class="text-[17px] leading-relaxed text-gray-600">{{ $features[0]['text'] }}</p>
            </div>
        </div>

        {{-- 02 Catalogue --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="flex flex-col gap-3.5 lg:order-1">
                <span class="text-sm font-extrabold text-[#5546F0]">{{ $features[1]['num'] }}</span>
                <h3 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-gray-950 sm:text-[2.125rem]">{{ $features[1]['title'] }}</h3>
                <p class="text-[17px] leading-relaxed text-gray-600">{{ $features[1]['text'] }}</p>
            </div>
            <div class="relative flex min-h-[18rem] items-center justify-center overflow-hidden rounded-[28px] bg-[#EEF0FF] p-6 sm:p-8 lg:order-2">
                <div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-28 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(165,180,252,1)_0%,rgba(255,255,255,0)_68%)] opacity-60"></div>
                <div class="relative grid w-full grid-cols-2 gap-3">
                    @foreach ($mockTalents as $talent)
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-3.5 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                            <span class="inline-flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-full text-[13px] font-bold text-white" style="background: {{ $talent[1] }}">{{ $talent[0] }}</span>
                            <span class="flex min-w-0 flex-col gap-0.5">
                                <span class="truncate text-sm font-bold text-gray-950">{{ $talent[2] }}</span>
                                <span class="text-xs text-gray-500">{{ $talent[3] }} · <span class="font-semibold text-green-600">{{ __('talenma.company_offer.trial_page.mock_verified') }}</span></span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 03 Annonces --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="relative flex min-h-[18rem] items-center justify-center overflow-hidden rounded-[28px] bg-[#EEF0FF] p-6 sm:p-8">
                <div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-28 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(165,180,252,1)_0%,rgba(255,255,255,0)_68%)] opacity-60"></div>
                <div class="relative flex w-full flex-col gap-2.5">
                    @foreach ($mockJobs as $job)
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-4 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                            <span class="flex flex-col gap-0.5">
                                <span class="text-[15px] font-bold text-gray-950">{{ $job[0] }}</span>
                                <span class="text-xs text-gray-500">{{ $job[1] }}</span>
                            </span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-800">{{ __('talenma.company_plans.ats.mock_status') }}</span>
                        </div>
                    @endforeach
                    <span class="self-end text-xs font-bold text-[#5546F0]">{{ __('talenma.company_offer.trial_page.mock_jobs_cap') }}</span>
                </div>
            </div>
            <div class="flex flex-col gap-3.5">
                <span class="text-sm font-extrabold text-[#5546F0]">{{ $features[2]['num'] }}</span>
                <h3 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-gray-950 sm:text-[2.125rem]">{{ $features[2]['title'] }}</h3>
                <p class="text-[17px] leading-relaxed text-gray-600">{{ $features[2]['text'] }}</p>
            </div>
        </div>

        {{-- 04 Page entreprise --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="flex flex-col gap-3.5 lg:order-1">
                <span class="text-sm font-extrabold text-[#5546F0]">{{ $features[3]['num'] }}</span>
                <h3 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-gray-950 sm:text-[2.125rem]">{{ $features[3]['title'] }}</h3>
                <p class="text-[17px] leading-relaxed text-gray-600">{{ $features[3]['text'] }}</p>
            </div>
            <div class="relative flex min-h-[18rem] items-center justify-center overflow-hidden rounded-[28px] bg-[#EEF0FF] p-6 sm:p-8 lg:order-2">
                <div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-28 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(165,180,252,1)_0%,rgba(255,255,255,0)_68%)] opacity-60"></div>
                <div class="relative w-full overflow-hidden rounded-[20px] bg-white shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                    <div class="h-[90px] {{ $gradient }}"></div>
                    <div class="flex flex-col gap-2.5 px-5 pb-5">
                        <span class="mt-[-28px] inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-sm font-extrabold text-[#5546F0] shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">{{ __('talenma.company_offer.trial_page.mock_logo') }}</span>
                        <span class="text-lg font-extrabold text-gray-950">{{ __('talenma.company_offer.trial_page.mock_company') }}</span>
                        <span class="text-[13px] text-gray-500">{{ __('talenma.company_offer.trial_page.mock_company_meta') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Après 45 jours --}}
    <section class="home-shell pb-14 sm:pb-20 lg:pb-24">
        <div class="relative overflow-hidden rounded-[32px] bg-[#0D0B2B] px-6 py-10 sm:px-12 sm:py-12">
            <div aria-hidden="true" class="pointer-events-none absolute -left-28 -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(124,58,237,1)_0%,rgba(255,255,255,0)_68%)] opacity-70"></div>
            <div class="relative grid items-center gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] lg:gap-12">
                <div class="flex flex-col gap-3.5">
                    <span class="text-[13px] font-bold tracking-[0.12em] text-[#EBBA6E]">{{ __('talenma.company_offer.trial_page.after_badge') }}</span>
                    <h2 class="text-3xl font-extrabold leading-tight tracking-[-0.02em] text-white sm:text-4xl">{{ __('talenma.company_offer.trial_page.after_title') }}</h2>
                    <p class="text-[17px] leading-relaxed text-indigo-200">{{ __('talenma.company_offer.trial_page.after_text') }}</p>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach (__('talenma.company_offer.trial_page.after_links') as $link)
                        <a href="{{ $offersUrl }}#formules" class="flex items-center justify-between gap-4 rounded-2xl border border-white/14 bg-white/[0.06] px-5 py-4 text-white transition hover:bg-white/10">
                            <span class="text-[17px] font-bold">{{ __('talenma.company_plans.plans.'.$link['key'].'.name') }}</span>
                            <span class="text-[13px] text-indigo-200">{{ $link['text'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="home-shell pb-14 sm:pb-20 lg:pb-24">
        <div class="relative overflow-hidden rounded-[36px] {{ $gradient }} px-6 py-12 sm:px-12 sm:py-14">
            <div aria-hidden="true" class="pointer-events-none absolute left-[55%] -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(235,186,110,1)_0%,rgba(255,255,255,0)_68%)] opacity-40"></div>
            <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                <h2 class="max-w-xl text-3xl font-extrabold leading-tight tracking-[-0.03em] text-white sm:text-[2.5rem]">
                    {{ __('talenma.company_offer.trial_page.final_title') }}
                    <span class="text-[#EBBA6E]">{{ __('talenma.company_offer.trial_page.final_accent') }}</span>
                </h2>
                <div class="flex flex-wrap gap-3">
                    <a href="#demande" class="inline-flex items-center justify-center rounded-[14px] bg-white px-6 py-4 text-base font-bold text-[#0D0B2B]">
                        {{ __('talenma.company_offer.trial_page.final_cta') }}
                    </a>
                    <a href="{{ $demoUrl }}" class="inline-flex items-center justify-center rounded-[14px] border-[1.5px] border-white/55 px-6 py-4 text-base font-semibold text-white">
                        {{ __('talenma.company_offer.trial_page.final_demo') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
