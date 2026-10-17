@php
    $user = auth()->user();
    $profileUrl = route('login');

    if ($user) {
        $profileUrl = ($user->isTalent() && $user->isApproved())
            ? route('profile.details.edit')
            : route('profile.edit');
    }

    $accountUrl = $user ? $profileUrl : route('register');
    $accountLabel = $user
        ? __('talenma.home.steps_notice_cta')
        : __('talenma.home.hero_create_account');

    $cards = [
        [
            'n' => '1',
            'title' => __('talenma.home.steps_1_title'),
            'desc' => __('talenma.home.steps_1_desc'),
            'image' => 'images/home/step-account.jpg',
            'scale' => false,
        ],
        [
            'n' => '2',
            'title' => __('talenma.home.steps_2_title'),
            'desc' => __('talenma.home.steps_2_desc'),
            'image' => 'images/home/step-visible.jpg',
            'scale' => false,
        ],
        [
            'n' => '3',
            'title' => __('talenma.home.steps_3_title'),
            'desc' => __('talenma.home.steps_3_desc'),
            'image' => 'images/home/step-apply.jpg',
            'scale' => true,
        ],
    ];
@endphp

<section class="mt-16 lg:mt-20" aria-labelledby="home-steps-title">
    <div class="text-center">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
            {{ __('talenma.home.steps_eyebrow') }}
        </p>
        <h2 id="home-steps-title" class="mx-auto mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.7rem] lg:leading-[1.15]">
            {{ __('talenma.home.steps_title') }}
        </h2>
    </div>

    <div class="mt-8 grid gap-4 sm:mt-10 sm:grid-cols-3 sm:gap-5">
        @foreach ($cards as $card)
            <article class="flex flex-col overflow-hidden rounded-[1.35rem] bg-white shadow-[0_16px_40px_rgba(79,70,229,0.10)] ring-1 ring-slate-200/70">
                <div class="relative h-40 overflow-hidden sm:h-[11.5rem]">
                    <img
                        src="{{ asset($card['image']) }}"
                        alt=""
                        class="h-full w-full object-cover {{ $card['scale'] ? 'origin-center scale-[2.15]' : '' }}"
                    >
                    <span class="absolute left-3 top-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/95 text-sm font-bold text-indigo-600 shadow-sm">{{ $card['n'] }}</span>
                </div>
                <div class="px-4 py-3.5 sm:px-5 sm:py-4">
                    <h3 class="text-base font-bold tracking-tight text-slate-900 sm:text-[1.05rem]">{{ $card['title'] }}</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $card['desc'] }}</p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ $accountUrl }}" class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ $accountLabel }}
        </a>
    </div>
</section>
