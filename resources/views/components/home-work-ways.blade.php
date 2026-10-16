@php
    $cards = [
        [
            'key' => 'remote',
            'label' => __('talenma.home.ways_remote'),
            'desc' => __('talenma.home.ways_remote_desc'),
            'image' => 'images/home/way-remote.jpg',
        ],
        [
            'key' => 'visa',
            'label' => __('talenma.home.ways_visa'),
            'desc' => __('talenma.home.ways_visa_desc'),
            'image' => 'images/home/way-visa.jpg',
        ],
        [
            'key' => 'local',
            'label' => __('talenma.home.ways_local'),
            'desc' => __('talenma.home.ways_local_desc'),
            'image' => 'images/home/way-local.jpg',
        ],
    ];
@endphp

<section class="mt-16 lg:mt-20" aria-labelledby="home-ways-title">
    <div class="text-center">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
            {{ __('talenma.home.ways_eyebrow') }}
        </p>
        <h2 id="home-ways-title" class="mx-auto mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.7rem] lg:leading-[1.15]">
            {{ __('talenma.home.ways_title') }} {{ __('talenma.home.ways_title_accent') }}
        </h2>
    </div>

    <div class="mt-8 grid gap-4 sm:mt-10 sm:grid-cols-3 sm:gap-5">
        @foreach ($cards as $card)
            <article class="flex flex-col overflow-hidden rounded-[1.35rem] bg-white shadow-[0_16px_40px_rgba(79,70,229,0.10)] ring-1 ring-slate-200/70">
                <div class="h-40 overflow-hidden sm:h-[11.5rem]">
                    <img
                        src="{{ asset($card['image']) }}"
                        alt=""
                        class="h-full w-full object-cover {{ $card['key'] === 'remote' ? 'origin-center scale-[1.72]' : '' }}"
                    >
                </div>
                <div class="px-4 py-3.5 sm:px-5 sm:py-4">
                    <p class="text-base font-bold tracking-tight text-slate-900 sm:text-[1.05rem]">{{ $card['label'] }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $card['desc'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>
