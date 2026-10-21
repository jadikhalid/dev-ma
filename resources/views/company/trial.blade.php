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
    <div class="home-shell pb-16 pt-8 sm:pb-20 sm:pt-12">
        <a href="{{ route('company.offer') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            {{ __('talenma.company_offer.portal_home') }}
        </a>

        <div class="mx-auto mt-6 max-w-2xl rounded-[1.75rem] border border-slate-200/80 bg-white px-5 py-7 shadow-[0_16px_40px_rgba(15,23,42,0.06)] sm:px-8 sm:py-9">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">{{ __('talenma.company_offer.trial_title') }}</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 sm:text-base">{{ __('talenma.company_offer.trial_subtitle') }}</p>

            <div class="mt-8">
                @include('company.partials.trial-request-form', [
                    'professionSectors' => $professionSectors,
                    'companyCountryOptions' => $companyCountryOptions,
                ])
            </div>
        </div>
    </div>
</section>
@endsection
