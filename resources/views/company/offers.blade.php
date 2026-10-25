@extends('layouts.public')

@section('title', __('talenma.company_plans.meta_title'))
@section('meta_description', __('talenma.company_plans.meta_description'))

@php
    $demoUrl = route('company.demo');
    $trialUrl = route('company.trial');

    $plans = [
        [
            'key' => 'trial',
            'price' => __('talenma.company_plans.price_trial_label'),
            'period' => '',
            'cta' => __('talenma.company_plans.cta_trial'),
            'href' => $trialUrl,
            'featured' => false,
            'tone' => 'slate',
        ],
        [
            'key' => 'essential',
            'price' => __('talenma.company_plans.price_essential_label'),
            'period' => '',
            'cta' => __('talenma.company_plans.cta_demo'),
            'href' => $demoUrl,
            'featured' => false,
            'tone' => 'indigo',
        ],
        [
            'key' => 'pro',
            'price' => __('talenma.company_plans.price_pro_label'),
            'period' => '',
            'cta' => __('talenma.company_plans.cta_demo'),
            'href' => $demoUrl,
            'featured' => true,
            'tone' => 'violet',
        ],
    ];

    $compareRows = [
        ['row' => 'page', 'trial' => 'page_trial', 'essential' => 'page_essential', 'pro' => 'page_pro'],
        ['row' => 'jobs', 'trial' => 'jobs_trial', 'essential' => 'jobs_essential', 'pro' => 'jobs_pro'],
        ['row' => 'applications', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'catalogue', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'direct_hire', 'trial' => 'yes', 'essential' => 'yes', 'pro' => 'yes'],
        ['row' => 'sourcing', 'trial' => 'no', 'essential' => 'no', 'pro' => 'sourcing_pro'],
        ['row' => 'brand', 'trial' => 'no', 'essential' => 'no', 'pro' => 'yes'],
        ['row' => 'support', 'trial' => 'support_std', 'essential' => 'support_std', 'pro' => 'support_prio'],
    ];
@endphp

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-white via-sky-50 to-sky-100" data-company-offers>
    <div class="home-shell pb-16 pt-8 sm:pt-[4.5rem] lg:pb-20 lg:pt-[5.5rem]">
        <div class="company-offers-frame">
        <div class="max-w-3xl">
            <p class="company-plans-badge inline-flex items-center rounded-full bg-indigo-600 px-4 text-xs font-bold uppercase tracking-[0.14em] text-white shadow-sm shadow-indigo-500/25">
                {{ __('talenma.company_plans.badge') }}
            </p>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-[2.6rem] lg:leading-[1.15]">
                {{ __('talenma.company_plans.title') }}
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-lg">
                {{ __('talenma.company_plans.intro') }}
            </p>
        </div>

        <div class="mt-10 grid gap-5 lg:grid-cols-3 lg:gap-6">
            @foreach ($plans as $plan)
                @php
                    $copy = __('talenma.company_plans.plans.'.$plan['key']);
                    $cardClass = $plan['featured']
                        ? 'relative flex flex-col rounded-[1.75rem] border-2 border-indigo-600 bg-white p-6 shadow-[0_20px_50px_-24px_rgba(79,70,229,0.45)] sm:p-7'
                        : 'relative flex flex-col rounded-[1.75rem] border border-slate-200/90 bg-white/95 p-6 shadow-[0_12px_36px_-20px_rgba(15,23,42,0.18)] sm:p-7';
                @endphp
                <article @class([$cardClass])>
                    @if ($plan['featured'])
                        <span class="absolute -top-3 left-6 inline-flex rounded-full bg-indigo-600 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white">
                            {{ __('talenma.company_plans.recommended') }}
                        </span>
                    @endif
                    <h2 class="text-xl font-bold tracking-tight text-slate-950">{{ $copy['name'] }}</h2>
                    <p class="mt-1.5 min-h-[2.875rem] text-sm leading-relaxed text-slate-500">{{ $copy['tagline'] }}</p>
                    <p class="mt-5 flex min-h-[2.5rem] items-center">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">{{ $plan['price'] }}</span>
                        @if ($plan['period'] !== '')
                            <span class="ml-2 text-sm font-medium text-slate-500">{{ $plan['period'] }}</span>
                        @endif
                    </p>
                    <ul class="mt-6 flex-1 space-y-2.5">
                        @foreach ($copy['features'] as $feature)
                            <li class="flex items-start gap-2.5 text-sm leading-snug text-slate-700">
                                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-700" aria-hidden="true">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.2 7.2a1 1 0 0 1-1.4 0L3.3 9.1a1 1 0 1 1 1.4-1.4l4.1 4.1 6.5-6.5a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                                </span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a
                        href="{{ $plan['href'] }}"
                        @class([
                            'mt-7 inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold transition',
                            'bg-indigo-600 text-white hover:bg-indigo-700' => $plan['featured'],
                            'border-2 border-slate-900 bg-slate-900 text-white hover:bg-slate-800' => ! $plan['featured'] && $plan['key'] === 'trial',
                            'border-2 border-indigo-600 text-indigo-700 hover:bg-indigo-50' => ! $plan['featured'] && $plan['key'] !== 'trial',
                        ])
                    >{{ $plan['cta'] }}</a>
                </article>
            @endforeach
        </div>

        <div class="mt-6 rounded-[1.5rem] border border-slate-200/90 bg-white/80 px-5 py-5 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:px-7">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-slate-950">{{ __('talenma.company_plans.custom.title') }}</h2>
                <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-600">{{ __('talenma.company_plans.custom.text') }}</p>
            </div>
            <a href="{{ $demoUrl }}" class="mt-4 inline-flex shrink-0 items-center justify-center rounded-xl border-2 border-slate-900 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-900 hover:text-white sm:mt-0">
                {{ __('talenma.company_plans.cta_custom') }}
            </a>
        </div>

        <div class="mt-14" id="comparer">
            <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ __('talenma.company_plans.compare.title') }}</h2>
            <div class="mt-5 overflow-x-auto rounded-[1.5rem] border border-slate-200/90 bg-white shadow-sm">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-slate-600">
                            <th scope="col" class="px-4 py-3.5 font-semibold sm:px-5">{{ __('talenma.company_plans.compare.feature') }}</th>
                            <th scope="col" class="px-3 py-3.5 font-semibold">{{ __('talenma.company_plans.plans.trial.name') }}</th>
                            <th scope="col" class="px-3 py-3.5 font-semibold">{{ __('talenma.company_plans.plans.essential.name') }}</th>
                            <th scope="col" class="px-3 py-3.5 font-semibold text-indigo-700">{{ __('talenma.company_plans.plans.pro.name') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($compareRows as $row)
                            <tr>
                                <th scope="row" class="px-4 py-3.5 font-medium text-slate-800 sm:px-5">{{ __('talenma.company_plans.compare.rows.'.$row['row']) }}</th>
                                <td class="px-3 py-3.5 text-slate-600">{{ __('talenma.company_plans.compare.values.'.$row['trial']) }}</td>
                                <td class="px-3 py-3.5 text-slate-600">{{ __('talenma.company_plans.compare.values.'.$row['essential']) }}</td>
                                <td class="px-3 py-3.5 font-medium text-slate-900">{{ __('talenma.company_plans.compare.values.'.$row['pro']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-14 overflow-hidden rounded-[1.75rem] bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-8 text-white shadow-[0_20px_48px_-24px_rgba(79,70,229,0.55)] sm:px-8 sm:py-9">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ __('talenma.company_plans.trial.title') }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-indigo-100 sm:text-base">{{ __('talenma.company_plans.trial.text') }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $trialUrl }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-indigo-50">
                        {{ __('talenma.company_plans.trial.cta_trial') }}
                    </a>
                    <a href="{{ $demoUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/35 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ __('talenma.company_plans.trial.cta_demo') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-14 max-w-3xl">
            <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ __('talenma.company_plans.faq.title') }}</h2>
            <div class="mt-5 space-y-3">
                @foreach (__('talenma.company_plans.faq.items') as $item)
                    <details class="group rounded-2xl border border-slate-200/90 bg-white px-5 py-1 open:shadow-sm">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 py-3.5 text-sm font-semibold text-slate-900 marker:content-none [&::-webkit-details-marker]:hidden">
                            <span>{{ $item['q'] }}</span>
                            <span class="text-lg font-medium text-slate-400 group-open:hidden" aria-hidden="true">+</span>
                            <span class="hidden text-lg font-medium text-slate-400 group-open:inline" aria-hidden="true">−</span>
                        </summary>
                        <p class="pb-4 text-sm leading-relaxed text-slate-600">{{ $item['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-6 rounded-[1.75rem] border border-slate-200/90 bg-white px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ __('talenma.company_plans.final.title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('talenma.company_plans.final.text') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $trialUrl }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('talenma.company_plans.cta_trial') }}
                </a>
                <a href="{{ $demoUrl }}" class="inline-flex items-center justify-center rounded-xl border-2 border-indigo-600 px-4 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
                    {{ __('talenma.company_plans.cta_demo') }}
                </a>
            </div>
        </div>
        </div>
    </div>
</section>
@endsection
