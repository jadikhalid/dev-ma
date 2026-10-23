@extends('layouts.public')

@section('title', __('talenma.company_offer.demo_title'))
@section('meta_description', __('talenma.company_offer.demo_subtitle'))

@section('content')
<section
    class="relative bg-gradient-to-br from-white via-sky-50 to-sky-100"
    data-company-demo
    x-data="{ close() { window.location.href = @js(route('company.offer')); } }"
    @company-offer-drawer-close.window="close()"
>
    <div class="home-shell pb-16 pt-8 sm:pb-24 sm:pt-24">
        <div class="company-request-frame mx-auto">
            <aside class="rounded-[1.75rem] border border-indigo-100 bg-white/90 px-5 py-6 shadow-[0_12px_32px_rgba(79,70,229,0.06)] sm:px-8 sm:py-7" data-company-demo-intro>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ __('talenma.company_offer.demo_title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600 sm:text-base">{{ __('talenma.company_offer.demo_subtitle') }}</p>
            </aside>

            <div class="mt-10 rounded-[1.75rem] border border-slate-200/80 bg-white px-5 py-7 shadow-[0_16px_40px_rgba(15,23,42,0.06)] sm:px-8 sm:py-9">
                <h1 class="text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">{{ __('talenma.company_offer.demo_form_title') }}</h1>

                <div class="mt-8">
                    @include('company.partials.demo-request-form')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
