@extends('layouts.public')

@section('title', __('talenma.career_pages.how_meta_title'))
@section('meta_description', __('talenma.career_pages.how_meta_description'))

@php
    $stepTones = [
        1 => ['card' => 'border-indigo-100 bg-indigo-50/80', 'kicker' => 'text-indigo-600', 'check' => 'bg-white text-indigo-700', 'pill' => 'border-indigo-100 bg-indigo-50 hover:bg-indigo-100', 'num' => 'bg-indigo-600'],
        2 => ['card' => 'border-violet-100 bg-violet-50/80', 'kicker' => 'text-violet-600', 'check' => 'bg-white text-violet-700', 'pill' => 'border-violet-100 bg-violet-50 hover:bg-violet-100', 'num' => 'bg-violet-600'],
        3 => ['card' => 'border-emerald-100 bg-emerald-50/80', 'kicker' => 'text-emerald-700', 'check' => 'bg-white text-emerald-700', 'pill' => 'border-emerald-100 bg-emerald-50 hover:bg-emerald-100', 'num' => 'bg-emerald-600'],
        4 => ['card' => 'border-amber-100 bg-amber-50/90', 'kicker' => 'text-amber-700', 'check' => 'bg-white text-amber-700', 'pill' => 'border-amber-100 bg-amber-50 hover:bg-amber-100', 'num' => 'bg-amber-500'],
    ];
    $reflexTones = [
        'bg-indigo-100 text-indigo-700',
        'bg-emerald-100 text-emerald-700',
        'bg-amber-100 text-amber-700',
    ];
    $reflexCards = [
        'border-indigo-100 bg-indigo-50/80',
        'border-emerald-100 bg-emerald-50/80',
        'border-amber-100 bg-amber-50/90',
    ];
@endphp

@section('content')
<section class="relative overflow-hidden bg-slate-50 pb-14 pt-10 sm:pb-16 sm:pt-12" data-career-how>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-gradient-to-b from-indigo-100 via-violet-50/70 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <h1 class="max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            {{ __('talenma.career_pages.how_title') }}
        </h1>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            {{ __('talenma.career_pages.how_intro') }}
        </p>

        <ol class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach (__('talenma.career_pages.how_phases') as $phase)
                <li>
                    <a href="#how-step-{{ $phase['n'] }}" class="flex items-center gap-3 rounded-2xl border px-3.5 py-3 shadow-[0_8px_24px_rgba(79,70,229,0.06)] transition {{ $stepTones[$loop->iteration]['pill'] }}">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white {{ $stepTones[$loop->iteration]['num'] }}">{{ $phase['n'] }}</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $phase['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ol>

        <div class="mt-8 space-y-6">
            @foreach (__('talenma.career_pages.how_steps') as $step)
                <article id="how-step-{{ $loop->iteration }}" class="grid items-center gap-6 rounded-[1.6rem] border p-5 shadow-[0_10px_30px_rgba(79,70,229,0.05)] sm:p-7 lg:grid-cols-2 lg:gap-10 {{ $stepTones[$loop->iteration]['card'] }}">
                    <div @class(['lg:order-2' => $loop->iteration % 2 === 0])>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] {{ $stepTones[$loop->iteration]['kicker'] }}">{{ $step['kicker'] }}</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $step['title'] }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['text'] }}</p>
                        <ul class="mt-4 space-y-2">
                            @foreach ($step['points'] as $point)
                                <li class="flex items-start gap-2.5 text-sm text-slate-700">
                                    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $stepTones[$loop->parent->iteration]['check'] }}" aria-hidden="true">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.2 7.2a1 1 0 0 1-1.4 0L3.3 9.1a1 1 0 1 1 1.4-1.4l4.1 4.1 6.5-6.5a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                                    </span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div @class(['lg:order-1' => $loop->iteration % 2 === 0])>
                        @if ($loop->iteration === 1)
                            <div class="rounded-2xl bg-white/80 p-5 ring-1 ring-indigo-100">
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3 text-sm text-slate-500">{{ __('talenma.career_pages.how_account_email') }}</div>
                                <div class="mt-3 flex items-center gap-3 rounded-xl border border-indigo-100 bg-white px-4 py-3">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-[10px] font-bold text-rose-600">PDF</span>
                                    <span class="text-sm font-medium text-slate-800">{{ __('talenma.career_pages.how_account_file') }}</span>
                                </div>
                                <a href="{{ route('register') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                    {{ __('talenma.career_pages.how_account_cta') }}
                                </a>
                            </div>
                        @elseif ($loop->iteration === 2)
                            <div class="rounded-2xl bg-white/80 p-5 ring-1 ring-violet-100">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-800">{{ __('talenma.career_pages.how_profile_label') }}</p>
                                    <p class="text-sm font-bold text-violet-700">{{ __('talenma.career_pages.how_profile_percent') }}</p>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-violet-100" aria-hidden="true">
                                    <div class="h-full w-4/5 rounded-full bg-violet-600"></div>
                                </div>
                                <p class="mt-4 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('talenma.career_pages.how_profile_skills_label') }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach (__('talenma.career_pages.how_profile_skills') as $skill)
                                        <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-medium text-violet-800">{{ $skill }}</span>
                                    @endforeach
                                </div>
                                <p class="mt-4 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('talenma.career_pages.how_profile_languages_label') }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach (__('talenma.career_pages.how_profile_languages') as $language)
                                        <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-800">{{ $language }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($loop->iteration === 3)
                            <div class="rounded-2xl bg-white/80 p-5 ring-1 ring-emerald-100">
                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    {{ __('talenma.career_pages.how_profile_badge') }}
                                </span>
                                <p class="mt-3 text-sm text-slate-500">{{ __('talenma.career_pages.how_profile_badge_hint') }}</p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach (__('talenma.career_pages.how_profile_tags') as $tag)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-800">{{ $tag }}</span>
                                    @endforeach
                                </div>
                                <p class="mt-4 text-sm font-medium text-slate-700">{{ __('talenma.career_pages.how_profile_visible') }}</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-white/80 p-5 ring-1 ring-amber-100">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-500 text-lg font-bold text-white" aria-hidden="true">O</span>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ __('talenma.career_pages.how_job_company') }}</p>
                                        <p class="mt-0.5 text-sm text-slate-500">{{ __('talenma.career_pages.how_job_meta') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('career.offers') }}" class="mt-4 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-800">
                                    {{ __('talenma.career_pages.how_job_cta') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('talenma.career_pages.how_reflexes_title') }}</h2>
            <ul class="mt-5 grid gap-4 md:grid-cols-3">
                @foreach (__('talenma.career_pages.how_reflexes') as $reflex)
                    <li class="rounded-[1.4rem] border p-5 shadow-[0_10px_30px_rgba(79,70,229,0.05)] {{ $reflexCards[$loop->index] }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $reflexTones[$loop->index] }}">{{ $loop->iteration }}</span>
                        <h3 class="mt-4 text-base font-bold text-slate-900">{{ $reflex['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $reflex['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-10">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('talenma.career_pages.how_faq_title') }}</h2>
                <a href="{{ route('faq') }}" class="shrink-0 text-sm font-semibold text-indigo-700 hover:text-indigo-800">{{ __('talenma.career_pages.how_faq_all') }}</a>
            </div>
            <div class="mt-4 divide-y divide-indigo-100 rounded-[1.4rem] border border-indigo-100 bg-white/80 px-5">
                @foreach (__('talenma.career_pages.how_faq') as $item)
                    <details class="group" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-4 text-sm font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                            <span>{{ $item['q'] }}</span>
                            <span class="text-lg font-medium text-slate-400 group-open:hidden" aria-hidden="true">+</span>
                            <span class="hidden text-lg font-medium text-slate-400 group-open:inline" aria-hidden="true">−</span>
                        </summary>
                        <p class="pb-4 text-sm leading-relaxed text-slate-600">{{ $item['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-6 rounded-[1.6rem] bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-8 text-white shadow-[0_16px_40px_rgba(79,70,229,0.25)] sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                {{ __('talenma.career_pages.how_cta_title') }}
                <span class="text-indigo-100">{{ __('talenma.career_pages.how_cta_accent') }}</span>
            </h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-indigo-50">
                    {{ __('talenma.career_pages.how_cta_account') }}
                </a>
                <a href="{{ route('career.offers') }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    {{ __('talenma.career_pages.how_cta_offers') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
