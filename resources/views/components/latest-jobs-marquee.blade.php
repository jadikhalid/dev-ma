@props([
    'jobs',
    'indexUrl',
])

@php
    $tones = [
        ['bg' => '#EEF0FF', 'accent' => '#5546F0'],
        ['bg' => '#F3EEFF', 'accent' => '#7C3AED'],
        ['bg' => '#EAF1FF', 'accent' => '#2563EB'],
    ];
@endphp

@if ($jobs->isNotEmpty())
    <section class="mt-14 w-full lg:mt-16" aria-labelledby="home-offers-title" data-initial-count="{{ $jobs->count() }}">
        <div class="flex items-end justify-between gap-6">
            <h2 id="home-offers-title" class="text-[2rem] font-bold tracking-[-0.03em] text-slate-950 sm:text-[2.75rem]">
                {{ __('talenma.home.latest_jobs_title') }}
            </h2>
            <a
                href="{{ $indexUrl }}"
                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#F3F4F6] px-[18px] py-3 text-[15px] font-semibold text-slate-950 transition hover:bg-slate-200"
            >
                {{ __('talenma.home.latest_jobs_view_all') }}
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($jobs as $job)
                @php
                    $tone = $tones[$loop->index % count($tones)];
                @endphp
                <a
                    href="{{ $job['url'] }}"
                    class="relative flex flex-col gap-[18px] overflow-hidden rounded-3xl p-6 text-slate-950"
                    style="background: {{ $tone['bg'] }}"
                >
                    <span class="pointer-events-none absolute inset-0" aria-hidden="true" style="background-image: linear-gradient({{ $tone['accent'] }}14 1px, transparent 1px), linear-gradient(90deg, {{ $tone['accent'] }}14 1px, transparent 1px), radial-gradient(circle, {{ $tone['accent'] }}27 1.6px, transparent 1.7px); background-size: 40px 40px, 40px 40px, 40px 40px;"></span>
                    <span class="relative flex items-center justify-between gap-3">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-[0_8px_18px_-10px_rgba(17,24,39,0.4)]">
                            @if ($job['logo_url'])
                                <img src="{{ $job['logo_url'] }}" alt="" class="max-h-9 max-w-9 object-contain" loading="lazy" decoding="async">
                            @else
                                <span class="text-sm font-bold" style="color: {{ $tone['accent'] }}" aria-hidden="true">{{ $job['company_initials'] }}</span>
                            @endif
                        </span>
                        @if (! empty($job['badge']))
                            <span class="rounded-full bg-white px-[11px] py-1.5 text-xs font-semibold" style="color: {{ $tone['accent'] }}">{{ $job['badge'] }}</span>
                        @endif
                    </span>
                    <span class="relative flex flex-col gap-1.5">
                        <span class="text-sm text-[#6B7280]">{{ $job['company'] }}</span>
                        <span class="text-[19px] font-bold leading-snug">{{ $job['title'] }}</span>
                    </span>
                    <span class="relative mt-auto inline-flex w-fit items-center gap-2 self-start rounded-full bg-white px-4 py-2.5 text-sm font-semibold" style="color: {{ $tone['accent'] }}">
                        {{ __('talenma.home.latest_jobs_open') }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif
