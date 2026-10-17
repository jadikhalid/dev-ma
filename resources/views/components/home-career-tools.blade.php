@php
    $user = auth()->user();
    $guest = auth()->guest();
    $cvUrl = $guest ? route('cv-builder.gate') : route('talent.cv-builder.index');
    $atsUrl = $guest ? route('ats-score.gate') : route('talent.ats-score.index');

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

<section id="outils" class="scroll-mt-28 bg-white pb-16 lg:pb-20" aria-labelledby="home-tools-title">
    <div class="home-shell">
        <div class="text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
                {{ __('talenma.home.tools_eyebrow') }}
            </p>
            <h2 id="home-tools-title" class="mx-auto mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.7rem] lg:leading-[1.15]">
                {{ __('talenma.home.tools_title') }}
            </h2>
        </div>

        <div class="mt-10 grid items-stretch gap-4 lg:grid-cols-2 lg:gap-5">
            <article class="home-tools-card flex h-full flex-col rounded-[1.6rem] border border-indigo-100 p-6 text-slate-900 shadow-[0_16px_40px_rgba(79,70,229,0.10)] sm:p-7">
                <p class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/80 px-3 py-1 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-100">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3.5A1.5 1.5 0 0 1 5.5 2h6.1a1.5 1.5 0 0 1 1.06.44l2.9 2.9A1.5 1.5 0 0 1 16 6.4V16.5A1.5 1.5 0 0 1 14.5 18h-9A1.5 1.5 0 0 1 4 16.5v-13Z"/></svg>
                    {{ __('talenma.home.tools_cv_badge') }}
                </p>
                <h3 class="mt-5 max-w-sm text-2xl font-bold leading-snug tracking-tight sm:text-[1.7rem]">
                    {{ __('talenma.home.tools_cv_title') }}
                </h3>

                <div class="relative mx-auto mt-6 h-64 w-full max-w-sm">
                    <div class="absolute bottom-2 left-1/2 z-10 h-44 w-[7.75rem] origin-bottom -translate-x-1/2 -rotate-[28deg] rounded-xl bg-white p-3 shadow-[0_12px_28px_rgba(79,70,229,0.16)] ring-1 ring-slate-200/80" aria-hidden="true">
                        <span class="block h-1.5 w-10 rounded-full bg-indigo-500"></span>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-4/5 rounded-full bg-slate-200"></span>
                        <span class="mt-3 block h-1 w-8 rounded-full bg-slate-300"></span>
                        <span class="mt-1.5 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-3/5 rounded-full bg-slate-200"></span>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-2/3 rounded-full bg-slate-200"></span>
                    </div>
                    <div class="absolute bottom-6 left-1/2 z-30 h-44 w-[7.75rem] origin-bottom -translate-x-1/2 rounded-xl bg-white p-3 shadow-[0_16px_32px_rgba(15,23,42,0.14)] ring-1 ring-slate-200/80" aria-hidden="true">
                        <div class="flex items-center gap-2">
                            <span class="h-7 w-7 shrink-0 rounded-md bg-teal-500"></span>
                            <span class="block h-1.5 flex-1 rounded-full bg-slate-200"></span>
                        </div>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-11/12 rounded-full bg-slate-200"></span>
                        <span class="mt-3 block h-1 w-7 rounded-full bg-teal-400"></span>
                        <span class="mt-1.5 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-2/3 rounded-full bg-slate-200"></span>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                    </div>
                    <div class="absolute bottom-2 left-1/2 z-20 h-44 w-[7.75rem] origin-bottom -translate-x-1/2 rotate-[28deg] rounded-xl bg-white p-3 shadow-[0_12px_28px_rgba(79,70,229,0.16)] ring-1 ring-slate-200/80" aria-hidden="true">
                        <span class="block h-6 rounded-md bg-violet-500"></span>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-4/5 rounded-full bg-slate-200"></span>
                        <div class="mt-3 grid grid-cols-2 gap-1.5">
                            <span class="h-1 rounded-full bg-slate-200"></span>
                            <span class="h-1 rounded-full bg-slate-200"></span>
                            <span class="h-1 rounded-full bg-slate-200"></span>
                            <span class="h-1 w-2/3 rounded-full bg-slate-200"></span>
                        </div>
                        <span class="mt-3 block h-1 w-full rounded-full bg-slate-200"></span>
                        <span class="mt-1.5 block h-1 w-3/4 rounded-full bg-slate-200"></span>
                    </div>
                    <p class="absolute -bottom-1 right-1 z-40 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-lg">
                        <span aria-hidden="true">✦</span>
                        {{ __('talenma.home.tools_cv_chip') }}
                    </p>
                </div>

                <div class="mt-auto pt-8">
                    <a href="{{ $cvUrl }}" class="inline-flex w-fit items-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('talenma.home.tools_cv_cta') }}
                    </a>
                </div>
            </article>

            <div class="flex flex-col gap-4 lg:h-full lg:gap-5">
                <article class="home-tools-card rounded-[1.6rem] border border-indigo-100 p-6 text-slate-900 shadow-[0_16px_40px_rgba(79,70,229,0.10)] sm:p-7">
                    <p class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/80 px-3 py-1 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-100">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 4.2 6 10 6 10s6-5.8 6-10a6 6 0 0 0-6-6Zm0 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" clip-rule="evenodd"/></svg>
                        {{ __('talenma.home.tools_ats_badge') }}
                    </p>
                    <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="grid h-[4.5rem] w-[4.5rem] shrink-0 place-items-center rounded-full" style="background: conic-gradient(#6366f1 0 86%, rgba(99,102,241,.16) 86% 100%)">
                            <div class="flex h-14 w-14 flex-col items-center justify-center rounded-full bg-white leading-none">
                                <span class="text-lg font-extrabold text-slate-900">86</span>
                                <span class="mt-0.5 text-[10px] text-slate-400">/100</span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold leading-snug">{{ __('talenma.home.tools_ats_title') }}</h3>
                            <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                                <li class="flex items-start gap-2"><span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>{{ __('talenma.home.tools_ats_item_1') }}</li>
                                <li class="flex items-start gap-2"><span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>{{ __('talenma.home.tools_ats_item_2') }}</li>
                                <li class="flex items-start gap-2"><span class="mt-0.5 text-amber-500" aria-hidden="true">!</span>{{ __('talenma.home.tools_ats_item_3') }}</li>
                            </ul>
                        </div>
                    </div>
                    <a href="{{ $atsUrl }}" class="mt-5 inline-flex w-fit items-center rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50">
                        {{ __('talenma.home.tools_ats_cta') }}
                    </a>
                </article>

                <article class="home-tools-card flex flex-1 flex-col rounded-[1.6rem] border border-indigo-100 p-6 text-slate-900 shadow-[0_16px_40px_rgba(79,70,229,0.10)] sm:p-7">
                    <p class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/80 px-3 py-1 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-100">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 10a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 10 10Zm-6.5 6.5a6.5 6.5 0 0 1 13 0 .75.75 0 0 1-.75.75h-11.5a.75.75 0 0 1-.75-.75Z"/></svg>
                        {{ __('talenma.home.tools_space_badge') }}
                    </p>
                    <h3 class="mt-4 max-w-sm text-lg font-bold leading-snug">{{ __('talenma.home.tools_space_title') }}</h3>
                    <ul class="mt-4 space-y-1.5 text-sm text-slate-600">
                        <li class="flex items-start gap-2"><span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>{{ __('talenma.home.tools_space_item_1') }}</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>{{ __('talenma.home.tools_space_item_2') }}</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>{{ __('talenma.home.tools_space_item_3') }}</li>
                    </ul>
                    <div class="mt-auto pt-5">
                        <a href="{{ $accountUrl }}" class="inline-flex w-fit items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                            {{ $accountLabel }}
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
