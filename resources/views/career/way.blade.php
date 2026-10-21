@extends('layouts.public')

@php
    $copy = __('talenma.work_ways.modes.'.$key);
    $visuals = [
        'remote' => [
            'slug' => 'full-remote',
            'image' => 'images/home/way-remote.jpg',
            'position' => 'center 38%',
            'alt' => __('talenma.home.ways_remote_alt'),
            'label' => __('talenma.home.ways_remote'),
            'accent' => '#5546F0',
        ],
        'visa' => [
            'slug' => 'sponsorship-visa',
            'image' => 'images/home/way-visa.jpg',
            'position' => 'center 12%',
            'alt' => __('talenma.home.ways_visa_alt'),
            'label' => __('talenma.home.ways_visa'),
            'accent' => '#7C3AED',
        ],
        'local' => [
            'slug' => 'placement-local',
            'image' => 'images/home/way-local.jpg',
            'position' => 'center 30%',
            'alt' => __('talenma.home.ways_local_alt'),
            'label' => __('talenma.home.ways_local'),
            'accent' => '#2563EB',
        ],
    ];
    $visual = $visuals[$key];
    $others = collect($visuals)->except($key);
@endphp

@section('title', $copy['meta_title'])
@section('meta_description', $copy['meta_description'])

@section('content')
<section class="bg-slate-50 pb-16 pt-8 sm:pb-20 sm:pt-10" data-work-way="{{ $key }}">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}#facon-de-travailler" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            {{ __('talenma.work_ways.back') }}
        </a>

        <article class="mt-5 overflow-hidden rounded-[28px] border border-slate-200/80 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.06)]">
            <div class="relative h-64 sm:h-80">
                <img
                    src="{{ asset($visual['image']) }}?v=3"
                    alt="{{ $visual['alt'] }}"
                    class="h-full w-full object-cover"
                    style="object-position: {{ $visual['position'] }}"
                >
                <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(2, 6, 23, 0.92) 0%, rgba(2, 6, 23, 0.78) 32%, rgba(2, 6, 23, 0.12) 68%, rgba(2, 6, 23, 0) 100%)" aria-hidden="true"></div>
                <div class="absolute inset-x-0 bottom-0 px-6 pb-6 sm:px-10 sm:pb-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80">{{ $visual['label'] }}</p>
                    <h1 class="mt-2 max-w-3xl text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $copy['title'] }}</h1>
                </div>
            </div>
            <p class="px-6 py-7 text-base leading-relaxed text-slate-600 sm:px-10 sm:py-8 sm:text-lg">{{ $copy['lead'] }}</p>
        </article>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-8">
                <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ __('talenma.work_ways.means_title') }}</h2>
                <ul class="mt-5 space-y-3">
                    @foreach ($copy['means'] as $point)
                        <li class="flex items-start gap-3 text-sm leading-relaxed text-slate-700">
                            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-white" style="background: {{ $visual['accent'] }}" aria-hidden="true">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.2 7.2a1 1 0 0 1-1.4 0L3.3 9.1a1 1 0 1 1 1.4-1.4l4.1 4.1 6.5-6.5a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                            </span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-8" style="box-shadow: 0 18px 40px -24px {{ $visual['accent'] }}">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" style="color: {{ $visual['accent'] }}">{{ __('talenma.work_ways.eyebrow') }}</p>
                <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900">{{ __('talenma.work_ways.role_title') }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $copy['role_lead'] }}</p>
                <ul class="mt-5 space-y-4">
                    @foreach ($copy['role'] as $item)
                        <li>
                            <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $item['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <section class="mt-6 rounded-[28px] border border-slate-200/80 bg-white p-6 sm:p-8">
            <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ __('talenma.work_ways.steps_title') }}</h2>
            <ol class="mt-5 grid gap-4 lg:grid-cols-3">
                @foreach ($copy['steps'] as $step)
                    <li class="rounded-2xl bg-slate-50 p-4">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white" style="background: {{ $visual['accent'] }}">{{ $loop->iteration }}</span>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $step['who'] }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $step['title'] }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
            <p class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-600">{{ $copy['boundary'] }}</p>
        </section>

        <div class="mt-6 flex flex-col gap-6 rounded-[28px] px-6 py-8 text-white sm:flex-row sm:items-center sm:justify-between sm:px-8" style="background: {{ $visual['accent'] }}">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold tracking-tight">{{ __('talenma.work_ways.cta_title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-white/85">{{ __('talenma.work_ways.cta_text') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                    {{ __('talenma.work_ways.cta_account') }}
                </a>
                <a href="{{ $offersUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    {{ __('talenma.work_ways.cta_offers') }}
                </a>
            </div>
        </div>

        <section class="mt-10">
            <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ __('talenma.work_ways.others_title') }}</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach ($others as $otherKey => $other)
                    <a href="{{ route('career.ways.show', $other['slug']) }}" class="group flex overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-sm transition hover:-translate-y-0.5">
                        <img
                            src="{{ asset($other['image']) }}?v=3"
                            alt=""
                            class="h-28 w-32 shrink-0 object-cover sm:h-auto sm:w-40"
                            style="object-position: {{ $other['position'] }}"
                        >
                        <span class="flex flex-1 items-center justify-between gap-3 px-4 py-4">
                            <span class="text-base font-bold tracking-tight text-slate-900">{{ $other['label'] }}</span>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white" style="background: {{ $other['accent'] }}" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</section>
@endsection
