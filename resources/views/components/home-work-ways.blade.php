@php
    $user = auth()->user();
    $offersUrl = ($user && $user->isTalent() && $user->isApproved())
        ? route('talent.jobs.index')
        : route('career.offers');

    $cards = [
        [
            'label' => __('talenma.home.ways_remote'),
            'image' => 'images/home/way-remote.jpg',
            'alt' => __('talenma.home.ways_remote_alt'),
            'position' => 'center 38%',
            'arrow' => '#5546F0',
        ],
        [
            'label' => __('talenma.home.ways_visa'),
            'image' => 'images/home/way-visa.jpg',
            'alt' => __('talenma.home.ways_visa_alt'),
            'position' => 'center 12%',
            'arrow' => '#7C3AED',
        ],
        [
            'label' => __('talenma.home.ways_local'),
            'image' => 'images/home/way-local.jpg',
            'alt' => __('talenma.home.ways_local_alt'),
            'position' => 'center 30%',
            'arrow' => '#2563EB',
        ],
    ];
@endphp

<section class="mt-16 lg:mt-20" aria-labelledby="home-ways-title">
    <h2 id="home-ways-title" class="text-[2rem] font-bold tracking-[-0.03em] text-slate-900 sm:text-4xl lg:text-[2.75rem]">
        {{ __('talenma.home.ways_title') }}
        <span class="text-[#5546F0]">{{ __('talenma.home.ways_title_accent') }}</span>
    </h2>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        @foreach ($cards as $card)
            <a href="{{ $offersUrl }}" class="flex flex-col overflow-hidden rounded-[28px] border border-[#EEF0F4] bg-white text-slate-900 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)] transition hover:-translate-y-0.5">
                <div class="relative h-52 overflow-hidden sm:h-[15.5rem]">
                    <img
                        src="{{ asset($card['image']) }}?v=2"
                        alt="{{ $card['alt'] }}"
                        class="h-full w-full object-cover"
                        style="object-position: {{ $card['position'] }}"
                    >
                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(160deg,rgba(85,70,240,0.10),rgba(124,58,237,0.18))]" aria-hidden="true"></div>
                </div>
                <div class="flex items-center justify-between gap-3 px-5 py-5 sm:px-6 sm:py-[1.35rem]">
                    <span class="text-lg font-bold tracking-tight sm:text-[1.3rem]">{{ $card['label'] }}</span>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-white" style="background: {{ $card['arrow'] }}; box-shadow: 0 10px 20px -8px {{ $card['arrow'] }}">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</section>
