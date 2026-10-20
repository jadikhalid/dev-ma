@php
    $user = auth()->user();
    $profileUrl = route('login');

    if ($user) {
        $profileUrl = ($user->isTalent() && $user->isApproved())
            ? route('profile.details.edit')
            : route('profile.edit');
    }

    $accountUrl = $user ? $profileUrl : route('register');
    $offersUrl = ($user && $user->isTalent() && $user->isApproved())
        ? route('talent.jobs.index')
        : route('career.offers');

    $cards = [
        ['tone' => '#EEF0FF', 'ink' => '#5546F0', 'label' => __('talenma.home.steps_1_label')],
        ['tone' => '#EAF1FF', 'ink' => '#2563EB', 'label' => __('talenma.home.steps_2_label')],
        ['tone' => '#F3EEFF', 'ink' => '#7C3AED', 'label' => __('talenma.home.steps_3_label')],
    ];
@endphp

<section class="mt-16 lg:mt-20" aria-labelledby="home-steps-title">
    <div class="text-center">
        <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5546F0]">
            {{ __('talenma.home.steps_eyebrow') }}
        </p>
        <h2 id="home-steps-title" class="mx-auto mt-2.5 max-w-3xl text-[2rem] font-bold leading-[1.1] tracking-[-0.03em] text-slate-950 sm:text-[2.875rem]">
            {{ __('talenma.home.steps_title') }}
        </h2>
    </div>

    <div class="mt-10 grid gap-5 lg:grid-cols-3">
        <article class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] p-7" style="background: {{ $cards[0]['tone'] }}">
            <span class="pointer-events-none absolute inset-0" aria-hidden="true" style="background-image: linear-gradient({{ $cards[0]['ink'] }}12 1px, transparent 1px), linear-gradient(90deg, {{ $cards[0]['ink'] }}12 1px, transparent 1px), radial-gradient(circle, {{ $cards[0]['ink'] }}2d 1.6px, transparent 1.7px); background-size: 44px 44px;"></span>
            <div class="relative flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-[14px] text-lg font-bold text-white shadow-[0_10px_20px_-10px_#5546F0]" style="background: {{ $cards[0]['ink'] }}">1</span>
                <span class="text-[13px] font-semibold" style="color: {{ $cards[0]['ink'] }}">{{ $cards[0]['label'] }}</span>
            </div>
            <div class="relative flex flex-col gap-2.5 rounded-[18px] bg-white p-4 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                <span class="rounded-[10px] border border-[#E5E7EB] px-3 py-2.5 text-[13px] text-[#6B7280]">{{ __('talenma.home.steps_1_email') }}</span>
                <span class="flex items-center gap-2.5 rounded-[10px] border border-[#E0E4FF] bg-[#F5F6FF] px-3 py-2.5">
                    <span class="flex h-[30px] w-[30px] items-center justify-center rounded-lg bg-violet-100 text-[9px] font-extrabold text-violet-700">PDF</span>
                    <span class="min-w-0 flex-1 truncate text-xs font-semibold text-slate-900">{{ __('talenma.home.steps_1_file') }}</span>
                    <span class="flex h-[22px] w-[22px] items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true">
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
                    </span>
                </span>
                <a href="{{ $accountUrl }}" class="rounded-[10px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 px-3 py-2.5 text-center text-[13px] font-semibold text-white">
                    {{ __('talenma.home.hero_create_account') }}
                </a>
            </div>
            <div class="relative">
                <h3 class="text-[22px] font-bold text-slate-950">{{ __('talenma.home.steps_1_title') }}</h3>
                <p class="mt-1.5 text-[15px] leading-relaxed text-[#4B5563]">{{ __('talenma.home.steps_1_desc') }}</p>
            </div>
        </article>

        <article class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] p-7" style="background: {{ $cards[1]['tone'] }}">
            <span class="pointer-events-none absolute inset-0" aria-hidden="true" style="background-image: linear-gradient({{ $cards[1]['ink'] }}12 1px, transparent 1px), linear-gradient(90deg, {{ $cards[1]['ink'] }}12 1px, transparent 1px), radial-gradient(circle, {{ $cards[1]['ink'] }}2d 1.6px, transparent 1.7px); background-size: 44px 44px;"></span>
            <div class="relative flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-[14px] text-lg font-bold text-white shadow-[0_10px_20px_-10px_#2563EB]" style="background: {{ $cards[1]['ink'] }}">2</span>
                <span class="text-[13px] font-semibold" style="color: {{ $cards[1]['ink'] }}">{{ $cards[1]['label'] }}</span>
            </div>
            <div class="relative flex flex-col gap-3 rounded-[18px] bg-white p-4 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                <div class="flex items-center gap-3">
                    <span class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-500 p-[3px]">
                        <span class="flex h-full w-full items-center justify-center rounded-full bg-white text-indigo-600">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>
                        </span>
                    </span>
                    <span>
                        <span class="block text-[13px] font-bold text-slate-950">{{ __('talenma.home.steps_2_badge') }}</span>
                        <span class="block text-[11px] text-[#6B7280]">{{ __('talenma.home.steps_2_badge_hint') }}</span>
                    </span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <span class="rounded-full bg-[#EEF0FF] px-2.5 py-1.5 text-[11px] font-semibold text-indigo-700">{{ __('talenma.home.steps_2_tag_remote') }}</span>
                    <span class="rounded-full bg-[#EEF0FF] px-2.5 py-1.5 text-[11px] font-semibold text-indigo-700">{{ __('talenma.home.steps_2_tag_visa') }}</span>
                    <span class="rounded-full bg-[#EEF0FF] px-2.5 py-1.5 text-[11px] font-semibold text-indigo-700">{{ __('talenma.home.steps_2_tag_city') }}</span>
                </div>
                <span class="flex items-center gap-2 text-xs text-[#4B5563]">
                    <svg class="h-[15px] w-[15px] text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ __('talenma.home.steps_2_visible') }}
                </span>
            </div>
            <div class="relative">
                <h3 class="text-[22px] font-bold text-slate-950">{{ __('talenma.home.steps_2_title') }}</h3>
                <p class="mt-1.5 text-[15px] leading-relaxed text-[#4B5563]">{{ __('talenma.home.steps_2_desc') }}</p>
            </div>
        </article>

        <article class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] p-7" style="background: {{ $cards[2]['tone'] }}">
            <span class="pointer-events-none absolute inset-0" aria-hidden="true" style="background-image: linear-gradient({{ $cards[2]['ink'] }}12 1px, transparent 1px), linear-gradient(90deg, {{ $cards[2]['ink'] }}12 1px, transparent 1px), radial-gradient(circle, {{ $cards[2]['ink'] }}2d 1.6px, transparent 1.7px); background-size: 44px 44px;"></span>
            <div class="relative flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-[14px] text-lg font-bold text-white shadow-[0_10px_20px_-10px_#7C3AED]" style="background: {{ $cards[2]['ink'] }}">3</span>
                <span class="text-[13px] font-semibold" style="color: {{ $cards[2]['ink'] }}">{{ $cards[2]['label'] }}</span>
            </div>
            <div class="relative flex flex-col gap-3 rounded-[18px] bg-white p-4 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                <div class="flex items-center gap-3">
                    <span class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-[10px] bg-[#F3EEFF] font-bold text-violet-600">O</span>
                    <span>
                        <span class="block text-[13px] font-semibold text-slate-950">{{ __('talenma.home.steps_3_company') }}</span>
                        <span class="block text-[11px] text-[#6B7280]">{{ __('talenma.home.steps_3_meta') }}</span>
                    </span>
                </div>
                <a href="{{ $offersUrl }}" class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 px-3 py-2.5 text-[13px] font-semibold text-white">
                    {{ __('talenma.home.steps_3_cta') }}
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
            <div class="relative">
                <h3 class="text-[22px] font-bold text-slate-950">{{ __('talenma.home.steps_3_title') }}</h3>
                <p class="mt-1.5 text-[15px] leading-relaxed text-[#4B5563]">{{ __('talenma.home.steps_3_desc') }}</p>
            </div>
        </article>
    </div>

    <div class="mt-5 flex flex-col items-start gap-4 rounded-[20px] border border-[#E8E9F0] bg-white px-6 py-5 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)] sm:flex-row sm:items-center">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-[#7DD3FC] text-[#0D0B2B]" aria-hidden="true">
            <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>
        </span>
        <p class="min-w-0 flex-1 text-base leading-relaxed text-[#374151]">
            <strong class="text-slate-950">{{ __('talenma.home.steps_notice_lead') }}</strong>
            {{ __('talenma.home.steps_notice_body') }}
        </p>
        <a href="{{ $profileUrl }}" class="inline-flex shrink-0 items-center rounded-[14px] bg-slate-950 px-5 py-3.5 text-[15px] font-semibold text-white transition hover:bg-slate-800">
            {{ __('talenma.home.steps_notice_cta') }}
        </a>
    </div>
</section>
