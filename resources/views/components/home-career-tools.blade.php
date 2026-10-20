@php
    $user = auth()->user();
    $guest = auth()->guest();
    $cvUrl = $guest ? route('cv-builder.intro') : route('talent.cv-builder.index');
    $atsUrl = $guest ? route('ats-score.intro') : route('talent.ats-score.index');

    if ($user) {
        $accountUrl = ($user->isTalent() && $user->isApproved())
            ? route('profile.details.edit')
            : route('profile.edit');
        $accountLabel = __('talenma.home.steps_notice_cta');
    } else {
        $accountUrl = route('register');
        $accountLabel = __('talenma.home.hero_create_account');
    }
@endphp

<section id="outils" class="home-tools-panel scroll-mt-28 mt-16 rounded-[28px] px-6 py-12 sm:px-10 sm:py-16 lg:mt-20 lg:px-16 lg:py-[6.5rem]" aria-labelledby="home-tools-title">
    <div class="relative">
        <h2 id="home-tools-title" class="text-[2rem] font-bold leading-[1.08] tracking-[-0.03em] text-white sm:text-5xl">
            {{ __('talenma.home.tools_title_lead') }}<br>
            <span class="text-[#EBBA6E]">{{ __('talenma.home.tools_title_accent') }}</span>
        </h2>

        <div class="mt-10 grid gap-6 lg:mt-12 lg:grid-cols-[1.15fr_1fr]">
            <article class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] border border-white/12 bg-white/[0.06] p-7 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] sm:p-11 lg:row-span-2">
                <p class="inline-flex w-fit items-center gap-2 rounded-full bg-[#7DD3FC]/15 px-3 py-1.5 text-[13px] font-semibold text-[#7DD3FC]">
                    <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h4"/></svg>
                    {{ __('talenma.home.tools_cv_badge') }}
                </p>
                <h3 class="max-w-md text-[1.7rem] font-bold leading-[1.15] tracking-[-0.02em] text-white sm:text-[2.125rem]">
                    {{ __('talenma.home.tools_cv_title') }}
                </h3>

                <div class="relative flex flex-1 items-center justify-center py-6">
                    <span class="pointer-events-none absolute left-6 top-0 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(99,102,241,0.9)_0%,rgba(255,255,255,0)_68%)] opacity-60" aria-hidden="true"></span>
                    <div class="relative w-[min(100%,300px)] -rotate-[4deg] rounded-2xl bg-white p-[22px] shadow-[0_40px_70px_-30px_rgba(0,0,0,0.55)]" aria-hidden="true">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-500 text-[15px] font-bold text-white">CV</span>
                            <span class="min-w-0 flex-1">
                                <span class="block h-2 w-[70%] rounded bg-slate-900"></span>
                                <span class="mt-1.5 block h-1.5 w-[45%] rounded bg-indigo-300"></span>
                            </span>
                        </div>
                        <p class="mt-3.5 text-[10px] font-bold tracking-[0.1em] text-indigo-600">{{ __('talenma.home.tools_cv_experience') }}</p>
                        <span class="mt-1.5 block h-1.5 w-[90%] rounded bg-gray-200"></span>
                        <span class="mt-1.5 block h-1.5 w-3/4 rounded bg-gray-200"></span>
                        <span class="mt-1.5 block h-1.5 w-[82%] rounded bg-gray-200"></span>
                        <p class="mt-3.5 text-[10px] font-bold tracking-[0.1em] text-indigo-600">{{ __('talenma.home.tools_cv_skills') }}</p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            <span class="rounded-md bg-[#EEF0FF] px-2 py-1 text-[10px] font-semibold text-indigo-700">{{ __('talenma.home.tools_cv_skill_1') }}</span>
                            <span class="rounded-md bg-[#F3EEFF] px-2 py-1 text-[10px] font-semibold text-violet-700">{{ __('talenma.home.tools_cv_skill_2') }}</span>
                            <span class="rounded-md bg-[#EAF1FF] px-2 py-1 text-[10px] font-semibold text-blue-600">{{ __('talenma.home.tools_cv_skill_3') }}</span>
                        </div>
                        <p class="mt-3.5 text-[10px] font-bold tracking-[0.1em] text-indigo-600">{{ __('talenma.home.tools_cv_education') }}</p>
                        <span class="mt-1.5 block h-1.5 w-4/5 rounded bg-gray-200"></span>
                        <span class="mt-1.5 block h-1.5 w-3/5 rounded bg-gray-200"></span>
                    </div>
                    <p class="absolute bottom-4 right-2 inline-flex items-center gap-2 rounded-[14px] bg-white px-3.5 py-2.5 text-[13px] font-semibold text-slate-900 shadow-[0_20px_40px_-16px_rgba(0,0,0,0.6)] sm:right-6">
                        <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>
                        {{ __('talenma.home.tools_cv_chip') }}
                    </p>
                </div>

                <a href="{{ $cvUrl }}" class="inline-flex w-fit items-center rounded-[14px] bg-white px-6 py-3.5 text-[15px] font-bold text-[#0D0B2B] transition hover:bg-indigo-50">
                    {{ __('talenma.home.tools_cv_cta') }}
                </a>
            </article>

            <article class="relative flex flex-col gap-5 overflow-hidden rounded-[28px] border border-white/12 bg-white/[0.06] p-7 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] sm:p-9">
                <p class="inline-flex w-fit items-center gap-2 rounded-full bg-cyan-400/15 px-3 py-1.5 text-[13px] font-semibold text-cyan-300">
                    <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="m9 12 2 2 4-4"/></svg>
                    {{ __('talenma.home.tools_ats_badge') }}
                </p>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-6">
                    <div class="relative h-[140px] w-[140px] shrink-0">
                        <svg class="h-[140px] w-[140px]" viewBox="0 0 140 140" aria-hidden="true">
                            <defs>
                                <linearGradient id="home-tools-ats" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#7DD3FC"></stop>
                                    <stop offset="1" stop-color="#A78BFA"></stop>
                                </linearGradient>
                            </defs>
                            <circle cx="70" cy="70" r="54" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="12"></circle>
                            <circle cx="70" cy="70" r="54" fill="none" stroke="url(#home-tools-ats)" stroke-width="12" stroke-linecap="round" stroke-dasharray="291.8 339.3" transform="rotate(-90 70 70)"></circle>
                        </svg>
                        <span class="absolute inset-0 flex flex-col items-center justify-center text-white">
                            <span class="text-4xl font-bold leading-none">86</span>
                            <span class="text-xs text-indigo-200">/ 100</span>
                        </span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-[22px] font-bold leading-snug text-white">{{ __('talenma.home.tools_ats_title') }}</h3>
                        <ul class="mt-2.5 space-y-2.5 text-sm text-indigo-100">
                            <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></span>{{ __('talenma.home.tools_ats_item_1') }}</li>
                            <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></span>{{ __('talenma.home.tools_ats_item_2') }}</li>
                            <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-[#7DD3FC]/25 text-[#7DD3FC]" aria-hidden="true">!</span>{{ __('talenma.home.tools_ats_item_3') }}</li>
                        </ul>
                    </div>
                </div>
                <a href="{{ $atsUrl }}" class="inline-flex w-fit items-center rounded-[14px] border border-white/25 bg-white/12 px-5 py-3.5 text-[15px] font-semibold text-white transition hover:bg-white/20">
                    {{ __('talenma.home.tools_ats_cta') }}
                </a>
            </article>

            <article class="relative flex flex-col gap-4 overflow-hidden rounded-[28px] border border-white/12 bg-white/[0.06] p-7 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] sm:p-9">
                <p class="inline-flex w-fit items-center gap-2 rounded-full bg-indigo-300/15 px-3 py-1.5 text-[13px] font-semibold text-indigo-300">
                    <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ __('talenma.home.tools_space_badge') }}
                </p>
                <h3 class="text-[22px] font-bold leading-snug text-white">{{ __('talenma.home.tools_space_title') }}</h3>
                <ul class="space-y-2.5 text-sm text-indigo-100">
                    <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></span>{{ __('talenma.home.tools_space_item_1') }}</li>
                    <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></span>{{ __('talenma.home.tools_space_item_2') }}</li>
                    <li class="flex items-center gap-2.5"><span class="flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-full bg-cyan-500 text-white" aria-hidden="true"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></span>{{ __('talenma.home.tools_space_item_3') }}</li>
                </ul>
                <a href="{{ $accountUrl }}" class="mt-auto inline-flex w-fit items-center rounded-[14px] bg-[#7DD3FC] px-5 py-3.5 text-[15px] font-bold text-[#0D0B2B] transition hover:bg-cyan-200">
                    {{ $accountLabel }}
                </a>
            </article>
        </div>
    </div>
</section>
