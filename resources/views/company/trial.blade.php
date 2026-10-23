@extends('layouts.public')

@section('title', __('talenma.company_offer.trial_title'))
@section('meta_description', __('talenma.company_offer.trial_subtitle'))

@section('content')
<section
    class="relative bg-gradient-to-br from-white via-sky-50 to-sky-100"
    data-company-trial
    x-data="{ close() { window.location.href = @js(route('company.offer')); } }"
    @company-offer-drawer-close.window="close()"
>
    <div class="home-shell pb-16 pt-8 sm:pb-24 sm:pt-24">
        @php($trialPlan = __('talenma.company_plans.plans.trial'))
        <div class="company-request-frame mx-auto">
            <aside class="rounded-[1.75rem] border border-indigo-100 bg-white/90 px-5 py-6 shadow-[0_12px_32px_rgba(79,70,229,0.06)] sm:px-8 sm:py-7" data-company-trial-offer>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ __('talenma.company_offer.trial_offer_name') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600 sm:text-base">{{ $trialPlan['tagline'] }}</p>
                <ul class="mt-5 grid gap-2.5 sm:grid-cols-2">
                    @foreach ($trialPlan['features'] as $feature)
                        <li class="flex items-start gap-2.5 text-sm leading-snug text-slate-700">
                            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-700" aria-hidden="true">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.2 7.2a1 1 0 0 1-1.4 0L3.3 9.1a1 1 0 1 1 1.4-1.4l4.1 4.1 6.5-6.5a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                            </span>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-5 text-sm leading-relaxed text-slate-500">{{ __('talenma.company_offer.trial_offer_note') }}</p>
            </aside>

            <div class="mt-10 rounded-[1.75rem] border border-slate-200/80 bg-white px-5 py-7 shadow-[0_16px_40px_rgba(15,23,42,0.06)] sm:px-8 sm:py-9">
                <h1 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ __('talenma.company_offer.trial_title') }}</h1>

                <div class="mt-8">
                    @include('company.partials.trial-request-form', [
                        'professionSectors' => $professionSectors,
                        'companyCountryOptions' => $companyCountryOptions,
                    ])
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
