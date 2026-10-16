@php
    $user = auth()->user();
    $profileUrl = route('login');

    if ($user) {
        $profileUrl = ($user->isTalent() && $user->isApproved())
            ? route('profile.details.edit')
            : route('profile.edit');
    }

    $accountUrl = $user ? $profileUrl : route('register');
    $cardGrid = 'pointer-events-none absolute inset-0 opacity-70';
    $cardGridStyle = 'background-image: linear-gradient(to right, rgba(124,108,245,0.14) 1px, transparent 1px), linear-gradient(to bottom, rgba(124,108,245,0.14) 1px, transparent 1px); background-size: 22px 22px;';
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

    <div class="mt-10 grid items-stretch gap-4 lg:grid-cols-3 lg:gap-5">
        <article class="relative flex flex-col overflow-hidden rounded-[1.7rem] bg-[#f3f0ff] px-4 pb-6 pt-4 sm:px-5">
            <div class="{{ $cardGrid }}" style="{{ $cardGridStyle }}" aria-hidden="true"></div>
            <div class="relative flex items-center justify-between">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-600 text-sm font-bold text-white shadow-sm shadow-violet-600/30">1</span>
                <span class="text-xs font-medium text-slate-400">{{ __('talenma.home.steps_1_label') }}</span>
            </div>
            <div class="relative mt-4 flex min-h-[13.5rem] flex-col justify-center gap-3 rounded-2xl bg-white p-4 shadow-[0_14px_34px_rgba(79,70,229,0.10)] ring-1 ring-white">
                <div class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-400">
                    {{ __('talenma.home.steps_1_email') }}
                </div>
                <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                    <span class="rounded-md bg-violet-100 px-1.5 py-0.5 text-[10px] font-bold tracking-wide text-violet-600">PDF</span>
                    <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700">{{ __('talenma.home.steps_1_file') }}</span>
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white" aria-hidden="true">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.2 7.2a1 1 0 0 1-1.4 0L3.3 9.1a1 1 0 1 1 1.4-1.4l4.1 4.1 6.5-6.5a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                    </span>
                </div>
                <a href="{{ $accountUrl }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 transition hover:from-blue-700 hover:to-violet-600">
                    {{ __('talenma.home.hero_create_account') }}
                </a>
            </div>
            <h3 class="relative mt-5 text-lg font-bold text-slate-900">{{ __('talenma.home.steps_1_title') }}</h3>
            <p class="relative mt-1 text-sm leading-relaxed text-slate-500">{{ __('talenma.home.steps_1_desc') }}</p>
        </article>

        <article class="relative flex flex-col overflow-hidden rounded-[1.7rem] bg-[#f3f0ff] px-4 pb-6 pt-4 sm:px-5">
            <div class="{{ $cardGrid }}" style="{{ $cardGridStyle }}" aria-hidden="true"></div>
            <div class="relative flex items-center justify-between">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white shadow-sm shadow-blue-600/30">2</span>
                <span class="text-xs font-medium text-slate-400">{{ __('talenma.home.steps_2_label') }}</span>
            </div>
            <div class="relative mt-4 flex min-h-[13.5rem] flex-col justify-center gap-3.5 rounded-2xl bg-white p-4 shadow-[0_14px_34px_rgba(79,70,229,0.10)] ring-1 ring-white">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-500">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3.5 13.6 8 18.2 9.5 13.6 11 12 15.5 10.4 11 5.8 9.5 10.4 8 12 3.5Z"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">{{ __('talenma.home.steps_2_badge') }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ __('talenma.home.steps_2_badge_hint') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('talenma.home.steps_2_tag_remote') }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('talenma.home.steps_2_tag_visa') }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('talenma.home.steps_2_tag_city') }}</span>
                </div>
                <p class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.5 12S6 6.5 12 6.5 21.5 12 21.5 12 17.9 17.5 12 17.5 2.5 12 2.5 12Z"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                    {{ __('talenma.home.steps_2_visible') }}
                </p>
            </div>
            <h3 class="relative mt-5 text-lg font-bold text-slate-900">{{ __('talenma.home.steps_2_title') }}</h3>
            <p class="relative mt-1 text-sm leading-relaxed text-slate-500">{{ __('talenma.home.steps_2_desc') }}</p>
        </article>

        <article class="relative flex flex-col overflow-hidden rounded-[1.7rem] bg-[#f3f0ff] px-4 pb-6 pt-4 sm:px-5">
            <div class="{{ $cardGrid }}" style="{{ $cardGridStyle }}" aria-hidden="true"></div>
            <div class="relative flex items-center justify-between">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-500 text-sm font-bold text-white shadow-sm shadow-violet-500/30">3</span>
                <span class="text-xs font-medium text-slate-400">{{ __('talenma.home.steps_3_label') }}</span>
            </div>
            <div class="relative mt-4 flex min-h-[13.5rem] flex-col justify-center gap-4 rounded-2xl bg-white p-4 shadow-[0_14px_34px_rgba(79,70,229,0.10)] ring-1 ring-white">
                <div class="flex items-start gap-2.5">
                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-violet-500" aria-hidden="true"></span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">{{ __('talenma.home.steps_3_company') }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ __('talenma.home.steps_3_meta') }}</p>
                    </div>
                </div>
                <a href="{{ route('career.offers') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 transition hover:from-blue-700 hover:to-violet-600">
                    {{ __('talenma.home.steps_3_cta') }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>
            <h3 class="relative mt-5 text-lg font-bold text-slate-900">{{ __('talenma.home.steps_3_title') }}</h3>
            <p class="relative mt-1 text-sm leading-relaxed text-slate-500">{{ __('talenma.home.steps_3_desc') }}</p>
        </article>
    </div>

    <div class="mt-5 flex flex-col gap-4 rounded-2xl border border-slate-200/90 bg-white px-4 py-3.5 shadow-sm sm:flex-row sm:items-center sm:px-5">
        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-500">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                <path stroke-linecap="round" stroke-width="1.7" d="M9.5 17a2.5 2.5 0 0 0 5 0"/>
            </svg>
        </span>
        <p class="min-w-0 flex-1 text-sm leading-relaxed text-slate-500">
            <span class="font-semibold text-slate-800">{{ __('talenma.home.steps_notice_lead') }}</span>
            {{ __('talenma.home.steps_notice_body') }}
        </p>
        <a href="{{ $profileUrl }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ __('talenma.home.steps_notice_cta') }}
        </a>
    </div>
</section>
