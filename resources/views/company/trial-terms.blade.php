@extends('layouts.public')

@section('title', __('talenma.company_offer.trial_terms.title'))
@section('meta_description', __('talenma.company_offer.trial_terms.meta'))

@section('content')
<section class="relative" data-company-trial-terms>
    <div class="home-shell pb-16 pt-8 sm:pb-20 sm:pt-12">
        <article class="mx-auto max-w-3xl rounded-[1.75rem] border border-slate-200/80 bg-white px-5 py-7 shadow-[0_16px_40px_rgba(15,23,42,0.06)] sm:px-8 sm:py-9">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">{{ __('talenma.company_offer.trial_terms.title') }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 sm:text-base">{{ __('talenma.company_offer.trial_terms.intro') }}</p>

            <div class="mt-8 space-y-7">
                @foreach (__('talenma.company_offer.trial_terms.sections') as $section)
                    <section>
                        <h2 class="text-lg font-bold text-slate-950">{{ $section['title'] }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $section['body'] }}</p>
                    </section>
                @endforeach
            </div>
        </article>
    </div>
</section>
@endsection
