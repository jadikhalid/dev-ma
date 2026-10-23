@extends('layouts.public')

@section('title', __('talenma.career_pages.how_meta_title'))
@section('meta_description', __('talenma.career_pages.how_meta_description'))

@php
    $howVisuals = [
        1 => ['ink' => '#5546F0', 'wash' => '#EEF0FF'],
        2 => ['ink' => '#2563EB', 'wash' => '#EAF1FF'],
        3 => ['ink' => '#7C3AED', 'wash' => '#F3EEFF'],
        4 => ['ink' => '#7C3AED', 'wash' => '#F3EEFF'],
    ];
    $phaseColors = ['#5546F0', '#2563EB', '#7C3AED', '#7C3AED'];
    $check = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>';
    $arrow = '<svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
@endphp

@section('content')
<div data-career-how>
    <section class="relative -mt-20 overflow-hidden pt-20 sm:-mt-[4.25rem] sm:pt-[4.25rem]">
        <div class="how-hero-frame" aria-hidden="true">
            <div class="how-wash how-wash-lilac -left-24 -top-40 h-[28rem] w-[28rem] opacity-50 sm:-left-32 sm:-top-72 sm:h-[760px] sm:w-[760px]"></div>
            <div class="how-wash how-wash-sky -right-24 -top-24 h-[22rem] w-[22rem] opacity-30 sm:-top-40 sm:right-0 sm:h-[700px] sm:w-[700px]"></div>
        </div>

        <div class="relative mx-auto flex w-full max-w-7xl flex-col items-center gap-5 px-4 pb-14 pt-10 text-center sm:gap-6 sm:px-6 sm:pb-16 sm:pt-14 lg:px-8 lg:pb-[4.5rem]">
            <p class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-700">{{ __('talenma.nav.home') }}</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-gray-900">{{ __('talenma.nav.how_it_works') }}</span>
            </p>
            <h1 class="max-w-4xl text-4xl font-extrabold leading-[1.05] tracking-tight text-gray-900 sm:text-6xl lg:text-[4.25rem] lg:leading-[1.02]">
                {{ __('talenma.career_pages.how_title_lead') }}
                <span class="bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] bg-clip-text text-transparent">{{ __('talenma.career_pages.how_title_accent') }}</span>
            </h1>
            <p class="text-base font-light text-gray-600 sm:text-xl">{{ __('talenma.career_pages.how_intro') }}</p>
            <ol class="mt-2 flex flex-wrap items-center justify-center gap-2.5">
                @foreach (__('talenma.career_pages.how_phases') as $phase)
                    @if (! $loop->first)
                        <li class="text-[#A5B4FC]" aria-hidden="true">{!! $arrow !!}</li>
                    @endif
                    <li class="how-lift flex items-center gap-2.5 rounded-full bg-white py-2.5 pl-2.5 pr-4 text-sm font-semibold text-gray-900 sm:text-[15px]">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-[13px] text-white" style="background: {{ $phaseColors[$loop->index] }}">{{ $phase['n'] }}</span>
                        {{ $phase['label'] }}
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="mx-auto flex w-full max-w-7xl flex-col gap-16 px-4 pb-16 sm:gap-24 sm:px-6 sm:pb-24 lg:px-8">
        @foreach (__('talenma.career_pages.how_steps') as $step)
            @php($visual = $howVisuals[$loop->iteration])
            <article id="how-step-{{ $loop->iteration }}" class="grid items-center gap-8 lg:grid-cols-2 lg:gap-20">
                <div @class(['lg:order-2' => $loop->iteration % 2 === 0])>
                    <div class="how-lift relative flex h-72 items-center justify-center overflow-hidden rounded-[2rem] sm:h-[420px]" style="--how-ink: {{ $visual['ink'] }}; background: {{ $visual['wash'] }}">
                        <div class="how-mesh absolute inset-0" aria-hidden="true"></div>
                        <div class="how-wash how-wash-lilac -left-16 -top-24 h-[26rem] w-[26rem] opacity-50" aria-hidden="true"></div>
                        <div class="relative w-full max-w-[380px] px-4">
                            @if ($loop->iteration === 1)
                                <div class="how-lift flex flex-col gap-2.5 rounded-[18px] bg-white p-4">
                                    <span class="rounded-[10px] border border-gray-200 px-3 py-2.5 text-[13px] text-gray-500">{{ __('talenma.career_pages.how_account_email') }}</span>
                                    <div class="flex items-center gap-2.5 rounded-[10px] border border-[#E0E4FF] bg-[#F5F6FF] px-3 py-2.5">
                                        <span class="inline-flex h-[30px] w-[30px] items-center justify-center rounded-lg bg-[#EDE9FE] text-[9px] font-extrabold text-[#6D28D9]">PDF</span>
                                        <span class="flex-1 text-xs font-semibold text-gray-900">{{ __('talenma.career_pages.how_account_file') }}</span>
                                        <span class="inline-flex h-[22px] w-[22px] items-center justify-center rounded-full bg-[#06B6D4] text-white">{!! $check !!}</span>
                                    </div>
                                    <a href="{{ route('register') }}" class="rounded-[10px] bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] px-3 py-2.5 text-center text-[13px] font-semibold text-white">{{ __('talenma.career_pages.how_account_cta') }}</a>
                                </div>
                            @elseif ($loop->iteration === 2)
                                <div class="how-lift flex flex-col gap-3 rounded-[18px] bg-white p-[18px]">
                                    <div class="flex items-center justify-between text-[13px] font-bold text-gray-900">
                                        <span>{{ __('talenma.career_pages.how_profile_label') }}</span>
                                        <span class="text-[#2563EB]">{{ __('talenma.career_pages.how_profile_percent') }}</span>
                                    </div>
                                    <span class="flex h-2 overflow-hidden rounded bg-[#EAF1FF]" aria-hidden="true"><span class="w-4/5 rounded bg-gradient-to-r from-[#22D3EE] to-[#2563EB]"></span></span>
                                    <span class="text-[11px] text-gray-500">{{ __('talenma.career_pages.how_profile_skills_label') }}</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (__('talenma.career_pages.how_profile_skills') as $skill)
                                            <span class="rounded-lg bg-[#EEF0FF] px-2.5 py-1.5 text-[11px] font-semibold text-[#4338CA]">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                    <span class="text-[11px] text-gray-500">{{ __('talenma.career_pages.how_profile_languages_label') }}</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (__('talenma.career_pages.how_profile_languages') as $language)
                                            <span class="rounded-lg bg-[#EAF1FF] px-2.5 py-1.5 text-[11px] font-semibold text-[#2563EB]">{{ $language }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif ($loop->iteration === 3)
                                <div class="how-lift flex flex-col gap-3 rounded-[18px] bg-white p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-[42px] w-[42px] shrink-0 rounded-full bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] p-[3px]">
                                            <span class="inline-flex flex-1 items-center justify-center rounded-full bg-white text-[#5546F0]">
                                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>
                                            </span>
                                        </span>
                                        <span class="flex flex-col gap-0.5">
                                            <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.career_pages.how_profile_badge') }}</span>
                                            <span class="text-[11px] text-gray-500">{{ __('talenma.career_pages.how_profile_badge_hint') }}</span>
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (__('talenma.career_pages.how_profile_tags') as $tag)
                                            <span class="rounded-full bg-[#EEF0FF] px-2.5 py-1.5 text-[11px] font-semibold text-[#4338CA]">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                    <span class="flex items-center gap-2 text-xs text-gray-600">
                                        <svg class="h-[15px] w-[15px] text-[#5546F0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        {{ __('talenma.career_pages.how_profile_visible') }}
                                    </span>
                                </div>
                            @else
                                <div class="how-lift flex flex-col gap-3 rounded-[18px] bg-white p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-[38px] w-[38px] items-center justify-center rounded-[10px] bg-[#F3EEFF] font-bold text-[#7C3AED]" aria-hidden="true">O</span>
                                        <span class="flex flex-col gap-0.5">
                                            <span class="text-[13px] font-semibold text-gray-900">{{ __('talenma.career_pages.how_job_company') }}</span>
                                            <span class="text-[11px] text-gray-500">{{ __('talenma.career_pages.how_job_meta') }}</span>
                                        </span>
                                    </div>
                                    <a href="{{ route('career.offers') }}" class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] px-3 py-2.5 text-[13px] font-semibold text-white">
                                        {{ __('talenma.career_pages.how_job_cta') }}
                                        {!! $arrow !!}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div @class(['lg:order-1' => $loop->iteration % 2 === 0])>
                    <div class="flex max-w-lg flex-col gap-5">
                        <span class="flex items-center gap-3.5">
                            <span class="how-num inline-flex h-[52px] w-[52px] items-center justify-center rounded-2xl text-[22px] font-extrabold text-white" style="--how-ink: {{ $visual['ink'] }}; background: {{ $visual['ink'] }}">{{ $loop->iteration }}</span>
                            <span class="text-sm font-bold uppercase tracking-[0.1em]" style="color: {{ $visual['ink'] }}">{{ $step['kicker'] }}</span>
                        </span>
                        <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-[2.5rem] sm:leading-[1.1]">{{ $step['title'] }}</h2>
                        <p class="text-base leading-relaxed text-gray-600 sm:text-lg">{{ $step['text'] }}</p>
                        <ul class="flex flex-col gap-3">
                            @foreach ($step['points'] as $point)
                                <li class="flex items-center gap-3 text-base text-gray-700">
                                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-white" style="background: {{ $visual['ink'] }}">{!! $check !!}</span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="relative overflow-hidden bg-[#0D0B2B]">
        <div class="how-mesh-light absolute inset-0" aria-hidden="true"></div>
        <div class="how-wash how-wash-violet -left-32 -top-32 h-[28rem] w-[28rem] opacity-70 sm:h-[700px] sm:w-[700px]" aria-hidden="true"></div>
        <div class="how-wash how-wash-indigo -right-24 top-24 h-[28rem] w-[28rem] opacity-45 sm:h-[700px] sm:w-[700px]" aria-hidden="true"></div>
        <div class="relative mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-16 sm:gap-11 sm:px-6 sm:py-24 lg:px-8">
            <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl sm:leading-[1.08]">
                {{ __('talenma.career_pages.how_reflexes_lead') }}
                <span class="text-[#EBBA6E]">{{ __('talenma.career_pages.how_reflexes_accent') }}</span>
            </h2>
            <ul class="grid gap-6 md:grid-cols-3">
                @foreach (__('talenma.career_pages.how_reflexes') as $reflex)
                    <li class="relative flex flex-col gap-3.5 rounded-3xl border border-white/10 bg-white/[0.06] p-8">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] text-white">
                            @if ($loop->iteration === 1)
                                <svg class="h-[26px] w-[26px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h4"/></svg>
                            @elseif ($loop->iteration === 2)
                                <svg class="h-[26px] w-[26px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>
                            @else
                                <svg class="h-[26px] w-[26px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            @endif
                        </span>
                        <h3 class="text-xl font-bold text-white">{{ $reflex['title'] }}</h3>
                        <p class="text-[15px] leading-relaxed text-[#C7D2FE]">{{ $reflex['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="mx-auto grid w-full max-w-7xl gap-8 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-12 lg:gap-16 lg:px-8">
        <div class="flex flex-col gap-3.5 lg:col-span-4">
            <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-[2.75rem] sm:leading-[1.08]">{{ __('talenma.career_pages.how_faq_title') }}</h2>
            <a href="{{ route('faq') }}" class="inline-flex w-fit items-center gap-2 self-start rounded-full bg-gray-100 px-[18px] py-3 text-[15px] font-semibold text-gray-900 hover:bg-gray-200">
                {{ __('talenma.career_pages.how_faq_all') }}
                {!! $arrow !!}
            </a>
        </div>
        <div class="flex flex-col gap-3.5 lg:col-span-8">
            @foreach (__('talenma.career_pages.how_faq') as $item)
                <details class="group rounded-[20px] border border-[#E8E9F0] bg-white px-6 py-5 sm:px-7 sm:py-6" @if ($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-base font-semibold text-gray-900 sm:text-lg [&::-webkit-details-marker]:hidden">
                        <span>{{ $item['q'] }}</span>
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xl text-gray-900 group-open:bg-[#0D0B2B] group-open:text-white">
                            <span class="group-open:hidden" aria-hidden="true">+</span>
                            <span class="hidden group-open:inline" aria-hidden="true">−</span>
                        </span>
                    </summary>
                    <p class="max-w-3xl pt-3 text-base leading-relaxed text-gray-600">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6 sm:pb-24 lg:px-8">
        <div class="relative flex flex-col gap-8 overflow-hidden rounded-[2rem] bg-gradient-to-r from-[#2563EB] via-[#5546F0] to-[#8B5CF6] px-6 py-10 sm:rounded-[2.5rem] sm:px-12 sm:py-14 lg:flex-row lg:items-center lg:justify-between lg:px-[4.5rem]">
            <div class="how-mesh-light absolute inset-0 opacity-80" aria-hidden="true"></div>
            <div class="how-wash how-wash-sky -right-10 -top-40 h-[28rem] w-[28rem] opacity-45" aria-hidden="true"></div>
            <h2 class="relative max-w-xl text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-[2.75rem] sm:leading-[1.08]">
                {{ __('talenma.career_pages.how_cta_title') }}
                <span class="text-[#EBBA6E]">{{ __('talenma.career_pages.how_cta_accent') }}</span>
            </h2>
            <div class="relative flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-7 py-4 text-base font-bold text-[#0D0B2B] hover:bg-indigo-50">{{ __('talenma.career_pages.how_cta_account') }}</a>
                <a href="{{ route('career.offers') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/50 px-7 py-4 text-base font-semibold text-white hover:bg-white/10">{{ __('talenma.career_pages.how_cta_offers') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
